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

$title          = $slug;

?>

<h1 class="title-forum"><?= $slug ?></h1>

<p class="path-forum"><i class="fas fa-home"></i><a href="<?= CreateUrl::url('forum') ?>"> Home</a> > <?= $slug ?></p>‡
<section class="forum category">
    <h2>Introduction </h2>
    <div class="separate-forum"></div>

    <p><?= $category->getIntro() ?></p>
    <table style="width:100%">
        <tr>
            <th>Title</th>
            <th>Topic</th>
            <th>Message(s)</th>
            <th>Last Message</th>
        </tr>
        <?php foreach($subCats as $subCat): ?>
            <tr>
            <td><a href="<?= CreateUrl::url('forum/' . $category->getUrlName(), ['slug' => $subCat->getUrlName(), 'id' => $subCat->getId()]) ?>"><?= $subCat->getName() ?></a></td>
            <td><?= $countTopics->countTopics($subCat->getId()) ?></td>
            <td><?= $idSubCat = $countMessages->countMessagesWithSubCat($subCat->getId()); ?></td>
            <td><?php echo $countMessages->countMessagesWithSubCat($subCat->getId()) === 0 ? "No Message" : 'by ' . $message->getLastMessageIndex($subCat->getId())->getName() . '<br>' .  $message->getLastMessageIndex($subCat->getId())->getDateTimeMessage()->format('d-m-Y, h:i'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table> 
</section>

