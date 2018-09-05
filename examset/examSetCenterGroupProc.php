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
	> 사용제한이 칼럼이 없음
	*/

	switch($proc){
		case 'write':

			/* 그룹 센터 코드 구하기*/
			$pArray = null;
			$sql = " SELECT max(convert(INT, center_group_code))+1 as centerGroupCode FROM [theExam].[dbo].[Def_exam_center_Group]";
			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
			$pCenterGroupCode = sprintf("%03d",$arrRows[0]["centerGroupCode"]); // 3자리, 앞에 0채움

			/* 입력 프로시저 */
			$pAddress = $pAddress1." ".$pAddress2;
			$pMapUrl = $pMapUrl;	// 칼럼없음.

			$pIUD = "I";
			$pArray = null;
			$sql = 'EXEC p_Def_exam_center_Group_IUD :IUD, :centerGroupCode, :centerGroupName, :SBCenterGroup, :centerMap, :zipCode, :address, :useCHK, :BEP, :memo ';

			$pArray[':IUD']								= $pIUD;
			$pArray[':centerGroupCode']	= $pCenterGroupCode;
			$pArray[':centerGroupName']	= $pCenterGroupName;
			$pArray[':SBCenterGroup']			= $pSBCenterGroup;
			$pArray[':centerMap']					= $pCenterMap;
			$pArray[':zipCode']						= $pZipcode;
			$pArray[':address']						= $pAddress;
			$pArray[':useCHK']						= $pUseCHK;
			$pArray[':BEP']								= $pBEP;
			$pArray[':memo']							= $pMemo;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] == 1 ){	//성공일때 메일 발송
				fnShowAlertMsg("등록 되었습니다.", "location.href = '/examset/examSetCenterGroupList.php';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}

		break;
		case 'modify':

			/* 수정 프로시저 */
			$pAddress = $pAddress1." ".$pAddress2;
			$pMapUrl = $pMapUrl;	// 칼럼없음.

			$pIUD = "U";
			$pArray = null;
			$sql = 'EXEC p_Def_exam_center_Group_IUD :IUD, :centerGroupCode, :centerGroupName, :SBCenterGroup, :centerMap, :zipCode, :address, :useCHK, :BEP, :memo ';

			$pArray[':IUD']								= $pIUD;
			$pArray[':centerGroupCode']	= $pCenterGroupCode;
			$pArray[':centerGroupName']	= $pCenterGroupName;
			$pArray[':SBCenterGroup']			= $pSBCenterGroup;
			$pArray[':centerMap']					= $pCenterMap;
			$pArray[':zipCode']						= $pZipcode;
			$pArray[':address']						= $pAddress;
			$pArray[':useCHK']						= $pUseCHK;
			$pArray[':BEP']								= $pBEP;
			$pArray[':memo']							= $pMemo;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] != '0' ){	//성공일때 메일 발송
				fnShowAlertMsg("수정 되었습니다.", "location.href = '/examset/examSetCenterGroupList.php';", true);
			}else{
				fnShowAlertMsg("수정 실패 되었습니다.", "history.back();", true);
			}
			
		break;

		case "delete" :
			
			/* 삭제 프로시저 */
			$pAddress = $pAddress1." ".$pAddress2;
			$pMapUrl = $pMapUrl;	// 칼럼없음.

			$pIUD = "D";
			$pArray = null;
			$sql = 'EXEC p_Def_exam_center_Group_IUD :IUD, :centerGroupCode, :centerGroupName, :SBCenterGroup, :centerMap, :zipCode, :address, :useCHK, :BEP, :memo ';

			$pArray[':IUD']								= $pIUD;
			$pArray[':centerGroupCode']	= $pCenterGroupCode;
			$pArray[':centerGroupName']	= $pCenterGroupName;
			$pArray[':SBCenterGroup']			= $pSBCenterGroup;
			$pArray[':centerMap']					= $pCenterMap;
			$pArray[':zipCode']						= $pZipcode;
			$pArray[':address']						= $pAddress;
			$pArray[':useCHK']						= $pUseCHK;
			$pArray[':BEP']								= $pBEP;
			$pArray[':memo']							= $pMemo;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] != '0' ){	//성공일때 메일 발송
				fnShowAlertMsg("삭제 되었습니다.", "location.href = '/examset/examSetCenterGroupList.php';", true);
			}else{
				fnShowAlertMsg("삭제 실패 되었습니다.", "history.back();", true);
			}
		break;

		default:
		break;
		exit;
	}
?>