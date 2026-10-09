<?php

use App\Manager\ForumDatabase;
use App\Form\AddTopic;
use App\URL\CreateUrl;
use App\Security\Csrf;

$categories = new ForumDatabase();
$categories = $categories->getCategories();

$subCats    = new ForumDatabase();
$incompleteForm = True;
$errors = [];

$title      = "New Topic";

if(!empty($_POST) && !empty($_SESSION['id'])){
    // Le champ "name" est en lecture seule dans le formulaire, mais ça ne
    // protège pas côté serveur : on impose donc le nom de la session.
    $_POST['name'] = $_SESSION['name'];

    $data = new AddTopic($_POST);
    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors = ['csrf' => ['Your session expired, please try again.']];
    }elseif($data->validateTopic()->resultValidator()){
        $incompleteForm = False;
        $urlSubCat = $data->getUrlSubCategory($_POST['subCat']);
        $data->createTopic($_POST);
        $urlTopic = $data->getUrlTopic();
        $success = "Your topic has been added";
    }else{
        $errors = $data->returnErrors();
    }
}
?>

<section>

<nav class="forum-breadcrumb">
    <a href="<?= CreateUrl::url('forum') ?>"><i class="fas fa-home"></i> Home</a>
    <span class="sep">/</span>
    New topic
</nav>

<div class="section-heading"><h1>Write your Topic</h1><span class="rule"></span></div>

<?php if(empty($_SESSION['id'])): ?>
    <p class="forum-empty"><a href="<?= CreateUrl::url('authentication/login', ['redirect' => $_SERVER['REQUEST_URI']]) ?>">Log in</a> to start a new topic.</p>

<?php elseif($incompleteForm): ?>
    <?php if(!empty($errors['csrf'])): ?>
        <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $errors['csrf'][0] ?></p>
    <?php endif; ?>

    <form action="" method="post" class="comment-form">
        <?= Csrf::field() ?>

        <label for="title">Title :</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
        <?php if(!empty($errors)): ?>
            <?= $data->arrayKeyExist('title', $errors) ?>
        <?php endif; ?>

        <label for="name">Your name :</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($_SESSION['name']) ?>" readonly>
        <?php if(!empty($errors)): ?>
            <?= $data->arrayKeyExist('name', $errors) ?>
        <?php endif; ?>

        <label for="subCat">Sub-category :</label>
        <select name="subCat" id="subCat">
            <?php foreach($categories as $category): ?>
                <optgroup label="<?= $category->getName() ?>">
                    <?php foreach($subCats->getSubCategories($category->getId()) as $subCat): ?>
                        <option value="<?= $subCat->getName() ?>" <?= ($_POST['subCat'] ?? '') === $subCat->getName() ? 'selected' : '' ?>><?= $subCat->getName() ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>

        <label for="subject">Subject :</label>
        <textarea name="subject" id="subject" rows="10"><?= htmlspecialchars($_POST['subject'] ?? '') ?></textarea>
        <?php if(!empty($errors)): ?>
            <?= $data->arrayKeyExist('subject', $errors) ?>
        <?php endif; ?>

        <button type="submit">Submit</button>
    </form>

<?php else: ?>
    <div class="forum-success">
        <p class="success"><?= $success ?> <i class="fas fa-smile-beam"></i></p>
        <div class="forum-actions">
            <a class="forum-add-topic" href="<?= CreateUrl::url('forum/' . $urlTopic) ?>">See your topic</a>
            <a class="forum-btn-secondary" href="<?= CreateUrl::url('forum/' . $urlSubCat) ?>">Back to <?= htmlspecialchars($_POST['subCat']) ?></a>
        </div>
    </div>
<?php endif; ?>

</section>