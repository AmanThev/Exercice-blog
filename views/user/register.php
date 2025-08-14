<?php

use App\URL\CreateUrl;
use App\HTML\Form;
use App\Form\AddUser;

$title = "Register";

if(!empty($_POST)){
    $data = new AddUser($_POST);
    if($data->validateUser()->resultValidator()){
        $data->addMembers();
        $_SESSION['name'] = $data->getField('name');
        header('Location: ' . CreateUrl::url('authentication/success'));
    }else{
        $errors = $data->returnErrors();
    }
}
?>

<h1 class="auth">Sign up</h1>

<section class="auth signup">
    <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
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
                <?= $data->arrayKeyExist('password', $errors) ?>
            <?php endif; ?>
        </div>
            <input type="submit" name="signup" value="Sign up">
    </form>
    <p>Already have an account, <a href="<?= CreateUrl::url('authentication/login') ?>" class=""> login</a></p>
</section>