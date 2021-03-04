<?php

use App\URL\ExplodeUrl;

$url            = new ExplodeUrl($_GET['url']);
$id             = $url->getId();



?>

<h1>Édition du post #<?= $id ?></h1>