<?php

use App\URL\CreateUrl;
use App\Form\Authentication;

$title = "Login";


if(!empty($_POST)){
    $data = new Authentication($_POST);
    if($data->validateAuth()->resultValidator()){
        $data->checkPassword()->resultValidator();
        $_SESSION['name'] = $data->getField('name');
        header('Location: ' . CreateUrl::url('authentication/success'));
    }else{
        $errors = $data->returnErrors();
    }
}

?>

<h1 class="auth">Login</h1>

<section class="auth login">
    <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
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