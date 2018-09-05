<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	function getMenuList($pMenuDepth, $pParMenuIdx, $onMenuIdx=""){

		$sql = ' SELECT ';
		$sql .= ' A.[Menu_idx],A.[Menu_Name],A.[Menu_order],A.[Menu_depth],A.[Par_Menu_idx],B.[Page_URL] ';
		$sql .= ' FROM ';
		$sql .= ' [theExam].[dbo].[Menu_Info] as A join ';
		$sql .= ' [theExam].[dbo].[Menu_Page] as B ';
		$sql .= ' on A.Menu_idx = B.Menu_idx and A.use_CHk = \'O\' ';
		$sql .= ' WHERE ';
		$sql .= ' A.Menu_depth = \''.$pMenuDepth.'\' AND ';
		$sql .= ' A.use_CHK = \'O\' AND';
		$sql .= ' A.Par_Menu_idx = \''.$pParMenuIdx.'\'';
		$sql .= ' ORDER BY A.Menu_order asc ';

		$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		$returnData = array("status"=>"success","depth"=>$pMenuDepth,"onMenuIdx"=>$onMenuIdx, "data"=>$arrRows);
		echo json_encode($returnData);
	}


	$proc = fnNoInjection($_REQUEST['proc']);	

	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	switch($proc){
		case 'write':


			$sql =" SELECT max(Menu_order) as pMenuOrder FROM Menu_info where Menu_depth ='$pMenuDepth'";
			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
			$pMenuOrder = $arrRows[0]["pMenuOrder"] + 1;
			$pIUD = "I";

			if($pMenuDepth == 3){
				$pMenuPageFirst = "X";
			}else{
				$pMenuPageFirst = "O";
			}
			$pUseCHK = "O";

			/*입력 프로시저*/
			$sql = 'EXEC [theExam].[dbo].[p_Menu_info_IUD] :IUD, :Menu_idx,:Menu_Name,  :Menu_depth, :Menu_order, :Par_Menu_idx, :Page_URL, :use_CHK, :Menu_Page_First';	
			$pArray[':IUD']		= 'I';
			$pArray[':Menu_idx']		= null;
			$pArray[':Menu_Name']		= $pMenuName;
			$pArray[':Menu_depth']		= $pMenuDepth;
			$pArray[':Menu_order']		= $pMenuOrder;
			$pArray[':Par_Menu_idx']		= $pParMenuIdx;
			$pArray[':Page_URL']		= $pPageURL;
			$pArray[':use_CHK']		= $pUseCHK;
			$pArray[':Menu_Page_First']		= $pMenuPageFirst;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
			$pArray = null;

			getMenuList($pMenuDepth, $pParMenuIdx); // 새로 입력된 메뉴 부르기

			exit;
		break;
		case 'modify':

			/*수정 프로시저*/
			$sql = 'EXEC [theExam].[dbo].[p_Menu_info_IUD] :IUD, :Menu_idx,:Menu_Name,  :Menu_depth, :Menu_order, :Par_Menu_idx, :Page_URL, :use_CHK, :Menu_Page_First';	
			$pArray[':IUD']		= 'U';
			$pArray[':Menu_idx']		= $pMenuIdx;
			$pArray[':Menu_Name']		= $pMenuName;
			$pArray[':Menu_depth']		= $pMenuDepth;
			$pArray[':Menu_order']		= $pMenuOrder;
			$pArray[':Par_Menu_idx']		= $pParMenuIdx;
			$pArray[':Page_URL']		= $pPageURL;
			$pArray[':use_CHK']		= $pUseCHK;
			$pArray[':Menu_Page_First']		= $pMenuPageFirst;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			getMenuList($pMenuDepth, $pParMenuIdx); // 수정된 메뉴 부르기

			exit;
		break;


		case 'delete':


			/* 하위 메뉴 존재 여부 검색 > 4dep은 바로 삭제 진입*/
			if($pMenuDepth != "4"){
				$sql = ' SELECT count(Menu_idx) as cnt  FROM [theExam].[dbo].[Menu_Info] ';
				$sql .= ' WHERE Par_Menu_idx = \''.$pMenuIdx.'\'';
				$sql .= ' and use_CHK = \'O\'';
				$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
				$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
				$cnt = $arrRows[0]["cnt"];
				if($cnt > 0){
					$returnData = array("status"=>"fail", "failcode"=>"90");
					echo json_encode($returnData);
					exit;
				}
			}

			/*삭제 프로시저*/
			$sql = 'EXEC [theExam].[dbo].[p_Menu_info_IUD] :IUD, :Menu_idx,:Menu_Name,  :Menu_depth, :Menu_order, :Par_Menu_idx, :Page_URL, :use_CHK, :Menu_Page_First';
			$pArray[':IUD']		= 'D';
			$pArray[':Menu_idx']		= $pMenuIdx;
			$pArray[':Menu_Name']		= $pMenuName;
			$pArray[':Menu_depth']		= $pMenuDepth;
			$pArray[':Menu_order']		= $pMenuOrder;
			$pArray[':Par_Menu_idx']		= $pParMenuIdx;
			$pArray[':Page_URL']		= $pPageURL;
			$pArray[':use_CHK']		= $pUseCHK;
			$pArray[':Menu_Page_First']		= $pMenuPageFirst;
			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			getMenuList($pMenuDepth, $pParMenuIdx); // 삭제된 메뉴 부르기
		break;


		
		case 'getMenuOrderChange': // 순서변경

			/*메뉴 순서 변경*/

			/*순서 변경 프로시저*/
			$sql = 'EXEC [theExam].[dbo].[p_Menu_info_U] :targetMenuIdx, :targetMenuOrder,:changeMenuIdx,  :changeMenuOrder';	
			$pArray[':targetMenuIdx']		= $pTargetMenuIdx;
			$pArray[':targetMenuOrder']		= $pTargetMenuOrder;
			$pArray[':changeMenuIdx']		= $pChangeMenuIdx;
			$pArray[':changeMenuOrder']		= $pChangeMenuOrder;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			getMenuList($pChangeDepth, $pChangeParMenuIdx, $pChangeMenuIdx);	// 메뉴 부르기
			exit;

		break;


		case 'getMenuLoadAjax':

			$findMenuDepth = $pMenuDepth + 1;
			$findParMenuIdx = $pMenuIdx;

			getMenuList($findMenuDepth, $findParMenuIdx);	// 메뉴 부르기
			exit;

		break;
		default:
		break;
		exit;
	}
?>