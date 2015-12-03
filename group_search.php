<?php
include("config.php");

$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";
$group_pin = isset($_REQUEST['group_pin']) ? $_REQUEST['group_pin'] :  "";


if($group_id != "" || $group_pin != ""){

	if($group_id != ''){
		$query_set_value = "group_id = '$group_id'";
	}else{
		$query_set_value = "group_pin = '$group_pin'";
	}

	$query = "select * from user_group where $query_set_value";

	$data = mysql_query($query);

	$total_row = mysql_num_rows($data);

	if($total_row > 0 ){

		$result = mysql_fetch_assoc($data);

		$arr_result = array( RESULT => SUCCESS,
							MESSAGE =>	"Groups Detail",
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

