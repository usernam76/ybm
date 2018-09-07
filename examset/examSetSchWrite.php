<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	$SBExamCate = "TOE";	// 토익시험을 기준으로 개발.
	
	$proc = "write";
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
	$exam = "TOEIC";
	
	$arrOpenField = array(
			"examCode",
			"examNum",
			"examDay", 
			"genRegi", 
			"scoreChange", 
			"scoreDay", 
			"refFirst"
	);


	switch($exam){
		case "TOEIC" : 
			array_push($arrOpenField, "examSpeCode");
			array_push($arrOpenField, "speRegi");
			array_push($arrOpenField, "refSecond");
		break;
		case "토익스피킹" : 
			array_push($arrOpenField, "regiExt");
		break;
		default: break;
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
										<select style="width: 200px;">  
											<option>TOEIC (00,000원) </option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr  id="examSpeCode" class="schField">
								<th>특별접수</th>
								<td>
									<div class="item">
										<select style="width: 200px;">  
											<option>TOEIC (00,000원) </option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr id="examNum" class="schField">
								<th>회차</th>
								<td>
									<div class="item">
										<input style="width: 150px;" type="text">
										<button class="btn_sm_bg_grey" type="button">중복확인</button>
									</div>
								</td>
							</tr>
							<tr  id="examDay" class="schField">
								<th>시험일</th>
								<td>
									<div class="item">
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
									</div>
								</td>
							</tr>
							<tr  id="examDate" class="schField">
								<th>시험기간</th>
								<td>
									<div class="item">
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> 시 &nbsp;
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> 분 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										( 입실통제시간 :
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> 시 &nbsp;
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> 분
										)
									</div>
								</td>
							</tr>
							<tr  id="genRegi" class="schField">
								<th>정기접수기간</th>
								<td>
									<div class="item">
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> &nbsp;~&nbsp;
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr  id="speRegi" class="schField">
								<th>특별접수기간</th>
								<td>
									<div class="item">
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> &nbsp;~&nbsp;
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr  id="regiExt" class="schField">
								<th>기간연장</th>
								<td>
									<div class="item">
										&nbsp;~&nbsp;
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr id="scoreChange" class="schField">
								<th>성적표 수령방법 변경기간</th>
								<td>
									<div class="item">
										&nbsp;~&nbsp;
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
									</div>
								</td>
							</tr>
							<tr id="scoreDay" class="schField">
								<th>성적발표일</th>
								<td>
									<div class="item">
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr id="refFirst" class="schField">
								<th>환불 1차</th>
								<td>
									<div class="item">
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> &nbsp;~&nbsp;
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr id="refSecond" class="schField">
								<th>환불 2차</th>
								<td>
									<div class="item">
										<input style="width: 160px;" class="datepicker" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> &nbsp;~&nbsp;
										<input style="width: 160px;" class="datepicker" type="text"><button class="btn_sm_calendar" type="button"></button>
										<select style="width: 70px;">  
											<option>00</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="wrap_btn">
					<button class="btn_fill btn_md" type="button">저장</button>
					<button class="btn_line btn_md" type="button">취소</button>
				</div>
			</div>
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
		dateFormat : 'yymmdd',         
		firstDay : 0,         
		isRTL : false,         
		showMonthAfterYear : false,         
		yearSuffix : '년',
		showOn: 'both',
		buttonImage: '/_resources/images/icon_cal.png',
		buttonImageOnly: true
	}; 
	$.datepicker.setDefaults($.datepicker.regional['ko']);         //default셋팅
	$(".datepicker" ).datepicker();  
	$('img.ui-datepicker-trigger').css({'cursor':'pointer', 'width':'31px', 'height':'31px', 'background-color':'#a4a8af', 'background-repeat':'no-repeat', 'background-position':'center center'});  //아이콘(icon) 위치


	var openField = function(idvle){
		$("#"+idvle).css("display", "table-row");
	}
<?php
for($i=0; $i<count($arrOpenField); $i++){
?>
	openField('<?=$arrOpenField[$i]?>');
<?php
}
?>


});

</script>

</fieldset>
</form> 
</body>
</html>
