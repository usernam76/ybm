<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "209";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
//	$valueValid = [
//		'idx' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 3],
//		'userId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 2, 'max' => 20]
//	];
	$resultArray = fnGetRequestParam($valueValid);

	$proc = "write";

	if ( $pGoodsCode != "" ){
		$sql  = " SELECT ";
		$sql .= "	goods_code, SB_goods_type, goods_name, disp_goods_name, disp_price, sell_price, use_CHK	";
		$sql .= " FROM Goods_info (nolock)	";
		$sql .= " WHERE goods_code = :goodsCode ";

		$pArray[':goodsCode'] = $pGoodsCode;

		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		if( count($arrRows) == 0 ){
			fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
		}else{
			$proc		= "modify";
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
			<h3 class="title">단과시험<?=( $proc == "write" )? "입력": "수정" ?></h3>

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
										<select id="sbGoodsType" name="sbGoodsType"></select>
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
            sbGoodsType: {
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
			sbGoodsType: {
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
		"sbInfo" 			: "sbGoodsType"	// SbInfo 정보
		, "sbKind" 			: "goods_type"	// sbKind 정보
		, "optYn"			: "Y"			// 상단 옵션 사용여부(Y, N)
		, "firstOptVal"		: ""			// 상단 옵션  value
		, "firstOptLable"	: "선택"			// 상단 옵션  text
	}
	common.sys.setSbInfoCreate(param);

	$("#sbGoodsType").val("<?=$arrRows[0]['SB_goods_type']?>").change();

});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
