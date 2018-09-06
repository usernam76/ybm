<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];

	$resultArray = fnGetRequestParam($valueValid);
	
	$totalRecords	= 0;		// 총 레코드 수
	$recordsPerPage	= 10;		// 한 페이지에 보일 레코드 수
	$pagePerBlock	= 10;		// 한번에 보일 페이지 블럭 수
	$currentPage	= 1;		// 현재 페이지
	$totalPage		= 1;
	if( $pCurrentPage > 0 ){
		$currentPage = $pCurrentPage;
	}

	$where		= "";
	if( $pSearchKey != "" ){
		if ( $pSearchType == "" ){
			$where = " AND ( coup_name LIKE '%". $pSearchKey ."%' OR Dept_Name LIKE '%". $pSearchKey ."%' ) ";
		}else{
			$where = " AND ". $pSearchType . " LIKE '%". $pSearchKey ."%' ";
		}
	}

	$sql  = " SELECT COUNT(*) AS totalRecords ";
	$sql .= " FROM Coup_Info as A (nolock) 	";
	$sql .= " JOIN Adm_info as B (nolock) on A.apply_id = B.Adm_id and A.applyType = B.AdmType 	";
	$sql .= " JOIN Adm_Dept_Info as C (nolock) on B.Dept_Code = C.Dept_Code 	";
	$sql .= " JOIN Coup_Service as D (nolock) on A.Coup_code = D.Coup_code	";
	$sql .= " WHERE SB_coup_cate = '일반쿠폰' ". $where."	";
	$arrRowsTotal = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	$sql  = " SELECT ";
	$sql .= "	A.Coup_code, Dept_Name, '', coup_name, [dbo].f_Coup_scv_type_name(svc_type) AS svcNm, svc, A.usable_Startday, A.usable_endday ";
	$sql .= "	, coup_count, ok_CHK	";
	$sql .= "	, ( SELECT COUNT(*) FROM Coup_List_User (nolock) WHERE A.Coup_code = Coup_code AND use_day IS NOT NULL ) AS use_count	";
	$sql .= " FROM Coup_Info as A (nolock) 	";
	$sql .= " JOIN Adm_info as B (nolock) on A.apply_id = B.Adm_id and A.applyType = B.AdmType 	";
	$sql .= " JOIN Adm_Dept_Info as C (nolock) on B.Dept_Code = C.Dept_Code 	";
	$sql .= " JOIN Coup_Service as D (nolock) on A.Coup_code = D.Coup_code	";
	$sql .= " WHERE SB_coup_cate = '일반쿠폰' ". $where;
	$sql .= " ORDER BY A.Coup_code DESC ";
	$sql .= " OFFSET ( ".$currentPage." - 1 ) * ".$recordsPerPage." ROWS ";
	$sql .= " FETCH NEXT ".$recordsPerPage." ROWS ONLY ";

	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	if ( count($arrRows) > 0 ){
		$totalRecords	= $arrRowsTotal[0]['totalRecords'];
		$totalPage		= ceil($totalRecords / $recordsPerPage);
	}	
?>
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>
<body>

<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">쿠폰 관리</h3>

			<!-- sorting area -->
<form name="frmSearch" id="frmSearch" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
			<div class="box_sort2">
				<strong class="part_tit">검색</strong>
				<div class="item line">
					<span class="fl_r">
						<?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_md' id='btnWrite'", "쿠폰 발급")?>
					</span>
					<select style="width: 200px;" name="searchType"> 
						<option value="">전체</option>
						<option value="coup_name"	<?=( $pSearchType == 'coup_name'	)? "SELECTED": "" ?> >쿠폰명</option> 
						<option value="Dept_Name"	<?=( $pSearchType == 'Dept_Name'	)? "SELECTED": "" ?> >부서</option> 
						<option value="coup_name"	<?=( $pSearchType == 'coup_name'	)? "SELECTED": "" ?> >사용처</option> 
					</select>
					<input style="width:300px;" type="text" id="searchKey" name="searchKey" value="<?=$pSearchKey?>">
					<button class="btn_fill btn_md" type="button" id="btnSearch">조회</button>	
				</div>
				<strong class="part_tit">필터</strong>
				<div class="item pad_t5">
					<input class="i_unit" id="all" type="radio" name="" value=""><label for="all">전체 (000)</label>
					<input class="i_unit" id="use" type="radio" name="" value=""><label for="use">사용가능 (00)</label>
					<input class="i_unit" id="end" type="radio" name="" value=""><label for="end">사용완료 (00)</label>
				</div>
			</div>
</form> 
			<!-- sorting area -->

			<!-- 테이블1 -->
			<div class="box_bs">
				<p class="fl_l pad_b10">총 <strong><?=$totalRecords?></strong> 건</p>
				<p class="item fl_r pad_b10">
					<select style="width: 200px;">  
						<option>정렬</option> 
					</select>
				</p>
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:200px">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
						</colgroup>
						<thead>
							<tr>
								<th>번호</th>
								<th>부서</th>
								<th>사용처</th>
								<th>쿠폰명</th>
								<th>할인</th>
								<th>유효기간</th>
								<th>발급</th>
								<th>사용</th>
								<th>미사용</th>
								<th>진행여부</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>
<?php
	$no = $totalRecords - ( ( $currentPage - 1 ) * $recordsPerPage );
	foreach($arrRows as $data) {
		$okChkNm = "";
		switch ( $data["ok_CHK"] ) {
			case 'O'	: $okChkNm = "승인"; break;
			case 'X'	: $okChkNm = "미승인"; break;
			case '-'	: $okChkNm = "대기"; break;
			default		: $okChkNm = ""; break;
		}
?>
							<tr>
								<td><?=$no--?></td>
								<td><?=$data['Dept_Name']?></td>
								<td></td>
								<td><a href="/language/couponView.php<?=fnGetParams().'currentPage='.$pCurrentPage?>&coupCode=<?=$data['Coup_code']?>"><?=$data['coup_name']?></td>
								<td><?=$data['svc'].$data['svcNm']?></td>
								<td><?=substr($data['usable_Startday'], 0, 10)?> ~ <?=substr($data['usable_endday'], 0, 10)?></td>
								<td><?=$data['coup_count']?></td>
								<td><?=$data['use_count']?></td>
								<td><?=$data['coup_count']-$data['use_count']?></td>
								<td><?=$okChkNm?></td>
								<td>
									<?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_sm btnIssuedList'", "발급리스트")?>
								</td>
							</tr>
<?php
	}
?>
					  </tbody>
					</table>
				</div>

				<!-- //page -->
				<?=fnPaginator($totalRecords, $recordsPerPage, $pagePerBlock, $currentPage)?>

				<div class="item r_txt">
					<input style="width: 40px;" type="text" id="goPageNo" value="<?=$currentPage?>"> / <?=$totalPage?> &nbsp;
					<button class="btn_line btn_sm" type="button" id="goPage">Go</button>	
				</div>
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

<script type="text/javascript">
$(document).ready(function () {

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

	$("#btnSearch").on("click", function(){
		$("#searchKey").val( $.trim($("#searchKey").val()) );

		$('#frmSearch').submit();
    });

	$("#goPage").on("click", function () {
		if( $("#goPageNo").val() > 0 && $("#goPageNo").val() <= <?=$totalPage?> ){
			location.href = "<?=$_SERVER['SCRIPT_NAME'].fnGetParams()?>currentPage="+$("#goPageNo").val();
		}else{
			alert("1 ~ <?=$totalPage?> 사이의 숫자를 입력해 주세요.")
		}
    });

	$("#btnWrite").on("click", function () {
		location.href = "/language/couponWrite.php";
	});

	$("#btnIssuedList").on("click", function () {
		location.href = "/language/couponIssuedList.php?admId="+admId;
	});

});

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>

