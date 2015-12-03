<?php
include("config.php");
include("./sendNotification.php");

$timeZone = isset($_REQUEST['timeZone']) ? $_REQUEST['timeZone'] :  "Asia/Kolkata";
date_default_timezone_set($timeZone);

$query_set_value = " created_date= '" . date('Y-m-d H:i:s') . "'";

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "";
$tap_id = isset($_REQUEST['tap_id']) ? $_REQUEST['tap_id'] :  "";
$admin_id = isset($_REQUEST['admin_id']) ? $_REQUEST['admin_id'] :  "";
//$members_id = isset($_REQUEST['members_id']) ? $_REQUEST['members_id'] :  "";
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] :  "";
$location = isset($_REQUEST['location']) ? $_REQUEST['location'] :  "";
$tap_time = isset($_REQUEST['tap_time']) ? $_REQUEST['tap_time'] :  "";
$tap_image = isset($_REQUEST['tap_image']) ? $_REQUEST['tap_image'] :  "";
$tap_pref = isset($_REQUEST['tap_pref']) ? $_REQUEST['tap_pref'] :  "";
$lat = isset($_REQUEST['lat']) ? $_REQUEST['lat'] :  "";
$lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] :  "";

//Comment By Yash
//if($_FILES['tap_image']['name'] != ''){
//	$tmp_name = $_FILES["tap_image"]["tmp_name"];
//	$namefile = $_FILES["tap_image"]["name"];
//	$ext = end(explode(".", $namefile));
//	$tap_image =time().".".$ext;
//	$fileUpload = move_uploaded_file($tmp_name,"tap_image/".$tap_image);
//}else{
//	$tap_image = $tap_image;
//}


if($admin_id != NULL){ $query_set_value .=", admin_id='$admin_id' "; }
if($group_id != NULL){ $query_set_value .=", group_id='$group_id' "; } // grup id
$query_set_value .=", name='' ";
if($location != NULL){ $query_set_value .=", location='$location' "; }
$query_set_value .=", tap_image='' ";
if($tap_time != NULL){ $query_set_value .=", tap_time='$tap_time' "; }
if($tap_pref != NULL){ $query_set_value .=", tap_pref='$tap_pref' "; }
if($lat != NULL){ $query_set_value .=", lat='$lat' "; }
if($lang != NULL){ $query_set_value .=", lang='$lang' "; }


if($action == "add" && $admin_id != '' && $group_id != '' ){

	 	$query = "INSERT INTO tap SET $query_set_value";

		mysql_query($query);

		$insert_id = mysql_insert_id();
		//$insert_id = 11;

		if($insert_id > 0 ){

			$select_query = "SELECT * FROM tap WHERE tap_id = $insert_id";

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

}elseif($action == "edit" && $tap_id != ''){

	$select_query = "SELECT * FROM tap WHERE tap_id = '$tap_id'";

	$data = mysql_query($select_query);

	if(mysql_num_rows($data) > 0){

		$result = mysql_fetch_assoc($data);

		$update_query = "UPDATE tap SET $query_set_value WHERE tap_id = ".$result['tap_id'];

		$data1 = mysql_query($update_query);

		$select_query = "SELECT * FROM tap WHERE tap_id = '$tap_id'";

		$data = mysql_query($select_query);

		$result = mysql_fetch_assoc($data);
                
                // Send notification to all group members
                
    
                $qry = "select user_group.*, user.udid,user.name from user_group left join user on user_group.admin_id = user.user_id where group_id = ". $result['group_id'];
                $groupAdminDetails = selectForJason($qry);
                
                $members = str_replace("|", ",",$groupAdminDetails[0]['members_id']);
                $sendNoti = new sendNotification();
                foreach($members as $member)
                {
                    $qry = "select * from user where user_id = $member";
                    $userDetails = selectForJason($qry);

                    $msg = $groupAdminDetails[0]["name"] . " has change the tap information" ;
                    if($userDetails[0]['udid'] != "")
                    {
                        $sendNoti->sendNotificationByApns($userDetails[0]['udid'],$msg , "tapDetailChang", $tap_id);
                    }
                }
                
                // send notification end
                

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

