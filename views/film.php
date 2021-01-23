<?php

use App\URL\ExplodeUrl;
use App\Manager\Database;
use App\Manager\FilmDatabase;
use App\Manager\CommentDatabase;
use App\URL\CreateUrl;

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

$memberExist    = new CommentDatabase;
$adminExist     = new CommentDatabase;

// dump($film->getUrlTitleCheck(), $slug, $film->getTitle());
// die;
if(strtolower($film->getUrlTitleCheck()) !== strtolower($slug)){
    $url = CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $id]);
    http_response_code(301);
    header('Location: ' . $url);
}

$title = $slug;
?>

<section class="film header-film">
    <img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $film->getPoster() ?>" alt="$film->title">
    <h1><?= $film->getTitle() ?></h1>
    <p>Directed by <?= $film->getDirector() ?></p>
</section>

<section class="content-film">
    <article class="info-film">
    <h2>Film Info</h2>
    <table>
        <tbody>
            <tr>
                <th scope="row">Director</th>
                <td><?= $film->getDirector() ?></td>
            </tr>
            <tr>
                <th scope="row">Producer</th>
                <td><?= $film->getProducer() ?></td>
            </tr>
            <tr>
                <th scope="row">Starring</th>
                <td>
                    <ul>
                        <?php foreach($film->getActor() as $actor): ?>
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
                <td><!--<span class="averageScore">☆☆☆☆☆</span>--><?= $totalRating ?><span id="totalVote"><?= ' ('.$totalVote.' votes)' ?></span></td>
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
</section>

<section class="film">
    <h2>Review User</h2>
    <?php if($comments != false): ?>
        <p class="total-comments"><?php echo $totalComment > 1 ? ' '.$totalComment.' Comments' : ' '.$totalComment.' Comment' ?></p>
        <?php foreach ($comments as $comment): ?>
            <div class="comment-user">
                <span class="photo-profile <?php if($memberExist->isMember($comment->getPseudo()) === 1){ 
                                                    echo 'member'; 
                                            }elseif($adminExist->isAdmin($comment->getPseudo()) === 1){ 
                                                echo 'admin';
                                            } ?>"><img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg" alt=""></span><h3><?= $comment->getPseudo() ?>,
                    <span class="date-comment">
                        <?php if($comment->getEdit() != NULL): ?>
                            <?= $comment->getDate()->format('d F Y') ?>: (Edited)
                        <?php else: ?>
                            <?= $comment->getDate()->format('d F Y') ?>:
                        <?php endif; ?>
                    </span>
                    <span class="rating-user">
                        <?php if($comment->getRatingFilm() == 0): ?>
                            <i class="fas fa-trash"></i>
                        <?php elseif($comment->getRatingFilm() == 1): ?>
                            <i class="fas fa-star"></i>
                        <?php elseif ($comment->getRatingFilm() == 2): ?>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <?php elseif ($comment->getRatingFilm()== 3): ?>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <?php elseif ($comment->getRatingFilm() == 4): ?>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <?php elseif ($comment->getRatingFilm() == 5): ?>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <?php endif; ?>
                    </span>
                </h3>
                <p class="comment-content"><?= $comment->getCommentFilm() ?></p>
                    <span>
                        <?php //if (is_connect() && $_SESSION['connect'] == $comment->pseudo): ?>
                            <!-- <a id="editComment" href="http://localhost/blogCinema/view/reviewFilmEdit.php?id=<?php // $film->id ?>">Edit your comment</a> -->
                        <?php //endif; ?>
                    </span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="comment-empty">No comment has been published...</p>
    <?php endif; ?>
</section>

<section class="film write-comment">
    <h2>Write your Comment</h2>
    <form action="reviewFilm.php?id=<?php //$film->id ?>" method="post" id="test1">
        <label for="pseudo">Your pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" value="<?php
            //if(isset($pseudo))
            //{ echo $pseudo;
            //}elseif (is_connect())
            //{ echo ($_SESSION['connect']);
            //} ?>" aria-describedby="pseudoInfo" placeholder="Write your pseudo">
        <small class="form-info" class="form-text text-muted">Your pseudo must not exceed 20 characters</small>
        <label for="comment">Your comment :</label>
        <small class="form-info">If you want to write spoilers, please surround them with tags [Spoiler] and [/Spoiler]</small><br>
        <textarea type="text" class="form-control"  name="comment" id="comment" rows="10"></textarea>
        <h3>Your rating :</h3>
        <div>
            <input type="radio" name="ratingFilm" id="rating-film-0" value="0" checked>
                <label for="rating-film-0" class="fas fa-trash"></label>
        </div>
        <div class="rating-film">
            <input type="radio" name="ratingFilm" id="rating-film-5" value="5">
                <label for="rating-film-5" class="fas fa-star"></label>
            <input type="radio" name="ratingFilm" id="rating-film-4" value="4">
                <label for="rating-film-4" class="fas fa-star"></label>
            <input type="radio" name="ratingFilm" id="rating-film-3" value="3">
                <label for="rating-film-3" class="fas fa-star"></label>
            <input type="radio" name="ratingFilm" id="rating-film-2" value="2">
                <label for="rating-film-2" class="fas fa-star"></label>
            <input type="radio" name="ratingFilm" id="rating-film-1" value="1">
                <label for="rating-film-1" class="fas fa-star"></label>
        </div>
        <div id="display-rating-film"> - </div>
        <?php // if(!empty($errors)):?>
            <?php //foreach ($errors as $error): ?>
                <!-- <p class="p-3 mb-2 bg-danger text-white"><?php //$error ?></p> -->
            <?php //endforeach; ?>
        <?php //endif; ?>
        <button type="submit" name="submit">Submit</button>
    </form>
</section>

<script src="<?= PUBLIC_PATH ?>/js/spoiler.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script>
        var inputStars = document.getElementsByName('ratingFilm');
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
