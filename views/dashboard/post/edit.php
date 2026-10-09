<?php

use App\URL\ExplodeUrl;
use App\URL\CreateUrl;
use App\Manager\PostDatabase;
use App\Form\EditPost;
use App\Security\Csrf;

$url    = new ExplodeUrl($_GET['url']);
$id     = $url->getId();

$postDb = new PostDatabase();
$post   = $postDb->getPostById($id);

$title  = 'Edit post';
$errors = [];

if(!empty($_POST)){
    $form = new EditPost($_POST);
    $file = $_FILES['picture'] ?? [];

    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors['form'][] = 'Your session expired, please try again.';
    }elseif($form->validateEdit($post->getTitle(), $file)->resultValidator()){
        $form->updatePost($id, $file);

        $_SESSION['flash_dash'] = 'Post updated.';
        header('Location: ' . CreateUrl::url('dashboard/posts'));
        exit;
    }else{
        $errors = $form->returnErrors();
    }
}

// Valeurs affichées : ce que l'utilisateur vient de taper, sinon celles de la base
// (celles de la base sont déjà encodées par le modèle Post, on ne les ré-échappe pas)
$titleValue   = isset($_POST['title'])   ? htmlspecialchars($_POST['title'])   : $post->getTitle();
$contentValue = isset($_POST['content']) ? htmlspecialchars($_POST['content']) : $post->getContent();
$isPublic     = !empty($_POST) ? !empty($_POST['public']) : $post->getPublic() === 1;
$picture      = $post->getPicture();

$fieldError = fn(string $key) => isset($errors[$key]) ? htmlspecialchars(implode(' ', $errors[$key])) : '';
?>

<div class="dash-pagehead">
    <h2 class="dash-title">Edit post</h2>
    <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/posts') ?>"><i class="fas fa-chevron-left"></i> Back to posts</a>
</div>

<form class="dash-form" action="" method="post" enctype="multipart/form-data" novalidate>
    <?= Csrf::field() ?>

    <?php if(!empty($errors['form'])): ?>
        <p class="dash-message error"><?= htmlspecialchars($errors['form'][0]) ?></p>
    <?php endif; ?>

    <div class="field">
        <label for="author">Author</label>
        <input type="text" id="author" value="<?= htmlspecialchars($post->getAuthor()) ?>" readonly>
        <small class="hint">The author cannot be changed.</small>
    </div>

    <div class="field">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="<?= $titleValue ?>" autocomplete="off">
        <p class="field-error"><?= $fieldError('title') ?></p>
    </div>

    <div class="field">
        <label for="content">Content</label>
        <textarea name="content" id="content" rows="14"><?= $contentValue ?></textarea>
        <small class="hint">At least 20 characters.</small>
        <p class="field-error"><?= $fieldError('content') ?></p>
    </div>

    <div class="field">
        <span class="field-label">Current picture</span>
        <?php if($picture !== ''): ?>
            <div class="current-picture"><img src="<?= PUBLIC_PATH ?>/img/postPicture/<?= $picture ?>" alt="Current picture"></div>
        <?php else: ?>
            <small class="hint">This post has no picture yet.</small>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="picture">Replace the picture</label>
        <div class="file-row">
            <input type="file" name="picture" id="picture" accept="image/png,image/jpeg,image/gif">
            <button type="button" class="file-clear" id="clear-picture" aria-label="Cancel the new picture" hidden><i class="fas fa-times"></i></button>
        </div>
        <div class="picture-preview" id="picture-preview" hidden><img src="" alt="Preview"></div>
        <small class="hint">Leave empty to keep the current picture. PNG, JPG or GIF, 2 MB maximum.</small>
        <p class="field-error"><?= $fieldError('picture') ?></p>
    </div>

    <div class="field">
        <span class="field-label">Visibility</span>
        <label class="switch">
            <input class="switch-input" type="checkbox" id="checkbox" name="public" value="1" <?= $isPublic ? 'checked' : '' ?>>
            <span class="switch-label" data-public="public" data-private="private"></span>
            <span class="switch-handle"></span>
        </label>
    </div>

    <div class="form-actions">
        <button class="dash-submit" type="submit"><span>Save changes</span></button>
        <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/posts') ?>">Cancel</a>
    </div>
</form>

<script src="<?= PUBLIC_PATH ?>/js/imagePreview.js"></script>