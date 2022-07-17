<?php 

use App\Form\Validator;
use App\Helpers\File;
use App\Model\Film;
use App\Manager\Database;
use App\Manager\UserDatabase;
use App\Manager\FilmDatabase;

// header("Content-Type:application/json; charset=UTF-8");

$maxSize = File::maxUpload();

if($_SERVER['CONTENT_LENGTH'] >= $maxSize){
	throw new Exception("The file is too big!!!");
};

$data = new Validator($_POST);
$data->check('required', ['author', 'title', 'date', 'director', 'writer', 'cast', 'production', 'genre', 'synopsis', 'review']);
if($_POST['author']){
	$data->check('exist', 'author', 'admins', 'name');
}
$data->check('lengthBetween', 'author', 2, 20);
$data->check('used', 'title', 'films');
$data->check('lengthMax', 'synopsis', 250);
$data->check('numberBetween', 'score', 0, 5);
$data->check('year', 'date'); 
if(is_uploaded_file($_FILES['poster']['tmp_name'])){
	$data->check('extensionPicture', 'poster', $_FILES['poster']);
}
$data->validateForm();

$result = [];
$errors = $data->getErrors();

if($data->validateForm()){
	$user = new UserDatabase();
	$user = $user->getAdminByName($_POST['author']);

	$_POST['IdAdmin'] = $user->getId();
	$_FILES['poster']['title'] = $_POST['title'];

	$film = new Film();
	Database::hydrate($film, $_POST);
	
	$newFilm = new FilmDatabase();
	$newFilm->createFilm($film);
	if(!empty($_FILES['poster']['tmp_name'])){
		Database::hydrateFile($film, $_FILES);
		$newFilm->uploadFile('films', $_FILES, 'posterFilm', 'poster');
	}

	$result["status"] = "ok";
	$result["good"]   = "Your film has been added"; 
}else{
	$result["status"] = "error";
	$result["error"]  = $errors;
}

echo json_encode($result);

