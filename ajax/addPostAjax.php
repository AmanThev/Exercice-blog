<?php

use App\Form\Validator;
use App\Helpers\File;

$maxSize = File::maxUpload();

if($_SERVER['CONTENT_LENGTH'] >= $maxSize){
	throw new Exception("The file is too big!!!");
};

$data = new Validator($_POST);
$data->check('required', ['author', 'titlePost']);
$data->check('lengthBetween', 'author', 2, 20);
$data->check('exist', 'titlePost', 'posts');
$data->check('lengthMin', 'content', 20);
if(!empty($_FILES['picture'])){
	$data->check('extensionPicture', 'picture', $_FILES['picture']);
}
$data->validateForm();

$result = [];
$errors = $data->getErrors();

if($data->validateForm()){
	$result["status"] = "ok";
	$result["good"] = "Your post has been posted"; 
}else{
	$result["status"] = "error";
	foreach($errors as $error => $key){
		$result["error"] = $key;
	}
}
echo json_encode($result);

// if(strlen($author) > 20){
// 	$data["status"]  = "error";
// 	$data["error"] = "Your pseudo exceed 20 characters";
// }else{
// $data['status'] = "ok";
// $data['result'] = "Your post has been posted";
// }

//echo json_encode($data);


//  $public    = $_POST['public'] === 'true' ? "1" : "0";;

// if ( $_FILES['file']['error'] > 0 ){
// 	echo 'Error: ' . $_FILES['file']['error'] . '<br>';
// }
// else {
// 	if(move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']))
// 	{
// 		echo "File Uploaded Successfully";
// 	}
// }