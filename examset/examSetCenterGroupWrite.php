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


	if($proc == "modify"){
		$pArray = null;
		$sql = " SELECT* FROM [theExam].[dbo].[Def_exam_center_Group] ";
		$sql .= " WHERE ";
		$sql .= " center_group_code = :centerGroupCode ";
		$pArray[':centerGroupCode'] = $pCenterGroupCode;

		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
	
		if( count($arrRows) == 0 ){
			fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
		}else{
			$centerGroupName = $arrRows[0]["center_group_name"];
			$SBCenterGroup = $arrRows[0]["SB_center_group"];
			$centerMap = $arrRows[0]["center_map"];
			$centerGroupType = $arrRows[0]["center_group_type"];
			$BEP = $arrRows[0]["BEP"];
			$useCHK	= $arrRows[0]["use_CHK"];
			$zipCode = $arrRows[0]["zipcode"];
			$address = $arrRows[0]["address"];
			$memo = $arrRows[0]["memo"];
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
			<h3 class="title">센터그룹관리 <span class="sm_tit">( 입력 )</span></h3>
			<!-- 테이블1 -->
			<div class="box_bs">
<form name="frmWrite" id="frmWrite" action="./examSetCenterGroupProc.php" method="post"> 
	<input type="hidden" name="proc" value="<?=$proc?>">
	<input type="hidden" name="centerGroupCode" value="<?=$pCenterGroupCode?>">
				<div class="wrap_tbl">
					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width: 180px;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
							<tr>
								<th>그룹명</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="centerGroupName" value="<?=$centerGroupName?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>그룹종류</th>
								<td>
									<div class="item">
										<select style="width:200px;"  name="SBCenterGroup">  
											<option>전체</option> 
											<option <?=($SBCenterGroup == "YBM직영CBT") ? "selected" : ""; ?> value="YBM직영CBT">YBM직영CBT</option> 
											<option <?=($SBCenterGroup == "YBM어학원CBT") ? "selected" : ""; ?> value="YBM어학원CBT">YBM어학원CBT</option> 
											<option <?=($SBCenterGroup == "4년제대학") ? "selected" : ""; ?> value="4년제대학">4년제대학</option> 
											<option <?=($SBCenterGroup == "2~3년제대학") ? "selected" : ""; ?> value="2~3년제대학">2~3년제대학</option> 
											<option <?=($SBCenterGroup == "중고교") ? "selected" : ""; ?> value="중고교">중고교</option> 
											<option <?=($SBCenterGroup == "직업학교학원") ? "selected" : ""; ?> value="직업학교학원">직업학교학원</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>센터약도</th>
								<td>
									<div class="item"> 
										<input style="width: 300px;" type="text" name="centerMap" value="<?=$centerMap?>">
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
								<th>사용제한</th>
								<td>
									<div class="item">
										<input class="i_unit" id="일반" type="radio" name="centerGroupType" value="N" <?=( $centerGroupType == "N" )? "checked": "" ?>><label for="일반">일반</label>
										<input class="i_unit" id="지정" type="radio" name="centerGroupType" value="G" <?=( $centerGroupType == "G" )? "checked": "" ?>><label for="지정">지정고사장 (단체접수 등)</label>
									</div>
								</td>
							</tr>
							<tr>
								<th>사용여부</th>
								<td>
									<div class="item">
										<input class="i_unit" id="O" type="radio" name="useCHK" value="O" <?=( $useCHK == "O" )? "checked": "" ?>><label for="O">사용</label>
										<input class="i_unit" id="X" type="radio" name="useCHK" value="X" <?=( $useCHK == "X" )? "checked": "" ?>><label for="X">사용 안함</label>
									</div>
								</td>
							</tr>
							<tr>
								<th>BEP</th>
								<td>
									<div class="item">
										<input style="width: 100px;" type="text" name="BEP" value="<?=$BEP?>">
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
			<!-- //테이블1-->
				</div>
</form>
				<div class="wrap_btn">
					<button class="btn_fill btn_md" type="button"  id="btnWrite"><?=( $proc == "write" )? "등록": "수정" ?></button>
					<button class="btn_line btn_md" type="button"  id="btnCancel">취소</button>
					<?php
					if($proc == "modify"){
					?>
					<button class="btn_fill btn_md" type="button"  id="btnDelete" style="float:right">삭제</button>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function () {

	/*입력/수정 유효성 체크*/
	$('#frmWrite').validate({
		onfocusout: false,
		rules: {
			centerGroupName: {
				required: true    //필수조건
			}, SBCenterGroup: {
				required: true    //필수조건
			}, centerMap: {
				required: true    //필수조건
			}, zipcode: {
				required: true    //필수조건
			}, address1: {
				required: true    //필수조건
			} /*, 사용제한: {
				required: true    //필수조건
			}*/, useCHK: {
				required: true    //필수조건
			}, BEP: {
				required: true    //필수조건
			}
		}, messages: {
			centerGroupName: {
				required: "그룹명을 입력 해주세요."
			}, SBCenterGroup: {
				required: "그룹종류를 선택 해주세요."
			}, centerMap: {
				required: "센터약도를 입력 해주세요."
			}, zipcode: {
				required: "우편번호 찾기를 통해 주소를 입력 해주세요."
			}, address1: {
				required: "우편번호 찾기를 통해 주소를 입력 해주세요."
			} /*, 사용제한: {
				required: "사용제한을 선택 해주세요."
			}*/, useCHK: {
				required: "사용여부를 선택 해주세요."
			}, BEP: {
				required: "BEP를 입력 해주세요."
			}
		}, errorPlacement: function (error, element) {
		}, invalidHandler: function (form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				alert(validator.errorList[0].message);
				validator.errorList[0].element.focus();
			}
		}
	});
	/*입력/수정 유효성 체크 끝*/

	/*입력/수정 전송*/
	$("#btnWrite").on("click", function(){
		$("#frmWrite").submit();
	});
	/*입력/수정 전송 끝*/

	$("#btnCancel").on("click", function(){
		history.back(-1);
	});

	/*삭제*/
	$("#btnDelete").on("click", function(){
		if(confirm("삭제하시겠습니까?")){
			var centerGroupCode = $("input[name=centerGroupCode]").val();
			location.href = "examSetCenterGroupProc.php?proc=delete&centerGroupCode="+centerGroupCode;
		}else{
			return false;
		}
	});
	/*삭제 끝*/

	/*숫자만 입력*/
	common.string.onlyNumber($("input[name=BEP]"));
	/*숫자만 입력 끝*/

});
</script>

<!--right //-->
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
