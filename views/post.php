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

$url            = new ExplodeUrl($_GET['url']);
$id             = $url->getId();
$slug           = $url->getSlug();

$userDatabase   = new UserDatabase;

$commentDatabase    = new CommentDatabase();
$comments           = $commentDatabase->getCommentById('comments_post', $id);
$totalComment       = $commentDatabase->totalComment('comments_post', $id);

$post           = new PostDatabase();
$post           = $post->getPostById($id);

// $userId	    = $_SESSION['id'];
$voteUser       = new VoteDatabase();
$voteUser       = $voteUser->voteUser('posts', $id, 2);

$commentForm = new Form($_POST);

if(strtolower($post->getUrlTitleCheck()) !== strtolower($slug)){
    $url = CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $id]);
    http_response_code(301);
    header('Location: ' . $url);
}

if(!empty($_POST)){
    $data = new AddComment($_POST);
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

$title = $slug;
?>

<section class="post">
	<h2><?= $post->getTitle() ?></h2>
	<p><?= $post->getContent() ?></p>
	<p class="blog-meta">By <?= $post->getAuthor() ?>,
		<span class="date-post">
            <?php if($post->getEdit() != NULL): ?>
                <?= $post->getDate()->format('d F Y') ?> (Edited)
			<?php else: ?>
				<?= $post->getDate()->format('d F Y') ?>
			<?php endif; ?>
        </span>
	</p>
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
            <form class="vote-form" action="vote.php?ref=posts&refId=<?= $id ?>&vote=1" method="POST">
                <div class="vote-form">
                    <button type="submit" class="vote-btn vote-like"><i class="fas fa-thumbs-up"></i> <?= $post->getLike() ?></button>
                </div>
            </form>
            <form class="vote-form" action="vote.php?ref=posts&refId=<?= $id ?>&vote=-1" method="POST">
                <div class="vote-form">
                    <button type="submit" class="vote-btn vote-dislike"><i class="fas fa-thumbs-down"></i> <?= $post->getDislike() ?></button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="post comment-post">
    <h2>Comment Users</h2>
    <?php if($comments != false): ?>
        <p class="total-comments"><?php echo $totalComment > 1 ? ' '.$totalComment.' Comments' : ' '.$totalComment.' Comment' ?></p>
        <?php foreach ($comments as $comment): ?>
            <div class="comment-user">
                <span class="photo-profile <?php if($userDatabase->statutUser($comment->getPseudo(), 'members') === 1){ 
                                    echo 'member'; 
                                }elseif($userDatabase->statutUser($comment->getPseudo(), 'admins') === 1){ 
                                    echo 'admin';
                                } ?>">
                    <img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg">
                </span>
                <h3><?= $comment->getPseudo() ?> :
                    <span class="date-comment">
                        <?php if($comment->getEdit() != NULL): ?>
                                <?= $comment->getDate()->format('d F Y') ?> (Edited) 
                        <?php else: ?>
                                <?= $comment->getDate()->format('d F Y') ?>
                        <?php endif; ?>
                    </span>
                </h3>
                <p class="comment-content"><?= $comment->getComment() ?></p>
                <span>
                    <?php //if (is_connect() && $_SESSION['connect'] == $comment->pseudo): ?> 
                    <!-- <a id="editComment" href="http://localhost/blogCinema/view/postEdit.php?id=<?php //$post->id ?>">Edit your comment</a> -->
                    <?php // endif; ?>          
                </span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="comment-empty">No comment has been published...</p>
    <?php endif; ?>
</section>

<!-- "< ?php 
                // if(isset($pseudo))
                // { echo $pseudo; 
                // }elseif (is_connect())
                // { echo ($_SESSION['connect']);
                //} ?//>"
            
            À rajouter dans value pseudo
-->

<section class="post write-comment">
    <h2>Write your Comment</h2>
    <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
        <?= $commentForm->inputText('pseudo', 'Your name', 'size', '20'); ?>
            <?php if(!empty($errors)): ?>
                <?= $data->arrayKeyExist('pseudo', $errors) ?>
            <?php endif; ?>
        <?= $commentForm->textArea('comment', 'Your comment', '10'); ?>
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
