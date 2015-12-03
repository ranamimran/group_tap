<?php
include("config.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "login";
$email = isset($_REQUEST['email']) ? $_REQUEST['email'] :  "";
$udid = isset($_REQUEST['udid']) ? $_REQUEST['udid'] :  "";
$fb_id = isset($_REQUEST['fb_id']) ? $_REQUEST['fb_id'] :  "";

if($action == "login" && $fb_id != ""){

	$query = "select * from user where fb_id= '$fb_id'";

	$data = mysql_query($query);

	$total_id = mysql_num_rows($data);

	if($total_id > 0 ){

		$result = mysql_fetch_assoc($data);
                
                $update_query = "update user set udid = '" . $udid . "' WHERE fb_id = ".$fb_id ;
//                print_r($update_query);
//                exit;
                $data1 = mysql_query($update_query);

		$arr_result = array( RESULT => SUCCESS,
							MESSAGE =>	"Login Succesfully ",
							DATA => $result
							);
	}else{
		$arr_result = array( RESULT => FAILED,
							MESSAGE =>	"Plese Try Again",
							DATA => ""
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

