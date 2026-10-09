<?php

use App\Form\EditProfile;
use App\Manager\UserDatabase;
use App\Manager\Exception\NotFoundException;
use App\URL\CreateUrl;
use App\Security\Csrf;

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

if(empty($_SESSION['id'])){
    header('Location: ' . CreateUrl::url('authentication/login', ['redirect' => $_SERVER['REQUEST_URI']]));
    exit;
}

try{
    $member = (new UserDatabase())->getMemberById((int)$_SESSION['id']);
}catch(NotFoundException $e){
    // Le compte n'existe plus : on referme la session
    header('Location: ' . CreateUrl::url('authentication/logout'));
    exit;
}

$title  = 'My profile';
$errors = [];
$form   = null;
$flash  = $_SESSION['flash_profile'] ?? null;
unset($_SESSION['flash_profile']);

if(!empty($_POST)){
    $form   = new EditProfile($_POST);
    $action = $_POST['action'] ?? '';

    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors = ['csrf' => ['Your session expired, please try again.']];
    }elseif($action === 'description'){
        if($form->validateDescription()->resultValidator()){
            $form->updateDescription($member->getId());
            $_SESSION['flash_profile'] = 'Your description has been updated.';
            header('Location: ' . CreateUrl::url('account'));
            exit;
        }
        $errors = $form->returnErrors();
    }elseif($action === 'password'){
        if($form->validatePassword($member->getPassword())->resultValidator()){
            $form->updatePassword($member->getId());
            session_regenerate_id(true);
            $_SESSION['flash_profile'] = 'Your password has been changed.';
            header('Location: ' . CreateUrl::url('account'));
            exit;
        }
        $errors = $form->returnErrors();
    }
}

// Si le formulaire "description" vient d'échouer, on garde ce que l'utilisateur a tapé
$descriptionValue = (($_POST['action'] ?? '') === 'description' && is_string($_POST['description'] ?? null))
    ? $_POST['description']
    : $member->getRawDescription();
?>

<section>

<nav class="forum-breadcrumb">
    <a href="<?= CreateUrl::url('home') ?>"><i class="fas fa-home"></i> Home</a>
    <span class="sep">/</span>
    My profile
</nav>

<div class="account-narrow">

    <div class="section-heading"><h1>My profile</h1><span class="rule"></span></div>

    <p class="account-intro">
        Signed in as <strong><?= $member->getName() ?></strong> &middot;
        <a href="<?= CreateUrl::urlSlugOnly('user', (string)$member->getId()) ?>">View my public profile</a>
    </p>

    <?php if($flash): ?>
        <p class="success"><?= htmlspecialchars($flash) ?></p>
    <?php endif; ?>

    <?php if(!empty($errors['csrf'])): ?>
        <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $errors['csrf'][0] ?></p>
    <?php endif; ?>

    <div class="section-heading"><h2>About you</h2><span class="rule"></span></div>

    <form class="comment-form" method="post" action="">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="description">

        <label for="description">Description :</label>
        <textarea name="description" id="description" rows="6"><?= htmlspecialchars($descriptionValue) ?></textarea>
        <?php if(!empty($errors)): ?>
            <?= $form->arrayKeyExist('description', $errors) ?>
        <?php endif; ?>

        <button type="submit">Save description</button>
    </form>

    <div class="section-heading"><h2>Password</h2><span class="rule"></span></div>

    <form class="comment-form" method="post" action="">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="password">

        <label for="current_password">Current password :</label>
        <input type="password" name="current_password" id="current_password" autocomplete="current-password" required>
        <?php if(!empty($errors)): ?>
            <?= $form->arrayKeyExist('current_password', $errors) ?>
        <?php endif; ?>

        <label for="password">New password :</label>
        <input type="password" name="password" id="password" autocomplete="new-password" required>
        <?php if(!empty($errors)): ?>
            <?= $form->arrayKeyExist('password', $errors) ?>
        <?php endif; ?>

        <label for="password2">Confirm new password :</label>
        <input type="password" name="password2" id="password2" autocomplete="new-password" required>
        <?php if(!empty($errors)): ?>
            <?= $form->arrayKeyExist('password2', $errors) ?>
        <?php endif; ?>

        <button type="submit">Change password</button>
    </form>

</div>

</section>