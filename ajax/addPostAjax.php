<?php

use App\Form\AddPost;
use App\Helpers\File;
use App\Security\Csrf;
use App\Security\AdminGuard;

header('Content-Type: application/json');

$send = function(array $result){
    echo json_encode($result);
    exit;
};

// Second verrou : la route est déjà réservée aux administrateurs, mais ce fichier ne doit jamais travailler sans eux
if(!AdminGuard::isAdmin()){
    $send(['status' => 'error', 'error' => ['form' => ['Your administrator session has expired, please log in again.']]]);
}

// Fichier plus gros que ce que PHP accepte : $_POST est alors vide
if(!empty($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] >= File::maxUpload()){
    $send(['status' => 'error', 'error' => ['picture' => ['The file is too big.']]]);
}

if(!Csrf::validate($_POST['csrf_token'] ?? null)){
    $send(['status' => 'error', 'error' => ['form' => ['Your session expired, please reload the page.']]]);
}

$file = $_FILES['picture'] ?? [];
$form = new AddPost($_POST);

if(!$form->validatePost($file)->resultValidator()){
    $send(['status' => 'error', 'error' => $form->returnErrors()]);
}

$form->createPost(AdminGuard::id(), $file);

$send(['status' => 'ok', 'good' => 'Your post has been posted.']);