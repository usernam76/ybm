<?php
	session_start();
	
	if( $_SESSION["admId"] == "" ){
		fnShowAlertMsg("세션이 만료 되어 자동 로그아웃되었습니다", "location.href = '/login.php';", true);
	}
	if( $_SESSION["admPwChk"] != "Y" ){		//비밀번호 변경 필요
		fnShowAlertMsg("", "location.href = '/password.php';", true);
	}
	
	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성

	//현재 페이지 및 상위 메뉴 정보
	$sql = " SELECT ";
	$sql .= "	Menu_idx1, Menu_idx2, Menu_idx3, Menu_idx4, Menu_Name4, AM.Role_RW ";
	$sql .= " FROM v_Menu_Page VMP ";
	$sql .= " LEFT OUTER JOIN Adm_Menu AM ON AM.Menu_idx = VMP.Menu_idx4 AND Adm_id = :loginId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' AND Menu_idx4 = :menuIdx4 ";

	$cArrayPageMenu[':loginId'] = $_SESSION["admId"];
//	$cArrayPageMenu[':loginId'] = "test";
	$cArrayPageMenu[':menuIdx4'] = $cPageMenuIdx;

	$cArrRowsPageMenu = $dbConn->fnSQLPrepare($sql, $cArrayPageMenu, ''); // 쿼리 실행

	$cPageMenuIdx1	= $cArrRowsPageMenu[0][Menu_idx1];
	$cPageMenuIdx2	= $cArrRowsPageMenu[0][Menu_idx2];
	$cPageMenuIdx3	= $cArrRowsPageMenu[0][Menu_idx3];
	$cPageMenuIdx4	= $cArrRowsPageMenu[0][Menu_idx4];
	$cPageMenuName	= $cArrRowsPageMenu[0][Menu_Name4];
	$cPageRoleRw	= $cArrRowsPageMenu[0][Role_RW];
	$cPageRoleRw	= "W";

	if( $cPageRoleRw == "" && $_SERVER['SCRIPT_NAME'] != "/main.php" ){	//권한없음
		fnShowAlertMsg("페이지 조회 권한이 없습니다.", "location.href = '/main.php';", true);
	}
?>