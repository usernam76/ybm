<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	

	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);
	

	if( $pCurrentPage > 0 ){
		$currentPage = $pCurrentPage;
	}


	$where		= "";
	if( $pSearchKey != "" ){
		if($pSearchType == "center_name"){
			$where = " AND STUFF( (SELECT '^' + center_name AS [text()] FROM [theExam].[dbo].[Def_exam_center] WITH (NOLOCK) WHERE center_group_code=DEG.center_group_code  FOR XML PATH('')), 1, 1, '') LIKE '%". $pSearchKey ."%' ";
		}else{
			$where = " AND ". $pSearchType . " LIKE '%". $pSearchKey ."%' ";
		}
	}

	if($pSBCenterGroup != "" && $pSBCenterGroup != "전체"){
		$where .= "AND SB_center_group = '".$pSBCenterGroup."'";
	}


	$coulmn = " 
	DEG.center_group_code, 
	DEG.center_group_name, 
	DEG.SB_center_group, 
	DEG.center_map, 
	DEG.BEP, 
	DEG.use_CHK,
	";
	
	$sql = " SELECT ";
	$sql .=  $coulmn;
	$sql .= " STUFF( ";
	$sql .= " (SELECT '^' + center_name AS [text()] FROM [theExam].[dbo].[Def_exam_center] ";
	$sql .= " WITH (NOLOCK) ";
	$sql .= " WHERE center_group_code=DEG.center_group_code ";
	$sql .= " FOR XML PATH('')), 1, 1, '') AS CBTCenterNames ";
	$sql .= "  from [theExam].[dbo].[Def_exam_center_Group] AS DEG ";
	$sql .= " WHERE 1=1 ". $where;
	$sql .= " ORDER BY case when use_CHK = 'O' then 1 else 2 end ASC ";
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>
<style>
.notUseCenter, .notUseCenter td{background-color: #a4a8af !important;}
</style>

<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">센터그룹관리</h3>
<form name="frmSearch" id="frmSearch" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
			<div class="box_sort2">
				<strong class="part_tit">검색</strong>
				<div class="item line">
					<select name="searchType" style="width:100px;">  
						<option value="center_group_name" <?=( $pSearchType == 'center_group_name')? "SELECTED": "" ?>>그룹명</option> 
						<option value="center_map" <?=( $pSearchType == 'center_map' )? "SELECTED": "" ?>>센터약도</option> 
						<option value="center_name" <?=( $pSearchType == 'center_name' )? "SELECTED": "" ?>>센터명</option> 
					</select>
					<input style="width:200px;" type="text"  id="searchKey" name="searchKey" value="<?=$pSearchKey?>">
					<button class="btn_fill btn_md" type="button"  id="btnSearch">조회</button>	
					<span class="fl_r">
					<?=fnButtonCreate($cPageRoleRw, "class='btn_line btn_md' id='btnWrite'", "그룹추가")?>
					</span>
				</div>
				<strong class="part_tit">필터</strong>
				<div class="item">
					<select name="SBCenterGroup" id="SBCenterGroup" style="width:200px;">
						<option>전체</option> 
						<option <?= ($pSBCenterGroup=="YBM직영CBT") ? "SELECTED" : ""?> value="YBM직영CBT">YBM직영CBT</option> 
						<option <?= ($pSBCenterGroup=="YBM어학원CBT") ? "SELECTED" : ""?> value="YBM어학원CBT">YBM어학원CBT</option> 
						<option <?= ($pSBCenterGroup=="4년제대학") ? "SELECTED" : ""?> value="4년제대학">4년제대학</option> 
						<option <?= ($pSBCenterGroup=="2~3년제대학") ? "SELECTED" : ""?> value="2~3년제대학">2~3년제대학</option> 
						<option <?= ($pSBCenterGroup=="중고교") ? "SELECTED" : ""?> value="중고교">중고교</option> 
						<option <?= ($pSBCenterGroup=="직업학교학원") ? "SELECTED" : ""?> value="직업학교학원">직업학교학원</option> 
					</select>
				</div>
			</div>
</form>
			<!-- sorting area -->
			<!-- 테이블1 -->
			<div class="box_bs">
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:80px">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
						</colgroup>
						<thead>
							<tr>
								<th>번호</th>
								<th>그룹명</th>
								<th>센터약도</th>
								<th>일반/지정</th>
								<th>BEP</th>
								<th>센터</th>
								<th>센터변경</th>
							</tr>
						</thead>
						<tbody>
<?php
	$no=1;
	foreach($arrRows as $data) {
		$notUseStyle = "";
		if($data["use_CHK"] == "X"){
			$notUseStyle = "class='notUseCenter'";
		}

		$groupCenter = str_replace("^","<br />", $data["CBTCenterNames"]);



?>
							<tr <?=$notUseStyle?>>
								<td><?=$no++?></td>
								<td><a href="./examSetCenterGroupWrite.php?proc=modify&centerGroupCode=<?=$data["center_group_code"]?>"><?=$data["center_group_name"]?></a></td>
								<td><?=$data["center_map"]?></td>
								<td><?=$data["일반"]?></td>
								<td><?=$data["BEP"]?></td>
								<td><?=$groupCenter?></td>
								<td><a href="#" id="myBtn">센터변경</a></td>
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
  <div class="modal-content">
    <span class="close"><img src="../resources/images/btn_x.png"></span>
    <!-- 팝업내용 -->
	<div class="wrap_tbl">
		<div class="box_inform">
			<p class="txt_l">
				<strong>[단체] 경북전문대학교/공학3관 1층</strong>
			</p>
		</div>
		<div class="item pad_tb15">
			<input class="i_unit" id="agree" type="checkbox"><label for="agree">그룹 미지정 센터만 보기</label>
		</div>
		<div class="colm2">
			<div class="l_cont">
				<div class="wrap_tbl">
					<table class="type01">
						<tbody>
							<tr>
								<th>전체 인증센터</th>
							</tr>
							<tr>
								<td>
									<ul class="list_area"><!--선택시 on-->
										<li><a href="#" class="on">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<span class="arr_btn_sm">
				<button class="btn_arr" type="button"><strong class="fs_sm">▶</strong></button><br>
				<button class="btn_arr" type="button" style="margin-top:5px;"><strong class="fs_sm">◀</strong></button>
			</span>
			<div class="r_cont">
				<div class="wrap_tbl">
					<table class="type01">
						<tbody>
							<tr>
								<th>해당 그룹센터</th>
							</tr>
							<tr>
								<td>
									<ul class="list_area">
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
										<li><a href="#">[단체]경북전문대학교/공학3관 1층A</a></li>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
	</div>
   <!-- 팝업내용 :: goal //-->
  </div>
  
</div>
<script type="text/javascript">
/*
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
*/


$(document).ready(function () {

	/* 검색  유효성 체크*/
	$('#frmSearch').validate({
		onfocusout: false,
		rules: {
			searchKey: {
				required: true    //필수조건
			}
		}, messages: {
			searchKey: {
				required: "검색어를 입력해주세요."
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
	/* 검색  유효성 체크 끝*/

	/* 검색 */
	$("#btnSearch").on("click", function(){
		$("#searchKey").val( $.trim($("#searchKey").val()) );
		$('#frmSearch').submit();
    });
	/* 검색 끝*/

	/* 필터 검색 */
	$("#SBCenterGroup").on("change", function(){
		var searchType = $("select[name=searchType]").val();
		var searchKey = $("input[name=searchKey]").val();
		var SBCenterGroup = $("#SBCenterGroup").val();
		location.href = "?searchType="+searchType+"&searchKey="+searchKey+"&SBCenterGroup="+SBCenterGroup;
	});
	/* 필터 검색 끝 */

	/* 센터그룹 추가 */
	$("#btnWrite").on("click", function(){
		location.href = "./examSetCenterGroupWrite.php";
	});
	/* 센터그룹 추가 끝 */
		

});


</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
