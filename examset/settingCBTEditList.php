<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';
	
	// validation 체크를 따로 안할 경우 빈 배열로 선언
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	$autoPop = fnNoInjection($_REQUEST['autoPop']);
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
	$sql .= " (SELECT SB_name FROM theExam.dbo.SB_Info where SB_value = EI.SB_Exam_cate AND SB_kind='exam_cate' AND Disp_type='A' OR Disp_type='B') as examFullName, ";
	$sql .= " (SELECT TOP 1 Exam_code FROM theExam.dbo.Exam_info WHERE Exam_num < EI.Exam_num AND SB_Exam_cate = EI.SB_Exam_cate ORDER BY Exam_num asc) as prevExamCode, ";
	$sql .= " (SELECT TOP 1 Exam_code FROM theExam.dbo.Exam_info WHERE Exam_num > EI.Exam_num AND SB_Exam_cate = EI.SB_Exam_cate ORDER BY Exam_num asc) as nextExamCode ";
	$sql .= " FROM  ";
	$sql .= " theExam.dbo.Exam_info as EI ";
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
	$sql = " SELECT DEC.[center_code] ";
	$sql .= " ,DEC.[SB_center_cate] ";
	$sql .= " ,DEC.[link_center_code] ";
	$sql .= " ,DEC.[center_group_code] ";
	$sql .= " ,DEC.[SB_area] ";
	$sql .= " ,DEC.[center_name] ";
	$sql .= " ,DEC.[zipcode] ";
	$sql .= " ,DEC.[address] ";
	$sql .= " ,DEC.[use_CHK] ";
	$sql .= " ,ECC.[SB_exam_regi_type] ";
	$sql .= " ,ECC.[Exam_code] ";
	$sql .= " ,ECC.[Exam_start_time] ";
	$sql .= " ,ECC.subject";
	$sql .= " ,ECC.certi_PC";
	$sql .= " ,ECC.use_PC";
;	$sql .= " ,(SELECT center_group_name FROM [Def_exam_center_Group] where center_group_code= DEC.center_group_code) as center_group_name";
	$sql .= " FROM ";
	$sql .= " [theExam].[dbo].[Def_exam_center] as DEC ";
	$sql .= "  join ";
	$sql .= " [theExam].[dbo].[exam_center_CBT] as ECC ";
	$sql .= " on DEC.center_code = ECC.center_code ";
	$sql .= " WHERE DEC.SB_center_cate = :centerCate ";
	$sql .= " AND DEC.use_CHK= :useCHK "; 
	$sql .= " AND ECC.exam_code = :examCode".$where;

	$pArray[':centerCate']			= $pCenterCate;
	$pArray[':useCHK']				= 'O';
	$pArray[':examCode']			= $pExamCode;

	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
	$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행


	if(empty($data["Exam_start_time"])){
		$examStartTime = "11:30";
	}else{
		$examStartTime = $data["Exam_start_time"];
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
			</div>

<form name="frmSearch" id="frmSearch" action="<?=$_SERVER['SCRIPT_NAME']?>" method="get"> 
	<input type="hidden" name="examCode" value="<?=$pExamCode?>" />
			<div class="box_sort2">
				<strong class="part_tit">검색</strong>
				<div class="item">
					<select name="searchType" style="width:200px;">  
						<option <?=( $pSearchType == 'SB_area'	)? "SELECTED": "" ?> value="SB_area">지역명</option> 
						<option <?=( $pSearchType == 'center_name'	)? "SELECTED": "" ?> value="center_name">학교명</option> 
						<option <?=( $pSearchType == 'address'	)? "SELECTED": "" ?> value="address">주소</option> 
					</select>
					<input style="width: 300px;" type="text"  id="searchKey" name="searchKey" value="<?=$pSearchKey?>">
					<button class="btn_fill btn_md" type="button" id="btnSearch">조회</button>
					<span class="fl_r">
					<?=fnButtonCreate($cPageRoleRw, "class='btn_fill btn_md btnAddPop'", "고사장추가")?>
					</span>	
				</div>
			</div>
</form>

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
							<col style="width:auto">
							<col style="width:300px">
							<col style="width:auto">
							<col style="width:auto">
							<col style="width:auto">
						</colgroup>
						<thead>
							<tr>
								<th>No</th>
								<th>코드</th>
								<th>지역</th>
								<th>그룹명</th>
								<th>센터명</th>
								<th>과목</th>
								<th>시험시간</th>
								<th>총좌석</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php
						$i=1;
						foreach($arrRows as $data){
						?>
							<tr>
								<td><?=$i?></td>
								<td><?=$data["center_code"]?></td>
								<td><?=$data["SB_area"]?></td>
								<td><?=$data["center_group_name"]?></td>
								<td><?=$data["center_name"]?></td>
								<td>
									<select name="subject_<?=$data["center_code"]?>" style="width: 80px;">  
										<option value="SO" <?=($data["subject"] == "SO") ? "selected" : "";?>>SO</option> 
										<option value="WO" <?=($data["subject"] == "WO") ? "selected" : "";?>>WO</option> 
										<option value="SW" <?=($data["subject"] == "SW") ? "selected" : "";?>>SW</option> 
									</select>
								</td>
								<td>
									<select name="Exam_start_time_<?=$data["center_code"]?>" style="width: 80px;">  

									<?php
										for($i=9; $i<=21; $i++){
											if(strlen($i) == 1) $i = "0".$i;
									?>
										<option value="<?=$i?>:30" <?=($examStartTime == $i.":30") ? "selected" : "";?>><?=$i?>:30</option> 
									<?php
									}
									?>
									</select>
								</td>
								<td><input style="width: 80px;" name="roomSeat_<?=$data["center_code"]?>" type="text" value="<?=$data["room_seat"]?>"></td>
								<td>
									<button class="btn_fill btn_sm btnModify" data-centerCode="<?=$data["center_code"]?>" data-examCode="<?=$pExamCode?>" type="button">수정</button>
									<button class="btn_line btn_sm btnDelete" data-centerCode="<?=$data["center_code"]?>" data-examCode="<?=$pExamCode?>" type="button">삭제</button>
								</td>
							</tr>
						<?php
							$i++;
						}
						?>
							
						</tbody>
					</table>
				</div>
				<div class="wrap_btn">
					<button class="btn_fill btn_lg" type="button" id="btnReady" data-examCode="<?=$pExamCode?>">고사장 준비 완료</button>
					<span class="fx_r"><button class="btn_line btn_md" id="btnInit" type="button" data-examCode="<?=$pExamCode?>">고사장 셋팅 초기화</button></span>
				</div>
			</div>
			<!-- //테이블1-->
		</div>
	</div>
</div>
<!--right //-->

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


	/*리스트 ROW 개별 수정*/
	$(".btnModify").on("click", function(){
		var msg = "수정하시겠습니까";
		if(confirm(msg)){
			var centerCode = $(this).attr("data-centerCode");
			var examCode = $(this).attr("data-examCode");
			var roomCount = $("input[name=roomCount_"+centerCode+"]").val();
			var roomSeat = $("input[name=roomSeat_"+centerCode+"]").val();
			var SBExamRegiType = $("select[name=SBExamRegiType_"+centerCode+"]").val();
			

			var u = "./settingCBTProc.php";				// 비동기 전송 파일 URL
			var param = {	// 파라메터
				"proc" : "getModifyCenterAjax",
				"centerCode" : centerCode,
				"examCode" : examCode,
				"roomCount" : roomCount,
				"roomSeat" : roomSeat,
				"SBExamRegiType" : SBExamRegiType
			};

			console.log(param);


		/* 데이터 비동기 전송*/
		$.ajax({ type:'post', url: u, dataType : 'json',data:param,
			success: function(resJson) {
			console.log(resJson)
				if(resJson.status == "success"){
					alert("수정 되었습니다.");
					return false;
				}
			},
			error: function(resJson) {
				console.log(resJson)
				alert("현재 서버 통신이 원활하지 않습니다.");
			}
		});
		}else{
			return;
		}
	});
	/*리스트 ROW 개별 수정 끝*/

	/*리스트 ROW 개별 삭제*/
	$(".btnDelete").on("click", function(){
		var msg = "삭제하시겠습니까";
		if(confirm(msg)){

			var centerCode = $(this).attr("data-centerCode");
			var examCode = $(this).attr("data-examCode");
			var SBExamRegiType = $("select[name=SBExamRegiType_"+centerCode+"]").val();

			var u = "./settingCBTProc.php";				// 비동기 전송 파일 URL
			var param = {	// 파라메터
				"proc" : "getDeleteCenterAjax",
				"centerCode" : centerCode,
				"examCode" : examCode,
				"SBExamRegiType" : SBExamRegiType
			};

			console.log(param)

			/* 데이터 비동기 전송*/
			$.ajax({ type:'post', url: u, dataType : 'json',data:param,
				success: function(resJson) {
					console.log(resJson)
					if(resJson.status == "success"){
						window.location.reload();
					}
				},
				error: function(resJson) {
					console.log(resJson)
					alert("현재 서버 통신이 원활하지 않습니다.");
				}
			});

		}else{
			return;
		}
		
	});
	/*리스트 ROW 개별 삭제 끝*/


	/* 고사장 최종 준비 완료 */
	$("#btnReady").on("click", function(){
		var examCode = $(this).attr("data-examCode");
		var u = "./settingCBTProc.php";				// 비동기 전송 파일 URL
		var param = {	// 파라메터
			"proc" : "getFinalReadyAjax",
			"examCode" : examCode
		};
		console.log(param)

		/* 데이터 비동기 전송*/
		$.ajax({ type:'post', url: u, dataType : 'json',data:param,
			success: function(resJson) {
				console.log(resJson)
				if(resJson.status == "success"){
					location.href = "./settingCBTList.php";
				}
			},
			error: function(resJson) {
				console.log(resJson)
				alert("현재 서버 통신이 원활하지 않습니다.");
			}
		});
	
	});
	/* 고사장 최종 준비 완료 끝 */

	/* 고사장 세팅 초기화 */
	$("#btnInit").on("click", function(){
		var examCode = $(this).attr("data-examCode");
		var u = "./settingCBTProc.php";				// 비동기 전송 파일 URL
		var param = {	// 파라메터
			"proc" : "getInitAjax",
			"examCode" : examCode
		};
		console.log(param)

		/* 데이터 비동기 전송*/
		$.ajax({ type:'post', url: u, dataType : 'json',data:param,
			success: function(resJson) {
				console.log(resJson)
				if(resJson.status == "success"){
					location.href = "./settingCBTList.php";
				}
			},
			error: function(resJson) {
				console.log(resJson)
				alert("현재 서버 통신이 원활하지 않습니다.");
			}
		});
	});
	/* 고사장 세팅 초기화  끝*/

	$(".btnNavExam").on("click", function(){
		if($(this).attr("data-url") == "non"){
			alert("이전/다음 회차가 없습니다.");
			return false;
		}
		location.href = $(this).attr("data-url");
	});

	$("select[name=examNavInfo]").on("change", function(){
		location.href = "./settingEditList.php?examCode="+$(this).val();
	});


	<?php
	if($autoPop == "Y"){
	?>
	// 회차별고사장세팅 메인 리스트에서 새로입력을 통해 접근시 팝업창을 노출한다.//
	$(".btnAddPop").click();

	// 새로고침시 계속 열리는 팝업창을 위해 브라우저 history의 URL에서 팝업관련 내용을 삭제한다.
	var state = null
	var title = 'YBM TOTAL EXAM';
	var url = 'settingEditList.php?examCode=<?=$pExamCode?>';
	history.pushState(state, title, url);
	<?php
	}
	?>
});


</script>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>

