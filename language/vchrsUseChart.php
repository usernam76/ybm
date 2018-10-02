<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "1214";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];

	$resultArray = fnGetRequestParam($valueValid);

	if( $pSYear == "" ){
		$pSYear = date('Y');
	}
	$cStartDay	= $pSYear."-01-01";
	$cEndDay	= $pSYear."-12-31";

	$sql  = " SELECT ";
	$sql .= "	coup_name, coup_code ";
	$sql .= " FROM Coup_Info as A (nolock) 	";
	$sql .= " WHERE SB_coup_cate = '응시권' ";
	$sql .= "	AND ((usable_Startday >= :StartDay1 AND usable_Startday <= :EndDay1) OR (usable_Endday >= :StartDay2 AND usable_Endday <= :EndDay2)) AND ok_CHK = 'O' ";
	$sql .= " ORDER BY Coup_code DESC ";

	$pArrayCoup[':StartDay1']	= $cStartDay;
	$pArrayCoup[':EndDay1']		= $cEndDay;
	$pArrayCoup[':StartDay2']	= $cStartDay;
	$pArrayCoup[':EndDay2']		= $cEndDay;

	$arrRowsCoup = $dbConn->fnSQLPrepare($sql, $pArrayCoup, ''); // 쿼리 실행

	if( count($arrRowsCoup) > 0 && $pSCoupCode == "" ){
		$pSCoupCode = $arrRowsCoup[0]['coup_code'];
	}

	if( $pSCoupCode != "" ){
		$sql  = " SELECT COUNT(*) AS totalRecords ";
		$sql .= " FROM Coup_List_User as A (nolock) 	";
		$sql .= " WHERE Coup_code = :coupCode ";

		$pArrayChart[':coupCode'] = $pSCoupCode;

		$arrRowsTotal = $dbConn->fnSQLPrepare($sql, $pArrayChart, ''); // 쿼리 실행
		$totalRecords	= $arrRowsTotal[0]['totalRecords'];

		$sql  = " SELECT ";
		$sql .= "	datepart(month, use_day) as month	";
		$sql .= "	, sum(case when use_day is not null then 1 else 0 end) as use_count	";
		$sql .= " FROM Coup_List_User (nolock) 	";
		$sql .= " WHERE use_day is not null And Coup_code = :coupCode ";
		$sql .= " GROUP BY datepart(month, use_day)	";

		$arrRowsChart = $dbConn->fnSQLPrepare($sql, $pArrayChart, ''); // 쿼리 실행	

		$chart_array = [];

		array_push($chart_array, array("month", "사용") );
		foreach($arrRowsChart as $data) {
			array_push($chart_array, array( $data['month']."월", $data['use_count']) );
		}

		$sql  = " SELECT ";
		$sql .= "	datepart(month, use_day) as month	";
		$sql .= "	, datepart(day, use_day) as day	";
		$sql .= "	, sum(case when use_day is not null then 1 else 0 end) as use_count	";
		$sql .= " FROM Coup_List_User AS A (nolock) 	";
		$sql .= " WHERE A.Coup_code = :coupCode		";
//		$sql .= "	and use_day <= @기준일 and use_day >= @기준일	";
		$sql .= " Group by datepart(month, use_day), datepart(day, use_day)	";
		$sql .= " having datepart(month, use_day) is not null	";
		$sql .= " Order by datepart(month, use_day), datepart(day, use_day)	";
		
		$arrRowsList = $dbConn->fnSQLPrepare($sql, $pArrayChart, ''); // 쿼리 실행
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
			<h3 class="title">사용량 통계</h3>

			<!-- sorting area -->
<form name="frmSearch" id="frmSearch" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
			<div class="box_sort c_txt">
				<div class="item"> 
					<select name="sYear" id="sYear">
<?php
	for($i=0;$i<5;$i++){
		if( $pSYear == date('Y')-$i ){
			echo "<option value='".(date('Y')-$i)."' SELECTED>".(date('Y')-$i)."</option>";
		}else{
			echo "<option value='".(date('Y')-$i)."'>".(date('Y')-$i)."</option>";
		}
	}
?>
					</select>&nbsp;&nbsp;
					<select name="sCoupCode" id="sCoupCode">
<?php
	foreach($arrRowsCoup as $data) {
		if( $pSCoupCode == $data['coup_code'] ){
			echo "<option value='".$data['coup_code']."' SELECTED>".$data['coup_name']."</option>";
		}else{
			echo "<option value='".$data['coup_code']."' >".$data['coup_name']."</option>";
		}
	}
?>
					</select>
				</div>		
			</div>
</form> 
			<!-- sorting area -->

			<!-- 테이블1 -->
<?php
	if( count($arrRowsChart) > 0 ){
?>
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
	$useCount		= 0;
	$notUseCount	= $totalRecords;
	$totalCount		= $totalRecords;
	foreach($arrRowsList as $data) {
		$useCountPer = 0;

		$notUseCount -= $data['use_count'];

		if ( $notUseCount <= 0 ){
			$useCountPer = 100;
		}else if( $data['use_count'] > 0 && $notUseCount > 0  ){
			$useCountPer = ( $totalCount - $notUseCount ) / $totalCount * 100;
		}
		echo "<tr><td>".$data['month']."월 ".$data['day']."일</td><td>".$data['use_count']."</td><td>".$notUseCount."</td><td>".number_format($useCountPer,2)."%</td></tr>";
	}
?>
					  </tbody>
					</table>
				</div>

			</div>
			<!-- //테이블1-->
<?php
	}
?>
		</div>
	</div>
</div>
<!--right //-->

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
$(document).ready(function () {

	$("#sYear").on("change", function(){
		$("#sCoupCode").val("");
		$('#frmSearch').submit();
    });

	$("#sCoupCode").on("change", function(){
		$('#frmSearch').submit();
    });

	
});

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
	var data = google.visualization.arrayToDataTable( <?=json_encode($chart_array)?> );

	var view = new google.visualization.DataView(data);
	view.setColumns([0, 1]);

	var options = {
		title: ''
	};

	var chart = new google.visualization.ColumnChart(document.getElementById("divChart"));
	chart.draw(view, options);

/*
	var selectHandler = function(e) {
		alert( data.getValue(chart.getSelection()[0]['row'], 0) );
   }
   google.visualization.events.addListener(chart, 'select', selectHandler);
*/

}

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>

