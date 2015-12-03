<?php
include("config.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "";
$tap_id = isset($_REQUEST['tap_id']) ? $_REQUEST['tap_id'] :  "";


if($action == "delete" && $tap_id != ''){
    $update_query = "delete from tap WHERE tap_id = ".$tap_id;
    
    $data1 = mysql_query($update_query);
    if($data1)
    {
        $arr_result = array( RESULT => SUCCESS,
								MESSAGE =>	"delete successfully",
								DATA => ""
								);
    }else{
        $arr_result = array( RESULT => FAILED,
								MESSAGE =>	"something went wrong",
								DATA => ""
								);
    }
} 


echo json_encode($arr_result);


?>

