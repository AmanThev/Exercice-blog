<?php

use App\URL\ExplodeUrl;
use App\Manager\Connection;
use App\Manager\PostDatabase;
use App\Manager\CommentDatabase;
use App\Manager\VoteDatabase;
use App\Manager\UserDatabase;
use App\URL\CreateUrl;
use App\Form\AddComment;
use App\HTML\Form;
use App\Security\Csrf;

$url            = new ExplodeUrl($_GET['url']);
$id             = $url->getId();
$slug           = $url->getSlug();

$userDatabase   = new UserDatabase;

$commentDatabase    = new CommentDatabase();
$comments           = $commentDatabase->getCommentById('comments_post', $id);
$totalComment       = $commentDatabase->totalComment('comments_post', $id);

$post           = new PostDatabase();
$post           = $post->getPostById($id);

$voteUser = false;
if(!empty($_SESSION['id'])){
    $voteUser = (new VoteDatabase())->voteUser('posts', $id, $_SESSION['id']);
}

$commentForm = new Form($_POST);

if(strtolower($post->getUrlTitleCheck()) !== strtolower($slug)){
    $url = CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $id]);
    http_response_code(301);
    header('Location: ' . $url);
    exit;
}

if(!empty($_POST)){
    $data = new AddComment($_POST);
    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors = ['csrf' => ['Your session expired, please try again.']];
    }else{
        //if($member or admin is connnect){
            //if($data->validateComment('admins' or 'members')->resultValidator())
                //$data->createComment();
        //}
        if($data->validateComment()->resultValidator()){
            $data->createCommentPost($id);
            $_SESSION["success"] = "Your comment has been added";
            header('Location: ' . CreateUrl::url('blog', ['slug' => $slug, 'id' => $id]));
            exit();
        }else{
            $errors = $data->returnErrors();
        }
    }
}

$title = $slug;
?>

<section class="post-single">
    <p class="post-single-kicker">Now reading</p>
    <h1 class="post-single-title"><?= $post->getTitle() ?></h1>
    <p class="post-single-meta">By <?= $post->getAuthor() ?> ·
        <span class="date-post">
            <?php if($post->getEdit() != NULL): ?>
                <?= $post->getDate()->format('d F Y') ?> (Edited)
            <?php else: ?>
                <?= $post->getDate()->format('d F Y') ?>
            <?php endif; ?>
        </span>
    </p>

    <div class="post-single-content">
        <p><?= $post->getContent() ?></p>
    </div>

    <div class="vote <?php if($voteUser !== false){
                                if($voteUser->getVote() == 1){
                                    echo "is-liked";
                                }elseif($voteUser->getVote() == -1){
                                    echo "is-disliked";
                                }
                            } ?>">
        <div class="vote-bar">
            <div class="vote-progress" style="width:<?= ($post->getLike() + $post->getDislike()) == 0 ? 100 : round(100 * ($post->getLike() / ($post->getLike() + $post->getDislike()))); ?>%;"></div>
        </div>
        <div class="vote-btns">
            <?php if(!empty($_SESSION['id'])): ?>
                <form class="vote-form" action="<?= CreateUrl::url('actions/vote', ['ref' => 'posts', 'refId' => $id, 'vote' => 1]) ?>" method="POST">
                    <?= Csrf::field() ?>
                    <button type="submit" class="vote-btn vote-like"><i class="fas fa-thumbs-up"></i> <?= $post->getLike() ?></button>
                </form>
                <form class="vote-form" action="<?= CreateUrl::url('actions/vote', ['ref' => 'posts', 'refId' => $id, 'vote' => -1]) ?>" method="POST">
                    <?= Csrf::field() ?>
                    <button type="submit" class="vote-btn vote-dislike"><i class="fas fa-thumbs-down"></i> <?= $post->getDislike() ?></button>
                </form>
            <?php else: ?>
                <span class="vote-count"><i class="fas fa-thumbs-up"></i> <?= $post->getLike() ?></span>
                <span class="vote-count"><i class="fas fa-thumbs-down"></i> <?= $post->getDislike() ?></span>
                <p class="vote-login-prompt"><a href="<?= CreateUrl::url('authentication/login', ['redirect' => $_SERVER['REQUEST_URI']]) ?>">Log in</a> to like or dislike this post.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="post-comments">
    <div class="section-heading"><h2>Comment Users</h2><span class="rule"></span></div>

    <?php if($comments != false): ?>
        <p class="total-comments"><?php echo $totalComment > 1 ? $totalComment.' Comments' : $totalComment.' Comment' ?></p>
        <?php foreach ($comments as $comment): ?>
            <div class="comment-user">
                <span class="photo-profile <?php if($userDatabase->statutUser($comment->getPseudo(), 'members') === 1){
                                    echo 'member';
                                }elseif($userDatabase->statutUser($comment->getPseudo(), 'admins') === 1){
                                    echo 'admin';
                                } ?>">
                    <img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg">
                </span>
                <div class="comment-body">
                    <h3><?= $comment->getPseudo() ?>
                        <span class="date-comment">
                            <?php if($comment->getEdit() != NULL): ?>
                                    <?= $comment->getDate()->format('d F Y') ?> (Edited)
                            <?php else: ?>
                                    <?= $comment->getDate()->format('d F Y') ?>
                            <?php endif; ?>
                        </span>
                    </h3>
                    <p class="comment-content"><?= $comment->getComment() ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="comment-empty">No comment has been published...</p>
    <?php endif; ?>
</section>

<section class="post-write-comment" id="post-write-comment">
    <div class="section-heading"><h2>Write your Comment</h2><span class="rule"></span></div>

    <?php if(!empty($errors['csrf'])): ?>
        <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $errors['csrf'][0] ?></p>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post" class="comment-form">
        <?= Csrf::field() ?>
        <?= $commentForm->inputText('pseudo', 'Your name', 'size', '20'); ?>
            <?php if(!empty($errors)): ?>
                <?= $data->arrayKeyExist('pseudo', $errors) ?>
            <?php endif; ?>
        <?= $commentForm->textArea('comment', 'Your comment', '6'); ?>
            <?php if(!empty($errors)): ?>
                <?= $data->arrayKeyExist('comment', $errors) ?>
            <?php endif; ?>
            <?php if(isset($_SESSION["success"])): ?>
                <p class="success"><?= $_SESSION["success"] ?></p>
                <?php unset($_SESSION["success"]); ?>
            <?php endif; ?>
        <?= $commentForm->button('Submit'); ?>
    </form>
</section>