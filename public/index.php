<?php

require '../vendor/autoload.php';

use App\Router\Router;

define('VIEWS', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views/');
define('DEBUG_TIME', microtime(true));
$rootEnd = strpos($_SERVER['SCRIPT_NAME'], '/cinema') + 7;
$www_root = substr($_SERVER['SCRIPT_NAME'], 0, $rootEnd);
define("WWW_ROOT", $www_root);
$publicEnd = strpos($_SERVER['SCRIPT_NAME'], '/public') + 7;
$public_root = substr($_SERVER['SCRIPT_NAME'], 0, $publicEnd);
define("PUBLIC_PATH", $public_root);

/*function explodeUrl($path){
  $path = str_replace('/', '-', $path);
  $path = explode('-', $path);
  dump($path);
  return $path;
}*/

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new Router($_GET['url']);
$router->get('/',  VIEWS . 'home');
$router->get('/home', VIEWS . 'home');
$router->get('/blog', VIEWS . 'blog');
$router->get('/blog/:slug-:id', VIEWS . 'post', 'post');
$router->get('/reviews', VIEWS . 'reviews');
$router->get('/reviews/:slug-:id', VIEWS . 'film', 'film');
$router->get('/forum', VIEWS . 'forum/index');
$router->get('/forum/:slug-:id', VIEWS . 'forum/category');
$router->get('/forum/:slugCat/:slug-:id', VIEWS . 'forum/subCategory');
$router->get('/forum/:slugCat/:slugSubCat/:slug-:id', VIEWS . 'forum/topic');
$router->get('/forum/newTopic', VIEWS . 'forum/addTopic');
$router->get('/quiz', VIEWS . 'quiz');
$router->get('/admin', VIEWS . 'presentationAdmin');

$router->get('/authentication/login', VIEWS . 'user/login');
$router->get('/authentication/register', VIEWS . 'user/register');

$router->get('/user/:slug', VIEWS . 'user/profileMember');

$router->get('/dashboard', VIEWS . 'dashboard/index');
$router->get('/dashboard/posts', VIEWS . 'dashboard/post/index');
$router->get('/dashboard/reviews', VIEWS .'dashboard/review/index');
$router->get('/dashboard/comments', VIEWS .'dashboard/comment');
$router->get('/dashboard/users', VIEWS .'dashboard/user/index');
$router->get('/dashboard/forum', VIEWS .'dashboard/forum');
$router->get('/dashboard/users/:slug', VIEWS .'dashboard/user/profileAdmin');


$router->post('/post/:id', function(){
    echo 'Add post n°' .$id;
});

$router->run();