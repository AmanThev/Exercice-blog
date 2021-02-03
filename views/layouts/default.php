<?php

use App\URL\UrlPublic;
use App\URL\CreateUrl;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Website Cinema'; ?></title>
    <link rel="stylesheet" type="text/css" href="<?= URLPublic::publicPath('css/style.css'); ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/2c5e081666.js"></script>
</head>
<body>

<header id="top">
<div class="lighting">
    <h1>Cinema</h1>
    <p>Welcome, ...</p> <!-- Nom de l'user -->
    <nav>
        <div class="separate-header"></div>
            <a href="<?= CreateUrl::url('home') ?>">Home</a>
            <a href="<?= CreateUrl::url('blog') ?>">Blog</a>
            <a href="<?= CreateUrl::url('reviews') ?>">Reviews</a>
            <a href="<?= CreateUrl::url('forum') ?>">Forum</a>
            <a href="<?= CreateUrl::url('quiz') ?>">Quiz</a>
        <div class="separate-header"></div>
    </nav>    
</div>
<a class="login" href="<?= CreateUrl::url('authentication/login') ?>">Login</a>    
</header>

<div class="container-fluid">
    <?= $content ?>

    <footer class="bg-light py-4 footer">
        <div class="container">
            <?php if (defined('DEBUG_TIME')): ?>
            Page générée en <?= round(1000 * (microtime(true) - DEBUG_TIME)) ?> ms
            <?php endif ?>
        </div>
    </footer>
</div>

<div><a id="scrolltotop" class="scrollInvisible" href="#top"></a></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  window.onscroll = function(ev) {
    document.getElementById("scrolltotop").className = (window.pageYOffset > 100) ? "scrollVisible" : "scrollInvisible";
  };
});
</script>
</body>
</html>