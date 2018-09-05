<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	session_start();		//세션시작
	session_unset();		// 현재 연결된 세션에 등록되어 있는 모든 변수의 값을 삭제한다
	session_destroy();		//현재의 세션을 종료한다

	fnShowAlertMsg("로그아웃 되었습니다.", "location.href = '/login.php';", true);
?>