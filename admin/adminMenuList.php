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
<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">메뉴관리</h3>
			<ul class="menu_manage_list">
				<li>
					<div class="box_bs">
						<p class="stit">1Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey" type="button" style="width:100%" id="myBtn">+ 메뉴 추가하기</button></p>
						<div class="box_ln">
							<ul>
							<?php
								foreach($arrRows as $data) {
							?>
								<li><a href="#" menuIdx="<?=$data["Menu_idx"]?>" parMenuIdx="<?=$data["Par_Menu_idx"]?>" menuOrder ="<?=$data["Menu_order"]?>" menuDepth="<?=$data["Menu_depth"]?>"><?=$data["Menu_Name"]?></a></li>
							<?php
							}
							?>
							</ul>
						</div>
						<p class="item">
							<button class="btn_arr" type="button"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button"><strong class="fs_sm">▼</strong></button>
							<button class="btn_line btn_md fl_r" type="button">정렬순서 저장</button>
						</p>
					</div>
				</li>
				<li class="arrow">
					<p>▶</p>
				</li>
				<li>
					<div class="box_bs">
						<p class="stit">2Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey" type="button" style="width:100%">+ 메뉴 추가하기</button></p>
						<div class="box_ln" check="2">
							<ul>
							<?php
							/*?>
								<li><a href="#" class="on">TOEIC</a></li>
								<li><a href="#">TOEIC Speaking</a></li>
								<li><a href="#">TOEFL ITP</a></li>
								<li><a href="#">TOEIC Bridge</a></li>
								<li><a href="#">JET</a></li>
								<li><a href="#">JET-SW</a></li>
								<li><a href="#">JPT</a></li>
								<li><a href="#">JPT-JAPAN</a></li>
								<li><a href="#">일본어검정</a></li>
								<li><a href="#">SJPT</a></li>
								<li><a href="#">TSC</a></li>
								<li><a href="#">상무한검</a></li>
								<li><a href="#">KPE</a></li>
								<?*/?>
							</ul>
						</div>
						<p class="item">
							<button class="btn_arr" type="button"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button"><strong class="fs_sm">▼</strong></button>
							<button class="btn_line btn_md fl_r" type="button">정렬순서 저장</button>
						</p>
					</div>
				</li>
				<li class="arrow">
					<p>▶</p>
				</li>
				<li>
					<div class="box_bs">
						<p class="stit">3Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey" type="button" style="width:100%">+ 메뉴 추가하기</button></p>
						<div class="box_ln"  check="3">
							<ul>
							<?php
							/*?>
								<li><a href="#" class="on">접수현황통계</a></li>
								<li><a href="#">접수관리</a></li>
								<li><a href="#">시험세팅</a></li>
								<?*/?>
							</ul>
						</div>
						<p class="item">
							<button class="btn_arr" type="button"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button"><strong class="fs_sm">▼</strong></button>
							<button class="btn_line btn_md fl_r" type="button">정렬순서 저장</button>
						</p>
					</div>
				</li>
				<li class="arrow">
					<p>▶</p>
				</li>
				<li>
					<div class="box_bs">
						<p class="stit">4Depth 메뉴</p>
						<p><button class="btn_sm_bg_grey" type="button" style="width:100%">+ 메뉴 추가하기</button></p>
						<div class="box_ln">
							<ul>
							<?php
							/*?>
								<li><a href="#" class="on">일별 접수통계</a></li>
								<li><a href="#">월별 접수통계</a></li>
								<li><a href="#">연도별 접수통계</a></li>
								<li><a href="#">지역/고사장 현황</a></li>
								<li><a href="#">단체별 현황</a></li>
								<li><a href="#">접수 예상 인원</a></li>
								<li><a href="#">접수자 추이 통계</a></li>
								<li><a href="#">접수자 성향 통계</a></li>
								<li><a href="#">(목표달성현황)</a></li>
								<li><a href="#">(센터별 운영통계)</a></li>
								<?*/?>
							</ul>
						</div>
						<p class="item">
							<button class="btn_arr" type="button"><strong class="fs_sm">▲</strong></button>
							<button class="btn_arr" type="button"><strong class="fs_sm">▼</strong></button>
							<button class="btn_line btn_md fl_r" type="button">정렬순서 저장</button>
						</p>
					</div>
				</li>
			</ul>
			
			<!-- 버튼 -->
			<div class="wrap_btn">
				<button class="btn_fill btn_lg" type="button">저장</button>
				<button class="btn_line btn_lg" type="button">취소</button>
			</div>
			<!-- 버튼 //-->
		</div>
	</div>
</div>
<!--right //-->
<!-- modal 팝업 :: goal-->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content" style="width: 400px; height: 250px;">
	<span class="close"><img class="sml" src="../resources/images/btn_x.png"></span>
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
							<input style="width: 90%;" type="text">
						</div>
					</td>
				</tr>
				<tr>
					<th>링크</th>
					<td>
						<div class="item">
							<input style="width: 90%;" type="text">
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 버튼 -->
	<div class="wrap_btn">
		<button class="btn_fill btn_sm" type="button">확인</button>
		<button class="btn_line btn_sm" type="button">취소</button>
	</div>
	<!-- 버튼 //-->
  </div>
</div>

<script type="text/javascript">
// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

var yUI = (function() {
	/*
		@auth : 최상운
		@date : 2018-08-20
		@description : 관리자 메뉴 관리 이벤트 스크립트
	*/

	/* 메뉴 클릭 이벤트 > 하위 메뉴 호출 */
	var menuEvent = function(){
		$(".box_ln > ul > li").on("click", function(){
			if($(this).find("a").attr("menuDepth")=="4" ){
				return; // 4depth 메뉴의 경우 하위 이벤트 설정x
			}
			var chkMenu = $(this).find("a").attr("menuIdx");
			$(".box_ln > ul > li").each(function(){
				if( chkMenu == $(this).find("a").attr("menuIdx") ) {
					$(this).find("a").addClass("on");
				}else{
					$(this).find("a").removeClass("on");
				}
			});

			var menuIdx = $(this).find("a").attr("menuIdx");	// menu 고유번호
			var menuDepth =$(this).find("a").attr("menuDepth");	// menu 메뉴단계
			var menuOrder = $(this).find("a").attr("menuOrder");	//	menu 정렬순서
			var parMenuIdx =$(this).find("a").attr("parMenuIdx");	// menu 상위메뉴 고유번호
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

	/* 하위 메뉴 삽입 */
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
		$(".box_ln").eq(addDepth-1).html('');	// 하위 메뉴 초기화
		$(".box_ln").eq(addDepth-1).html(addHTML);	// 메뉴 입력
	}


	var init = function(){
		menuEvent();
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
