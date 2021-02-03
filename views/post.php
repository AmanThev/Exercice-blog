<?php

use App\URL\ExplodeUrl;
use App\Manager\Database;
use App\Manager\PostDatabase;
use App\Manager\CommentDatabase;
use App\Manager\VoteDatabase;
use App\Manager\UserDatabase;
use App\URL\CreateUrl;

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

if(strtolower($post->getUrlTitleCheck()) !== strtolower($slug)){
    $url = CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $id]);
    http_response_code(301);
    header('Location: ' . $url);
}

$title = $slug;
?>

<section class="post">
	<h1><?= $post->getTitle() ?></h1>
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
    <h1>Comment Users</h1>
    <?php if($comments != false): ?>
        <p class="total-comments"><?php echo $totalComment > 1 ? ' '.$totalComment.' Comments' : ' '.$totalComment.' Comment' ?></p>
        <?php foreach ($comments as $comment): ?>
            <div class="comment-user">
                <span class="photo-profile <?php if($userDatabase->statutUser($comment->getPseudo(), 'members') === 1){ 
                                                echo 'member'; 
                                            }elseif($userDatabase->statutUser($comment->getPseudo(), 'admins') === 1){ 
                                                echo 'admin';
                                            } ?>"><img src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg"></span><h2><?= $comment->getPseudo() ?>:
                    <span class="date-comment">
                        <?php if($comment->getEdit() != NULL): ?>
                            <?= $comment->getDate()->format('d F Y') ?> (Edited) 
                        <?php else: ?>
                            <?= $comment->getDate()->format('d F Y') ?>
                        <?php endif; ?>
                    </span>
                </h2>
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
    <h1>Write your Comment</h1>
	<form action="post.php?id=<//?= $post->id ?>" method="post">
        <label for="pseudo">Your pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" value="" aria-describedby="pseudoInfo" placeholder="Write your pseudo">
        <small>Your pseudo must not exceed 20 characters</small>
        <label for="comment">Your comment :</label>
        <textarea type="text" name="comment" id="comment" rows="10"></textarea>
                <!-- // < ?php if(!empty($errors)): ?>
                //     <  ?php foreach ($errors as $error): ?>
                //         <p class="p-3 mb-2 bg-danger text-white">< ?= $error ?></p>
                //      < ?php endforeach; ?>
                // < ?php endif; ?>	 -->
        <button type="submit" name="submit">Submit</button>
    </form>
</section>
