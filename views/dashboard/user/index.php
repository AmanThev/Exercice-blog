<?php

use App\Manager\UserDatabase;
use App\Manager\PostDatabase;
use App\Manager\FilmDatabase;
use App\Manager\VoteDatabase;
use App\Manager\CommentDatabase;
use App\SQL\Paginate;
use App\URL\CreateUrl;

$title          = 'Dashboard/Users';

$admins         = new UserDatabase();
$admins         = $admins->getAdmins();

$members        = new UserDatabase();
$members        = $members->getMembers();

$memberComment  = new CommentDatabase();

$memberDislike  = new VoteDatabase();

$memberLike     = new VoteDatabase();

$postWritten    = new PostDatabase();

$reviewWritten  = new FilmDatabase();
?>

<h3 class="title-page">Users</h3>

<form class="choose-button" method="get">
    <label for="users">Choose:</label>
    <span class="custom-dropdown custom-dropdown-red custom-dropdown-small">
        <select class="custom-dropdown_select custom-dropdown_select-red" id="users" name="users">
            <option value="members" <?php if (isset($users) && $users === "members") echo "selected"?>>Members</option>
            <option value="admins" <?php if (isset($users) && $users === "admins") echo "selected"?>>Admins</option>
        </select>
    </span>
    <button class="submit" type="submit">Submit</button>
</form>

<?php if(isset($_GET['users']) && $_GET['users'] == 'admins'): ?>
    <button class="create-button">Add new Admin</button>
    
    <table style="width:86%">
        <tr>
            <th style="width:40%">Name</th>
            <th style="width:15%">Email</th>
            <th style="width:15%">Post Written</th>
            <th style="width:15%">Review Written</th>
            <th style="width:15%">Action</th>
        </tr>
        <?php foreach($admins as $admin): ?>
            <tr>
                <td><?= $admin->getName() ?></td>
                <td><?= $admin->getEmail() ?></td>
                <td><?= $postWritten->postWritten($admin->getId()) ?></td>
                <td><?= $reviewWritten->reviewWritten($admin->getId())?></td>
                <td><a href="<?= CreateUrl::urlSlugOnly('dashboard/users', $admin->getName()) ?>"><i class="fas fa-user-circle"></i> View Profile</a></td> 
                <!-- Chaque admin a seulement accès à son compte -->
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <table style="width:86%">
        <tr>
            <th style="width:40%">Name</th>
            <th style="width:15%">Email</th>
            <th style="width:15%">Like</th>
            <th style="width:15%">Dislike</th>
            <th style="width:15%">Comment</th>
        </tr>
        <?php foreach($members as $member): ?>
            <tr>
                <td><?= $member->getName() ?></td>
                <td><?= $member->getEmail() ?></td>
                <td><?= $memberLike->userLike($member->getId()) ?></td>
                <td><?= $memberDislike->userDislike($member->getId()) ?></td>
                <td class="title-table"><?= $memberComment->userComment($member->getName()) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>