<?php

use App\URL\ExplodeUrl;
use App\Manager\UserDatabase;
use App\Manager\FilmDatabase;
use App\Manager\PostDatabase;


$url            = new ExplodeUrl($_GET['url']);
$slug           = $url->getSlugName();

$title          = $slug;

$admin          = new UserDatabase();
$admin          = $admin->getAdminByName($slug);

$films          = new FilmDatabase();
$films          = $films->getFilmByAdminId($admin->getId());
$totalReviews   = count($films);

$posts          = new PostDatabase();
$posts          = $posts->getPostByAdminId($admin->getId());
$totalPosts     = count($posts);

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
    <p>You have write <?php echo $totalPosts > 1 ? ' '.$totalPosts.' Posts' : ' '.$totalPosts.' Post' ?></p>
<?php foreach($posts as $post): ?>
<ul>
    <li><?= $post->getTitle() ?> 
        <span><i class="fas fa-comment"></i></span>
        <span><i class="fas fa-thumbs-up"></i></span>
        <span><i class="fas fa-thumbs-down"></i></span>
    </li>
</ul>
<?php endforeach; ?>


<p>You have write <?php echo $totalReviews > 1 ? ' '.$totalReviews.' Reviews' : ' '.$totalReviews.' Review' ?></p>
<?php foreach($films as $film): ?>
<ul>
    <li><?= $film->getTitle() ?> 
        <span><i class="fas fa-comment"></i></span>
        <span><i class="fas fa-thumbs-up"></i></span>
        <span><i class="fas fa-thumbs-down"></i></span>
    </li>
</ul>
<?php endforeach; ?>
</section>