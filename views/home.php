<?php 
use App\Manager\PostDatabase;
use App\Manager\FilmDatabase;
use App\URL\CreateUrl;
use App\HTML\Form;

$title  = 'HomePage'; 

$films  = new FilmDatabase();
$films  = $films->getFilmsHome();

$new    = new PostDatabase();
$new    = $new->getLastPost();

$posts  = new PostDatabase();
$posts  = $posts->getPostsHome();

$searchForm = new Form($_POST);
?> 
<section>
    <div class="ticket-wrap">
        <div class="ticket">
            <div class="ticket-main">
                <p class="ticket-kicker">Now Showing</p>
                <h1 class="ticket-title"><?= $new->getTitle() ?></h1>
                <p class="ticket-text"><?= $new->getExcerptContent() ?></p>
                <p><a href="<?= CreateUrl::url('blog', ['slug' => $new->getUrlTitle(), 'id' => $new->getId()]); ?>" class="stub-btn">Read more</a></p>
            </div>
            <div class="ticket-side">
                <span class="admit">ADMIT ONE</span>
                <span class="date-stub"><?= $new->getDate()->format('d · m · Y') ?></span>
            </div>
        </div>
    </div>

    <div class="content-row">
        <div class="posts-col">
            <div class="section-heading"><h2>Latest Posts</h2><span class="rule"></span></div>
            <div class="post-grid">
                <?php foreach($posts as $post): ?>
                    <article class="post-card">
                        <img src="<?= PUBLIC_PATH ?>/img/postPicture/<?= $post->getPicture() ?>" class="post-img" alt="...">
                        <div class="post-card-body">
                            <h4 class="post-title"><?= $post->getTitle() ?></h4>
                            <p class="post-text"><?= $post->getExcerptContent() ?></p>
                            <p><a href="<?= CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $post->getId()]); ?>" class="card-link">Read more</a></p>
                            <small class="post-date text-muted"><?= $post->getDate()->format('d F Y') ?></small>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <aside>
            <div class="search-home box">
                <form class="search-box" method="post" action="<?= CreateUrl::url('search') ?>">
                    <?= $searchForm->searchBox('search', 'all'); ?>
                </form>
            </div>
            <div class="poll box">
                <h3>Movie Poll :</h3>
                <form>
                    <p class="poll-q">Do you like 3D ?</p>
                    <div class="poll-radio">
                        <input type="radio" id="yesOpinion" name="opinion" value="yes" checked>
                        <label for="yesOpinion">Yes</label>
                    </div>
                    <div class="poll-radio">
                        <input type="radio" id="noOpinion" name="opinion" value="no">
                        <label for="noOpinion">No</label>
                    </div>
                    <div class="poll-radio">
                        <input type="radio" id="doNotKnowOpinion" name="opinion" value="doNotKnow">
                        <label for="doNotKnowOpinion">Don't know</label>
                    </div>
                    <div>
                        <button type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </aside>
    </div>

    <div class="section-heading"><h2>Latest Reviews</h2><span class="rule"></span></div>
    <div class="review-grid">
        <?php foreach($films as $film): ?>
            <div class="review-card">
                <img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $film->getPoster() ?>" class="review-poster" alt="...">
                <div class="review-body">
                    <h4><?= $film->getTitle() ?></h4>
                    <p><?= $film->getExcerptContent() ?></p>
                    <a href="<?= CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $film->getId()]); ?>" class="card-link">Read more</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


</section>
    <!-- 
    <section>
        <div class="spotlight">
            <img src="< ?= //PUBLIC_PATH ?>/img/postPicture/< ?= //$new->getPicture() ?>" class="spotlight-img" alt="...">
            <div class="spotlight-img-overlay">
        <h1 class="spotlight-title">< ?= //$new->getTitle() ?></h1>
        <p class="spotlight-text">< ?= //$new->getExcerptContent() ?></p>
        <p><a href="< ?= //CreateUrl::url('blog', ['slug' => $new->getUrlTitle(), 'id' => $new->getId()]); ?>" class="spotlight-read">Read more</a></p>
        <p class="spotlight-text">< ?= //$new->getDate()->format('d F Y') ?></p>
    </div>
</div>

<div class="latest-card">
<h1>Latest Posts</h1>
    <div class="latest-deck">
        < ?php //foreach($posts as $post): ?>
            <div class="latest">
                <img src="< ?= //PUBLIC_PATH ?>/img/postPicture/< ?= //$post->getPicture() ?>" class="latest-img" alt="...">
                <div class="latest-body">
                    <h5 class="latest-title">< ?= //$post->getTitle() ?></h5>
                    <p class="card-text">< ?= //$post->getExcerptContent() ?></p>
                    <p><a href="< ?= //CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $post->getId()]); ?>" class="latest-read posts">Read more</a></p>
                </div>
                <div class="latest-footer">
                    <small class="text-muted">< ?= //$post->getDate()->format('d F Y') ?></small>
                </div>
            </div>
        < ?php //endforeach; ?>
    </div>
</div>

<div class="latest-card">
<h1>Latest Reviews</h1>
    <div class="latest-deck">
        < ?php //foreach($films as $film): ?>
            <div class="latest">
                <img src="< ?= //PUBLIC_PATH ?>/img/posterFilm/< ?= //$film->getPoster() ?>" class="latest-reviews-img" alt="...">
                <div class="latest-body">
                    <h5 class="latest-title">< ?= //$film->getTitle() ?></h5>
                    <p class="card-text">< ?= //$film->getExcerptContent() ?></p>
                    <p><a href="< ?= //CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $film->getId()]); ?>" class="latest-read reviews">Read more</a></p>
                </div>
            </div>
        < ?php //endforeach; ?>
    </div>
</div>
</section>

<aside>
<div class="search-home">
    <form class="search-box" method="post" action="< ?= //CreateUrl::url('search') ?>">
        < ?= //$searchForm->searchBox('search', 'all'); ?>
    </form>
</div>
<div class="poll">
    <h1>Movie Poll :</h1>
    <form>
        <p>Do you like 3D ?</p>
        <div class="poll-radio">
            <input type="radio" id="yesOpinion" name="opinion" value="yes" checked>
            <label for="yesOpinion">Yes</label>
        </div>
        <div class="poll-radio">
            <input type="radio" id="noOpinion" name="opinion" value="no">
            <label for="noOpinion">No</label>
        </div>
        <div class="poll-radio">
            <input type="radio" id="doNotKnowOpinion" name="opinion" value="doNotKnow">
            <label for="doNotKnowOpinion">Don't know</label>
        </div>
        <div>
            <button type="submit">Submit</button>
        </div>
    </form>
</div>
<div>
    <h1>Fil Twitter</h1>
</div>
</aside> -->