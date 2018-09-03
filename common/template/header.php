<?php
	//현재 페이지 및 상위 메뉴 정보
	$sql = " SELECT ";
	$sql .= "	Menu_idx1, Menu_idx2, Menu_idx3, Menu_idx4, AM.Role_RW ";
	$sql .= " FROM [theExam].[dbo].v_Menu_Page VMP ";
	$sql .= " LEFT OUTER JOIN [theExam].[dbo].[Adm_Menu] AM ON AM.Menu_idx = VMP.Menu_idx4 AND Adm_id = :loginId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' AND Menu_idx4 = :menuIdx4 ";

	$cArrayPageMenu[':loginId'] = "test";
	$cArrayPageMenu[':menuIdx4'] = $cPageMenuIdx;

	$cArrRowsPageMenu = $dbConn->fnSQLPrepare($sql, $cArrayPageMenu, ''); // 쿼리 실행

	$cPageMenuIdx1	= $cArrRowsPageMenu[0][Menu_idx1];
	$cPageMenuIdx2	= $cArrRowsPageMenu[0][Menu_idx2];
	$cPageMenuIdx3	= $cArrRowsPageMenu[0][Menu_idx3];
	$cPageMenuIdx4	= $cArrRowsPageMenu[0][Menu_idx4];
	$cPageRoleRw	= $cArrRowsPageMenu[0][Role_RW];
	$cPageRoleRw	= "W";

	if( $cPageRoleRw == "" ){	//권한없음
		fnShowAlertMsg("페이지 조회 권한이 없습니다.", "location.href = '/index.php';", true);
	}

	$sql = " SELECT ";
	$sql .= "	Menu_idx1, Menu_Name1, Page_url1";
	$sql .= " FROM [theExam].[dbo].v_Menu_Page VMP ";
	$sql .= " INNER JOIN [theExam].[dbo].[Adm_Menu] AM ON AM.[Menu_idx] = VMP.Menu_idx4 AND Adm_id = :loginId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' AND ISNULL(AM.Role_RW, '') != '' ";
	$sql .= " GROUP BY Menu_idx1, Menu_Name1, Page_url1, Menu_order1 ";
	$sql .= " ORDER BY Menu_order1 ";

	$cArrayMenu[':loginId'] = "test";

	$cArrRowsMenu1 = $dbConn->fnSQLPrepare($sql, $cArrayMenu, ''); // 쿼리 실행

	$sql = " SELECT ";
	$sql .= "	Menu_idx2, Menu_Name2, Page_url2";
	$sql .= " FROM [theExam].[dbo].v_Menu_Page VMP ";
	$sql .= " INNER JOIN [theExam].[dbo].[Adm_Menu] AM ON AM.[Menu_idx] = VMP.Menu_idx4 AND Adm_id = :loginId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' AND ISNULL(AM.Role_RW, '') != '' AND Menu_idx1 = :menuIdx1 ";
	$sql .= " GROUP BY Menu_idx2, Menu_Name2, Page_url2, Menu_order2 ";
	$sql .= " ORDER BY Menu_order2 ";

	$cArrayMenu[':menuIdx1'] = $cPageMenuIdx1;

	$cArrRowsMenu2 = $dbConn->fnSQLPrepare($sql, $cArrayMenu, ''); // 쿼리 실행

	$sql = " SELECT ";
	$sql .= "	Menu_idx3, Menu_Name3, Page_url3";
	$sql .= " FROM [theExam].[dbo].v_Menu_Page VMP ";
	$sql .= " INNER JOIN [theExam].[dbo].[Adm_Menu] AM ON AM.[Menu_idx] = VMP.Menu_idx4 AND Adm_id = :loginId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' AND ISNULL(AM.Role_RW, '') != '' AND Menu_idx1 = :menuIdx1 AND Menu_idx2 = :menuIdx2 ";
	$sql .= " GROUP BY Menu_idx3, Menu_Name3, Page_url3, Menu_order3 ";
	$sql .= " ORDER BY Menu_order3 ";
	
	$cArrayMenu[':menuIdx2'] = $cPageMenuIdx2;

	$cArrRowsMenu3 = $dbConn->fnSQLPrepare($sql, $cArrayMenu, ''); // 쿼리 실행

	$sql = " SELECT ";
	$sql .= "	Menu_idx4, Menu_Name4, Page_url4";
	$sql .= " FROM [theExam].[dbo].v_Menu_Page VMP ";
	$sql .= " INNER JOIN [theExam].[dbo].[Adm_Menu] AM ON AM.[Menu_idx] = VMP.Menu_idx4 AND Adm_id = :loginId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' AND ISNULL(AM.Role_RW, '') != ''  AND Menu_idx1 = :menuIdx1 AND Menu_idx2 = :menuIdx2 AND Menu_idx3 = :menuIdx3 ";
	$sql .= " ORDER BY Menu_order4 ";

	$cArrayMenu[':menuIdx3'] = $cPageMenuIdx3;

	$cArrRowsMenu4 = $dbConn->fnSQLPrepare($sql, $cArrayMenu, ''); // 쿼리 실행
?>

<body>
<!-- header -->
<div class="wrap_top_bg">
	<div class="wrap_top">
		<h1 class="logo">
			<img src="/_resources/images/logo.png">
		</h1>
		<ul class="nav">
<?php
	foreach($cArrRowsMenu1 as $data) {
		if( $cPageMenuIdx1 == $data['Menu_idx1'] ){
			echo "<li><a href='".$data['Page_url1']."' class='on'>".$data['Menu_Name1']."</a></li>";
		}else{
			echo "<li><a href='".$data['Page_url1']."'>".$data['Menu_Name1']."</a></li>";
		}
	}
?>
		</ul>
		<div class="info dropdown">
		  <button class="dropbtn">홍길동 님 &nbsp;&nbsp; <span class="fs_sm">▼</span> </button>
		  <div class="dropdown-content">
			<a href="#">내용</a>
			<a href="#">내용 2</a>
			<a href="#">내용 3</a>
		  </div>
		</div>
	</div>
</div>
<!-- header //-->