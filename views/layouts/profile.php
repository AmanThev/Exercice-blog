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
    <link rel="stylesheet" type="text/css" href="<?= URLPublic::publicPath('css/profile.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= URLPublic::publicPath('css/dashboard.css'); ?>">
    <link rel="icon" type="image/x-icon" href="<?= URLPublic::publicPath('img/layout/favicon.ico'); ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/2c5e081666.js"></script>
</head>

<body id="member-profile-page">

<div class="title-dash">
    <h1>Welcome to you profile page, <?= $slug ?> 😁</h1>
    <a class="homepage" href="<?= CreateUrl::url('home') ?>"><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i>Back to home</a>
</div>

<header>
    <nav>
        <h2>Administration</h2>
        <a href="#"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </nav>  
</header>

<div class="container-fluid">
    <?= $content ?>

    <?php require_once('debugTime.php'); ?>
</div>
</body>
</html>