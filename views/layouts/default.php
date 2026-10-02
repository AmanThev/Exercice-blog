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
    <link rel="icon" type="image/x-icon" href="<?= URLPublic::publicPath('img/layout/favicon.ico'); ?>">
    <script src="<?= PUBLIC_PATH ?>/js/scrollToError.js"></script>
    <script src="https://kit.fontawesome.com/2c5e081666.js"></script>
</head>
<body>

<header id="top" class="marquee">
<div class="marquee-frame">
    <h1>Cinéma</h1>
    <?php if(isset($_SESSION['name'])): ?>
        <p class="site-sub">Your seat is waiting, <?= $_SESSION['name']  ?></p>
    <?php else: ?>
        <p class="site-sub">Now Playing</p>
    <?php endif; ?>
</div>
<nav>
    <a href="<?= CreateUrl::url('home') ?>">Home</a>
    <a href="<?= CreateUrl::url('blog') ?>">Blog</a>
    <a href="<?= CreateUrl::url('reviews') ?>">Reviews</a>
    <a href="<?= CreateUrl::url('forum') ?>">Forum</a>
</nav>
<?php if(isset($_SESSION['name'])): ?>
    <a class="login" href="<?= CreateUrl::url('authentication/logout', ['redirect' => $_SERVER['REQUEST_URI']]) ?>">Logout</a>
<?php else: ?>    
    <a class="login" href="<?= CreateUrl::url('authentication/login', ['redirect' => $_SERVER['REQUEST_URI']]) ?>">Login</a>  
    <?php endif ?>
</header>
<div class="bulbs"></div>

<div class="container-fluid">
    <?= $content ?>

    
    <?php require_once('debugTime.php'); ?>
</div>

<div class="bulbs"></div>
<footer class="site-footer">
    <div class="footer-inner">
        <p class="footer-brand">Cinéma</p>
        <nav class="footer-links">
            <a href="<?= CreateUrl::url('legal') ?>">Legal Notice</a>
            <a href="<?= CreateUrl::url('privacy') ?>">Privacy</a>
        </nav>
        <p class="footer-copy">&copy; <?= date('Y') ?> Cinéma — Now Playing</p>
    </div>
</footer>

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
