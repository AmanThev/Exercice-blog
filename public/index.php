<?php
// The session cookie cannot be read by JavaScript, and is not sent along with a request made by another site
// An identifier chosen by somebody else is never accepted : the server gives a new one
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();

require '../vendor/autoload.php';

use App\Router\Router;

define('VIEWS', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views/');
define('AJAX', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ajax/');
define('IMAGE', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'img/');
define('ACTIONS', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'actions/');
define('DEBUG_TIME', microtime(true));
// The folder of the site in the address : "/cinema/public" with the current installation, "" when the
// DocumentRoot of the web server is the folder "public"
$publicFolder = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define("PUBLIC_PATH", $publicFolder);
define("WWW_ROOT", preg_replace('#/public$#', '', $publicFolder));

// Written in the configuration, never read from the request (see CreateUrl::baseUrl).
// In production : set them in the web server (Apache : SetEnv SITE_URL https://www.mysite.tld) or write them here.
define('SITE_URL', rtrim(getenv('SITE_URL') ?: 'http://localhost', '/'));
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'no-reply@localhost');

// The detailed error page (it shows the code, the session and the tokens) only for a visitor who is on
// the machine itself. Elsewhere, a visitor only sees a short message.
// (It is the address of the visitor that is checked : the name of the site comes from a header that
// anybody can write. Behind a reverse proxy every visitor looks local : use another condition then.)
$isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);

if($isLocal){
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}else{
    ini_set('display_errors', '0');
    set_exception_handler(function(\Throwable $e){
        error_log((string)$e);
        $notFound = $e instanceof \App\Manager\Exception\NotFoundException || $e instanceof \App\Router\RouterException;
        http_response_code($notFound ? 404 : 500);
        echo $notFound ? 'Page not found.' : 'Something went wrong, please try again later.';
    });
}

$router = new Router($_GET['url']);
$router->get('/',  VIEWS . 'home');
$router->get('/home', VIEWS . 'home');

$router->get('/legal', VIEWS . 'legal');
$router->get('/privacy', VIEWS . 'privacy');

$router->get('/blog', VIEWS . 'blog');
$router->get('/blog/:slug-:id', VIEWS . 'post', 'post');
$router->post('/blog/:slug-:id', VIEWS . 'post');

$router->get('/reviews', VIEWS . 'reviews');
$router->get('/reviews/:slug-:id', VIEWS . 'film', 'film');
$router->post('/reviews/:slug-:id', VIEWS . 'film', 'addCommentReview');

$router->get('/forum', VIEWS . 'forum/index');
$router->get('/forum/:slug-:id', VIEWS . 'forum/category');
$router->get('/forum/:slugCat/:slug-:id', VIEWS . 'forum/subCategory');
$router->get('/forum/:slugCat/:slugSubCat/:slug-:id', VIEWS . 'forum/topic');
$router->post('/forum/:slugCat/:slugSubCat/:slug-:id', VIEWS . 'forum/topic');
$router->get('/forum/newTopic', VIEWS . 'forum/addTopic');
$router->post('/forum/newTopic', VIEWS . 'forum/addTopic');
$router->post('/forum/topic/:id/closeTopic', VIEWS . 'forum/closeTopic');

$router->get('/search', VIEWS . 'search');
$router->post('/search', VIEWS . 'search');

$router->get('/admin', VIEWS . 'presentationAdmin');

$router->get('/authentication/login', VIEWS . 'user/login');
$router->post('/authentication/login', VIEWS . 'user/login');
$router->get('/authentication/register', VIEWS . 'user/register');
$router->post('/authentication/register', VIEWS . 'user/register');
$router->get('/authentication/success', VIEWS . 'user/success');
$router->get('/authentication/logout', VIEWS . 'user/logout');
$router->get('/authentication/forget', VIEWS . 'user/forget');
$router->post('/authentication/forget', VIEWS . 'user/forget');
$router->get('/authentication/reset/:token', VIEWS . 'user/reset');
$router->post('/authentication/reset/:token', VIEWS . 'user/reset');
$router->get('/authentication/reset-success', VIEWS . 'user/reset-success');

$router->get('/authentication/account-deleted', VIEWS . 'user/account-deleted');

$router->get('/authentication/admin', VIEWS . 'user/adminLogin');
$router->post('/authentication/admin', VIEWS . 'user/adminLogin');

$router->get('/user/:slug', VIEWS . 'user/profileMember');

$router->get('/account', VIEWS . 'user/account');
$router->post('/account', VIEWS . 'user/account');

/**
 * Dashboard
 */
$router->get('/dashboard', VIEWS . 'dashboard/index');

$router->get('/dashboard/posts', VIEWS . 'dashboard/post/index');
$router->get('/dashboard/posts/newPost', VIEWS . 'dashboard/post/new', 'new_post');
$router->get('/dashboard/posts/:id', VIEWS . 'dashboard/post/edit', 'post_edit');

$router->get('/dashboard/reviews', VIEWS .'dashboard/review/index');
$router->get('/dashboard/reviews/newReview', VIEWS . 'dashboard/review/new', 'new_review');
$router->get('/dashboard/reviews/:id', VIEWS . 'dashboard/review/edit', 'review_edit');

$router->get('/dashboard/comments', VIEWS .'dashboard/comment/comment');
$router->post('/dashboard/comments/delete', VIEWS . 'dashboard/comment/delete');

$router->get('/dashboard/users', VIEWS .'dashboard/user/index');

$router->get('/dashboard/forum', VIEWS .'dashboard/forum/forum');
$router->post('/dashboard/forum/action', VIEWS . 'dashboard/forum/action');

$router->get('/dashboard/forum/newCategory', VIEWS . 'dashboard/forum/category');
$router->post('/dashboard/forum/newCategory', VIEWS . 'dashboard/forum/category');
$router->get('/dashboard/forum/category/:id', VIEWS . 'dashboard/forum/category');
$router->post('/dashboard/forum/category/:id', VIEWS . 'dashboard/forum/category');
$router->get('/dashboard/forum/newSubCategory', VIEWS . 'dashboard/forum/subCategory');
$router->post('/dashboard/forum/newSubCategory', VIEWS . 'dashboard/forum/subCategory');
$router->get('/dashboard/forum/subCategory/:id', VIEWS . 'dashboard/forum/subCategory');
$router->post('/dashboard/forum/subCategory/:id', VIEWS . 'dashboard/forum/subCategory');

$router->get('/dashboard/users/:slug', VIEWS .'dashboard/user/profileAdmin');

$router->post('/dashboard/logout', VIEWS . 'dashboard/logout');

$router->post('/dashboard/posts/newPost', VIEWS . 'dashboard/post/new', 'new_post_added');
$router->post('/dashboard/posts/:id/delete', VIEWS . 'dashboard/post/delete', 'post_delete');
$router->post('/dashboard/posts/:id', VIEWS . 'dashboard/post/edit', 'post_edited');

$router->post('/dashboard/reviews/newFilm', VIEWS . 'dashboard/review/new', 'new_film_added');
$router->post('/dashboard/reviews/:id', VIEWS . 'dashboard/review/edit', 'new_film_edited');
$router->post('/dashboard/reviews/:id/delete', VIEWS . 'dashboard/review/delete');

/**
 * Ajax
 */
$router->post('/ajax/addPostAjax', AJAX . 'addPostAjax');
$router->post('/ajax/addFilmAjax', AJAX . 'addFilmAjax');

$router->post('/actions/vote', ACTIONS . 'vote');
$router->post('/actions/poll', ACTIONS . 'poll');

$router->run();