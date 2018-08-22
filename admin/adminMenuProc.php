<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$arrayValid = fnGetRequestParam();
//	foreach($arrayValid as $k=>$v){ ${$k} =$v;}
	$proc = $arrayValid["proc"];
	switch($proc){
		case 'write':
			$returnData = array("status"=>"success");
			echo json_encode($returnData);
		break;
		case 'modify':
			$returnData = array("status"=>"success");
			echo json_encode($returnData);
		break;

		case 'getMenuLoadAjax':
			$findMenuDepth = $arrayValid["menuDepth"] + 1;
			$findParMenuIdx = $arrayValid["menuIdx"];


			$sql = ' SELECT ';
			$sql .= ' [Menu_idx],[Menu_Name],[Menu_order],[Menu_depth],[Par_Menu_idx] ';
			$sql .= ' FROM [theExam].[dbo].[Menu_Info] ';
			$sql .= ' WHERE Menu_depth = \''.$findMenuDepth.'\'';
			$sql .= ' and Par_Menu_idx = \''.$findParMenuIdx.'\'';
			$sql .= ' ORDER BY Menu_order asc ';

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

			$returnData = array("status"=>"success","depth"=>$findMenuDepth, "data"=>$arrRows);
			echo json_encode($returnData);
		break;
		default:

			break;
			exit;
	}
?>