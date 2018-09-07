<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	$proc = fnNoInjection($_REQUEST['proc']);	

	switch($proc){
		case 'write':

			break;
		case 'modify':

			break;
		case 'delete':

			break;

		case 'app':
			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$sql = "EXEC p_Coup_Apply :coupCode, :okId, :okType, :okChk";	
			$pArray[':coupCode']	= $pCoupCode;
			$pArray[':okId']		= $_SESSION["admId"];
			$pArray[':okType']		= $_SESSION["admType"];
			$pArray[':okChk']		= "O";

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			$returnData = array("status"=>"success", "result"=>$result[0]['']);

			echo json_encode($returnData);

			break;

		case 'unApp':
			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$sql = "EXEC p_Coup_Apply :coupCode, :okId, :okType, :okChk";	
			$pArray[':coupCode']	= $pCoupCode;
			$pArray[':okId']		= $_SESSION["admId"];
			$pArray[':okType']		= $_SESSION["admType"];
			$pArray[':okChk']		= "X";

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			$returnData = array("status"=>"success", "result"=>$result[0]['']);

			echo json_encode($returnData);

			break;

		case 'getSampleListAjax':
			// javascript 에서 url : /sample/proc.php?proc=getSmapleListAjax 와 같이 호출 및 return 받아 사용
			echo json_encode(/*json형태로 리턴할 결과값*/);
			break;
		default:

			break;
			exit;
	}

?>