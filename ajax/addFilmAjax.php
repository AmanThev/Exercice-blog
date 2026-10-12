<?php

use App\Form\AddFilm;
use App\Helpers\File;
use App\Security\Csrf;
use App\Security\AdminGuard;

header('Content-Type: application/json');

$send = function(array $result){
    echo json_encode($result);
    exit;
};

// Second lock: the route is already reserved for administrators, but this file must never work without them
if(!AdminGuard::isAdmin()){
    $send(['status' => 'error', 'error' => ['form' => ['Your administrator session has expired, please log in again.']]]);
}

// File bigger than what PHP accepts: $_POST is then empty
if(!empty($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] >= File::maxUpload()){
    $send(['status' => 'error', 'error' => ['poster' => ['The file is too big.']]]);
}

if(!Csrf::validate($_POST['csrf_token'] ?? null)){
    $send(['status' => 'error', 'error' => ['form' => ['Your session expired, please reload the page.']]]);
}

$file = $_FILES['poster'] ?? [];
$form = new AddFilm($_POST);

if(!$form->validateFilm($file)->resultValidator()){
    $send(['status' => 'error', 'error' => $form->returnErrors()]);
}

$form->createFilm(AdminGuard::id(), $file);

$send(['status' => 'ok', 'good' => 'Your review has been added.']);