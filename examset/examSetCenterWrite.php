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

	/*
	@ ETSCerti 어떻게 하지?

	*/

	if($proc == "modify"){
		
		$sql = " SELECT ";
		$sql .= " (SELECT left(SB_name,CHARINDEX('#', SB_name, 1) -1 ) FROM [theExam].[dbo].[SB_Info] WHERE SB_kind='area' AND SB_value = DEC.SB_area) as areaLev1, ";
		$sql .= " DEC.SB_area, DEC.center_group_code, DEC.link_center_code, DEC.center_name, DEC.zipcode, DEC.address, DEC.memo, DEC.use_CHK, ";
		$sql .= " DCC.STN_CenterName, DCC.STN_CenterID,DCC.STN_username,DCC.STN_password,DCC.PC_count,DCC.certi_PC,DCC.use_PC,DCC.ETS_certi"; 
		$sql .= " FROM ";
		$sql .= " [theExam].[dbo].[Def_exam_center] AS [DEC] ";
		$sql .= " inner join ";
		$sql .= " [theExam].[dbo].[Def_center_CBT] AS [DCC] ";
		$sql .= " on DEC.center_code = DCC.center_code ";
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
			$centerGroupCode = $arrRows[0]["center_group_code"];
			$linkCenterCode = $arrRows[0]["link_center_code"];
			$centerName = $arrRows[0]["center_name"];
			$memo = $arrRows[0]["memo"];
			$STNCenterName = $arrRows[0]["STN_CenterName"];
			$STNCenterId = $arrRows[0]["STN_CenterID"];
			$STNUserName = $arrRows[0]["STN_username"];
			$STNPassword = $arrRows[0]["STN_password"];
			$mapUrl = $arrRows[0]["mapUrl"];
			$PCCount = $arrRows[0]["PC_count"];
			$certiPC = $arrRows[0]["certi_PC"];
			$usePC = $arrRows[0]["use_PC"];
			$ETSCerti = $arrRows[0]["ETS_certi"];

		}
	}

	$pArray = null;
	$sql = " SELECT * FROM [theExam].[dbo].[Def_exam_center_Group] ";
	$sql .= " WHERE use_CHK='O'";
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행



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
			<h3 class="title">지역/고사장 관리 - 센터 관리 <span class="sm_tit">( 입력 )</span></h3>
			<!-- 테이블1 -->
			<div class="box_bs">
<form name="frmWrite" id="frmWrite" action="./examSetProc.php" method="post"> 
	<input type="hidden" name="proc" value="<?=$proc?>">
	<input type="hidden" name="centerCode" value="<?=$pCenterCode?>">
	<input type="hidden" name="centerCate" value="<?=$pCenterCate?>">
				<div class="wrap_tbl">
					<div class="box_inform">
						<p class="txt_l">
							<strong class="s_tit">센터 기본정보</strong>
						</p>
					</div>
					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width: 180px;">
							<col style="width: auto;">
							<col style="width: 180px;">
							<col style="width: auto;">
							<col style="width: 180px;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
							<tr>
								<th>센터코드</th>
								<td colspan="5">
									<div class="item">
										<input style="width: 100px;" type="text" name="linkCenterCode" value="<?=$linkCenterCode?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>지역선택</th>
								<td colspan="5">
									<div class="item"> 
										<select style="width:200px;" name="areaLev1" id="areaLev1"></select>
										<select style="width:200px;" name="areaLev2" id="areaLev2"></select>
									</div>
								</td>
							</tr>
							<tr>
								<th>센터 그룹</th>
								<td colspan="5">
									<div class="item"> 
										<select style="width:400px;" name="centerGroupCode" id="centerGroupCode">
										<?php
										foreach($arrRows as $data){
										?>
											<option value="<?=$data["center_group_code"]?>" <?=($data["center_group_code"] == $centerGroupCode) ? "selected" : ""; ?>><?=$data["center_group_name"]?></option>
										<?php
										}?>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>센터명</th>
								<td colspan="5">
									<div class="item">
										<input style="width: 300px;" type="text" name="centerName" value="<?=$centerName?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>STN CenterName</th>
								<td colspan="2">
									<div class="item">
										<input style="width: 150px;" type="text" name="STNCenterName" value="<?=$STNCenterName?>">
									</div>
								</td>
								<th>STN CenterID</th>
								<td colspan="2">
									<div class="item">
										<input style="width: 150px;" type="text" name="STNCenterId" value="<?=$STNCenterId?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>STN username</th>
								<td colspan="2">
									<div class="item">
										<input style="width: 150px;" type="text" name="STNUserName" value="<?=$STNUserName?>">
									</div>
								</td>
								<th>STN password</th>
								<td colspan="2">
									<div class="item">
										<input style="width: 150px;" type="password" name="STNPassword" value="<?=$STNPassword?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>지도 URL</th>
								<td colspan="5">
									<div class="item">
										<input style="width: 80%;" type="text" name="mapUrl" value="<?=$mapUrl?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>보유 PC 수</th>
								<td>
									<div class="item">
										<input style="width: 100px;" type="text" name="PCCount" value="<?=$PCCount?>">
									</div>
								</td>
								<th>인증 PC 수</th>
								<td>
									<div class="item">
										<input style="width: 100px;" type="text" name="certiPC" value="<?=$certiPC?>">
									</div>
								</td>
								<th>접수가능 PC 수</th>
								<td>
									<div class="item">
										<input style="width: 100px;" type="text" name="usePC" value="<?=$usePC?>">
									</div>
								</td>
							</tr>
						</tbody>
					</table>

					<div class="box_inform" style="margin-top:20px;">
						<p class="txt_l">
							<strong class="s_tit">기타 관리정보</strong>
						</p>
					</div>

					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width: 180px;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
							<tr>
								<th>싱테</th>
								<td>
									<div class="item">
										<select style="width:200px;" name="ETSCerti">
											<option <?=( $ETSCerti == "신청" )? "selected": "" ?> value="신청">신청</option>
											<option <?=( $ETSCerti == "인증" )? "selected": "" ?> value="인증">인증</option>
											<option <?=( $ETSCerti == "인증불가" )? "selected": "" ?> value="인증불가">인증불가</option>
											<option <?=( $ETSCerti == "보류" )? "selected": "" ?> value="보류">보류</option>
											<option <?=( $ETSCerti == "삭제" )? "selected": "" ?> value="삭제">삭제</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>메모</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="memo" value="<?=$memo?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>ETS전송</th>
								<td>
									<div class="item">실패</div>
								</td>
							</tr>
							<tr>
								<th>신청일자</th>
								<td>
									<div class="item">1999-12-31 오후 11:59:59</div>
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
				required: "센터명을 입력 해주세요."
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
