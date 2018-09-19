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

	$sql = "
	Select 
		Exam_num, 
		Exam_day, 
		A.gen_regi_Start, 
		A.gen_regi_End, 
		sum(case when B.fin_CHK = 'O' then 1 else 0 end) as fin_center, 
		sum(case when B.fin_CHK = '-' then 1 else 0 end) as wait_center, 
		sum(case when D.Group_Exam is not null then 1 else 0 end) as group_center, 
		case when A.fin_CHK = 'O' then '완료' when A.fin_CHK = 'X' then '준비' end as fin_CHK
	From
		exam_info as A (nolock) join 
		v_Exam_center as B (nolock) 
			on A.Exam_code = B.Exam_code
		left outer join Group_Exam_info as C (nolock) 
			on A.Exam_code = C.Exam_code
		left outer join Group_exam_center as D (nolock) 
			on C.Group_Exam = D.Group_Exam
		Group by Exam_num, Exam_day, A.gen_regi_Start, A.gen_regi_End, A.fin_CHK
";


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
			<h3 class="title">회차별 고사장세팅</h3>
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
							<col style="width:auto">
						</colgroup>
						<thead>
							<tr>
								<th>회차</th>
								<th>시험일</th>
								<th>접수기간</th>
								<th>등록 고사장수</th>
								<th>대기 고사장수</th>
								<th>지정 고사장수<br>(특정 사용자만 접수가능)</th>
								<th>고사장세팅</th>
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
								<td><a href="#"><?=$data["Exam_day"]?></a></td>
								<td><?=$data["gen_regi_Start"]?> ~ <?=$data["gen_regi_End"]?></td>
								<td><?=$data["fin_center"]?></td>
								<td><?=$data["wait_center"]?></td>
								<td><?=$data["group_center"]?></td>
								<td><a href="#" class="setting" data-centerStatus="<?=$examStatus?>"><?=$data["fin_CHK"]?></a></td>
							</tr>
<?php
	}
?>
						<?php
						/*
							<tr>
								<td>000</td>
								<td><a href="#">2018-00-00</a></td>
								<td>0</td>
								<td>0</td>
								<td>00</td>
								<td>00</td>
								<td><a href="#">완료</a></td>
							</tr>
							<tr class="other"><!-- 배경색 other-->
								<td>000</td>
								<td><a href="#">2018-00-00</a></td>
								<td>0</td>
								<td>0</td>
								<td>00</td>
								<td>00</td>
								<td><a href="#">완료</a></td>
							</tr>
							*/?>
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
    <span class="close"><img src="../resources/images/btn_x.png"></span>
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
							   <select style="width: 100%;">  
									<option>회차 선택</option> 
									<option>선택 둘</option> 
									<option>선택 셋</option> 
							   </select>
							</div>
							<div class="item">
								<a href="#" class="sel_link">선택 회차<br>고사장 불러오기</a>
							</div>
						</td>
						<td>
							<div class="item">
							   <a href="#" class="sel_link">새로 입력</a>
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
// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}



$(document).ready(function () {
	

	$(".setting").on("click", function(){
		
	});

});

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
