<?php 


//header("Content-Type:application/json; charset=UTF-8");

$result = [];

$data = new Validator($_POST);

$data->check('required', ['author', 'title', 'year', 'director', 'writer', 'starring', 'production', 'genre', 'synopsis', 'review']);
$data->check('lengthBetween', 'author', 2, 20);
$data->check('exist', 'title');
$data->check('lengthMax', 'synopsis', 250);
$data->check('numberBetween', 'score', 0, 5);
$data->check('date', 'date');
$data->check('extensionPicture', 'picture');


//les inputs doit être rempli
//le nom de l'auteur doit être inférieur à 20 caractères
//le synopsis ne doit pas dépasser 250 caractères
//le score doit être entre 0 et 5
//format date utiliser => preg_match
//le titre ne doit pas déjà exister
//vérifer l'extension du fichier


$data = [];
$author = $_POST['author'];

if(strlen($author) > 20){
	$data["status"]  = "error";
	$data["error"] = "Your pseudo exceed 20 characters";
}else{
	$data['status'] = "ok";
	$data['result'] = "Your post has been posted";
}

echo json_encode($_POST);
