<?php

use App\URL\ExplodeUrl;
use App\URL\CreateUrl;
use App\Manager\FilmDatabase;
use App\Security\Csrf;

// This route only exists in POST: a simple link can never delete a review
if(Csrf::validate($_POST['csrf_token'] ?? null)){
    $id = (new ExplodeUrl($_GET['url']))->getId();
    (new FilmDatabase())->deleteFilm($id);
    $_SESSION['flash_dash'] = 'Review deleted.';
}else{
    $_SESSION['flash_dash'] = 'Your session expired, nothing was deleted.';
}

header('Location: ' . CreateUrl::url('dashboard/reviews'));
exit;