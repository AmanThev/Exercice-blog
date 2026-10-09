<?php

use App\Manager\PostDatabase;
use App\Manager\CommentDatabase;
use App\Manager\FilmDatabase;
use App\Manager\ForumDatabase;
use App\Manager\UserDatabase;
use App\Manager\VoteDatabase;

/**
 * Section Post
 */
$bestComNumberPost  = new CommentDatabase();

$bestComPost        = new PostDatabase();

$bestPosts          = new PostDatabase();
$bestPosts          = $bestPosts->bestVote();

$lastComPost        = new CommentDatabase();
$lastComPost        = $lastComPost->getLastComment('comments_post');

$lastPost           = new PostDatabase();
$lastPost           = $lastPost->getLastPost();

$mostComPost        = new CommentDatabase();
$mostComPost        = $mostComPost->mostComments('comments_post');

$postCom            = new CommentDatabase();
$postCom            = $postCom->totalComment('comments_post', $lastPost->getId());

$titleLastCom       = new PostDatabase();
$titleLastCom       = $titleLastCom->getPostByCommentId($lastComPost->getIndexId());

/**
 * Section Review/Film
 */
$bestComFilm        = new FilmDatabase();

$bestComNumberFilm  = new CommentDatabase();
$bestRating         = new CommentDatabase();
$bestRating         = $bestRating->bestRating();

$bestRatFilm        = new FilmDatabase();

$lastComFilm        = new CommentDatabase();
$lastComFilm        = $lastComFilm->getLastComment('comments_film');

$lastFilm           = new FilmDatabase();
$lastFilm           = $lastFilm->getLastFilm();

$filmCom            = new CommentDatabase();
$filmCom            = $filmCom->totalComment('comments_film', $lastFilm->getId());

$mostComFilm        = new CommentDatabase();
$mostComFilm        = $mostComFilm->mostComments('comments_film');

$titleLastFilm      = new FilmDatabase();
$titleLastFilm      = $titleLastFilm->getFilmByCommentId($lastComFilm->getIndexId());

$totalRating        = new FilmDatabase();
$totalRating        = $totalRating->totalRating($lastFilm->getId());

$totalVote          = new FilmDatabase();
$totalVote          = $totalVote->totalVote($lastFilm->getId());


/**
 * Section Statistic
 */
$totalAdmins    = new UserDatabase();
$totalAdmins    = $totalAdmins->countAdmins();

$totalComFilm   = new CommentDatabase();
$totalComFilm   = $totalComFilm->totalAllComment('comments_film');

$totalComPost   = new CommentDatabase();
$totalComPost   = $totalComPost->totalAllComment('comments_post');

$totalCat       = new ForumDatabase();
$totalCat       = $totalCat->countAllCategories();

$totalFilm      = new FilmDatabase();
$totalFilm      = $totalFilm->totalFilms();

$totalMembers   = new UserDatabase();
$totalMembers   = $totalMembers->countMembers();

$totalMessages  = new ForumDatabase();
$totalMessages  = $totalMessages->countAllMessages();

$totalPost      = new PostDatabase();
$totalPost      = $totalPost->totalPosts();

$totalSubCat    = new ForumDatabase();
$totalSubCat    = $totalSubCat->countAllSubCategories();

$totalTopics    = new ForumDatabase();
$totalTopics    = $totalTopics->countAllTopics();

$topicNonResolved   = new ForumDatabase();
$topicNonResolved   = $topicNonResolved->countOpenTopics();

// "1 comment" / "2 comments"
$plural = fn(int $n, string $word) => $n . ' ' . $word . ($n === 1 ? '' : 's');
?>

<h2 class="dash-title">Overview</h2>

<div class="dash-tiles">
    <div class="dash-tile"><i class="fas fa-edit"></i><strong><?= $totalPost ?></strong><span>Posts</span></div>
    <div class="dash-tile"><i class="fas fa-film"></i><strong><?= $totalFilm ?></strong><span>Reviews</span></div>
    <div class="dash-tile"><i class="fas fa-comment"></i><strong><?= $totalComPost + $totalComFilm ?></strong><span>Comments</span></div>
    <div class="dash-tile"><i class="fas fa-users"></i><strong><?= $totalMembers ?></strong><span>Members</span></div>
    <div class="dash-tile"><i class="fas fa-paper-plane"></i><strong><?= $totalTopics ?></strong><span>Topics</span></div>
    <div class="dash-tile"><i class="fas fa-comments"></i><strong><?= $totalMessages ?></strong><span>Messages</span></div>
</div>

<div class="dash-grid">

    <article class="dash-card">
        <h3 class="dash-card-title">Posts</h3>

        <p class="dash-label">Last post</p>
        <div class="dash-last">
            <img class="dash-thumb" src="<?= PUBLIC_PATH ?>/img/postPicture/<?= $lastPost->getPicture() ?>" alt="">
            <div>
                <p class="dash-last-title"><?= $lastPost->getTitle() ?></p>
                <p class="dash-meta">
                    <span><i class="fas fa-comment"></i><?= $plural((int)$postCom, 'comment') ?></span>
                    <span><i class="fas fa-thumbs-up"></i><?= $plural((int)$lastPost->getLike(), 'like') ?></span>
                    <span><i class="fas fa-thumbs-down"></i><?= $plural((int)$lastPost->getDislike(), 'dislike') ?></span>
                </p>
            </div>
        </div>

        <p class="dash-label">Last comment</p>
        <div class="dash-quote">
            <p class="dash-quote-meta">on <strong><?= $titleLastCom->getTitle() ?></strong> &middot; by <?= $lastComPost->getPseudo() ?> &middot; <?= $lastComPost->getDate()->format('d F Y') ?></p>
            <p class="dash-quote-text"><?= $lastComPost->getComment() ?></p>
        </div>

        <p class="dash-label">Most commented</p>
        <ul class="dash-list">
            <?php foreach($mostComPost as $k): ?>
                <?php $nb = (int)$bestComNumberPost->countCommentByIndexId($k['index_id'], 'comments_post')->getBest(); ?>
                <li>
                    <span><?= $bestComPost->getPostById($k['index_id'])->getTitle() ?></span>
                    <strong><i class="fas fa-comment"></i><?= $plural($nb, 'comment') ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="dash-label">Most liked</p>
        <ul class="dash-list">
            <?php foreach($bestPosts as $bestPost): ?>
                <li>
                    <span><?= $bestPost->getTitle() ?></span>
                    <strong><i class="fas fa-thumbs-up"></i><?= $plural((int)$bestPost->getLike(), 'like') ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </article>

    <article class="dash-card">
        <h3 class="dash-card-title">Reviews</h3>

        <p class="dash-label">Last review</p>
        <div class="dash-last">
            <img class="dash-thumb dash-thumb-film" src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $lastFilm->getPoster() ?>" alt="">
            <div>
                <p class="dash-last-title"><?= $lastFilm->getTitle() ?></p>
                <p class="dash-meta">
                    <span><i class="fas fa-comment"></i><?= $plural((int)$filmCom, 'comment') ?></span>
                    <span><i class="fas fa-poll-h"></i><?= $plural((int)$totalVote, 'vote') ?></span>
                    <span><i class="fas fa-star"></i>Score <?= $totalRating ?></span>
                </p>
            </div>
        </div>

        <p class="dash-label">Last comment</p>
        <div class="dash-quote">
            <p class="dash-quote-meta">on <strong><?= $titleLastFilm->getTitle() ?></strong> &middot; by <?= $lastComFilm->getPseudo() ?> &middot; <?= $lastComFilm->getDate()->format('d F Y') ?></p>
            <p class="dash-quote-text"><?= $lastComFilm->getComment() ?></p>
        </div>

        <p class="dash-label">Most commented</p>
        <ul class="dash-list">
            <?php foreach($mostComFilm as $l): ?>
                <?php $nb = (int)$bestComNumberFilm->countCommentByIndexId($l['index_id'], 'comments_film')->getBest(); ?>
                <li>
                    <span><?= $bestComFilm->getFilmById($l['index_id'])->getTitle() ?></span>
                    <strong><i class="fas fa-comment"></i><?= $plural($nb, 'comment') ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="dash-label">Best average</p>
        <ul class="dash-list">
            <?php foreach($bestRating as $m): ?>
                <li>
                    <span><?= $bestRatFilm->getFilmById($m['index_id'])->getTitle() ?></span>
                    <strong><i class="fas fa-star"></i><?= number_format($m['rating'], 2, ',', '.') ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </article>

    <article class="dash-card">
        <h3 class="dash-card-title">Community</h3>

        <p class="dash-label">Users</p>
        <ul class="dash-list">
            <li><span><i class="fas fa-user"></i>Members</span><strong><?= $totalMembers ?></strong></li>
            <li><span><i class="fas fa-user-cog"></i>Administrators</span><strong><?= $totalAdmins ?></strong></li>
        </ul>

        <p class="dash-label">Forum</p>
        <ul class="dash-list">
            <li><span><i class="fas fa-folder"></i>Categories</span><strong><?= $totalCat ?></strong></li>
            <li><span><i class="fas fa-file"></i>Sub-categories</span><strong><?= $totalSubCat ?></strong></li>
            <li><span><i class="fas fa-paper-plane"></i>Topics</span><strong><?= $totalTopics ?></strong></li>
            <li><span><i class="fas fa-lock-open"></i>Open topics</span><strong><?= $topicNonResolved ?></strong></li>
            <li><span><i class="fas fa-comments"></i>Messages</span><strong><?= $totalMessages ?></strong></li>
        </ul>

        <p class="dash-label">Comments</p>
        <ul class="dash-list">
            <li><span><i class="fas fa-pen"></i>On posts</span><strong><?= $totalComPost ?></strong></li>
            <li><span><i class="fas fa-file-video"></i>On reviews</span><strong><?= $totalComFilm ?></strong></li>
        </ul>
    </article>

</div>