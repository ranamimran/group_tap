<?php
include("config.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "";
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";


if($action == "delete" && $group_id != ''){
    $update_query = "delete from user_group WHERE group_id = ".$group_id;
    
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

