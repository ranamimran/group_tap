<?php
include("config.php");
include("./sendNotification.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "";

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


if($action == "locationRequest"){
    $tap_id = isset($_REQUEST['tap_id']) ? $_REQUEST['tap_id'] :  "";
    $update_query = "update tap set isSendLocation = 1 WHERE tap_id = ".$tap_id;
    
    $data1 = mysql_query($update_query);
    
    // Send Notification
    
    $qry = "select * from tap where tap_id = $tap_id";
    $tapDetails = selectForJason($qry);
    
    $qry = "select user_group.*, user.udid,user.name from user_group left join user on user_group.admin_id = user.user_id where group_id = ". $tapDetails[0]['group_id'];
    $groupAdminDetails = selectForJason($qry);

    $members = str_replace("|", ",",$groupAdminDetails[0]['members_id']);
    $sendNoti = new sendNotification();
    foreach($members as $member)
    {
        $qry = "select * from user where user_id = $member";
        $userDetails = selectForJason($qry);

        $msg = $groupAdminDetails[0]["name"] . " has request to send your location" ;
        if($userDetails[0]['udid'] != "")
        {
            $sendNoti->sendNotificationByApns($userDetails[0]['udid'],$msg , "locationRequest", $tap_id);
        }
    }
    
    // end notification
    
    if($data1)
    {
        $arr_result = array( RESULT => SUCCESS,
								MESSAGE =>	"request sent successfully",
								DATA => ""
								);
    }else{
        $arr_result = array( RESULT => FAILED,
								MESSAGE =>	"something went wrong",
								DATA => ""
								);
    }
}elseif($action == "updateUserLocation")
{
    $member_id = isset($_REQUEST['member_id']) ? $_REQUEST['member_id'] :  "";
    $tap_id = isset($_REQUEST['tap_id']) ? $_REQUEST['tap_id'] :  "";
    $latitude = isset($_REQUEST['latitude']) ? $_REQUEST['latitude'] :  "";
    $longitude = isset($_REQUEST['longitude']) ? $_REQUEST['longitude'] :  "";
    $update_query = "update user set latitude = $latitude , longitude = $longitude WHERE user_id = ".$member_id;
    
    $data1 = mysql_query($update_query);
    
    // insert user entry for track of location send 
    $update_query = "insert into userlocationfortap set tap_id = $tap_id , user_id = $member_id";
    
//    print_r($update_query);
    $data1 = mysql_query($update_query);
    
    if($data1)
    {
        $arr_result = array( RESULT => SUCCESS,
								MESSAGE =>	"sent location successfully",
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

