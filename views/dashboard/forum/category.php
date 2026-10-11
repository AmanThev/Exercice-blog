<?php

use App\Manager\ForumDatabase;
use App\Manager\Exception\NotFoundException;
use App\Form\AddCategory;
use App\Form\EditCategory;
use App\URL\CreateUrl;
use App\Security\Csrf;

// dashboard/forum/newCategory  (a new category)  or  dashboard/forum/category/3  (edit the category 3)
$parts  = explode('/', trim($_GET['url'], '/'));
$isEdit = ($parts[2] ?? '') === 'category';
$id     = $isEdit ? ($parts[3] ?? '') : '';

$category = null;
if($isEdit && ctype_digit($id)){
    try{
        $category = (new ForumDatabase())->getCategoryById((int)$id);
    }catch(NotFoundException $e){
        $category = null;
    }
}
$notFound = $isEdit && $category === null;

$title  = $notFound ? 'Category not found' : ($isEdit ? 'Edit category' : 'New category');
$errors = [];

if($notFound){
    http_response_code(404);
}elseif(!empty($_POST)){
    $form = $isEdit ? new EditCategory($_POST) : new AddCategory($_POST);

    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors['form'][] = 'Your session expired, please try again.';
    }elseif($form->validateCategory($isEdit ? html_entity_decode($category->getName(), ENT_QUOTES) : null)->resultValidator()){
        if($isEdit){
            $form->updateCategory((int)$id);
            $_SESSION['flash_dash'] = 'Category updated.';
        }else{
            $form->createCategory();
            $_SESSION['flash_dash'] = 'Category created.';
        }
        header('Location: ' . CreateUrl::url('dashboard/forum'));
        exit;
    }else{
        $errors = $form->returnErrors();
    }
}

// What is shown in the fields : what was just typed, otherwise the category (its getters already escape the text)
$typed = fn(string $key) => (isset($_POST[$key]) && is_string($_POST[$key])) ? htmlspecialchars($_POST[$key]) : null;
$name  = $typed('name')         ?? ($isEdit && $category ? $category->getName()  : '');
$intro = $typed('introduction') ?? ($isEdit && $category ? $category->getIntro() : '');

$fieldError = fn(string $key) => isset($errors[$key]) ? htmlspecialchars(implode(' ', $errors[$key])) : '';
?>

<?php if($notFound): ?>
    <div class="dash-pagehead">
        <h2 class="dash-title">Category not found</h2>
        <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/forum') ?>"><i class="fas fa-chevron-left"></i> Back to the forum</a>
    </div>
    <p class="dash-empty">This category doesn't exist.</p>
<?php else: ?>
    <div class="dash-pagehead">
        <h2 class="dash-title"><?= $isEdit ? 'Edit category' : 'New category' ?></h2>
        <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/forum') ?>"><i class="fas fa-chevron-left"></i> Back to the forum</a>
    </div>

    <form class="dash-form" action="" method="post" novalidate>
        <?= Csrf::field() ?>

        <?php if(!empty($errors['form'])): ?>
            <p class="dash-message error"><?= htmlspecialchars($errors['form'][0]) ?></p>
        <?php endif; ?>

        <div class="field">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= $name ?>" maxlength="100" autocomplete="off">
            <small class="hint">Words with letters only, separated by one space, for example "Cinema professions". It is part of the address of the page.</small>
            <p class="field-error"><?= $fieldError('name') ?></p>
        </div>

        <div class="field">
            <label for="introduction">Introduction</label>
            <textarea name="introduction" id="introduction" rows="6"><?= $intro ?></textarea>
            <small class="hint">10 to 1000 characters. It is shown at the top of the category page.</small>
            <p class="field-error"><?= $fieldError('introduction') ?></p>
        </div>

        <div class="form-actions">
            <button class="dash-submit" type="submit"><span><?= $isEdit ? 'Save changes' : 'Create the category' ?></span></button>
            <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/forum') ?>">Cancel</a>
        </div>
    </form>
<?php endif; ?>