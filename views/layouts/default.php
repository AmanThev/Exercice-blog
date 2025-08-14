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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/2c5e081666.js"></script>
</head>
<body>

<header id="top">
<div class="lighting">
    <h1>Cinema</h1>
    <?php if(isset($_SESSION['name'])): ?>
        <p>Hi <?= $_SESSION['name']  ?></p> 
    <?php endif; ?>
    <nav>
        <div class="separate-header"></div>
            <a href="<?= CreateUrl::url('home') ?>">Home</a>
            <a href="<?= CreateUrl::url('blog') ?>">Blog</a>
            <a href="<?= CreateUrl::url('reviews') ?>">Reviews</a>
            <a href="<?= CreateUrl::url('forum') ?>">Forum</a>
        <div class="separate-header"></div>
    </nav>    
</div>
<?php if(isset($_SESSION['name'])): ?>
    <a class="login" href="<?= CreateUrl::url('authentication/logout') ?>">Logout</a>
<?php else: ?>    
    <a class="login" href="<?= CreateUrl::url('authentication/login') ?>">Login</a>    
<?php endif ?>
</header>

<div class="container-fluid">
    <?= $content ?>

    <?php require_once('debugTime.php'); ?>
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