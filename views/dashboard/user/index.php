<?php

use App\Manager\UserDatabase;
use App\URL\CreateUrl;

$title = 'Dashboard/Users';

// ?users=members or ?users=admins : any other value shows the members
$requested = $_GET['users'] ?? 'members';
$kind      = (is_string($requested) && in_array($requested, ['members', 'admins'], true)) ? $requested : 'members';
$isAdmins  = $kind === 'admins';

$userDb = new UserDatabase();
$users  = $isAdmins ? $userDb->getAdminsWithStats() : $userDb->getMembersWithStats();
$totals = ['members' => $userDb->countMembers(), 'admins' => $userDb->countAdmins()];

$e = fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
?>

<div class="dash-pagehead">
    <h2 class="dash-title">Users</h2>
</div>

<nav class="dash-filter">
    <a href="?users=members" class="<?= $isAdmins ? '' : 'active' ?>">Members <span class="dash-count"><?= $totals['members'] ?></span></a>
    <a href="?users=admins" class="<?= $isAdmins ? 'active' : '' ?>">Admins <span class="dash-count"><?= $totals['admins'] ?></span></a>
</nav>

<?php if(empty($users)): ?>
    <p class="dash-empty">No <?= $isAdmins ? 'admin' : 'member' ?> yet.</p>
<?php elseif($isAdmins): ?>
    <table class="dash-table">
        <thead>
            <tr>
                <th><div><span>Name</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Position</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Email</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Posts</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Reviews</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $admin): ?>
                <tr>
                    <td data-label="Name"><?= $e($admin['name']) ?></td>
                    <td data-label="Position"><?= $e($admin['position']) ?></td>
                    <td data-label="Email"><?= $e($admin['email']) ?></td>
                    <td data-label="Posts"><?= $admin['posts'] ?></td>
                    <td data-label="Reviews"><?= $admin['reviews'] ?></td>
                    <td data-label="Action" class="dash-actions">
                        <a href="<?= CreateUrl::urlSlugOnly('dashboard/users', (string)$admin['id']) ?>"><i class="fas fa-user-circle"></i>View profile</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <table class="dash-table">
        <thead>
            <tr>
                <th><div><span>Name</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Email</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Likes</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Dislikes</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Comments</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $member): ?>
                <tr>
                    <td data-label="Name"><?= $e($member['name']) ?></td>
                    <td data-label="Email"><?= $e($member['email']) ?></td>
                    <td data-label="Likes"><?= $member['likes'] ?></td>
                    <td data-label="Dislikes"><?= $member['dislikes'] ?></td>
                    <td data-label="Comments"><?= $member['comments'] ?></td>
                    <td data-label="Action" class="dash-actions">
                        <a href="<?= CreateUrl::urlSlugOnly('user', (string)$member['id']) ?>"><i class="fas fa-user-circle"></i>View profile</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script src="<?= PUBLIC_PATH ?>/js/sortTable.js"></script>