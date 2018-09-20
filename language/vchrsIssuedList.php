<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "207";	//메뉴고유번호
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
			$where = " AND ( B.Coup_no LIKE '%". $pSearchKey ."%' ) ";
		}else{
			$where = " AND ". $pSearchType . " LIKE '%". $pSearchKey ."%' ";
		}
	}

	$sql  = " SELECT COUNT(*) AS totalRecords ";
	$sql .= " FROM Coup_Info as A (nolock) 	";
	$sql .= " JOIN Coup_List as B (nolock) on A.Coup_code = B.Coup_code	";
	$sql .= " LEFT OUTER JOIN Coup_List_User as C (nolock) on B.Coup_code = C.Coup_code AND B.Coup_no = C.Coup_no	";
	$sql .= " LEFT OUTER JOIN Member_certi as D (nolock) on C.Member_id = D.Member_id	";
	$sql .= " WHERE SB_coup_cate = '응시권' AND A.Coup_code = :coupCode ". $where."	";

	$pArray[':coupCode']	= $pCoupCode;
	$arrRowsTotal = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	$sql  = " SELECT ";
	$sql .= "	A.Coup_code, A.coup_name, B.Coup_no, B.reg_day, C.Member_id, C.use_day, D.Userid ";
	$sql .= " FROM Coup_Info as A (nolock) 	";
	$sql .= " JOIN Coup_List as B (nolock) on A.Coup_code = B.Coup_code	";
	$sql .= " LEFT OUTER JOIN Coup_List_User as C (nolock) on B.Coup_code = C.Coup_code AND B.Coup_no = C.Coup_no	";
	$sql .= " LEFT OUTER JOIN Member_certi as D (nolock) on C.Member_id = D.Member_id	";
	$sql .= " WHERE SB_coup_cate = '응시권' AND A.Coup_code = :coupCode ". $where;
	$sql .= " ORDER BY B.Coup_no ASC ";
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
			<h3 class="title">응시권 발급 리스트</h3>

			<!-- sorting area -->
<form name="frmSearch" id="frmSearch" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
<input type="hidden" name="coupCode" value="<?=$pCoupCode?>">
			<div class="box_sort2">
				<strong class="part_tit">검색</strong>
				<div class="item line">
					<span class="fl_r">
						<?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_md' id='btnSms'", "문자발송")?>
						<?=fnButtonCreate($cPageRoleRw, "class='btn_fill_ex btn_md' id='btnExcel'", "Excel")?>
					</span>
					<select style="width: 200px;" name="searchType"> 
						<option value="">전체</option>
						<option value="B.Coup_no"	<?=( $pSearchType == 'B.Coup_no'	)? "SELECTED": "" ?> >쿠폰번호</option> 
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
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:50px">
							<col style="width:80px">
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
								<th><input class="i_unit" id="allCheck" type="checkbox"></th>
								<th>번호</th>
								<th>응시권명</th>
								<th>응시권번호</th>
								<th>사용자ID</th>
								<th>응시권등록일</th>
								<th>상태</th>
								<th>사용일</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>
<?php
	$no = $totalRecords - ( ( $currentPage - 1 ) * $recordsPerPage );
	foreach($arrRows as $data) {
		$chkDsblStyle = "";
		$btnDsblStyle1 = "";
		$btnDsblStyle2 = "";
		if( $data['Member_id'] == "" ){
			$btnDsblStyle2 = "disabled style='color: #a4a8af'";
		}else{
			if( $data['use_day'] != "" ){
				$btnDsblStyle2 = "disabled style='color: #a4a8af'";
			}
			$btnDsblStyle1 = "disabled style='color: #a4a8af'";
		}

		if(  $data['Member_id'] == "" || $data['use_day'] != "" ){
			$chkDsblStyle = "disabled";
		}

?>
							<tr>
								<td><input class="i_unit" id="lv1" type="checkbox" <?=$chkDsblStyle?>></td>
								<td><?=$no--?></td>
								<td><?=$data['coup_name']?></td>
								<td><?=$data['Coup_no']?></td>
								<td><?=$data['Userid']?></td>
								<td><?=$data['reg_day']?></td>
								<td><?=( $data['use_day'] == '' )? "미사용": "사용" ?></td>
								<td><?=$data['use_day']?></td>
								<td>
									<?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_sm btnUserAddPop' ".$btnDsblStyle1, "사용자등록")?>
									<?=fnButtonCreate($cPageRoleRw, "class='btn_line btn_sm btnIssuedCancel' ".$btnDsblStyle2, "발급취소")?>
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
<!-- modal 팝업 :: statis_hour-->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content" style="width: 400px; height: 350px;">
	<span class="close"><img class="sml" src="/_resources/images/btn_x.png"></span>
	<div class="wrap_tbl">
		<div class="box_inform">
			<p class="txt_l">
				<span class="stit">사용자 등록</span>
			</p>
		</div>
		<table class="type02">
			<caption></caption>
			<colgroup>
				<col style="width: 30%;">
				<col style="width: auto;">
			</colgroup>
			<tbody>
				<tr>
					<th>응시권명</th>
					<td><span id="coupName"><span></td>
				</tr>
				<tr>
					<th>응시권번호</th>
					<td><span id="coupNo"><span></td>
				</tr>
				<tr>
					<th>사용자ID</th>
					<td>
						<div class="item">
							<input style="width:90%;" type="text" id="userId" name="userId">
						</div>
					</td>
				</tr>
				<tr>
					<th>비고</th>
					<td>
						<div class="item">
							<input style="width:90%;" type="text" id="memo" name="memo">
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="wrap_btn">
		<?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_md' id='btnUserAdd'", "확인")?>
		<button class="btn_line btn_md" id='btnUserCancel' type="button">취소</button>
	</div>
  </div>
</div>
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

	$("#allCheck").click(function(){
		if($("#allCheck").prop("checked")) {
			$("input[type=checkbox]").prop("checked",true); 
		}else{
			$("input[type=checkbox]").prop("checked",false); 
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

	$(".close").on("click", function () {
		$("#myModal").hide();
	});
	$("#btnUserCancel").on("click", function () {
		$("#myModal").hide();
	});

	$(".btnUserAddPop").on("click", function () {
		$("#coupName").text( $(this).parents("tr").children().eq(2).text() );
		$("#coupNo").text( $(this).parents("tr").children().eq(3).text() );
		$("#userId").val('');
		$("#memo").val('');

		$("#myModal").show();
	});

	$("#btnUserAdd").on("click", function () {
		var u = "/language/vchrsProc.php";
		var param = {
			"proc"		: "userAdd",
			"coupCode"	: "<?=$pCoupCode?>",
			"coupNo"	: $("#coupNo").text(),
			"userId"	: $("#userId").val(),
			"memo"		: $("#memo").val()
		};
		$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
			success: function(resJson) {
				if( resJson.result == 1 ){
					alert("사용자 등록 되었습니다.");
					location.reload();
				}else if( resJson.result == 2 ){
					alert("사용자ID가 존재하지 않습니다.");
				}else{
					alert("사용자 등록이 실패 하였습니다.");
				}
			},
			error: function(e) {
				alert("현재 서버 통신이 원할하지 않습니다.");
			}
		});
	});

	$(".btnIssuedCancel").on("click", function () {
		if (confirm("응시권 발급을 취소하시겠습니까?") == true){
			var u = "/language/vchrsProc.php";
			var param = {
				"proc"		: "issuedCancel",
				"coupCode"	: "<?=$pCoupCode?>",
				"coupNo"	: $(this).parents("tr").children().eq(3).text(),
				"userId"	: $(this).parents("tr").children().eq(4).text()
			};
			$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
				success: function(resJson) {
					if( resJson.result == 1 ){
						alert("발급 취소 되었습니다.");
						location.reload();
					}else{
						alert("발급 취소가 실패 하였습니다.");
					}
				},
				error: function(e) {
					alert("현재 서버 통신이 원할하지 않습니다.");
				}
			});
		}		
	});


});

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>

