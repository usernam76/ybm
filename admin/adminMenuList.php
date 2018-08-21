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
	
	$sql = 'SELECT';
	$sql .= '[Menu_idx],[Menu_Name],[Menu_order],[Menu_depth]';
	$sql .= 'FROM [theExam].[dbo].[Menu_Info]';
	$sql .= 'WHERE Menu_depth = 1';
	$sql .= 'ORDER BY Menu_order asc';
	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
	
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>

<style>
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
						<p><button class="btn_sm_bg_grey menu_add" data-depth="1" type="button" style="width:100%" id="myBtn">+ 메뉴 추가하기</button></p>
						<div class="box_ln">
							<ul>
							<?php
								foreach($arrRows as $data) {
							?>
								<li>
									<a href="#" menuIdx="<?=$data["Menu_idx"]?>" parMenuIdx="<?=$data["Par_Menu_idx"]?>" menuOrder ="<?=$data["Menu_order"]?>" menuDepth="<?=$data["Menu_depth"]?>"><?=$data["Menu_Name"]?></a>
									<span class="btn_r">
										<button class="btn_sm_box" type="button">수정</button> 
										<button class="btn_sm_box" type="button">삭제</button>
									</span>
								</li>
							<?php
							}
							?>
							</ul>
						</div>
						<p class="item fl_r">
							<button class="btn_arr" type="button"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button"><strong class="fs_sm">▼</strong></button>
						</p>
					</div>
				</li>
				<li class="arrow">
					<p>▶</p>
				</li>
				<li>
					<div class="box_bs">
						<p class="stit">2Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey menu_add" data-depth="2" type="button" style="width:100%">+ 메뉴 추가하기</button></p>
						<div class="box_ln">
						</div>
						<p class="item fl_r">
							<button class="btn_arr" type="button"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button"><strong class="fs_sm">▼</strong></button>
						</p>
					</div>
				</li>
				<li class="arrow">
					<p>▶</p>
				</li>
				<li>
					<div class="box_bs">
						<p class="stit">3Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey menu_add" data-depth="3" type="button" style="width:100%">+ 메뉴 추가하기</button></p>
						<div class="box_ln" >
						</div>
						<p class="item fl_r">
							<button class="btn_arr" type="button"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button"><strong class="fs_sm">▼</strong></button>
						</p>
					</div>
				</li>
				<li class="arrow">
					<p>▶</p>
				</li>
				<li>
					<div class="box_bs">
						<p class="stit">4Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey menu_add" data-depth="4" type="button" style="width:100%">+ 메뉴 추가하기</button></p>
						<div class="box_ln">
						</div>
						<p class="item fl_r">
							<button class="btn_arr" type="button"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button"><strong class="fs_sm">▼</strong></button>
						</p>
					</div>
				</li>
			</ul>
			
		</div>
	</div>
</div>
<!--right //-->
<!-- modal 팝업 :: goal-->
<div id="myModal" class="menu_add_form modal">
	<!-- Modal content -->
	<div class="modal-content" style="width: 400px; height: 250px;">
<form name="addMenuForm" method="post">
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
								<input style="width: 90%;" type="text"  name="menu_url">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<!-- 버튼 -->
		<div class="wrap_btn">
			<button class="btn_fill btn_sm btn_ok" type="button">확인</button>
			<button class="btn_line btn_sm btn_cancel" type="button">취소</button>
		</div>
</form>
	<!-- 버튼 //-->
	</div>
</div>

<script type="text/javascript">
// Get the modal
/*
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");
*/
// Get the <span> element that closes the modal
/*
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
/*btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}
*/

var yUI = (function() {
	/*
		@auth : 최상운
		@date : 2018-08-20
		@description : 관리자 메뉴 관리 이벤트 스크립트
	*/

	/* modal 제어 event*/
	var modalMenuControlEvent = function(){

		var currentDepth = 1;
		var currentParMenuIdx = 0;

		// 메뉴 등록
		$(".btn_ok").on("click", function(){

		
			var menuName = $("form[name=addMenuForm]").find("[name=menu_name]").val();
			var menuUrl = $("form[name=addMenuForm]").find("[name=menu_url]").val();
			var menuDepth = currentDepth;
			var parMenuIdx = currentParMenuIdx;

			var u = "./adminMenuProc.php";	// 비동기 전송 파일 URL
			var param = {	// 파라메터
				"proc" : "insert",
				"menuName"		:	menuName,
				"menuUrl"	:	menuUrl,
				"menuDepth"	:	menuDepth,
				"parMenuIdx"	:	parMenuIdx
			};

			console.log(param)

		});

		/* modal open event*/
		$(".menu_add").on("click", function(){
			currentDepth = $(this).attr("data-depth"); // 추가 버튼 클릭한 메뉴 depth
			currentDepth = parseInt(currentDepth, 10);
			if(currentDepth != 1){	// 1dep 메뉴는 체크X
				// 상위 메뉴 선택 체크
				var parentMenuObj = $(".box_ln").eq(currentDepth-2).find("ul").find(".on");	// 상위 메뉴객체
				if(parentMenuObj.length < 1){
					alert("상위메뉴를 선택하셔야 합니다.");
					return false;
				}
				currentParMenuIdx = parentMenuObj.attr("menuIdx");
			}else{
				var parentMenuObj ;
			}
			$(".menu_add_form").css("display", "block");
		});

		/*modal close event*/
		$(".close").on("click", function(){
			$(".modal").css("display", "none");
		});

	}




	/* 메뉴 클릭 이벤트 > 하위 메뉴 호출 */
	var menuEvent = function(){
		$(".box_ln > ul > li > a").on("click", function(){
			var currentMenuDepth =parseInt( $(this).attr("menuDepth") , 10);
			if(currentMenuDepth==4 ){
				return; // 4depth 메뉴의 경우 하위 이벤트 설정x
			}

			var chkMenu = $(this).attr("menuIdx");

			$(this).parent().parent().find("a").removeClass("on");
			$(this).addClass("on");

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
				success: function(e) {
					if(e.status == "success"){
						menuLoad(e)
					}else{
						console.log("[Error]");
						console.log(e)
					}
				},
				error: function(e) {
					console.log("[Error]");
					console.log(e)
				}
			});
		});
	};

	/* 하위 메뉴 리스트 업 */
	var menuLoad = function(e){
		var addDepth = parseInt(e.depth,10);
		var len = e.data.length;
		var addHTML = "";
		addHTML = "<ul>"
		for(var i=0; i<len; i++){
			addHTML += '<li>';
			addHTML += '<a href="#"';
			addHTML += 'menuIdx="'+e.data[i].Menu_idx+'"';
			addHTML += 'parMenuIdx="'+e.data[i].Par_Menu_idx+'"'; 
			addHTML += 'menuOrder ="'+e.data[i].Menu_order+'"';
			addHTML += 'menuDepth="'+e.data[i].Menu_depth+'"';
			addHTML += '>'+e.data[i].Menu_Name+'</a>';
			addHTML += '</li>';
		}
		addHTML += "</ul>";
		var len = $(".box_ln").length;
		for(var i=addDepth-1; i<len; i++){
			$(".box_ln").eq(i).html('');	// 하위 메뉴 초기화
		}
		$(".box_ln").eq(addDepth-1).html(addHTML);	// 메뉴 입력


		menuEvent();	// menu 이벤트 추가
	}


	var init = function(){
		menuEvent();	// 메뉴명 이벤트
		modalMenuControlEvent();
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
</fieldset>
</form> 
<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
