<?php

/**
 * Checks a site from the outside, like a visitor (or an attacker) would see it.
 * To run from a terminal, in the folder of the project :
 *
 *   php scripts/check-site.php https://www.mysite.tld
 *   php scripts/check-site.php https://www.mysite.tld/cinema
 *   php scripts/check-site.php https://localhost/cinema --insecure     (certificate not signed by an authority)
 *
 * [FAIL] a hole that must be closed before the site is public
 * [WARN] something that should be improved
 * [info] for your information
 */
if(PHP_SAPI !== 'cli'){
    http_response_code(404);
    exit;
}

$args     = array_slice($argv, 1);
$insecure = in_array('--insecure', $args, true);
$urls     = array_values(array_filter($args, fn($arg) => $arg[0] !== '-'));

if(count($urls) !== 1 || !preg_match('#^https?://#i', $urls[0])){
    echo "Usage : php scripts/check-site.php https://www.mysite.tld [--insecure]\n";
    exit(2);
}

$base   = rtrim($urls[0], '/');
$parts  = parse_url($base);
$host   = $parts['host'] ?? '';
$isHttps = strtolower($parts['scheme']) === 'https';
$isLocal = in_array($host, ['localhost', '127.0.0.1', '::1', '[::1]'], true);

/**
 * One request. Redirections are NOT followed : a redirection is an answer like another.
 *
 * @return array{status:int, headers:array, body:string, failed:bool}
 */
function request(string $method, string $url, bool $insecure, array $headers = [], string $content = ''): array
{
    $context = stream_context_create([
        'http' => [
            'method'          => $method,
            'header'          => implode("\r\n", $headers),
            'content'         => $content,
            'ignore_errors'   => true,
            'follow_location' => 0,
            'max_redirects'   => 0,
            'timeout'         => 15,
        ],
        'ssl' => ['verify_peer' => !$insecure, 'verify_peer_name' => !$insecure],
    ]);

    $body        = @file_get_contents($url, false, $context);
    $rawHeaders  = $http_response_header ?? [];
    $result      = ['status' => 0, 'headers' => [], 'body' => (string)$body, 'failed' => $body === false && empty($rawHeaders)];

    foreach($rawHeaders as $line){
        if(preg_match('#^HTTP/\S+\s+(\d{3})#', $line, $m)){
            $result['status']  = (int)$m[1];
            $result['headers'] = [];                    // after a redirection, only the last answer counts
        }elseif(str_contains($line, ':')){
            [$name, $value] = explode(':', $line, 2);
            $result['headers'][strtolower(trim($name))][] = trim($value);
        }
    }
    return $result;
}

$counts  = ['FAIL' => 0, 'WARN' => 0, 'ok' => 0, 'info' => 0];
$report  = function(string $level, string $message) use (&$counts){
    $counts[$level]++;
    echo str_pad('[' . $level . ']', 7) . $message . "\n";
};
$header  = fn(array $r, string $name) => $r['headers'][$name][0] ?? null;

echo "Checking $base\n\n";

$home = request('GET', $base . '/', $insecure);
if($home['failed']){
    echo "The site cannot be reached (" . $base . "/). Nothing can be checked.\n";
    if($isHttps && !$insecure){
        echo "If it is a test site with a certificate that is not signed by an authority, run it again with --insecure.\n";
    }
    exit(2);
}
if($home['status'] !== 200){
    $report('WARN', "The home page answers with the status {$home['status']} (200 expected) : check the address you gave.");
}

echo "-- Connection\n";
if($isHttps){
    $report('ok', 'The site is served over HTTPS.');
    $http = request('GET', 'http://' . substr($base, 8) . '/', $insecure);
    $location = (string)$header($http, 'location');
    if(in_array($http['status'], [301, 302, 307, 308], true) && stripos($location, 'https://') === 0){
        $report('ok', 'The address in HTTP is redirected to HTTPS.');
    }elseif($http['failed']){
        $report('info', 'The address in HTTP does not answer.');
    }else{
        $report('WARN', 'The address in HTTP answers (status ' . $http['status'] . ') instead of being redirected to HTTPS.');
    }
    $report($header($home, 'strict-transport-security') ? 'ok' : 'WARN', $header($home, 'strict-transport-security')
        ? 'The header Strict-Transport-Security is set (the browser keeps using HTTPS).'
        : 'The header Strict-Transport-Security is missing.');
}elseif($isLocal){
    $report('info', 'Local site served in HTTP : normal for a test. A public site must use HTTPS.');
}else{
    $report('FAIL', 'The site is served in HTTP : passwords and sessions travel in clear. Use HTTPS.');
}

echo "\n-- Files that must never be reachable\n";
$secret = ['.git/HEAD', '.git/config', '.gitignore', '.env', 'composer.json', 'composer.lock', 'vendor/autoload.php',
           'src/Manager/Connection.php', 'scripts/set-admin-password.php', 'views/home.php', 'ajax/addPostAjax.php',
           '.htaccess', 'database.sql', 'backup.sql', 'dump.sql'];
$open = [];
foreach($secret as $path){
    $r = request('GET', $base . '/' . $path, $insecure);
    if($r['status'] === 200){
        $open[] = $path . ' (' . strlen($r['body']) . ' bytes)';
    }
}
if($open){
    foreach($open as $line){
        $report('FAIL', "Reachable : /$line");
    }
    $report('info', 'Fix : the DocumentRoot of the web server must be the folder "public" of the project, and nothing else.');
}else{
    $report('ok', count($secret) . ' sensitive paths tested (source, git history, configuration, dumps) : none is reachable.');
}

echo "\n-- The dashboard\n";
foreach(['dashboard', 'dashboard/posts', 'dashboard/forum', 'dashboard/users'] as $path){
    $r = request('GET', $base . '/' . $path, $insecure);
    if($r['status'] === 200){
        $report('FAIL', "/$path is open WITHOUT login.");
    }elseif(in_array($r['status'], [301, 302, 303, 307, 308, 401, 403], true)){
        $report('ok', "/$path is closed (status {$r['status']}" . ($header($r, 'location') ? ', goes to ' . $header($r, 'location') : '') . ').');
    }else{
        $report('WARN', "/$path answers with the status {$r['status']} : is it the right site ?");
    }
}
$login = request('GET', $base . '/authentication/admin', $insecure);
$report($login['status'] === 200 ? 'ok' : 'WARN', $login['status'] === 200
    ? 'The page to log in as an administrator is there.'
    : "The page /authentication/admin answers with the status {$login['status']}.");
if($login['status'] === 200){
    $report(stripos((string)$header($login, 'cache-control'), 'no-store') !== false ? 'ok' : 'WARN',
        stripos((string)$header($login, 'cache-control'), 'no-store') !== false
            ? 'The login page is never kept in a cache.'
            : 'The login page can be kept in a cache (header Cache-Control: no-store missing).');
}

echo "\n-- Errors must not show anything\n";
$leaks = '#Whoops|Stack trace|\.php on line|Fatal error|Uncaught|vendor[\\\\/]|htdocs|/var/www|C:\\\\|PDOException|SQLSTATE#i';
$probes = [
    'a page that does not exist'                 => request('GET', $base . '/page-' . bin2hex(random_bytes(6)), $insecure),
    'a login form filled with arrays'            => request('POST', $base . '/authentication/login', $insecure, ['Content-Type: application/x-www-form-urlencoded'], 'name[]=x&password[]=y'),
    'an address with strange characters'         => request('GET', $base . '/blog/%00%27%22%3Cscript%3E-999999999999', $insecure),
];
foreach($probes as $label => $r){
    if(preg_match($leaks, $r['body'], $m)){
        $report('FAIL', "Answer to $label shows technical details (\"{$m[0]}\") : the detailed error page must only be shown on the local machine.");
    }else{
        $report('ok', "Answer to $label (status {$r['status']}) shows no technical detail.");
    }
}

echo "\n-- Headers of the home page\n";
$rules = [
    ['x-content-type-options', fn($v) => stripos($v, 'nosniff') !== false,  'X-Content-Type-Options: nosniff is missing (a browser could guess another type for a file).'],
    ['referrer-policy',        fn($v) => $v !== null,                       'Referrer-Policy is missing (the address of the pages is sent to other sites).'],
];
foreach($rules as [$name, $test, $warning]){
    $value = $header($home, $name);
    $report($value !== null && $test($value) ? 'ok' : 'WARN', $value !== null && $test($value) ? "$name : $value" : $warning);
}
$frame = $header($home, 'x-frame-options') ?? (preg_match('/frame-ancestors/i', (string)$header($home, 'content-security-policy')) ? 'frame-ancestors' : null);
$report($frame ? 'ok' : 'WARN', $frame ? "The site cannot be put in a frame of another site ($frame)." : 'Nothing forbids another site to show this one in a frame (X-Frame-Options or frame-ancestors is missing).');
$report($header($home, 'x-powered-by') ? 'WARN' : 'ok', $header($home, 'x-powered-by')
    ? 'X-Powered-By tells the version of PHP (' . $header($home, 'x-powered-by') . ') : set expose_php = Off in php.ini.'
    : 'The version of PHP is not shown.');
$server = (string)$header($home, 'server');
$report(preg_match('#/\d#', $server) ? 'WARN' : 'ok', preg_match('#/\d#', $server)
    ? "The header Server tells the version ($server) : set ServerTokens Prod in Apache."
    : 'The header Server does not tell a version.');
if(!$header($home, 'content-security-policy')){
    $report('info', 'No Content-Security-Policy (optional : the site uses scripts and styles of other sites, it needs a careful one).');
}

echo "\n-- The session cookie\n";
$cookie = null;
foreach($login['headers']['set-cookie'] ?? [] as $candidate){
    if(stripos($candidate, 'PHPSESSID') === 0 || stripos($candidate, 'session') !== false){
        $cookie = $candidate;
        break;
    }
}
if($cookie === null){
    $report('info', 'No session cookie was sent by the login page.');
}else{
    $report(stripos($cookie, 'httponly') !== false ? 'ok' : 'FAIL', stripos($cookie, 'httponly') !== false
        ? 'HttpOnly : JavaScript cannot read the cookie.' : 'The cookie is not HttpOnly : a script injected in a page could steal it.');
    $report(stripos($cookie, 'samesite') !== false ? 'ok' : 'WARN', stripos($cookie, 'samesite') !== false
        ? 'SameSite : the cookie is not sent along with a request made by another site.' : 'The cookie has no SameSite attribute.');
    if($isHttps){
        $report(stripos($cookie, 'secure') !== false ? 'ok' : 'FAIL', stripos($cookie, 'secure') !== false
            ? 'Secure : the cookie only travels over HTTPS.' : 'The cookie is not Secure : it can travel over HTTP.');
    }
}

echo "\n-- Lists of files\n";
$listed = [];
foreach(['img/', 'img/postPicture/', 'public/img/', 'css/', 'js/', 'uploads/'] as $path){
    $r = request('GET', $base . '/' . $path, $insecure);
    if($r['status'] === 200 && preg_match('/Index of|Directory listing/i', $r['body'])){
        $listed[] = $path;
    }
}
if($listed){
    foreach($listed as $path){
        $report('FAIL', "/$path shows the list of its files (set Options -Indexes in Apache).");
    }
}else{
    $report('ok', 'No folder shows the list of its files.');
}

echo "\n";
echo "{$counts['ok']} ok, {$counts['WARN']} to improve, {$counts['FAIL']} to fix before the site is public.\n";
exit($counts['FAIL'] > 0 ? 1 : 0);