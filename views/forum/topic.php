<?php

use App\URL\ExplodeUrl;
use App\Manager\ForumDatabase;
use App\URL\CreateUrl;

$url = new ExplodeUrl($_GET['url']);

$id     = $url->getIdForTopic();
$slug   = $url->getSlugTopic();
$cat    = $url->getCatForPathTopic();
$subCat = $url->getSubCatForPathTopic();

$catId      = new ForumDatabase();
$catId      = $catId->getCategoryName($cat);
$subCatId   = new ForumDatabase();
$subCatId   = $subCatId->getSubCategoryName($subCat);
$topic      = new ForumDatabase();
$topic      = $topic->getTopic($id, $slug);

// if(strtolower($topic->getTitle()) !== strtolower($slug)){
//     $url = CreateUrl::url('forum/' . $catId->getUrlName() . '/' . $subCatId->getUrlName(), ['slug' => $topic->getUrlTitle(), 'id' => $id]);
//     http_response_code(301);
//     header('Location: ' . $url);
// }

$messages = new ForumDatabase();
$messages = $messages->getMessage($id);

$countMessages  = new ForumDatabase();
$countTopic     = new ForumDatabase();
$countMsg       = new ForumDatabase();

$title = $slug;
?>

<h1 class="title-forum">Forum</h1>
<!-- pagination -->
<p class="path-forum"><i class="fas fa-home"></i><a href="<?= CreateUrl::url('forum') ?>"> Home</a> > <a href="<?= CreateUrl::url('forum/', ['slug' => $catId->getUrlName(), 'id' => $catId->getId()]) ?>"><?= $cat ?></a> > <a href="<?= CreateUrl::url('forum/' . $cat, ['slug' => $subCatId->getUrlName() , 'id' => $subCatId->getId() ]) ?>"><?= $subCat ?> </a> > <?= $topic->getTitle() ?></p>
<section class="forum message">
    <h2><?= $topic->getTitle() ?></h2>
    <div class="separate-forum"></div>
    <div class="row">
    <div class="pseudo"><p><?= $topic->getName() ?></p>
    <img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg" alt=""></div>
    <div class="subject"><p><?= $topic->getDateTimeCreation()->format('M d, Y h:i a') ?></p><p><?= $topic->getSubject() ?></p></div>
    </div>
    <?php foreach($messages as $message): ?>
        <div class="row">
        <div class="pseudo"><p><?= $message->getName() ?></p>
        <img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg" alt=""></div>
        <div class="content"><p><?= $message->getDateTimeMessage()->format('M d, Y h:i a') ?></p><p><?= $message->getMessage() ?></p></div>
        </div>
    <?php endforeach; ?>
    <p><?php echo $countMsg->countMessages($id) > 1 ? ' '.$countMsg->countMessages($id).' Messages' : ' '.$countMsg->countMessages($id).' Message' ?></p><!--Ex 10 msg 20 + Pagination -->
</section>

<h2 class="title-message-bottom"><?= $topic->getTitle() ?></h2>

<?php if($topic->getResolved() ==! 1): ?>
    <form class="write-message" action="" method="post">
        <label for="message">Write your message:</label>
        <textarea name="message" id="messafe" cols="110" rows="15"></textarea>
        <input type="submit" name="login" value="Submit">
    </form>    
<?php endif; ?>


