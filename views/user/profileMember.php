<?php

use App\URL\ExplodeUrl;
use App\HTML\Profile;


$url            = new ExplodeUrl($_GET['url']);
$slug           = $url->getSlug();

$title          = $slug;

$topProfile     = new Profile('members');


?>

<?= $topProfile->topProfile($slug); ?>