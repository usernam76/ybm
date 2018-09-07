<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	$SBExamCate = "TOE";	// 토익시험을 기준으로 개발.
	
	$coulmn = "
		[SB_Exam_cate]
		,[Exam_num]
		,[Exam_Name]
		,convert(char(16),[Exam_day],120) as [Exam_day]
		,convert(char(13),[Score_day],120) as [Score_day]
		,[Exam_start_time]
		,[check_in_time]
		,convert(char(13),[gen_regi_Start],120) as [gen_regi_Start]
		,convert(char(13),[gen_regi_End],120) as [gen_regi_End]
		,convert(char(13),[spe_regi_Start],120) as [spe_regi_Start] 
		,convert(char(13),[spe_regi_End],120) as [spe_regi_End] 
		,convert(char(13),[ref_first_start],120) as [ref_first_start] 
		,convert(char(13),[ref_first_end],120) as [ref_first_end] 
		,convert(char(13),[ref_sec_start],120) as [ref_sec_start] 
		,convert(char(13),[ref_sec_end],120) as [ref_sec_end] 
		,convert(char(13),[regi_ext_end],120) as [regi_ext_end] 
		,convert(char(13),[score_change_start],120) as [score_change_start] 
		,convert(char(13),[score_change_end],120) as [score_change_end] 
		,[conf_type]
		,[update_day]
		,[ok_id]
		,[okType]
	";

	$pArray = null;
	$sql = " SELECT ".$coulmn. " FROM ";
	$sql .= "  [theExam].[dbo].[Exam_Info] ";
	$sql .= " WHERE SB_Exam_cate = :SBExamCate";
	$sql .=  "";
	$pArray[':SBExamCate']							= $SBExamCate;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행


	/*
	@ 리스트 테이블 칼럼
	@ TOEIC, JPT > 특별추가 접수기간 있음
	@ 토익스피킹, Jet, Jet-SW, Toeic Bridge > 기간연장 기능 사용
	@ $listTableCoulmn
	 > oc=>원칼럼  tc=>투칼럼 btn=>버튼&링크
	 > 칼럼숫자^테이블칼럼명^한글네임
	*/
	$listTableCoulmn = array(
"oc^Exam_num^회차",
"oc^Exam_day^시험일",
"tc^gen_regi_Start^gen_regi_End^정기접수기간",
"tc^spe_regi_Start^spe_regi_End^특별추가 접수기간",
"oc^regi_ext_end^기간연장",
"oc^^정기응시료",
"oc^^특별응시료",
"tc^score_change_start^score_change_end^성적표수령방법 변경기간",
"oc^Score_day^성적발표일",
"tc^ref_first_start^ref_first_end^환불1차",
"tc^ref_sec_start^ref_sec_end^환불2차",
"btn^수정"
	);

	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>

<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">시험일정관리</h3>
			<div class="box_sort c_txt">
				<div class="item"> 
					<select style="width: 300px;">  
						<option>2016년</option>
						<option>2017년</option>
						<option>2018년</option>
						<option>2019년</option>
						<option>2020년</option>
					</select>
				</div>
				<span class="fx_r">
					<button class="btn_fill btn_md" id="btnCalendar" type="button">캘린더 보기</button>
					<?=fnButtonCreate($cPageRoleRw, "class='btn_line btn_md' id='btnWrite' ", "일정 추가")?>
				</span>
			</div>
			<!-- 테이블1 -->
			<div class="box_bs">
				
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
						<?php
						for($i=0; $i<count($listTableCoulmn); $i++){
						?>
							<col style="width:auto">
							<?php
							}
							?>
						</colgroup>
						<thead>
							<tr>
							<?php
							for($i=0; $i<count($listTableCoulmn); $i++){
								$arrThisCoulmn = explode("^", $listTableCoulmn[$i]);
							?>
								<th><?=$arrThisCoulmn[count($arrThisCoulmn)-1]?></th>
							<?php
							}
							?>
							</tr>
						</thead>
						<tbody>
							<tr>
							<?php
							foreach($arrRows as $data){
								for($i=0; $i<count($listTableCoulmn); $i++){
									$arrThisCoulmn = explode("^", $listTableCoulmn[$i]);
									switch($arrThisCoulmn[0]){ 
										case "oc" : 
											$thisRows = $data[$arrThisCoulmn[1]];
										break;
										case "tc" :
											$thisRows = $data[$arrThisCoulmn[1]]."~".$data[$arrThisCoulmn[2]];
										break;
										case "btn" : 
											$thisRows = "<a href='#'>".$arrThisCoulmn[count($arrThisCoulmn)-1]."</a>";
										break;
									}
							?>
								<th><?=$thisRows?></th>
								<?php
								}
							}
							?>
							<!--
							<td>"회차"</td>
							<td>"시험일"</td>
							<td>"정기접수기간"</td>
							<td>"특별추가 접수기간"</td>
							<td>"기간연장"</td>
							<td>"정기응시료"</td>
							<td>"특별응시료"</td>
							<td>"성적표수령방법 변경기간"</td>
							<td>"성적발표일"</td>
							<td>"환불1차"</td>
							<td>"환불2차"</td>
							<td>"수정"</td>
							-->
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!-- //테이블1-->
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->
<script>

$(document).ready(function () {

	// 일정 추가
	$("#btnWrite").on("click", function(){
		location.href = "./examSetSchWrite.php";
	})
});

</script>
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
