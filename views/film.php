<?php

use App\URL\ExplodeUrl;
use App\Manager\Connection;
use App\Manager\FilmDatabase;
use App\Manager\CommentDatabase;
use App\Manager\UserDatabase;
use App\URL\CreateUrl;
use App\Form\AddComment;
use App\HTML\Form;
use App\Security\Csrf;

$url            = new ExplodeUrl($_GET['url']);
$id             = $url->getId();
$slug           = $url->getSlug();

$film           = new FilmDatabase();
$film           = $film->getFilmById($id);

$totalVote      = new FilmDatabase();
$totalVote      = $totalVote->totalVote($id);

$totalRating    = new FilmDatabase();
$totalRating    = str_replace('.', ',' , $totalRating->totalRating($id));

$comments       = new CommentDatabase();
$comments       = $comments->getCommentById('comments_film', $id);

$totalComment   = new CommentDatabase;
$totalComment   = $totalComment->totalComment('comments_film', $id);

$userDatabase   = new UserDatabase;

$commentForm    = new Form($_POST);

if(strtolower($film->getUrlTitleCheck()) !== strtolower($slug)){
    $url = CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $id]);
    http_response_code(301);
    header('Location: ' . $url);
    exit;
}

if(!empty($_POST)){
    $data = new AddComment($_POST);
    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors = ['csrf' => ['Your session expired, please try again.']];
    }else{
        $commenterType = !empty($_SESSION['name']) ? 'members' : null;
        if($data->validateComment($commenterType)->validateRating()->resultValidator()){
            $data->createCommentFilm($id);
            $_SESSION["success"] = "Your comment has been added";
            header('Location: ' . CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $id]));
            exit();
        }else{
            $errors = $data->returnErrors();
        }
    }
}

$title = $slug;
?>

<section>

<section class="film-hero">
    <div class="film-hero-poster">
        <img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $film->getPoster() ?>" alt="<?= $film->getTitle() ?>">
    </div>
    <div class="film-hero-info">
        <p class="film-hero-kicker">Now showing</p>
        <h1 class="film-hero-title"><?= $film->getTitle() ?></h1>
        <p class="film-hero-director">Directed by <?= $film->getDirector() ?></p>
    </div>
</section>

<div class="content-film">
    <article class="info-film">
        <h2>Film Info</h2>
        <table>
            <tbody>
                <tr>
                    <th scope="row">Director</th>
                    <td><?= $film->getDirector() ?></td>
                </tr>
                <tr>
                    <th scope="row">Production</th>
                    <td><?= $film->getProduction() ?></td>
                </tr>
                <tr>
                    <th scope="row">Writer</th>
                    <td><?= $film->getWriter() ?></td>
                </tr>
                <tr>
                    <th scope="row">Starring</th>
                    <td>
                        <ul>
                            <?php foreach($film->getListCast() as $actor): ?>
                                <li><?= $actor ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Year</th>
                    <td><?= $film->getDate() ?></td>
                </tr>
                <tr>
                    <th scope="row">Genre</th>
                    <td><?= $film->getGenre() ?></td>
                </tr>
                <tr>
                    <th scope="row">Score<sup>*</sup></th>
                    <td>
                        <span class="averageScore">
                            <i class="fas fa-star" style="color:<?= $totalRating >= 1 ? 'var(--brass-bright)' : 'var(--line)'; ?>;"></i>
                            <i class="fas fa-star" style="color:<?= $totalRating >= 2 ? 'var(--brass-bright)' : 'var(--line)'; ?>;"></i>
                            <i class="fas fa-star" style="color:<?= $totalRating >= 3 ? 'var(--brass-bright)' : 'var(--line)'; ?>;"></i>
                            <i class="fas fa-star" style="color:<?= $totalRating >= 4 ? 'var(--brass-bright)' : 'var(--line)'; ?>;"></i>
                            <i class="fas fa-star" style="color:<?= $totalRating >= 5 ? 'var(--brass-bright)' : 'var(--line)'; ?>;"></i>
                        </span>

                        <?= $totalRating ?>
                        <span id="totalVote"><?= ' ('.$totalVote.' votes)' ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="reference">* Average votes of users and admins site</p>
    </article>

    <article class="review-film">
        <h2>Synopsis</h2>
        <p><?= $film->getSynopsis() ?></p>
        <h2>Review from <?= $film->getAuthor() ?></h2>
        <p><?= $film->getReview() ?></p>
    </article>
</div>

<section class="post-comments">
    <div class="section-heading"><h2>Review User</h2><span class="rule"></span></div>
    <?php if($comments): ?>
        <p class="total-comments"><?php echo $totalComment > 1 ? $totalComment.' Comments' : $totalComment.' Comment' ?></p>
        <?php foreach ($comments as $comment): ?>
            <div class="comment-user">
                <span class="photo-profile <?php if($userDatabase->statutUser($comment->getPseudo(), 'members') === 1){
                                                echo 'member';
                                            }elseif($userDatabase->statutUser($comment->getPseudo(), 'admins') === 1){
                                                echo 'admin';
                                            } ?>">
                    <img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg" alt="">
                </span>
                <div class="comment-body">
                    <h3><?= $comment->getPseudo() ?>
                        <span class="date-comment">
                            <?php if($comment->getEdit() != NULL): ?>
                                <?= $comment->getDate()->format('d F Y') ?> (Edited)
                            <?php else: ?>
                                <?= $comment->getDate()->format('d F Y') ?>
                            <?php endif; ?>
                        </span>
                        <span class="rating-user">
                            <?php for($s = 1; $s <= 5; $s++): ?>
                                <i class="fas fa-star" style="color:<?= $comment->getRatingFilm() >= $s ? 'var(--brass-bright)' : 'var(--line)'; ?>;"></i>
                            <?php endfor; ?>
                        </span>
                    </h3>
                    <p class="comment-content"><?= $comment->getCommentFilm() ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="comment-empty">No comment has been published...</p>
    <?php endif; ?>
</section>

<section class="post-write-comment">
    <div class="section-heading"><h2>Write your Comment</h2><span class="rule"></span></div>

    <?php if(!empty($errors['csrf'])): ?>
        <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $errors['csrf'][0] ?></p>
    <?php endif; ?>

    <form action="" method="post" class="comment-form">
        <?= Csrf::field() ?>
        <?php if(!empty($_SESSION['name'])): ?>
            <label for="pseudo">Your name :</label>
            <input type="text" name="pseudo" id="pseudo" value="<?= htmlspecialchars($_SESSION['name']) ?>" readonly>
        <?php else: ?>
            <?= $commentForm->inputText('pseudo', 'Your name', 'size', '20'); ?>
        <?php endif; ?>
            <?php if(!empty($errors)): ?>
                <?= $data->arrayKeyExist('pseudo', $errors) ?>
            <?php endif; ?>
        <?= $commentForm->textArea('comment', 'Your comment', 10, 'spoilers'); ?>
            <?php if(!empty($errors)): ?>
                <?= $data->arrayKeyExist('comment', $errors) ?>
            <?php endif; ?>
        <?= $commentForm->ratingRadioStar('rating-film', 5); ?>
        <div id="display-rating-film"> - </div>
        <?php if(!empty($errors)): ?>
                <?= $data->arrayKeyExist('rating-film', $errors) ?>
            <?php endif; ?>
        <?php if(isset($_SESSION["success"])): ?>
            <p class="success"><?= $_SESSION["success"] ?></p>
            <?php unset($_SESSION["success"]); ?>
        <?php endif; ?>
        <?= $commentForm->button('Submit'); ?>
    </form>
</section>

</section>

<script src="<?= PUBLIC_PATH ?>/js/spoiler.js"></script>
<script>
        var inputStars = document.getElementsByName('rating-film');
        var commentStar = document.getElementById('display-rating-film');

        var commentRateLoad = function(element){
            document.addEventListener('DOMContentLoaded', function() {
                if(element.checked){
                    var inputValue = element.value;
                    commentRate(inputValue);
                }
            });
        }

        var commentRateClick = function(element){
            document.addEventListener('input', function() {
                if(element.checked){
                    var inputValue = element.value;
                    commentRate(inputValue);
                }
            });
        }

        var commentRate = function(element){
            switch (element) {
                case '1':
                    commentStar.className =  'rate-1';
                    break;
                case '2':
                    commentStar.className =  'rate-2';
                    break;
                case '3':
                    commentStar.className =  'rate-3';
                    break;
                case '4':
                    commentStar.className =  'rate-4';
                    break;
                case '5':
                    commentStar.className =  'rate-5';
                    break;
                default:
                    commentStar.className =  'rate-0';
            }
        }

        for(var i = 0; i < inputStars.length; i++){
            commentRateLoad(inputStars[i]);
            commentRateClick(inputStars[i]);
        }
</script>