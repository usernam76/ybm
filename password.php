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
		<h2>비밀번호 변경</h2>
		<p class="login_txt">· &nbsp; 안전한 서비스 이용을 위해 주기적으로 비밀번호를 변경해주시기 바랍니다.</p>
<form name="frm" id="frm" action="/loginProc.php" method="post"> 
<input type="hidden" name="proc" value="password">
		<section class="member">
			<!-- login_input -->
			<div class="login_input">
				<div class="input_wrap">
					<p class="input_cell focus_input"><input type="password" name="admPw" id="admPw" placeholder="현재 비밀번호" class="focus_data"></p>
					<p class="input_cell focus_input"><input type="password" name="admPwNew" id="admPwNew" placeholder="새 비밀번호" class="focus_data"></p>
					<p class="input_cell focus_input"><input type="password" name="admPwNewC" id="admPwNewC" placeholder="새 비밀번호 확인" class="focus_data"></p>
				</div>
				<p class="info2">· &nbsp; 비밀번호는 영문,숫자,특수기호를 혼합하여 8자~15자로 만들어 주시기<br> &nbsp;&nbsp;&nbsp;&nbsp; 바랍니다.<br>
				 &nbsp;&nbsp;&nbsp;&nbsp; 단, 다음의 특수기호는 보안상의 문제로 사용하실 수 없습니다.<br>
				  &nbsp;&nbsp;&nbsp;&nbsp; <strong class="pointColor">‘ ; -- < ( ) \ /</strong>
				</p>
				<p><a class="btn_login" id="btnPwChg">변경하기</a></p>
			</div>
			<!-- //login_input -->
		</section>
</form> 
		<!-- login_inquiry -->
		
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
            admPw: {
                required: true    //필수조건
			}, admPwNew: {
                required: true    //필수조건
			}, admPwNewC: {
                required: true    //필수조건
			}
        }, messages: {
			admPw: {
				required: "현재 비밀번호를 입력해주세요."
			}, admPwNew: {
				required: "새 비밀번호를 입력해주세요."
			}, admPwNewC: {
				required: "새 비밀번호 확인를 입력해주세요."
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

	$("#btnPwChg").on("click", function () {

		$('#frm').submit();
    });

});
</script>