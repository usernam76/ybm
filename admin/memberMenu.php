<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성

	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [
		'admId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 30]
	];
	$resultArray = fnGetRequestParam($valueValid);

	//계정정보
	$sql = " SELECT ";
	$sql .= "	AI.Adm_id, AI.AdmType, AI.Adm_name, AI.Adm_Email, ADI.Dept_Name";
	$sql .= " FROM [theExam].[dbo].[Adm_info]  AS AI ";
	$sql .= " LEFT OUTER JOIN [theExam].[dbo].[Adm_Dept_Info] AS ADI (nolock) ON AI.Dept_Code = ADI.Dept_Code ";
	$sql .= " WHERE Adm_id = :admId ";

	$pArray[':admId'] = $pAdmId;

	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	if( count($arrRows) == 0 ){
		fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
	}

	// 계정 메뉴권한 정보
	$sql = " SELECT ";
	$sql .= "	Menu_Name1, Menu_idx1, Menu_Name2, Menu_idx2, Menu_Name3, Menu_idx3, Menu_Name4, Menu_idx4 ";
	$sql .= "	, ( SELECT COUNT(*) FROM [theExam].[dbo].Menu_Info VMI2 WHERE Par_Menu_idx = VMI.Menu_idx2 AND ( SELECT COUNT(*) FROM [theExam].[dbo].Menu_Info WHERE Par_Menu_idx = VMI2.Menu_idx ) > 0 ) AS MenuCnt";
	$sql .= "	, AM.[Role_RW] ";
	$sql .= " FROM [theExam].[dbo].v_Menu_Info VMI ";
	$sql .= " LEFT OUTER JOIN [theExam].[dbo].[Adm_Menu] AM ON AM.[Menu_idx] = VMI.Menu_idx4 AND Adm_id = :admId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' ";

	$arrRowsMenu = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

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
			<h3 class="title">메뉴설정</h3>
			<!-- 테이블 1-->
			<div class="box_bs">
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
						</colgroup>
						<thead>
							<tr>
								<th>ID</th>
								<th>이름</th>
								<th>이메일</th>
								<th>소속부서</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?=$arrRows[0][Adm_id]?></td>
								<td><?=$arrRows[0][Adm_name]?></td>
								<td><?=$arrRows[0][Adm_Email]?></td>
								<td><?=$arrRows[0][Dept_Name]?></td>
							</tr>
					  </tbody>
					</table>
				</div>
			</div>
			<!-- 테이블 1-->
			<!-- 테이블 2-->
<form name="frmCopy" id="frmCopy" action="/admin/memberProc.php" method="post"> 
<input type="hidden" name="proc" value="menuCopy">
<input type="hidden" name="admId" value="<?=$arrRows[0][Adm_id]?>">
<input type="hidden" name="admType" value="<?=$arrRows[0][AdmType]?>">
			<div class="box_bs">
				<div class="box_inform">
					<p class="fl_l">
					<strong class="s_tit fm_malgun">권한복사</strong> 
					</p>
				</div>
				<div class="wrap_tbl">
					<table class="type01">
						<tbody><tr>
							<td class="headline">
								<strong><?=$arrRows[0][Adm_id]?></strong> 에게
								<input style="width: 100px;" type="text" id="copyId" name="copyId"> 과 동일한 권한 주기 &nbsp;&nbsp;
								<button class="btn_fill btn_md" type="button" id="btnCopy">확인</button>
							</td>
						</tr>
					</tbody></table>
				</div>
			</div>
			<!-- 테이블 2-->
</form> 
<form name="frmWrite" id="frmWrite" action="/admin/memberProc.php" method="post"> 
<input type="hidden" name="proc" value="menuSave">
<input type="hidden" name="admId" value="<?=$arrRows[0][Adm_id]?>">
<input type="hidden" name="admType" value="<?=$arrRows[0][AdmType]?>">

			<!-- 테이블 3-->
			<div class="box_bs">
				<div class="box_inform">
					<p class="fl_l">
					<strong class="s_tit fm_malgun">상세설정</strong> 
					</p>
				</div>
				<div class="wrap_tbl">
					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width: 200px;">
							<col style="width: 200px;">
							<col style="width: auto;">
						</colgroup>
						<tbody>
<?php
	$oldMenuIdx1	= "";
	$oldMenuIdx2	= "";
	$oldMenuIdx3	= "";

	$htmlStr = "";
	foreach($arrRowsMenu as $data) {

		//Menu1
		if( $oldMenuIdx1 != $data['Menu_idx1'] ){
			if( $oldMenuIdx1 != "" ){
				$htmlStr .= "				</tbody>";
				$htmlStr .= "				</table>";
				$htmlStr .= "			</div>";
				$htmlStr .= "		</td>";
				$htmlStr .= "</tr>";
			}

			$htmlStr .= "<tr>";
			$htmlStr .= "	<td colspan='3'>";
			$htmlStr .= "		<div class='item fl_l'>".$data['Menu_Name1']." &nbsp;&nbsp; ";
			$htmlStr .= "			<select style='width: 200px;' class='menu1' menuIdx='".$data['Menu_idx1']."' >";
			$htmlStr .= "				<option value='N'>전체변경 선택</option>";
			$htmlStr .= "				<option value=''>권한없음</option>";
			$htmlStr .= "				<option value='R'>읽기</option>";
			$htmlStr .= "				<option value='W'>쓰기/수정/삭제</option>";
			$htmlStr .= "			</select>";
			$htmlStr .= "		</div>";
			$htmlStr .= "		<span class='fl_r'><button class='btn_fill btn_md btnhHidden' type='button' menuIdx='".$data['Menu_idx1']."' >권한상세</button></span>";
			$htmlStr .= "	</td>";
			$htmlStr .= "</tr>";
		}
		//Menu2
		if( $oldMenuIdx2 != $data['Menu_idx2'] ){

			if( $oldMenuIdx1 == $data['Menu_idx1'] ){
				$htmlStr .= "				</tbody>";
				$htmlStr .= "				</table>";
				$htmlStr .= "			</div>";
				$htmlStr .= "		</td>";
				$htmlStr .= "</tr>";
			}

			$htmlStr .= "<tr class='trMenuIdx".$data['Menu_idx1']."'>";
			$htmlStr .= "	<th rowspan='".$data['MenuCnt']."' style='border-right:1px solid #d6d7da;' class='c_txt'>".$data['Menu_Name2'];
			$htmlStr .= "		<div class='item pad_t10'>";
			$htmlStr .= "			<select style='width: 160px;' class='menu2' menuIdx='".$data['Menu_idx2']."' >";
			$htmlStr .= "				<option value='N'>전체변경 선택</option>";
			$htmlStr .= "				<option value=''>권한없음</option>";
			$htmlStr .= "				<option value='R'>읽기</option>";
			$htmlStr .= "				<option value='W'>쓰기/수정/삭제</option>";
			$htmlStr .= "			</select>";
			$htmlStr .= "		</div>";
			$htmlStr .= "	</th>";
		}
		//Menu2
		if( $oldMenuIdx3 != $data['Menu_idx3'] ){
			if( $oldMenuIdx2 == $data['Menu_idx2'] ){
				$htmlStr .= "				</tbody>";
				$htmlStr .= "				</table>";
				$htmlStr .= "			</div>";
				$htmlStr .= "		</td>";
				$htmlStr .= "</tr>";
				$htmlStr .= "<tr class='trMenuIdx".$data['Menu_idx1']."'>";
			}

			$htmlStr .= "	<th class='c_txt'>".$data['Menu_Name3'];
			$htmlStr .= "		<div class='item pad_t10'>";
			$htmlStr .= "			<select style='width: 160px;' class='menu3' menuIdx='".$data['Menu_idx3']."' >";
			$htmlStr .= "				<option value='N'>전체변경 선택</option>";
			$htmlStr .= "				<option value=''>권한없음</option>";
			$htmlStr .= "				<option value='R'>읽기</option>";
			$htmlStr .= "				<option value='W'>쓰기/수정/삭제</option>";
			$htmlStr .= "			</select>";
			$htmlStr .= "		</div>";
			$htmlStr .= "	</th>";
			$htmlStr .= "	<td>";
			$htmlStr .= "		<div class='wrap_tbl'>";
			$htmlStr .= "			<table class='type02 bd_top'>";
			$htmlStr .= "				<colgroup>";
			$htmlStr .= "					<col style='width: 180px;'>";
			$htmlStr .= "					<col style='width: auto;'>";
			$htmlStr .= "				</colgroup>";
			$htmlStr .= "				<tbody>";
		}

		$sRoleRwR	= "";
		$sRoleRwW	= "";
		if( $data['Role_RW'] == 'R'){ $sRoleRwR	= "SELECTED"; }
		if( $data['Role_RW'] == 'W'){ $sRoleRwW	= "SELECTED"; }

		$htmlStr .= "			<tr>";
		$htmlStr .= "				<th>".$data['Menu_Name4']."</th>";
		$htmlStr .= "				<td>";
		$htmlStr .= "					<div class='item'>";
		$htmlStr .= "						<select style='width: 200px;' class='menuIdx".$data['Menu_idx1']." menuIdx".$data['Menu_idx2']." menuIdx".$data['Menu_idx3']."' name='roleRw[]'>";
		$htmlStr .= "							<option value=''>권한없음</option>";
		$htmlStr .= "							<option value='R' ".$sRoleRwR.">읽기</option>";
		$htmlStr .= "							<option value='W' ".$sRoleRwW.">쓰기/수정/삭제</option>";
		$htmlStr .= "						</select>";
		$htmlStr .= "					</div>";
		$htmlStr .= "				</td>";
		$htmlStr .= "			</tr>";

		$htmlStr .= " <input type='hidden' name='menuIdx[]' value='".$data['Menu_idx4']."'>";


		$oldMenuIdx1 = $data['Menu_idx1'];
		$oldMenuIdx2 = $data['Menu_idx2'];
		$oldMenuIdx3 = $data['Menu_idx3'];
	}
	echo $htmlStr;
?>
											</tbody>
										</table>
									</div>
									<!-- 마지막 depth -->
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!-- 테이블 3-->
</form> 
			<!-- 버튼 -->
			<div class="wrap_btn">
				<button class="btn_fill btn_lg" type="button" id="btnWrite">저장</button>
				<button class="btn_line btn_lg" type="button" id="btnCancel">취소</button>
			</div>
			<!-- 버튼 //-->
		</div>
	</div>
</div>
<!--right //-->

<script type="text/javascript">
$(document).ready(function () {

	$('#frmCopy').validate({
        onfocusout: false,
        rules: {
            copyId: {
                required: true    //필수조건
			}
        }, messages: {
			copyId: {
				required: "아이디를 입력해주세요."
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


	$("#btnCopy").on("click", function () {
		$("#copyId").val( $.trim($("#copyId").val()) );

		if ( $("#copyId").val() == ""){
			alert("아이디를 입력해 주세요.");
			return false;
		}

		$('#frmCopy').submit();
    });

	$("#btnWrite").on("click", function () {

		$('#frmWrite').submit();
    });

	$("#btnCancel").on("click", function () {
		location.href = "/admin/memberList.php<?=fnGetParams().'currentPage='.$pCurrentPage?>";
	});

	//메뉴권한 전체 변경 1depth
	$(".menu1").on("change", function () {	
		if ( $(this).val() != "N" ){
			$('.menuIdx'+$(this).attr("menuIdx")).val( $(this).val() );
		}
	});

	//메뉴권한 전체 변경 2depth
	$(".menu2").on("change", function () {	
		if ( $(this).val() != "N" ){
			$('.menuIdx'+$(this).attr("menuIdx")).val( $(this).val() );
		}
	});

	//메뉴권한 전체 변경 3depth
	$(".menu3").on("change", function () {	
		if ( $(this).val() != "N" ){
			$('.menuIdx'+$(this).attr("menuIdx")).val( $(this).val() );
		}
	});

	//하위 숨김 처리
	$(".btnhHidden").on("click", function () {
		$('.trMenuIdx'+$(this).attr("menuIdx")).toggle();
	});


});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
