<?php
include("config.php");

$query_set_value = " created_date=NOW()";

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "";
$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] :  "";
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] :  "";
$image = isset($_REQUEST['image']) ? $_REQUEST['name'] :  "";
$udid = isset($_REQUEST['udid']) ? $_REQUEST['udid'] :  "";
$fb_id = isset($_REQUEST['fb_id']) ? $_REQUEST['fb_id'] :  "";
$email = isset($_REQUEST['email']) ? $_REQUEST['email'] :  "";
$mobile = isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] :  "";
$latitude = isset($_REQUEST['latitude']) ? $_REQUEST['latitude'] :  "";
$longitude = isset($_REQUEST['longitude']) ? $_REQUEST['longitude'] :  "1";

if($_FILES['image']['name'] != ''){
	$tmp_name = $_FILES["image"]["tmp_name"];
	$namefile = $_FILES["image"]["name"];
	$ext = end(explode(".", $namefile));
	$image_name =time().".".$ext;
	$fileUpload = move_uploaded_file($tmp_name,"img/".$image_name);
}else{
	$image_name = $image;
}


if($user_id != NULL){ $query_set_value .=", user_id='$user_id' "; }
if($name != NULL){ $query_set_value .=", name='$name' "; }
if($image_name != NULL){ $query_set_value .=", image='$image_name' "; }
if($udid != NULL){ $query_set_value .=", udid='$udid' "; }
if($fb_id != NULL){ $query_set_value .=", fb_id='$fb_id' "; }
if($email != NULL){ $query_set_value .=", email='$email' "; }
if($mobile != NULL){ $query_set_value .=", mobile='$mobile' "; }
if($latitude != NULL){ $query_set_value .=", latitude='$latitude' "; }
if($longitude != NULL){ $query_set_value .=", longitude='$longitude' "; }


if($action == "add" && $fb_id != ""){

	$select_email = mysql_query("Select * From user Where fb_id = '$fb_id'");

	if(mysql_num_rows($select_email)) {

		$arr_result = array( RESULT => FAILED,
							MESSAGE =>	"User already exists.",
							DATA => ""
							);

	}else{

		$query = "INSERT INTO user SET $query_set_value";

		mysql_query($query);

		$insert_id = mysql_insert_id();

		if($insert_id > 0 ){

			$select_query = "select * from user where user_id = $insert_id";

			$data = mysql_query($select_query);

			$result = mysql_fetch_assoc($data);

			$arr_result = array( RESULT => SUCCESS,
								MESSAGE =>	"Data Insert Succesfully ",
								DATA => $result
								);
			}else{
					$arr_result = array( RESULT => FAILED,
										MESSAGE =>	"Data Insert not Succesfully ",
										DATA => ""
										);
			}
	}

}elseif($action == "edit" && $fb_id != ''){

	$select_query = "select * from user where fb_id = '$fb_id'";

	$data = mysql_query($select_query);

	if(mysql_num_rows($data) > 0){

		$result = mysql_fetch_assoc($data);

		$update_query = "UPDATE user SET $query_set_value WHERE user_id = ".$result['user_id'];

		mysql_query($update_query);

		$select_query = "select * from user where fb_id = '$fb_id'";

		$data = mysql_query($select_query);

		$result = mysql_fetch_assoc($data);

		$arr_result = array( RESULT => SUCCESS,
							MESSAGE =>	"Data Update Succesfully ",
							DATA => $result
							);
	}else{
		$arr_result = array( RESULT => SUCCESS,
								MESSAGE =>	"No Record Found",
								DATA => $result
								);
	}

}else{
	$arr_result = array( RESULT => FAILED,
						MESSAGE =>	"Plese Provide Data",
						DATA => ""
						);
}



echo json_encode($arr_result);


?>

