<?php
include("config.php");

$fb_id = isset($_REQUEST['fb_id']) ? $_REQUEST['fb_id'] :  "''";
//$email = isset($_REQUEST['email']) ? $_REQUEST['email'] :  "''";
$mobNum = isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] :  "''";

if(isset($_REQUEST['fb_id'])){
        $fb_id = ($fb_id != "") ? $fb_id : "''";
        $mobNum = ($mobNum != "") ? $mobNum : "''";
	$query = "select * from user where fb_id in ($fb_id) or mobile in ($mobNum)";

	$data = mysql_query($query);
        
//        print_r($query);
//        exit;

	$total_id = mysql_num_rows($data);

	if($total_id > 0 ){
            

//		$result = mysql_fetch_array($data);
//		
//		$data = array(
//						"member_id" => $result['user_id'],
//						"name" => $result['name'],
//						"image" => $result['image'],
//						"email" => $result['email'],
//						"mobile" => $result['mobile'],
//						"latitude" => $result['latitude'],
//						"longitude" => $result['longitude'],			
//					);
             $count = 0;
            $datar = array();
            while ($row = mysql_fetch_assoc($data)) {
                foreach ($row as $key => $value) {
                    if (!is_array($value)) {
                        $row[$key] = htmlspecialchars_decode($value, ENT_QUOTES);
                    }
                }
                $datar[$count] = $row;
                $count++;
            }

		$arr_result = array( RESULT => SUCCESS,
							MESSAGE =>	"Get Data Sucessfully",
							DATA => $datar
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

