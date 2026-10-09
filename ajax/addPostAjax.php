<?php

use App\Form\AddPost;
use App\Helpers\File;
use App\Security\Csrf;

header('Content-Type: application/json');

$send = function(array $result){
    echo json_encode($result);
    exit;
};

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

$form->createPost($file);

$send(['status' => 'ok', 'good' => 'Your post has been posted.']);