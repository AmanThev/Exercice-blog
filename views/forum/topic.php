<?php

use App\URL\ExplodeUrl;
use App\Manager\ForumDatabase;
use App\URL\CreateUrl;
use App\Form\AddMessage;
use App\Security\Csrf;

$url            = new ExplodeUrl($_GET['url']);
$id             = $url->getIdForTopic();
$slug           = $url->getSlugTopic();
$cat            = $url->getCatForPathTopic();
$subCat         = $url->getSubCatForPathTopic();

$catId          = new ForumDatabase();
$catId          = $catId->getCategoryName($cat);

$countMsg       = new ForumDatabase();
$totalMessages  = $countMsg->countMessages($id);

$messages       = new ForumDatabase();
$messages       = $messages->getMessage($id);

$subCatId       = new ForumDatabase();
$subCatId       = $subCatId->getSubCategoryName($subCat);

$topics         = new ForumDatabase();
$topic          = $topics->getTopic($id, $slug);

$isClosed       = $topic->getResolved() === 1;
$isOwner        = !empty($_SESSION['id']) && $topic->getIdMember() === (int)$_SESSION['id'];

if(!empty($_POST) && !empty($_SESSION['id']) && !$isClosed){
    $data = new AddMessage($_POST);
    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors = ['csrf' => ['Your session expired, please try again.']];
    }elseif($data->validateMessage()->resultValidator()){
        $dataTopic = [  "idTopic"  => $id,
                        "idSubCat" => $subCatId->getId(),
                        "idMember" => (int)$_SESSION['id']];
        $data->createMessage($dataTopic);
        $data->redirectForm($cat, $subCat, $slug, $id, $success = true);
    }else{
        $errors = $data->returnErrors();
        if(isset($_GET['success'])){
            $data->redirectForm($cat, $subCat, $slug, $id);
        }
    }
}

$title = $topic->getTitle();
?>

<section>

<nav class="forum-breadcrumb">
    <a href="<?= CreateUrl::url('forum') ?>"><i class="fas fa-home"></i> Home</a>
    <span class="sep">/</span>
    <a href="<?= CreateUrl::url('forum', ['slug' => $catId->getUrlName(), 'id' => $catId->getId()]) ?>"><?= $catId->getName() ?></a>
    <span class="sep">/</span>
    <a href="<?= CreateUrl::url('forum/' . $catId->getUrlName(), ['slug' => $subCatId->getUrlName(), 'id' => $subCatId->getId()]) ?>"><?= $subCatId->getName() ?></a>
    <span class="sep">/</span>
    <?= $topic->getTitle() ?>
</nav>

<div class="forum-topic-header">
    <div class="section-heading"><h1><?= $topic->getTitle() ?></h1><span class="rule"></span></div>
    <?php if($isClosed): ?>
        <span class="forum-closed-badge"><i class="fas fa-lock"></i> Closed</span>
    <?php elseif($isOwner): ?>
        <form action="<?= CreateUrl::urlDashboardAction('forum/topic', $id, 'closeTopic') ?>" method="post" onsubmit="return confirm('Close this topic? Nobody will be able to reply afterwards.');">
            <?= Csrf::field() ?>
            <button type="submit" class="forum-close-btn"><i class="fas fa-lock"></i> Done? Close this topic</button>
        </form>
    <?php endif; ?>
</div>

<div class="forum-thread">
    <article class="forum-post forum-post-first">
        <div class="forum-post-author">
            <img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg" alt="" class="forum-post-avatar">
            <span class="forum-post-name"><?= $topic->getName() ?></span>
        </div>
        <div class="forum-post-body">
            <p class="forum-post-date"><?= $topic->getDateTimeCreation()->format('M d, Y h:i a') ?></p>
            <p class="forum-post-text"><?= nl2br($topic->getSubject()) ?></p>
        </div>
    </article>

    <?php foreach($messages as $message): ?>
        <article class="forum-post">
            <div class="forum-post-author">
                <img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg" alt="" class="forum-post-avatar">
                <span class="forum-post-name"><?= $message->getName() ?></span>
            </div>
            <div class="forum-post-body">
                <p class="forum-post-date"><?= $message->getDateTimeMessage()->format('M d, Y h:i a') ?></p>
                <p class="forum-post-text"><?= nl2br($message->getMessage()) ?></p>
            </div>
        </article>
    <?php endforeach; ?>
</div>

<p class="total-comments"><?= $totalMessages > 1 ? $totalMessages . ' Messages' : $totalMessages . ' Message' ?></p>

<div class="section-heading"><h2>Your reply</h2><span class="rule"></span></div>

<?php if($isClosed): ?>
    <p class="forum-empty">This topic is closed, no more replies can be posted.</p>
<?php elseif(empty($_SESSION['id'])): ?>
    <p class="forum-empty"><a href="<?= CreateUrl::url('authentication/login', ['redirect' => $_SERVER['REQUEST_URI']]) ?>">Log in</a> to join the discussion.</p>
<?php else: ?>
    <?php if(!empty($errors['csrf'])): ?>
        <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $errors['csrf'][0] ?></p>
    <?php endif; ?>
    <form class="comment-form" action="" method="post">
        <?= Csrf::field() ?>
        <label for="message">Write your message :</label>
        <textarea name="message" id="message" rows="8"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        <?php if(!empty($errors)): ?>
            <?= $data->arrayKeyExist('message', $errors) ?>
        <?php endif; ?>
        <?php if(isset($_GET['success'])): ?>
            <p class="success">Your message has been posted.</p>
        <?php endif; ?>
        <button type="submit">Submit</button>
    </form>
<?php endif; ?>

</section>