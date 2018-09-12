<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "207";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	$proc = fnNoInjection($_REQUEST['proc']);	

	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	switch($proc){
		case 'write':

			break;
		case 'modify':

			break;
		case 'delete':

			break;

		case 'app':

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

			$sql = "EXEC p_Coup_Apply :coupCode, :okId, :okType, :okChk";	
			$pArray[':coupCode']	= $pCoupCode;
			$pArray[':okId']		= $_SESSION["admId"];
			$pArray[':okType']		= $_SESSION["admType"];
			$pArray[':okChk']		= "X";

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			$returnData = array("status"=>"success", "result"=>$result[0]['']);

			echo json_encode($returnData);

			break;

		case 'userAdd':

			$sql = "EXEC p_Coup_Member_I 'I', :coupCode, :coupNo, :userId, :memo";	
			$pArray[':coupCode']	= $pCoupCode;
			$pArray[':coupNo']		= $pCoupNo;
			$pArray[':userId']		= $pUserId;
			$pArray[':memo']		= $pMemo;

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			$returnData = array("status"=>"success", "result"=>$result[0][result]);

			echo json_encode($returnData);

			break;

		case 'issuedCancel':

			$sql = "EXEC p_Coup_Member_I 'D', :coupCode, :coupNo, :userId, :memo";	
			$pArray[':coupCode']	= $pCoupCode;
			$pArray[':coupNo']		= $pCoupNo;
			$pArray[':userId']		= $pUserId;
			$pArray[':memo']		= "";

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			$returnData = array("status"=>"success", "result"=>$result[0][result]);

			echo json_encode($returnData);

			break;


		default:

			break;
			exit;
	}

?>