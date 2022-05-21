<?php
use App\Manager\ForumDatabase;
use App\URL\ExplodeUrl;


$url = new ExplodeUrl($_GET['url']);
$id = $url->getId();

$topics = new ForumDatabase();
$topics->closeTopic($id);

header('Location: ' . $_SERVER['HTTP_REFERER']);


