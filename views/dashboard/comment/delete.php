<?php

use App\Manager\CommentDatabase;
use App\URL\CreateUrl;
use App\Security\Csrf;

// The two kinds of comments : value of the "table" field => comment table
$tables = ['posts' => 'comments_post', 'reviews' => 'comments_film'];

$kind = $_POST['table'] ?? '';
$id   = $_POST['id'] ?? '';

$validKind = is_string($kind) && isset($tables[$kind]);
$validId   = is_string($id) && ctype_digit($id);

// This route only exists in POST : a simple link can never delete a comment
if(!Csrf::validate($_POST['csrf_token'] ?? null)){
    $_SESSION['flash_dash'] = 'Your session expired, nothing was deleted.';
}elseif(!$validKind || !$validId){
    $_SESSION['flash_dash'] = 'Invalid request, nothing was deleted.';
}else{
    (new CommentDatabase())->deleteComment($tables[$kind], (int)$id);
    $_SESSION['flash_dash'] = 'Comment deleted.';
}

header('Location: ' . CreateUrl::url('dashboard/comments', ['table' => $validKind ? $kind : 'posts']));
exit;