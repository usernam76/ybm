<?php
	$sql = " SELECT ";
	$sql .= "	Menu_idx1, Menu_Name1, Page_url1";
	$sql .= " FROM v_Menu_Page VMP (nolock) ";
	$sql .= " INNER JOIN Adm_Menu AM (nolock) ON AM.[Menu_idx] = VMP.Menu_idx4 AND Adm_id = :loginId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' AND ISNULL(AM.Role_RW, '') != '' ";
	$sql .= " GROUP BY Menu_idx1, Menu_Name1, Page_url1, Menu_order1 ";
	$sql .= " ORDER BY Menu_order1 ";

//	$cArrayMenu[':loginId'] = "test";
	$cArrayMenu[':loginId'] = $_SESSION["admId"];

	$cArrRowsMenu1 = $dbConn->fnSQLPrepare($sql, $cArrayMenu, ''); // 쿼리 실행

	$sql = " SELECT ";
	$sql .= "	Menu_idx2, Menu_Name2, Page_url2";
	$sql .= " FROM v_Menu_Page VMP (nolock) ";
	$sql .= " INNER JOIN Adm_Menu AM (nolock) ON AM.[Menu_idx] = VMP.Menu_idx4 AND Adm_id = :loginId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' AND ISNULL(AM.Role_RW, '') != '' AND Menu_idx1 = :menuIdx1 ";
	$sql .= " GROUP BY Menu_idx2, Menu_Name2, Page_url2, Menu_order2 ";
	$sql .= " ORDER BY Menu_order2 ";

	$cArrayMenu[':menuIdx1'] = $cPageMenuIdx1;

	$cArrRowsMenu2 = $dbConn->fnSQLPrepare($sql, $cArrayMenu, ''); // 쿼리 실행

	$sql = " SELECT ";
	$sql .= "	Menu_idx3, Menu_Name3, Page_url3, Menu_idx4, Menu_Name4, Page_url4";
	$sql .= " FROM v_Menu_Page VMP (nolock) ";
	$sql .= " INNER JOIN Adm_Menu AM (nolock) ON AM.[Menu_idx] = VMP.Menu_idx4 AND Adm_id = :loginId ";
	$sql .= " WHERE ISNULL(Menu_idx4, '') != '' AND ISNULL(AM.Role_RW, '') != '' AND Menu_idx1 = :menuIdx1 AND Menu_idx2 = :menuIdx2 ";
	$sql .= " ORDER BY Menu_order3, Menu_order4 ";
	
	$cArrayMenu[':menuIdx2'] = $cPageMenuIdx2;

	$cArrRowsMenu3 = $dbConn->fnSQLPrepare($sql, $cArrayMenu, ''); // 쿼리 실행	
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
		  <button class="dropbtn"><?=$_SESSION["admNm"]?> 님 &nbsp;&nbsp; <span class="fs_sm">▼</span> </button>
		  <div class="dropdown-content">
			<a href="/password.php">비밀번호변경</a>
			<a href="/logout.php">로그아웃</a>
		  </div>
		</div>
	</div>
</div>
<!-- header //-->