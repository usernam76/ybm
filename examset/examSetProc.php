<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$proc = fnNoInjection($_REQUEST['proc']);	
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);
	
	/*

	@ 최상운 2018.09.03

	pLinkCenterCode --- 고사장이랑 연계되는 고유코드
	> DB 명세서 에는 본사 전산실에서 가져오는 코드라 적혀있음
	> 기획자 진경란 과장은 자동생성되는 코드라고 함.
	> 그냥 max값에 +1하면 되는거면 굳이 작성폼에서 보여줄 필요가 있나?
	> 그리고 테이블에 지도 URL 칼럼 없음(프로시저포함)
	*/

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