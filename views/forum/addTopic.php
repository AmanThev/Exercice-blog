<?php

use App\Manager\ForumDatabase;
use App\Form\AddTopic;
use App\URL\CreateUrl;

$categories = new ForumDatabase();
$categories = $categories->getCategories();

$subCats    = new ForumDatabase();
$incompleteForm = True;
$errors = [];

$title      = "New Topic";
if(!empty($_POST)){
    $data = new AddTopic($_POST);
    if($data->validateTopic()->resultValidator()){
        $incompleteForm = False;
        $urlSubCat = $data->getUrlSubCategory($_POST['subCat']);
        $data->createTopic($_POST);
        $urlTopic = $data->getUrlTopic();
        $success = "Your Topic has been added";
    }else{
        $errors = $data->returnErrors();
    }
}
?>

<h1 class="title-forum">New Topic</h1>

<section class="forum add-topic">
    <h2>Write your Topic</h2>
    <div class="separate-forum"></div>
    <form action="" method="post">
        <?php if($incompleteForm): ?>
            <div class="inputBox">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" value="<?php if(isset($_POST['title'])) echo $_POST['title'] ?>" required>
                <?php if(!empty($errors)): ?>
                    <?= $data->arrayKeyExist('title', $errors) ?>
                <?php endif; ?>
            </div>
            <div class="inputBox">
                <label for="name">Your name:</label>
                <input type="text" name="name" id="name" value="<?php if(isset($_POST['name'])) echo $_POST['name'] ?>" required>
                <?php if(!empty($errors)): ?>
                    <?= $data->arrayKeyExist('name', $errors) ?>
                <?php endif; ?>
            </div>
            <div>
                <label for="subCat">Name of the Sub-Category:</label>
                <select name="subCat" id="subCat">
                    <?php foreach($categories as $category): ?>
                        <optgroup label="<?= $category->getName() ?>"><?= $category->getName() ?>
                            <?php foreach($subCats->getSubCategories($category->getId()) as $subCat): ?>
                                <option value="<?= $subCat->getName() ?>"><?= $subCat->getName() ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="subject" for="subject">Subject:</label>
                <textarea name="subject" id="subject" cols="30" rows="10"><?php if(isset($_POST['subject'])) echo $_POST['subject'] ?></textarea>
                <?php if(!empty($errors)): ?>
                    <?= $data->arrayKeyExist('subject', $errors) ?>
                <?php endif; ?>
            </div>
            <input type="submit" value="Submit">
        <?php else: ?>
            <div class="successForm">
                <p><?= $success ?> <i class="fas fa-smile-beam"></i></p>
            </div>
            <div class="success-button">
                <a href="<?= CreateUrl::url('forum/'. $urlSubCat) ?>">
                    <input type="button" value="Go to : <?= $_POST['subCat'] ?>">
                </a>
                <a href="<?= CreateUrl::url('forum/'. $urlTopic) ?>">
                    <input type="button" value="See your Topic">
                </a>
            </div>
        <?php endif; ?>
    </form>
</section>