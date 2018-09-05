<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "192";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	function getLoadArea($pAreaLev1){
		$sql = " SELECT ";
		$sql .= " left(SB_name,CHARINDEX('#', SB_name, 1) -1 ) as areaLev1, ";
		$sql .= " SB_value, ";
		$sql .= " centerCount ";
		$sql .= " FROM  ";
		$sql .= " (SELECT ";
		$sql .= " SB_name, ";
		$sql .= " SB_value, ";
		$sql .= " SB_order, ";
		$sql .= " (SELECT count(*) FROM [theExam].[dbo].[Def_exam_center] where SB_area=SBI.SB_value) as centerCount ";
		$sql .= " FROM ";
		$sql .= " [theExam].[dbo].[SB_Info] AS SBI ";
		$sql .= " where  ";
		$sql .= " SB_kind='area' and ";
		$sql .= " left(SB_name,CHARINDEX('#', SB_name, 1) -1 ) = :areaLev1 ";
		$sql .= " ) AS A ";
		$sql .= " ORDER BY SB_order asc";

		$pArray[':areaLev1'] = $pAreaLev1;

		$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
		$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

		$returnData = array("status"=>"success","areaLev1"=>$pAreaLev1, "data"=>$arrRows);
		echo json_encode($returnData);
	}


	$proc = fnNoInjection($_REQUEST['proc']);	

	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	switch($proc){
		case 'write':
			$pSBValue = $pAreaLev2;
			$pSBName = $pAreaLev1."#".$pAreaLev2;

			$sql = " SELECT count(SB_kind) as cnt FROM [theExam].[dbo].[SB_Info] where SB_kind = 'area' and SB_name =  :SB_name and SB_value= :SB_value ";
			$pArray[':SB_name']		= $pSBName;
			$pArray[':SB_value']		= $pSBValue;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
			$cnt = $arrRows[0]["cnt"] ;

			if($cnt > 0){
				$returnData = array("status"=>"fail","failcode"=>"90");
				echo json_encode($returnData);
				exit;
			}


			$pArray = null;
			$sql =" SELECT max(SB_order) as pSBOrder FROM [theExam].[dbo].[SB_Info] where SB_kind = 'area' and left(SB_name, CHARINDEX('#',SB_name,1)-1 ) = :areaLev1 ";
			$pArray[':areaLev1'] = $pAreaLev1;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행
			$pSBOrder = $arrRows[0]["pSBOrder"] + 1;


			$pIUD = "I";

			$pArray = null;
			/*입력 프로시저*/
			$sql = 'EXEC [theExam].[dbo].[p_Area_I] :IUD, :SB_kind,:SB_value,  :SB_name, :SB_order';	
			$pArray[':IUD']		= 'I';
			$pArray[':SB_kind']		= "area";
			$pArray[':SB_value']		= $pSBValue;
			$pArray[':SB_name']		= $pSBName;
			$pArray[':SB_order']		= $pSBOrder;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
			$pArray = null;

			getLoadArea($pAreaLev1);

			exit;

			exit;
		break;


		case 'getAreaLoadAjax':
				getLoadArea($pAreaLev1);
			exit;

		break;
		default:
		break;
		exit;
	}
?>