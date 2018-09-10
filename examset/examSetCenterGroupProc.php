<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	$proc = fnNoInjection($_REQUEST['proc']);	

	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	function getCenterList($pCenterGroupCode, $pCenterGroupName){

		$coulmn = "
			center_code,
			center_name,
			case when isnull(center_group_code,'null') = 'null' then 'X' else 'O' end as use_group_CHK, 
			center_group_code,
			use_CHK
		";
		$pArray = null;
		$sql = " SELECT ".$coulmn;
		$sql .= " FROM [theExam].[dbo].[Def_exam_center] ";
		$sql .= " WHERE ";
		$sql .= " SB_center_cate = 'CBT' ";
		$sql .= " ORDER BY case when use_CHK='O' then 1 else 2 end asc";
		$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		$totalCenter = array();				// 전체 CBT 센터
		$notUseCenter = array();			// 미지정 CBT 센터
		$thisGroupCenter = array();	// 선택 그룹의 CBT 센터

		$tc = 0;
		$nuc = 0 ;
		$tgc = 0;
		foreach($arrRows as $data) {
			$totalCenter[$tc] = $data;
			$tc++;
			if($data["use_group_CHK"] == "X"){
				$notUseCenter[$nuc] = $data;
				$nuc++;
			}
			if($data["center_group_code"] == $pCenterGroupCode){
				$thisGroupCenter[$tgc] = $data;
				$tgc++;
			}
		}

		$returnData = array("status"=>"success", "centerGroupCode"=>$pCenterGroupCode, "centerGroupName"=>$pCenterGroupName, "totalCenter"=>$totalCenter, "notUseCenter"=>$notUseCenter, "thisGroupCenter"=>$thisGroupCenter);
		echo json_encode($returnData);
	}


	/*
	@ 최상운 2018.09.03
	> 사용제한이 칼럼이 없음
	*/

	switch($proc){
		case 'write':

			/* 그룹 센터 코드 구하기*/
			$pArray = null;
			$sql = " SELECT max(convert(INT, center_group_code))+1 as centerGroupCode FROM [theExam].[dbo].[Def_exam_center_Group]";
			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
			$pCenterGroupCode = sprintf("%03d",$arrRows[0]["centerGroupCode"]); // 3자리, 앞에 0채움

			/* 입력 프로시저 */
			$pAddress = $pAddress1." ".$pAddress2;
			$pMapUrl = $pMapUrl;	// 칼럼없음.

			$pIUD = "I";
			$pArray = null;
			$sql = 'EXEC p_Def_exam_center_Group_IUD :IUD, :centerGroupCode, :centerGroupName, :SBCenterGroup, :centerMap, :zipCode, :address, :useCHK, :BEP, :memo, :centerGroupType ';

			$pArray[':IUD']								= $pIUD;
			$pArray[':centerGroupCode']	= $pCenterGroupCode;
			$pArray[':centerGroupName']	= $pCenterGroupName;
			$pArray[':SBCenterGroup']			= $pSBCenterGroup;
			$pArray[':centerMap']					= $pCenterMap;
			$pArray[':zipCode']						= $pZipcode;
			$pArray[':address']						= $pAddress;
			$pArray[':useCHK']						= $pUseCHK;
			$pArray[':BEP']								= $pBEP;
			$pArray[':memo']							= $pMemo;
			$pArray[':centerGroupType']		= $pCenterGroupType;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] == 1 ){	//성공일때 메일 발송
				fnShowAlertMsg("등록 되었습니다.", "location.href = '/examset/examSetCenterGroupList.php';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}

		break;
		case 'modify':

			/* 수정 프로시저 */
			$pAddress = $pAddress1." ".$pAddress2;
			$pMapUrl = $pMapUrl;	// 칼럼없음.

			$pIUD = "U";
			$pArray = null;
			$sql = 'EXEC p_Def_exam_center_Group_IUD :IUD, :centerGroupCode, :centerGroupName, :SBCenterGroup, :centerMap, :zipCode, :address, :useCHK, :BEP, :memo, :centerGroupType ';

			$pArray[':IUD']								= $pIUD;
			$pArray[':centerGroupCode']	= $pCenterGroupCode;
			$pArray[':centerGroupName']	= $pCenterGroupName;
			$pArray[':SBCenterGroup']			= $pSBCenterGroup;
			$pArray[':centerMap']					= $pCenterMap;
			$pArray[':zipCode']						= $pZipcode;
			$pArray[':address']						= $pAddress;
			$pArray[':useCHK']						= $pUseCHK;
			$pArray[':BEP']								= $pBEP;
			$pArray[':memo']							= $pMemo;
			$pArray[':centerGroupType']		= $pCenterGroupType;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] != '0' ){	//성공일때 메일 발송
				fnShowAlertMsg("수정 되었습니다.", "location.href = '/examset/examSetCenterGroupList.php';", true);
			}else{
				fnShowAlertMsg("수정 실패 되었습니다.", "history.back();", true);
			}
			
		break;

		case "delete" :
			
			/* 삭제 프로시저 */
			$pAddress = $pAddress1." ".$pAddress2;
			$pMapUrl = $pMapUrl;	// 칼럼없음.

			$pIUD = "D";
			$pArray = null;
			$sql = 'EXEC p_Def_exam_center_Group_IUD :IUD, :centerGroupCode, :centerGroupName, :SBCenterGroup, :centerMap, :zipCode, :address, :useCHK, :BEP, :memo, :centerGroupType ';

			$pArray[':IUD']								= $pIUD;
			$pArray[':centerGroupCode']	= $pCenterGroupCode;
			$pArray[':centerGroupName']	= $pCenterGroupName;
			$pArray[':SBCenterGroup']			= $pSBCenterGroup;
			$pArray[':centerMap']					= $pCenterMap;
			$pArray[':zipCode']						= $pZipcode;
			$pArray[':address']						= $pAddress;
			$pArray[':useCHK']						= $pUseCHK;
			$pArray[':BEP']								= $pBEP;
			$pArray[':memo']							= $pMemo;
			$pArray[':centerGroupType']		= $pCenterGroupType;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] != '0' ){	//성공일때 메일 발송
				fnShowAlertMsg("삭제 되었습니다.", "location.href = '/examset/examSetCenterGroupList.php';", true);
			}else{
				fnShowAlertMsg("삭제 실패 되었습니다.", "history.back();", true);
			}
		break;

		// 센터변경 메뉴 불러오기
		case "getCenterLoadAjax" : 
			getCenterList($pCenterGroupCode, $pCenterGroupName);
			exit;
		break;

		// 센터에 그룹코드 지정
		case "getGroupInCenterAjax" :
			$successChk = "Y";
			foreach($pArrOnCenters as $pCenterCode){
				$pIUD = "U";
				$pArray = null;
				$sql = 'EXEC p_Def_exam_center_Group_U :IUD, :centerCode, :centerGroupCode';
				$pArray[':IUD']							= $pIUD;
				$pArray[':centerCode']				= $pCenterCode;
				$pArray[':centerGroupCode']				= $pCenterGroupCode;
				$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
				$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

				if( $result[0][result] == '0' ){
					$successChk = "N";
				}
			}
			if($successChk == "N"){
				$returnData = array("status"=>"fail", "failcode"=>"81");
				echo json_encode($returnData);
				exit;
			}
			getCenterList($pCenterGroupCode, $pCenterGroupName);
		break;

		// 센터에 그룹코드  제거
		case "getGroupOutCenterAjax" :
			$successChk = "Y";
			foreach($pArrOnCenters as $pCenterCode){
				$pIUD = "U";
				$pArray = null;
				$sql = 'EXEC p_Def_exam_center_Group_U :IUD, :centerCode, :centerGroupCode';
				$pArray[':IUD']							= $pIUD;
				$pArray[':centerCode']				= $pCenterCode;
				$pArray[':centerGroupCode']				= null;
				$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
				$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
				if( $result[0][result] == '0' ){
					$successChk = "N";
				}
			}
			if($successChk == "N"){
				$returnData = array("status"=>"fail", "failcode"=>"81");
				echo json_encode($returnData);
				exit;
			}
			getCenterList($pCenterGroupCode, $pCenterGroupName);
		break;

		default:
		break;
		exit;
	}
?>