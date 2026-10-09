<?php

use App\Manager\ForumDatabase;
use App\URL\ExplodeUrl;
use App\URL\CreateUrl;
use App\Security\Csrf;

$fallback = $_SERVER['HTTP_REFERER'] ?? CreateUrl::url('forum');

if(empty($_SESSION['id']) || !Csrf::validate($_POST['csrf_token'] ?? null)){
    header('Location: ' . $fallback);
    exit;
}

$url = new ExplodeUrl($_GET['url']);
$id  = $url->getId();

$forum = new ForumDatabase();
$topic = $forum->getTopic($id, '');

if($topic->getIdMember() === (int)$_SESSION['id']){
    $forum->closeTopic($id);
}

header('Location: ' . $fallback);
exit;
