<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "210";	//메뉴고유번호
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

	if ( $pGoodsCode != "" ){
		$sql  = " SELECT ";
		$sql .= "	goods_code, SB_goods_type1, SB_goods_type2, goods_name, disp_goods_name, disp_price, sell_price, use_CHK	";
		$sql .= " FROM Goods_info (nolock)	";
		$sql .= " WHERE goods_code = :goodsCode ";

		$pArray[':goodsCode'] = $pGoodsCode;

		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		$sql  = " SELECT ";
		$sql .= "	GP.goods_price, GI.goods_code, GI.goods_name, GI.disp_goods_name, GI.disp_price, SI.SB_name AS sbGoodsTypeNm2	";
		$sql .= " FROM Goods_Pack AS GP (nolock)	";
		$sql .= " JOIN Goods_info AS GI (nolock) ON GP.goods_code = GI.goods_code	";
		$sql .= " JOIN SB_Info AS SI (nolock) ON SI.SB_kind = 'goods_type2' AND SI.SB_value = GI.SB_goods_type2	";
		$sql .= " WHERE pack_goods_code = :goodsCode ";

		$arrRows2 = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

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
			<h3 class="title">패키지시험 <?=( $proc == "write" )? "입력": "수정" ?></h3>

<form name="frmWrite" id="frmWrite" action="/language/packageProc.php" method="post"> 
<input type="hidden" name="proc" value="<?=$proc?>">
<input type="hidden" name="goodsCode" value="<?=$pGoodsCode?>">
<input type="hidden" id="dispPrice" name="dispPrice" value="">
<input type="hidden" id="sellPrice" name="sellPrice" value="">

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
				<div class="wrap_tbl pad_t10">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width: auto;">
							<col style="width: 220px;">
							<col style="width: auto;">
							<col style="width: auto;">
							<col style="width: auto;">
							<col style="width: auto;">
						</colgroup>
						<thead>
							<tr>
								<th>구분</th>
								<th>코드</th>
								<th>시험명</th>
								<th>정가</th>
								<th>할인가</th>
								<th>패키지금액</th>
							</tr>
						</thead>
						<tbody>
<?php
	if( $proc == "modify" ){
		foreach($arrRows2 as $data) {
?>
							<tr class="trDanList">
								<td><?=$data['sbGoodsTypeNm2']?></td>
								<td><input style="width: 100px;" type="text" name="goodsCodes[]" value="<?=$data['goods_code']?>" readonly><button class="btn_sm_bg_grey btnSearchPop" type="button">검색</button></td>
								<td><?=$data['disp_goods_name']?></td>
								<td><?=number_format($data['disp_price'])?>원</td>
								<td><?=number_format($data['disp_price']-$data['goods_price'])?>원</td>
								<td><input style="width: 100px;" type="text" class="onlyNumber2" name="goodsPrices[]" value="<?=$data['goods_price']?>">원</td>
							</tr>
<?php
		}
	}else{
?>
							<tr class="trDanList">
								<td></td>
								<td><input style="width: 100px;" type="text" name="goodsCodes[]" value="" readonly><button class="btn_sm_bg_grey btnSearchPop" type="button">검색</button></td>
								<td></td>
								<td>0원</td>
								<td>0원</td>
								<td><input style="width: 100px;" type="text" class="onlyNumber2" name="goodsPrices[]" value="0">원</td>
							</tr>
							<tr class="trDanList">
								<td></td>
								<td><input style="width: 100px;" type="text" name="goodsCodes[]" value="" readonly><button class="btn_sm_bg_grey btnSearchPop" type="button">검색</button></td>
								<td></td>
								<td>0원</td>
								<td>0원</td>
								<td><input style="width: 100px;" type="text" class="onlyNumber2" name="goodsPrices[]" value="0">원</td>
							</tr>
<?php	
	}
?>
							<tr>
								<td colspan="3" class="total">합계</td>
								<td class="total" id="danTotalDis">0원</td>
								<td class="total" id="danTotalHal">0원</td>
								<td class="total point" id="danTotalPack">0원</td>
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
<!-- modal 팝업 :: statis_hour-->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content" style="width: 600px; height: 500px;">
	<span class="close"><img class="sml" src="/_resources/images/btn_x.png"></span>
	<div class="wrap_tbl">
		<div class="box_inform">
			<p class="txt_l">
				<span class="stit">단과시험 코드</span>
			</p>
		</div>
		<!-- sorting area -->
		<div class="item line">
			<select id="searchType" name="searchType"> 
				<option value="">전체</option>
				<option value="goods_name"		>상품명</option> 
				<option value="disp_goods_name" >노출명</option> 
				<option value="goods_code"		>단가시험코드</option> 
			</select>
			<input style="width:200px;" type="text" id="searchKey" name="searchKey" value="">
			<button class="btn_fill btn_md" type="button" id="btnSearch">조회</button>	
		</div>
		<!-- sorting area -->
		<table class="type01">
			<caption></caption>
			<colgroup>
				<col style="width: auto;">
				<col style="width: auto;">
				<col style="width: auto;">
				<col style="width: auto;">
			</colgroup>
			<thead>
				<tr>
					<th>단가시험코드</th>
					<th>상품명</th>
					<th>노출명</th>
					<th>정가</th>
				</tr>
			</thead>
			<tbody id="mdDanList">
			</tbody>
		</table>
	</div>	
  </div>
</div>

<script type="text/javascript">
$(document).ready(function () {

	$('#frmWrite').validate({
        onfocusout: false,
        rules: {
            sbGoodsType1: {
                required: true    //필수조건
			},sbGoodsType2: {
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
		location.href = "/language/packageList.php<?=fnGetParams().'currentPage='.$pCurrentPage?>";
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
	
	$(".onlyNumber2").on("keypress keyup", function (){ 
		danTotal();
	});

	var danTotal = function(){
		var tmpDanDis = 0;
		var tmpDanHal = 0;
		var tmpDanPack = 0;

		var tmpDanTotalDis = 0;
		var tmpDanTotalHal = 0;
		var tmpDanTotalPack = 0;
		$(".trDanList").each( function() {
			tmpDanDis = parseInt( common.string.replace( $(this).children().eq(3).text(), ',', '') );
			tmpDanPack =  parseInt( $(this).children().eq(5).find("input").val() );
			tmpDanHal = tmpDanDis - tmpDanPack;

			$(this).children().eq(4).text( $.number(tmpDanHal)+"원" );

			tmpDanTotalDis = tmpDanTotalDis + tmpDanDis;
			tmpDanTotalPack = tmpDanTotalPack + tmpDanPack;
			tmpDanTotalHal = tmpDanTotalHal + tmpDanHal;
		});

		$("#danTotalDis").text( $.number(tmpDanTotalDis)+"원" );
		$("#danTotalPack").text( $.number(tmpDanTotalPack)+"원" );
		$("#danTotalHal").text( $.number(tmpDanTotalHal)+"원" );

		$("#dispPrice").val( tmpDanTotalDis );
		$("#sellPrice").val( tmpDanTotalPack );
	}
	danTotal();	

	$(".close").on("click", function () {
		$("#myModal").hide();
	});
	$("#btnUserCancel").on("click", function () {
		$("#myModal").hide();
	});

	var danList;
	$(".btnSearchPop").on("click", function () {
		danList = $(this);

		$("#searchType").val('');
		$("#searchKey").val('');

		$("#myModal").show();
		danSearch();
	});

	$("#btnSearch").on("click", function () {
		danSearch();
	});

	var danSearch = function(){

		var u = "/language/packageProc.php";
		var param = {
			"proc"			: "danSearch",
			"searchType"	: $("#searchType").val(),
			"searchKey"		: $("#searchKey").val()
		};
		$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
			success: function(resJson) {
				var list = resJson.result;
				var html = "";
				for(var i=0 ; i<list.length; i++){
					html = html + "<tr>";
					html = html + "<td><a class='mdGoodsCode' data-sbGoodsType='"+list[i].sbGoodsTypeNm2+"'>"+list[i].goods_code+"</a></td>";
					html = html + "<td>"+list[i].goods_name+"</td>";
					html = html + "<td>"+list[i].disp_goods_name+"</td>";
					html = html + "<td>"+$.number(list[i].disp_price)+"원</td>";
					html = html + "</tr>";
				}
				$("#mdDanList").html( html );
			},
			error: function(e) {
				alert("현재 서버 통신이 원할하지 않습니다.");
			}
		});
    };

	$(document).on("click",".mdGoodsCode",function(){
		var returnNow = false;

		var tmpCode = $(this).text();

		$(".trDanList").each( function() {
			if ( $(this).children().eq(1).find("input").val() == tmpCode ){
				alert("동일한 품목이 등록 되어 있습니다.");

				returnNow = true;
				return false;
			}
		});
		if( returnNow ){
			return;
		}

		var tmpPrice = $(this).parents("tr").children().eq(3).text();
		tmpPrice = common.string.replace( tmpPrice, ',', '');
		tmpPrice = common.string.replace( tmpPrice, '원', '');

		danList.parents("tr").children().eq(0).text( $(this).attr("data-sbGoodsType") );
		danList.parents("tr").children().eq(1).find("input").val( $(this).text() );
		danList.parents("tr").children().eq(2).text( $(this).parents("tr").children().eq(2).text() );
		danList.parents("tr").children().eq(3).text( $(this).parents("tr").children().eq(3).text() );
		danList.parents("tr").children().eq(4).text( '0원' );
		danList.parents("tr").children().eq(5).find("input").val( tmpPrice );

		danTotal();
		$("#myModal").hide();
	});

});

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
