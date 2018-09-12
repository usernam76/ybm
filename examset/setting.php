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
	$pCenterCate = "CBT";	// 해당 페이지에서는 CBT만 입력한다.



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
							<tr class="other">
								<td>000</td>
								<td><a href="#">2018-00-00</a></td>
								<td>0</td>
								<td>0</td>
								<td>00</td>
								<td>00</td>
								<td><a href="#">완료</a></td>
							</tr>
							<tr>
								<td>000</td>
								<td><a href="#">2018-00-00</a></td>
								<td>0</td>
								<td>0</td>
								<td>00</td>
								<td>00</td>
								<td><a href="#">완료</a></td>
							</tr>
							<tr>
								<td>000</td>
								<td><a href="#">2018-00-00</a></td>
								<td>0</td>
								<td>0</td>
								<td>00</td>
								<td>00</td>
								<td><a href="#" id="myBtn">준비</a></td>
							</tr>
							<tr>
								<td>000</td>
								<td><a href="#">2018-00-00</a></td>
								<td>0</td>
								<td>0</td>
								<td>00</td>
								<td>00</td>
								<td><a href="#">준비</a></td>
							</tr>
							<tr>
								<td>000</td>
								<td><a href="#">2018-00-00</a></td>
								<td>0</td>
								<td>0</td>
								<td>00</td>
								<td>00</td>
								<td><a href="#">준비</a></td>
							</tr>
							<tr>
								<td>000</td>
								<td><a href="#">2018-00-00</a></td>
								<td>0</td>
								<td>0</td>
								<td>00</td>
								<td>00</td>
								<td><a href="#">준비</a></td>
							</tr>
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

</script>
</fieldset>
</form> 
</body>
</html>
