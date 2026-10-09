<?php

use App\URL\ExplodeUrl;
use App\Manager\ForumDatabase;
use App\URL\CreateUrl;

$url            = new ExplodeUrl($_GET['url']);
$id             = $url->getIdForSubCat();
$slug           = $url->getSlugSubCat();
$cat            = $url->getCatForPathTopic();

$catId          = new ForumDatabase();
$catId          = $catId->getCategoryName($cat);

$countMessages  = new ForumDatabase();

$countTopic     = new ForumDatabase();

$message        = new ForumDatabase();

$subCat         = new ForumDatabase();
$subCat         = $subCat->getSubCategory($id, $slug);

$topics         = new ForumDatabase();
$topics         = $topics->getTopics($id);

$title          = $subCat->getName();
?>

<section>

<nav class="forum-breadcrumb">
    <a href="<?= CreateUrl::url('forum') ?>"><i class="fas fa-home"></i> Home</a>
    <span class="sep">/</span>
    <a href="<?= CreateUrl::url('forum', ['slug' => $catId->getUrlName(), 'id' => $catId->getId()]) ?>"><?= $cat ?></a>
    <span class="sep">/</span>
    <?= $subCat->getName() ?>
</nav>

<div class="forum-topic-header">
    <div class="section-heading"><h1><?= $subCat->getName() ?></h1><span class="rule"></span></div>
    <a class="forum-add-topic" href="<?= CreateUrl::url('forum/newTopic') ?>"><i class="fas fa-plus-square"></i> Add Topic</a>
</div>

<?php if($countTopic->countTopics($id) === 0): ?>
    <p class="forum-empty">No topic yet — why not <a href="<?= CreateUrl::url('forum/newTopic') ?>">start one</a>?</p>
<?php else: ?>
    <table class="forum-table forum-topics-table">
        <thead>
            <tr>
                <th class="col-status">Status</th>
                <th>Title</th>
                <th>Messages</th>
                <th>Last message</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($topics as $topic): ?>
                <tr>
                    <td data-label="Status" class="forum-status <?php echo $topic->getResolved() === 1 ? 'resolved' : 'no-resolved'; ?> <?php echo $countMessages->countMessages($topic->getId()) === 0 ? 'pending' : ''; ?>">
                        <?php if($topic->getResolved() === 1): ?>
                            <i class="fas fa-lock" title="Closed"></i>
                        <?php elseif($countMessages->countMessages($topic->getId()) === 0): ?>
                            <i class="far fa-circle" title="No replies yet"></i>
                        <?php else: ?>
                            <i class="fas fa-comment-dots" title="Active discussion"></i>
                        <?php endif; ?>
                    </td>
                    <td data-label="Title">
                        <a href="<?= CreateUrl::url('forum/' . $catId->getUrlName() . '/' . CreateUrl::urlTitle($slug), ['slug' => $topic->getUrlTitle(), 'id' => $topic->getId()]) ?>" class="forum-subcat-link"><?= $topic->getTitle() ?></a>
                    </td>
                    <td data-label="Messages"><?= $countMessages->countMessages($topic->getId()) ?></td>
                    <td data-label="Last message">
                        <?php if($countMessages->countMessages($topic->getId()) === 0): ?>
                            <span class="forum-no-message">No message</span>
                        <?php else: ?>
                            <span class="forum-last-author">by <?= $message->getLastMessage($topic->getId())->getName() ?></span>
                            <span class="forum-last-date"><?= $message->getLastMessage($topic->getId())->getDateTimeMessage()->format('d-m-Y, h:i') ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</section>