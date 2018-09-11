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
			<h3 class="title">응시권<?=( $proc == "write" )? "발급": "수정" ?></h3>

<form name="frmWrite" id="frmWrite" action="/admin/memberProc.php" method="post"> 
<input type="hidden" name="proc" value="<?=$proc?>">

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
								<td>홍길동</td>
								<th>부서</th>
								<td>홍보팀</td>
							</tr>
							<tr>
								<th>기안문서번호</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 300px;" type="text">
									</div>
								</td>
							</tr>
							<tr>
								<th>쿠폰명</th>
								<td colspan="3">
									<div class="item">
										<input style="width: 300px;" type="text">
									</div>
								</td>
							</tr>
							<tr>
								<th>종류</th>
								<td colspan="3">
									<div class="item">
										<select style="width: 300px;">  
											<option>일반쿠폰</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>발급대상</th>
								<td colspan="3">
									<div class="item">
										<select style="width: 300px;">  
											<option>일반쿠폰</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>발급구분</th>
								<td colspan="3">
									<div class="item">
										<select style="width: 300px;">  
											<option>일반쿠폰</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>사용조건</th>
								<td colspan="3">
									<div class="item">
										<select style="width: 300px;">  
											<option>유효성적 보유자</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>시험</th>
								<td colspan="3">
									<div class="item">
										<input class="i_unit" id="" type="radio"><label for="">TOEIC</label>
										<input class="i_unit" id="" type="radio"><label for="">TOEIC Speaking and Writing Tests</label>
										<input class="i_unit" id="" type="radio"><label for="">TOEFL ITP</label>
									</div>
									<div class="item pad_t10">
										<input class="i_unit" id="" type="radio"><label for="">JPT</label>
										<input class="i_unit" id="" type="radio"><label for="">SJPT</label>
										<input class="i_unit" id="" type="radio"><label for="">TSC</label>
										<input class="i_unit" id="" type="radio"><label for="">상무한검</label>
										<input class="i_unit" id="" type="radio"><label for="">KPE</label>
									</div>
									<div class="item pad_t10">
										<input class="i_unit" id="" type="radio"><label for="">TOEIC Bridge</label>
										<input class="i_unit" id="" type="radio"><label for="">JET</label>
										<input class="i_unit" id="" type="radio"><label for="">JET-SW</label>
										<input class="i_unit" id="" type="radio"><label for="">JET-Kids</label>
									</div>
								</td>
							</tr>
							<tr>
								<th>금액</th>
								<td colspan="3">
									<div class="item">
									<input style="width: 200px;" type="text">
									<select style="width: 100px;">  
										<option></option> 
										<option>선택 둘</option> 
										<option>선택 셋</option> 
									</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>수량</th>
								<td colspan="3">
									<div class="item">
										<input class="i_unit" id="" type="radio"><label for=""><input style="width: 100px;" type="text"> 매</label>
									</div>
									<div class="item pad_t10">
										<input class="i_unit" id="" type="radio"><label for="">제한없음</label>
									</div>
								</td>
							</tr>
							<tr>
								<th>사용기간</th>
								<td colspan="3">
									<div class="item">
										<p><input class="i_unit" id="" type="radio"><label for="">회차설정</label></p>
										<p class="pad_t10">
										<select style="width:200px;">  
											<option>309회 18.07.08(일)</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select> ~
										<select style="width:200px;">  
											<option>309회 18.07.08(일)</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
										</p> 
									</div>
									<div class="item pad_t10"> <!-- 활성화 btn_sm_bg_on -->
										<p><input class="i_unit" id="" type="radio"><label for="">기간설정</label></p>
										<p class="pad_t10">
										<button class="btn_sm_bg_on" type="button">1주일</button>
										<button class="btn_sm_bg_grey" type="button">1개월</button>
										<button class="btn_sm_bg_grey" type="button">3개월</button>
										<button class="btn_sm_bg_grey" type="button">6개월</button>
										<button class="btn_sm_bg_grey" type="button">1년</button>
										</p>
									</div>
									<div class="item pad_t10">
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
										&nbsp;&nbsp; ~ &nbsp;&nbsp;
										<input style="width: 160px;" type="text"><button class="btn_sm_calendar" type="button"></button>
									</div>
									<div class="item pad_t10">
										<p><input class="i_unit" id="" type="radio"><label for="">발급일부터 사용</label></p>
										<p class="pad_t10">
										<select style="width:200px;">  
											<option>00일</option> 
											<option>선택 둘</option> 
											<option>선택 셋</option> 
										</select>
										</p> 
									</div> 
								</td>
							</tr>
							<tr>
								<th>업체</th>
								<td colspan="3">
									<input style="width:300px;" type="text">
								</td>
							</tr>
							<tr>
								<th>업체담당자</th>
								<td colspan="3">
									<input style="width:300px;" type="text">
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
//		$('#frmWrite').submit();
    });

	$("#admId").keyup(function(event){		// 영문 숫자만 입력
		if (!(event.keyCode >=37 && event.keyCode<=40)) {
			var inputVal = $(this).val();
			$(this).val(inputVal.replace(/[^a-z0-9]/gi,''));
		}
		$("#idCheck").val("");
	});

	$("#btnCancel").on("click", function () {
		location.href = "/language/vchrsList.php<?=fnGetParams().'currentPage='.$pCurrentPage?>";
	});



});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
