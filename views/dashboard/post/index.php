<?php

use App\Manager\PostDatabase;
use App\SQL\Paginate;
use App\URL\CreateUrl;

$title = 'Dashboard/Posts';

$posts = new PostDatabase();

$posts = !empty($_GET['status']) ? $posts->getPosts($_GET['status']) : $posts->getAllPosts();
$status = $_GET['status'] ?? 'all';
?>
<h3 class="title-page">Posts</h3>

<div class="button-new">
    <form class="choose-button" method="get">
        <label for="status">Choose the status :</label>
        <span class="custom-dropdown">
            <select class="custom-dropdown-select" id="status" name="status">
                <option value="all" <?php if(isset($status) && $status === "all") echo "selected"?>>All Posts</option>
                <option value="public" <?php if(isset($status) && $status === "public") echo "selected"?>>Public</option>
                <option value="private" <?php if(isset($status) && $status === "private") echo "selected"?>>Private</option>
            </select>
        </span>
        <button class="submit" type="submit">Submit</button>
    </form>
    <a class="create-button" href="<?= CreateUrl::url('dashboard/posts/newPost'); ?>">Create New Post</a>
</div>
    
<table class="tritable" style="width:86%">
    <tr>
        <th style="width:40%">
            <div>
                <span>Title</span>
                <span class="sort-table"><i class="fas fa-sort"></i></span>
            </div>
        </th>
        <th style="width:15%">
            <div>
                <span>Author</span>
                <span class="sort-table"><i class="fas fa-sort"></i></i></span>
            </div>
        </th>
        <th style="width:10%">
            <div>
                <span>Status</span>
                <span class="sort-table"><i class="fas fa-sort"></i></i></span>
            </div>
        </th>
        <th style="width:20%">
            <div>
                <span>Date</span>
                <span class="sort-table"><i class="fas fa-sort"></i></i></span>
            </div>
        </th>
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

<script src="<?= PUBLIC_PATH ?>/js/sortTable.js"></script>