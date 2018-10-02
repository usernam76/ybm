<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "209";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	if( $cPageRoleRw != "W" ){	//쓰기 권한 필요
		fnShowAlertMsg("페이지 쓰기 권한이 없습니다.", "location.href = '/main.php';", true);
	}

	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
//	$valueValid = [
//		'idx' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 3],
//		'userId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 2, 'max' => 20]
//	];
	$resultArray = fnGetRequestParam($valueValid);

	$proc = "write";
	$options1	= "5,6급(초급)";
	$options2	= "3,4급(중급)";
	$options3	= "1,2급(고급)";

	if ( $pGoodsCode != "" ){
		$sql  = " SELECT ";
		$sql .= "	goods_code, SB_goods_type1, SB_goods_type2, goods_name, disp_goods_name, disp_price, sell_price, use_CHK	";
		$sql .= " FROM Goods_info (nolock)	";
		$sql .= " WHERE goods_code = :goodsCode ";

		$pArray[':goodsCode'] = $pGoodsCode;

		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		if( count($arrRows) == 0 ){
			fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
		}else{
			$proc		= "modify";
		}

		$sql  = " SELECT ";
		$sql .= "	option_name	";
		$sql .= " FROM Goods_option (nolock)	";
		$sql .= " WHERE goods_code = :goodsCode	";
		$sql .= " ORDER BY option_name DESC		";

		$arrRowsOptions = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		for($i=0;$i<sizeof($arrRowsOptions);$i++){
			${"options".($i+1)} = $arrRowsOptions[$i]['option_name'];
		}

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
			<h3 class="title">단과시험 <?=( $proc == "write" )? "입력": "수정" ?></h3>

<form name="frmWrite" id="frmWrite" action="/language/danProc.php" method="post"> 
<input type="hidden" name="proc" value="<?=$proc?>">
<input type="hidden" name="goodsCode" value="<?=$pGoodsCode?>">

			<!-- 세로형 테이블 -->
			<div class="box_bs">
				<div class="wrap_tbl">
					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width: 180px;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
							<tr>
								<th>분류</th>
								<td colspan="3">
									<div class="item">
										<select id="sbGoodsType1" name="sbGoodsType1"></select>
										<select id="sbGoodsType2" name="sbGoodsType2"></select>
									</div>
								</td>
							</tr>
							<tr>
								<th>상품명</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 300px;" type="text" name="goodsName" value="<?=$arrRows[0]['goods_name']?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>노출명</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 300px;" type="text" name="dispGoodsName" value="<?=$arrRows[0]['disp_goods_name']?>">
									</div>
								</td>
							</tr>
							<tr id="options">
								<th>급수 분류</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 100px;" type="text" name="options1" value="<?=$options1?>">
										<input style="width: 100px;" type="text" name="options2" value="<?=$options2?>">
										<input style="width: 100px;" type="text" name="options3" value="<?=$options3?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>정가</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 300px;" type="text" name="dispPrice" value="<?=$arrRows[0]['disp_price']?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>판매가</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 300px;" type="text" name="sellPrice" value="<?=$arrRows[0]['sell_price']?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>발급구분</th>
								<td colspan="3">
									<div class="item">
										<input class="i_unit" type="radio" name="useChk" value="O" <?=( $proc == "write" || $arrRows[0]['use_CHK'] == 'O' )? "CHECKED": "" ?> ><label for="">노출</label>
										<input class="i_unit" type="radio" name="useChk" value="X" <?=( $arrRows[0]['use_CHK'] == 'X' )? "CHECKED": "" ?> ><label for="">노출 안 함</label>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
</form> 
			<!-- 세로형 테이블 //-->

			<div class="wrap_btn">
				<button type="button" class="btn_fill btn_lg" id="btnWrite"><?=( $proc == "write" )? "등록": "확인" ?></button>
				<button type="button" class="btn_line btn_lg" id="btnCancel">취소</button>
			</div>

		</div>
	</div>
</div>
<!--right //-->

<script type="text/javascript">
$(document).ready(function () {

	$('#frmWrite').validate({
        onfocusout: false,
        rules: {
            sbGoodsType1: {
                required: true    //필수조건
			}, sbGoodsType2: {
                required: true    //필수조건
			}, goodsName: {
                required: true    //필수조건
			}, dispGoodsName: {
                required: true    //필수조건
			}, dispPrice: {
                required: true    //필수조건
			}, sellPrice: {
                required: true    //필수조건
			}
        }, messages: {
			sbGoodsType1: {
				required: "분류를 선택해주세요."
			},sbGoodsType2: {
				required: "분류를 선택해주세요."
			}, goodsName: {
				required: "상품명을 입력해주세요."
			}, dispGoodsName: {
				required: "노출명을 입력해주세요."
			}, dispPrice: {
				required: "정가를 입력해주세요."
			}, sellPrice: {
				required: "판매가를 입력해주세요."
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

	$("#btnWrite").on("click", function () {
		$('#frmWrite').submit();
    });

	$("#btnCancel").on("click", function () {
		location.href = "/language/danList.php<?=fnGetParams().'currentPage='.$pCurrentPage?>";
	});

	var param = {
		"sbInfo" 			: "sbGoodsType1"	// SbInfo 정보
		, "sbKind" 			: "goods_type1"	// sbKind 정보
		, "optYn"			: "Y"			// 상단 옵션 사용여부(Y, N)
		, "firstOptVal"		: ""			// 상단 옵션  value
		, "firstOptLable"	: "선택"			// 상단 옵션  text
	}
	common.sys.setSbInfoCreate(param);

	var param = {
		"sbInfo" 			: "sbGoodsType2"	// SbInfo 정보
		, "sbKind" 			: "goods_type2"	// sbKind 정보
		, "optYn"			: "Y"			// 상단 옵션 사용여부(Y, N)
		, "firstOptVal"		: ""			// 상단 옵션  value
		, "firstOptLable"	: "선택"			// 상단 옵션  text
	}
	common.sys.setSbInfoCreate(param);

	$("#sbGoodsType1").val("<?=$arrRows[0]['SB_goods_type1']?>").change();
	$("#sbGoodsType2").val("<?=$arrRows[0]['SB_goods_type2']?>").change();

	if( "<?=$arrRows[0]['SB_goods_type1']?>" == "jet" ){
		$("#options").show();
	}else{
		$("#options").hide();
	}

	$("#sbGoodsType1").on("change", function () {
		if ( $("#sbGoodsType1").val() == "jet" ){
			$("#options").show();
		}else{
			$("#options").hide();
		}
	});

});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
