<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';


	/*
	@ 시험세팅 > 시험일정관리 > 캘린더보기 데이터
	@ 시험일, 성적발표일, 접수시작일 3개의 데이터를 json 으로 리턴합니다.
	@ 180911 > 토익기준으로 개발
	*/
	$SBExamCate = "TOE";	// 토익시험을 기준으로 개발.

	$coulmn = "
		EI.[Exam_code]
		,EI.[SB_Exam_cate]
		,EI.[Exam_num]
		,EI.[Exam_Name]
		,EI.[Exam_day]
		,EI.[Score_day]
		,EI.[gen_regi_Start]
		,EI.[gen_regi_End] as gen_regi_End
		,EI.[spe_regi_Start] as spe_regi_Start
		,EI.[spe_regi_End] as spe_regi_End
		,GI.[SB_goods_type2]
		, (SELECT SB_name FROM [theExam].[dbo].[SB_info] WHERE SB_kind = 'exam_cate' AND SB_value=EI.SB_Exam_Cate) as examFullCate
	";

	$pArray = null;
	$sql = " SELECT ".$coulmn. " FROM ";
	$sql .= "  [theExam].[dbo].[Exam_Info] AS EI ";
	$sql .= "  LEFT OUTER JOIN ";
	$sql .= "  [theExam].[dbo].[Exam_Goods] AS EG ";
	$sql .= "  on EI.Exam_code = EG.Exam_code ";
	$sql .= "  INNER JOIN ";
	$sql .= "  [theExam].[dbo].[Goods_info] as GI ";
	$sql .= "  on GI.goods_code = EG.goods_code ";
	$sql .= " WHERE ";
	$sql .= "  EI.SB_Exam_Cate = :SBExamCate";
	$pArray[':SBExamCate']				= $SBExamCate;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

/*
시험일 : Exam_day
정기접수기간 : gen_regi_Start ~~ gen_regi_End
특별추가 접수기간 : "spe_regi_Start ~~ spe_regi_End",
성적발표일 : Score_day
*/

	$returnData = array();
	foreach($arrRows as $data){
		$examNum = $data["Exam_num"];				// 회차
		$examName = $data["Exam_Name"];			// 시험명
		$SBGoodsType2 = $data["SB_goods_type2"];	// 접수구분
		$examDay = $data["Exam_day"];			// 시험일
		$scoreDay = $data["Score_day"];			// 성적발표일
		$examFullCate = $data["examFullCate"];			// 시험명칭
		if($SBGoodsType2 == "EXR"){		// 정기접수
			$regiStart = $data["gen_regi_Start"];
			$regiEnd = $data["gen_regi_End"];
		}else if($SBGoodsType2 == "EXS"){		// 특별접수
			$regiStart = $data["spe_regi_Start"];
			$regiEnd = $data["spe_regi_End"];
		}
		array_push($returnData, array('title'=>$examFullCate." ".$examNum."회 시험일",'start'=>$examDay, 'color'=>'#F8b195'));
		array_push($returnData, array('title'=>$examFullCate." ".$examNum."회 성적 발표",'start'=>$scoreDay, 'color'=>'#99b898'));
		array_push($returnData, array('title'=>$examFullCate." ".$examNum."회 접수 시작",'start'=>$regiStart, 'color'=>'skyblue'));
		array_push($returnData, array('title'=>$examFullCate." ".$examNum."회 접수 종료",'start'=>$regiEnd, 'color'=>'skyblue'));
	}

	echo json_encode($returnData);

?>