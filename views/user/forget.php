<?php

use App\URL\CreateUrl;
use App\Form\PasswordReset;

$tile = "Forgot password";

if(!empty($_POST)){
    $data = new PasswordReset($_POST);
    if($data->validEmail()->resultValidator()){
        $data->sendEmail();
        dd($data);
        // $data->checkPassword()->resultValidator();
        // $_SESSION['name'] = $data->getField('name');
        // header('Location: ' . CreateUrl::url('authentication/success'));
    }else{
        $errors = $data->returnErrors();
    }
}
?>

<h1 class="auth">Reset your Password</h1>

<section class="auth forget">
    <form action="" method="post">
        <div class="inputBox">
            <input type="email" id="email" name="email">
            <label for="email">Enter your email :</label>
            <?php if(!empty($errors)): ?>
                <?= $data->arrayKeyExist('email', $errors) ?>
            <?php endif; ?>
        </div>
        <input type="submit" name="reset" value="Reset">
    </form>