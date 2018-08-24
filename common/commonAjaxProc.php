<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$proc = fnNoInjection($_REQUEST['proc']);	

	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	switch($proc){
		
		case 'detpLev1Ajax':

			$sql= " SELECT ";
			$sql .= "	Dept_Code AS cd, Dept_Name AS cdNm ";
			$sql .= " FROM [theExam].[dbo].[Adm_Dept_Info] ";
			$sql .= " WHERE PDept_Code = 0 ";
			$sql .= " ORDER BY Dept_order ";

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

			$returnData = array("status"=>"success", "data"=>$arrRows);
			echo json_encode($returnData);

			break;
		case 'detpLev2Ajax':

			$sql = " SELECT ";
			$sql .= "	Dept_Code AS cd, Dept_Name AS cdNm ";
			$sql .= " FROM [theExam].[dbo].[Adm_Dept_Info] ";
			$sql .= " WHERE PDept_Code = :detpLev1 ";
			$sql .= " ORDER BY Dept_order ";

			$pArray[':detpLev1'] = $pDetpLev1;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

			$returnData = array("status"=>"success", "data"=>$arrRows);
			echo json_encode($returnData);

			break;
		case 'detpLev3Ajax':

			$sql = " SELECT ";
			$sql .= "	ISNULL( ADI2.Dept_Code,  ADI1.Dept_Code ) AS cd, ISNULL( ADI2.Dept_Name,  ADI1.Dept_Name ) AS cdNm ";
			$sql .= " FROM [theExam].[dbo].[Adm_Dept_Info] AS ADI1 (nolock) ";
			$sql .= " LEFT OUTER JOIN [theExam].[dbo].[Adm_Dept_Info] AS ADI2 (nolock) ON ADI1.Dept_Code = ADI2.PDept_Code ";
			$sql .= " WHERE ADI1.PDept_Code = :detpLev2 ";
			$sql .= " ORDER BY ADI1.Dept_order, ADI2.Dept_order ";

			$pArray[':detpLev2'] = $pDetpLev2;

			$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
			$arrRows = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

			$returnData = array("status"=>"success", "data"=>$arrRows);
			echo json_encode($returnData);

			break;

		default:

		break;
		exit;
	}

?>