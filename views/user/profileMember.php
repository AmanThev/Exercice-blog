<?php

use App\Manager\UserDatabase;
use App\Manager\VoteDatabase;
use App\Manager\Exception\NotFoundException;
use App\URL\CreateUrl;

// /user/12  ->  $_GET['url'] = "user/12" : on identifie le membre par son id
$parts  = explode('/', trim($_GET['url'], '/'));
$id     = $parts[1] ?? '';
$member = null;

if(ctype_digit($id)){
    try{
        $member = (new UserDatabase())->getMemberById((int)$id);
    }catch(NotFoundException $e){
        $member = null;
    }
}

if($member === null){
    http_response_code(404);
    $title = 'Member not found';
}else{
    $title          = $member->getName();
    $isOwner        = !empty($_SESSION['id']) && (int)$_SESSION['id'] === $member->getId();
    $votes          = new VoteDatabase();
    $likes          = $votes->userLike($member->getId());
    $dislikes       = $votes->userDislike($member->getId());
    $hasDescription = trim($member->getRawDescription()) !== '';
}
?>

<section>

<?php if($member === null): ?>
    <div class="section-heading"><h1>Member not found</h1><span class="rule"></span></div>
    <p class="forum-empty">This profile doesn't exist. <a href="<?= CreateUrl::url('home') ?>">Back to home</a></p>

<?php else: ?>
    <div class="profile-card">
        <img class="profile-avatar" src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg" alt="">
        <div class="profile-info">
            <p class="profile-kicker">Member</p>
            <h1 class="profile-name"><?= $member->getName() ?></h1>

            <div class="profile-stats">
                <span><i class="fas fa-thumbs-up"></i> <strong><?= $likes ?></strong> likes given</span>
                <span><i class="fas fa-thumbs-down"></i> <strong><?= $dislikes ?></strong> dislikes given</span>
            </div>

            <?php if($hasDescription): ?>
                <p class="profile-description"><?= $member->getDescription() ?></p>
            <?php else: ?>
                <p class="profile-description empty">No description yet.</p>
            <?php endif; ?>

            <?php if($isOwner): ?>
                <a class="forum-add-topic" href="<?= CreateUrl::url('account') ?>"><i class="fas fa-pen"></i> Edit my profile</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

</section>