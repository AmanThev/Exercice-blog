<?php

use App\Manager\ForumDatabase;
use App\Manager\Exception\NotFoundException;
use App\Form\AddSubCategory;
use App\Form\EditSubCategory;
use App\URL\CreateUrl;
use App\Security\Csrf;

// dashboard/forum/newSubCategory[?category=2]  (a new sub-category)  or  dashboard/forum/subCategory/5  (edit the sub-category 5)
$parts  = explode('/', trim($_GET['url'], '/'));
$isEdit = ($parts[2] ?? '') === 'subCategory';
$id     = $isEdit ? ($parts[3] ?? '') : '';

$forumDb     = new ForumDatabase();
$subCategory = null;
if($isEdit && ctype_digit($id)){
    try{
        $subCategory = $forumDb->getSubCategoryById((int)$id);
    }catch(NotFoundException $e){
        $subCategory = null;
    }
}
$notFound = $isEdit && $subCategory === null;

$title  = $notFound ? 'Sub-category not found' : ($isEdit ? 'Edit sub-category' : 'New sub-category');
$errors = [];

if($notFound){
    http_response_code(404);
}elseif(!empty($_POST)){
    $form = $isEdit ? new EditSubCategory($_POST) : new AddSubCategory($_POST);

    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors['form'][] = 'Your session expired, please try again.';
    }elseif($form->validateSubCategory($isEdit ? html_entity_decode($subCategory->getName(), ENT_QUOTES) : null)->resultValidator()){
        if($isEdit){
            $form->updateSubCategory((int)$id);
            $_SESSION['flash_dash'] = 'Sub-category updated.';
        }else{
            $form->createSubCategory();
            $_SESSION['flash_dash'] = 'Sub-category created.';
        }
        header('Location: ' . CreateUrl::url('dashboard/forum'));
        exit;
    }else{
        $errors = $form->returnErrors();
    }
}

$categories = $notFound ? [] : $forumDb->getCategories();

// What is shown in the fields : what was just typed, otherwise the sub-category (its getters already escape the text).
// For a new sub-category, the category can be chosen in advance with ?category=2
$typed = fn(string $key) => (isset($_POST[$key]) && is_string($_POST[$key])) ? $_POST[$key] : null;
$name  = ($typed('name') !== null) ? htmlspecialchars($typed('name')) : ($isEdit && $subCategory ? $subCategory->getName() : '');
$chosenCategory = $typed('id_categories')
    ?? ($isEdit && $subCategory ? (string)$subCategory->getIdCategory() : (is_string($_GET['category'] ?? null) ? $_GET['category'] : ''));

$fieldError = fn(string $key) => isset($errors[$key]) ? htmlspecialchars(implode(' ', $errors[$key])) : '';
?>

<?php if($notFound): ?>
    <div class="dash-pagehead">
        <h2 class="dash-title">Sub-category not found</h2>
        <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/forum') ?>"><i class="fas fa-chevron-left"></i> Back to the forum</a>
    </div>
    <p class="dash-empty">This sub-category doesn't exist.</p>
<?php else: ?>
    <div class="dash-pagehead">
        <h2 class="dash-title"><?= $isEdit ? 'Edit sub-category' : 'New sub-category' ?></h2>
        <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/forum') ?>"><i class="fas fa-chevron-left"></i> Back to the forum</a>
    </div>

    <form class="dash-form" action="" method="post" novalidate>
        <?= Csrf::field() ?>

        <?php if(!empty($errors['form'])): ?>
            <p class="dash-message error"><?= htmlspecialchars($errors['form'][0]) ?></p>
        <?php endif; ?>

        <div class="field">
            <label for="id_categories">Category</label>
            <select name="id_categories" id="id_categories">
                <option value="">Choose a category</option>
                <?php foreach($categories as $category): ?>
                    <option value="<?= $category->getId() ?>" <?= (string)$category->getId() === (string)$chosenCategory ? 'selected' : '' ?>><?= $category->getName() ?></option>
                <?php endforeach; ?>
            </select>
            <p class="field-error"><?= $fieldError('id_categories') ?></p>
        </div>

        <div class="field">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= $name ?>" maxlength="100" autocomplete="off">
            <small class="hint">Words with letters only, separated by one space, for example "Visual Effects". It is part of the address of the page, and it must not be used by another sub-category.</small>
            <p class="field-error"><?= $fieldError('name') ?></p>
        </div>

        <div class="form-actions">
            <button class="dash-submit" type="submit"><span><?= $isEdit ? 'Save changes' : 'Create the sub-category' ?></span></button>
            <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/forum') ?>">Cancel</a>
        </div>
    </form>
<?php endif; ?>