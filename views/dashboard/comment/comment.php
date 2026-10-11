<?php

use App\Manager\CommentDatabase;
use App\URL\CreateUrl;
use App\Security\Csrf;

$title = 'Dashboard/Comments';

// The two kinds of comments : value of ?table= => comment table, public page, labels
$kinds = [
    'posts'   => ['table' => 'comments_post', 'page' => 'blog',    'label' => 'Posts',   'target' => 'Post'],
    'reviews' => ['table' => 'comments_film', 'page' => 'reviews', 'label' => 'Reviews', 'target' => 'Review'],
];
$requested = $_GET['table'] ?? 'posts';
$kind      = (is_string($requested) && isset($kinds[$requested])) ? $requested : 'posts';
$isReview  = $kind === 'reviews';
$page      = $kinds[$kind]['page'];

$commentDb = new CommentDatabase();
$comments  = $commentDb->getCommentsWithTitle($kinds[$kind]['table']);
$totals    = [
    'posts'   => $commentDb->totalAllComment('comments_post'),
    'reviews' => $commentDb->totalAllComment('comments_film'),
];

// Escapes what is not escaped yet (a text already encoded when it was saved is left as it is)
$e      = fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8', false);

// Message left by the deletion of a comment
$flash = $_SESSION['flash_dash'] ?? null;
unset($_SESSION['flash_dash']);
?>

<div class="dash-pagehead">
    <h2 class="dash-title">Comments</h2>
</div>

<?php if($flash): ?>
    <p class="dash-message valid"><?= htmlspecialchars($flash) ?></p>
<?php endif; ?>

<nav class="dash-filter">
    <?php foreach($kinds as $key => $info): ?>
        <a href="?table=<?= $key ?>" class="<?= $kind === $key ? 'active' : '' ?>"><?= $info['label'] ?> <span class="dash-count"><?= $totals[$key] ?></span></a>
    <?php endforeach; ?>
</nav>

<?php if(empty($comments)): ?>
    <p class="dash-empty">No comment on the <?= $isReview ? 'reviews' : 'posts' ?> yet.</p>
<?php else: ?>
    <table class="dash-table">
        <thead>
            <tr>
                <th><div><span>Pseudo</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th>Comment</th>
                <th><div><span>Date</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <?php if($isReview): ?>
                    <th><div><span>Rating</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <?php endif; ?>
                <th><div><span><?= $kinds[$kind]['target'] ?></span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($comments as $comment): ?>
                <?php $date = new DateTime($comment['comment_date'] ?? 'now'); ?>
                <tr>
                    <td data-label="Pseudo"><?= $e($comment['pseudo'] ?? '') ?></td>
                    <td data-label="Comment" class="comment-cell"><?= nl2br($e($comment['comment'] ?? '')) ?></td>
                    <td data-label="Date" data-sort="<?= $date->format('Y-m-d') ?>"><?= $date->format('d F Y') ?></td>
                    <?php if($isReview): ?>
                        <td data-label="Rating"><?= (int)($comment['rating_film'] ?? 0) ?> <i class="fas fa-star rating-star"></i></td>
                    <?php endif; ?>
                    <td data-label="<?= $kinds[$kind]['target'] ?>">
                        <?php if(!empty($comment['target_title'])): ?>
                            <a href="<?= CreateUrl::url($page, ['slug' => CreateUrl::urlTitle($comment['target_title']), 'id' => (int)$comment['index_id']]) ?>"><?= $e($comment['target_title']) ?></a>
                        <?php else: ?>
                            <span class="dash-muted">(deleted)</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Action" class="dash-actions">
                        <?php if(isset($comment['id'])): ?>
                            <form action="<?= CreateUrl::url('dashboard/comments/delete') ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this comment?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="table" value="<?= $kind ?>">
                                <input type="hidden" name="id" value="<?= (int)$comment['id'] ?>">
                                <button type="submit" class="delete"><i class="fas fa-times-circle"></i>Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script src="<?= PUBLIC_PATH ?>/js/sortTable.js"></script>