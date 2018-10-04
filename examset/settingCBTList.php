<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);


	if($pExamCate == "") $pExamCate = "TOS";		// 기본은 TOE, 상황에 따라 변수 변경


	$pArray = null;
	$sql = " SELECT Exam_num, exam_code FROM theExam.dbo.exam_info WHERE  fin_CHK='O' and SB_Exam_cate = :examCate";
	$pArray["examCate"] = $pExamCate;
	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrCompleteCenter = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행


	$pArray = null;
	$sql = " SELECT ";
	$sql .= " Exam_num,   ";
	$sql .= " Exam_day,   ";
	$sql .= " exam_code, ";
	$sql .= " gen_regi_Start,  ";
	$sql .= " gen_regi_End, ";
	$sql .= " (SELECT sum(case when fin_CHK = 'O' then 1 else 0 end) FROM theExam.dbo.exam_center_CBT WHERE exam_code = EI.exam_code) as fin_center, ";
	$sql .= " (SELECT sum(case when SB_exam_regi_type ='지정' then 1 else 0 end) FROM theExam.dbo.exam_center_CBT WHERE exam_code = EI.exam_code) as group_center, ";
	$sql .= " (case when fin_CHK = 'O' then '완료' when fin_CHK = 'X' then '준비' end) as fin_CHK ";
	$sql .= " from ";
	$sql .= " theExam.dbo.exam_info as EI ";
	$sql .= " where SB_Exam_cate=:examCate ";
	$pArray["examCate"] = $pExamCate;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>


<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">회차별 센터세팅</h3>
			<!-- 테이블1 -->
			<div class="box_bs">
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:100px">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
						</colgroup>
						<thead>
							<tr>
								<th>회차</th>
								<th>시험일</th>
								<th>접수기간</th>
								<th>등록 센터 수</th>
								<th>지정 센터 수<br>(특정 사용자만 접수가능)</th>
								<th>센터 세팅</th>
							</tr>
						</thead>
						<tbody>
<?php
	foreach($arrRows as $data) {

		if($data["fin_CHK"] == "완료"){
			$addClass = ' class="other"';
			$examStatus = "complete";
		}else{
			if($data["fin_center"] == 0 && $data["wait_center"]==0  && $data["group_center"]==0){
				$examStatus = "non";
			}else{
				$examStatus = "ready";
			}
		}
		
?>
							<tr<?=$addClass?>>
								<td><?=$data["Exam_num"]?></td>
								<td><?=$data["Exam_day"]?></td>
								<td><?=$data["gen_regi_Start"]?> ~ <?=$data["gen_regi_End"]?></td>
								<td><?=($data["fin_center"] == "" ? "0" : $data["fin_center"]); ?></td>
								<td><?=($data["group_center"] == "" ? "0" : $data["group_center"]); ?></td>
								<td><a href="#" class="setting" data-examCode="<?=$data["exam_code"]?>" data-centerStatus="<?=$examStatus?>"><?=$data["fin_CHK"]?></a></td>
							</tr>
<?php
	}
?>
						</tbody>
					</table>
				</div>
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->
<!-- modal 팝업 :: goal-->


<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content" style="height:250px;">
   	<h2 class="p_title">고사장세팅</h2>
    <span class="close"><img src="../_resources/images/btn_x.png"></span>
    <!-- 팝업내용 -->
	<div class="wrap_tbl">
		<div class="wrap_tbl">
			<table class="type02">
				<caption></caption>
				<colgroup>
					<col style="width: auto;">
					<col style="width: auto;">
				</colgroup>
				<tbody>
					<tr>
						<td>
							<div class="item">
							   <select name="prevExamNum" style="width: 100%;">  
									<option value="">회차 선택</option> 
							   <?php
							   foreach($arrCompleteCenter as $data){
							   ?>
									<option value="<?=$data["exam_code"]?>"><?=$data["Exam_num"]?>회</option>
								<?php
								}
							   ?>
							   </select>
							</div>
							<div class="item">
								<a href="#" class="sel_link" id="copyLoad">선택 회차<br>고사장 불러오기</a>
							</div>
						</td>
						<td>
							<div class="item">
							   <a href="#" class="sel_link newWrite">새로 입력</a>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
   <!-- 팝업내용 :: goal //-->
  </div>
</div>

<script type="text/javascript">


$(document).ready(function () {
	var examCode;

	/* 레이어 팝업 새로 입력 */
	$(".newWrite").on("click", function(){
		location.href =  "./settingCBTEditList.php?examCode="+examCode+"&autoPop=Y";
	});
	/* 레이어 팝업 새로 입력 끝 */

	/* 레이어 팝업 닫기 */
	$(".close").on("click", function(){
		$("#myModal").css("display", "none");
	});
	/* 레이어 팝업 닫기 끝*/
	
	/* 선택 회차 불러오기 */
	$("#copyLoad").on("click", function(){
		var prevExamCode = $("select[name=prevExamNum]").val();
		var u = "./settingCBTProc.php";				// 비동기 전송 파일 URL
		var param = {	// 파라메터
			"proc" : "getCopyCenterAjax",
			"prevExamCode" : prevExamCode,
			"examCode" : examCode
		};

		/* 데이터 비동기 전송*/
		$.ajax({ type:'post', url: u, dataType : 'json',data:param,
			success: function(resJson) {
				if(resJson.status == "success"){
					window.location.reload();
					/*
					새로고침 이후 완료표시 여부에 대해 이후 액션이 필요.
					*/
					return false;
				}
			},
			error: function(resJson) {
				console.log(resJson)
				alert("현재 서버 통신이 원활하지 않습니다.");
			}
		});

	});


	/*회차 준비/완료 클릭 이벤트*/
	$(".setting").on("click", function(){
		var examStatus = $(this).attr("data-centerStatus");
		examCode  = $(this).attr("data-examCode");

		switch(examStatus){
			/* 최초 입력*/
			case "non" :
				$("#myModal").css("display", "block");
			break;
			/* 최초 입력 끝*/

			/* 준비 */
			case "ready" :
				location.href =  "./settingCBTEditList.php?examCode="+examCode;
			break;
			/* 준비 끝 */

			
			/* 완료 */
			case "complete" : 
				location.href = "./settingCBTDetailList.php?examCode="+examCode;
			break;
			/* 완료  끝*/
			default:
			break;
		}
	});

});

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
