<?php
if($_SERVER['SERVER_NAME'] == 'localhost'){
	define("DB_HOST", "localhost");
	define("DB_DATABASE", "group_tap");
	define("DB_USER", "root");
	define("DB_PASSWORD", "anil@123.com");
	define("IMAGE_URL", "http://localhost/group_tap/img/");
	define("GROUP_IMAGE_URL", "http://localhost/group_tap/group_image/");

}else{
	define("DB_HOST", "localhost");
	define("DB_DATABASE", "group_tap");
	define("DB_USER", "root");
	define("DB_PASSWORD", "anil@123.com");
	define("IMAGE_URL", "http://demo.revolutioninfotech.co/group_tap/img/");
	define("GROUP_IMAGE_URL", "http://demo.revolutioninfotech.co/group_tap/group_image/");
}

$db = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die( "can not connect to server : ". mysql_error());
mysql_select_db(DB_DATABASE, $db) or die(" can not connect to database :  ". mysql_error());


define("SUCCESS", "success");
define("FAILED", "failed");

define("RESULT", "result");
define("MESSAGE", "message");
define("DATA", "data");
?>