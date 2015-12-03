<?php
include("config.php");

$query_set_value = " created_date=NOW()";

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "";
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";
$group_history_id = isset($_REQUEST['group_history_id']) ? $_REQUEST['group_history_id'] :  "";
$latitude = isset($_REQUEST['latitude']) ? $_REQUEST['latitude'] :  "";
$longitude = isset($_REQUEST['longitude']) ? $_REQUEST['longitude'] :  "";


if($group_id != NULL){ $query_set_value .=", group_id='$group_id' "; }
if($latitude != NULL){ $query_set_value .=", latitude='$latitude' "; }
if($longitude != NULL){ $query_set_value .=", longitude='$longitude' "; }


if($action == "add" && $group_id != '' && $latitude != '' && $longitude != '' ){

	 	$query = "INSERT INTO group_history SET $query_set_value";

		mysql_query($query);

		$insert_id = mysql_insert_id();
		//$insert_id = 11;

		if($insert_id > 0 ){

			$select_query = "select * from group_history where group_history_id = $insert_id";

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

}elseif($action == "edit" && $group_history_id != ''){

	$select_query = "SELECT * FROM group_history WHERE group_history_id = '$group_history_id'";

	$data = mysql_query($select_query);

	if(mysql_num_rows($data) > 0){

		$result = mysql_fetch_assoc($data);

		$update_query = "UPDATE group_history SET $query_set_value WHERE group_history_id = ".$result['group_history_id'];

		$data1 = mysql_query($update_query);

		$select_query = "SELECT * FROM group_history WHERE group_history_id = '$group_history_id'";

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

