<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$proc = fnNoInjection($_REQUEST['proc']);	
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	switch($proc){
		case 'write':

			/* 입력 프로시저 */
			$pCenterCode = "";
			$pLinkCenterName = "";
			$pZipcode = $pZipcode;
			$pSBArea = $pAreaLev2;
			$pAddress = $pAddress1." ".$pAddress2;
			$pMapUrl = $pMapUrl;
			$pUseChk = "O";
			$pOkId = "csw";
			$pOkType = "";
			$pSTNCenterName = "";
			$pSTNCenterId = "";
			$pSTNUserName = "";
			$pSTNPassword = "";
			$pPCCount = "";
			$pCertPc = "";
			$pUsePc = "";
			$pETSCert = "";

			$pIUD = "I";
			$pArray = null;
			$sql = 'EXEC p_Def_exam_center_IUD :IUD, :centerCode, :centerCate, :linkCenterCode, :centerGroupCode, :SBArea, :centerName, :zipCode, :address, :memo, :useChk, :okId, :okType, :roomCount, :roomSeat, :STNCenterName, :STNCenterId, :STNUserName, :STNPassword, :PCCount, :certPc, :usePc, :ETSCert';

			$pArray[':IUD']							= $pIUD;
			$pArray[':centerCode']				= $pCenterCode;
			$pArray[':centerCate']				= $pCenterCate;
			$pArray[':linkCenterCode']		= $pLinkCenterCode;
			$pArray[':centerGroupCode']= $pCenterGroupCode;
			$pArray[':SBArea']						= $pSBArea;
			$pArray[':centerName']			= $pCenterName;
			$pArray[':zipCode']					= $pZipcode;
			$pArray[':address']					= $pAddress;
			$pArray[':memo']						= $pMemo;
			$pArray[':useChk']						= $pUseChk;
			$pArray[':okId']							= $pOkId;
			$pArray[':okType']						= $pOkType;
			$pArray[':roomCount']				= $pRoomCount;
			$pArray[':roomSeat']				= $pRoomSeat;
			$pArray[':STNCenterName']	= $pSTNCenterName;
			$pArray[':STNCenterId']			= $pSTNCenterId;
			$pArray[':STNUserName']		= $pSTNUserName;
			$pArray[':STNPassword']			= $pSTNPassword;
			$pArray[':PCCount']					= $pPCCount;
			$pArray[':certPc']						= $pCertPc;
			$pArray[':usePc']						= $pUsePc;
			$pArray[':ETSCert']					= $pETSCert;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] == 1 ){	//성공일때 메일 발송
				fnShowAlertMsg("등록 되었습니다.", "location.href = '/examset/examSetDef.php';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}


		break;


		case 'getAreaLoadAjax':
			exit;

		break;
		default:
		break;
		exit;
	}
?>