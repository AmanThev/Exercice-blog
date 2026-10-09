<?php

use App\URL\ExplodeUrl;
use App\URL\CreateUrl;
use App\Manager\PostDatabase;
use App\Security\Csrf;

// Cette route n'existe qu'en POST : un simple lien ne peut jamais supprimer un post
if(Csrf::validate($_POST['csrf_token'] ?? null)){
    $id = (new ExplodeUrl($_GET['url']))->getId();
    (new PostDatabase())->deletePost($id);
    $_SESSION['flash_dash'] = 'Post deleted.';
}else{
    $_SESSION['flash_dash'] = 'Your session expired, nothing was deleted.';
}

header('Location: ' . CreateUrl::url('dashboard/posts'));
exit;