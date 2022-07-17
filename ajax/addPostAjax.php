<?php

use App\Form\Validator;
use App\Helpers\File;
use App\Model\Post;
use App\Manager\Database;
use App\Manager\UserDatabase;
use App\Manager\PostDatabase;

$maxSize = File::maxUpload();

if($_SERVER['CONTENT_LENGTH'] >= $maxSize){
	throw new Exception("The file is too big!!!");
};

$data = new Validator($_POST);
$data->check('required', ['author', 'title']);
$data->check('lengthBetween', 'author', 2, 20);
$data->check('used', 'title', 'posts');
$data->check('lengthMin', 'content', 20);
if($_POST['author']){
	$data->check('exist', 'author', 'admins', 'name');
}
if(!empty($_FILES['picture']['tmp_name'])){
	$data->check('extensionPicture', 'picture', $_FILES['picture']);
}
$data->validateForm();

$result = [];
$errors = $data->getErrors();

if($data->validateForm()){
	$user = new UserDatabase();
	$user = $user->getAdminByName($_POST['author']);

	$_POST['IdAdmin'] = $user->getId();
	$_POST['public'] = $_POST['public'] === 'true' ? "1" : "0";
	$_FILES['picture']['title'] = $_POST['title'];

	$post = new Post();
	Database::hydrate($post, $_POST);
	
	$newPost = new PostDatabase();
	$newPost->createPost($post);
	if(!empty($_FILES['picture']['tmp_name'])){
		Database::hydrateFile($post, $_FILES);
		$newPost->uploadFile('posts', $_FILES, 'postPicture');
	}

	$result["status"] 	= "ok";
	$result["good"] 	= "Your post has been posted"; 
}else{
	$result["status"] 	= "error";
	$result["error"] 	= $errors;
}

echo json_encode($result);





