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
    <link rel="stylesheet" type="text/css" href="<?= URLPublic::publicPath('css/auth.css'); ?>">
    <link rel="icon" type="image/x-icon" href="<?= URLPublic::publicPath('img/layout/favicon.ico'); ?>">
    <script src="https://kit.fontawesome.com/2c5e081666.js"></script>
</head>
<body>

<header>
<a class="homepage" href="<?= CreateUrl::url('home') ?>"><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i>Back to home</a>
</header>

<div class="container-fluid">
    <?= $content ?>
</div>
</body>
</html>