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


if($action == "searchByPin"){
    $group_pin = isset($_REQUEST['group_pin']) ? $_REQUEST['group_pin'] :  "";
    $query = "SELECT * FROM user_group WHERE group_pin = '".$group_pin ."'";
    $groupDetails = selectForJason($query);
   
    $mem_ids = str_replace("|", ",",$groupDetails[0]['members_id']);
    $select_query = "SELECT * FROM `user` WHERE user_id in ($mem_ids)";
    $memDetails = selectForJason($select_query);
    $groupDetails[0]["memDetails"] = $memDetails;
    if($groupDetails)
    {
        $arr_result = array( RESULT => SUCCESS,
								MESSAGE =>	"delete successfully",
								DATA => $groupDetails[0]
								);
    }else{
        $arr_result = array( RESULT => FAILED,
								MESSAGE =>	"something went wrong",
								DATA => ""
								);
    }
} elseif($action == "sendMembershiprRequest"){
    $group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] :  "";
    
    $qry = "select * from group_request where group_id = $group_id" . " and member_id = " . $user_id ;
    $isAlready = selectForJason($qry);
    
    if(count($isAlready) > 0)
    {
        $arr_result = array( RESULT => FAILED,
                                                                    MESSAGE =>	"request alredy sent",
                                                                    DATA => ""
                                                                    );
    
    }else{
        $update_query = "insert into group_request set group_id = $group_id , member_id = $user_id";

        $data1 = mysql_query($update_query);

        $qry = "select user_group.*, user.udid,user.name from user_group left join user on user_group.admin_id = user.user_id where group_id = $group_id";
        $groupAdminDetails = selectForJason($qry);

        $qry = "select * from user where user_id = $user_id";
        $userDetails = selectForJason($qry);

        $msg = $userDetails[0]["name"] . " want to join your group" .$groupAdminDetails[0]["group_name"] ;

        $sendNoti = new sendNotification();
        
//        print_r($groupAdminDetails);

        if($groupAdminDetails[0]['udid'] != "")
        {
            $sendNoti->sendNotificationByApns($groupAdminDetails[0]['udid'],$msg , "groupRequest", $group_id);
//            exit;
        }
//        exit;

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
    }
} elseif($action == "getGroupRequest")
{
    $group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";
    $query = "SELECT *  FROM group_request left join user on group_request.member_id = user.user_id WHERE group_id = '".$group_id ."'";
    $request = selectForJason($query);
    if($request)
    {
        $arr_result = array( RESULT => SUCCESS,
								MESSAGE =>	"request list successfully",
								DATA => $request
								);
    }else{
        $arr_result = array( RESULT => FAILED,
								MESSAGE =>	"something went wrong",
								DATA => ""
								);
    }
}elseif($action == "rejectRequest")
{
    $group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] :  "";
    $update_query = "delete from group_request WHERE group_id = ".$group_id . " and member_id = " . $user_id ;
//    print_r($update_query);
//    exit;
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
}elseif($action == "acceptRequest")
{
    $group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] :  "";
    
    $query = "select * from user_group WHERE group_id = '".$group_id ."'";
    $grpDetail = selectForJason($query);
    $memIds = $grpDetail[0]["members_id"] . "|" . $user_id;
    $update_query = "update user_group set members_id = '" . $memIds . "'  WHERE group_id = ".$group_id ;
    
    $data1 = mysql_query($update_query);
    
    
    $update_query = "delete from group_request WHERE group_id = ".$group_id . " and member_id = " . $user_id ;
    
    $data1 = mysql_query($update_query);
    
    // send Notification
    
    $qry = "select user_group.*, user.udid,user.name from user_group left join user on user_group.admin_id = user.user_id where group_id = $group_id";
    $groupAdminDetails = selectForJason($qry);
    
    $qry = "select * from user where user_id = $user_id";
    $userDetails = selectForJason($qry);
    
    $msg = $groupAdminDetails[0]["name"] . " has been accepted your group request" ;
    
    $sendNoti = new sendNotification();
    if($userDetails[0]['udid'] != "")
    {
        $sendNoti->sendNotificationByApns($userDetails[0]['udid'],$msg , "groupRequestAccept", $group_id);
    }
    
    
    if($data1)
    {
        $arr_result = array( RESULT => SUCCESS,
								MESSAGE =>	"accept request successfully",
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

