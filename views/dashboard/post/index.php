<?php

use App\Manager\PostDatabase;
use App\SQL\Paginate;
use App\URL\CreateUrl;

$title = 'Dashboard/Posts';

$posts = new PostDatabase();
$posts = $posts->getAllPosts();

?>
<h3 class="title-page">Posts</h3>

<a class="create-button" href="<?= CreateUrl::url('dashboard/posts/newPost'); ?>">Create New Post</a>

<table style="width:86%">
    <tr>
        <th style="width:40%">Title</th>
        <th style="width:15%">Author</th>
        <th style="width:10%">Status</th>
        <th style="width:20%">Date</th>
        <th style="width:15%">Action</th>
    </tr>
    <?php foreach($posts as $post): ?>
        <tr class="<?php echo $post->getPublic() === 0 ? "private" : "" ?>">
            <td><?= $post->getTitle() ?></td>
            <td><?= $post->getAuthor() ?></td>
            <td><?php echo $post->getPublic() === 1 ? "public" : "private" ?></td>
            <td><?= $post->getDate()->format('d F Y') ?></td>
            <td>
                <a href="<?= CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $post->getId()]); ?>"><i class="fas fa-eye"></i> Preview</a>
                <a href="<?= CreateUrl::urlDashboardAction('dashboard/posts', $post->getId()); ?>"><i class="fas fa-pen"></i> Edit</a>
                <form action="<?= CreateUrl::urlDashboardAction('dashboard/posts', $post->getId(), 'delete'); ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this post?')">
                    <button><i class="fas fa-times-circle"></i> Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>