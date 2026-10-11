<?php

use App\Manager\ForumDatabase;
use App\URL\CreateUrl;
use App\Security\Csrf;

// Action sent by the forms of the forum page => [method of ForumDatabase, message shown afterwards]
$actions = [
    'delete_topic'       => ['deleteTopic',       'Topic deleted.'],
    'delete_subcategory' => ['deleteSubCategory', 'Sub-category deleted, with its topics and messages.'],
    'delete_category'    => ['deleteCategory',    'Category deleted, with its sub-categories, topics and messages.'],
    'close_topic'        => ['closeTopic',        'Topic closed.'],
    'reopen_topic'       => ['reopenTopic',       'Topic reopened.'],
];

$action = $_POST['action'] ?? '';
$id     = $_POST['id'] ?? '';

// This route only exists in POST : a simple link can never delete anything
if(!Csrf::validate($_POST['csrf_token'] ?? null)){
    $_SESSION['flash_dash'] = 'Your session expired, nothing was changed.';
}elseif(!is_string($action) || !isset($actions[$action]) || !is_string($id) || !ctype_digit($id)){
    $_SESSION['flash_dash'] = 'Invalid request, nothing was changed.';
}else{
    [$method, $message] = $actions[$action];
    (new ForumDatabase())->$method((int)$id);
    $_SESSION['flash_dash'] = $message;
}

header('Location: ' . CreateUrl::url('dashboard/forum'));
exit;