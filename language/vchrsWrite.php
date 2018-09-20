<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "207";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
//	$valueValid = [
//		'idx' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 3],
//		'userId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 2, 'max' => 20]
//	];
	$resultArray = fnGetRequestParam($valueValid);

	$proc = "write";

	$admNm		= $_SESSION["admNm"];
	$deptName	= $_SESSION["deptName"];
	$svc		= 44500;

	if ( $pCoupCode != "" ){
		$sql  = " SELECT ";
		$sql .= "	SB_coup_cate, B.Adm_name, C.Dept_Name, A.regi_day, A.doc_num, A.coup_name, A.SB_coup_type, svc_type, svc ";
		$sql .= "	, CONVERT(CHAR(10), A.usable_Startday, 23) AS usable_Startday, CONVERT(CHAR(10), A.usable_endday, 23) AS usable_endday	";
		$sql .= "	, coup_count, ok_CHK, free_CHK, comp_name, comp_mng, A.ok_id, A.ok_day, E.Adm_name AS okNm	";
		$sql .= "	, ( SELECT area_data FROM Coup_Area_Data (nolock) WHERE A.Coup_code = Coup_code AND SB_use_area = 'usr' ) AS areaDataUsr	";
		$sql .= "	, ( SELECT area_data FROM Coup_Area_Data (nolock) WHERE A.Coup_code = Coup_code AND SB_use_area = 'usa' ) AS areaDataUsa	";
		$sql .= " FROM Coup_Info as A (nolock) 	";
		$sql .= " JOIN Adm_info as B (nolock) on A.apply_id = B.Adm_id and A.applyType = B.AdmType 	";
		$sql .= " JOIN Adm_Dept_Info as C (nolock) on B.Dept_Code = C.Dept_Code 	";
		$sql .= " JOIN Coup_Service as D (nolock) on A.Coup_code = D.Coup_code	";
		$sql .= " LEFT OUTER JOIN Adm_info as E (nolock) on A.ok_id = E.Adm_id and A.okType = E.AdmType 	";
		$sql .= " WHERE SB_coup_cate = '응시권' AND A.Coup_code = :coupCode ";

		$pArray[':coupCode'] = $pCoupCode;

		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		if( count($arrRows) == 0 ){
			fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
		}else{
			$proc		= "modify";
			$admNm		= $arrRows[0]['Adm_name'];
			$deptName	= $arrRows[0]['Dept_Name'];
			$svc		= $arrRows[0]['svc'];

			$coupCount	= $arrRows[0]['coup_count'];
			if( $coupCount == -1 ){
				$coupCount = "";
			}
		}

		$sql  = " SELECT ";
		$sql .= "	A.goods_code, Exam_code ";
		$sql .= " FROM Goods_info as A (nolock) 	";
		$sql .= " LEFT OUTER JOIN Exam_Goods B (nolock) ON A.goods_code = B.goods_code	";
		$sql .= "	AND Exam_code IN ( SELECT area_data FROM Coup_Area_Data WHERE Coup_code = :coupCode AND SB_use_area = 'enm' )		";
		$sql .= " WHERE A.goods_code IN ( SELECT area_data FROM Coup_Area_Data WHERE Coup_code = :coupCode2 AND SB_use_area = 'pro' )	";
		$sql .= " ORDER BY A.goods_code, B.Exam_code 	";

		$pArray[':coupCode2'] = $pCoupCode;

		$arrRowsGoods = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
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
			<h3 class="title">응시권 <?=( $proc == "write" )? "발급": "수정" ?></h3>

<form name="frmWrite" id="frmWrite" action="/language/vchrsProc.php" method="post"> 
<input type="hidden" name="proc" value="<?=$proc?>">
<input type="hidden" name="coupCode" value="<?=$pCoupCode?>">
<input type="hidden" name="sbCoupCate" value="응시권">
<input type="hidden" name="svcType" value="S-">
<input type="hidden" name="sbCoupType" value="OG">

			<!-- 세로형 테이블 -->
			<div class="box_bs">
				<div class="wrap_tbl">
					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width: 180px;">
							<col style="width: auto;">
							<col style="width: 180px;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
							<tr>
								<th>신청자</th>
								<td><?=$admNm?></td>
								<th>부서</th>
								<td><?=$deptName?></td>
							</tr>
							<tr>
								<th>기안문서번호</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 300px;" type="text" name="docNum" value="<?=$arrRows[0]['doc_num']?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>응시권명</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 300px;" type="text" name="coupName" value="<?=$arrRows[0]['coup_name']?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>시험</th>
								<td colspan="3" id="goodsInfoList">
									<div class="item divGoodsInfo">
										<select class="goodsInfo" name="goodsInfo[]"></select>&nbsp;
										<label for="">회차설정</label>
										<select name="examNums[]"><option value="">전체</option></select>
										<?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_sm btnGoodsAdd'", "추가")?>
									</div>
								</td>
							</tr>
							<tr>
								<th>금액</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 100px;" type="text" class="onlyNumber2" name="svc" value="<?=$svc?>"> 원								
									</div>
								</td>
							</tr>
							<tr>
								<th>수량</th>
								<td colspan="3">
									<div class="item">
										<label for=""><input style="width: 100px;" type="text" class="onlyNumber2" id="coupCount" name="coupCount" value="<?=$coupCount?>" > 매</label>
									</div>									
								</td>
							</tr>
							<tr>
								<th>사용기간</th>
								<td colspan="3">									
									<div class="item pad_t10"> <!-- 활성화 btn_sm_bg_on -->
										<button class="btn_sm_bg_grey btnDaySet" type="button" data-sDay="usableStartday" data-eDay="usableEndday" data-dayKind="0" data-dayType="day" data-day="7">1주일</button>
										<button class="btn_sm_bg_grey btnDaySet" type="button" data-sDay="usableStartday" data-eDay="usableEndday" data-dayKind="0" data-dayType="month" data-day="1">1개월</button>
										<button class="btn_sm_bg_grey btnDaySet" type="button" data-sDay="usableStartday" data-eDay="usableEndday" data-dayKind="0" data-dayType="month" data-day="3">3개월</button>
										<button class="btn_sm_bg_grey btnDaySet" type="button" data-sDay="usableStartday" data-eDay="usableEndday" data-dayKind="0" data-dayType="month" data-day="6">6개월</button>
										<button class="btn_sm_bg_grey btnDaySet" type="button" data-sDay="usableStartday" data-eDay="usableEndday" data-dayKind="0" data-dayType="year" data-day="1">1년</button>
									</div>
									<div class="item pad_t10">
										<input style="width: 160px;" type="text" class="datepicker" readonly id="usableStartday" name="usableStartday" value="<?=$arrRows[0]['usable_Startday']?>" >
										&nbsp;&nbsp; ~ &nbsp;&nbsp;
										<input style="width: 160px;" type="text" class="datepicker" readonly id="usableEndday" name="usableEndday" value="<?=$arrRows[0]['usable_endday']?>" >
									</div>
								</td>
							</tr>
							<tr>
								<th>정산구분</th>
								<td colspan="3">
									<div class="item">
										<input class="i_unit" type="radio" name="freeChk" value="0" <?=( $arrRows[0]['free_CHK'] == 0	)? "CHECKED": "" ?>><label for="">무료발급</label>
										<input class="i_unit" type="radio" name="freeChk" value="1" <?=( $arrRows[0]['free_CHK'] == 1	)? "CHECKED": "" ?>><label for="">유료결제</label>
									</div>
								</td>
							</tr>
							<tr>
								<th>업체</th>
								<td colspan="3">
									<input style="width:300px;" type="text" name="compName" value="<?=$arrRows[0]['comp_name']?>">
								</td>
							</tr>
							<tr>
								<th>업체담당자</th>
								<td colspan="3">
									<input style="width:300px;" type="text" name="compMng" value="<?=$arrRows[0]['comp_mng']?>">
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
            docNum: {
                required: true    //필수조건
			}, coupName: {
                required: true    //필수조건
			}, sbCoupCate: {
                required: true    //필수조건
			}, svc: {
                required: true    //필수조건
			}, coupCount: {
                required: true    //필수조건
			}, compName: {
                required: true    //필수조건
			}, compMng: {
                required: true    //필수조건
			}

        }, messages: {
			docNum: {
				required: "기안문서번호를 입력해주세요."
			}, coupName: {
				required: "응시권명을 입력해주세요."
			}, sbCoupCate: {
				required: "응시권종류를 입력해주세요."
			}, svc: {
				required: "금액을 입력해주세요."
			}, coupCount: {
				required: "수량를 입력해주세요."
			}, compName: {
				required: "업체를 입력해주세요."
			}, compMng: {
				required: "업체담당자를 입력해주세요."
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
        }, submitHandler: function (form) {
			var returnNow = false;

			$(".divGoodsInfo").each( function() {
				var jGoodsInfo	= $(this).find("select").eq(0).val();

				if( jGoodsInfo == ""  ){
					alert("시험을 선택해주세요.");

					returnNow = true;
					return false;
				}
			});
			if( returnNow ){
				return false;
			}

			form.submit();
        }
    });


	$("#btnWrite").on("click", function () {
		$('#frmWrite').submit();
    });

	$("#btnCancel").on("click", function () {
		location.href = "/language/vchrsList.php<?=fnGetParams().'currentPage='.$pCurrentPage?>";
	});

	$(".btnGoodsAdd").on("click", function () {
		var html = "";
		html += "<div class='item pad_t10 divGoodsInfo'>";
		html += "<select class='goodsInfo' name='goodsInfo[]'></select>&nbsp;&nbsp;<label for=''>회차설정</label>";
		html += "<select class='examNums' name='examNums[]'><option value=''>전체</option></select>&nbsp;";
		html += "<button type='button' class='btn_fill btn_sm btnExamDel'>삭제</button>";
		html += "</div>";

		$("#goodsInfoList").append( html );

		$('#goodsInfoList div.divGoodsInfo').last().children('select').eq(0).html( common.sys.setComboOptHtml(goodsInfoList, "Y", "", "선택") );
	});

	$(document).on("click", ".btnExamDel", function () {
		$(this).parent('div.divGoodsInfo').remove();
	});

	$(document).on("change", ".goodsInfo", function () { 
		$(this).parent().children('select').eq(1).html( common.sys.setComboOptHtml( common.sys.getExamInfoList( $(this).val() ), "Y", "", "전체") );
	});

	$(document).on("change", ".examNums", function () { 

		var returnNow		= false;
		var jExamNums		= $(this).val();

		if ( jExamNums != "" ){			
			$(this).val("");

			$(".divGoodsInfo").each( function() {
				if( jExamNums == $(this).find("select").eq(1).val()  ){
					alert("동일한 시험 회차가 존재합니다." );

					returnNow = true;
					return false;
				}
			});
			if( returnNow ){
				jExamNums = "";
			}
		}

		$(this).val( jExamNums );
	});

	//사용기간 디폴트 세팅
	if( $("#usableStartday").val() == "" ){
		$(".btnDaySet").eq(1).trigger("click"); 
	}

	//시험정보 세팅
	var goodsInfoList	= common.sys.getGoodsInfoList();
	$('.goodsInfo').html( common.sys.setComboOptHtml(goodsInfoList, "Y", "", "선택") );
<?php
	$i = 0;
	if( count($arrRowsGoods) > 0 ){
		foreach($arrRowsGoods as $data) {
			if( $i > 0 ){
				echo "$('.btnGoodsAdd').trigger('click'); ";
				echo "$('#goodsInfoList div.divGoodsInfo').last().children('select').eq(0).html( common.sys.setComboOptHtml(goodsInfoList, 'Y', '', '선택') ); ";
			}

			echo "$('#goodsInfoList div.divGoodsInfo').last().children('select').eq(0).val('".$data['goods_code']."'); ";

			echo "$('#goodsInfoList div.divGoodsInfo').last().children('select').eq(0).trigger('change'); ";

			echo "$('#goodsInfoList div.divGoodsInfo').last().children('select').eq(1).val('".$data['Exam_code']."'); ";
			$i++;
		}
	}

?>


});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
