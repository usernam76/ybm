/**
 * 일정 javascript 통합
 */
var calendarTagId = "calendarTag"; // FullCalendar 플러그인이 표시되는 태그 아이디 값
var shareTagId = "shareUserTag"; // JsTree 플러긍인이 표시되는 태그 아이디 값

$(document).ready(function(){
//	loadTree(); // 조직도 로드 (JsTree 플러그인  사용)
	loadCalendar(); // 캘린더 및 캘린더 이벤트 로드 (FullCalendar 플러그인 사용)
//	getShareUserListAjax("", ""); // 공유자 전체 목록을 가져와서 jstree 플러그인에 데이터를 입력
	
	$("#btnDisplayMeEvent").prop("checked", true); // 내 일정 포함 체크 된 상태로 시작
	$("#btnDisplayHolidayEvent").prop("checked", true); // 공휴일 일정 포함 체크 된 상태로 시작
});

/* 이벤트 등록/수정 validation 및 실행 */
$(document)
	.on("click", "#btnInsertUpdateEvent", function(){
		$("#scheduleForm").validate({
			rules: {
				compName: { required: true },
				title: { required: true },
				startDate: { required: true},
				startTime: { required: true},
				endDate : { required: true},
				endTime : { required: true}
			}, 
			messages: {
				compName: { required: "필수 입력값입니다." },
				title: { required: "필수 입력값입니다." },
				startDate: { required: "필수 입력값입니다."},
				startTime: { required: "필수 입력값입니다."},
				endDate: { required: "필수 입력값입니다."},
				endTime: { required: "필수 입력값입니다."}
			}, 
			invalidHandler: function(form, validator) {
				validator.errorList[0].element.focus();
			}
		});
		
		if($("#scheduleForm").valid()){ //$("#scheduleForm").submit();
			var flag = true; // 기본값 true 
			if($("#repeatCheck").prop("checked")){ // 반복일정 이벤트인지 체크 (true 반복O / false 반복X)
				flag = checkEventRepeat(); // 반복일정 등록 세팅 (flag : 반복일정 세팅 결과 완료 여부 체크 - true 성공 / false 실패)
			}			

			if(flag){
				if($("#mode").val()=="M" && $("#schRepIdx").val() != ""){	// 반복일정이면
					if($("#UDAllRepeat").prop("checked")){
						$("#schIdx").val("");
					}else{
						$("#schRepIdx").val("");
					}
				}

				$("#func").val("IUEventAjax");
				var formData = $("#scheduleForm").serialize();
				$.ajax({
					type: "post",
					url: "/schedule/func.php",
					data: formData,
					success: function(data){
						if(data.trim()=="1"){
							getCalendarEventListAjax($("#"+calendarTagId).data("date").start, $("#"+calendarTagId).data("date").end, ""); // viewRender 호출시마다 이벤트 새로 가져옴.					
						}else{
							alert("오류가 발생했습니다. 관리자에게 연락해주세요." + data);
							return;				
						}
						setCalendarEventClose();
					},
					error: function(request,status,error){
						alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});
			}
		}
	})
	.on("change", "#btnDisplayMeEvent", function(e){ // 내 일정 보이기/숨기기
		var start = $("#"+calendarTagId).data("date").start;
		var end = $("#"+calendarTagId).data("date").end;
		if($(this).prop("checked")){
			$(this).val("1");
		}else{
			$(this).val("0");
		}
		getCalendarEventListAjax(start, end, '');
	})
	.on("change", "#btnDisplayHolidayEvent", function(e){ // 내 일정 보이기/숨기기
		getHolidayEvent();  // 공휴일 이벤트 추가/삭제
	}
);

/* 이벤트 삭제 */
$(document).on("click", "#btnDeleteEvent", function(){
	var flag = true;
	if(confirm("일정을 삭제하시겠습니까?")){
		$("#func").val("DEventAjax");
		if($("#schRepIdx").val() != ""){	// 반복일정이면
			if($("#UDAllRepeat").prop("checked")){
				$("#schIdx").val("");
			}else{
				$("#schRepIdx").val("");
			}
		}
	}else{
		flag = false;
	}

	if(flag){
		var formData = $("#scheduleForm").serialize();
		$.ajax({
			type: "post",
			url: "/schedule/func.php",
			data: formData,
			success: function(data){
				if(data.trim()=="1"){
					getCalendarEventListAjax($("#"+calendarTagId).data("date").start, $("#"+calendarTagId).data("date").end, ""); // viewRender 호출시마다 이벤트 새로 가져옴.					
				}
				setCalendarEventClose();
			},
			error: function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
});



/* 캘린더 플러그인 호출
 	select : 캘린더의 날짜를 선택했을 때 실행
 	eventClick : 캘린더의 이벤트를 선택했을 때 실행
 	eventDrop : 캘린더 이벤트를 이동했을 때 실행
 	viewRender : 캘린더 새 기간이 렌더링 되거나 유형이 전환 될때 trigger 됨 */
function loadCalendar(){
	var hOption = ""; // contentHeight 옵션 (pc : "" // mobile : "auto")
	if(isMobile()){
		hOption = "auto";
	}	
	
 	$("#" + calendarTagId).fullCalendar({
		locale: "ko",
		header: { left : "prev,next, today", center : "title", right : "month,listWeek,listDay, gotoDate, expandCalendar"	},
		views: { listWeek: { buttonText: "주"}, listDay: { buttonText: "일" } },
		businessHours: { dow : [1,2,3,4,5], start : "08:00", end : "19:00" },
		eventLimit: true,
		editable: true,
		selectable: true,
		lazyFetching: false,
		nextDayThreshold: "00:00",
		minTime: "07:00",
		contentHeight: hOption,
		eventLimit: true,
		googleCalendarApiKey: "AIzaSyDcnW6WejpTOCffshGDDb4neIrXVUA1EAE", //google API KEY
		customButtons: {
			gotoDate: { 
				text: "날짜이동"
			},
			expandCalendar: { 
				text: "확장+", 
				click: function(){
					/* 확장버튼을 눌렀을 때 */
					if($(".col-calendar").hasClass("col-md-8")){
						$(".col-calendar").removeClass("col-md-8"); // 캘린더 box div 확장
						$(".col-calendar").addClass("col-md-12");
						$(".col-share").removeClass("col-md-4"); // 공유 box 아래 row로 보내고 12로 확장
						$(".col-share").addClass("col-md-12");
						$(".share-user").parent().removeClass("col-lg-3"); // 공유자 버튼 한 row에 많이 나오도록
						$(".share-user").parent().addClass("col-lg-2");
						$(".fc-expandCalendar-button").text("-"); // 확장버튼을 축소하기 버튼으로 표기변경
					/* 축소버튼을 눌렀을 때 */
					}else{														 // 축소 버튼
						$(".col-calendar").removeClass("col-md-12"); // 캘린더 box div 축소
						$(".col-calendar").addClass("col-md-8");
						$(".col-share").removeClass("col-md-12"); // 공유 box 윗 row로 보내고 4로 축소
						$(".col-share").addClass("col-md-4");
						$(".share-user").parent().removeClass("col-lg-2"); // 공유자 버튼 한 row에 적게 나오도록
						$(".share-user").parent().addClass("col-lg-3");
						$(".fc-expandCalendar-button").text("확장+"); // 축소버튼을 확대하기 버튼으로 표기 변경
					}
				}
	
			}
		},
		dayClick: function(dateTime, jsEvent, view){
			var dateTime = $.fullCalendar.moment(dateTime).format();
			getEventFormAjax(dateTime, dateTime, "", "", "W");
		},
		eventClick: function(calEvent, jsEvent, view){
			if(calEvent.source.className!="koHolidays"){
				getEventFormAjax("", "", calEvent.schIdx, calEvent.userId, "R");
			}			
		},
		eventDrop: function(event, delta, revertFunc){
			updateEventDateAjax(event.userId, event.schIdx, delta._days);
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
			$("#" + calendarTagId).data("date", {start: view.start, end: view.end}); // fullCalendar 플러그인에서 현재 view에 나오는 첫째날과 마지막날의 날짜
			$("#" + calendarTagId).data("viewName", view.name); // fullCalendar 플러그인의 view 형태 (month, listDay, day etc..)
			/***************************************************************************************************************/
			getShareUserListAjax($("#"+calendarTagId).data("date").start, $("#"+calendarTagId).data("date").end)
			getCalendarEventListAjax($("#"+calendarTagId).data("date").start, $("#"+calendarTagId).data("date").end, ""); // viewRender 호출시마다 이벤트 새로 가져옴.
		}
	});

	/* 날짜이동 custom 버튼에 datepicker 플러그인 적용 */
	$(".fc-gotoDate-button").datepicker({ 
		autoclose: true,
		format: "yyyy-mm-dd",
		language: "kr"
	}).on("changeDate", function(e){ // datepicker 달력의 날짜 클릭 시 fullcalendar 날짜 이동
		$("#calendarTag").fullCalendar("gotoDate", e.date);
	});

	/* 날짜이동 custom 버튼에 tooltip 표기 */
	$(".fc-gotoDate-button").attr({
		"data-toggle":"tooltip",
		"title": "연월 클릭 → 연도 클릭시 연도이동이 가능합니다."
	});
}

/* 각종 모달 및 폼의 닫기버튼 누를 때 PC, Mobile 에 따른 태그 세팅*/
$(document).on("click", "#btnCloseEvent", function(e){	
	if(isMobile()){ // 모바일일 경우 일반 태그 생성
		$("#"+ calendarTagId).show(); 
		$("#eventFormTag").hide();
	}else{ // 모바일이 아닐 경우 모달 태그 생성
		$("#eventModal").modal("hide");
	}
});

/* 이벤트 입력/수정/상세 폼 생성 */
function getEventFormAjax(startDate, endDate, schIdx, userId, mode){
	var dataTag = "";
	var url = "";
	if(isMobile()){ // 모바일일 경우 일반 태그 생성
		dataTag = "eventFormTag";
		$("#"+calendarTagId).hide(); 
		$("#eventFormTag").show();
	}else{ // 모바일이 아닐 경우 모달 태그 생성
		dataTag = "eventDetail";
		$("#eventModal").modal("show");
	}
	
	if(mode=="R"){ // R
		url = "view.php";
	}else{ // W, M
		url = "write.php";
	}
	$.ajax({
		type: "post",
		url: "/schedule/" + url,
		data: { "schIdx": schIdx, "start": startDate, "end": endDate, "mode": mode, "userId": userId },
		success: function(data){
			$("#"+dataTag).html(data);			
		},
		error: function(request,status,error){
	        alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	});
}

/* 이벤트 입력 폼 닫을 때 PC, Mobile 에 따른 view 세팅 */
function setCalendarEventClose(){
	if(isMobile()){ // 모바일일 경우 일반 태그 생성
		$("#"+calendarTagId).show(); 
		$("#eventFormTag").hide();
	}else{ // 모바일이 아닐 경우 모달 태그 생성
		$("#eventModal").modal("hide");
	}
}

/* 
	이벤트 호출 
	- 공유 박스의 개별 공유자 버튼을 누를 경우 clickUesr로 버튼에 해당하는 userId를 가져온다.
	- 일반적으로 일정만 가져올 때는 clickUser = "" 로 넘기면 된다.
*/
function getCalendarEventListAjax(startDate, endDate, clickUser){
	 // 캘린더 이벤트 초기화를 위해 기존 이벤트 삭제
	var startDate = $.fullCalendar.moment(startDate).format();
	var endDate = $.fullCalendar.moment(endDate).format();
	var shareUser = ""; // 나에게 공유한 사람들 id 리스트

	if(clickUser==""){ // default 상황 (내 일정과, 조직도에 체크된 사람의 이벤트를 호출한다)
		shareUser = getSharedUserIdList(); // 조직도에 체크된 사람 목록(공유한 사람)을 아이디1#아이디2#아이디3#아이디4 형태로 가져옴
		var shareUserArray = shareUser.split("#");
		
		$(".btn-share[id^=btn-user]").each(function(i){
			$(this).removeClass("share-btn-action");
			$(this).html($(this).data("usertext"));
		});
		for(var i=0;i<shareUserArray.length;i++){
			var userName = $(".btn-share#btn-user"+shareUserArray[i]).data("usertext");
			$(".btn-share#btn-user"+shareUserArray[i]).addClass("share-btn-action");
			$(".btn-share#btn-user"+shareUserArray[i]).html("<i class='glyphicon glyphicon-ok'></i>&nbsp;" + userName);
		}
		var displayMyEvent = $("#btnDisplayMeEvent").val();

		$.ajax({
			type: "POST",
			url: "/schedule/func.php",
			asynce: false,
			data: { "displayMyEvent" : displayMyEvent, "shareUserId": shareUser, "start": startDate, "end": endDate, "func": "getEventListAjax" },
			success: function(data){
				data = $.parseJSON(data);
				var calendarViewName = $("#" + calendarTagId).data("viewName"); // fullCalendar 플러그인의 view 형태 (month, listDay, day etc..)
				if(calendarViewName == "listDay" || calendarViewName == "listWeek"){
					for(var i=0;i<data.length;i++){
						data[i].title = data[i].userName + " : " + data[i].title;
					}
				}
				var eventSources =$("#"+calendarTagId).fullCalendar("getEventSources");
				for(var i=0;i<eventSources.length;i++){
					if(eventSources[i].className=="koHolidays"){
						eventSources.splice([i], 1);
					}
				}
				$("#"+calendarTagId).fullCalendar("removeEventSources", eventSources); // 기존 데이터 삭제 후
				$("#"+calendarTagId).fullCalendar("addEventSource", data); // 새로 가져온 데이터를 add 시킨다.
				getHolidayEvent(); // 공휴일 이벤트 추가/삭제
				$("[data-toggle='tooltip']").tooltip();
			},
			error: function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}else{ 
		/* 
			공유 박스의 개별 공유자 버튼을 누를 경우 jsTree의 체크를 모두 해제 후, 누른 사용자만 체크한다. 
			조직도 체크 이벤트 안(jsTree 콜백함수)에서 getCalendarEventListAjax 함수를 호출하므로 비동기 통신 없이 체크만 한다.
			(loadTree() 함수의 "select_node.jstree deselect_node.jstree" 콜백함수 참조)
		*/
		shareUser = clickUser;
		
		var userName = $(".btn-share#btn-user"+shareUser).data("usertext");
		if($("#"+shareTagId).jstree(true).is_selected(shareUser)){
			$("#"+shareTagId).jstree(true).uncheck_node(shareUser);	
			$(".btn-share#btn-user"+shareUser).removeClass("share-btn-action");
			$(".btn-share#btn-user"+shareUser).html(userName);
		}else{
			$("#"+shareTagId).jstree(true).check_node(shareUser);
			$(".btn-share#btn-user"+shareUser).addClass("share-btn-action");
			$(".btn-share#btn-user"+shareUser).html("<i class='glyphicon glyphicon-ok'></i>&nbsp;" + userName);
		}		
	}
}

/* 공휴일 이벤트 입력 (구글 한국 공휴일 구독) */
function getHolidayEvent(){
	var holidayData = { // 구글 캘린더 한국 공휴일 구독 데이터
		googleCalendarId : "ko.south_korea#holiday@group.v.calendar.google.com"
		, className : "koHolidays"
		, color : "#FF0000"
		, textColor : "#FFFFFF"
		, url : "#"
		, editable: false
	}
	if($("#btnDisplayHolidayEvent").prop("checked")){ // 공휴일 체크박스 체크가 되어있으면
		var eventSources = $("#"+calendarTagId).fullCalendar("getEventSources"); // 현재 캘린더에 입력된 이벤트 배열을 호출
		isAddedHolidayEvent = false; // 공휴일 이벤트 추가 여부
		for(var i=0;i<eventSources.length;i++){
			if(eventSources[i].className=="koHolidays") isAddedHolidayEvent = true; 
		}
		if(!isAddedHolidayEvent) $("#"+calendarTagId).fullCalendar("addEventSource", holidayData); // 공휴일 이벤트가 추가되어 있지 않을때만 공휴일 이벤트 추가
	}else{ // 공휴일 체크박스 체크가 되어있지 않으면
		$("#"+calendarTagId).fullCalendar("removeEventSource", holidayData); // 공휴일 이벤트 삭제
	}
}

/* 이벤트 날짜 수정(드래그&드롭 전용) */
function updateEventDateAjax(userId, schIdx, changedDate){
	$.ajax({
		type: "post",
		async: false,
		url: "/schedule/func.php",
		data: "&userId="+userId+"&schIdx="+schIdx+"&changedDate="+changedDate+"&mode=M&func=UEventDateAjax",
		success: function(data){
			if(data.trim()!="1") alert("오류가 발생했습니다. 관리자에게 연락해주세요.");	
			getCalendarEventListAjax($("#"+calendarTagId).data("date").start, $("#"+calendarTagId).data("date").end, ""); // viewRender 호출시마다 이벤트 새로 가져옴.
		},
		error: function(request,status,error){
	        alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"er:"+error);
	    }
	});
}

/* 
	공유자 목록 추출 
	return : userId1#userId2#userId3#userId4# 형태의 string
*/
function getSharedUserIdList(){
	var checkedObj;
	var shareUserIdList = "";
	checkedObj = $("#"+shareTagId).jstree("get_checked", true); // jstree 에서 체크된 object 추출
	$.each(checkedObj, function(i){ 
		if(this.type != "department"){ // 체크된 object 에서 유저 정보만 추출
			shareUserIdList += this.id.trim()+"#"; // 아이디1#아이디2#아이디3#아이디4# 형태로 변환
		}
	});
	return shareUserIdList;
}




/* 
	JsTree 플러그인 호출 
	refresh.jstree : jstree가 refresh 될 경우 콜백 함수
*/
function loadTree(){
	$("#"+shareTagId).jstree({
		"core": {
			"check_callback": false,
		},
		"types": {
			"member": { "icon": "hide "},
			"department": { "icon" : "text-light-blue fa fa-fw fa-folder-o"}
		},
		"plugins": ["checkbox", "types", "search"]
	}).bind("refresh.jstree", function(){
		$("#"+shareTagId).jstree("open_node", "#00000");
	}).on("select_node.jstree deselect_node.jstree", function(e, data) {
		getCalendarEventListAjax($("#"+calendarTagId).data("date").start, $("#"+calendarTagId).data("date").end, ""); // viewRender 호출시마다 이벤트 새로 가져옴.
	});
}

/* 공유목록 생성하여 JsTree에 data 삽입 후 refresh */
function getShareUserListAjax(startDate, endDate) {
	if(startDate!="" && endDate != "") {
		startDate = $.fullCalendar.moment(startDate).format();
		endDate = $.fullCalendar.moment(endDate).format();
	}
	$.ajax({
		type: "post",
		url: "/schedule/func.php",
		data: { "start": startDate, "end": endDate, "func": "getShareUserListAjax" },
		async: false,
		success: function(data){
			if(startDate!="" && endDate != ""){
				htmlTag = "";
				data = $.parseJSON(data); // 나에게 이벤트 공유한 사람 정보 호출 json type
				/* 나에게 이벤트 공유한 사람 목록 버튼 생성 */
				for(var i=0;i<data.length;i++){
					htmlTag += "<div class='col-lg-3 col-md-6 col-sm-6 col-xs-6 text-center'>";
					htmlTag += "<div class='external-event cursor-pointer btn-share share-user' id='btn-user"+data[i].userId+"' style='background-color:"+ data[i].userColor +"' onclick=\"getCalendarEventListAjax('"+ startDate+"', '"+ endDate +"', '"+ data[i].userId +"')\" data-usertext='"+ data[i].userName +"' data-userid='"+ data[i].userId +"'>";
					htmlTag += data[i].userName; /*+ " [" + data[i].scheduleCount + "]";*/
					htmlTag += "</div>";
					htmlTag += "</div>";
				}
				$("#shareUserBtn").html(htmlTag);
			}else{
				data = $.parseJSON(data);
				$("#"+shareTagId).jstree(true).settings.core.data = data;
				$("#"+shareTagId).jstree(true).refresh();
			}
    	},
		error: function(request,status,error){
	        alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	});
}

