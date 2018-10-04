<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	if($pCenterCate == "") $pCenterCate = "CBT";		// 기본은 CBT, 상황에 따라 변수 변경

	$pArray = null;
	$sql = "Select  ";
	$sql .= " Exam_Name, Exam_num, Exam_day, link_center_code, center_name, subject,B.certi_PC, A.use_CHK, A.SB_exam_regi_type  ";
	$sql .= " From exam_center as A (nolock) join "; 
	$sql .= " exam_center_CBT as B (nolock)  ";
	$sql .= " on a.center_code = B.center_code join  ";
	$sql .= " Def_exam_center as C (nolock) on A.center_code = C.center_code join  ";
	$sql .= " Exam_Info as D (nolock) on A.Exam_code = D.Exam_code ";
	$sql .= " Where A.center_code = :centerCode and A.Exam_code = :examCode ";
	$pArray[':centerCode'] = $pCenterCode;
	$pArray[':examCode'] = $pExamCode;


	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	if( count($arrRows) == 0 ){
		fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
	}else{
		$examNum = $arrRows[0]["Exam_num"];
		$examDay = $arrRows[0]["Exam_day"];
		$centerName = $arrRows[0]["center_name"];
		$certiPC = $arrRows[0]["certi_PC"];
		$subject = $arrRows[0]["subject"];
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
<form name="frmWrite" id="frmWrite" action="./settingCBTProc.php" method="post"> 
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
								<th>센터</th>
								<td><?=$centerName?></td>
							</tr>
							<tr>
								<th>과목</th>
								<td><?=$subject?></td>
							</tr>
							<tr>
								<th>총좌석수</th>
								<td>
									<div class="item"> 
										<input style="width: 80px;" name="certi_PC" type="text" value="<?=$certiPC?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>사용여부</th>
								<td>
									<div class="item">
										<input class="i_unit" id="use" type="radio" name="useCHK" value="O" <?=($useCHK == "O")? "checked":""; ?>><label for="use">사용</label>
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

