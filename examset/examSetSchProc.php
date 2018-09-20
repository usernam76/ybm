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

			$pExamCode = $pSBExamCate."_".$pExamNum;
			$pExamDay = $pExamDay;
			$pScoreDay = $pScoreDayDay." ".$pScoreDayHours.":00";

			$pExamStartTime = $pExamStartTimeHours.":".$pExamStartTimeMin;		// 시험시간
			$pCheckInTime = $pCheckInTimeHours.":".$pCheckInTimeMin;					// 입실시간

			$pGenRegiStart = $pGenRegiStartDay." ".$pGenRegiStartHours.":00";
			$pGenRegiEnd = $pGenRegiEndDay." ".$pGenRegiEndHours.":00";
			$pSpeRegiStart =  $pSpeRegiStartDay." ".$pSpeRegiStartHours.":00";
			$pSpeRegiEnd =  $pSpeRegiEndDay." ".$pSpeRegiEndHours.":00";
			$pRefFirstStart =  $pRefFirstStartDay." ".$pRefFirstStartHours.":00";
			$pRefFirstEnd =  $pRefFirstEndDay." ".$pRefFirstEndHours.":00";

			$pRefSecStart =  $pRefSecondStartDay." ".$pRefSecondStartHours.":00";
			$pRefSecEnd =  $pRefSecondEndDay." ".$pRefSecondEndHours.":00";

			$pRegiExtEnd = $pRegiExtEndDay." ".$regiExtEndHours;
			$pScoreChangeStart = $pScoreChangeDay;
			$pScoreChangeEnd = $pScoreChangeDay;

			if($pGoodsCodeSpe != ""){
				$pGoodsCode = $pGoodsCode."#".$pGoodsCodeSpe;
				$pSellStart =$pGenRegiStart."#".$pSpeRegiStart;
				$pSellEnd = $pGenRegiEnd."#".$pSpeRegiEnd;
			}
			else{
				$pSellStart =$pGenRegiStart;
				$pSellEnd = $pGenRegiEnd;
			}

			/*
			임시입력
			*/
			$pExamName = null;
			$pConfType = "없음";
			$pOkId = "csw";
			$pOkType = "T";

			/* 입력 프로시저 실행*/
			$pIUD = "I";
			$pArray = null;
			$sql = "EXEC p_Exam_info_IUD
:IUD,:Exam_code,:SB_Exam_cate,:Exam_num,:Exam_Name,:Exam_day,:Score_day,:Exam_start_time,:check_in_time,:gen_regi_Start,:gen_regi_End,:spe_regi_Start,:spe_regi_End,:ref_first_start,:ref_first_end,:ref_sec_start,:ref_sec_end,:regi_ext_end,:score_change_start,:score_change_end,:conf_type,:ok_id,:okType,:Exam_Goods_Code,:goods_codes,:Group_code,:sell_start,:sell_end";
			$pArray[':IUD']									= $pIUD;
			$pArray[':Exam_code']						= $pExamCode;
			$pArray[':SB_Exam_cate']				= $pSBExamCate;
			$pArray[':Exam_num']						= $pExamNum;
			$pArray[':Exam_Name']					= $pExamName;
			$pArray[':Exam_day']						= $pExamDay;
			$pArray[':Score_day']						= $pScoreDay;
			$pArray[':Exam_start_time']			= $pExamStartTime;
			$pArray[':check_in_time']				= $pCheckInTime;
			$pArray[':gen_regi_Start']				= $pGenRegiStart;
			$pArray[':gen_regi_End']				= $pGenRegiEnd;
			$pArray[':spe_regi_Start']				= $pSpeRegiStart;
			$pArray[':spe_regi_End']					= $pSpeRegiEnd;
			$pArray[':ref_first_start']					= $pRefFirstStart;
			$pArray[':ref_first_end']					= $pRefFirstEnd;
			$pArray[':ref_sec_start']					= $pRefSecStart;
			$pArray[':ref_sec_end']					= $pRefSecEnd;
			$pArray[':regi_ext_end']					= $pRegiExtEnd;
			$pArray[':score_change_start']		= $pScoreChangeStart;
			$pArray[':score_change_end']		= $pScoreChangeEnd;
			$pArray[':conf_type']						= $pConfType;
			$pArray[':ok_id']								= $pOkId;
			$pArray[':okType']								= $pOkType;
			$pArray[':Exam_Goods_Code']		= $pExamGoodsCode;
			$pArray[':goods_codes']					= $pGoodsCode;
			$pArray[':Group_code']					= $pGroupCode;
			$pArray[':sell_start']							= $pSellStart;
			$pArray[':sell_end']							= $pSellEnd;


			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if(in_array($pExamCode, $result[0])) {
				fnShowAlertMsg("등록 되었습니다.", "location.href = '/examset/examSetSchList.php';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}
			exit;
		break;
		case 'modify':

			$pExamCode = $pSBExamCate."_".$pExamNum;
			$pExamDay = $pExamDay;
			$pScoreDay = $pScoreDayDay." ".$pScoreDayHours.":00";

			$pExamStartTime = $pExamStartTimeHours.":".$pExamStartTimeMin;		// 시험시간
			$pCheckInTime = $pCheckInTimeHours.":".$pCheckInTimeMin;					// 입실시간

			$pGenRegiStart = $pGenRegiStartDay." ".$pGenRegiStartHours.":00";
			$pGenRegiEnd = $pGenRegiEndDay." ".$pGenRegiEndHours.":00";
			$pSpeRegiStart =  $pSpeRegiStartDay." ".$pSpeRegiStartHours.":00";
			$pSpeRegiEnd =  $pSpeRegiEndDay." ".$pSpeRegiEndHours.":00";
			$pRefFirstStart =  $pRefFirstStartDay." ".$pRefFirstStartHours.":00";
			$pRefFirstEnd =  $pRefFirstEndDay." ".$pRefFirstEndHours.":00";

			$pRefSecStart =  $pRefSecondStartDay." ".$pRefSecondStartHours.":00";
			$pRefSecEnd =  $pRefSecondEndDay." ".$pRefSecondEndHours.":00";

			$pRegiExtEnd = $pRegiExtEndDay." ".$regiExtEndHours;
			$pScoreChangeStart = $pScoreChangeDay;
			$pScoreChangeEnd = $pScoreChangeDay;

			if($pGoodsCodeSpe != ""){
				$pGoodsCode = $pGoodsCode."#".$pGoodsCodeSpe;
				$pSellStart =$pGenRegiStart."#".$pSpeRegiStart;
				$pSellEnd = $pGenRegiEnd."#".$pSpeRegiEnd;
			}
			else{
				$pSellStart =$pGenRegiStart;
				$pSellEnd = $pGenRegiEnd;
			}

			/*
			임시입력
			*/
			$pExamName = null;
			$pConfType = "없음";
			$pOkId = "csw";
			$pOkType = "T";

			/* 입력 프로시저 실행*/
			$pIUD = "U";
			$pArray = null;
			$sql = "EXEC p_Exam_info_IUD
:IUD,:Exam_code,:SB_Exam_cate,:Exam_num,:Exam_Name,:Exam_day,:Score_day,:Exam_start_time,:check_in_time,:gen_regi_Start,:gen_regi_End,:spe_regi_Start,:spe_regi_End,:ref_first_start,:ref_first_end,:ref_sec_start,:ref_sec_end,:regi_ext_end,:score_change_start,:score_change_end,:conf_type,:ok_id,:okType,:Exam_Goods_Code,:goods_codes,:Group_code,:sell_start,:sell_end";
			$pArray[':IUD']									= $pIUD;
			$pArray[':Exam_code']						= $pExamCode;
			$pArray[':SB_Exam_cate']				= $pSBExamCate;
			$pArray[':Exam_num']						= $pExamNum;
			$pArray[':Exam_Name']					= $pExamName;
			$pArray[':Exam_day']						= $pExamDay;
			$pArray[':Score_day']						= $pScoreDay;
			$pArray[':Exam_start_time']			= $pExamStartTime;
			$pArray[':check_in_time']				= $pCheckInTime;
			$pArray[':gen_regi_Start']				= $pGenRegiStart;
			$pArray[':gen_regi_End']				= $pGenRegiEnd;
			$pArray[':spe_regi_Start']				= $pSpeRegiStart;
			$pArray[':spe_regi_End']					= $pSpeRegiEnd;
			$pArray[':ref_first_start']					= $pRefFirstStart;
			$pArray[':ref_first_end']					= $pRefFirstEnd;
			$pArray[':ref_sec_start']					= $pRefSecStart;
			$pArray[':ref_sec_end']					= $pRefSecEnd;
			$pArray[':regi_ext_end']					= $pRegiExtEnd;
			$pArray[':score_change_start']		= $pScoreChangeStart;
			$pArray[':score_change_end']		= $pScoreChangeEnd;
			$pArray[':conf_type']						= $pConfType;
			$pArray[':ok_id']								= $pOkId;
			$pArray[':okType']								= $pOkType;
			$pArray[':Exam_Goods_Code']		= $pExamGoodsCode;
			$pArray[':goods_codes']					= $pGoodsCode;
			$pArray[':Group_code']					= $pGroupCode;
			$pArray[':sell_start']							= $pSellStart;
			$pArray[':sell_end']							= $pSellEnd;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행


			if(in_array($pExamCode, $result[0])) {
				fnShowAlertMsg("수정 되었습니다.", "location.href = '/examset/examSetSchWrite.php?proc=modify&examCode=".$pExamCode."';", true);
			}else{
				fnShowAlertMsg("수정 실패 되었습니다.", "history.back();", true);
			}
			exit;


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