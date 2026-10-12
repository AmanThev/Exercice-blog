<?php

use App\Manager\PollDatabase;
use App\Security\Csrf;
use App\URL\CreateUrl;

// The script of the page (poll.js) sends this header: it wants JSON back.
// Without JavaScript the form still works: the visitor is sent back to the home page.
$wantsJson = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
$poll      = new PollDatabase();
$userId    = (int)($_SESSION['id'] ?? 0);

$send = function(string $status, string $message) use ($wantsJson, $poll, $userId){
    if(!$wantsJson){
        header('Location: ' . CreateUrl::url('home'));
        exit;
    }

    // The results are drawn by the same file as the home page, so there is only one version of the HTML.
    $results    = $poll->getResults();
    $userAnswer = $userId > 0 ? $poll->getAnswer($userId) : null;

    ob_start();
    require VIEWS . 'partials/poll-results.php';
    $html = ob_get_clean();

    header('Content-Type: application/json');
    echo json_encode([
        'status'  => $status,
        'message' => $message,
        'html'    => $status === 'ok' || $userAnswer !== null ? $html : null,
    ]);
    exit;
};

if($userId <= 0){
    if(!$wantsJson){
        header('Location: ' . CreateUrl::url('authentication/login', ['redirect' => CreateUrl::url('home')]));
        exit;
    }
    $send('error', 'Log in to vote.');
}

if(!Csrf::validate($_POST['csrf_token'] ?? null)){
    $send('error', 'Your session expired, please reload the page.');
}

$answer = is_string($_POST['opinion'] ?? null) ? $_POST['opinion'] : '';

if(!array_key_exists($answer, PollDatabase::ANSWERS)){
    $send('error', 'Choose an answer.');
}

if(!$poll->addVote($userId, $answer)){
    $send('error', 'You have already voted.');
}

$send('ok', 'Thanks for your vote!');
