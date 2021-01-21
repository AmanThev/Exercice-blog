<?php

use App\URL\ExplodeUrl;
use App\Manager\UserDatabase;


$url    = new ExplodeUrl($_GET['url']);
$slug   = $url->getSlugName();

$title = $slug;

$admin = new UserDatabase();
$admin = $admin->getAdminByName($slug);

?>

<div class="top-profile-admin">
    <aside class="photo-admin">
        <img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg">
    </aside>
    
    <section class="intro-admin">
        <h2><?= $admin->getName() ?><span>- <?= $admin->getPosition() ?></span></h2>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda nobis non tempora dicta labore, ab nesciunt explicabo ratione, iusto ea odio! Eum, laudantium labore consequatur inventore sed reprehenderit enim repudiandae.</p>
    </section>
</div>

<section class="data-admin">
    <h2>What have you done?</h2>
    <p>You have write 2 posts</p>
<ul>
    <li>Title post 
        <span><i class="fas fa-comment"></i></span>
        <span><i class="fas fa-thumbs-up"></i></span>
        <span><i class="fas fa-thumbs-down"></i></span>
    </li>
</ul>

<p>You have write 2 review</p>
<ul>
    <li>Title film 
        <span><i class="fas fa-comment"></i></span>
        <span><i class="fas fa-thumbs-up"></i></span>
        <span><i class="fas fa-thumbs-down"></i></span>
    </li>
</ul>
</section>