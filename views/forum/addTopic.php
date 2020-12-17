<?php

use App\Manager\ForumDatabase;

$categories = new ForumDatabase();
$categories = $categories->getCategories();

$subCats = new ForumDatabase();

$title = "New Topic";

?>

<h1 class="title-forum">New Topic</h1>

<section class="forum add-topic">
    <h2>Write your Topic</h2>
    <div class="separate-forum"></div>
    <form action="" method="post">
        <div class="inputBox">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div class="inputBox">
            <label for="name">Your name:</label>
            <input type="text" name="name" id="name" required>
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
            <textarea name="subject" id="subject" cols="30" rows="10"></textarea>
        </div>
        <input type="submit" name="login" value="Submit">
    </form>
</section>