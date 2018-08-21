<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
//	$valueValid = [
//		'idx' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 3],
//		'userId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 2, 'max' => 20]
//	];
	$resultArray = fnGetRequestParam($valueValid);

	$proc = "write";

	$admEmail1	= "";
	$admEmail2	= "";
	$admTel1	= "";
	$admTel2	= "";
	$admTel3	= "";

	if ( $pAdmId != "" ){
		$sql = ' SELECT ';
		$sql .= ' Adm_id, Adm_name, Adm_Email, Reg_day, Login_day, Password_day ';
		$sql .= ' FROM [theExam].[dbo].[Adm_info] ';
		$sql .= ' WHERE Adm_id = :admId ';

		$pArray[':admId'] = $pAdmId;

		$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		if( count($arrRows) == 0 ){
			fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
		}else{
			$proc = "modify";
		}		
	}	
?>
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>
<body>

<script type="text/javascript">
$(document).ready(function () {

	$('#writeFrm').validate({
        onfocusout: false,
        rules: {
            admId: {
                required: true    //필수조건
			}, admName: {
                required: true    //필수조건
			}, tokenCode: {
                required: true    //필수조건
			}
        }, messages: {
			admId: {
				required: "아이디를 입력해주세요."
			}, admName: {
				required: "이름을 입력해주세요."
			}, tokenCode: {
				required: "eToken을 입력해주세요."
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

	$("#writeBtn").click(function () {
		$("#admId").val( $.trim($("#admId").val()) );

		$('#writeFrm').submit();
    });

	$("#cancelBtn").click(function () {
		location.href = "./memberList.php<?=fnGetParams().'currentPage='.$pCurrentPage?>";
	});
	

});
</script>

<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">계정관리</h3>

<form name="writeFrm" id="writeFrm" action="./memberProc.php" method="post"> 
<input type="hidden" name="proc" value="<?=$proc?>">

			<!-- 세로형 테이블 -->
			<div class="box_bs">
				<div class="box_inform">
					<p class="pad_tb15">
						* 처음 로그인 하신 경우나 비밀번호를 분실하여 임시 비밀번호를 발급받은 경우 반드시 비밀번호를 변경해 주시기 바랍니다.<br>
						* 비밀번호는 최소 30일에 한번은 변경해주셔야 지속적으로 사이트 이용이 가능합니다.<br>
						* 이메일, 소속부서, 컴퓨터 아이피주소 등의 개인정보는 관리자만 수정이 가능하오니, 정보가 변경되었을 경우 관리자에게 연락하여 수정해주시기 바랍니다.<br>
					</p>
				</div>

				<div class="wrap_tbl">
					<table class="type02">
						<caption></caption>
						<colgroup>
							<col style="width:180px">
							<col style="width:auto">
						</colgroup>
						<tbody>
							<tbody>
							<tr>
								<th>아이디</th>
								<td>
									<div class="item">
<?php	if( $proc == "write" ){		?>
										<input style="width: 300px;" type="text" id="admId" name="admId" value="">
<?php	}else{		?>
										<?=$arrRows[0][Adm_id]?>
										<input type="hidden" name="admId" value="<?=$arrRows[0][Adm_id]?>">
<?php	}		?>
									</div>
								</td>
							</tr>
							<tr>
								<th>비밀번호</th>
								<td>
									<span class="point">* 자동생성되어 입력한 이메일로 발송</span>
								</td>
							</tr>
							<tr>
								<th>이름</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" id="admName" name="admName" value="<?=$arrRows[0][Adm_name]?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>eToken</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="tokenCode" value="<?=$arrRows[0][Token_code]?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>이메일</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="admEmail1" value="<?=$admEmail1?>">
										@ 
										<select style="width: 300px;" name="Adm_Email">  
											<option>ybm.co.kr</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>전화번호</th>
								<td>
									<div class="item">
										<input style="width: 150px;" type="text" name="admTel1" value="<?=$admTel1?>"> -
										<input style="width: 150px;" type="text" name="admTel2" value="<?=$admTel2?>"> -
										<input style="width: 150px;" type="text" name="admTel3" value="<?=$admTel3?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>소속회사/부서</th>
								<td>
									<div class="item">
										<select style="width: 300px;">  
											<option>선택</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> &nbsp;
										<select style="width: 300px;">  
											<option>선택</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>컴퓨터IP</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="Adm_Email" value="<?=$Adm_Email?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>개인정보 권한</th>
								<td>
									<div class="item">
										<input class="i_unit" id="" type="radio"><label for="">부여</label>
										<input class="i_unit" id="" type="radio"><label for="">부여안함</label>
									</div>
								</td>
							</tr>
							<tr>
								<th>결제 권한</th>
								<td>
									<div class="item">
										<input class="i_unit" id="" type="radio"><label for="">부여</label>
										<input class="i_unit" id="" type="radio"><label for="">부여안함</label>
									</div>
								</td>
							</tr>
							<tr>
								<th>사용여부</th>
								<td>
									<div class="item">
										<input class="i_unit" id="" type="radio"><label for="">사용</label>
										<input class="i_unit" id="" type="radio"><label for="">부여안함</label>
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
				<button type="button" class="btn_fill btn_lg" id="writeBtn">등록</button>
				<button type="button" class="btn_line btn_lg" id="cancelBtn">취소</button>
			</div>

		</div>
	</div>
</div>
<!--right //-->

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
