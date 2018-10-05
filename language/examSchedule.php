<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "1209";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	$SBExamCate = "TOE";	// 토익시험을 기준으로 개발.

	$listYear = fnNoInjection($_REQUEST['listYear']);
	$listMonth = fnNoInjection($_REQUEST['listMonth']);
	if(empty($listYear)) $listYear = date("Y");
	if(empty($listMonth)) $listMonth = date("m");
?>
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>

<link href="/_resources/components/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" media="screen">
<link href="/_resources/components/fullcalendar/fullcalendar.print.min.css" rel="stylesheet" type="text/css" media="print">

<script type="text/javascript" src='/_resources/components/fullcalendar/lib/moment.min.js'></script>
<script type="text/javascript" src='/_resources/components/fullcalendar/fullcalendar.min.js'></script>
<script type="text/javascript" src='/_resources/components/fullcalendar/locale/ko.js'></script>
<script type="text/javascript" src="/_resources/components/fullcalendar/gcal.js"></script>

<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">Exam 스케줄</h3>
			<!-- 테이블 1-->
			<div class="box_bs">
				<input type="hidden" id="dp">
				<div id='calendar'></div>
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

<script type="text/javascript">
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
		header: {left: 'prev,next today', center: 'title', right: 'month,basicWeek,basicDay, gotoDate' },
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
			} ,
			'./examScheduleData.php'
		],
		customButtons: {
			gotoDate: { 
				text: "날짜이동"
			},
		},
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
				element.attr("title", calEvent.title );
				if( calEvent.kind == "1" ){
//					element.find('.fc-title').prepend('<span class="bl test">시험</span>'); 
				}else{
//					element.find('.fc-title').prepend('<span class="bl test">시험</span>'); 
				}
			}
		},	
		viewRender: function(view, element){
			/* 다른 이벤트에도 사용을 위해 fullCalendar의 data에 담는다. (fullCalendar가 호출된 이후에 어디서든 호출 가능) */ 
			$("#calendar").data("date", {start: view.start, end: view.end}); // fullCalendar 플러그인에서 현재 view에 나오는 첫째날과 마지막날의 날짜
			$("#calendar").data("viewName", view.name); // fullCalendar 플러그인의 view 형태 (month, listDay, day etc..)
			/***************************************************************************************************************/
		}
	});

	/* 날짜이동 custom 버튼에 datepicker 플러그인 적용 */
	$("#box_bs").width()

	$("#dp").datepicker({
		dateFormat: 'yy-mm-dd',
        onSelect: function(dateText, inst) {
			$('#calendar').fullCalendar("gotoDate", dateText);
        },
        beforeShow: function (input, inst) {
			inst.dpDiv.css({marginTop: 30 + 'px', marginLeft: $(".box_bs").width() - 210 + 'px'});
        }
    });

    $(".fc-gotoDate-button").click(function() {
        $("#dp").datepicker("show");
    });

	/* 날짜이동 custom 버튼에 tooltip 표기 */
	$(".fc-gotoDate-button").attr({
		"data-toggle":"tooltip",
		"title": "연월 클릭 → 연도 클릭시 연도이동이 가능합니다."
	});
}

</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>