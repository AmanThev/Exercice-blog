<?php

use App\URL\CreateUrl;
use App\Form\PasswordReset;
use App\Security\Csrf;

$title = "Forgot password";

if(!empty($_POST)){
    $data = new PasswordReset($_POST);
    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors = ['csrf' => ['Your session expired, please try again.']];
    }else{
        if($data->validEmail()->resultValidator()){
            $data->sendEmail();
            $success = "If an account exists with this email, a reset link has been sent.";
        }else{
            $errors = $data->returnErrors();
        }
    }
}
?>

<div class="auth-wrap">
    <p class="auth-kicker">Lost your ticket?</p>
    <h1 class="auth-title">Reset your Password</h1>

    <div class="auth-card">
        <section class="auth forget">
            <?php if(!empty($success)): ?>
                <p><?= $success ?></p>
            <?php else: ?>
                <?php if(!empty($errors['csrf'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $errors['csrf'][0] ?></p>
                <?php endif; ?>
                <form action="" method="post">
                    <?= Csrf::field() ?>
                    <div class="inputBox">
                        <input type="text" name="email" id="email" required>
                        <label for="email">Enter your mail</label>
                        <?php if(!empty($errors)): ?>
                            <?= $data->arrayKeyExist('email', $errors) ?>
                        <?php endif; ?>
                    </div>
                    <input type="submit" name="reset" value="Reset">
                </form>
            <?php endif; ?>
        </section>
    </div>
</div>