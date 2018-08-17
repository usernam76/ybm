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
			<h3 class="title">신청리스트</h3>
			<!-- sorting area -->
			<div class="box_sort2">
				<strong class="part_tit">검색</strong>
				<div class="item line">
					<select style="width:200px;">  
						<option>아이디</option> 
						<option>이름</option> 
						<option>이메일</option> 
						<option>소속부서</option> 
					</select>
					<input style="width:300px;" type="text">
					<button class="btn_fill btn_md" type="button">검색</button>	
				</div>				
			</div>
			<!-- sorting area -->
			<!-- 테이블1 -->
			<div class="box_bs">
				<p class="fl_l pad_b10">총 <strong><?=$totalRecords?></strong> 건</p>
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:300px">
						</colgroup>
						<thead>
							<tr>
								<th>번호</th>
								<th>아이디</th>
								<th>이름</th>
								<th>이메일</th>
								<th>소속부서</th>
								<th>등록일</th>
								<th>만료일</th>
								<th>최종접속일</th>
								<th>사용여부</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>
<?php
	$no = $totalRecords - ( ( $currentPage - 1 ) * $recordsPerPage );
	foreach($arrRows as $data) {
?>
							<tr>
								<td><?=$no--?></td>
								<td><a href="#"><?=$data['Adm_id']?></a></td>
								<td><?=$data['Adm_name']?></td>
								<td><?=$data['Adm_Email']?></td>
								<td></td>
								<td><?=$data['Reg_day']?></td>
								<td><?=$data['Password_day']?></td>
								<td><?=$data['Login_day']?></td>
								<td></td>
								<td>
									<button type="button" class="btn_fill btn_sm">수정</button>
									<button type="button" class="btn_fill btn_sm">메뉴 설정</button>
									<button type="button" class="btn_fill btn_sm">해제</button>								
								</td>
							</tr>
<?php
	}
?>
					  </tbody>
					</table>
				</div>

				<!-- //page -->
				<?=fnPaginator($totalRecords, $recordsPerPage, $pagePerBlock, $currentPage)?>

				<div class="item r_txt">
					<input style="width: 40px;" type="text"> / 50 &nbsp;
					<button class="btn_line btn_sm" type="button">Go</button>	
				</div>
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
