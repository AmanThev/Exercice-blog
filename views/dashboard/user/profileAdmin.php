<?php

use App\URL\ExplodeUrl;
use App\Manager\UserDatabase;
use App\Manager\FilmDatabase;
use App\Manager\PostDatabase;
use App\Manager\CommentDatabase;


$url            = new ExplodeUrl($_GET['url']);
$slug           = $url->getSlugName();

$title          = $slug;

$admin          = new UserDatabase();
$admin          = $admin->getAdminByName($slug);

$filmsDatabase  = new FilmDatabase();
$films          = $filmsDatabase->getFilmByAdminId($admin->getId());
$totalComFilms  = new CommentDatabase();
$totalReviews   = count($films);


$postsDatabase  = new PostDatabase();
$posts          = $postsDatabase->getPostByAdminId($admin->getId());
$totalComPosts  = new CommentDatabase();
$totalPosts     = count($posts);


?>

<div class="top-profile-admin">
    <aside class="photo-admin">
        <img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg">
    </aside>
    
    <section class="intro-admin">
        <h2><?= $admin->getName() ?><span><?= $admin->getPosition() ?></span></h2>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda nobis non tempora dicta labore, ab nesciunt explicabo ratione, iusto ea odio! Eum, laudantium labore consequatur inventore sed reprehenderit enim repudiandae.</p>
    </section>
</div>

<section class="data-admin">
    <h2>What have you done?</h2>
    <h3>Posts</h3>
    <?php if($totalPosts === 0):?>
        <p class="admin-empty">🤔 It seems you have write nothing</p>
    <?php else: ?>
        <p class="admin-data">You have write <?php echo $totalPosts > 1 ? ' '.$totalPosts.' Posts' : ' '.$totalPosts.' Post' ?> :</p>
        <ul>
            <?php foreach($posts as $post): ?>
                <li><p><i class="fas fa-edit"></i><?= $post->getTitle() ?></p>
                    <span><i class="fas fa-comment"></i><?= $totalComPosts->totalComment('comments_post', $post->getId()) ?></span>
                    <span><i class="fas fa-thumbs-up"></i><?= $post->getLike() ?></span>
                    <span><i class="fas fa-thumbs-down"></i><?= $post->getDislike() ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

            <div id="separate-profile-admin"></div>

    <h3>Reviews</h3>
    <?php if($totalReviews === 0):?>
        <p class="admin-empty">🤔 It seems you have write nothing</p>
    <?php else: ?>
        <p class="admin-data">You have write <?php echo $totalReviews > 1 ? ' '.$totalReviews.' Reviews' : ' '.$totalReviews.' Review' ?> :</p>
        <ul>
            <?php foreach($films as $film): ?>
                <li><p><i class="fas fa-film"></i><?= $film->getTitle() ?></p>
                    <span><i class="fas fa-comment"></i><?= $totalComFilms->totalComment('comments_film', $film->getId()) ?></span>
                    <span><i class="fas fa-poll-h"></i><?= $filmsDatabase->totalVote($film->getId()) ?></span>
                    <span><i class="fas fa-star"></i><?= $filmsDatabase->totalRating($film->getId()) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>