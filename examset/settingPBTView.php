<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	if($pCenterCate == "") $pCenterCate = "PBT";		// 기본은 PBT, 상황에 따라 변수 변경

	$pArray = null;
	$sql = "Select 
		D.Exam_num
		, D.Exam_day
		, C.center_name
		, B.room_count
		, B.room_seat
		, A.use_CHK
		, A.SB_exam_regi_type 
		,A.memo
	FROM
		exam_center as A 
		join 
		exam_center_PBT as B 
			on A.center_code = B.center_code  and A.exam_code=B.exam_code
		join 
		Def_exam_center as C 
			on A.center_code = C.center_code 
		join 
		Exam_Info as D 
			on A.Exam_code = D.Exam_code
	WHERE 
		A.center_code = :centerCode 
		AND A.Exam_code = :exam_code
	";
	$pArray[':centerCode'] = $pCenterCode;
	$pArray[':exam_code'] = $pExamCode;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	if( count($arrRows) == 0 ){
		fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
	}else{
		$examNum = $arrRows[0]["Exam_num"];
		$examDay = $arrRows[0]["Exam_day"];
		$centerName = $arrRows[0]["center_name"];
		$roomCount = $arrRows[0]["room_count"];
		$roomSeat = $arrRows[0]["room_seat"];
		$useCHK = $arrRows[0]["use_CHK"];
		$SBExamRegiType = $arrRows[0]["SB_exam_regi_type"];
		$memo = $arrRows[0]["memo"];

	}



	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';

?>

<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">회차별 고사장세팅 <span class="sm_tit">( 수정 )</span></h3>
			<!-- 테이블1 -->
<form name="frmWrite" id="frmWrite" action="./settingPBTProc.php" method="post"> 
	<input type="hidden" name="proc" value="modifyCenter">
	<input type="hidden" name="centerCode" value="<?=$pCenterCode?>">
	<input type="hidden" name="examCode" value="<?=$pExamCode?>">
			<div class="box_bs">
				<div class="wrap_tbl">
					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width: 180px;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
							<tr>
								<th>회차</th>
								<td><strong><?=$examNum?>회 (<?=substr($examDay,0,10)?>)</strong></td>
							</tr>
							<tr>
								<th>고사장</th>
								<td><?=$centerName?></td>
							</tr>
							<tr>
								<th>고사실수</th>
								<td>
									<div class="item"> 
										<input style="width: 80px;" name="roomCount" type="text" value="<?=$roomCount?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>좌석수</th>
								<td>
									<div class="item">
										<input class="i_unit" id="20" type="radio" name="roomSeat" value="20" <?=( $roomSeat == "20" )? "checked": "" ?>><label for="20">20</label>
										<input class="i_unit" id="25" type="radio" name="roomSeat" value="25" <?=( $roomSeat == "25" )? "checked": "" ?>><label for="25">25</label>
										<input class="i_unit" id="30" type="radio" name="roomSeat" value="30" <?=( $roomSeat == "30" )? "checked": "" ?>><label for="30">30</label>
										<input class="i_unit" id="35" type="radio" name="roomSeat" value="35" <?=( $roomSeat == "35" )? "checked": "" ?>><label for="35">35</label>
										<input class="i_unit" id="40" type="radio" name="roomSeat" value="40" <?=( $roomSeat == "40" )? "checked": "" ?>><label for="40">40</label>
										<input class="i_unit" id="60" type="radio" name="roomSeat" value="60" <?=( $roomSeat == "60" )? "checked": "" ?>><label for="60">60</label>
									</div>
								</td>
							</tr>
							<tr>
								<th>총좌석수</th>
								<td><span id="totalRoom"><?=($roomCount * $roomSeat)?></span></td>
							</tr>
							<tr>
								<th>남은좌석</th>
								<td>000</td>
							</tr>
							<tr>
								<th>사용여부</th>
								<td>
									<div class="item">
										<input class="i_unit" id="use" type="radio" name="useCHK" value="O" <?=($useCHK == "O")? "checked":""; ?>><label for="use">사용</label>
										<input class="i_unit" id="wait" type="radio" name="useCHK" value="-" <?=($useCHK == "-")? "checked":""; ?>><label for="wait">대기</label>
										<input class="i_unit" id="none" type="radio" name="useCHK"  value="X" <?=($useCHK == "X")? "checked":""; ?>><label for="none">미사용</label>
									</div>
								</td>
							</tr>
							<tr>
								<th>사용제한</th>
								<td>
									<div class="item">
									
										<input class="i_unit" name="SBExamRegiType" id="general" type="radio" value="일반" <?=($SBExamRegiType == "일반")? "checked" : ""; ?>><label for="general">일반고사장</label>
										<input class="i_unit" name="SBExamRegiType" id="special" type="radio" value="지정" <?=($SBExamRegiType == "지정")? "checked" : ""; ?>><label for="special">지정고사장 ( 단체접수 )</label>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										좌석확보 <input style="width: 80px;" type="text">
									</div>
								</td>
							</tr>
							<tr>
								<th>비고</th>
								<td>
									<div class="item">
										<input style="width: 90%;" type="text" name="memo" value="<?=$memo?>">
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="wrap_btn">
					<button class="btn_fill btn_md" id="btnWrite" type="button">확인</button>
					<button class="btn_line btn_md" id="btnCancel" type="button">취소</button>
				</div>
			</div>
			<!-- //테이블1-->
</form>

			<!-- //테이블2-->
			<div class="box_bs">
				<div class="box_inform">
					<p class="txt_l">
					<strong class="s_tit fm_malgun">변경내역</strong>
					</p>
				</div>
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:auto;">
							<col style="width:auto;">
							<col style="width:auto;">
							<col style="width:auto;">
							<col style="width:auto;">
							<col style="width:auto;">
							<col style="width:200px;">
						</colgroup>
						<thead>
							<tr>
								<th>유형</th>
								<th>고사실수</th>
								<th>좌석수</th>
								<th>사용여부</th>
								<th>사용제한</th>
								<th>관리자</th>
								<th>변경일</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>준비</td>
								<td>5</td>
								<td>30</td>
								<td>사용함</td>
								<td>일반고사장</td>
								<td>admin</td>
								<td>2018-00-00 오후 00:00:00</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!-- //테이블2-->
		</div>
	</div>
</div>
<!--right //-->
<script>

$(document).ready(function () {



	$("#btnWrite").on("click", function(){
		$("#frmWrite").submit();
	});
	$("#btnCancel").on("click", function(){
		history.back(-1);
	});


	/*고사실 좌석수 계산*/
	$("input[name=roomCount]").on("keyup", function(){
		var roomCount = $(this).val();
		var roomSeat = $("input[name=roomSeat]:checked").val();
		totalCount(roomCount, roomSeat);
	});
	$(".i_unit").on("click", function(){
		var roomCount = $("input[name=roomCount]").val();
		var roomSeat = $(this).val();
		totalCount(roomCount, roomSeat);
	});
	var totalCount = function(rc, rs){
		if(typeof rc == "undefined" || typeof rs == "undefined"){ return false; }
		var totalCount = parseInt(rc,10) * parseInt(rs, 10);
		if(isNaN(totalCount)){ return false; }
		$("#totalRoom").text(totalCount);
	}
	/*고사실 좌석수 계산 끝*/

	/*숫자만 입력*/
	common.string.onlyNumber($("input[name=roomCount]"));
});
</script>
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>

