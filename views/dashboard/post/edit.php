<?php

TODO: 
 //changer de fichier, upload apparait if upLoad est rempli modifier l'image sinon ne rien faire

use App\URL\ExplodeUrl;
use App\Manager\PostDatabase;
use App\HTML\Form;

$url            = new ExplodeUrl($_GET['url']);
$id             = $url->getId();

$post = new PostDatabase();
$post = $post->getPostById($id);

$formEdit = new Form($post);

?>

<h3 class="title-page">Edit : <?= $post->getTitle() ?></h3>

<form action="" class="dashboard-form" method="post" enctype="multipart/form-data">
    <div class="input inputAuthor">
        <?= $formEdit->inputText('author', 'Your name', 'size', '20'); ?>
            <!-- < ?php if(!empty($errors)): ?>
                < ?= $data->arrayKeyExist('pseudo', $errors) ?>
            < ?php endif; ?> -->
    </div>
    <div class="input">
        <?= $formEdit->inputText('title', 'Post\'s title'); ?>
    </div>
    <div class="textarea">
        <?= $formEdit->textarea('content', 'Your Post', 20); ?>
    </div>
    <div class="block-image">
        <div class="image-preview">
            <?= $formEdit->displayPicture('picture', 'display'); ?>
        </div>
        <div>
            <p class="reveal-text">Changer d'image</p>
        </div>
    </div>
    <div class="picture">
        <?= $formEdit->pictureWithoutDisplay('picture', 'Upload', 'picture'); ?>
    </div>
   
    <div>
        <?= $formEdit->switchButton('public', 'public', 'private'); ?>
    </div> 
    <div class="button">
        <?= $formEdit->button('Edit'); ?>
    </div>
</form>

<script>
    var revealText = document.querySelector(".reveal-text");
    var picture = document.querySelector(".picture");

    revealText.addEventListener("click", function(e) {
        picture.classList += " show-label-picture";
    }, true);

</script>