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


/*
@ 시험마다 표시하는 항목 다름.
> 전체항목 순서 : 시험구분>특별접수>회차>시험일>시험시간>정기접수기간>특별접수기간>기간연장>성적표 수령방법 변경기간>성적발표일>환불1차>환불2차
>시험구분>특별접수>
Exam_num>
Exam_day>
Exam_start_time/check_in_time>
gen_regi_Start/gen_regi_End>
spe_regi_Start/spe_regi_End>
regi_ext_end>
score_change_start/score_change_end>
Score_day>
ref_first_start/ref_first_end>
ref_sec_start/ref_sec_end

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
	$SBExamCate = "TOE";	// TEST>전체필드오픈
	
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
									<div name="examCode" class="item">
										<select style="width: 200px;">  
											<option value="">TOEIC (00,000원) </option> 
										</select>
									</div>
								</td>
							</tr>
							<tr  id="examSpeCode" class="schField">
								<th>특별접수</th>
								<td>
									<div class="item">
										<select name="examSpeCode" style="width: 200px;">  
											<option value="TOE">TOEIC (00,000원) </option> 
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
										<input style="width: 150px;" name="examNum" class="onlyNumber" type="text" value="<?=$arrRows[0]["examNum"]?>">
										<input type="hidden" id="examNumCheck" name="examNumCheck" value="">
										<button class="btn_sm_bg_grey" type="button" id="btnIdCheck">중복확인</button>
<?php	}?>
									</div>
								</td>
							</tr>
							<tr  id="examDay" class="schField">
								<th>시험일</th>
								<td>
									<div class="item">
										<input style="width: 160px;" name="examDay" class="datepicker" type="text">
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
											<option value="00">00</option> 
											<option value="10">10</option> 
											<option value="20">20</option> 
											<option value="30">30</option> 
											<option value="40">40</option> 
											<option value="50">50</option> 
										</select> 분 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										( 입실통제시간 :
										<select name="checkInTimeHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $checkInTimeHours)?>
										</select> 시 &nbsp;
										<select name="checkInTimeMin" style="width: 70px;">  
											<option value="00">00</option> 
											<option value="10">10</option> 
											<option value="20">20</option> 
											<option value="30">30</option> 
											<option value="40">40</option> 
											<option value="50">50</option> 
										</select> 분
										)
									</div>
								</td>
							</tr>
							<tr  id="genRegi" class="schField">
								<th>정기접수기간</th>
								<td>
									<div class="item">
										<input name="genRegiStartDay" style="width: 160px;" class="datepicker" type="text">
										<select name="genRegiStartHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $genRegiStartHours)?>
										</select> &nbsp;~&nbsp;
										<input name="genRegiEndDay" style="width: 160px;" class="datepicker" type="text">
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
										<input  name="speRegiStartDay" style="width: 160px;" class="datepicker" type="text">
										<select name="speRegiStartHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $speRegiStartHours)?>
										</select> &nbsp;~&nbsp;
										<input name="speRegiEndDay" style="width: 160px;" class="datepicker" type="text">
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
										<input name="regiExtEndDay" style="width: 160px;" class="datepicker" type="text">
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
										<input name="" style="width: 160px;" class="datepicker" type="text">
									</div>
								</td>
							</tr>
							<tr id="scoreDay" class="schField">
								<th>성적발표일</th>
								<td>
									<div class="item">
										<input name="scoreDayDay" style="width: 160px;" class="datepicker" type="text">
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
										<input name="refFirstStartDay" style="width: 160px;" class="datepicker" type="text">
										<select name="refFirstStartHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $refFirstStartHours)?>
										</select> &nbsp;~&nbsp;
										<input name="refFirstEndDay" style="width: 160px;" class="datepicker" type="text">
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
										<input name="refSecondStartDay" style="width: 160px;" class="datepicker" type="text">
										<select name="refSecondStartHours" style="width: 70px;">  
											<?=hoursSelect('0', '23', $refSecondStartHours)?>
										</select> &nbsp;~&nbsp;
										<input name="refSecondEndDay" style="width: 160px;" class="datepicker" type="text">
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

	$.datepicker.regional['ko'] = {         
		closeText : '닫기',         
		prevText : '이전달',         
		nextText : '다음달',         
		currentText : '오늘',         
		monthNames : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],         
		monthNamesShort : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],         
		dayNames : ['일', '월', '화', '수', '목', '금', '토'],         
		dayNamesShort : ['일', '월', '화', '수', '목', '금', '토'],         
		dayNamesMin : ['일', '월', '화', '수', '목', '금', '토'],         
		weekHeader : 'Wk',         
		dateFormat : 'yy-mm-dd',         
		firstDay : 0,         
		isRTL : false,         
		showMonthAfterYear : false,         
		yearSuffix : '년',
		showOn: 'both',
		buttonImageOnly: false
	}; 

	$.datepicker.setDefaults($.datepicker.regional['ko']);         //default셋팅
	$(".datepicker" ).datepicker();  
	$(".ui-datepicker-trigger").css({'font-size':'0px', 'line-height':'0px', 'margin-top':'-2px', 'margin-left':'3px'});
	$(".ui-datepicker-trigger").addClass('btn_sm_calendar');

	/*시험에 따라 보여줄 항목*/
	var openField = function(idvle){
		$("#"+idvle).css("display", "table-row");
	}

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


	
	$("#btnWrite").on("click", function () {

		$('#frmWrite').submit();
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
