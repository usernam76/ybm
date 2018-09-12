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

	$listYear = fnNoInjection($_REQUEST['listYear']);
	$listMonth = fnNoInjection($_REQUEST['listMonth']);
	if(empty($listYear)) $listYear = date("Y");
	if(empty($listMonth)) $listMonth = date("m");


	$coulmn = "
		EI.[Exam_code]
		,EI.[SB_Exam_cate]
		,EI.[Exam_num]
		,EI.[Exam_Name]
		,convert(char(16),EI.[Exam_day],120) as Exam_day
		,convert(char(13),EI.[Score_day],120) as Score_day
		,EI.[Exam_start_time]
		,EI.[check_in_time]
		,convert(char(13),EI.[gen_regi_Start],120) as gen_regi_Start
		,convert(char(13),EI.[gen_regi_End],120) as gen_regi_End
		,convert(char(13),EI.[spe_regi_Start],120) as spe_regi_Start
		,convert(char(13),EI.[spe_regi_End],120) as spe_regi_End
		,convert(char(13),EI.[ref_first_start],120) as ref_first_start
		,convert(char(13),EI.[ref_first_end],120) as ref_first_end
		,convert(char(13),EI.[ref_sec_start],120) as ref_sec_start
		,convert(char(13),EI.[ref_sec_end],120) as ref_sec_end
		,convert(char(13),EI.[regi_ext_end],120) as regi_ext_end
		,convert(char(13),EI.[score_change_start],120) as score_change_start
		,convert(char(13),EI.[score_change_end],120) as score_change_end
		,EI.[conf_type]
		,EI.[update_day]
		,EI.[ok_id]
		,EI.[okType]
		,GI.[sell_price]
		,GI.[SB_goods_type]
	";

	$pArray = null;
	$sql = " SELECT ".$coulmn. " FROM ";
	$sql .= "  [theExam].[dbo].[Exam_Info] AS EI ";
	$sql .= "  LEFT OUTER JOIN ";
	$sql .= "  [theExam].[dbo].[Exam_Goods] AS EG ";
	$sql .= "  on EI.Exam_code = EG.Exam_code ";
	$sql .= "  INNER JOIN ";
	$sql .= "  [theExam].[dbo].[Goods_info] as GI ";
	$sql .= "  on GI.goods_code = EG.goods_code ";
	$sql .= " WHERE ";
	$sql .= "  convert(char(4),EI.[gen_regi_start],120)=:listYear";

	//$pArray[':SBExamCate']	= $SBExamCate;
	$pArray[':listYear']				= $listYear;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>

<link href="/_resources/components/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" media="screen">
<link href="/_resources/components/fullcalendar/fullcalendar.print.min.css" rel="stylesheet" type="text/css" media="print">
<link href="/_resources/components/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src='/_resources/components/fullcalendar/lib/moment.min.js'></script>
<script type="text/javascript" src='/_resources/components/fullcalendar/fullcalendar.min.js'></script>
<script type="text/javascript" src='/_resources/components/fullcalendar/locale/ko.js'></script>
<script type="text/javascript" src="/_resources/components/fullcalendar/gcal.js"></script>

<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">시험일정관리</h3>
			<div class="box_sort c_txt">
				<span class="fx_r">
					<button class="btn_fill btn_md" id="btnList" type="button">리스트 보기</button>
					<button class="btn_line btn_md" id="btnPrint" type="button">인쇄</button>
				</span>
			</div>
			<!-- 테이블1 -->
			<div class="box_bs">
				<div id='calendar'></div>
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

<script>

$(document).ready(function () {

	// 리스트 년도변경
	$("#listYear").on("change",function(){
		location.href = "?listYear="+$(this).val();
	});

	// 인쇄
	$("#btnPrint").on("click", function(){
		/*
		@ PDF 출력이 들어갑니다.
		@ 97 슬라이드
		*/
		alert('PDF 출력이 들어갑니다.');
	})

	// 리스트보기
	$("#btnList").on("click", function(){
		location.href = "./examSetSchList.php";
	});


	onSetCalendar();

});

function onSetCalendar(){

	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	$('#calendar').fullCalendar({
		locale: 'ko',
		header: {left: 'prev,next today', center: 'title', right: 'month,basicWeek,basicDay' },
		defaultView: "month",
		selectable: true,
		navLinks: true, // can click day/week names to navigate views
		editable: true,
		eventLimit: true, // allow "more" link when too many events
		googleCalendarApiKey: "AIzaSyDcnW6WejpTOCffshGDDb4neIrXVUA1EAE", //google API KEY
		eventSources : [
			// 대한민국의 공휴일
			{
				googleCalendarId : "ko.south_korea#holiday@group.v.calendar.google.com"
				, className : "koHolidays"
				, color : "#FF0000"
				, textColor : "#FFFFFF"
			},
			'./examSetSchCalData.php'
		],
		dayClick: function(dateTime, jsEvent, view){
			var dateTime = $.fullCalendar.moment(dateTime).format();
		},
		eventClick: function(calEvent, jsEvent, view){
		},
		eventDrop: function(event, delta, revertFunc){
		},
		eventRender: function(calEvent, element) {
			element.attr("data-toggle", "tooltip");
			if(calEvent.source.className=="koHolidays"){ // 공휴일 이벤트 일 경우 이벤트 속성 세팅
				element.attr("title", calEvent.title);
				element.attr("href", "#");
			}else{ // 일반 이벤트 일 경우 이벤트 속성 세팅
				element.attr("title", calEvent.userName + " ("+calEvent.posName+")");
			}
		},	
		viewRender: function(view, element){
			/* 다른 이벤트에도 사용을 위해 fullCalendar의 data에 담는다. (fullCalendar가 호출된 이후에 어디서든 호출 가능) */ 
			$("#calendar").data("date", {start: view.start, end: view.end}); // fullCalendar 플러그인에서 현재 view에 나오는 첫째날과 마지막날의 날짜
			$("#calendar").data("viewName", view.name); // fullCalendar 플러그인의 view 형태 (month, listDay, day etc..)
			/***************************************************************************************************************/
		}
	});
}

</script>
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
