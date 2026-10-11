<?php

use App\Manager\UserDatabase;

/**
 * Sets the password of an administrator. To run from a terminal, in the folder of the project :
 *
 *   php scripts/set-admin-password.php            shows the list of the administrators
 *   php scripts/set-admin-password.php Nicolas    asks for the new password of Nicolas
 *
 * (what you type is visible in the terminal : change the password again if somebody could see it)
 */
if(PHP_SAPI !== 'cli'){
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/vendor/autoload.php';

$db   = new UserDatabase();
$name = $argv[1] ?? null;

if($name === null){
    echo "Administrators :\n";
    foreach($db->getAdminsWithStats() as $admin){
        echo "  - {$admin['name']} ({$admin['position']})\n";
    }
    echo "\nUsage : php scripts/set-admin-password.php <name>\n";
    exit(0);
}

echo "New password for $name (5 characters at least) : ";
$password = rtrim((string)fgets(STDIN), "\r\n");
echo "Again : ";
$again = rtrim((string)fgets(STDIN), "\r\n");

if(mb_strlen($password) < 5){
    echo "Too short : nothing changed.\n";
    exit(1);
}
if($password !== $again){
    echo "The two passwords are not the same : nothing changed.\n";
    exit(1);
}

if($db->setAdminPassword($name, $password)){
    echo "Done : $name can now log in on authentication/admin.\n";
    exit(0);
}

echo "There is no administrator named \"$name\" : nothing changed.\n";
exit(1);