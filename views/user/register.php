<?php

use App\URL\CreateUrl;
use App\HTML\Form;
use App\Form\AddUser;
use App\Security\Csrf;

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

if(!empty($_SESSION['id'])){
    header('Location: ' . CreateUrl::url('home'));
    exit;
}

$title = "Register";

if(!empty($_POST)){
    $data = new AddUser($_POST);
    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors = ['csrf' => ['Your session expired, please try again.']];
    }else{
        if($data->validateUser()->resultValidator()){
            $id = $data->addMembers();
            // session_regenerate_id(true); // décommente si tu veux éviter la fixation de session
            $_SESSION['id']   = $id;
            $_SESSION['name'] = $data->getField('name');
            header('Location: ' . CreateUrl::url('authentication/success'));
            exit;
        }else{
            $errors = $data->returnErrors();
        }
    }
}
?>

<div class="auth-wrap">
    <p class="auth-kicker">New to the audience</p>
    <h1 class="auth-title">Sign up</h1>

    <div class="auth-card">
        <section class="auth signup">
            <?php if(!empty($errors['csrf'])): ?>
                <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $errors['csrf'][0] ?></p>
            <?php endif; ?>
            <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
                <?= Csrf::field() ?>
                <div class="inputBox">
                    <input type="text" name="name" id="name" required>
                    <label for="name">Username</label>
                    <?php if(!empty($errors)): ?>
                        <?= $data->arrayKeyExist('name', $errors) ?>
                    <?php endif; ?>
                </div>
                <div class="inputBox">
                    <input type="text" name="email" id="email" required>
                    <label for="email">Email</label>
                    <?php if(!empty($errors)): ?>
                        <?= $data->arrayKeyExist('email', $errors) ?>
                    <?php endif; ?>
                </div>
                <div class="inputBox">
                    <input type="password" name="password" id="password" required>
                    <label class="password" for="password">Password</label>
                </div>
                <div class="inputBox">
                    <input type="password" name="password2" id="password2" required>
                    <label for="password2">Confirm password</label>
                    <?php if(!empty($errors)): ?>
                        <?= $data->arrayKeyExist('password2', $errors) ?>
                    <?php endif; ?>
                </div>
                    <input type="submit" name="signup" value="Sign up">
            </form>
            <p>Already have an account, <a href="<?= CreateUrl::url('authentication/login') ?>" class=""> login</a></p>
        </section>
    </div>
</div>