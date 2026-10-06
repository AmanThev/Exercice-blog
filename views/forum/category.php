<?php

use App\URL\ExplodeUrl;
use App\Manager\ForumDatabase;
use App\URL\CreateUrl;


$url            = new ExplodeUrl($_GET['url']);
$id             = $url->getId();
$slug           = $url->getSlug();

$category       = new ForumDatabase();
$category       = $category->getCategory($id, $slug);

$countTopics    = new ForumDatabase();
$countMessages  = new ForumDatabase();

$message        = new ForumDatabase();

$subCats        = new ForumDatabase();
$subCats        = $subCats->getSubCategories($id);

$title          = $category->getName();
?>

<section>

<nav class="forum-breadcrumb">
    <a href="<?= CreateUrl::url('forum') ?>"><i class="fas fa-home"></i> Home</a>
    <span class="sep">/</span>
    <?= $category->getName() ?>
</nav>

<div class="section-heading"><h1><?= $category->getName() ?></h1><span class="rule"></span></div>

<div class="forum-intro">
    <h2>Introduction</h2>
    <p><?= $category->getIntro() ?></p>
</div>

<table class="forum-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Topics</th>
            <th>Messages</th>
            <th>Last message</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($subCats as $subCat): ?>
            <tr>
                <td data-label="Title">
                    <a href="<?= CreateUrl::url('forum/' . $category->getUrlName(), ['slug' => $subCat->getUrlName(), 'id' => $subCat->getId()]) ?>" class="forum-subcat-link"><?= $subCat->getName() ?></a>
                </td>
                <td data-label="Topics"><?= $countTopics->countTopics($subCat->getId()) ?></td>
                <td data-label="Messages"><?= $countMessages->countMessagesWithSubCat($subCat->getId()); ?></td>
                <td data-label="Last message">
                    <?php if($countMessages->countMessagesWithSubCat($subCat->getId()) === 0): ?>
                        <span class="forum-no-message">No message</span>
                    <?php else: ?>
                        <span class="forum-last-author">by <?= $message->getLastMessageIndex($subCat->getId())->getName() ?></span>
                        <span class="forum-last-date"><?= $message->getLastMessageIndex($subCat->getId())->getDateTimeMessage()->format('d-m-Y, h:i') ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</section>