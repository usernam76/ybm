<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];

	$resultArray = fnGetRequestParam($valueValid);
	

	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>

<script type="text/javascript" src="https://common.ybmnet.co.kr/postcode/js/postcode.js"></script>
<script type="text/javascript">
function getZipcodeSearch(){
	ybm.load(function(){
		new ybm.postcode({
			//recordsPerPage: "10",				// default : 10
			//pagePerBlock: "5",				// default : 5
			//layer: false,						// default : false (true 더라도 모바일에선 무조건 팝업으로 띄움)
			//layerTagId: "postcodeLayerPopup",	// default : "" (layerPopup 이 true 일때만 사용)
			oncomplete: function(data){			// callback func
				//console.log(data);			// zipNo, addrPart1, addrDetail
				document.getElementById("zip1").value = data.zipNo;
				document.getElementById("addr1").value = data.addrPart1;
				document.getElementById("addr2").value = data.addrDetail;
			}
		}).open();
	}());
}
</script>
<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">지역/고사장 관리 - 고사장 관리 <span class="sm_tit">( 입력 )</span></h3>
			<!-- 테이블1 -->
			<div class="box_bs">
<form name="frmWrite" id="frmWrite" action="./examSetProc.php" method="post"> 
	<input type="hidden" name="proc" value="<?=$proc?>">
				<div class="wrap_tbl">
					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width: 180px;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
							<tr>
								<th>고사장코드</th>
								<td>
									<div class="item">
										<input style="width: 100px;" type="text">
									</div>
								</td>
							</tr>
							<tr>
								<th>지역선택</th>
								<td>
									<div class="item"> 
										<select style="width:200px;" name="areaA">  
											<option>시도</option> 
											<option value="서울">서울특별시</option> 
											<option value="경기">경기도</option> 
										</select>
										<select style="width:200px;" name="areaB">  
											<option>시군구</option> 
											<option value="강남">강남</option> 
											<option value="강북">강북</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>고사장명</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="centerName">
									</div>
								</td>
							</tr>
							<tr>
								<th>주소</th>
								<td>
									<div class="item">
										<input style="width: 200px;" type="text" name="zipcode" id="zip1">
										<button class="btn_sm_bg_grey" type="button" onclick="getZipcodeSearch()">우편번호 검색</button>
									</div>
									<div class="item pad_t5">
										<input style="width: 80%;" type="text" name="address1" id="addr1">
									</div>
									<div class="item pad_t5">
										<input style="width: 80%;" type="text" name="address2" id="addr2">
									</div>
								</td>
							</tr>
							<tr>
								<th>지도 URL</th>
								<td>
									<div class="item">
										<input style="width: 80%;" type="text">
									</div>
								</td>
							</tr>
							<tr>
								<th>고사실수</th>
								<td>
									<div class="item">
										<input style="width: 100px;" type="text" name="roomCount">
									</div>
								</td>
							</tr>
							<tr>
								<th>좌석수</th>
								<td>
									<div class="item">
										<input class="i_unit" id="20" type="radio" name="roomSeat" value="20"><label for="20">20</label>
										<input class="i_unit" id="25" type="radio" name="roomSeat" value="25"><label for="25">25</label>
										<input class="i_unit" id="30" type="radio" name="roomSeat" value="30"><label for="30">30</label>
										<input class="i_unit" id="35" type="radio" name="roomSeat" value="35"><label for="35">35</label>
										<input class="i_unit" id="40" type="radio" name="roomSeat" value="40"><label for="40">40</label>
										<input class="i_unit" id="60" type="radio" name="roomSeat" value="60"><label for="60">60</label>
										<span class="fl_r">(총좌석수 : <span id="totalRoom"></span>)</span>
									</div>
								</td>
							</tr>
							<tr>
								<th>특이사항</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="memo">
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
</form>
				<div class="wrap_btn">
					<button class="btn_fill btn_md" type="button"  id="btnWrite"><?=( $proc == "write" )? "등록": "확인" ?></button>
					<button class="btn_line btn_md" type="button"  id="btnCancel">취소</button>
				</div>
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>

<script>
$(document).ready(function () {

	
	$('#frmWrite').validate({
        onfocusout: false,
        rules: {
            centerName: {
                required: true    //필수조건
			}, idCheck: {
                required: true    //필수조건
			}, admName: {
                required: true    //필수조건
			}, tokenCode: {
                required: true    //필수조건
			}, admEmail1: {
                required: true    //필수조건
			}, detpLev3: {
                required: true    //필수조건
			}
        }, messages: {
			admId: {
				required: "고사장명을 입력해주세요."
			}, idCheck: {
				required: "아이디 중복체크를 해주세요."
			}, admName: {
				required: "이름을 입력해주세요."
			}, tokenCode: {
				required: "eToken을 입력해주세요."
			}, admEmail1: {
				required: "이메일을 입력해주세요."
			}, detpLev3: {
				required: "부서를 선택해주세요."
			}
        }, errorPlacement: function (error, element) {
            // $(element).removeClass('error');
            // do nothing;
        }, invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                alert(validator.errorList[0].message);
                validator.errorList[0].element.focus();
            }
        }
    });

	$("#btnWrite").on("click", function(){
		
		
	});


	$("input[name=roomCount]").on("change", function(){
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
		if(typeof rc == "undefined" || typeof rs == "undefined"){
			return false;
		}
		var totalCount = parseInt(rc,10) * parseInt(rs, 10);

		if(isNaN(totalCount)){
			return false;
		}

		$("#totalRoom").text(totalCount);
	}
});
</script>

<!--right //-->

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
