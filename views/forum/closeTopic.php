<?php
use App\Manager\ForumDatabase;
use App\URL\ExplodeUrl;


$test = new ExplodeUrl($_GET['url']);
$id = $test->getId();

$topics = new ForumDatabase();
$topics->closeTopic($id);

header('Location: ' . $_SERVER['HTTP_REFERER']);


