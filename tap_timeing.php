<?php
include("config.php");

function selectForJason($sql = "") {
    $data = array();
    $results = mysql_query($sql);
    if ((!$results) or (empty($results))) {
        return $data;
    }
    $count = 0;
    while ($row = mysql_fetch_assoc($results)) {
        foreach ($row as $key => $value) {
            if (!is_array($value)) {
                $row[$key] = htmlspecialchars_decode($value, ENT_QUOTES);
            }
        }
        $data[$count] = $row;
        $count++;
    }
    mysql_free_result($results);
    return $data;
}


$query_set_value = " created_date=NOW()";

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "";
$tap_timeing_id = isset($_REQUEST['tap_timeing_id']) ? $_REQUEST['tap_timeing_id'] :  "";
$tap_id = isset($_REQUEST['tap_id']) ? $_REQUEST['tap_id'] :  "";
$member_id = isset($_REQUEST['member_id']) ? $_REQUEST['member_id'] :  "";
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] :  "";

if($member_id != NULL){ $query_set_value .=", member_id='$member_id' "; }
if($status != NULL){ $query_set_value .=", status='$status' "; }
if($tap_id != NULL){ $query_set_value .=", tap_id='$tap_id' "; }

if($action == "add" && $member_id != '' && $status != '' ){

	 	$query = "INSERT INTO tap_timing SET $query_set_value";
//                print_r($query);exit;
		mysql_query($query);

		$insert_id = mysql_insert_id();
		//$insert_id = 11;
                
                // send notification
                
                $qry = "select * from tap where tap_id = $tap_id";
                $tapDetails = selectForJason($qry);

                $qry = "select user_group.*, user.udid,user.name from user_group left join user on user_group.admin_id = user.user_id where group_id = ". $tapDetails[0]['group_id'];
                $groupAdminDetails = selectForJason($qry);

                $qry = "select * from user where user_id = $member_id";
                $userDetails = selectForJason($qry);
                
                if($status == 1)
                {
                    $msg = $userDetails[0]["name"] . " is running late" ;
                }else if($status == 1)
                {
                    $msg = $userDetails[0]["name"] . " will not coming" ;
                }else{
                    $msg = $userDetails[0]["name"] . " will come";
                }

                $sendNoti = new sendNotification();
                
                if($userDetails[0]['udid'] != "")
                {
                    $sendNoti->sendNotificationByApns($groupAdminDetails[0]['udid'],$msg , "tapTiming", $tap_id);
                }
                
		if($insert_id > 0 ){

			$select_query = "SELECT * FROM tap_timing WHERE tap_timing_id = $insert_id";

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

}elseif($action == "edit" && $tap_id != '' && $member_id != ""){

	$select_query = "SELECT * FROM tap_timeing WHERE tap_timeing_id = '$tap_timeing_id'";

	$data = mysql_query($select_query);

	if(mysql_num_rows($data) > 0){

		$result = mysql_fetch_assoc($data);

		$update_query = "UPDATE tap_timeing SET $query_set_value WHERE tap_timeing_id = ".$result['tap_timeing_id'];

		$data1 = mysql_query($update_query);

		$select_query = "SELECT * FROM tap_timeing WHERE tap_timeing_id = '$tap_timeing_id'";

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

