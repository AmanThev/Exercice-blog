<?php

use App\URL\CreateUrl;

$title = 'Success';
$name = $_SESSION['name'];

?>

<div class="auth-wrap">
    <p class="auth-kicker">Casting confirmed</p>
    <h1 class="auth-title">Welcome</h1>

    <div class="auth-card">
        <section class="auth success">
            <p>Glad to have you in the cast, <strong><?= $name ?></strong>.</p>
            <a href="<?= CreateUrl::url('home') ?>" class="auth-btn">Enter the theater</a>
        </section>
    </div>
</div>
