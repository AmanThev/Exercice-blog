<?php

use App\URL\ExplodeUrl;
use App\HTML\Profile;


$url            = new ExplodeUrl($_GET['url']);
$slug           = $url->getSlug();

$title          = $slug;

$profile     = new Profile('members');


?>


<?= $profile->topProfile($slug); ?>

<?= $profile->bottomProfile(); ?>
