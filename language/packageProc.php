<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';

	$cPageMenuIdx = "210";	//메뉴고유번호
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/headerRole.php';

	$proc = fnNoInjection($_REQUEST['proc']);	

	switch($proc){
		case 'write':

			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$sql = "EXEC p_Goods_IUD 'I', :goodsCode, :goodsName, :dispGoodsName, :sbGoodsType1, :sbGoodsType2, :dispPrice, :sellPrice, 'O', :useChk, :okId, :okType, :updateId, :updateType, 0, :goods_codes, :goods_prices, 0 ";	
			$pArray[':goodsCode']		= "";
			$pArray[':goodsName']		= $pGoodsName;
			$pArray[':dispGoodsName']	= $pDispGoodsName;
			$pArray[':sbGoodsType1']	= $pSbGoodsType1;
			$pArray[':sbGoodsType2']	= $pSbGoodsType2;
			$pArray[':dispPrice']		= $pDispPrice;
			$pArray[':sellPrice']		= $pSellPrice;
			$pArray[':useChk']			= $pUseChk;
			$pArray[':okId']			= $_SESSION["admId"];
			$pArray[':okType']			= $_SESSION["admType"];
			$pArray[':updateId']		= $_SESSION["admId"];
			$pArray[':updateType']		= $_SESSION["admType"];
			$pArray[':goods_codes']		= implode("#", $pGoodsCodes);
			$pArray[':goods_prices']	= implode("#", $pGoodsPrices);

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행
			
			if( $result[0][result] == 1 ){			
				fnShowAlertMsg("등록 되었습니다.", "location.href = '/language/packageList.php';", true);
			}else{
				fnShowAlertMsg("등록 실패 되었습니다.", "history.back();", true);
			}

			break;
		case 'modify':

			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$sql = "EXEC p_Goods_IUD 'U', :goodsCode, :goodsName, :dispGoodsName, :sbGoodsType1, :sbGoodsType2, :dispPrice, :sellPrice, 'O', :useChk, :okId, :okType, :updateId, :updateType, 0, :goods_codes, :goods_prices, 0 ";	
			$pArray[':goodsCode']		= $pGoodsCode;
			$pArray[':goodsName']		= $pGoodsName;
			$pArray[':dispGoodsName']	= $pDispGoodsName;
			$pArray[':sbGoodsType1']	= $pSbGoodsType1;
			$pArray[':sbGoodsType2']	= $pSbGoodsType2;
			$pArray[':dispPrice']		= $pDispPrice;
			$pArray[':sellPrice']		= $pSellPrice;
			$pArray[':useChk']			= $pUseChk;
			$pArray[':okId']			= $_SESSION["admId"];
			$pArray[':okType']			= $_SESSION["admType"];
			$pArray[':updateId']		= $_SESSION["admId"];
			$pArray[':updateType']		= $_SESSION["admType"];
 			$pArray[':goods_codes']		= implode("#", $pGoodsCodes);
			$pArray[':goods_prices']	= implode("#", $pGoodsPrices);

			$result = $dbConn->fnSQLPrepare($sql, $pArray, 'IUD'); // 쿼리 실행

			if( $result[0][result] == 1 ){
				fnShowAlertMsg("수정 되었습니다.", "location.href = '/language/packageList.php';", true);
			}else{
				fnShowAlertMsg("수정 실패 되었습니다.".json_encode($result), "history.back();", true);
			}

			break;
		case 'delete':

			break;		

		case 'danSearch':
			$valueValid = [];
			$resultArray = fnGetRequestParam($valueValid);

			$where = "";
			if( $pSearchKey != "" ){
				if ( $pSearchType == "" ){
					$where = " AND ( goods_name LIKE '%". $pSearchKey ."%' OR disp_goods_name LIKE '%". $pSearchKey ."%' OR goods_code LIKE '%". $pSearchKey ."%' ) ";
				}else{
					$where = " AND ". $pSearchType . " LIKE '%". $pSearchKey ."%' ";
				}
			}

			$sql  = " SELECT ";
			$sql .= "	GI.goods_code, GI.goods_name, GI.disp_goods_name, GI.disp_price, GI.sell_price, GI.use_CHK, SI.SB_name AS sbGoodsTypeNm2 ";
			$sql .= " FROM Goods_info AS GI (nolock) 	";
			$sql .= " JOIN SB_Info AS SI (nolock) ON SI.SB_kind = 'goods_type2' AND SI.SB_value = GI.SB_goods_type2	";
			$sql .= " WHERE pack_CHK = 'X' ". $where;
			$sql .= " ORDER BY update_day DESC ";

			$result = $dbConn->fnSQLPrepare($sql, $pArray, ''); // 쿼리 실행

			$returnData = array("status"=>"success", "result"=>$result);

			echo json_encode($returnData);

			break;

		default:

			break;
			exit;
	}

?>