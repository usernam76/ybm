<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	$proc = fnNoInjection($_REQUEST['proc']);	

	$valueValid = [];

	$resultArray = fnGetRequestParam($valueValid);
	
	/*
	@ 최상운 2018.09.03
	pLinkCenterCode --- 고사장이랑 연계되는 고유코드
	> DB 명세서 에는 본사 전산실에서 가져오는 코드라 데이터 연동이라고 적혀있음
	> 기획자 진경란 과장은 자동생성되는 코드라고 함.
	> 주소1과 주소2 > 테이블은 address 1칼럼, 수정에서는 분리해줘야 함.  분리안함
	*/

	switch($proc){
		case 'write':

			/* 입력 프로시저 */
			$pCenterCode = $pCenterCate."_".$pLinkCenterCode;
			$pZipcode = $pZipcode;	// zipcode
			$pSBArea = $pAreaLev2;	
			$pAddress = $pAddress1." ".$pAddress2;
			$pUseChk = "O";
			$pOkId = "csw";	// 테스트 아이디
			$pOkType = "T";	// 테스트의 T

			$pIUD = "I";
			$pArray = null;
			$sql = 'EXEC p_Def_exam_center_IUD :IUD, :centerCode, :centerCate, :linkCenterCode, :centerGroupCode, :SBArea, :centerName, :zipCode, :address, :mapURL, :memo, :useChk, :okId, :okType, :roomCount, :roomSeat, :STNCenterName, :STNCenterId, :STNUserName, :STNPassword, :PCCount, :certiPC, :usePC, :ETSCerti';

			$pArray[':IUD']							= $pIUD;
			$pArray[':centerCode']				= $pCenterCode;
			$pArray[':centerCate']				= $pCenterCate;
			$pArray[':linkCenterCode']		= $pLinkCenterCode;
			$pArray[':centerGroupCode']= $pCenterGroupCode;
			$pArray[':SBArea']						= $pSBArea;
			$pArray[':centerName']			= $pCenterName;
			$pArray[':zipCode']					= $pZipcode;
			$pArray[':address']					= $pAddress;
			$pArray[':mapURL']					= $pMapURL;
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
			$pArray[':certiPC']						= $pCertiPC;
			$pArray[':usePC']						= $pUsePC;
			$pArray[':ETSCerti']					= $pETSCerti;


			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행



			if( $result[0][result] != '0' ){	//성공일때 메일 발송
				fnShowAlertMsg("등록 되었습니다.", "location.href = '/examset/examSetDef.php?centerCate=".$pCenterCate."';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}


		break;
		case 'modify':

			/* 수정 프로시저 */
			$pZipcode = $pZipcode;	// zipcode
			$pSBArea = $pAreaLev2;	
			$pAddress = $pAddress1." ".$pAddress2;
			$pMapUrl = $pMapUrl;	// 칼럼없음.
			$pUseChk = "O";
			$pOkId = "csw";	// 테스트 아이디
			$pOkType = "T";	// 테스트의 T

			$pIUD = "U";
			$pArray = null;
			$sql = 'EXEC p_Def_exam_center_IUD :IUD, :centerCode, :centerCate, :linkCenterCode, :centerGroupCode, :SBArea, :centerName, :zipCode, :address, :mapURL, :memo, :useChk, :okId, :okType, :roomCount, :roomSeat, :STNCenterName, :STNCenterId, :STNUserName, :STNPassword, :PCCount, :certiPC, :usePC, :ETSCerti';

			$pArray[':IUD']							= $pIUD;
			$pArray[':centerCode']				= $pCenterCode;
			$pArray[':centerCate']				= $pCenterCate;
			$pArray[':linkCenterCode']		= $pLinkCenterCode;
			$pArray[':centerGroupCode']= $pCenterGroupCode;
			$pArray[':SBArea']						= $pSBArea;
			$pArray[':centerName']			= $pCenterName;
			$pArray[':zipCode']					= $pZipcode;
			$pArray[':address']					= $pAddress;
			$pArray[':mapURL']					= $pMapURL;
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
			$pArray[':certiPC']						= $pCertiPC;
			$pArray[':usePC']						= $pUsePC;
			$pArray[':ETSCerti']					= $pETSCerti;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] != '0' ){	
				fnShowAlertMsg("수정 되었습니다.", "location.href = '/examset/examSetDef.php?centerCate=".$pCenterCate."';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}
			
		break;

		default:
		break;
		exit;
	}
?>