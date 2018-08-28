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


	$sql = ' SELECT ';
	$sql .= ' A.[Menu_idx],A.[Menu_Name],A.[Menu_order],A.[Menu_depth],B.[Page_URL] ';
	$sql .= ' FROM ';
	$sql .= ' [theExam].[dbo].[Menu_Info] as A join ';
	$sql .= ' [theExam].[dbo].[Menu_Page] as B ';
	$sql .= ' on A.Menu_idx = B.Menu_idx and A.use_CHk = \'O\' ';
	$sql .= ' WHERE ';
	$sql .= ' A.Menu_depth = 1 AND ';
	$sql .= ' A.use_CHK = \'O\'';
	$sql .= ' ORDER BY A.Menu_order asc ';


	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
	
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>

<style>
/*on class 모니터 밝기 때문에 안보여서 강제로 넣음. 완료시 삭제*/
.on{background-color:red !important; font-weight:700 !important;}
</style>
<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">메뉴관리</h3>
			<ul class="menu_manage_list">
				<li>
					<div class="box_bs">
						<p class="stit">1Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey btnAddMenu" data-depth="1" type="button" style="width:100%" id="myBtn">+ 메뉴 추가하기</button></p>
						<div class="box_ln">
							<ul>
							<?php
								foreach($arrRows as $data) {
							?>
								<li>
									<a href="#" menuIdx="<?=$data["Menu_idx"]?>" parMenuIdx="<?=$data["Par_Menu_idx"]?>" menuOrder ="<?=$data["Menu_order"]?>" menuDepth="<?=$data["Menu_depth"]?>" pageURL="<?=$data["Page_URL"]?>"><?=$data["Menu_Name"]?></a>
									<span class="btn_r">
										<button class="btn_sm_box btnModifyMenu" type="button">수정</button> 
										<button class="btn_sm_box btnDeleteMenu" type="button">삭제</button>
									</span>
								</li>
							<?php
							}
							?>
							</ul>
						</div>
						<p class="item fl_r">
							<button class="btn_arr" type="button" data-depth="1" data-change-mode="up"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button" data-depth="1" data-change-mode="down"><strong class="fs_sm">▼</strong></button>
						</p>
					</div>
				</li>
				<li class="arrow">
					<p>▶</p>
				</li>
				<li>
					<div class="box_bs">
						<p class="stit">2Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey btnAddMenu" data-depth="2" type="button" style="width:100%">+ 메뉴 추가하기</button></p>
						<div class="box_ln">
						</div>
						<p class="item fl_r">
							<button class="btn_arr" type="button" data-depth="2" data-change-mode="up"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button" data-depth="2" data-change-mode="down"><strong class="fs_sm">▼</strong></button>
						</p>
					</div>
				</li>
				<li class="arrow">
					<p>▶</p>
				</li>
				<li>
					<div class="box_bs">
						<p class="stit">3Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey btnAddMenu" data-depth="3" type="button" style="width:100%">+ 메뉴 추가하기</button></p>
						<div class="box_ln" >
						</div>
						<p class="item fl_r">
							<button class="btn_arr" type="button" data-depth="3" data-change-mode="up"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button" data-depth="3" data-change-mode="down"><strong class="fs_sm">▼</strong></button>
						</p>
					</div>
				</li>
				<li class="arrow">
					<p>▶</p>
				</li>
				<li>
					<div class="box_bs">
						<p class="stit">4Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey btnAddMenu" data-depth="4" type="button" style="width:100%">+ 메뉴 추가하기</button></p>
						<div class="box_ln">
						</div>
						<p class="item fl_r">
							<button class="btn_arr" type="button" data-depth="4" data-change-mode="up"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button" data-depth="4" data-change-mode="down"><strong class="fs_sm">▼</strong></button>
						</p>
					</div>
				</li>
			</ul>
			
		</div>
	</div>
</div>
<!--right //-->
<!-- modal 팝업 :: goal-->
<div id="myModal" class="modalPopWrite modal">
	<!-- Modal content -->
	<div class="modal-content" style="width: 400px; height: 250px;">
<form name="frmWrite" method="post">
		<span class="close"><img class="sml" src="../_resources/images/btn_x.png"></span>
		<div class="wrap_tbl">
			<div class="box_inform">
				<p class="txt_l">
					<span class="stit">메뉴 추가</span>
				</p>
			</div>
			<table class="type02">
				<caption></caption>
				<colgroup>
					<col style="width: 30%;">
					<col style="width: auto;">
				</colgroup>
				<tbody>
					<tr>
						<th>메뉴명</th>
						<td>
							<div class="item">
								<input style="width: 90%;" type="text" name="menu_name">
							</div>
						</td>
					</tr>
					<tr>
						<th>링크</th>
						<td>
							<div class="item">
								<input style="width: 90%;" type="text"  name="page_url">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<!-- 버튼 -->
		<div class="wrap_btn">
			<button class="btn_fill btn_sm btnOk" type="button">확인</button>
			<button class="btn_line btn_sm btnCancel" type="button">취소</button>
		</div>
</form>
	<!-- 버튼 //-->
	</div>
</div>


<!-- modal 팝업 :: goal-->
<div id="myModal" class="modalPopMoidfy modal">
	<!-- Modal content -->
	<div class="modal-content" style="width: 400px; height: 250px;">
<form name="frmModify" method="post">
		<span class="close"><img class="sml" src="../_resources/images/btn_x.png"></span>
		<div class="wrap_tbl">
			<div class="box_inform">
				<p class="txt_l">
					<span class="stit">메뉴 수정</span>
				</p>
			</div>
			<table class="type02">
				<caption></caption>
				<colgroup>
					<col style="width: 30%;">
					<col style="width: auto;">
				</colgroup>
				<tbody>
					<tr>
						<th>메뉴명</th>
						<td>
							<div class="item">
								<input style="width: 90%;" type="text" name="menu_name">
							</div>
						</td>
					</tr>
					<tr>
						<th>링크</th>
						<td>
							<div class="item">
								<input style="width: 90%;" type="text"  name="page_url">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<!-- 버튼 -->
		<div class="wrap_btn">
			<button class="btn_fill btn_sm btnOk" type="button">확인</button>
			<button class="btn_line btn_sm btnCancel" type="button">취소</button>
		</div>
</form>
	<!-- 버튼 //-->
	</div>
</div>


<script type="text/javascript">

var yUI = (function() {
	/*
		@auth : 최상운
		@date : 2018-08-20
		@description : 관리자 메뉴 관리 이벤트 스크립트
	*/

	/* 메뉴 순서 변경 */
	var menuOrderChangeEvent = function(){
		
		$(".btn_arr").off("click");
		$(".btn_arr").on("click", function(){
			var changeDepth = $(this).attr("data-depth");			// 바꾸는 메뉴의 depth
			var changeMode = $(this).attr("data-change-mode");	// 바꾸는 메뉴 모드(up/down)
			var changeMenu = $(".box_ln").eq(changeDepth-1).find("ul").find(".on");	// 진행하는 메뉴의 객체

			var changeMenuIdx = changeMenu.attr("menuIdx");
			var changeMenuOrder = changeMenu.attr("menuOrder");
			if(changeMenu.length < 1){
				alert("순서를 변경하려는 메뉴를 선택 해주세요");
				return false;
			}

			var changeParMenuIdx = changeMenu.attr("parMenuIdx");	// 진행하는 메뉴의 상위메뉴 객체 idx

			var targetMenu = null; 
			if(changeMode == "up"){
				targetMenu = changeMenu.parent().prev().find("a");
			}else if(changeMode == "down"){
				targetMenu = changeMenu.parent().next().find("a");
			}

			if(targetMenu.length < 1){
				alert("순서를 변경할 수 없습니다.");
				return false;
			}

			var targetMenuIdx = targetMenu.attr("menuIdx");
			var targetMenuOrder = targetMenu.attr("menuOrder");

			var u = "./adminMenuProc.php";				// 비동기 전송 파일 URL
			var param = {
				"proc" : "getMenuOrderChange",
				"changeDepth" : changeDepth, 
				"changeParMenuIdx" : changeParMenuIdx, 
				"changeMenuIdx" : changeMenuIdx,
				"changeMenuOrder" : changeMenuOrder,
				"targetMenuIdx" : targetMenuIdx,
				"targetMenuOrder" : targetMenuOrder
				,"targetMenuText" : targetMenu.text()
				,"changeMenuText" : changeMenu.text()
			}


			/* 데이터 비동기 전송*/
			$.ajax({ type:'post', url: u, dataType : 'json',data:param,
				success: function(resJson) {
					console.log(resJson)
					menuLoad(resJson);
				},
				error: function(resJson) {
					alert("현재 서버 통신이 원활하지 않습니다.");
					console.log("[Error]");
					console.log(resJson)
				}
			});
		});
	}


	/* 메뉴 IUD 제어 event*/
	var menuIUDControlEvent = function(){

		var currentDepth = 1;			// 현재 선택한 메뉴의 depth
		var currentParMenuIdx = 0;	//	현재 선택 상위메뉴의 고유번호
		var currentMenuIdx = null;

		/* 메뉴 등록 */

		// 메뉴등록 팝업 노출
		$(".btnAddMenu").off("click");
		$(".btnAddMenu").on("click", function(){
			currentDepth = $(this).attr("data-depth"); // 추가 버튼 클릭한 메뉴 depth
			currentDepth = parseInt(currentDepth, 10);
			if(currentDepth != 1){	// 1dep 메뉴는 체크X
				// 상위 메뉴 선택 체크
				var parentMenuObj = $(".box_ln").eq(currentDepth-2).find("ul").find(".on");	// 상위 메뉴객체
				if(parentMenuObj.length < 1){
					alert("상위메뉴를 선택하셔야 합니다.");
					return false;
				}
				currentParMenuIdx = parentMenuObj.attr("menuIdx");		// 상위 메뉴 고유번호
			}else{
				var parentMenuObj ;	// 1depth는 상위메뉴가 없음.
			}
			$("form[name=frmWrite]").find("[name=menu_name]").val('');
			$("form[name=frmWrite]").find("[name=page_url]").val('');
			$(".modalPopWrite").css("display", "block");
		});

		/* 메뉴수정 */
		// 메뉴수정 팝업 노출
		$(".btnModifyMenu").off("click");
		$(".btnModifyMenu").on("click", function(){
			var menuIdx = $(this).parent().prev().attr("menuIdx");	// 수정 선택 menu 고유번호
			var menuName = $(this).parent().prev().text();	// 수정 선택 menu명
			var pageURL = $(this).parent().prev().attr("pageURL");	// 수정 선택 pageURL
			var menuDepth = $(this).parent().prev().attr("menuDepth");	// 수정 선택 pageURL
			var parMenuIdx = $(this).parent().prev().attr("parMenuIdx");	// 수정 선택 pageURL
			currentMenuIdx = menuIdx;
			currentDepth = menuDepth;				// 수정하는 메뉴의 depth
			currentParMenuIdx = parMenuIdx;	// 수정하는 메뉴의 상위 메뉴 고유번호, 1depth는 0
			$("form[name=frmModify]").find("[name=menu_name]").val(menuName);
			$("form[name=frmModify]").find("[name=page_url]").val(pageURL);
			$(".modalPopMoidfy").css("display", "block");
		});


		// 메뉴 팝업 등록 및 수정
		$(".btnOk").off("click");
		$(".btnOk").on("click", function(){
			var menuDepth = currentDepth;				// 입력하는 메뉴의 depth
			var parMenuIdx = currentParMenuIdx;	// 입력하는 메뉴의 상위 메뉴 고유번호, 1depth는 0

			if($(this).parent().parent().attr("name") == "frmWrite"){	// 상단 폼에 따라 proc 변경
				var proc = "write";
				var menuName = $("form[name=frmWrite]").find("[name=menu_name]").val();
				var pageURL = $("form[name=frmWrite]").find("[name=page_url]").val();
				var menuIdx = null;
			}else{
				var proc = "modify";
				var menuName = $("form[name=frmModify]").find("[name=menu_name]").val();
				var pageURL = $("form[name=frmModify]").find("[name=page_url]").val();
				var menuIdx = currentMenuIdx;
			}

			var u = "./adminMenuProc.php";				// 비동기 전송 파일 URL

			var param = {	// 파라메터
				"proc" : proc,
				"menuIdx" : menuIdx,
				"menuName"		:	menuName,
				"pageURL"	:	pageURL,
				"menuDepth"	:	menuDepth,
				"parMenuIdx"	:	parMenuIdx
			};


			/* 데이터 비동기 전송*/
			$.ajax({ type:'post', url: u, dataType : 'json',data:param,
				success: function(resJson) {
//					console.log(resJson)
					menuLoad(resJson);

				},
				error: function(resJson) {
					alert("현재 서버 통신이 원활하지 않습니다.");
//					console.log("[Error]");
//					console.log(resJson)
				}
			});
		});

		/* 메뉴 삭제 */
		$(".btnDeleteMenu").off("click");
		$(".btnDeleteMenu").on("click", function(){

			if(confirm("삭제하시겠습니까?")){

				var menuIdx = $(this).parent().prev().attr("menuIdx");	// 수정 선택 menu 고유번호
				var menuDepth = $(this).parent().prev().attr("menuDepth");	// 수정 선택 menu 깊이

				var u = "./adminMenuProc.php";				// 비동기 전송 파일 URL
				var param = {	// 파라메터
					"proc" : "delete",
					"menuDepth"	:	menuDepth,
					"menuIdx"	:	menuIdx
				};

				/* 데이터 비동기 전송*/
				$.ajax({ type:'post', url: u, dataType : 'json',data:param,
					success: function(resJson) {
						if(resJson.status == "fail"){
							if(resJson.failcode == "90"){
								alert("하위 메뉴가 존재합니다.");
								return false;
							}
						}
						else if(resJson.status == "success"){
							menuLoad(resJson)
							return false;
						}
					},
					error: function(resJson) {
//						console.log(resJson)
						alert("현재 서버 통신이 원활하지 않습니다.");
					}
				});
			}else{
				return;
			}
		});

		/*modal close event*/
		$(".btnCancel").on("click", function(){
			$(".modal").css("display", "none");
		});

		/*modal close event*/
		$(".close").on("click", function(){
			$(".modal").css("display", "none");
		});

	} /* 메뉴 삭제 끝*/


	/* 메뉴 클릭 이벤트 > 하위 메뉴 호출 */
	var menuEvent = function(){
		$(".box_ln > ul > li > a").off("click");
		$(".box_ln > ul > li > a").on("click", function(){
			var currentMenuDepth =parseInt( $(this).attr("menuDepth") , 10);

			var chkMenu = $(this).attr("menuIdx");
			$(this).parent().parent().find("a").removeClass("on");
			$(this).addClass("on");

			if(currentMenuDepth==4 ){
				return; // 4depth 메뉴의 경우 하위 이벤트 설정x
			}
			var menuIdx = $(this).attr("menuIdx");	// menu 고유번호
			var menuDepth =$(this).attr("menuDepth");	// menu 메뉴단계
			var menuOrder = $(this).attr("menuOrder");	//	menu 정렬순서
			var parMenuIdx =$(this).attr("parMenuIdx");	// menu 상위메뉴 고유번호

			var u = "./adminMenuProc.php";	// 비동기 전송 파일 URL

			var param = {	// 파라메터
				"proc" : "getMenuLoadAjax",
				"menuIdx"		:	menuIdx,
				"menuDepth"	:	menuDepth,
				"menuOrder"	:	menuOrder,
				"parMenuIdx"	:	parMenuIdx
			};
			/* 하위 메뉴 출력 정보 전송*/
			$.ajax({ type:'post', url: u, dataType : 'json',data:param,
				success: function(resJson) {
					console.log(resJson);
					menuLoad(resJson);
				},
				error: function(resJson) {
					console.log(resJson);
					alert("현재 서버 통신이 원활하지 않습니다.");
					return false;
				}
			});
		});
	};

	/* 메뉴 클릭 이벤트 > 하위 메뉴 호출 끝*/

	/*  메뉴 리스트 업 */
	var menuLoad = function(resJson){

		var addDepth = parseInt(resJson.depth,10);
		var onMenuIdx = resJson.onMenuIdx;
		var len = resJson.data.length;
		var addHTML = "";
		addHTML = "<ul>"

		for(var i=0; i<len; i++){
			addHTML += '<li>';
			addHTML += '<a href="#"';
			addHTML += 'menuIdx="'+resJson.data[i].Menu_idx+'"';
			addHTML += 'parMenuIdx="'+resJson.data[i].Par_Menu_idx+'"'; 
			addHTML += 'menuOrder ="'+resJson.data[i].Menu_order+'"';
			addHTML += 'menuDepth="'+resJson.data[i].Menu_depth+'"';
			addHTML += 'pageURL="'+resJson.data[i].Page_URL+'"';

			if( onMenuIdx == resJson.data[i].Menu_idx){
				addHTML += ' class="on"';
			}
			addHTML += '>'+resJson.data[i].Menu_Name+'</a>';
			addHTML += '<span class="btn_r">';
			addHTML += '<button class="btn_sm_box btnModifyMenu" type="button">수정</button>';
			addHTML += '<button class="btn_sm_box btnDeleteMenu" type="button">삭제</button>';
			addHTML += '</span>';
			addHTML += '</li>';
		}
		addHTML += "</ul>";

		var len = $(".box_ln").length;
		for(var i=addDepth-1; i<len; i++){
			$(".box_ln").eq(i).html('');	//  메뉴 초기화
		}
		$(".box_ln").eq(addDepth-1).html(addHTML);	// 메뉴 입력
		init();
	}
	/* 하위 메뉴 리스트 업 끝*/


	var init = function(){
		menuEvent();	// 트리메뉴 이벤트
		menuIUDControlEvent(); // IUD 이벤트
		menuOrderChangeEvent(); // 순서변경 이벤트
	};

	return {
		init: init
	};

}());

;(function() {
  $(document).ready(function() {
    yUI.init();
  });
}());


</script>
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
