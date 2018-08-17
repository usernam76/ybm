<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	//$valueValid = [];
	$valueValid = [
		'idx' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 3],
		'userId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 2, 'max' => 20]
	];
	
	$totalRecords	= 0;		// 총 레코드 수
	$recordsPerPage	= 10;		// 한 페이지에 보일 레코드 수
	$pagePerBlock	= 10;		// 한번에 보일 페이지 블럭 수
	$currentPage	= 1;		// 현재 페이지

	$sql = ' SELECT ';
	$sql .= ' (SELECT COUNT(*) FROM [theExam].[dbo].[Adm_info] ) AS totalRecords ';
	$sql .= ' , Adm_id, Adm_name, Adm_Email, Reg_day, Login_day, Password_day ';
	$sql .= ' FROM [theExam].[dbo].[Adm_info] ';
	$sql .= ' ORDER BY Reg_day DESC ';
	$sql .= ' OFFSET ( '.$currentPage.' - 1 ) * '.$recordsPerPage.' ROWS ';
	$sql .= ' FETCH NEXT '.$recordsPerPage.' ROWS ONLY ';

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	if ( count($arrRows) > 0 ){
		$totalRecords	= $arrRows[0][totalRecords];
	}

?>
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>
<body>
<form action="" method="post"> 
<fieldset> 

<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">계정관리</h3>
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
										<input style="width: 300px;" type="text">
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
										<input style="width: 300px;" type="text">
									</div>
								</td>
							</tr>
							<tr>
								<th>eToken</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text">
									</div>
								</td>
							</tr>
							<tr>
								<th>이메일</th>
								<td>
									<div class="item">
										<input style="width: 300px;" type="text">
										@ 
										<select style="width: 300px;">  
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
										<input style="width: 150px;" type="text"> -
										<input style="width: 150px;" type="text"> -
										<input style="width: 150px;" type="text">
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
										<input style="width: 300px;" type="text">
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

				<div class="wrap_btn">
					<button type="button" class="btn_fill btn_lg">등록</button>
					<button type="button" class="btn_line btn_lg">취소</button>
				</div>

			</div>
			<!-- 세로형 테이블 //-->
		</div>
	</div>
</div>
<!--right //-->

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
