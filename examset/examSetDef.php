<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
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
		$where = " AND ". $pSearchType . " LIKE '%". $pSearchKey ."%' ";
	}

	$sql = " SELECT ";
	$sql .= " (SELECT COUNT(*) FROM [theExam].[dbo].[Def_exam_center] WHERE 1=1 ". $where." ) AS totalRecords ";
	$sql .= " , [center_code] ,[SB_center_cate] ,[link_center_code] ,[center_group_code] ,[SB_area] ,[center_name] ,[zipcode] ,[address],[memo] ,[use_CHK] ,[update_day] ,[ok_id] ,[okType] ";
	$sql .= " FROM [theExam].[dbo].[Def_exam_center]";
	$sql .= " WHERE use_CHK='O' ". $where;
	$sql .= " ORDER BY update_day DESC ";
	$sql .= " OFFSET ( ".$currentPage." - 1 ) * ".$recordsPerPage." ROWS ";
	$sql .= " FETCH NEXT ".$recordsPerPage." ROWS ONLY ";

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	if ( count($arrRows) > 0 ){
		$totalRecords	= $arrRows[0][totalRecords];
		$totalPage		= ceil($totalRecords / $recordsPerPage);
	}

	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>

<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">지역/고사장 관리 - 고사장 관리</h3>
<form name="frmSearch" id="frmSearch" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
			<div class="box_sort2">
				<strong class="part_tit">검색</strong>
				<div class="item line">
					<select style="width:100px;">  
						<option value="center_name "		<?=( $pSearchKey == '	center_name '	)? "SELECTED": "" ?>>고사장명</option> 
						<option value="	link_center_code "		<?=( $pSearchKey == '	link_center_code '	)? "SELECTED": "" ?>>고사장코드</option> 
						<option value="address "		<?=( $pSearchKey == 'address '	)? "SELECTED": "" ?>>주소</option> 
					</select>
					<input style="width:200px;" type="text"  id="searchKey" name="searchKey" value="<?=$pSearchKey?>">
					<button class="btn_fill btn_md" type="button" id="btnSearch">조회</button>	
					<span class="fl_r"><button class="btn_line btn_md" type="button">고사장 추가</button></span>
				</div>
				<strong class="part_tit">필터</strong>
				<div class="item">
					<select style="width:100px;">  
						<option>강원</option> 
						<option>경기</option> 
					</select>
					<select style="width:100px;">  
						<option>인천</option> 
						<option>강릉</option> 
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
							<col style="width:100px">
							<col style="width:100px">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:80px">
						</colgroup>
						<thead>
							<tr>
								<th>번호</th>
								<th>고사장코드</th>
								<th>시군구</th>
								<th>고사장명</th>
								<th>주소</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>

<?php
	$no = $totalRecords - ( ( $currentPage - 1 ) * $recordsPerPage );
	foreach($arrRows as $data) {
?>

							<tr>
								<td><?=$no--;?></td>
								<td><?=$data['link_center_code']?></td>
								<td><?=$data['SB_area']?></td>
								<td><?=$data['center_name']?></td>
								<td>[<?=$data['zipcode']?>] <?=$data['address']?></td>
								<td>
									<button type="button" class="btn_fill btn_sm btnModify">수정</button>
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
<script>
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


});

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
