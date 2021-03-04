<?php

use App\URL\ExplodeUrl;

$url            = new ExplodeUrl($_GET['url']);
$id             = $url->getId();


//class postDatabase
//header('Location: ' . $urldashboard)

?>

<h1>Suppression du post #<?= $id ?></h1>