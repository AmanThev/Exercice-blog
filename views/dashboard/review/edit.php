<?php

use App\URL\ExplodeUrl;
use App\URL\CreateUrl;
use App\Manager\FilmDatabase;
use App\Form\EditFilm;
use App\Security\Csrf;

$id     = (new ExplodeUrl($_GET['url']))->getId();
$film   = (new FilmDatabase())->getFilmById($id);

$title  = 'Edit review';
$errors = [];

if(!empty($_POST)){
    $form = new EditFilm($_POST);
    $file = $_FILES['poster'] ?? [];

    if(!Csrf::validate($_POST['csrf_token'] ?? null)){
        $errors['form'][] = 'Your session expired, please try again.';
    }elseif($form->validateEdit($film->getTitle(), $file)->resultValidator()){
        $form->updateFilm($id, $file);

        $_SESSION['flash_dash'] = 'Review updated.';
        header('Location: ' . CreateUrl::url('dashboard/reviews'));
        exit;
    }else{
        $errors = $form->returnErrors();
    }
}

// Values shown : what the user has just typed, otherwise the ones of the database
// (the ones of the database are already encoded by the Film model, they are not escaped twice)
$fromDatabase = [
    'author'     => $film->getAuthor(),
    'title'      => $film->getTitle(),
    'date'       => $film->getDate(),
    'director'   => $film->getDirector(),
    'writer'     => $film->getWriter(),
    'cast'       => $film->getCast(),
    'production' => $film->getProduction(),
    'genre'      => $film->getGenre(),
    'synopsis'   => $film->getSynopsis(),
    'review'     => $film->getReviewForEdit(),
    'score'      => $film->getScore(),
];
$values = [];
foreach($fromDatabase as $key => $value){
    $typed = ($key !== 'author' && isset($_POST[$key]) && is_string($_POST[$key]));
    $values[$key] = $typed ? htmlspecialchars($_POST[$key]) : $value;
}

// Variables read by the shared fields (_fields.php)
$isEdit        = true;
$currentPoster = $film->getPoster();
$fieldError    = fn(string $key) => isset($errors[$key]) ? htmlspecialchars(implode(' ', $errors[$key])) : '';
?>

<div class="dash-pagehead">
    <h2 class="dash-title">Edit review</h2>
    <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/reviews') ?>"><i class="fas fa-chevron-left"></i> Back to reviews</a>
</div>

<form class="dash-form" action="" method="post" enctype="multipart/form-data" novalidate>
    <?= Csrf::field() ?>

    <?php if(!empty($errors['form'])): ?>
        <p class="dash-message error"><?= htmlspecialchars($errors['form'][0]) ?></p>
    <?php endif; ?>

    <?php require VIEWS . 'dashboard/review/_fields.php'; ?>

    <div class="form-actions">
        <button class="dash-submit" type="submit"><span>Save changes</span></button>
        <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/reviews') ?>">Cancel</a>
    </div>
</form>

<script src="<?= PUBLIC_PATH ?>/js/imagePreview.js"></script>