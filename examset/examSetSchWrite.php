<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	$proc = fnNoInjection($_REQUEST['proc']);
	if(empty($proc)) $proc = "write";

	function hoursSelect($start, $end, $check){
		$end = $end+1;
		for($i=$start; $i<$end; $i++){
			if(strlen($i)==1) $i="0".$i;
			($check == $i) ? $onSelected = "selected" : $onSelected = "";
			echo "<option value=".$i." ".$onSelected.">".$i."</option> ";
		}
	}

	function minSelect($start, $end, $check){
		for($i=$start; $i<=$end; $i=$i+10){
			if(strlen($i)==1) $i="0".$i;
			($check == $i) ? $onSelected = "selected" : $onSelected = "";
			echo "<option value=".$i." ".$onSelected.">".$i."</option> ";
		}
	}

	/*단과시험 정보 출력*/
	$pArray = null;
	$coulmn = " [goods_code] ,[goods_name] ,[disp_goods_name] ,[SB_goods_type] ,[disp_price] ,[sell_price]";
	$sql = "SELECT ".$coulmn;
	$sql .= " FROM [Goods_info]";
	$sql .="  WHERE use_CHK = 'O' AND pack_CHK='X'";
	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	$arrGenExam = array();		// 정기시험(시험구분)
	$arrSpeExam = array();			// 특별시험(특별접수)

	foreach($arrRows as $data){
		//goods_code^goods_name^sell_price
		$inArrayExamInfo = $data["goods_code"]."^".$data["goods_name"]."^".$data["sell_price"];
	
		switch($data['SB_goods_type']){
			// 정기시험
			case "EXR" :
				array_push($arrGenExam, $inArrayExamInfo);
			break;
			// 특별시험
			case "EXS":
				array_push($arrSpeExam, $inArrayExamInfo);
			break;
		}
	}
	/*단과시험 정보 출력 끝*/



/*
	@ 화면구성
	@ 입력하는 필드 배열에 담는다.
	@ 시험마다 화면 노출하는 항목 다름.
		> 공통입력 항목 : 시험구분, 회차, 시험일, 정기접수기간, 성적표 수령방법 변경기간, 성적발표일, 환불1차
		> TOEIC, JPT : 특별접수, 특별접수기간, 환불 2차
		> 토익스피킹, KPE, JET-SW : 시험시간 삭제
		> 토익스피킹, 주니어테스트 : 기간연장 항목 추가
				examCode		시험구분
				examSpeCode	특별접수
				examNum			회차
				examDay			시험일
				examDate			시험시간
				genRegi				정기접수기간
				speRegi				특별접수기간
				regiExt				기간연장
				scoreChange	성적표 수령방법 변경기간
				scoreDay			성적 발표일
				refFirst				환불 1차
				refSecond			환불 2차
*/

	$SBExamCate = "TOE";	// 토익>전체필드오픈
	
	// 공통 입력항목 세팅
	$arrOpenField = array(
			"examCode",
			"examNum",
			"examDay", 
			"genRegi", 
			"scoreChange", 
			"scoreDay", 
			"refFirst"
	);
	// 공통 입력항목 세팅 끝

	// 시험별 추가 입력항목
	switch($SBExamCate){
		case "TOEIC" : 
			array_push($arrOpenField, "examSpeCode");
			array_push($arrOpenField, "speRegi");
			array_push($arrOpenField, "refSecond");
		break;
		case "토익스피킹" : 
			array_push($arrOpenField, "regiExt");
		break;

		case "TOE" :  // 전체오픈
			array_push($arrOpenField, "examSpeCode");
			array_push($arrOpenField, "examDate");
			array_push($arrOpenField, "regiExt");
			array_push($arrOpenField, "speRegi");
			array_push($arrOpenField, "regiExt");
			array_push($arrOpenField, "refSecond");
		break;
		default: break;
	}
	// 시험별 추가 입력항목 끝

	if($proc == "modify"){

		$pArray = null;
		$coulmn = "
			[Exam_code] as Exam_code
			,[SB_Exam_cate] as SB_Exam_cate
			,[Exam_num] as Exam_num
			,[Exam_Name] as Exam_Name
			,convert(char(16),[Exam_day],120) as Exam_day
			,convert(char(13),[Score_day],120) as Score_day
			,[Exam_start_time] as Exam_start_time
			,[check_in_time] as check_in_time
			,convert(char(13),[gen_regi_Start],120) as gen_regi_Start
			,convert(char(13),[gen_regi_End],120) as gen_regi_End
			,convert(char(13),[spe_regi_Start],120) as spe_regi_Start
			,convert(char(13),[spe_regi_End],120) as spe_regi_End
			,convert(char(13),[ref_first_start],120) as ref_first_start
			,convert(char(13),[ref_first_end],120) as ref_first_end
			,convert(char(13),[ref_sec_start],120) as ref_sec_start
			,convert(char(13),[ref_sec_end],120) as ref_sec_end
			,convert(char(13),[regi_ext_end],120) as regi_ext_end
			,convert(char(13),[score_change_start],120) as score_change_start
			,convert(char(13),[score_change_end],120) as score_change_end
			,[conf_type] as conf_type
			,[update_day] as update_day
			,[ok_id] as ok_id
			,[okType] as okType
			,(select goods_code from Exam_Info as EI LEFT OUTER JOIN Exam_Goods as EG  on EI.Exam_code=EG.Exam_code WHERE EG.Exam_code = oEI.Exam_code AND LEFT(EG.goods_code,3) = 'EXR') as goods_code
			,(select goods_code from Exam_Info as EI LEFT OUTER JOIN Exam_Goods as EG  on EI.Exam_code=EG.Exam_code WHERE EG.Exam_code = oEI.Exam_code AND LEFT(EG.goods_code,3) = 'EXS') as goods_code_spe
		";



		$sql = "SELECT ".$coulmn." FROM";
		$sql .= "  [Exam_Info] AS oEI ";
		$sql .= " WHERE ";
		$sql .= " Exam_code = :examCode";
		$pArray[':examCode'] = $pExamCode;

		$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		$examNum = $arrRows[0]["Exam_num"];			// 회차
		$examDay = $arrRows[0]["Exam_day"];				// 시험일
		$SBExamCate =  $arrRows[0]["SB_Exam_cate"];

		$goodsCode = $arrRows[0]["goods_code"];				// 단과시험정보
		$goodsCodeSpe = $arrRows[0]["goods_code_spe"];				// 단과시험정보


		$examStartTime = explode(":",$arrRows[0]["Exam_start_time"]);		//시험기간
			$examStartTimeHours = $examStartTime[0];	// 시험기간>시간
			$examStartTimeMin = $examStartTime[1];		// 시험기간>분
		$checkInTime = explode(":",$arrRows[0]["check_in_time"]);	//입실통제시간
			$checkInTimeHours = $checkInTime[0];				// 입실통제시간>시간
			$checkInTimeMin = $checkInTime[1];					// 입실통제시간>분
		$genRegiStart = explode(" ", $arrRows[0]["gen_regi_Start"]);	// 정기접수기간 시작
			$genRegiStartDay = $genRegiStart[0];										// 정기접수기간 시작>년월일
			$genRegiStartHours = substr($genRegiStart[1],0,2);				// 정기접수기간 시작>시간
		$genRegiEnd = explode(" ", $arrRows[0]["gen_regi_End"]);	// 정기접수기간 종료
			$genRegiEndDay = $genRegiEnd[0];											// 정기접수기간 종료>년월일
			$genRegiEndHours = substr($genRegiEnd[1],0,2);					// 정기접수기간 종료>시간
		$speRegiStart = explode(" ", $arrRows[0]["spe_regi_Start"]);	// 특별접수기간
			$speRegiStartDay = $speRegiStart[0];											// 특별접수기간 시작>년월일
			$speRegiStartHours = substr($speRegiStart[1],0,2);				// 특별접수기간 시작>시간
		$speRegiEnd = explode(" ", $arrRows[0]["spe_regi_End"]);	// 특별접수기간
			$speRegiEndDay = $speRegiEnd[0];												// 특별접수기간 시작>년월일
			$speRegiEndHours = substr($speRegiEnd[1],0,2);					// 특별접수기간 시작>시간
		$regiExtEnd = explode(" ",$arrRows[0]["regi_ext_end"]);			// 기간연장기간
			$regiExtEndDay = $regiExtEnd[0];													// 기간연장기간 시작>년월일
			$regiExtEndHours = substr($regiExtEnd[1],0,2);						// 기간연장기간 시작>시간
		$scoreChangeDay = substr($arrRows[0]["score_change_end"],0,10);	// 성적표 수령방법 변경기간 (db>start~end, 기획퍼블>~end)
		$scoreDay = explode(" ",$arrRows[0]["Score_day"]);				// 성적발표일
			$scoreDayDay = $scoreDay[0];													// 성적발표일 >년월일
			$scoreDayHours = substr($scoreDay[1],0,2);							// 성적발표일 >시간
		$refFirstStart = explode(" ",$arrRows[0]["ref_first_start"]);			// 1차 환불기간 시작
			$refFirstStartDay = $refFirstStart[0];													// 1차 환불기간 시작>년월일
			$refFirstStartHours = substr($refFirstStart[1],0,2);						// 1차 환불기간 시작>시간
		$refFirstEnd = explode(" ",$arrRows[0]["ref_first_end"]);			// 1차 환불기간 종료
			$refFirstEndDay = $refFirstEnd[0];													// 1차 환불기간 종료>년월일
			$refFirstEndHours = substr($refFirstEnd[1],0,2);						// 1차 환불기간 종료>시간
		$refSecStart = explode(" ",$arrRows[0]["ref_first_end"]);				// 2차 환불기간 시작
			$refSecondStartDay = $refSecStart[0];											// 2차 환불기간 시작>년월일
			$refSecondStartHours = substr($refSecStart[1],0,2);					// 2차 환불기간 시작>시간
		$refSecEnd = explode(" ",$arrRows[0]["ref_sec_end"]);				// 2차 환불기간 종료
			$refSecondEndDay = $refSecEnd[0];												// 2차 환불기간 종료>년월일
			$refSecondEndHours = substr($refSecEnd[1],0,2);						// 2차 환불기간 종료>시간

	}


	
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>
<!--right -->

<style>
.schField{display:none;}
</style>

<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">시험일정관리 <span class="sm_tit">( 입력 )</span></h3>
			<!-- 테이블1 -->
<form name="frmWrite" id="frmWrite" action="./examSetSchProc.php" method="post"> 
	<input type="hidden" name="proc" value="<?=$proc?>" />
	<input type="hidden" name="SBExamCate" value="<?=$SBExamCate?>" />
	<?php	if($proc=="modify"){?>
	<input type="hidden" name="examCode" value="<?=$pExamCode?>" />
	<?php	}?>
			<div class="box_bs">
				<div class="wrap_tbl">
					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width: 180px;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
							<tr  id="examCode" class="schField">
								<th>시험구분</th>
								<td>
									<div class="item">
										<select name="goodsCode" >
<?php	
	/*
	[0] > goods_code
	[1] > goods_name
	[2] > sell_price
	*/
	foreach($arrGenExam as $data){
		$arrData = explode("^",$data);
		
?>
											<option value="<?=$arrData[0]?>" <?=($arrData[0] == $goodsCode) ? "selected" : ""; ?>><?=$arrData[1]?> (<?=number_format($arrData[2])?>원) </option> 
<?php	}?>
										</select>
									</div>
								</td>
							</tr>
							<tr  id="examSpeCode" class="schField">
								<th>특별접수</th>
								<td>
									<div class="item">
										<select name="goodsCodeSpe">  
<?php	
	/*
	[0] > goods_code
	[1] > goods_name
	[2] > sell_price
	*/
	foreach($arrSpeExam as $data){
		$arrData = explode("^",$data);

?>
											<option value="<?=$arrData[0]?>" <?=($arrData[0] == $goodsCode) ? "selected" : ""; ?>><?=$arrData[1]?> (<?=number_format($arrData[2])?>원) </option> 
<?php	}?>
										</select>
									</div>
								</td>
							</tr>
							<tr id="examNum" class="schField">
								<th>회차</th>
								<td>
									<div class="item">
<?php	if($proc == "write"){?>
										<input style="width: 150px;" name="examNum" class="onlyNumber" type="text">
										<input type="hidden" id="examNumCheck" name="examNumCheck" value="">
										<button class="btn_sm_bg_grey" type="button" id="btnIdCheck">중복확인</button>
<?php	}else{?>
										<?=$examNum?>
										<input type="hidden"name="examNum" class="onlyNumber" value="<?=$examNum?>">
										<input type="hidden" id="examNumCheck" name="examNumCheck" value="Y">
<?php	}?>
									</div>
								</td>
							</tr>
							<tr  id="examDay" class="schField">
								<th>시험일</th>
								<td>
									<div class="item">
										<input style="width: 160px;" name="examDay" class="datepicker" type="text" value="<?=$examDay?>">
									</div>
								</td>
							</tr>
							<tr  id="examDate" class="schField">
								<th>시험기간</th>
								<td>
									<div class="item">
										<select name="examStartTimeHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $examStartTimeHours)?>
										</select> 시 &nbsp;
										<select name="examStartTimeMin" style="width: 70px;">  
											<?=minSelect('0', '50', $examStartTimeMin)?>
										</select> 분 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										( 입실통제시간 :
										<select name="checkInTimeHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $checkInTimeHours)?>
										</select> 시 &nbsp;
										<select name="checkInTimeMin" style="width: 70px;">  
											<?=minSelect('0', '50', $checkInTimeMin)?>
										</select> 분
										)
									</div>
								</td>
							</tr>
							<tr  id="genRegi" class="schField">
								<th>정기접수기간</th>
								<td>
									<div class="item">
										<input name="genRegiStartDay" style="width: 160px;" class="datepicker" type="text" value="<?=$genRegiStartDay?>">
										<select name="genRegiStartHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $genRegiStartHours)?>
										</select> &nbsp;~&nbsp;
										<input name="genRegiEndDay" style="width: 160px;" class="datepicker" type="text" value="<?=$genRegiEndDay?>">
										<select name="genRegiEndHours" >
											<?=hoursSelect('0', '23', $genRegiEndHours)?>
										</select>
									</div>
								</td>
							</tr>
							<tr  id="speRegi" class="schField">
								<th>특별접수기간</th>
								<td>
									<div class="item">
										<input  name="speRegiStartDay" style="width: 160px;" class="datepicker" type="text" value="<?=$speRegiStartDay?>">
										<select name="speRegiStartHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $speRegiStartHours)?>
										</select> &nbsp;~&nbsp;
										<input name="speRegiEndDay" style="width: 160px;" class="datepicker" type="text" value="<?=$speRegiEndDay?>">
										<select name="speRegiEndHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $speRegiEndHours)?>
										</select>
									</div>
								</td>
							</tr>
							<tr  id="regiExt" class="schField">
								<th>기간연장</th>
								<td>
									<div class="item">
										&nbsp;~&nbsp;
										<input name="regiExtEndDay" style="width: 160px;" class="datepicker" type="text" value="<?=$regiExtEndDay?>">
										<select name="regiExtEndHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $regiExtEndHours)?>
										</select>
									</div>
								</td>
							</tr>
							<tr id="scoreChange" class="schField">
								<th>성적표 수령방법 변경기간</th>
								<td>
									<div class="item">
										&nbsp;~&nbsp;
										<input name="scoreChangeDay" style="width: 160px;" class="datepicker" type="text" value="<?=$scoreChangeDay?>">
									</div>
								</td>
							</tr>
							<tr id="scoreDay" class="schField">
								<th>성적발표일</th>
								<td>
									<div class="item">
										<input name="scoreDayDay" style="width: 160px;" class="datepicker" type="text" value="<?=$scoreDayDay?>">
										<select name="scoreDayHours"style="width: 70px;">
											<?=hoursSelect('0', '23', $scoreDayHours)?>
										</select>
									</div>
								</td>
							</tr>
							<tr id="refFirst" class="schField">
								<th>환불 1차</th>
								<td>
									<div class="item">
										<input name="refFirstStartDay" style="width: 160px;" class="datepicker" type="text" value="<?=$refFirstStartDay?>">
										<select name="refFirstStartHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $refFirstStartHours)?>
										</select> &nbsp;~&nbsp;
										<input name="refFirstEndDay" style="width: 160px;" class="datepicker" type="text" value="<?=$refFirstEndDay?>">
										<select name="refFirstEndHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $refFirstEndHours)?>
										</select>
									</div>
								</td>
							</tr>
							<tr id="refSecond" class="schField">
								<th>환불 2차</th>
								<td>
									<div class="item">
										<input name="refSecondStartDay" style="width: 160px;" class="datepicker" type="text" value="<?=$refSecondStartDay?>">
										<select name="refSecondStartHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $refSecondStartHours)?>
										</select> &nbsp;~&nbsp;
										<input name="refSecondEndDay" style="width: 160px;" class="datepicker" type="text" value="<?=$refSecondEndDay?>">
										<select name="refSecondEndHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $refSecondEndHours)?>
										</select>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="wrap_btn">
					<button class="btn_fill btn_md" id="btnWrite" type="button">저장</button>
					<button class="btn_line btn_md" id="btnCancel"  type="button">취소</button>
				</div>
			</div>
</form>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

<script>



$(document).ready(function () {


	/*시험에 따라 보여줄 항목*/
	var openField = function(idvle){
		$("#"+idvle).css("display", "table-row");
	}
	
	/* @ 회차 중복체크 */
	$("#btnIdCheck").on("click", function(){
		var examNum = $.trim($("input[name=examNum]").val());
		if ( examNum == ""){
			alert("회차를 입력해 주세요.");
			return false;
		}
		var u = "./examSetSchProc.php";
		var param = {
			"proc"	: "examCheck",
			"examNum"	: examNum
		};
		$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
			success: function(resJson) {
				console.log(resJson);
				if( resJson.data[0].cnt == 0 ){
					alert("사용 가능한 회차 입니다.");
					$("#examNumCheck").val("Y");
				}else{
					alert("중복된 회차가 존재 합니다.");
				}
			},
			error: function(e) {
				console.log(e)
				alert("현재 서버 통신이 원할하지 않습니다.");
			}
		});
	});
	/* @ 회차 중복체크 끝*/


	
	$("#btnWrite").on("click", function () {
	/*
	@ 유효성체크 필요
	*/
		$('#frmWrite').submit();
    });

	$("#btnCancel").on("click", function(){
		location.href = "./examSetSchList.php";
	});


<?php
for($i=0; $i<count($arrOpenField); $i++){
?>
	openField('<?=$arrOpenField[$i]?>');
<?php
}
?>

	/*숫자만 입력*/
	common.string.onlyNumber($(".onlyNumber"));

});

</script>

</fieldset>
</form> 
</body>
</html>
