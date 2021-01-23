<?php

use App\Manager\PostDatabase;
use App\SQL\Paginate;
use App\URL\CreateUrl;

$title = 'Dashboard/Posts';

$posts = new PostDatabase();
$posts = $posts->getAllPosts();

?>
<h3 class="title-page">Posts</h3>

<button class="create-button">Create New Post</button>

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
                <a href=""><i class="fas fa-pen"></i> Edit</a>
                <a href=""><i class="fas fa-times"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>