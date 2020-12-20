<?php

use App\Manager\Database;
use App\Manager\UserDatabase;


$title = 'Admins';

$administrator = new UserDatabase();
$admins = $administrator->getAdminsPresentationPage('Admin');
$modos = $administrator->getAdminsPresentationPage('Modo');

?>

<h1 class="admin-page">Our team</h1>
<h2 class="title-admin-page">Administrator</h2>

<section class="admin-row">
    <?php foreach($admins as $admin): ?>
        <div class="profile-box">
            <h4><?= $admin->getName() ?></h4>
            <img src="<?= PUBLIC_PATH ?>/img/photoProfile/admin.jpg">
                <div class="social-box">
                    <i class="fab fa-facebook"></i>
                    <i class="fab fa-twitter"></i>
                    <i class="fab fa-linkedin"></i>
                    <i class="fab fa-instagram"></i>
                </div>
            <p><?= $admin->getDescription() ?></p>
        </div>
    <?php endforeach; ?>
</section>

<div id="separate-admin"></div>

<h2 class="title-admin-page">Moderator</h2>
<section class="modo-row">
    
    <?php foreach($modos as $modo): ?>
    <div class="profile-box">
        <h4><?= $modo->getName() ?></h4>
            <img src="<?= PUBLIC_PATH ?>/img/photoProfile/modo.jpg">
                <div class="social-box">
                    <i class="fab fa-facebook"></i>
                    <i class="fab fa-twitter"></i>
                    <i class="fab fa-linkedin"></i>
                    <i class="fab fa-instagram"></i>
                </div>
            <p><?= $modo->getDescription() ?></p>
    </div>
    <?php endforeach; ?>
</section>