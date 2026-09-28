<?php

use App\URL\CreateUrl;
use App\Url\ExplodeUrl;
use App\Form\PasswordReset;
use App\Security\Csrf;

$title = "Reset your password";

$urlParts = ExplodeUrl::explodePath($_GET['url']);
$token    = $urlParts[2] ?? null;

$tokenValid = !empty($token) && PasswordReset::tokenIsValid($token);

if(!empty($_POST) && $tokenValid){
    $data = new PasswordReset($_POST);

    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors = ['csrf' => ['Your session expired, please try again.']];
    }else{
        $data->resetPassword($token);

        if($data->resultValidator()){
            header('Location: ' . CreateUrl::url('authentication/reset-success'));
            exit;
        }else{
            $errors = $data->returnErrors();
            if(!empty($errors['token'])){
                $tokenValid = false;
            }
        }
    }
}
?>

<div class="auth-wrap">
    <p class="auth-kicker">New scene, new password</p>
    <h1 class="auth-title">Reset your Password</h1>

    <div class="auth-card">
        <section class="auth reset">
            <?php if(!$tokenValid): ?>
                <p>This reset link is invalid or has expired. For your security, password reset links only stay active for 15 minutes after you request them. <a href="<?= CreateUrl::url('authentication/forget') ?>">Request a new one</a>.</p>
            <?php else: ?>
                <?php if(!empty($errors['csrf'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $errors['csrf'][0] ?></p>
                <?php endif; ?>
                <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
                    <?= Csrf::field() ?>
                    <div class="inputBox">
                        <input type="password" name="password" id="password" autocomplete="new-password" required>
                        <label for="password">New password</label>
                        <?php if(!empty($errors)): ?>
                            <?= $data->arrayKeyExist('password', $errors) ?>
                        <?php endif; ?>
                    </div>
                    <div class="inputBox">
                        <input type="password" name="password2" id="password2" autocomplete="new-password" required>
                        <label for="password2">Confirm new password</label>
                        <?php if(!empty($errors)): ?>
                            <?= $data->arrayKeyExist('password2', $errors) ?>
                        <?php endif; ?>
                    </div>
                    <input type="submit" name="reset" value="Reset password">
                </form>
            <?php endif; ?>
        </section>
    </div>
</div>