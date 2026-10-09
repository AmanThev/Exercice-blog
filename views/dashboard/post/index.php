<?php

use App\Manager\PostDatabase;
use App\URL\CreateUrl;
use App\Security\Csrf;

$title = 'Dashboard/Posts';

$allowed   = ['all', 'public', 'private'];
$requested = $_GET['status'] ?? 'all';
$status    = (is_string($requested) && in_array($requested, $allowed, true)) ? $requested : 'all';

$postDb = new PostDatabase();
$posts  = $status === 'all' ? $postDb->getAllPosts() : $postDb->getPosts($status);

$filters = ['all' => 'All posts', 'public' => 'Public', 'private' => 'Private'];

// Message laissé par l'édition ou la suppression d'un post
$flash = $_SESSION['flash_dash'] ?? null;
unset($_SESSION['flash_dash']);
?>

<div class="dash-pagehead">
    <h2 class="dash-title">Posts</h2>
    <a class="dash-btn" href="<?= CreateUrl::url('dashboard/posts/newPost'); ?>"><i class="fas fa-plus"></i> New post</a>
</div>

<?php if($flash): ?>
    <p class="dash-message valid"><?= htmlspecialchars($flash) ?></p>
<?php endif; ?>

<nav class="dash-filter">
    <?php foreach($filters as $key => $label): ?>
        <a href="?status=<?= $key ?>" class="<?= $status === $key ? 'active' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</nav>

<?php if(empty($posts)): ?>
    <p class="dash-empty">No post to show for this filter.</p>
<?php else: ?>
    <table class="dash-table">
        <thead>
            <tr>
                <th><div><span>Title</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Author</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Status</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Date</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($posts as $post): ?>
                <tr class="<?= $post->getPublic() === 0 ? 'private' : '' ?>">
                    <td data-label="Title"><?= $post->getTitle() ?></td>
                    <td data-label="Author"><?= $post->getAuthor() ?></td>
                    <td data-label="Status">
                        <span class="status-badge <?= $post->getPublic() === 1 ? 'public' : 'private' ?>"><?= $post->getPublic() === 1 ? 'public' : 'private' ?></span>
                    </td>
                    <td data-label="Date" data-sort="<?= $post->getDate()->format('Y-m-d') ?>"><?= $post->getDate()->format('d F Y') ?></td>
                    <td data-label="Action" class="dash-actions">
                        <a href="<?= CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $post->getId()]); ?>"><i class="fas fa-eye"></i>Preview</a>
                        <a href="<?= CreateUrl::urlDashboardAction('dashboard/posts', $post->getId()); ?>"><i class="fas fa-pen"></i>Edit</a>
                        <form action="<?= CreateUrl::urlDashboardAction('dashboard/posts', $post->getId(), 'delete'); ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this post? Its comments and votes will be deleted too.')">
                            <?= Csrf::field() ?>
                            <button type="submit" class="delete"><i class="fas fa-times-circle"></i>Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script src="<?= PUBLIC_PATH ?>/js/sortTable.js"></script>