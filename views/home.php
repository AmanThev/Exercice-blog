<?php 

use App\Manager\PostDatabase;
use App\Manager\FilmDatabase;
use App\URL\CreateUrl;

$title  = 'HomePage'; 

$films  = new FilmDatabase();
$films  = $films->getFilmsHome();

$new    = new PostDatabase();
$new    = $new->getLastPost();

$posts  = new PostDatabase();
$posts  = $posts->getPostsHome();

?> 

<section>
<div class="spotlight">
  <img src="<?= PUBLIC_PATH ?>/img/postPicture/<?= $new->getPicture() ?>" class="spotlight-img" alt="...">
  <div class="spotlight-img-overlay">
    <h1 class="spotlight-title"><?= $new->getTitle() ?></h1>
    <p class="spotlight-text"><?= $new->getExcerptContent() ?></p>
    <p><a href="<?= CreateUrl::url('blog', ['slug' => $new->getUrlTitle(), 'id' => $new->getId()]); ?>" class="spotlight-read">Read more</a></p>
    <p class="spotlight-text"><?= $new->getDate()->format('d F Y') ?></p>
  </div>
</div>

<div class="latest-card">
<h1>Latest Posts</h1>
    <div class="latest-deck">
    <?php foreach($posts as $post): ?>
        <div class="latest">
            <img src="<?= PUBLIC_PATH ?>/img/postPicture/<?= $post->getPicture() ?>" class="latest-img" alt="...">
            <div class="latest-body">
                <h5 class="latest-title"><?= $post->getTitle() ?></h5>
                <p class="card-text"><?= $post->getExcerptContent() ?></p>
                <p><a href="<?= CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $post->getId()]); ?>" class="latest-read posts">Read more</a></p>
            </div>
            <div class="latest-footer">
                <small class="text-muted"><?= $post->getDate()->format('d F Y') ?></small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="latest-card">
<h1>Latest Reviews</h1>
    <div class="latest-deck">
    <?php foreach($films as $film): ?>
        <div class="latest">
            <img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $film->getPoster() ?>" class="latest-reviews-img" alt="...">
            <div class="latest-body">
                <h5 class="latest-title"><?= $film->getTitle() ?></h5>
                <p class="card-text"><?= $film->getExcerptContent() ?></p>
                <p><a href="<?= CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $film->getId()]); ?>" class="latest-read reviews">Read more</a></p>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>
</section>

<aside>
<div class="search-home">
    <form class="search-home-box" method="post" action="...">
        <input class="search-home-text" type = "search" name="search" placeholder="Search">
        <button class="search-home-btn" name="submit"><i class="fas fa-search"></i></button>
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
</aside>