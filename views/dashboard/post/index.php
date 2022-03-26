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

<form class="choose-button" method="get">
    <label for="users">Choose:</label>
    <span class="custom-dropdown custom-dropdown-red custom-dropdown-small">
        <select class="custom-dropdown_select custom-dropdown_select-red" id="select-status" name="select-status">
            <option value="members" <?php if (isset($users) && $users === "members") echo "selected"?>>Public</option>
            <option value="admins" <?php if (isset($users) && $users === "admins") echo "selected"?>>Private</option>
            <option value="All" <?php if (isset($users) && $users === "admins") echo "selected"?>>All Posts</option>
        </select>
    </span>
    <button class="submit" type="submit">Submit</button>
</form>

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