<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "207";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	$proc = fnNoInjection($_POST['proc']);	

	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	switch($proc){
		case 'write':
			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$pSbUseAreas		= "usr#usa";
			$pSbUseAreaDatas	= $pSbAreaDataUsr."#".$pSbAreaDataUsa;

			foreach(array_unique($pGoodsInfo) as $data) {
				$pSbUseAreas		.= "#pro";
				$pSbUseAreaDatas	.= "#".$data;
			}
			foreach(array_unique($pExamNums) as $data) {
				$pSbUseAreas		.= "#enm";
				$pSbUseAreaDatas	.= "#".$data;
			}

			$sql = "EXEC p_Coup_Info_IUD 'I', :coupCode, :sbCoupCate, :sbCoupType, :coupName, :usableStartday, :usableEndday, :coupInsertDay, :explain, :useCount, :coupCount, :userCoupCount, :freeChk, :applyId, :applyType, :docNum, :compName, :compMng, :sbUseAreas, :sbUseAreaDatas, :svcTypes, :svcs, :svcMaxs ";

			$pArray[':coupCode']			= "";
			$pArray[':sbCoupCate']			= $pSbCoupCate;
			$pArray[':sbCoupType']			= $pSbCoupType;
			$pArray[':coupName']			= $pCoupName;
			$pArray[':usableStartday']		= $pUsableStartday;
			$pArray[':usableEndday']		= $pUsableEndday;
			$pArray[':coupInsertDay']		= 0;
			$pArray[':explain']				= $pCoupName;
			$pArray[':useCount']			= 1;
			$pArray[':coupCount']			= $pCoupCount;
			$pArray[':userCoupCount']		= 1;
			$pArray[':freeChk']				= $pFreeChk;
			$pArray[':applyId']				= $_SESSION["admId"];
			$pArray[':applyType']			= $_SESSION["admType"];
			$pArray[':docNum']				= $pDocNum;
			$pArray[':compName']			= $pCompName;
			$pArray[':compMng']				= $pCompMng;

			$pArray[':sbUseAreas']			= $pSbUseAreas;
			$pArray[':sbUseAreaDatas']		= $pSbUseAreaDatas;

			$pArray[':svcTypes']			= $pSvcType;
			$pArray[':svcs']				= $pSvc;
			$pArray[':svcMaxs']				= -1;

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
			
			if( $result[0][result] >= 1 ){			
				fnShowAlertMsg("등록 되었습니다.", "location.href = '/language/vchrsList.php';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}

			break;
		case 'modify':
			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$pSbUseAreas		= "usr#usa";
			$pSbUseAreaDatas	= $pSbAreaDataUsr."#".$pSbAreaDataUsa;

			foreach(array_unique($pGoodsInfo) as $data) {
				$pSbUseAreas		.= "#pro";
				$pSbUseAreaDatas	.= "#".$data;
			}
			foreach(array_unique($pExamNums) as $data) {
				$pSbUseAreas		.= "#enm";
				$pSbUseAreaDatas	.= "#".$data;
			}
			
			$sql = "EXEC p_Coup_Info_IUD 'U', :coupCode, :sbCoupCate, :sbCoupType, :coupName, :usableStartday, :usableEndday, :coupInsertDay, :explain, :useCount, :coupCount, :userCoupCount, :freeChk, :applyId, :applyType, :docNum, :compName, :compMng, :sbUseAreas, :sbUseAreaDatas, :svcTypes, :svcs, :svcMaxs ";

			$pArray[':coupCode']			= $pCoupCode;
			$pArray[':sbCoupCate']			= $pSbCoupCate;
			$pArray[':sbCoupType']			= $pSbCoupType;
			$pArray[':coupName']			= $pCoupName;
			$pArray[':usableStartday']		= $pUsableStartday;
			$pArray[':usableEndday']		= $pUsableEndday;
			$pArray[':coupInsertDay']		= 0;
			$pArray[':explain']				= $pCoupName;
			$pArray[':useCount']			= 1;
			$pArray[':coupCount']			= $pCoupCount;
			$pArray[':userCoupCount']		= 1;
			$pArray[':freeChk']				= $pFreeChk;
			$pArray[':applyId']				= $_SESSION["admId"];
			$pArray[':applyType']			= $_SESSION["admType"];
			$pArray[':docNum']				= $pDocNum;
			$pArray[':compName']			= $pCompName;
			$pArray[':compMng']				= $pCompMng;

			$pArray[':sbUseAreas']			= $pSbUseAreas;
			$pArray[':sbUseAreaDatas']		= $pSbUseAreaDatas;

			$pArray[':svcTypes']			= $pSvcType;
			$pArray[':svcs']				= $pSvc;
			$pArray[':svcMaxs']				= -1;

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
			
			echo $result[0][result];
			/*
			if( $result[0][result] >= 1 ){			
				fnShowAlertMsg("수정 되었습니다.", "location.href = '/language/vchrsList.php';", true);
			}else{
				fnShowAlertMsg("수정 실패 되었습니다.", "history.back();", true);
			}
*/
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