<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [
		'admId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 30]
	];
	$resultArray = fnGetRequestParam($valueValid);

	//계정정보
	$sql = " SELECT ";
	$sql .= "	AI.Adm_id, AI.Adm_name, AI.Adm_Email, ADI.Dept_Name";
	$sql .= " FROM [theExam].[dbo].[Adm_info]  AS AI ";
	$sql .= " LEFT OUTER JOIN [theExam].[dbo].[Adm_Dept_Info] AS ADI (nolock) ON AI.Dept_Code = ADI.Dept_Code ";
	$sql .= " WHERE Adm_id = :admId ";

	$pArray[':admId'] = $pAdmId;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	if( count($arrRows) == 0 ){
		fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
	}

	// 계정 메뉴권한 정보
	$sql = " SELECT ";
	$sql .= "	Menu_Name1, Menu_idx1 ";
	$sql .= "	, ( SELECT COUNT(*) FROM [theExam].[dbo].v_Menu_Info WHERE ISNULL(Menu_idx4, '') != '' AND Menu_idx1 = VMI.Menu_idx1 ) AS MenuCnt1 ";
	$sql .= "	, Menu_Name2, Menu_idx2 ";
	$sql .= "	, ( SELECT COUNT(*) FROM [theExam].[dbo].v_Menu_Info WHERE ISNULL(Menu_idx4, '') != '' AND Menu_idx2 = VMI.Menu_idx2 ) AS MenuCnt2 ";
	$sql .= "	, Menu_Name3, Menu_idx3 ";
	$sql .= "	, ( SELECT COUNT(*) FROM [theExam].[dbo].v_Menu_Info WHERE ISNULL(Menu_idx4, '') != '' AND Menu_idx3 = VMI.Menu_idx3 ) AS MenuCnt3 ";
	$sql .= "	, Menu_Name4, Menu_idx4 ";
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
			<!-- 테이블2 -->
			<div class="box_bs tree">
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
<form name="frmCopy" id="frmCopy" action="/admin/memberProc.php" method="post"> 
<input type="hidden" name="proc" value="menuCopy">
<input type="hidden" name="admId" value="<?=$arrRows[0][Adm_id]?>">
<div class="wrap_box_tree">					
					<div class="wrap_tbl pad_t20">
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
</form> 
<form name="frmWrite" id="frmWrite" action="/admin/memberProc.php" method="post"> 
<input type="hidden" name="proc" value="menuSave">
<input type="hidden" name="admId" value="<?=$arrRows[0][Adm_id]?>">
<?php
	$old_Menu_idx1	= "";
	$old_Menu_idx2	= "";
	$old_Menu_idx3	= "";

	foreach($arrRowsMenu as $data) {
		if( $old_Menu_idx != $data['Menu_idx1'] ){
			echo "";
		}

?>
<?=$data['Menu_Name1']?>
<?=$data['Menu_idx1']?>
<?=$data['MenuCnt1']?>
<?php
	}
?>
			</div>
</form> 

			<!-- //테이블2-->
			<!-- 버튼 -->
			<div class="wrap_btn">
				<button class="btn_fill btn_lg" type="button" id="btnWrite">확인</button>
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



});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
