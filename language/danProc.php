<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "209";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	$proc = fnNoInjection($_REQUEST['proc']);	

	$valueValid = [];
	$resultArray = fnGetRequestParam($valueValid);

	switch($proc){
		case 'write':

			$sql = "EXEC p_Goods_IUD 'I', :goodsCode, :goodsName, :dispGoodsName, :sbGoodsType, :dispPrice, :sellPrice, 'X', :useChk, :okId, :okType, :updateId, :updateType, 0, '', '', '' ";	
			$pArray[':goodsCode']		= "";
			$pArray[':goodsName']		= $pGoodsName;
			$pArray[':dispGoodsName']	= $pDispGoodsName;
			$pArray[':sbGoodsType']		= $pSbGoodsType;
			$pArray[':dispPrice']		= $pDispPrice;
			$pArray[':sellPrice']		= $pSellPrice;
			$pArray[':useChk']			= $pUseChk;
			$pArray[':okId']			= $_SESSION["admId"];
			$pArray[':okType']			= $_SESSION["admType"];
			$pArray[':updateId']		= $_SESSION["admId"];
			$pArray[':updateType']		= $_SESSION["admType"];

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
			
			if( $result[0][result] == 1 ){			
				fnShowAlertMsg("등록 되었습니다.", "location.href = '/language/danList.php';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}

			break;
		case 'modify':

			$sql = "EXEC p_Goods_IUD 'U', :goodsCode, :goodsName, :dispGoodsName, :sbGoodsType, :dispPrice, :sellPrice, 'X', :useChk, :okId, :okType, :updateId, :updateType, 0, '', '', '' ";	
			$pArray[':goodsCode']		= $pGoodsCode;
			$pArray[':goodsName']		= $pGoodsName;
			$pArray[':dispGoodsName']	= $pDispGoodsName;
			$pArray[':sbGoodsType']		= $pSbGoodsType;
			$pArray[':dispPrice']		= $pDispPrice;
			$pArray[':sellPrice']		= $pSellPrice;
			$pArray[':useChk']			= $pUseChk;
			$pArray[':okId']			= $_SESSION["admId"];
			$pArray[':okType']			= $_SESSION["admType"];
			$pArray[':updateId']		= $_SESSION["admId"];
			$pArray[':updateType']		= $_SESSION["admType"];

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] == 1 ){
				fnShowAlertMsg("수정 되었습니다.", "location.href = '/language/danList.php';", true);
			}else{
				fnShowAlertMsg("수정 실패 되었습니다.".json_encode($result), "history.back();", true);
			}

			break;
		case 'delete':

			break;		


		default:

			break;
			exit;
	}

?>