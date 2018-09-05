<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	
	session_start();
	
	if( $_SESSION["admId"] == "" ){
		fnShowAlertMsg("", "location.href = '/login.php';", true);
	}

	fnShowAlertMsg("", "location.href = '/main.php';", true);

?>