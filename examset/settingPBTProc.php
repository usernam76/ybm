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
	@ 최상운 2018.09.18
	> PBT 회차별 고사장세팅 PROC
	*/

	switch($proc){
		
		case "write" : 

		/*

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행


			if( $result[0][result] != '0' ){	//성공일때 메일 발송
				fnShowAlertMsg("등록 되었습니다.", "location.href = '/examset/examSetDef.php?centerCate=".$pCenterCate."';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}

*/

		break;


		/* 고사장 개별 수정 */
		case 'getModifyCenterAjax':

			$pIUD = "U";
			$pArray = null;
			$sql = 'EXEC p_exam_center_PBT_IUD :IUD, :Exam_code, :center_codes, :SB_exam_regi_types, :room_counts, :room_seats, :memos';
			$pArray[':IUD']									= $pIUD;
			$pArray[':Exam_code']						= $pExamCode;
			$pArray[':center_codes']					= $pCenterCode;
			$pArray[':SB_exam_regi_types']	= $pSBExamRegiType;
			$pArray[':room_counts']					= $pRoomCount;
			$pArray[':room_seats']						= $pRoomSeat;
			$pArray[':memos']								= $pMemo;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if(in_array( $pExamCode, $result[0])){
				$returnData = array("status"=>"success");
				echo json_encode($returnData);
			}
			exit;

		break;

		/* 고사장 세팅 개별 삭제 */
		case "getDeleteCenterAjax" :

			$pIUD = "D";
			$pArray = null;
			$sql = 'EXEC p_exam_center_PBT_IUD :IUD, :Exam_code, :center_codes, :SB_exam_regi_types, :room_counts, :room_seats, :memos';
			$pArray[':IUD']									= $pIUD;
			$pArray[':Exam_code']						= $pExamCode;
			$pArray[':center_codes']					= $pCenterCode;
			$pArray[':SB_exam_regi_types']	= $pSBExamRegiType;
			$pArray[':room_counts']					= $pRoomCount;
			$pArray[':room_seats']						= $pRoomSeat;
			$pArray[':memos']								= $pMemo;


			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if(in_array( $pExamCode, $result[0])){
				$returnData = array("status"=>"success");
				echo json_encode($returnData);
			}
			exit;


		break;

		/* 고사장 세팅 준비 완료 */
		case "getFinalReadyAjax" : 

			$pIUD = "I";
			$pArray = null;
			$sql = 'EXEC p_Exam_fin_CHK :fin_CHK, :Exam_code';
			$pArray[':fin_CHK']									= "O";
			$pArray[':Exam_code']						= $pExamCode;
			
			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			$returnData = array("status"=>"success");
			echo json_encode($returnData);

		break;


		/* 고사장 세팅 초기화 */
		case "getInitAjax" : 

			$pIUD = "D";
			$pArray = null;
			$sql = 'EXEC p_Exam_center_ID :IUD, :Exam_code, :center_codes, :SB_exam_regi_type, :use_CHK, :AdmType, :Adm_id, :memo';
			$pArray[':IUD']									= $pIUD;
			$pArray[':Exam_code']						= $pExamCode;
			$pArray[':center_codes']					= null;
			$pArray[':SB_exam_regi_type']		= null;
			$pArray[':use_CHK']							= null;
			$pArray[':AdmType']							= null;
			$pArray[':Adm_id']							= null;
			$pArray[':memo']								= null;
			
			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			$returnData = array("status"=>"success");
			echo json_encode($returnData);

		break;

		/* 고사장 추가 팝업 */
		case 'addCenterAjax':

			$pCenterCodes = implode("#", $pCenterCodes);
			$pSBExamRegiType = "일반";
			$pUseChk = "-";
			$pAdmType = "T";
			$pAdmId = "csw";
			$memo = null;

			$pIUD = "I";
			$pArray = null;
			$sql = 'EXEC p_Exam_center_ID :IUD, :Exam_code, :center_codes, :SB_exam_regi_type, :use_CHK, :AdmType, :Adm_id, :memo';

			$pArray[':IUD']									= $pIUD;
			$pArray[':Exam_code']						= $pExamCode;
			$pArray[':center_codes']					= $pCenterCodes;
			$pArray[':SB_exam_regi_type']		= $pSBExamRegiType;
			$pArray[':use_CHK']							= $pUseChk;
			$pArray[':AdmType']							= $pAdmType;
			$pArray[':Adm_id']							= $pAdmId;
			$pArray[':memo']								= $memo;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			$returnData = array("status"=>"success");
			echo json_encode($returnData);

		break;
		/* 고사장 추가 팝업 끝*/


		default:
		break;
	}
?>