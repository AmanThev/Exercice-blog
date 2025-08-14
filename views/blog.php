<?php

use App\Manager\PostDatabase;
use App\Manager\UserDatabase;
use App\SQL\Paginate;
use App\URL\CreateUrl;
use App\HTML\Form;

$title      = 'Blog';

$author     = new UserDatabase();

$new        = new PostDatabase();
$new        = $new->getLastPost();

$pagination = new PostDatabase();

$posts      = new PostDatabase();
$posts      = $posts->getPostsPublic();

$searchForm = new Form($_POST);

?>

<h1 id="title-blog">Blog</h1>

<section class="top-blog">
    <h2>Last post</h2>
    <div class="last-post">
        <div class="img-last-post">
            <img src="<?= PUBLIC_PATH ?>/img/postPicture/<?= $new->getPicture() ?>" alt="">
        </div>
        <div class="content-last-post">
            <h2><?= $new->getTitle() ?></h2>
            <p><?= $new->getExcerptLastContent() ?></p>
            <span><a href="<?= CreateUrl::url('blog', ['slug' => $new->getUrlTitle(), 'id' => $new->getId()]); ?>">Read More</a></span>
        </div>
    </div>
</section>

<div class="bottom-blog">
    <section class="all-post">
        <h2 id="title-for-all-post">All posts</h2>
        <form class="search-box" method="post" action="<?= CreateUrl::url('search') ?>">
            <?= $searchForm->searchBox('search', 'post'); ?>
        </form>
        <div class="deck-all-post">
            <?php foreach($posts as $post): ?>
            <div class="box-all-post">
                <div class="img-all-post">
                    <img src="<?= PUBLIC_PATH ?>/img/postPicture/<?= $post->getPicture() ?>" alt="">
                </div>
                <div class="content-all-post">
                    <h2><?= $post->getTitle() ?></h2>
                    <p><?= $post->getExcerptContent() ?></p>
                    <p><a href="<?= CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $post->getId()]); ?>" class="read-all-post">Read more</a></p>
                    <small class="text-muted">By <?= $author->getAdminById($post->getIdAdmin())->getName() ?></small>
                </div>
                <div class="footer-all-post">
                    <small class="text-muted"><?= $post->getDate()->format('d F Y') ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="pagination-all-post">
            <ul>
                <?php $pagination->postPaginationNumber('public'); ?>
            </ul>
        </div>
    </section>
</div>