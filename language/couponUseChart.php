<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "204";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];

	$resultArray = fnGetRequestParam($valueValid);
	
	$sql  = " SELECT ";
	$sql .= "	A.Coup_code, Dept_Name, comp_name, coup_name, [dbo].f_Coup_scv_type_name(svc_type) AS svcNm, svc ";
	$sql .= "	, CONVERT(CHAR(10), A.usable_Startday, 23) AS usable_Startday, CONVERT(CHAR(10), A.usable_endday, 23) AS usable_endday, coup_count, ok_CHK	";
	$sql .= "	, ( SELECT COUNT(*) FROM Coup_List_User (nolock) WHERE A.Coup_code = Coup_code AND use_day IS NOT NULL ) AS use_count	";
	$sql .= " FROM Coup_Info as A (nolock) 	";
	$sql .= " JOIN Adm_info as B (nolock) on A.apply_id = B.Adm_id and A.applyType = B.AdmType 	";
	$sql .= " JOIN Adm_Dept_Info as C (nolock) on B.Dept_Code = C.Dept_Code 	";
	$sql .= " JOIN Coup_Service as D (nolock) on A.Coup_code = D.Coup_code	";
	$sql .= " WHERE SB_coup_cate != '응시권' ". $where;
	$sql .= " ORDER BY A.Coup_code DESC ";

	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
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
			<h3 class="title">사용량 통계</h3>

			<!-- sorting area -->
			<div class="box_sort c_txt">
				<div class="item"> 
					<select style="width: 300px;">  
						<option> [토익] 352회 | 2018.03.31 (일)[접수중]</option> 
						<option>선택 둘</option> 
						<option>선택 셋</option> 
					</select>
					<select style="width: 300px;">  
						<option> [토익] 352회 | 2018.03.31 (일)[접수중]</option> 
						<option>선택 둘</option> 
						<option>선택 셋</option> 
					</select>
				</div>		
			</div>
			<!-- sorting area -->

			<!-- 테이블1 -->
			<div class="box_bs">
				<div id="divChart" style="height:400px;"></div>

				<p class="fl_l pad_b10">전체 발급 수량 : <strong><?=$totalRecords?></strong>매</p>
				
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
								<th>날짜</th>
								<th>사용</th>
								<th>미사용</th>
								<th>사용율(%)</th>
							</tr>
						</thead>
						<tbody>
<?php
	foreach($arrRows as $data) {
?>
<?php
	}
?>
					  </tbody>
					</table>
				</div>

			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
$(document).ready(function () {

	
});

	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
		var data = google.visualization.arrayToDataTable([
			["month", "사용" ],
			["1월", 10],
			["2월", 20],
			["3월", 30],
			["4월", 40],
			["5월", 50],
			["6월", 50],
			["7월", 50],
			["8월", 50],
			["9월", 50],
			["10월", 50],
			["11월", 50],
			["12월", 50],
		]);

		var options = {
			title: ''
		};

		var chart = new google.visualization.ColumnChart(document.getElementById("divChart"));
		chart.draw(data, options);
	}

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>

