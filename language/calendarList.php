<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];

	$resultArray = fnGetRequestParam($valueValid);

?>
<?php
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
<script type="text/javascript" src='/_resources/components/bootstrap-datepicker/bootstrap-datepicker.min.js'></script>

<style type="text/css">

/*	.fc-sat { background-color:#0000FF; }	/* 토요일 */
/*	.fc-sun { background-color:#FF0000; }	/* 일요일 */

	.fc-sat .fc-day-number { color:#0000FF; }	/* 토요일 */
	.fc-sun .fc-day-number { color:#FF0000; }	/* 일요일 */
</style>

<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">달력설정</h3>
			<!-- 테이블 1-->
			<div class="box_bs">
				<div id='calendar'></div>

			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

<script type="text/javascript">
$(document).ready(function () {

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
			} 
		],
		events: [
			{
				title: 'All Day Event',
				start: '2018-09-01'
			},
			{
				title: 'Long Event',
				start: '2018-09-07',
				end: '2018-09-10'
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: '2018-09-09T16:00:00'
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: '2018-09-16T16:00:00'
			},
			{
				title: 'Conference',
				start: '2018-09-11',
				end: '2018-09-13'
			},
			{
				title: 'Meeting',
				start: '2018-09-12T10:30:00',
				end: '2018-09-12T12:30:00'
			},
			{
				title: 'Lunch',
				start: '2018-09-12T12:00:00'
			},
			{
				title: 'Meeting',
				start: '2018-09-12T14:30:00'
			},
			{
				title: 'Happy Hour',
				start: '2018-09-12T17:30:00'
			},
			{
				title: 'Dinner',
				start: '2018-09-12T20:00:00'
			},
			{
				title: 'Birthday Party',
				start: '2018-09-13T07:00:00'
			},
			{
				title: 'Click for Google',
				url: 'http://google.com/',
				start: '2018-09-28'
			}
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



});
</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>