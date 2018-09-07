<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
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
	$useChk		= "O";

	if ( $pAdmId != "" ){
		$sql  = " SELECT ";
		$sql .= "	AI.Adm_id, AI.Adm_name, AI.Adm_Email, AI.Dept_Code, AI.Token_code, AI.Reg_day, AI.Login_day, AI.Password_day, AI.Adm_IP, AI.use_CHK, AI.Update_day ";
		$sql .= "	, ISNULL( ADI4.Dept_Code, ADI3.Dept_Code ) AS detpLev1 ";
		$sql .= "	, CASE WHEN ISNULL( ADI4.Dept_Code, '' ) = '' THEN ADI2.Dept_Code ELSE ADI3.Dept_Code END AS detpLev2 ";
		$sql .= " FROM Adm_info AS AI ";
		$sql .= " JOIN Adm_Dept_Info AS ADI1 (nolock) ON AI.Dept_Code = ADI1.Dept_Code ";
		$sql .= " JOIN Adm_Dept_Info AS ADI2 (nolock) ON ADI1.PDept_Code = ADI2.Dept_Code ";
		$sql .= " JOIN Adm_Dept_Info AS ADI3 (nolock) ON ADI2.PDept_Code = ADI3.Dept_Code ";
		$sql .= " LEFT OUTER JOIN Adm_Dept_Info AS ADI4 (nolock) ON ADI3.PDept_Code = ADI4.Dept_Code ";
		$sql .= " WHERE Adm_id = :admId ";

		$pArray[':admId'] = $pAdmId;

		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		if( count($arrRows) == 0 ){
			fnShowAlertMsg("데이터가 존재하지 않습니다.", "history.back();", true);
		}else{
			$proc = "modify";

			$useChk	= $arrRows[0]['use_CHK'];

			$admEmail = explode("@",$arrRows[0]['Adm_Email']);
			$admEmail1 = $admEmail[0];
			$admEmail2 = $admEmail[1];
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
			<h3 class="title">계정<?=( $proc == "write" )? "등록": "수정" ?></h3>

<form name="frmWrite" id="frmWrite" action="/admin/memberProc.php" method="post"> 
<input type="hidden" name="proc" value="<?=$proc?>">

			<!-- 세로형 테이블 -->
			<div class="box_bs">
				<div class="box_inform">
					<p class="pad_tb15">
						* 처음 로그인 하신 경우나 비밀번호를 분실하여 임시 비밀번호를 발급받은 경우 반드시 비밀번호를 변경해 주시기 바랍니다.<br>
						* 비밀번호는 최소 90일에 한번은 변경해주셔야 지속적으로 사이트 이용이 가능합니다.<br>
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
										<input type="hidden" id="idCheck" name="idCheck" value="">
										<button type="button" class="btn_fill btn_sm" id="btnIdCheck">중복체크</button>
<?php	}else{		?>
										<?=$arrRows[0]['Adm_id']?>
										<input type="hidden" id="admId" name="admId" value="<?=$arrRows[0]['Adm_id']?>">
										<input type="hidden" id="idCheck" name="idCheck" value="Y">
<?php	}		?>
									</div>
								</td>
							</tr>
							<tr>
								<th>비밀번호</th>
								<td>
<?php	if( $proc == "write" ){		?>
										<span class="point">* 자동생성되어 입력한 이메일로 발송</span>
<?php	}else{		?>
										<input style="width: 300px;" type="password" value="**********" readonly>
										<button type="button" class="btn_fill btn_sm" id="btnPassClear">초기화</button>
										<span class="point">* * 영문, 숫자, 특수기호 조합으로 8자리 이상 15자리 이내</span>
<?php	}		?>
									<span class="point">* 자동생성되어 입력한 이메일로 발송</span>
								</td>
							</tr>
							<tr>
								<th>이름</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="admName" value="<?=$arrRows[0]['Adm_name']?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>eToken</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="tokenCode" value="<?=$arrRows[0]['Token_code']?>">
									</div>
								</td>
							</tr>
							<tr>
								<th>이메일</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="admEmail1" value="<?=$admEmail1?>">
										@ 
										<select name="admEmail2">  
											<option value="ybm.co.kr"	<?=( $admEmail2 == 'ybm.co.kr'		)? "SELECTED": "" ?> >ybm.co.kr</option>
											<option value="toeic.co.kr"	<?=( $admEmail2 == 'toeic.co.kr'	)? "SELECTED": "" ?> >toeic.co.kr</option
										</select>
									</div>
								</td>
							</tr>							
							<tr>
								<th>소속회사/부서</th>
								<td>
									<div class="item">
										<select id="detpLev1" name="detpLev1"></select> &nbsp;
										<select id="detpLev2" name="detpLev2"></select> &nbsp;
										<select id="deptCode" name="deptCode"></select>
									</div>
								</td>
							</tr>
							<tr>
								<th>컴퓨터IP</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text" name="admIp" value="<?=$arrRows[0]['Adm_IP']?>">
									</div>
								</td>
							</tr>							
							<tr>
								<th>사용여부</th>
								<td>
									<div class="item">
										<input class="i_unit" type="radio" name="useChk" value="O" <?=( $useChk == "O" )? "CHECKED": "" ?> ><label for="">사용</label>
										<input class="i_unit" type="radio" name="useChk" value="X" <?=( $useChk == "X" )? "CHECKED": "" ?> ><label for="">사용안함</label>
									</div>
								</td>
							</tr>
<?php	if( $proc == "modify" ){		?>
							<tr>
								<th>등록일자</th>
								<td><span><?=$arrRows[0]['Reg_day']?></span></td>
							</tr>
							<tr>
								<th>만료일자</th>
								<td><span><?=fnCalDate($arrRows[0][Password_day], 'day', 90)?></span></td>
							</tr>
							<tr>
								<th>최종수정일자</th>
								<td><span><?=$arrRows[0]['Update_day']?></span></td>
							</tr>
<?php	}		?>
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
            admId: {
                required: true    //필수조건
			}, idCheck: {
                required: true    //필수조건
			}, admName: {
                required: true    //필수조건
			}, tokenCode: {
                required: true    //필수조건
			}, admEmail1: {
                required: true    //필수조건
			}, detpLev3: {
                required: true    //필수조건
			}
        }, messages: {
			admId: {
				required: "아이디를 입력해주세요."
			}, idCheck: {
				required: "아이디 중복체크를 해주세요."
			}, admName: {
				required: "이름을 입력해주세요."
			}, tokenCode: {
				required: "eToken을 입력해주세요."
			}, admEmail1: {
				required: "이메일을 입력해주세요."
			}, detpLev3: {
				required: "부서를 선택해주세요."
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
		$("#admId").val( $.trim($("#admId").val()) );

		if ( $("#admId").val() == ""){
			alert("아이디를 입력해 주세요.");
			return false;
		}
		if ( $("#idCheck").val() == ""){
			alert("아이디 중복체크를 해주세요.");
			return false;
		}

		$('#frmWrite').submit();
    });

	$("#admId").keyup(function(event){		// 영문 숫자만 입력
		if (!(event.keyCode >=37 && event.keyCode<=40)) {
			var inputVal = $(this).val();
			$(this).val(inputVal.replace(/[^a-z0-9]/gi,''));
		}
		$("#idCheck").val("");
	});

	$("#btnCancel").on("click", function () {
		location.href = "/admin/memberList.php<?=fnGetParams().'currentPage='.$pCurrentPage?>";
	});

	$("#btnIdCheck").on("click", function(){
		$("#admId").val( $.trim($("#admId").val()) );

		if ( $("#admId").val() == ""){
			alert("아이디를 입력해 주세요.");
			return false;
		}

		var u = "/admin/memberProc.php";
		var param = {
			"proc"	: "idCheck",
			"admId"	: $("#admId").val()
		};

		$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
			success: function(resJson) {
				if( resJson.data[0].cnt == 0 ){
					alert("사용 가능한 아이디 입니다.");
					$("#idCheck").val("Y");
				}else{
					alert("중복된 아이디가 존재 합니다.");
				}
			},
			error: function(e) {
				alert("현재 서버 통신이 원할하지 않습니다.");
			}
		});

    });

	$("#btnPassClear").on("click", function(){
		var u = "/admin/memberProc.php";
		var param = {
			"proc"	: "passClear",
			"admId"	: $("#admId").val(),
			"admEmail"	: "<?=$arrRows[0]['Adm_Email']?>"
		};

		$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
			success: function(resJson) {
				if( resJson.result == 1 ){
					alert("비밀번호가 초기화 되었습니다.");
				}else{
					alert("비밀번호 초기화 실패 하였습니다.");
				}
			},
			error: function(e) {
				alert("현재 서버 통신이 원할하지 않습니다.");
			}
		});
    });
	
	var param = {
		"detpLev1" 			: "detpLev1"	// 1detp 부서정보
		, "detpLev2" 		: "detpLev2"	// 2detp 부서정보
		, "detpLev3" 		: "deptCode"	// 3detp 부서정보
		, "optYn"			: "Y"			// 상단 옵션 사용여부(Y, N)
		, "firstOptVal"		: ""			// 상단 옵션  value
		, "firstOptLable"	: "선택"			// 상단 옵션  text
	}
	common.sys.setDeptComboCreate(param);

	$("#detpLev1").val("<?=$arrRows[0]['detpLev1']?>").change();
	$("#detpLev2").val("<?=$arrRows[0]['detpLev2']?>").change();
	$("#deptCode").val("<?=$arrRows[0]['Dept_Code']?>");


});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
