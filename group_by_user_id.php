<?php
include("config.php");

$query_set_value = " created_date=NOW()";

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "";
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";
$members_id = isset($_REQUEST['members_id']) ? $_REQUEST['members_id'] :  "";

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


if($action == "search" && $members_id != ''){

//    $query = "SELECT * FROM user_group WHERE members_id LIKE '%|$members_id|%' order by created_date desc";
    $query = "SELECT *, (select count(*) as tot from group_request where group_id = user_group.group_id) as totalNumOfRequest FROM user_group WHERE FIND_IN_SET($members_id, replace(members_id,'|',',')) > 0  order by created_date desc";
    $groups = selectForJason($query);
    foreach($groups as $key=>$value)
    {
        $mem_ids = str_replace("|", ",",$groups[$key]['members_id']);
        $mem_ids = rtrim($mem_ids, ',');
        $select_query = "SELECT * FROM `user` WHERE user_id in ($mem_ids)";
        $memDetails = selectForJason($select_query);
        $groups[$key]["memdetails"] = $memDetails;
    }
    if($groups)
    {
    $arr_result = array( RESULT => SUCCESS,
                    MESSAGE =>	"Group data",
                    DATA => $groups
                );
    }else{
        $arr_result = array( RESULT => FAILED,
                    MESSAGE =>	"No Group Found.",
                    DATA => ""
                    );
    }
//	 	$query = "SELECT * FROM user_group WHERE members_id LIKE '%|$members_id|%'";
//
//		$data = mysql_query($query);
//
//		$total_group = mysql_num_rows($data);
//		//$insert_id = 11;
//
//		if($total_group > 0 ){
//
//			$count = 0;
//			while($group_data = mysql_fetch_assoc($data)){
//
//				$query1 = "SELECT latitude,longitude FROM group_history WHERE group_id = '".$group_data['group_id']."'";
//
//				$group_h_data = mysql_query($query1);
//
//				$total_history = mysql_num_rows($group_h_data);
//
//				if($total_history > 0){
//					$history_data = mysql_fetch_assoc($group_h_data);
//					$result[$count]['group_id'] = $group_data['group_id'];
//					$result[$count]['admin_id'] = $group_data['admin_id'];
//					$result[$count]['members_id'] = $group_data['members_id'];
//					$result[$count]['group_name'] = $group_data['group_name'];
//					$result[$count]['group_pin'] = $group_data['group_pin'];
//					$result[$count]['group_image'] = $group_data['group_image'];
//					$result[$count]['latitude']	 = $history_data['latitude'];
//					$result[$count]['longitude']	 = $history_data['longitude'];
//                                        
//                                        $mem_ids = str_replace("|", ",",$group_data['members_id']);
//                                        $select_query = "SELECT * FROM `user` WHERE user_id in ($mem_ids)";
//                            //            print_r($select_query);
//                                        $memDetails = selectForJason($select_query);
//                                        $result[$count]["memdetails"] = $memDetails;
//
//					if($result[$count]['admin_id'] == $members_id){
//						$result[$count]['is_admin']	 = '1';
//					}else{
//						$result[$count]['is_admin']	 = '0';
//					}
//
//				}
//				$count++;
//			}
//
//			$arr_result = array( RESULT => SUCCESS,
//								MESSAGE =>	"Group data",
//								DATA => $result
//								);
//		}else{
//				$arr_result = array( RESULT => FAILED,
//									MESSAGE =>	"No Group Found.",
//									DATA => ""
//									);
//		}

}else{
	$arr_result = array( RESULT => FAILED,
						MESSAGE =>	"Plese Provide Data",
						DATA => ""
						);
}



echo json_encode($arr_result);


?>

