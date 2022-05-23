<?php

require '../vendor/autoload.php';

use App\Router\Router;

define('VIEWS', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views/');
define('AJAX', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ajax/');
define('IMAGE', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'img/');
define('DEBUG_TIME', microtime(true));
$rootEnd = strpos($_SERVER['SCRIPT_NAME'], '/cinema') + 7;
$www_root = substr($_SERVER['SCRIPT_NAME'], 0, $rootEnd);
define("WWW_ROOT", $www_root);
$publicEnd = strpos($_SERVER['SCRIPT_NAME'], '/public') + 7;
$public_root = substr($_SERVER['SCRIPT_NAME'], 0, $publicEnd);
define("PUBLIC_PATH", $public_root);

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new Router($_GET['url']);
$router->get('/',  VIEWS . 'home');
$router->get('/home', VIEWS . 'home');

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
$router->get('/forum/topic/:id/closeTopic', VIEWS . 'forum/closeTopic');

$router->get('/quiz', VIEWS . 'quiz');

$router->get('/admin', VIEWS . 'presentationAdmin');

$router->get('/authentication/login', VIEWS . 'user/login');
$router->get('/authentication/register', VIEWS . 'user/register');

$router->get('/user/:slug', VIEWS . 'user/profileMember');

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

$router->get('/dashboard/comments', VIEWS .'dashboard/comment');

$router->get('/dashboard/users', VIEWS .'dashboard/user/index');

$router->get('/dashboard/forum', VIEWS .'dashboard/forum');

$router->get('/dashboard/users/:slug', VIEWS .'dashboard/user/profileAdmin');

$router->post('/dashboard/posts/newPost', VIEWS . 'dashboard/post/new', 'new_post_added');
$router->post('/dashboard/posts/:id/delete', VIEWS . 'dashboard/post/delete', 'post_delete');

/**
 * Ajax
 */
$router->post('/ajax/addPostAjax', AJAX . 'addPostAjax');
$router->post('/ajax/addFilmAjax', AJAX . 'addFilmAjax');

$router->run();