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
$topicNonResolved   = $topicNonResolved->countOpenTopics()
?>

<section class="dash-home">
<article>
    <h2>Post</h2>
    <h3>Last Post:</h3>
    <p class="title-table"><img src="<?= PUBLIC_PATH ?>/img/postPicture/<?= $lastPost->getPicture() ?>" alt="<?= $lastPost->getTitle() ?>"><?= $lastPost->getTitle() ?></p>
    <p><span><i class="fas fa-comment"></i><?php echo $postCom > 1 ? ' '.$postCom.' Comments' : ' '.$postCom.' Comment' ?></span><span><i class="fas fa-thumbs-up"></i><?php echo $lastPost->getLike() > 1 ? ' '.$lastPost->getLike().' Likes ' : ' '.$lastPost->getLike().' Like '; ?></span><span><i class="fas fa-thumbs-down"></i><?php echo $lastPost->getDislike() > 1 ? ' '.$lastPost->getDislike().' Dislikes ' : ' '.$lastPost->getDislike().' Dislike '; ?></span></p>
    <div class="separate"></div>
    <h3>Last Comment:</h3>
    <p class="post-title">Post's Title: <span><?= $titleLastCom->getTitle() ?></span></p>
    <p>By <?= $lastComPost->getPseudo() ?><span class="last-comment"><?= $lastComPost->getDate()->format('d F Y')?></span></p>
    <p class="last-comment"><?= $lastComPost->getComment() ?></p>
    <div class="separate"></div>
    <h3>Best Post:</h3>
        <p>Post with the most comments:</p>
        <?php foreach($mostComPost as $k): ?>
            <p class="best"><?= $bestComPost->getPostById($k['index_id'])->getTitle(); ?>: <span><i class="fas fa-comment"></i><?php echo $bestComNumberPost->countCommentByIndexId($k['index_id'], 'comments_post')->getBest() > 1 ? ' '.$bestComNumberPost->countCommentByIndexId($k['index_id'], 'comments_post')->getBest().' Comments ' : ' '.$bestComNumberPost->countCommentByIndexId($k['index_id'], 'comments_post')->getBest().' Comment '; ?></span></p>
        <?php endforeach; ?>
        <p>Post with the most likes:</p>
        <?php foreach($bestPosts as $bestPost): ?>
            <p class="best"><?= $bestPost->getTitle() ?>: <span><i class="fas fa-thumbs-up"></i><?php echo $bestPost->getLike() > 1 ? ' '.$bestPost->getLike().' Likes ' : ' '.$bestPost->getLike().' Like '; ?></span></p>
        <?php endforeach; ?>
</article>
<article>
    <h2>Statistic</h2>
    <h3><i class="fas fa-edit"></i> Post:</h3>
    <p><span><i class="fas fa-pen"></i> <?= $totalPost ?> Posts</span><span><i class="fas fa-comment"></i> <?= $totalComPost ?> Comments</span></p>
    <div class="separate"></div>
    <h3><i class="fas fa-film"></i> Film:</h3>
    <p><span><i class="fas fa-file-video"></i> <?= $totalFilm ?> Reviews</span><span><i class="fas fa-comment"></i> <?= $totalComFilm ?> Comments</span></p>
    <div class="separate"></div>
    <h3><i class="fab fa-forumbee"></i> Forum:</h3>
    <p><span><i class="fas fa-folder"></i> <?= $totalCat ?> Categories</span><span><i class="fas fa-file"></i> <?= $totalSubCat ?> Sub-Categories</span></p>
    <p><span><i class="fas fa-paper-plane"></i> <?= $totalTopics ?> Topics</span><span><i class="fas fa-paper-plane"></i> <?= $topicNonResolved ?> Topics non resolved</span></p>
    <p><span><i class="fas fa-comments"></i> <?= $totalMessages ?> messages</span></p>
    <div class="separate"></div>
    <h3><i class="fas fa-users"></i> Users:</h3>
    <!-- <p>Nombre de visite</p> -->
    <p><i class="fas fa-user"></i> <?= $totalMembers ?> Members</p>
    <p><i class="fas fa-user-cog"></i> <?= $totalAdmins ?> Administrator</p>
</article>

<article>
    <h2>Reviews</h2>
    <h3>Last Review:</h3>
    <p class="title-table"><img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $lastFilm->getPoster() ?>" alt="<?= $lastFilm->getTitle() ?>"><?= $lastFilm->getTitle() ?></p>
    <p><span><i class="fas fa-comment"></i><?php echo $filmCom > 1 ? ' '.$filmCom.' Comments' : ' '.$filmCom.' Comment' ?></span><span><i class="fas fa-poll-h"></i><?php echo $totalVote > 1 ? ' '.$totalVote.' Votes ' : ' '.$totalVote.' Vote '; ?></span><span>Score <?= $totalRating.' ' ?><i class="fas fa-star"></i></span></p>
    <div class="separate"></div>
    <h3>Last Comment:</h3>
    <p class="post-title">Film's Title: <span><?= $titleLastFilm->getTitle() ?></span></p>
    <p>By <?= $lastComFilm->getPseudo() ?><span><?= $lastComFilm->getDate()->format('d F Y')?></span></p>
    <p class="last-comment"><?= $lastComFilm->getComment() ?></p>
    <div class="separate"></div>
    <h3>Best Review:</h3>
        <p>Reviews with the most comments:</p>
        <?php foreach($mostComFilm as $l): ?>
            <p class="best"><?= $bestComFilm->getFilmById($l['index_id'])->getTitle(); ?>: <span><i class="fas fa-comment"></i><?php echo $bestComNumberFilm->countCommentByIndexId($l['index_id'], 'comments_film')->getBest() > 1 ? ' '.$bestComNumberFilm->countCommentByIndexId($l['index_id'], 'comments_film')->getBest().' Comments ' : ' '.$bestComNumberFilm->countCommentByIndexId($l['index_id'], 'comments_film')->getBest().' Comment '; ?></span></p>
        <?php endforeach; ?>
          
        <p>Reviews with the best average:</p>
        <?php foreach($bestRating as $m): ?>
            <p class="best"><?= $bestRatFilm->getFilmById($m['index_id'])->getTitle(); ?>: <span><i class="fas fa-star"></i> <?= number_format($m['rating'], 2, ',', '.') ?></span></p>
        <?php endforeach; ?>
</article>

<article>
<h2>Most Active</h2>
<p>Photo du membre</p>
<p>Nombre de comment</p>
</article>
</section>