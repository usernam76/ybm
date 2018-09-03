<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/classes/DBConnMgr.class.php';
	
	$dbConn = new DBConnMgr(DB_DRIVER, DB_USER, DB_PASSWD); // DB커넥션 객체 생성

	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/head.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/header.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/left.php';

	echo '<p style="padding-left:300px;padding-top:200px">';
	echo '- 모든 변수/함수 명칭은 Camel 표기법으로 작성<br>';
	echo '- 함수명은 동사(get/set/IUD - InsertUpdateDelete) 와 같이 동사로 시작 ex) getPdsList / getPdsListCount / IUDPds etc...<br>';
	echo '- 파일명은 비지니스로직 + 동작 + 실행.php 로 한다. ex) login.php / examList.php / examWrite.php / examDetail.php<br>';
	echo '	(동작 : 입력 write / 수정 modify / 삭제 delete / 리스트 list)<br>';
	echo '<br>';
	echo '- /_common/function.php 파일은 /_common/function 폴더 안의 분류별 함수파일들을 인클루드하여 모아놓은 파일이다. <br>';
	echo '- 보안 등 모든 프로젝트에서 공용으로 쓰일 공통 함수 같은 경우는 /_common/function 폴더에 fn함수종류.php 에 파일을 추가 한 뒤 /_common 폴더의 function 에 include한다.<br>';
	echo '- 본 사이트에서만 공용으로 쓰일 공통 함수 같은 경우는 /common 폴더에 위치한다. (login, logout, 권한관리 등)<br>';
	echo '<br>';
	echo '- head, header, left, footer 등의 include 하여 공용으로 사용할 돔 php 파일은 /common/templates 에 보관<br>';
	echo '- JAVASCRIPT 관련하여 오픈소스 및 콤포넌트는 /_resources/components/ 폴더 아래에 폴더를 만들어서 관리<br>';
	echo '- 직접 개발하는 js 파일은 /_resources/js 폴더에서 관리<br>';
	echo '- 직접 코딩한 css 파일은 /_resources/css 폴더에서 관리<br>'; 
	echo '- 공용으로 쓰일 컴퍼넌트나 js, css 파일은 /common/template/head.php 파일에 include<br>';
	echo '- 특정 페이지에만 사용되는 컴퍼넌트나 js, css파일은 각 파일의 상단에 include<br>';
	echo '<br>';
	echo '<br>';
	echo '- DB접속 관련 쿼리 샘플은 /sample/list.php 확인<br>';
	echo '- 파라미터를 \'\' 로 감쌀 때는 fnSecurity.php 의 fnCDBValue() 함수를 사용, 만약 빈값일 경우 NULL 문자열의 입력이 필요할 경우 fnCDBValue2() 함수 사용 (해당 파일 소스 및 주석 확인 요망)<br>';
	echo '<br>';
	echo '<br>';
	echo ' ※ 전체적으로 /_common 폴더 안의 파일들을 열어 소스 및 주석을 숙지해 주시고 개발 부탁 드립니다. 질문사항은 기업부설연구소 서윤형(024) 로 주시면 됩니다.<br>';
	echo ' ※ 현재 디자인 부분은 미완성 상태의 디자인 파일을 적용한 것이므로, 완성된 디자인 파일을 샘플과 동일한 방식으로 적용하여 개발해주시길 바랍니다.';
	echo '<br>';
	echo ' ※ 주소검색 모듈은 자체적인 서비스가 있으니 /postcode.php 확인 바랍니다.';
	echo '</p>';
	require_once $_SERVER["DOCUMENT_ROOT"].'/common/template/footer.php';
?>