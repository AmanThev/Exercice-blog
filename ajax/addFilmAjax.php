<?php 


//header("Content-Type:application/json; charset=UTF-8");

$result = [];

// $data = new Validator($_POST);

// $data->check('required', ['author', 'title', 'year', 'director', 'writer', 'starring', 'production', 'genre', 'synopsis', 'review']);
// $data->check('lengthBetween', 'author', 2, 20);
// $data->check('exist', 'title');
// $data->check('lengthMax', 'synopsis', 250);
// $data->check('numberBetween', 'score', 0, 5);
// $data->check('date', 'date');
// $data->check('extensionPicture', 'picture');


$data = [];
//$author = $_POST['author'];

// if(strlen($author) > 20){
// 	$data["status"]  = "error";
// 	$data["error"] = "Your pseudo exceed 20 characters";
// }else{
$data['status'] = "ok";
$data['result'] = "Your post has been posted";
// }

echo json_encode($_POST);
