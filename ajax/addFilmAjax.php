<?php 

use App\Form\Validator;
use App\Helpers\File;

//header("Content-Type:application/json; charset=UTF-8");

$maxSize = File::maxUpload();

if($_SERVER['CONTENT_LENGTH'] >= $maxSize){
	throw new Exception("The file is too big!!!");
};

$data = new Validator($_POST);

$data->check('required', ['author', 'title', 'year', 'director', 'writer', 'starring', 'production', 'genre', 'synopsis', 'review']);
$data->check('lengthBetween', 'author', 2, 20);
$data->check('exist', 'reviewTitle', 'film');
$data->check('lengthMax', 'synopsis', 250);
$data->check('numberBetween', 'score', 0, 5);
$data->check('year', 'date');
if(!empty($_FILES['poster'])){
	$data->check('extensionPicture', 'poster', $_FILES['poster']);
}
$data->validateForm();


$result = [];
$errors = $data->getErrors();

if($data->validateForm()){
	$result["status"] 	= "ok";
	$result["good"] 	= "Your film has been added"; 
}else{
	$result["status"] 	= "error";
	$result["error"] 	= $errors;
}
echo json_encode($result);

