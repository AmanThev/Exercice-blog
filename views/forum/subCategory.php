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

$title          = $slug;

?>

<h1 class="title-forum">Forum</h1>

<p class="path-forum"><i class="fas fa-home"></i><a href="<?= CreateUrl::url('forum') ?>"> Home</a> > <a href="<?= CreateUrl::url('forum', ['slug' => $catId->getUrlName(), 'id' => $catId->getId()]) ?>"><?= $cat ?></a> > <?= $slug ?></p>
<section class="forum topic">
    <h2><?= $slug ?></h2><a class="link-add-topic" href="<?= CreateUrl::url('forum/newTopic') ?>" ><i class="fas fa-plus-square"></i> Add Topic</a>
    <div class="separate-forum"></div>
        <?php if($countTopic->countTopics($id) === 0): ?>
            <p>Sorry, no topic ..... but if you want you can add one here ->>>> <a href="<?= CreateUrl::url('forum/newTopic') ?>">New post</a></p>
        <?php else: ?>
        <table style="width:100%">
            <tr>
                <th style="width:10%">Status</th>
                <th style="width:45%">Title</th>
                <th style="width:15%">Message(s)</th>
                <th style="width:30%">Last Message</th>
            </tr>
            <?php foreach($topics as $topic): ?>
            <tr>
                <td class="<?php echo $topic->getResolved() === 1 ? 'resolved' : 'no-resolved' ?> <?php echo $countMessages->countMessages($topic->getId()) === 0 ? "pending" : "" ?>"><i class="fas fa-check-circle"></i></td>
                <td><a href="<?= CreateUrl::url('forum/' . $catId->getUrlName() . '/' . CreateUrl::urlTitle($slug), ['slug' => $topic->getUrlTitle(), 'id' => $topic->getId()]) ?>"><?= $topic->getTitle() ?></a></td>
                <td><?= $countMessages->countMessages($topic->getId()) ?></td>
                <td><?php echo $countMessages->countMessages($topic->getId()) === 0 ? "No Message" : 'by ' . $message->getLastMessage($topic->getId())->getName() . '<br>' .  $message->getLastMessage($topic->getId())->getDateTimeMessage()->format('d-m-Y, h:i'); ?></td>
            </tr>
            <?php endforeach; ?>
        </table> 
        <?php endif; ?>
</section>

