<?php

use App\URL\CreateUrl;
use App\Form\Authentication;
use App\Security\Csrf;

$title = "Login";


if(!empty($_POST)){
    $data = new Authentication($_POST);
    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors = ['csrf' => ['Your session expired, please try again.']];
    }else{
        if($data->validateAuth()->resultValidator()){
            if($data->checkPassword()->resultValidator()){
                $_SESSION['name'] = $data->getField('name');
                header('Location: ' . CreateUrl::url('authentication/success'));
                exit;
            }else{
                $errors = $data->returnErrors();
            }
        }else{
            $errors = $data->returnErrors();
        }
    }
}

?>

<div class="auth-wrap">
    <p class="auth-kicker">Private screening</p>
    <h1 class="auth-title">Login</h1>

    <div class="auth-card">
        <section class="auth login">
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
                    <input type="password" name="password" id="password" required>
                    <label class="password" for="password">Password</label>
                    <small>Password gone? Probably got a role in another film. <a href="<?= CreateUrl::url('authentication/forget') ?>">Click here to recast it.</a></small>
                    <?php if(!empty($errors)): ?>
                        <?= $data->arrayKeyExist('password', $errors) ?>
                    <?php endif; ?>
                </div>
                    <input type="submit" name="login" value="Login">
            </form>
            <p>The Adventure Starts Here... <a href="<?= CreateUrl::url('authentication/register') ?>">Sign Up Now to Join the Cast.</a></p>
        </section>
    </div>
</div>
