<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "209";	//메뉴고유번호
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
			$where = " AND ( goods_name LIKE '%". $pSearchKey ."%' OR disp_goods_name LIKE '%". $pSearchKey ."%' OR Goods_code LIKE '%". $pSearchKey ."%' ) ";
		}else{
			$where = " AND ". $pSearchType . " LIKE '%". $pSearchKey ."%' ";
		}
	}

	$sql  = " SELECT COUNT(*) AS totalRecords ";
	$sql .= " FROM Goods_info as A (nolock) 	";
	$sql .= " WHERE pack_CHK = 'X' ". $where;
	$arrRowsTotal = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	$sql  = " SELECT ";
	$sql .= "	Goods_code, goods_name, disp_goods_name, sell_price, use_CHK ";
	$sql .= " FROM Goods_info as A (nolock) 	";
	$sql .= " WHERE pack_CHK = 'X' ". $where;
	$sql .= " ORDER BY update_day DESC ";
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
			<h3 class="title">상품 관리</h3>

			<!-- sorting area -->
<form name="frmSearch" id="frmSearch" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
			<div class="box_sort2">
				<strong class="part_tit">검색</strong>
				<div class="item line">
					<span class="fl_r">
						<?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_md' id='btnWrite'", "추가")?>
					</span>
					<select style="width: 200px;" name="searchType"> 
						<option value="">전체</option>
						<option value="goods_name"		<?=( $pSearchType == 'goods_name'		)? "SELECTED": "" ?> >상품명</option> 
						<option value="disp_goods_name"	<?=( $pSearchType == 'disp_goods_name'	)? "SELECTED": "" ?> >노출명</option> 
						<option value="Goods_code"		<?=( $pSearchType == 'Goods_code'		)? "SELECTED": "" ?> >단가시험코드</option> 
					</select>
					<input style="width:300px;" type="text" id="searchKey" name="searchKey" value="<?=$pSearchKey?>">
					<button class="btn_fill btn_md" type="button" id="btnSearch">조회</button>	
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
							<col style="width: 80px;">
							<col style="width: auto;">
							<col style="width: auto;">
							<col style="width: auto;">
							<col style="width: auto;">
							<col style="width: auto;">
							<col style="width: 80px;">
						</colgroup>
						<thead>
							<tr>
								<th>번호</th>
								<th>단가시험코드</th>
								<th>상품명</th>
								<th>노출명</th>
								<th>판매가</th>
								<th>판매</th>
								<th>수정</th>
							</tr>
						</thead>
						<tbody>
<?php
	$no = $totalRecords - ( ( $currentPage - 1 ) * $recordsPerPage );
	foreach($arrRows as $data) {
		$okChkNm = "";
		switch ( $data["use_CHK"] ) {
			case 'O'	: $okChkNm = "판매"; break;
			case 'X'	: $okChkNm = "미판매"; break;
			default		: $okChkNm = ""; break;

		}
?>
							<tr>
								<td><?=$no--?></td>
								<td><?=$data['Goods_code']?></td>
								<td><?=$data['goods_name']?></td>
								<td><?=$data['disp_goods_name']?></td>
								<td><?=number_format($data['sell_price'])?>원</td>
								<td><?=$okChkNm?></td>
								<td>
									<?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_sm btnModify'", "수정")?>
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
		location.href = "/language/danWrite.php";
	});

	$(".btnModify").on("click", function () {
		var goodsCode = $(this).parents("tr").children().eq(1).text();

		location.href = "/language/danWrite.php?goodsCode="+goodsCode;
	});

});

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>

