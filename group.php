<?php
ini_set('max_execution_time', 0);
include("config.php");

$query_set_value = " created_date=NOW()";

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  "";
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] :  "";
$admin_id = isset($_REQUEST['admin_id']) ? $_REQUEST['admin_id'] :  "";
$members_id = isset($_REQUEST['members_id']) ? $_REQUEST['members_id'] :  "";
$group_name = isset($_REQUEST['group_name']) ? $_REQUEST['group_name'] :  "";
$group_image = isset($_REQUEST['group_image']) ? $_REQUEST['group_image'] :  "";
$group_pin = isset($_REQUEST['group_pin']) ? $_REQUEST['group_pin'] :  "";
$isClosed = isset($_REQUEST['isClosed']) ? $_REQUEST['isClosed'] :  0;

if($_FILES['group_image']['name'] != ''){
	$tmp_name = $_FILES["group_image"]["tmp_name"];
	$namefile = $_FILES["group_image"]["name"];
//	$ext = end(explode(".", $namefile));
//	$group_image =time().".".$ext;
	$group_image =time().".png";
	$fileUpload = move_uploaded_file($tmp_name,"group_image/".$group_image);
}else{
	$group_image = $group_image;
}


if($admin_id != NULL){ $query_set_value .=", admin_id='$admin_id' "; }
if($members_id != NULL){ $query_set_value .=", members_id='$members_id' "; }
if($group_name != NULL){ $query_set_value .=", group_name='$group_name' "; }
if($group_image != NULL){ $query_set_value .=", group_image='$group_image' "; }
if($isClosed != NULL){ $query_set_value .=", isClosed='$isClosed' "; }


if($action == "add" && $admin_id != '' && $members_id != '' && $group_name != '' ){



	 	$query = "INSERT INTO user_group SET $query_set_value";

		mysql_query($query);

		$insert_id = mysql_insert_id();
		//$insert_id = 11;

		if($insert_id > 0 ){

			$no_of_member = explode('|',$members_id );
			$no_of_member = count($no_of_member);

			$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
//			$randstring = '';
//			for ($i = 0; $i < 5; $i++) {
//				$randstring .= $characters[rand(0, $charactersLength - 1)];
//			}
                        
                        $digits = 4;
                        $group_pin  = rand(pow(10, $digits-1), pow(10, $digits)-1);

//			$group_pin = $insert_id.'-'.$admin_id.'-'.$randstring;

			$update_query = "UPDATE user_group SET group_pin='$group_pin' WHERE group_id='$insert_id'";

			mysql_query($update_query);

			$select_query = "select * from user_group where group_id = $insert_id";

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

}else if($action == "edit" && $group_id != ''){

	$select_query = "select * from user_group where group_id = '$group_id'";

	$data = mysql_query($select_query);

	if(mysql_num_rows($data) > 0){

		$result = mysql_fetch_assoc($data);

		$update_query = "UPDATE user_group SET $query_set_value WHERE group_id = ".$result['group_id'];

		$data1 = mysql_query($update_query);

		$select_query = "select * from user_group where group_id = '$group_id'";

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

