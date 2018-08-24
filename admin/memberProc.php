<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$proc = fnNoInjection($_REQUEST['proc']);	

	switch($proc){
		case 'write':

			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$pwdStr  = '!@#$%*&abcdefghijklmnpqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ23456789';
			$pwd = substr(str_shuffle($pwdStr), 0, 12);

			$sql = "EXEC p_Adm_info_IUD 'I', :admId, :pwd, :deptCode, :admName, :admEmail, :etc, :tokenCode, :admIp, :useChk";	
			$pArray[':admId']		= $pAdmId;
			$pArray[':pwd']			= $pwd;
			$pArray[':deptCode']	= $pDeptCode;
			$pArray[':admName']		= $pAdmName;
			$pArray[':admEmail']	= $pAdmEmail1."@".$pAdmEmail2;
			$pArray[':etc']			= "etc";
			$pArray[':tokenCode']	= $pTokenCode;
			$pArray[':admIp']		= $pAdmIp;
			$pArray[':useChk']		= $pUseChk;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
			
			if( $result[0][result] == 1 ){
				fnShowAlertMsg("등록 되었습니다.", "location.href = './memberList.php';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}

			break;
		case 'modify':

			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$sql = "EXEC p_Adm_info_IUD 'U', :admId, :pwd, :deptCode, :admName, :admEmail, :etc, :tokenCode, :admIp, :useChk";	
			$pArray[':admId']		= $pAdmId;
			$pArray[':pwd']			= "";
			$pArray[':deptCode']	= $pDeptCode;
			$pArray[':admName']		= $pAdmName;
			$pArray[':admEmail']	= $pAdmEmail1."@".$pAdmEmail2;
			$pArray[':etc']			= $pEtc;
			$pArray[':tokenCode']	= $pTokenCode;
			$pArray[':admIp']		= $pAdmIp;
			$pArray[':useChk']		= $pUseChk;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] == 1 ){
				fnShowAlertMsg("수정 되었습니다.", "location.href = './memberList.php';", true);
			}else{
				fnShowAlertMsg("수정 실패 되었습니다.".json_encode($result), "history.back();", true);
			}

			break;
		case 'delete':


			break;
		case 'idCheck':
			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$sql = " SELECT ";
			$sql .= "	COUNT(*) AS cnt ";
			$sql .= " FROM [theExam].[dbo].[Adm_info] ";
			$sql .= " WHERE Adm_id = :admId ";

			$pArray[':admId'] = $pAdmId;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

			$returnData = array("status"=>"success", "data"=>$arrRows);
			echo json_encode($returnData);

			break;
		case 'passClear':
			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$pwdStr  = '!@#$%*&abcdefghijklmnpqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ23456789';
			$pwd = substr(str_shuffle($pwdStr), 0, 12);

			$sql = "EXEC p_Adm_info_IUD 'P', :admId, :pwd, :deptCode, :admName, :admEmail, :etc, :tokenCode, :admIp, :useChk";	
			$pArray[':admId']		= $pAdmId;
			$pArray[':pwd']			= $pwd;
			$pArray[':deptCode']	= "";
			$pArray[':admName']		= "";
			$pArray[':admEmail']	= "";
			$pArray[':etc']			= "";
			$pArray[':tokenCode']	= "";
			$pArray[':admIp']		= "";
			$pArray[':useChk']		= "";

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			$returnData = array("status"=>"success", "result"=>$result);
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