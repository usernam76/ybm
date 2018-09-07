<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title>어학시험 admin</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="stylesheet" type="text/css" href="/_resources/css/login.css" media="all">
<link rel="stylesheet" type="text/css" href="/_resources/css/nanumbarungothic.css" media="all">
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="/_resources/js/jquery.js"></script>
<script type="text/javascript" src="/_resources/js/common.js"></script>
<script type="text/javascript" src="/_resources/components/jquery-validation/jquery.validate.min.js"></script>

</head>

<body class="bg_login">
<span class="logo"><img src="/_resources/images/logo_login.png"></span>
<!-- wrap -->
<section id="member_login">
	<!-- 가로 560px  -->
	<section id="wrap_member">
		<div class="notice">
			<p class="tit"><img src="/_resources/images/icon_notice.png" alt=""> &nbsp;공지사항</p>
			<ul>
				<li>· &nbsp; 본페이지에 로그인하셔서 사용하시려면 토큰 드라이버(PKI Client)와 ActiveX의 설치가 필요합니다.</li>
				<li>· &nbsp; ActiveX는 자동으로 설치되며, 설치여부를 묻는 메시지가 나타나면 설치하시면 됩니다.</li>
				<li>· &nbsp; 토큰드라이버(PKI Client) 설치는 페이지 하단의 “파일 다운로드”를 클릭하여 파일을 다운로드 하신 후 <br>
				&nbsp;&nbsp;&nbsp; 압축파일을 해제하여 프로그램을 설치하시면 됩니다.</li>
				<li>· &nbsp; 프로그램 설치 후 PC의 USB포트에 보안 토큰을 장착하여 로그인하시면 됩니다.</li>
				<li>· &nbsp; 타인의 보안토큰으로 로그인 할 수 없습니다.</li>
				<li>· &nbsp; 설치 및 사용법에 관한 문의 사항은 아래 담당자에게 문의해주시기 바랍니다.</li>
			</ul>
		</div>
<form name="frm" id="frm" action="/loginProc.php" method="post"> 
<input type="hidden" name="proc" value="login">
		<section class="member">
			<!-- login_input -->
			<div class="login_input">
				<div class="input_wrap">
					<p class="input_cell focus_input"><input type="text" name="admId" id="admId" value="test" placeholder="아이디" class="focus_data"></p>
					<p class="input_cell focus_input"><input type="password" name="admPw" id="admPw" value="1234" placeholder="비밀번호" class="focus_data"></p>
					<a class="btn_login" id="btnLogin">로그인</a>
				</div>
			</div>
			<!-- //login_input -->
		</section>
</form> 

		<!-- login_inquiry -->
		<div class="login_inquiry">
			<p class="info">· &nbsp; 관리자 아이디 및 비밀번호를 분실하셨을 경우 아래의 담당자에게 문의해주시기 바랍니다.</p>
			<p class="info">&nbsp;&nbsp;&nbsp; <strong>손윤석 / sonsem@ybm.co.kr / 내선301번</strong></p>
			<p class="info">· &nbsp; 파일 다운로드 &nbsp; <a href="https://admin.toeic.co.kr/PKIClient-x32-5.1-SP1.zip" class="btn_sm_line">32bit</a> <a href="https://admin.toeic.co.kr/PKIClient-x64-5.1-SP1.zip" class="btn_sm_line">64bit</a> </p>
		</div>
		<!-- //login_inquiry -->
	<!-- 가로 560px  -->
</section>
<!-- //wrap -->
</body>
</html>

<script type="text/javascript">
$(document).ready(function () {

	$('#frm').validate({
        onfocusout: false,
        rules: {
            admId: {
                required: true    //필수조건
			}, admPw: {
                required: true    //필수조건
			}
        }, messages: {
			admId: {
				required: "아이디를 입력해주세요."
			}, admPw: {
				required: "비밀번호를 입력해주세요."
			}
        }, errorPlacement: function (error, element) {
            // $(element).removeClass('error');
            // do nothing;
        }, invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                alert(validator.errorList[0].message);
                validator.errorList[0].element.focus();
            }
        }
    });

	$("#btnLogin").on("click", function () {
		$("#admId").val( $.trim($("#admId").val()) );

		$('#frm').submit();
    });

});
</script>
