<?php

include("config.php");

$timeZone = isset($_REQUEST['timeZone']) ? $_REQUEST['timeZone'] :  "Asia/Kolkata";
date_default_timezone_set($timeZone);

//date_default_timezone_set('Asia/Kolkata');
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

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$member_id = isset($_REQUEST['member_id']) ? $_REQUEST['member_id'] : "";



if ($member_id != '') {
    
    $select_query = "SELECT tap.*,
        (select count(*) as tot from tap_timing where tap_id = tap.tap_id and status != 0 and status != 2 ) as memberResponse
        , user_group.members_id,user_group.group_name,user_group.group_image,user_group.isClosed FROM tap
        left join user_group on tap.`group_id` = user_group.group_id 
        WHERE DATE(tap.created_date) = '" . date('Y-m-d'). "' AND (`tap_time` 
        BETWEEN TIME( DATE_SUB( '". date('Y-m-d H:i:s') ."' , INTERVAL 15 
        MINUTE ) ) 
        AND TIME( '".date('Y-m-d H:i:s') ."' ) or `tap_time` >  TIME( '".date('Y-m-d H:i:s') ."' ) )  AND ( tap.admin_id = $member_id OR FIND_IN_SET($member_id , replace(user_group.members_id,'|',',')) > 0 ) order by tap_time "  ;
//        AND TIME( NOW( ) ) AND tap.admin_id = $member_id OR FIND_IN_SET($member_id , replace(user_group.members_id,'|',',')) > 0  order by created_date desc"  ;
//    print_r($select_query);exit;
    $data = mysql_query($select_query);
    $total_id = mysql_num_rows($data);

    if($total_id > 0 ){
        $count = 0;
        $tapdetails = selectForJason($select_query);
        foreach($tapdetails as $key=>$value)
        {
            $mem_ids = str_replace("|", ",",$tapdetails[$key]['members_id']);
            $select_query = "SELECT *, (select status from tap_timing where tap_id = " . $tapdetails[$key]["tap_id"] ." and member_id = user.user_id limit 1) as status, IF( COALESCE((select id from userlocationfortap where tap_id = " . $tapdetails[$key]["tap_id"] ." and user_id = user.user_id limit 1), FALSE ) >= 1,  TRUE,  FALSE ) as isUserAlreadySentLocation FROM `user` WHERE user_id in ($mem_ids)";
//            print_r($select_query);
            $memDetails = selectForJason($select_query);
            $tapdetails[$key]["memdetails"] = $memDetails;
            
        }
        $arr_result = array(RESULT => SUCCESS,
            MESSAGE => "Data listed Succesfully ",
            DATA => $tapdetails
        );
    }else{
        $arr_result = array(RESULT => SUCCESS,
        MESSAGE => "No Record Found",
        DATA => array()
    );
    }
    
} else {
    $arr_result = array(RESULT => SUCCESS,
        MESSAGE => "No Record Found",
        DATA => $result
    );
}


echo json_encode($arr_result);
?>

