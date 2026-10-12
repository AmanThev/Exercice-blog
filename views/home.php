<?php 
use App\Manager\PostDatabase;
use App\Manager\FilmDatabase;
use App\URL\CreateUrl;
use App\HTML\Form;
use App\Manager\PollDatabase;
use App\Security\Csrf;

$title  = 'HomePage'; 

$films  = new FilmDatabase();
$films  = $films->getFilmsHome();

$new    = new PostDatabase();
$new    = $new->getLastPost();

$posts  = new PostDatabase();
$posts  = $posts->getPostsHome();

$searchForm = new Form($_POST);

// The poll: a member who has not voted yet sees the form, everybody else sees the results
$poll       = new PollDatabase();
$results    = $poll->getResults();
$userAnswer = !empty($_SESSION['id']) ? $poll->getAnswer((int)$_SESSION['id']) : null;
$canVote    = !empty($_SESSION['id']) && $userAnswer === null;
?> 

<section>

<div class="ticket-wrap">
    <div class="ticket">
        <div class="ticket-main">
            <p class="ticket-kicker">À l'affiche</p>
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
            <div id="poll-content">
                <?php if($canVote): ?>
                    <form id="poll-form" method="post" action="<?= CreateUrl::url('actions/poll') ?>">
                        <p class="poll-q"><?= htmlspecialchars(PollDatabase::QUESTION) ?></p>
                        <?php foreach(PollDatabase::ANSWERS as $key => $label): ?>
                            <div class="poll-radio">
                                <input type="radio" id="poll-<?= $key ?>" name="opinion" value="<?= $key ?>" required>
                                <label for="poll-<?= $key ?>"><?= htmlspecialchars($label) ?></label>
                            </div>
                        <?php endforeach; ?>
                        <?= Csrf::field() ?>
                        <div>
                            <button type="submit">Submit</button>
                        </div>
                    </form>
                <?php else: ?>
                    <?php require VIEWS . 'partials/poll-results.php'; ?>
                    <?php if(empty($_SESSION['id'])): ?>
                        <p class="poll-login"><a href="<?= CreateUrl::url('authentication/login', ['redirect' => $_SERVER['REQUEST_URI']]) ?>">Log in</a> to vote.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <p class="poll-message" id="poll-message" role="status"></p>
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

<script src="<?= PUBLIC_PATH ?>/js/poll.js"></script>