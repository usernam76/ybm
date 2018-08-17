<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	//$valueValid = [];
	$valueValid = [
		'idx' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 3],
		'userId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 2, 'max' => 20]
	];
	
	
	$resultArray = fnGetRequestParam($valueValid);
	echo '[REQUEST 파라미터 배열]: ';
	print_r($resultArray);
	echo '<br>';

	$sql = 'EXEC p_Tag_IUD :userId, :tagName, \'\', :tagIdx, :IUD ';	
	$pArray[':userId'] = $pUserId;
	$pArray[':tagName'] = $pTagName;
	$pArray[':tagIdx'] = $pTagIdx;
	$pArray[':IUD'] = $pIUD;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
	echo '[쿼리 결과]: ';
	print_r($arrRows);
	echo '<br>';




