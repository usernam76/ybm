<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];

	$resultArray = fnGetRequestParam($valueValid);
	
	$totalRecords	= 0;		// 총 레코드 수
	$recordsPerPage	= 10;		// 한 페이지에 보일 레코드 수
	$pagePerBlock	= 10;		// 한번에 보일 페이지 블럭 수
	$currentPage	= 1;		// 현재 페이지
	$totalPage		= 1;
	if( $pCurrentPage > 0 ){
		$currentPage = $pCurrentPage;
	}

	$where		= "";
	if( $pSearchKey != "" ){
		$where = " AND ". $pSearchType . " LIKE '%". $pSearchKey ."%' ";
	}

	$sql = " SELECT  ";
	$sql .= " left(SB_name,CHARINDEX('#', SB_name, 1) -1 ) as areaLev1, ";
	$sql .= " count(SB_name) as countArea, ";
	$sql .= " sum(centerCount) as countCenter ";
	$sql .= " FROM  ";
	$sql .= " (SELECT  ";
	$sql .= " SB_name, ";
	$sql .= " (SELECT count(*) FROM [theExam].[dbo].[Def_exam_center] where SB_area=SBI.SB_value) as centerCount ";
	$sql .= " FROM ";
	$sql .= " [theExam].[dbo].[SB_Info] AS SBI ";
	$sql .= " where SB_kind='area') AS A ";
	$sql .= " group by left(SB_name,CHARINDEX('#', SB_name, 1) -1 ) ";

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';
?>

<style>
.countArea{
	cursor:pointer;
	_cursor:hand;
}
.countArea .on{
	background-color:#eee;
}
</style>


<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">지역/고사장관리</h3>
			<!-- 테이블2 -->
			<div class="box_bs">
				<div class="colm2">
						<div class="l_cont">
							<p class="s_title">&nbsp;</p>
							<div class="wrap_tbl">
								<table class="type01">
									<caption></caption>
									<colgroup>
										<col style="width: auto;">
										<col style="width: auto;">
										<col style="width: auto;">
									</colgroup>
									<thead>
										<tr>
											<th>시도</th>
											<th>시군구수</th>
											<th>총고사장 및 센터수</th>
										</tr>
									</thead>
									<tbody>

<?php
foreach($arrRows as $data) {
?>

										<tr>
											<td><?=$data["areaLev1"]?></td>
											<td><a class="countArea"><?=$data["countArea"]?></a></td>
											<td><?=$data["countCenter"]?></td>
										</tr>
<?php
	$totalCountArea += $data["countArea"];
	$totalCountCenter += $data["countCenter"];
}
?>
										<tr>
											<td class="total">합계</td>
											<td class="total"><?=$totalCountArea?></td>
											<td class="total"><?=$totalCountCenter?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<span class="arr"></span>
						<div class="r_cont">
							<p class="s_title">
							<span id="areaTitle"></span>
							<span class="fl_r">
								<button class="btn_fill btn_md" type="button" id="btnLoadWrite">지역추가</button>
							</span>
							</p>
							<div class="wrap_tbl">
								<table class="type01" id="areaLev2Tbl">
									<caption></caption>
									<colgroup>
										<col style="width: auto;">
										<col style="width: auto;">
									</colgroup>
									<thead>
										<tr>
											<th>번호</th>
											<th>시군구</th>
											<th>총고사장 및 센터수</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
			</div>
			<!-- //테이블2-->
		</div>
	</div>
</div>
<!--right //-->
<!-- modal 팝업 :: goal-->
<div id="myModal" class="modal">
<form name="frmWrite" method="post">
  <!-- Modal content -->
  <div class="modal-content" style="height:250px;">
   	<h2 class="p_title">지역추가</h2>
    <span class="close"><img src="../_resources/images/btn_x.png"></span>
    <!-- 팝업내용 -->
	<div class="wrap_tbl">
		<div class="wrap_tbl">
			<table class="type02">
				<caption></caption>
				<colgroup>
					<col style="width: 120px;">
					<col style="width: auto;">
				</colgroup>
				<tbody>
					<tr>
						<th>시도</th>
						<td>
							<div class="item">
								<select name="areaLev1" style="width: 200px;" id="areaLev1"></select>
							</div>
						</td>
					</tr>
					<tr>
						<th>시군구</th>
						<td>
							<div class="item">
								<input style="width: 300px;" name="areaLev2" type="text">
								<button class="btn_fill btn_md" type="button" id="btnWrite">추가</button>	
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
   <!-- 팝업내용 :: goal //-->
  </div>
</form>
</div>

<script type="text/javascript">
// Get the modal
var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

$(function(){
	
	/* 지역 등록 추가*/
	$("#btnWrite").on("click", function(){

		if(!confirm("지역을 추가하시겠습니까?")){
			return false;
		}

		var areaLev1 = $("form[name=frmWrite]").find("select[name=areaLev1]").val();
		var areaLev2 = $.trim($("form[name=frmWrite]").find(" input[name=areaLev2]").val());

		if(areaLev1 == ""){
			alert("시도를 선택해 주세요");
			return false;
		}
		if(areaLev2 == ""){
			alert("시군구를 입력해 주세요");
			return false;
		}

		var u = "./examSetAreaProc.php";				// 비동기 전송 파일 URL
		var param = {	// 파라메터
			"proc" : "write",
			"areaLev1" : areaLev1,
			"areaLev2" : areaLev2
		};

		/* 데이터 비동기 전송*/
		$.ajax({ type:'post', url: u, dataType : 'json',data:param,
			success: function(resJson) {
				if(resJson.status == "success"){
//					console.log(resJson)
					areaLoad(resJson);
					return false;
				}
				else if(resJson.status == "fail"){
//					console.log(resJson)
					if(resJson.failcode=="90"){
						alert("이미 등록된 지역 입니다.");
						return false;
					}
				}
			},
			error: function(resJson) {
//				console.log(resJson)
				alert("현재 서버 통신이 원활하지 않습니다.");
			}
		});

	});

	/* 지역 추가 팝업 노출*/
	$("#btnLoadWrite").on("click", function(){
		var modal = document.getElementById('myModal');
		modal.style.display = "block";


		var areaLev1 = $("form[name=frmWrite]").find("select[name=areaLev1]").val('');
		var areaLev2 = $.trim($("form[name=frmWrite]").find(" input[name=areaLev2]").val(''));
	});



	/*시도 클릭시 시군구 리스트업 이벤트*/
	$(".countArea").on("click", function(){
		var areaLev1 = $(this).parent().prev().text();
		var u = "./examSetAreaProc.php";				// 비동기 전송 파일 URL
		var param = {	// 파라메터
			"proc" : "getAreaLoadAjax",
			"areaLev1" : areaLev1
		};
		/* 데이터 비동기 전송*/
		$.ajax({ type:'post', url: u, dataType : 'json',data:param,
			success: function(resJson) {
				if(resJson.status == "success"){
					//console.log(resJson)
					areaLoad(resJson)
					return false;
				}
			},
			error: function(resJson) {
				alert("현재 서버 통신이 원활하지 않습니다.");
			}
		});
	});


	/* 시도클릭 > 시군구 리스트업 */
	var areaLoad = function(resJson){
		console.log(resJson);
		$('#areaLev2Tbl > tbody > tr').remove();
		$("#areaTitle").html(resJson.areaLev1);
		var len = resJson.data.length;
		var addHTML = "";
		var totalCenterCount = 0;


		for(var i=0; i<len; i++){
			addHTML += '<tr>';
			addHTML += '<td>'+(i+1)+'</td>';
			addHTML += '<td><a href="./examSetDef.php?areaLev1='+resJson.data[i].areaLev1+'&areaLev2='+resJson.data[i].SB_value+'" class="countAreaLev2">'+resJson.data[i].SB_value+'</a></td>';
			addHTML += '<td>'+resJson.data[i].centerCount+'</td>';
			addHTML += '</tr>';
		totalCenterCount += parseInt(resJson.data[i].centerCount, 10);
		}
		addHTML += '<tr>';
		addHTML += '<td class="total" colspan="2">합계</td>';
		addHTML += '<td class="total">'+totalCenterCount+'</td>';
		addHTML += '</tr>';
		$('#areaLev2Tbl > tbody:first').append(addHTML);
	}
	/* 시도클릭 > 시군구 리스트업 끝*/

	var param = {
		"areaLev1" 			: "areaLev1"	// 1detp 부서정보
		, "optYn"			: "Y"			// 상단 옵션 사용여부(Y, N)
		, "firstOptVal"		: ""			// 상단 옵션  value
		, "firstOptLable"	: "선택"			// 상단 옵션  text
	}
	common.sys.setAreaComboCreate(param);

});


</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>
