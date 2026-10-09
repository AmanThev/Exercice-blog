<?php
use App\URL\UrlPublic;
use App\URL\CreateUrl;

$current  = rtrim((string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$navItems = [
    ['dashboard',          'Dashboard', 'fas fa-tachometer-alt', true],
    ['dashboard/posts',    'Posts',     'fas fa-edit',           false],
    ['dashboard/reviews',  'Reviews',   'fas fa-film',           false],
    ['dashboard/comments', 'Comments',  'fas fa-comment',        false],
    ['dashboard/forum',    'Forum',     'fab fa-forumbee',       false],
    ['dashboard/users',    'Users',     'fas fa-user',           false],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard'; ?></title>
    <!-- Bootstrap provisoire : à retirer quand toutes les pages du dashboard auront été refaites -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?= URLPublic::publicPath('css/profile.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= URLPublic::publicPath('css/dashboard.css'); ?>">
    <link rel="icon" type="image/x-icon" href="<?= URLPublic::publicPath('img/layout/favicon.ico'); ?>">
    <script src="https://kit.fontawesome.com/2c5e081666.js"></script>
    <script
            src="https://code.jquery.com/jquery-3.6.0.js"
            integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
            crossorigin="anonymous">
    </script>
</head>
<body class="dash">

<div class="dash-topbar">
    <h1>Admin Panel</h1>
    <a href="<?= CreateUrl::url('home') ?>"><i class="fas fa-chevron-left"></i> Back to website</a>
</div>

<div class="dash-shell">
    <nav class="dash-nav">
        <p class="dash-nav-title">Administration</p>
        <?php foreach($navItems as [$path, $label, $icon, $exact]): ?>
            <?php
                $href   = CreateUrl::url($path);
                $active = $exact ? ($current === $href) : str_starts_with($current, $href);
            ?>
            <a href="<?= $href ?>" class="<?= $active ? 'active' : '' ?>"><i class="<?= $icon ?>"></i><span><?= $label ?></span></a>
        <?php endforeach; ?>
    </nav>

    <main class="dash-main">
        <?= $content ?>
    </main>
</div>

</body>
</html>