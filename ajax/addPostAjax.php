<?php
/*
	Error :
		pseudo exceed 20 caracters
		extension picture
		title already exist
		empty author and title
*/
$data = [];
$author = $_POST['author'];

if(strlen($author) > 20){
	$data["status"]  = "error";
	$data["error"] = "Your pseudo exceed 20 characters";
}else{
	$data['status'] = "ok";
	$data['result'] = "Your post has been posted";
}

echo json_encode($data);


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