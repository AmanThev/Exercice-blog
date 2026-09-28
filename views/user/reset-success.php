<?php

use App\URL\CreateUrl;

$title = "Password Reset";
$loginUrl = CreateUrl::url('authentication/login');
?>

<div class="auth-wrap">
    <p class="auth-kicker">Password reset</p>
    <h1 class="auth-title">All set!</h1>

    <div class="auth-card">
        <section class="auth success">
            <p>Your password has been changed successfully.</p>
            <p>You'll be redirected to the login page in a few seconds...</p>
            <a href="<?= $loginUrl ?>" class="auth-btn">Go to Login now</a>
        </section>
    </div>
</div>

<script>
    setTimeout(function(){
        window.location.href = "<?= $loginUrl ?>";
    }, 3000);
</script>
