<?php

use App\URL\CreateUrl;
use App\Form\AdminLogin;
use App\Security\Csrf;
use App\Security\AdminGuard;

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Where to go after the login : back to the dashboard page that asked for it.
// Only a page of the dashboard of this site is accepted (never another site).
$redirect = $_GET['redirect'] ?? null;
$target   = (is_string($redirect) && str_starts_with($redirect, WWW_ROOT . '/dashboard'))
    ? $redirect
    : CreateUrl::url('dashboard');

if(AdminGuard::isAdmin()){
    header('Location: ' . $target);
    exit;
}

$title  = 'Admin login';
$errors = [];

if(!empty($_POST)){
    $form = new AdminLogin($_POST);

    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors['form'] = ['Your session expired, please try again.'];
    }elseif($form->validateLogin()->resultValidator()){
        $admin = $form->admin();
        AdminGuard::login((int)$admin['id'], (string)$admin['name']);
        header('Location: ' . $target);
        exit;
    }else{
        $errors = $form->returnErrors();
    }
}
?>

<div class="auth-wrap">
    <p class="auth-kicker">Backstage</p>
    <h1 class="auth-title">Admin login</h1>

    <div class="auth-card">
        <section class="auth login">
            <?php foreach($errors as $messages): ?>
                <?php foreach($messages as $message): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($message) ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
                <?= Csrf::field() ?>
                <div class="inputBox">
                    <input type="text" name="name" id="name" autocomplete="username" required>
                    <label for="name">Username</label>
                </div>
                <div class="inputBox">
                    <input type="password" name="password" id="password" autocomplete="current-password" required>
                    <label class="password" for="password">Password</label>
                </div>
                <input type="submit" name="login" value="Login">
            </form>
            <p><a href="<?= CreateUrl::url('home') ?>">Back to the website</a></p>
        </section>
    </div>
</div>