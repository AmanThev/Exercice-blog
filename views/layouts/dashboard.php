<?php
use App\URL\UrlPublic;
use App\URL\CreateUrl;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" type="text/css" href="<?= URLPublic::publicPath('css/dashboard.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= URLPublic::publicPath('css/profile.css'); ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="<?= URLPublic::publicPath('img/layout/favicon.ico'); ?>">
    <script src="https://kit.fontawesome.com/2c5e081666.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script
			  src="https://code.jquery.com/jquery-3.6.0.js"
			  integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
			  crossorigin="anonymous">
    </script>
</head>
<body>
    <div class="title-dash"> 
        <h1>Admin Panel</h1>
        <a href="<?= CreateUrl::url('home') ?>"><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i>Back to website</a>
    </div>
<header>
    <nav>
        <h2>Administration</h2>
        <a href="<?= CreateUrl::url('dashboard') ?>"><i class="fas fa-tachometer-alt"></i>DashBoard</a>
        <a href="<?= CreateUrl::url('dashboard/posts') ?>"><i class="fas fa-edit"></i>Posts</a>
        <a href="<?= CreateUrl::url('dashboard/reviews') ?>"><i class="fas fa-film"></i>Reviews</a>
        <a href="<?= CreateUrl::url('dashboard/comments') ?>"><i class="fas fa-comment"></i>Comments</a>
        <a href="<?= CreateUrl::url('dashboard/forum') ?>"><i class="fab fa-forumbee"></i>Forum</a>
        <a href="<?= CreateUrl::url('quiz') ?>"><i class="fas fa-question-circle"></i>Quiz</a>
        <a href="<?= CreateUrl::url('dashboard/users') ?>"><i class="fas fa-user"></i>Users</a>
    </nav>    
</header>

<div class="container-fluid">
    <?= $content ?>

    <?php require_once('debugTime.php'); ?>
</div>
</body>
</html>