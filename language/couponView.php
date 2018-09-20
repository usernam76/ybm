<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "204";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
//	$valueValid = [
//		'idx' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 3],
//		'userId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 2, 'max' => 20]
//	];
	$resultArray = fnGetRequestParam($valueValid);

	$sql  = " SELECT ";
	$sql .= "	B.Adm_name, C.Dept_Name, A.regi_day, A.doc_num, A.coup_name, A.SB_coup_type, F.SB_name AS sbCoupTypeNm, [dbo].f_Coup_scv_type_name(svc_type) AS svcNm, svc ";
	$sql .= "	, CONVERT(CHAR(10), A.usable_Startday, 23) AS usable_Startday, CONVERT(CHAR(10), A.usable_endday, 23) AS usable_endday ";
	$sql .= "	, coup_count, ok_CHK, comp_name, comp_mng, A.ok_id, A.ok_day, E.Adm_name AS okNm	";
	$sql .= "	, ( SELECT area_data FROM Coup_Area_Data (nolock) WHERE A.Coup_code = Coup_code AND SB_use_area = 'usr' ) AS areaDataUsr	";
	$sql .= "	, ( SELECT area_data FROM Coup_Area_Data (nolock) WHERE A.Coup_code = Coup_code AND SB_use_area = 'usa' ) AS areaDataUsa	";
	$sql .= " FROM Coup_Info as A (nolock) 	";
	$sql .= " JOIN Adm_info as B (nolock) on A.apply_id = B.Adm_id and A.applyType = B.AdmType 	";
	$sql .= " JOIN Adm_Dept_Info as C (nolock) on B.Dept_Code = C.Dept_Code 	";
	$sql .= " JOIN Coup_Service as D (nolock) on A.Coup_code = D.Coup_code	";
	$sql .= " LEFT OUTER JOIN Adm_info as E (nolock) on A.ok_id = E.Adm_id and A.okType = E.AdmType 	";
	$sql .= " LEFT OUTER JOIN SB_Info as F (nolock) on A.SB_coup_type = F.SB_value and F.SB_kind = 'coup_type' 	";
	$sql .= " WHERE A.SB_coup_cate != '응시권' AND A.Coup_code = :coupCode ";

	$pArray[':coupCode'] = $pCoupCode;

	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	if( count($arrRows) == 0 ){
		fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
	}

	$sql  = " SELECT ";
	$sql .= "	A.goods_code, B.Exam_code ";
	$sql .= "	,goods_name, CAST(Exam_num AS varchar(10))+'회 '+CONVERT(CHAR(8), Exam_day, 2)+'('+LEFT(DATENAME(DW, Exam_day),1)+')' AS examNumNm ";
	$sql .= " FROM Goods_info as A (nolock) 	";
	$sql .= " LEFT OUTER JOIN Exam_Goods B (nolock) ON A.goods_code = B.goods_code	";
	$sql .= "	AND B.Exam_code IN ( SELECT area_data FROM Coup_Area_Data WHERE Coup_code = :coupCode AND SB_use_area = 'enm' )		";
	$sql .= " LEFT OUTER JOIN Exam_Info C (nolock) ON B.Exam_code = C.Exam_code		";
	$sql .= " WHERE A.goods_code IN ( SELECT area_data FROM Coup_Area_Data WHERE Coup_code = :coupCode2 AND SB_use_area = 'pro' )	";
	$sql .= " ORDER BY A.goods_code, B.Exam_code 	";

	$pArray[':coupCode2'] = $pCoupCode;

	$arrRowsGoods = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	$goodsInfoList = "";

	foreach($arrRowsGoods as $data) {

		$goodsInfoList .= $data['goods_name'];

		if( $data['examNumNm'] == "" ){
			$goodsInfoList .= "&nbsp;&nbsp;회차&nbsp;:&nbsp;전체</br>";
		}else{
			$goodsInfoList .= "&nbsp;&nbsp;회차&nbsp;:&nbsp;".$data['examNumNm']."</br>";
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
			<h3 class="title">쿠폰 상세</h3>
			<!-- 테이블1 -->
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
								<td><?=$arrRows[0]['Adm_name']?></td>
								<th>부서</th>
								<td><?=$arrRows[0]['Dept_Name']?></td>
							</tr>
							<tr>
								<th>신청일시</th>
								<td><?=$arrRows[0]['regi_day']?></td>
								<th>기안문서번호</th>
								<td><?=$arrRows[0]['doc_num']?></td>
							</tr>
							<tr>
								<th>쿠폰명</th>
								<td colspan="3"><?=$arrRows[0]['coup_name']?></td>
							</tr>
							<tr>
								<th>발급구분</th>
								<td colspan="3"><?=$arrRows[0]['sbCoupTypeNm']?></td>
							</tr>
							<tr>
								<th>발급대상</th>
								<td colspan="3"><?=$arrRows[0]['areaDataUsr']?></td>
							</tr>
							<tr>
								<th>사용조건</th>
								<td colspan="3"><?=$arrRows[0]['areaDataUsa']?></td>
							</tr>
							<tr>
								<th>시험</th>
								<td colspan="3"><?=$goodsInfoList?></td>
							</tr>
							<tr>
								<th>할인</th>
								<td colspan="3"><?=$arrRows[0]['svc'].$arrRows[0]['svcNm']?></td>
							</tr>
							<tr>
								<th>수량</th>
								<td colspan="3"><?=( $arrRows[0]['coup_count'] == '-1'	)? "제한 없음": $arrRows[0]['coup_count'] ?></td>
							</tr>
							<tr>
								<th>사용기간</th>
								<td colspan="3"><?=substr($arrRows[0]['usable_Startday'], 0, 10)?> ~ <?=substr($arrRows[0]['usable_endday'], 0, 10)?></td>
							</tr>
							<tr>
								<th>업체</th>
								<td colspan="3"><?=$arrRows[0]['comp_name']?></td>
							</tr>
							<tr>
								<th>업체담당자</th>
								<td colspan="3"><?=$arrRows[0]['comp_mng']?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!-- //테이블1-->
			<!-- 테이블3 -->
			<div class="box_bs">
				<div class="box_inform">
					<p class="txt_l">
					<strong class="s_tit fm_malgun">담당자 결제</strong>
					</p>
				</div>
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width: auto;">
							<col style="width: auto;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
							<tr>
								<th>처리상태</th>
								<th>결재자</th>
								<th>결재일시</th>
							</tr>
<?php
	$okChkNm = "";
	switch ( $arrRows[0]['ok_CHK'] ) {
		case 'O'	: $okChkNm = "승인"; break;
		case 'X'	: $okChkNm = "미승인"; break;
		case '-'	: $okChkNm = "대기"; break;
		default		: $okChkNm = ""; break;
	}
	if( $arrRows[0]['ok_CHK'] == "-" ){
		echo "<tr>";
		echo "<td>".$okChkNm."</td>";
		echo "<td>".fnButtonCreate($cPageRoleRw, "class='btn_fill btn_sm' id='btnApp'", "승인")."&nbsp;&nbsp;".fnButtonCreate($cPageRoleRw, "class='btn_line btn_sm' id='btnUnApp'", "미승인")."</td>";
		echo "<td></td>";
		echo "</tr>";
	}else{
		echo "<tr>";
		echo "<td>".$okChkNm."</td>";
		echo "<td>".$arrRows[0]['okNm']."(".$arrRows[0]['ok_id'].")</td>";
		echo "<td>".$arrRows[0]['ok_day']."</td>";
		echo "</tr>";
	}
?>
						</tbody>
					</table>
				</div>
			</div>
			<!-- //테이블3-->
			<!-- 세로형 테이블 //-->

			<div class="wrap_btn">
				<?=( $arrRows[0]['ok_CHK'] == "-" )? fnButtonCreate($cPageRoleRw, "class='btn_fill btn_lg' id='btnModify'", "수정"): "" ?>
				<button type="button" class="btn_line btn_lg" id="btnCancel">목록으로</button>
			</div>

		</div>
	</div>
</div>
<!--right //-->

<script type="text/javascript">
$(document).ready(function () {

	$("#btnModify").on("click", function () {
		location.href = "/language/couponWrite.php?coupCode=<?=$pCoupCode?>";
	});

	$("#btnCancel").on("click", function () {
		location.href = "/language/couponList.php<?=fnGetParams().'currentPage='.$pCurrentPage?>";
	});

	$("#btnApp").on("click", function () {		
		var u = "/language/couponProc.php";
		var param = {
			"proc"		: "app",
			"okChk"		: "O",
			"coupCode"	: "<?=$pCoupCode?>"
		};
		$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
			success: function(resJson) {
				if( resJson.result == 1 ){
					alert("결제 승인 되었습니다.");
					location.reload();
				}else{
					alert("결제 승인이 실패 하였습니다.");
				}
			},
			error: function(e) {
				alert("현재 서버 통신이 원할하지 않습니다.");
			}
		});
	});

	$("#btnUnApp").on("click", function () {
		var u = "/language/couponProc.php";
		var param = {
			"proc"		: "unApp",
			"okChk"		: "X",
			"coupCode"	: "<?=$pCoupCode?>"
		};

		$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
			success: function(resJson) {
				if( resJson.result == 1 ){
					alert("결제 미승인 되었습니다.");
					location.reload();
				}else{
					alert("결제 미승인이 실패 하였습니다.");
				}
			},
			error: function(e) {
				alert("현재 서버 통신이 원할하지 않습니다.");
			}
		});
	});
	
});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
