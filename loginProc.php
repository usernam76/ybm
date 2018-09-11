<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	session_start();

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성

	$proc = fnNoInjection($_REQUEST['proc']);	

	switch($proc){
		case 'login':

			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$sql = " SELECT ";
			$sql .= "	Adm_name, CONVERT(NVARCHAR, Password) AS admPw, AdmType, Login_day, Password_day, pass_CHK_count, AI.use_CHK, ADI.Dept_Name ";
			$sql .= " FROM Adm_info AI ";
			$sql .= " JOIN Adm_Dept_Info AS ADI (nolock) ON AI.Dept_Code = ADI.Dept_Code ";
			$sql .= " WHERE Adm_id = :admId ";

			$pArray[':admId'] = $pAdmId;

			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

			if( count($arrRows) == 0 ){
				fnShowAlertMsg("아이디나 비밀번호를 잘못 입력하셨습니다.", "history.back();", true);
			}else{
				if( $arrRows[0][use_CHK] == "X" ){			//사용이 제한 된 사용자
					fnShowAlertMsg("사용이 제한 되어 로그인이 불가능합니다.", "history.back();", true);					
				}

				$nowtime	= strtotime(date("Y-m-d H:i:s"));
				$detailtime	= $_SESSION["admPwFail"];
				$cha = $nowtime - $detailtime;

				if( $arrRows[0][pass_CHK_count] >= 5 && date('Hi', $cha) > 5 ){		//실패 횟수 5번, 로그인 시도 
					fnShowAlertMsg("5회 이상 잘못 입력시 5분간 로그인이 불가능합니다.", "history.back();", true);					
				}
				if( $pAdmPw != $arrRows[0][admPw] ){
					//로그인 실패 횟수 추가
					$sql = "EXEC p_Adm_info_IU 'F', :admId, :pwd, :deptCode, :admName, :admEmail, :etc, :tokenCode, :admIp, :useChk";	
					$pArray[':admId']		= $pAdmId;
					$pArray[':pwd']			= "";
					$pArray[':deptCode']	= "";
					$pArray[':admName']		= "";
					$pArray[':admEmail']	= "";
					$pArray[':etc']			= "";
					$pArray[':tokenCode']	= "";
					$pArray[':admIp']		= "";
					$pArray[':useChk']		= "";
					$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

					if( $arrRows[0][pass_CHK_count]+1 >= 5 ){
						$_SESSION["admPwFail"]	= strtotime(date("Y-m-d H:i:s"));
					}
					fnShowAlertMsg("아이디나 비밀번호를 잘못 입력하셨습니다.실패횟수 ".($arrRows[0][pass_CHK_count]+1)." ", "history.back();", true);
				}
				if( fnDateDiff( fnCalDate($data['Login_day'], 'month', 3), '') < 0 ){		//로그인 3개월 만료
					fnShowAlertMsg("계정이 만료 되어 로그인 불가능 합니다.", "history.back();", true);					
				}
				
				//로그인 성공
				$sql = "EXEC p_Adm_info_IU 'S', :admId, :pwd, :deptCode, :admName, :admEmail, :etc, :tokenCode, :admIp, :useChk";	
				$pArray[':admId']		= $pAdmId;
				$pArray[':pwd']			= "";
				$pArray[':deptCode']	= "";
				$pArray[':admName']		= "";
				$pArray[':admEmail']	= "";
				$pArray[':etc']			= "";
				$pArray[':tokenCode']	= "";
				$pArray[':admIp']		= "";
				$pArray[':useChk']		= "";
				$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

				//세션 생성
				$_SESSION["admId"]		= $pAdmId;
				$_SESSION["admNm"]		= $arrRows[0]['Adm_name'];
				$_SESSION["admType"]	= $arrRows[0]['AdmType'];
				$_SESSION["deptName"]	= $arrRows[0]['Dept_Name'];
				$_SESSION["admPwChk"]	= "Y";
				$_SESSION["admPwFail"]	= "";
				$_SESSION['LAST_ACTIVITY'] = time();

				if( fnDateDiff( fnCalDate($data['Password_day'], 'day', 90), '') < 0 ){		//비밀번호 변경
					$_SESSION["admPwChk"]	= "N";
					fnShowAlertMsg("", "location.href = '/password.php';", true);
				}
				
				fnShowAlertMsg("", "location.href = '/main.php';", true);
			}


			break;
		case 'password':

			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$sql = "EXEC p_Adm_info_IU 'P', :admId, :pwd, :deptCode, :admName, :admEmail, :etc, :tokenCode, :admIp, :useChk";	
			$pArray[':admId']		= $_SESSION["admId"];
			$pArray[':pwd']			= $pAdmPwNew;
			$pArray[':deptCode']	= "";
			$pArray[':admName']		= "";
			$pArray[':admEmail']	= "";
			$pArray[':etc']			= "";
			$pArray[':tokenCode']	= "";
			$pArray[':admIp']		= "";
			$pArray[':useChk']		= "";

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] == 1 ){
				$_SESSION["admPwChk"]	= "Y";

				fnShowAlertMsg("비밀번호 변경 되었습니다.", "location.href = '/main.php';", true);
			}else{
				fnShowAlertMsg("비밀번호 변경이 실패 되었습니다.".json_encode($result), "history.back();", true);
			}

			break;

		default:

			break;
			exit;
	}

?>