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
	$pCenterCate = "CBT";	// 해당 페이지에서는 PBT만 입력한다.

	if($proc == "modify"){
		
		$sql = " SELECT ";
		$sql .= " (SELECT left(SB_name,CHARINDEX('#', SB_name, 1) -1 ) FROM [theExam].[dbo].[SB_Info] WHERE SB_kind='area' AND SB_value = DEC.SB_area) as areaLev1, ";
		$sql .= " DEC.SB_area, DEC.link_center_code, DEC.center_name, DEC.zipcode, DEC.address, DEC.memo, DEC.use_CHK, DCP.room_count, DCP.room_seat";
		$sql .= " FROM ";
		$sql .= " [theExam].[dbo].[Def_exam_center] as DEC ";
		$sql .= " left outer join ";
		$sql .= " [theExam].[dbo].[Def_center_PBT] as DCP ";
		$sql .= " on DEC.center_code = DCP.center_code ";
		$sql .= " WHERE ";
		$sql .= " DEC.center_code = :centerCode ";
		$pArray[':centerCode'] = $pCenterCode;

		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
	
		if( count($arrRows) == 0 ){
			fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
		}else{
			$useChk	= $arrRows[0]["use_CHK"];
			$areaLev1 = $arrRows[0]["areaLev1"];
			$areaLev2 = $arrRows[0]["SB_area"];
			$linkCenterCode = $arrRows[0]["link_center_code"];
			$centerName = $arrRows[0]["center_name"];
			$zipCode = $arrRows[0]["zipcode"];
			$mapUrl = $arrRows[0]["mapUrl"];
			$address = $arrRows[0]["address"];
			$memo = $arrRows[0]["memo"];
			$roomCount = $arrRows[0]["room_count"];
			$roomSeat = $arrRows[0]["room_seat"];
		}
	}

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
	<input type="hidden" name="centerCode" value="<?=$pCenterCode?>">
	<input type="hidden" name="centerCate" value="<?=$pCenterCate?>">
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
										<input style="width: 100px;" type="text" name="linkCenterCode" value="<?=$linkCenterCode?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>지역선택</th>
								<td>
									<div class="item"> 
										<select style="width:200px;" name="areaLev1" id="areaLev1"></select>
										<select style="width:200px;" name="areaLev2" id="areaLev2"></select>
									</div>
								</td>
							</tr>
							<tr>
								<th>고사장명</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="centerName" value="<?=$centerName?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>주소</th>
								<td>
									<div class="item">
										<input style="width: 200px;" type="text" name="zipcode" id="zip1" value="<?=$zipCode?>">
										<button class="btn_sm_bg_grey" type="button" onclick="getZipcodeSearch()">우편번호 검색</button>
									</div>
									<div class="item pad_t5">
										<input style="width: 80%;" type="text" name="address1" id="addr1" value="<?=$address?>">
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
										<input style="width: 80%;" type="text" name="mapUrl" value="<?=$mapUr?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>고사실수</th>
								<td>
									<div class="item">
										<input style="width: 100px;" type="text" name="roomCount" value="<?=$roomCount?>">
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
										<span class="fl_r">(총좌석수 : <span id="totalRoom"><?=($roomCount * $roomSeat)?></span>)</span>
									</div>
								</td>
							</tr>
							<tr>
								<th>특이사항</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="memo" value="<?=$memo?>">
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
</form>
				<div class="wrap_btn">
					<button class="btn_fill btn_md" type="button"  id="btnWrite"><?=( $proc == "write" )? "등록": "수정" ?></button>
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
            areaLev1: {
                required: true    //필수조건
			}, areaLev2: {
                required: true    //필수조건
			}, centerName: {
                required: true    //필수조건
			}, roomCount: {
                required: true    //필수조건
			}, roomSeat: {
                required: true    //필수조건
			}
        }, messages: {
			areaLev1: {
				required: "지역을 선택 해주세요."
			}, areaLev2: {
				required: "지역을 선택 해주세요."
			}, centerName: {
				required: "고사장명을 입력 해주세요."
			}, roomCount: {
				required: "고사실수를 입력 해주세요."
			}, roomSeat: {
				required: "좌석수를 선택 해주세요."
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
		$("#frmWrite").submit();
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


	/*지역정보 공용*/
	var param = {
		"areaLev1" 		: "areaLev1"	// 1detp 부서정보
		, "areaLev2" 		: "areaLev2"	// 2detp 부서정보
		, "optYn"			: "Y"			// 상단 옵션 사용여부(Y, N)
		, "firstOptVal"	: ""			// 상단 옵션  value
		, "firstOptLable"	: "선택"			// 상단 옵션  text
	}
	common.sys.setAreaComboCreate(param);
	$("#areaLev1").val('<?=$areaLev1?>').change();
	$("#areaLev2").val('<?=$areaLev2?>').change();
	/*지역정보 공용 끝*/

	/*숫자만 입력*/
	common.string.onlyNumber($("input[name=roomCount]"));

});
</script>

<!--right //-->

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
