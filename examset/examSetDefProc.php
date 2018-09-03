<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$proc = fnNoInjection($_REQUEST['proc']);	
	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	switch($proc){
		case 'write':
			/* PBT 입력 */
			if($pCenterCate == "PBT"){

				$pIUD = "I";
				$pArray = null;
				/*입력 프로시저*/

				$pSBArea = $pAreaLev2;

				foreach($_POST as $k=>$v){
					echo $k."--->".$v."<br />";
				}

/*
				$sql = 'EXEC [theExam].[dbo].[p_Area_I] :IUD, :SB_kind,:SB_value,  :SB_name, :SB_order';	
				$pArray[':IUD']		= 'I';
				$pArray[':SB_kind']		= "area";
				$pArray[':SB_value']		= $pSBValue;
				$pArray[':SB_name']		= $pSBName;
				$pArray[':SB_order']		= $pSBOrder;

				$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성
				$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
				$pArray = null;
				*/


			/* CBT 입력 */
			}else{
			
			}

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