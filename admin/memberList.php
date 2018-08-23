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

	$sql = ' SELECT ';
	$sql .= ' (SELECT COUNT(*) FROM [theExam].[dbo].[Adm_info] WHERE 1=1 '. $where.' ) AS totalRecords ';
	$sql .= ' , Adm_id, Adm_name, Adm_Email, Reg_day, Login_day, Password_day ';
	$sql .= ' FROM [theExam].[dbo].[Adm_info] ';
	$sql .= ' WHERE 1=1 '. $where;
	$sql .= ' ORDER BY Reg_day DESC ';
	$sql .= ' OFFSET ( '.$currentPage.' - 1 ) * '.$recordsPerPage.' ROWS ';
	$sql .= ' FETCH NEXT '.$recordsPerPage.' ROWS ONLY ';

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	if ( count($arrRows) > 0 ){
		$totalRecords	= $arrRows[0][totalRecords];
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
			<h3 class="title">계정관리</h3>

			<!-- sorting area -->
<form name="searchFrm" id="searchFrm" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
			<div class="box_sort2">
				<strong class="part_tit">검색</strong>
				<div class="item line">
					<select style="width:200px;" name="searchType">  
						<option value="Adm_id"		<?=( $pSearchKey == 'Adm_id'	)? "SELECTED": "" ?> >아이디</option> 
						<option value="Adm_name"	<?=( $pSearchKey == 'Adm_name'	)? "SELECTED": "" ?> >이름</option> 
						<option value="Adm_Email"	<?=( $pSearchKey == 'Adm_Email'	)? "SELECTED": "" ?> >이메일</option> 
						<option value="Adm_Email"	<?=( $pSearchKey == 'Adm_Email'	)? "SELECTED": "" ?> >소속부서</option> 
					</select>
					<input style="width:300px;" type="text" id="searchKey" name="searchKey" value="<?=$pSearchKey?>">
					<button class="btn_fill btn_md" type="button" id="searchBtn">검색</button>	

					<span class="fl_r">
						<button class="btn_fill btn_md" type="button" id="writeBtn">등록</button>
					</span>
				</div>
			</div>
</form> 
			<!-- sorting area -->

			<!-- 테이블1 -->
			<div class="box_bs">
				<p class="fl_l pad_b10">총 <strong><?=$totalRecords?></strong> 건</p>
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:200px">
						</colgroup>
						<thead>
							<tr>
								<th>번호</th>
								<th>아이디</th>
								<th>이름</th>
								<th>이메일</th>
								<th>소속부서</th>
								<th>등록일</th>
								<th>만료일</th>
								<th>최종접속일</th>
								<th>사용여부</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>
<?php
	$no = $totalRecords - ( ( $currentPage - 1 ) * $recordsPerPage );
	foreach($arrRows as $data) {
?>
							<tr>
								<td><?=$no--?></td>
								<td><?=$data['Adm_id']?></td>
								<td><?=$data['Adm_name']?></td>
								<td><?=$data['Adm_Email']?></td>
								<td></td>
								<td><?=substr($data['Reg_day'], 0, 10)?></td>
								<td><?=fnCalDate($data['Password_day'], 'month', 1)?></td>
								<td><?=substr($data['Login_day'], 0, 10)?></td>
								<td><?=( fnDateDiff($data['Login_day'], '') <= 90 )? "Y": "N" ?></td>
								<td>
									<button type="button" class="btn_fill btn_sm modifyBtn">수정</button>
									<button type="button" class="btn_line btn_sm menuSetBtn">메뉴 설정</button>
									<button type="button" class="btn_fill btn_sm dsblBtn">해제</button>								
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

	$('#searchFrm').validate({
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

	$("#searchBtn").on("click", function(){
		$("#searchKey").val( $.trim($("#searchKey").val()) );

		$('#searchFrm').submit();
    });

	$("#goPage").on("click", function () {
		if( $("#goPageNo").val() > 0 && $("#goPageNo").val() <= <?=$totalPage?> ){
			location.href = "<?=$_SERVER['SCRIPT_NAME'].fnGetParams()?>currentPage="+$("#goPageNo").val();
		}else{
			alert("1 ~ <?=$totalPage?> 사이의 숫자를 입력해 주세요.")
		}
    });

	$("#writeBtn").on("click", function () {
		location.href = "./memberWrite.php";
	});

	$(".modifyBtn").on("click", function () {
		var admId = $(this).parents("tr").children().eq(1).text();

		location.href = "./memberWrite.php?admId="+admId;
	});

	$(".menuSetBtn").on("click", function () {
		var admId = $(this).parents("tr").children().eq(1).text();

		location.href = "./memberWrite.php?admId="+admId;
	});

	$(".dsblBtn").on("click", function () {
		var admId = $(this).parents("tr").children().eq(1).text();

		location.href = "./memberWrite.php?admId="+admId;
	});

});

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>

