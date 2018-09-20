<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>YBM TOTAL EXAM</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" type="text/css" href="/_resources/css/default.css" media="all">
	<link rel="stylesheet" type="text/css" href="/_resources/css/nanumbarungothic.css" media="all">
	<link rel="stylesheet" type="text/css" href="/_resources/components/jquery/jquery-ui.min.css" media="all">
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript" src="/_resources/js/jquery.js"></script>
	<script type="text/javascript" src="/_resources/js/common.js"></script>
	<script type="text/javascript" src="/_resources/components/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="/_resources/components/jquery/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/_resources/components/jquery/jquery.number.min.js"></script>
	<script type="text/javascript" src="/_resources/components/jquery/jquery.json-2.4.js"></script>
	<script type="text/javascript" src="/_resources/components/jquery-validation/jquery.validate.min.js"></script>
	<script type="text/javascript" src="/_resources/js/devCommon.js"></script>

<!-- // JS FILES -->
<script type="text/javascript">

$(document).ready(function () {
	//2deth 이동
	$("#menuIdx2").on("change", function () {
		location.href = $(this).val();
	});

	//숫자만 입력
	common.string.onlyNumber2( $(".onlyNumber2") );

	//달력
	$( ".datepicker" ).datepicker({
		dateFormat: 'yy-mm-dd'
		,prevText : '이전달'
		,nextText : '다음달'
		,showOtherMonths: true //빈 공간에 현재월의 앞뒤월의 날짜를 표시
		,showMonthAfterYear:true //년도 먼저 나오고, 뒤에 월 표시
		,showOn: "both" //button:버튼을 표시하고,버튼을 눌러야만 달력 표시 ^ both:버튼을 표시하고,버튼을 누르거나 input을 클릭하면 달력 표시  
		,buttonImageOnly: false //기본 버튼의 회색 부분을 없애고, 이미지만 보이게 함
		,buttonText: "선택" //버튼에 마우스 갖다 댔을 때 표시되는 텍스트                
		,yearSuffix: "년" //달력의 년도 부분 뒤에 붙는 텍스트
		,monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'] //달력의 월 부분 텍스트
		,monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'] //달력의 월 부분 Tooltip 텍스트
		,dayNamesMin: ['일','월','화','수','목','금','토'] //달력의 요일 부분 텍스트
		,dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'] //달력의 요일 부분 Tooltip 텍스트
	});
	$(".ui-datepicker-trigger").css({'font-size':'0px', 'line-height':'0px', 'margin-top':'-2px', 'margin-left':'3px'});
	$(".ui-datepicker-trigger").addClass('btn_sm_calendar');

	//기간설정
	$(".btnDaySet").on("click", function () {

		var date = new Date();
		var edate = new Date();

		if( $(this).attr("data-dayType") == "day" ){
			edate.setDate( parseInt(edate.getDate())+parseInt($(this).attr("data-day")) );
		}else if( $(this).attr("data-dayType") == "month" ){
			edate.setMonth( parseInt(edate.getMonth())+parseInt($(this).attr("data-day")) );
		}else if( $(this).attr("data-dayType") == "year" ){
			edate.setFullYear( parseInt(edate.getFullYear())+parseInt($(this).attr("data-day")) );
		}

		if( $(this).attr("data-dayKind") == "0" ){
			$('#'+$(this).attr("data-sDay")).val( date.toISOString().slice(0,10) );
			$('#'+$(this).attr("data-eDay")).val( edate.toISOString().slice(0,10) );
		}else{
			$('#'+$(this).attr("data-sDay")).val( edate.toISOString().slice(0,10) );
			$('#'+$(this).attr("data-eDay")).val( date.toISOString().slice(0,10) );
		}
	});



});

</script>
</head>


