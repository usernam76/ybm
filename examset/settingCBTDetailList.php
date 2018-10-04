<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	if($pCenterCate == "") $pCenterCate = "CBT";		// 기본은 CBT, 상황에 따라 변수 변경


	/*
	@ 시험 회차 타이틀 및 네비게이션(이전,다음글)
	*/
	$daily = array('일','월','화','수','목','금','토');

	$pArray = null;
	$sql = " SELECT  ";
	$sql .= " Exam_num, ";
	$sql .= " Exam_code, ";
	$sql .= " Exam_day, ";
	$sql .= " (SELECT SB_name FROM SB_Info where SB_value = EI.SB_Exam_cate AND SB_kind='exam_cate' AND Disp_type='A' OR Disp_type='B') as examFullName, ";
	$sql .= " (SELECT TOP 1 Exam_code FROM Exam_info WHERE Exam_num < EI.Exam_num AND SB_Exam_cate = EI.SB_Exam_cate ORDER BY Exam_num asc) as prevExamCode, ";
	$sql .= " (SELECT TOP 1 Exam_code FROM Exam_info WHERE Exam_num > EI.Exam_num AND SB_Exam_cate = EI.SB_Exam_cate ORDER BY Exam_num asc) as nextExamCode ";
	$sql .= " FROM  ";
	$sql .= " Exam_info as EI ";
	$sql .= " where SB_Exam_cate = :examCate ";
	$pArray[':examCate']			= substr($pExamCode,0,3);

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrExamRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

	$arrExamNavInfo = array();
	foreach($arrExamRows as $data){
		$optFullName = "[".$data["examFullName"]."] ".$data["Exam_num"]."회 | ".substr($data["Exam_day"],0,10)." (".$daily[date("w", strtotime(substr($data["Exam_day"],0,10)))].") ";

		$arrExamNavInfo[$data["Exam_code"]] = array("examCode"=>$data["Exam_code"] , "optFullName"=>$optFullName, "nextExamCode"=> $data["nextExamCode"], "prevExamCode"=>$data["prevExamCode"]);
	}
	if($arrExamNavInfo[$pExamCode]["prevExamCode"] != ""){
		$prevURL = $_SERVER["SCRIPT_NAME"]."?examCode=".$arrExamNavInfo[$pExamCode]["prevExamCode"];
	}else{
		$prevURL = "non";
	}
	if($arrExamNavInfo[$pExamCode]["nextExamCode"] != ""){
		$nextURL = $_SERVER["SCRIPT_NAME"]."?examCode=".$arrExamNavInfo[$pExamCode]["nextExamCode"];
	}else{
		$nextURL = "non";
	}

	/*
	@ 해당 시험 세팅 고사장 설정
	*/
	$where		= "";
	if( $pSearchKey != "" ){
		$where .= " AND ". $pSearchType . " LIKE '%". $pSearchKey ."%' ";
	}

	$pArray = null;
	$sql =" Select ";
	$sql .= " SB_area, link_center_code, center_name, subject, Exam_start_time, certi_PC as totalPC, certi_PC-use_PC as requestPC, use_PC, C.use_CHK as use_CHK, B.center_group_code,A.center_code";
	$sql .= " From exam_center_CBT as A (nolock) ";
	$sql .= " join Def_exam_center as B (nolock) on A.center_code = B.center_code ";
	$sql .= " join exam_center as C (nolock) on A.Exam_code = C.Exam_code and A.center_code = C.center_code ";
	$sql .= " Where A.Exam_code = :examCode ";
	$sql .= " ORDER BY B.center_group_code asc";
	$pArray[':examCode']			= $pExamCode;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행


	$arrGroupCenter = array();

	foreach($arrRows as $data){
		$arrGroupCenter[$data["center_group_code"]][] = $data;
	}





	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';


?>



<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 
			<h3 class="title">회차별 센터세팅 <span class="sm_tit">( 상세보기 )</span></h3>
			<!-- sorting area -->
			<div class="box_sort c_txt">
				<div class="item"> 
					<button class="btn_arr btnNavExam" type="button" data-url="<?=$prevURL?>"><strong class="fs_sm">◀</strong></button>
					<select name="examNavInfo" style="width: 300px;">  
			<?php	foreach($arrExamNavInfo as $k=>$v){?>
						<option <?=($arrExamNavInfo[$k]["examCode"] == $pExamCode)? "selected" : ""; ?> value="<?=$arrExamNavInfo[$k]["examCode"]?>"><?=$arrExamNavInfo[$k]["optFullName"]?></option> 
			<?php	}?>
					</select>
					<button class="btn_arr btnNavExam" type="button" data-url="<?=$nextURL?>"><strong class="fs_sm">▶</strong></button>
				</div>
				<span class="fl_r"><?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_md btnAddPop'", "센터 추가")?></span>
				
			</div>
			<div class="box_sort2 l_txt">

<form name="frmSearch" id="frmSearch" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
	<input type="hidden" name="examCode" value="<?=$pExamCode?>" />

				<strong class="part_tit">검색</strong>
				<div class="item line">
					<select name="searchType" style="width:300px;">  
						<option <?=( $pSearchType == 'center_name'	)? "SELECTED": "" ?> value="center_name">센터명</option> 
						<option <?=( $pSearchType == 'link_center_code'	)? "SELECTED": "" ?> value="SB_area">센터코드</option> 
						<option <?=( $pSearchType == 'Exam_start_time'	)? "SELECTED": "" ?> value="center_code">시험시간</option> 
					</select>
					<input style="width: 300px;" type="text"  id="searchKey" name="searchKey" value="<?=$pSearchKey?>">
					<button class="btn_fill btn_md" type="button" id="btnSearch">조회</button>
				</div>
				<strong class="part_tit">필터</strong>
				<div class="item">
			    	<select name="subject" style="width: 300px;"> 
						<option>과목 선택</option> 
						<option>SW</option> 
						<option>Speaking</option> 
						<option>Writing</option> 
					</select>
					<select name="use_CHK" style="width: 300px;">  
						<option>사용 여부</option> 
						<option>사용</option> 
						<option>사용 안함</option> 
					</select>
				</div>
</form>
			</div>
			<!-- sorting area -->
			<!-- 테이블1 -->
			<div class="box_bs">
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:40px">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:180px">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
						</colgroup>
						<thead>
							<tr>
								<th>번호</th>
								<th>지역</th>
								<th>센터코드</th>
								<th>센터명</th>
								<th>시간</th>
								<th>총좌석</th>
								<th>응시좌석</th>
								<th>남은좌석</th>
								<th>사용</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>
						<?php

						$sumTotalPC = 0;
						$sumRequestPC = 0;
						$sumUsePC = 0;
						$article_no = count($arrRows);

						foreach($arrGroupCenter as $k=>$v){
							foreach($v as $p=>$data){
						?>
							<tr>
								<td><?=$article_no?></td>
								<td><?=$data["SB_area"]?></td>
								<td><?=$data["link_center_code"]?></td>
								<td><?=$data["center_name"]?></td>
								<td><?=$data["Exam_start_time"]?></td>
								<td><?=$data["totalPC"]?></td>
								<td><?=$data["requestPC"]?></td>
								<td><?=$data["use_PC"]?></td>
								<td><?=$data["use_CHK"]?></td>
								<td><button class="btn_fill btn_sm btnModify" data-centerCode="<?=$data["center_code"]?>" data-examCode="<?=$pExamCode?>" type="button">수정</button></td>
							</tr>
							<?php
								$article_no--;
								$sumTotalPC		= $sumTotalPC + $data["totalPC"];
								$sumRequestPC	= $sumRequestPC + $data["requestPC"];
								$sumUsePC			= $sumUsePC + $data["use_PC"];
							}
							?>
							<tr>
								<td colspan="5" class="total">그룹 합계</td>
								<td class="total"><?=$sumTotalPC?></td>
								<td class="total"><?=$sumRequestPC?></td>
								<td class="total"><?=$sumUsePC?></td>
								<td class="total">-</td>
								<td class="total">-</td>
							</tr>
						<?php
							$sumTotalPC = 0;
							$sumRequestPC = 0;
							$sumUsePC = 0;
						}
						?>
						<?php
						/*
							<tr>
								<td>서울</td>
								<td><a href="#">강서구</a></td>
								<td>0</td>
								<td>0</td>
								<td>00</td>
								<td><a href="#">000</a></td>
								<td>0%</td>
								<td>00</td>
								<td>00</td>
								<td><button class="btn_fill btn_sm" type="button">수정</button></td>
							</tr>
							<tr>
								<td colspan="5" class="total">서울합계</td>
								<td class="total">00</td>
								<td class="total">0</td>
								<td class="total">0</td>
								<td class="total">0</td>
								<td class="total">0</td>
							</tr>
							<tr>
								<td class="point">서울</td>
								<td class="point"><a href="#">강서구</a></td>
								<td class="point">0</td>
								<td class="point">0</td>
								<td class="point">00</td>
								<td class="point"><a href="#">000</a></td>
								<td class="point">0%</td>
								<td class="point">00</td>
								<td class="point">00</td>
								<td><button class="btn_fill btn_sm" type="button">수정</button></td>
							</tr>
							*/?>
						</tbody>
					</table>
				</div>
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

<?php
/*
<!--right -->
<div id="right_area">
	<div class="wrap_contents">
		<div class="wid_fix"> 

			<!-- sorting area -->
			<!-- 테이블1 -->
			<div class="box_bs">
				<div class="wrap_tbl">
					<table class="type01">
						<caption></caption>
						<colgroup>
							<col style="width:80px">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:300px">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
						</colgroup>
						<thead>
							<tr>
								<th>No</th>
								<th>지역</th>
								<th>고사장코드</th>
								<th>고사장명</th>
								<th>좌석수</th>
								<th>고사실수</th>
								<th>총좌석수</th>
								<th>정기접수인원</th>
								<th>특별접수인원</th>
								<th>응시인원</th>
								<th>남은좌석</th>
								<th>마감비율(%)</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$i=1;
						foreach($arrRows as $data){
						?>
							<tr>
								<td>No</td>
								<td><?=$data["SB_area"]?></td>
								<td><?=$data["center_code"]?></td>
								<td><?=$data["center_name"]?></td>
								<td><?=$data["room_count"]?></td>
								<td><?=$data["room_seat"]?></td>
								<td><?=$data["use_seat"]?></td>
								<td><?=$data["gen_count"]?></td>
								<td><?=$data["spe_count"]?></td>
								<td>응시인원</td>
								<td>남은좌석</td>
								<td>마감비율(%)</td>
								<td><button class="btn_fill btn_sm btnModify" data-centerCode="<?=$data["center_code"]?>" data-examCode="<?=$pExamCode?>" type="button">수정</button></td>
							</tr>
						<?php
							$i++;
						}
						?>
							
						</tbody>
					</table>
				</div>
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

*/
?>
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

	/* 고사장 추가*/
	$(".btnAddPop").on("click", function(){
		var u = "./settingCBTAddPop.php?examCode=<?=$pExamCode?>";
		var name = "settingAddPopup";
		var opt = "width=680,height=700,menubar=no,status=no,toolbar=no";
		var addPop = window.open(u, name, opt)
		addPop.focus();
	});
	/* 고사장 추가 끝*/

	/* 상단 네비 */
		// 시험 콤보박스 좌우(이전/다음) 클릭 시
	$(".btnNavExam").on("click", function(){
		if($(this).attr("data-url") == "non"){
			alert("이전/다음 회차가 없습니다.");
			return false;
		}
		location.href = $(this).attr("data-url");
	});
		// 시험 콤보박스 선택 시,
	$("select[name=examNavInfo]").on("change", function(){
		location.href = "./settingCBTEditList.php?examCode="+$(this).val();
	});
	/* 상단 네비 끝*/


	$(".btnModify").on("click", function(){
		var centerCode = $(this).attr("data-centerCode");
		var examCode = $(this).attr("data-examCode");
		location.href = "./settingCBTView.php?centerCode="+centerCode+"&examCode="+examCode;

	});

});


</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>

