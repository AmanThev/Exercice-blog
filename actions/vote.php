<?php

use App\Manager\VoteDatabase;
use App\URL\CreateUrl;
use App\Security\Csrf;

$fallback = $_SERVER['HTTP_REFERER'] ?? CreateUrl::url('home');

if(empty($_SESSION['id'])){
    header('Location: ' . CreateUrl::url('authentication/login'));
    exit;
}

if(!Csrf::validate($_POST['csrf_token'] ?? null)){
    header('Location: ' . $fallback);
    exit;
}

$allowedRefs = ['posts', 'reviews'];
$ref   = $_GET['ref'] ?? '';
$refId = (int)($_GET['refId'] ?? 0);
$vote  = (int)($_GET['vote'] ?? 0);

if(!in_array($ref, $allowedRefs, true) || $refId <= 0 || !in_array($vote, [1, -1], true)){
    header('Location: ' . $fallback);
    exit;
}

$userId = (int)$_SESSION['id'];

$voteDatabase = new VoteDatabase();
$existingVote = $voteDatabase->voteUser($ref, $refId, $userId);

if($existingVote === false){
    $voteDatabase->insertVote($ref, $refId, $userId, $vote);
}elseif((int)$existingVote->getVote() === $vote){
    // Recliquer sur le même vote le retire (toggle)
    $voteDatabase->deleteVote($ref, $refId, $userId);
}else{
    $voteDatabase->updateVote($ref, $refId, $userId, $vote);
}

$voteDatabase->countAndUpdateVotes($ref, $refId);

header('Location: ' . $fallback);
exit;