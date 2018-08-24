<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [
		'admId' => ['type' => 'string', 'notnull' => true, 'default' => '', 'min' => 0, 'max' => 30]
	];
	$resultArray = fnGetRequestParam($valueValid);

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
	}else{
		$proc = "modify";

		$useChk	= $arrRows[0][use_CHK];

		$admEmail = explode("@",$arrRows[0][Adm_Email]);
		$admEmail1 = $admEmail[0];
		$admEmail2 = $admEmail[1];
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
				<div class="wrap_box_tree">
					<div class="l_cont">
						<p class="mtit">전체 메뉴</p>
							<div class="box_tree">
								<!-- tree -->
								<div class="nav_tree">
									<ul>
										<li class="nav_tree_on"><button type="button">+</button><a class="nav_tree_label" href="#">시험관리</a> 
											<ul>
												<li class="nav_tree_on"><button type="button">+</button><a class="nav_tree_label" href="#">TOEIC</a> 
													<ul>
														<li class="nav_tree_on"><button type="button">+</button><a class="nav_tree_label" href="#">접수현황통계</a>
															<ul>
																<li class="nav_tree_off"><a class="nav_tree_label" href="#">일별 접수통계</a></li>
																<li class="nav_tree_off"><a class="nav_tree_label none" href="#">월별 접수통계</a></li>
																<li class="nav_tree_last"><a class="nav_tree_label none" href="#">연도별 접수통계</a></li>
																<li class="nav_tree_last"><a class="nav_tree_label none" href="#">지역/고사장 현황</a></li>
																<li class="nav_tree_last"><a class="nav_tree_label none" href="#">단체별 현황</a></li>
																<li class="nav_tree_last"><a class="nav_tree_label none" href="#">접수 예상 인원</a></li>
																<li class="nav_tree_last"><a class="nav_tree_label" href="#">접수자 추이 통계</a></li>
																<li class="nav_tree_last"><a class="nav_tree_label" href="#">접수자 성향 통계</a></li>
																<li class="nav_tree_last"><a class="nav_tree_labe none" href="#">목표달성현황</a></li>
															</ul>
														</li>
														<li class="nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">접수관리</a></li>
														<li class="nav_tree_last nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">시험세팅</a></li>
													</ul>
												</li>
												<li class="nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">TOEIC Speaking</a></li>
												<li class="nav_tree_last nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">TOEIC Speaking</a></li>
											</ul>
										</li>
									</ul>
								</div>
								<!-- //tree-->
							</div>
						</div>
						<div class="c_cont">
							<div class="item"> 
								<p><button class="btn_arr" type="button"><strong class="fs_sm">▶</strong></button><br>추가</p>
								<p class="pad_t20"><button class="btn_arr" type="button"><strong class="fs_sm">◀</strong></button><br>제거</p>
							</div>
						</div>
						<div class="r_cont">
							<p class="mtit">사용자 지정메뉴</p>
								<div class="box_tree">
									<!-- tree -->
								<div class="nav_tree">
									<ul>
										<li class="nav_tree_on"><button type="button">+</button><a class="nav_tree_label" href="#">시험관리</a> 
											<ul>
												<li class="nav_tree_on"><button type="button">+</button><a class="nav_tree_label" href="#">TOEIC</a> 
													<ul>
														<li class="nav_tree_on"><button type="button">+</button><a class="nav_tree_label" href="#">접수현황통계</a>
															<ul>
																<li class="nav_tree_off"><a class="nav_tree_label" href="#">일별 접수통계</a></li>
																<li class="nav_tree_off"><a class="nav_tree_label" href="#">지역/고사장 현황</a></li>
																<li class="nav_tree_last"><a class="nav_tree_label" href="#">단체별 현황</a></li>
																<li class="nav_tree_last"><a class="nav_tree_label" href="#">접수 예상 인원</a></li>
																<li class="nav_tree_last"><a class="nav_tree_label" href="#">목표달성현황</a></li>
															</ul>
														</li>
														<li class="nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">접수관리</a></li>
														<li class="nav_tree_last nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">시험세팅</a></li>
													</ul>
												</li>
												<li class="nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">TOEIC Speaking</a></li>
												<li class="nav_tree_last nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">TOEIC Speaking</a></li>
											</ul>
										</li>
										<li class="nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">성적표발급</a></li>
										<li class="nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">어학공통</a></li>
										<li class="nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">수험자관리</a></li>
										<li class="nav_tree_off"><button type="button">+</button><a class="nav_tree_label" href="#">PLAZA관리</a></li>
									</ul>
								</div>
								<!-- //tree-->
								</div>
						</div>
					</div>
					<div class="wrap_tbl pad_t20">
						<table class="type01">
							<tbody><tr>
								<td class="headline">
									<strong><?=$arrRows[0][Adm_id]?></strong> 에게
									<input style="width: 100px;" type="text"> 과 동일한 권한 주기 &nbsp;&nbsp;
									<button class="btn_fill btn_md" type="button" id="btnCopy">확인</button>
								</td>
							</tr>
						</tbody></table>
					</div>
				</div>
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

	$("#btnCopy").on("click", function () {

//		$('#frmWrite').submit();
    });

	$("#btnWrite").on("click", function () {

//		$('#frmWrite').submit();
    });

	$("#btnCancel").on("click", function () {
		location.href = "./memberList.php<?=fnGetParams().'currentPage='.$pCurrentPage?>";
	});



});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
