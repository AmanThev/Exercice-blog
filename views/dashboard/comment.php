<?php

use App\Manager\CommentDatabase;
use App\SQL\Paginate;
use App\URL\CreateUrl;

$title          = 'Dashboard/Comments';

$filmComments   = new CommentDatabase();
$filmComments   = $filmComments->getComments('comments_film');

$postComments   = new CommentDatabase();
$postComments   = $postComments->getComments('comments_post');

$titleFilm      = new CommentDatabase();

$titlePost      = new CommentDatabase();
?>

<h3 class="title-page">Comments</h3>

<form class="choose-button only-choose" method="get">
    <label for="table">Choose the table :</label>
    <span class="custom-dropdown">
        <select class="custom-dropdown-select" id="table" name="table">
            <option value="posts" <?php if (isset($table) && $table === "posts") echo "selected"?>>Posts</option>
            <option value="reviews" <?php if (isset($table) && $table === "reviews") echo "selected"?>>Reviews</option>
        </select>
    </span>
    <button class="submit" type="submit">Submit</button>
</form>

<?php if(isset($_GET['table']) && $_GET['table'] == 'reviews'): ?>
    
    <table style="width:75%">
        <tr>
            <th style="width:10%">Pseudo</th>
            <th style="width:50%">Comment</th>
            <th style="width:15%">Date</th>
            <th style="width:5%">Rating</th>
            <th style="width:25%">Name of the film</th>
        </tr>
        <?php foreach($filmComments as $filmComment): ?>
            <tr>
                <td><?= $filmComment->getPseudo() ?></td>
                <td><?= $filmComment->getCommentFilm() ?></td>
                <td><?= $filmComment->getDate()->format('d F Y') ?></td>
                <td><?= $filmComment->getRatingFilm() ?></td>
                <td class="title-table"><?= $titleFilm->findTitle($filmComment->getIndexId(), 'comments_film', 'films')->getTitle(); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <table style="width:86%">
        <tr>
            <th style="width:10%">Pseudo</th>
            <th style="width:55%">Comment</th>
            <th style="width:20%">Date</th>
            <th style="width:20%">Name of the post</th>
        </tr>
        <?php foreach($postComments as $postComment): ?>
            <tr>
                <td><?= $postComment->getPseudo() ?></td>
                <td><?= $postComment->getComment() ?></td>
                <td><?= $postComment->getDate()->format('d F Y') ?></td>
                <td class="title-table"><?= $titlePost->findTitle($postComment->getIndexId(), 'comments_post', 'posts')->getTitle(); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>


