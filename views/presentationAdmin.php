<?php

use App\Manager\UserDatabase;

$title = 'Our team';

$userDb = new UserDatabase();
$teams  = [
    ['title' => 'Administrators', 'members' => $userDb->getAdminsPresentationPage('Admin'), 'photo' => 'admin.jpg'],
    ['title' => 'Moderators',     'members' => $userDb->getAdminsPresentationPage('Modo'),  'photo' => 'modo.jpg'],
];
$hasTeam = !empty($teams[0]['members']) || !empty($teams[1]['members']);
?>

<section>

<div class="section-heading"><h1>Our team</h1><span class="rule"></span></div>

<?php if(!$hasTeam): ?>
    <p class="forum-empty">The team page is empty for now.</p>
<?php endif; ?>

<?php foreach($teams as $team): ?>
    <?php if(!empty($team['members'])): ?>
        <h2 class="team-title"><?= $team['title'] ?></h2>

        <div class="team-grid">
            <?php foreach($team['members'] as $member): ?>
                <?php
                    // A long description is shortened, with a "Read more" to see all of it
                    $full    = trim($member->getDescription());
                    $short   = $member->getExcerptDescription(150);
                    $hasMore = $short !== $full;
                ?>
                <article class="team-card">
                    <?php if($hasMore): ?>
                        <input type="checkbox" class="team-toggle" id="team-<?= $member->getId() ?>">
                    <?php endif; ?>

                    <img class="team-photo" src="<?= PUBLIC_PATH ?>/img/photoProfile/<?= $team['photo'] ?>" alt="">
                    <h3><?= $member->getName() ?></h3>

                    <?php if($full !== ''): ?>
                        <p class="team-short"><?= nl2br($short) ?></p>
                        <?php if($hasMore): ?>
                            <p class="team-full"><?= nl2br($full) ?></p>
                            <label for="team-<?= $member->getId() ?>" class="team-more"><span class="more">Read more</span><span class="less">Show less</span></label>
                        <?php endif; ?>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

</section>