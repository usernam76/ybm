<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	$proc = fnNoInjection($_REQUEST['proc']);	

	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);




	switch($proc){
		case 'write':

/*
examStartTimeHours--->03
examStartTimeMin--->30
checkInTimeHours--->06
checkInTimeMin--->40
genRegiStartDay--->2018-09-13
genRegiStartHours--->03
genRegiEndDay--->2018-09-12
genRegiEndHours--->10
speRegiStartDay--->2018-09-11
speRegiStartHours--->06
speRegiEndDay--->2018-09-27
speRegiEndHours--->14
regiExtEndDay--->2018-09-20
regiExtEndHours--->04
scoreDayDay--->2018-09-20
scoreDayHours--->15
refFirstStartDay--->2018-09-26
refFirstStartHours--->15
refFirstEndDay--->2018-09-27
refFirstEndHours--->18
refSecondStartDay--->2018-09-12
refSecondStartHours--->17
refSecondEndDay--->2018-09-28
refSecondEndHours--->14
*/
			$pExamName = "테스트 시험";
			$pExamDay = $pExamDay;
			$pScoreDay = $pScoreDay;

			$pExamStartTime = $pEamStartTimeHours.":".$pEamStartTimeMin;		// 시험시간
			$pCheckInTime = $pCheckInTimeHours.":".$pCheckInTimeMin;					// 입실시간

			$pGenRegiStart = $pGenRegiStartDay." ".$pGenRegiStartHours.":00:00";
			$pGenRegiEnd = $pGenEndStartDay." ".$pGenRegiEndHours.":00:00";
			$pSpeRegiStart =  $pSpeRegiStartDay." ".$pSpeRegiStartHours.":00:00";
			$pSpeRegiEnd =  $pSpeRegiEndDay." ".$pSpeRegiEndHours.":00:00";
			$pRefFirstStart =  $pRefFirstStartDay." ".$pRefFirstStartHours.":00:00";
			$pRefFirstEnd =  $pRefFirstEndDay." ".$pRefFirstEndHours.":00:00";

			$pRefSecStart =  $pRefSecondStartDay." ".$pRefSecondStartHours.":00:00";
			$pRefSecEnd =  $pRefSecondEndDay." ".$pRefSecondEndHours.":00:00";

			$pOkId = "csw";
			$pOkType = "T";

			/* 입력 프로시저 실행*/
			$pIUD = "I";
			$pArray = null;
			$sql = "EXEC [theExam].[dbo].[p_Exam_info_IUD] :IUD,:ExamCode,:SBExamCate,:ExamNumint,:ExamName,:ExamDay,:ScoreDay,:ExamStartTime,:checkInTime,:genRegiStart,:genRegiEnd,:speRegiStart,:speRegiEnd,:refFirstStart,:refFirstEnd,:refSecStart,:refSecEnd,:regiExtEnd,:scoreChangeStart,:scoreChangeEnd,:confType,:okId,:okType,:ExamGoodsCode,:goodsCodes,:GroupCode,:sellStart,:sellEnd";

			$pArray[':IUD']								= $pIUD;
			$pArray[':ExamCode']					= $pExamCode;
			$pArray[':SBExamCate']				= $pSBExamCate;
			$pArray[':ExamNum']					= $pExamNum;
			$pArray[':ExamName']					= $pExamName;
			$pArray[':ExamDay']						= $pExamDay;
			$pArray[':ScoreDay']						= $pScoreDay;
			$pArray[':ExamStartTime']			= $pExamStartTime;
			$pArray[':checkInTime']				= $pCheckInTime;
			$pArray[':genRegiStart']				= $pGenRegiStart;
			$pArray[':genRegiEnd']				= $pGenRegiEnd;
			$pArray[':speRegiStart']				= $pSpeRegiStart;
			$pArray[':speRegiEnd']					= $pSpeRegiEnd;
			$pArray[':refFirstStart']					= $pRefFirstStart;
			$pArray[':refFirstEnd']					= $pRefFirstEnd;
			$pArray[':refSecStart']					= $pRefSecStart;
			$pArray[':refSecEnd']					= $pRefSecEnd;
			$pArray[':regiExtEnd']					= $pRegiExtEnd;
			$pArray[':scoreChangeStart']		= $pScoreChangeStart;
			$pArray[':scoreChangeEnd']		= $pScoreChangeEnd;
			$pArray[':confType']						= $pConfType;
			$pArray[':okId']								= $pOkId;
			$pArray[':okType']							= $pOkType;
			$pArray[':ExamGoodsCode']		= $pExamGoodsCode;
			$pArray[':goodsCodes']				= $pGoodsCodes;
			$pArray[':GroupCode']				= $pGroupCode;
			$pArray[':sellStart']						= $pSellStart;
			$pArray[':sellEnd']							= $pSellEnd;


			foreach($pArray as $k=>$v){
				echo $k."--->".$v."<br />";
			}

			exit;
		break;
		case 'modify':
		break;
		case "delete" :
		break;


		/* @ 회차 중복 체크 */
		case "examCheck" : 
			$sql  = " SELECT COUNT(*) AS cnt  FROM [theExam].[dbo].[Exam_Info] ";
			$sql .= " WHERE Exam_num = :examNum";
			$pArray[':examNum'] = $pExamNum;
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
			$returnData = array("status"=>"success", "data"=>$arrRows);
			echo json_encode($returnData);
		break;
		/* @ 회차 중복 체크 끝*/

		case "getGroupInCenterAjax" :
		break;
		default:
		break;
		exit;
	}
?>