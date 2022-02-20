<?php 

use App\Form\Validator;
use App\Helpers\File;

//header("Content-Type:application/json; charset=UTF-8");

$maxSize = File::maxUpload();

if($_SERVER['CONTENT_LENGTH'] >= $maxSize){
	throw new Exception("The file is too big!!!");
};

$data = new Validator($_POST);
$data->check('required', ['author', 'reviewTitle', 'year', 'director', 'writer', 'cast', 'production', 'genre', 'synopsis', 'review']);
$data->check('lengthBetween', 'author', 2, 20);
$data->check('exist', 'reviewTitle', 'films');
$data->check('lengthMax', 'synopsis', 250);
$data->check('numberBetween', 'score', 0, 5);
$data->check('year', 'year');
if(is_uploaded_file($_FILES['poster']['tmp_name'])){
	$data->check('extensionPicture', 'poster', $_FILES['poster']);
}
$data->validateForm();


$result = [];
$errors = $data->getErrors();

if($data->validateForm()){
	$result["status"] = "ok";
	$result["good"]   = "Your film has been added"; 
}else{
	$result["status"] = "error";
	$result["error"]  = $errors;
}

// upload file
echo json_encode($result);

