<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	$centerCate = fnNoInjection($_REQUEST['centerCate']);
	if(empty($centerCate)) $centerCate = "CBT";


	$where		= "";
	if( $pSearchKey != "" ){
		$where .= " AND ". $pSearchType . " LIKE '%". $pSearchKey ."%' ";
	}


	$pArray = null;
	$sql = " Select  ";
	$sql .= " SB_area, A.center_code,center_group_name, center_name, PC_count, certi_PC, use_PC ";
	$sql .= " From ";
	$sql .= " Def_exam_center as A (nolock) ";
	$sql .= "  join Def_center_CBT as B (nolock) ";
	$sql .= " on A.center_code = B.center_code ";
	$sql .= " join Def_exam_center_group as D (nolock)  ";
	$sql .= " on A.center_group_code = D.center_group_code ";
	$sql .= " left outer join exam_center as C (nolock) ";
	$sql .= " on A.center_code = C.center_code and C.Exam_code = :examCode ";
	$sql .= " Where SB_center_cate = 'CBT' and C.Exam_code is null ".$where;

	$pArray['examCode'] = $pExamCode;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';

?>
<body>
   <div class="modal-content">
		<h2 class="p_title">센터 추가</h2>
		<span class="close"><img src="../_resources/images/btn_x.png"></span>
		<!-- 팝업내용 -->
		<div class="wrap_tbl">
<form name="frmSearch" id="frmSearch" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
			<div class="box_inform c_txt">
				<div class="item pad_b30">
					<select name="searchType" style="width:100px;">  
						<option <?=( $pSearchType == 'center_name')? "SELECTED": "" ?> value="center_name">센터명</option> 
						<option <?=( $pSearchType == 'center_group_name')? "SELECTED": "" ?> value="center_group_name">그룹명</option> 
					</select>
					<input style="width: 300px;" type="text"  id="searchKey" name="searchKey" value="<?=$pSearchKey?>">
					<button class="btn_fill btn_md" type="button" id="btnSearch">검색</button>	
				</div><br>
			</div>
</form>
			<table class="type01">
				<caption></caption>
				<colgroup>
					<col style="width:8%;">
					<col style="width:auto;">
					<col style="width:auto;">
					<col style="width:20%;">
					<col style="width:20%;">
					<col style="width:auto;">
					<col style="width:auto;">
					<col style="width:auto;">
				</colgroup>
				<thead>
					<tr>
						<th><div class="item"><input class="i_unit" id="subj1" type="checkbox" name="allChoise" ></div></th>
						<th>지역</th>
						<th>센터코드</th>
						<th>그룹명</th>
						<th>센터명</th>
						<th>보유PC</th>
						<th>인증PC</th>
						<th>접수가능PC</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if(count($arrRows)==0){
				?>
					<tr>
						<td colspan="8">검색 결과가 없습니다.</td>
					</tr>
				<?php
				}else{
					foreach($arrRows as $data){
				?>
					<tr>
						<td><div class="item"><input class="i_unit" id="subj1" type="checkbox" name="choise" value="<?=$data["center_code"]?>"></div></td>
						<td><?=$data["SB_area"]?></td>
						<td><?=$data["center_code"]?></td>
						<td><?=$data["center_group_name"]?></td>
						<td><?=$data["center_name"]?></td>
						<td><?=$data["PC_count"]?></td>
						<td><?=$data["certi_PC"]?></td>
						<td><?=$data["use_PC"]?></td>
					</tr>
				<?php
					}
				}
				?>
				</tbody>
			</table>
		</div>
   		<div class="wrap_btn">
   			<button class="btn_fill btn_md" type="button" id="btnWrite">추가</button>
   			<button class="btn_line btn_md" type="button" id="btnCancel">취소</button>
   		</div>
	   <!-- 팝업내용 :: statis_hour //-->
	  </div>

<script>
$(document).ready(function () {
	/* 검색 유효성 체크 */
	$('#frmSearch').validate({
        onfocusout: false,
        rules: {
            searchKey: {
                required: true    //필수조건
			}
        }, messages: {
			searchKey: {
				required: "검색어를 입력해주세요."
			}
        }, errorPlacement: function (error, element) {
            // $(element).removeClass('error');
            // do nothing;
        }, invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                alert(validator.errorList[0].message);
                validator.errorList[0].element.focus();
            }
        }
    });

	// 검색
	$("#btnSearch").on("click", function(){
		$("#searchKey").val( $.trim($("#searchKey").val()) );
		$('#frmSearch').submit();
    });

	// 전체선택
	$("input[name=allChoise]").on("click", function(){
		var checked = $(this).is(":checked");
		$("input[name=choise").prop('checked', checked) ;
	});

	/* 팝업 닫기 */
		// 상단 X
	$(".close").on("click", function(){
		window.open("about:blank","_self").close();
	});
		// 취소버튼
	$("#btnCancel").on("click", function(){
		window.open("about:blank","_self").close();
	});
	/* 팝업 닫기 끝*/

	/*선택 고사장 추가*/
	$("#btnWrite").on("click", function(){

		var arrChoiseCenterCode = new Array();	// 선택 고사장
		$("input[name=choise]").each(function(){
			if($(this).is(":checked")){
				arrChoiseCenterCode.push($(this).val());
			}
		});

		var u = "./settingCBTProc.php";				// 비동기 전송 파일 URL
		var param = {	// 파라메터
			"proc" : "addCenterAjax",
			"centerCodes" : arrChoiseCenterCode,
			"examCode" : '<?=$pExamCode?>'
		};

		/* 데이터 비동기 전송*/
		$.ajax({ type:'post', url: u, dataType : 'json',data:param,
			success: function(resJson) {
				if(resJson.status == "success"){
					window.location.reload();
					opener.location.reload();
					return false;
				}
			},
			error: function(resJson) {
				console.log(resJson)
				alert("현재 서버 통신이 원활하지 않습니다.");
			}
		});

		


	});
	/*선택 고사장 추가 끝*/

 });
</script>

  </body>
</html>