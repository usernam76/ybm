<?php
	/*
		@auth : 서윤형
		@date : 2017-08-16
		@desc : AES256 암호화(fnAES256Encode() / 복호화(fnAES256Decode())
		@param : 
			- $str : 암호화 또는 복호화 할 문자열
		@return : 암호화 또는 복호화 된 문자열
	*/
	function fnAES256Encode($str){
		$key = file_get_contents("/usr/local/aeskey/Key.dat");
		$IV = file_get_contents("/usr/local/aeskey/IV.dat");
		$result = base64_encode(openssl_encrypt($str, "aes-256-cbc", $key, true, $IV));
		$key = null;
		$IV = null;

		return $result;
	}

	function fnAES256Decode($str){
		$key = file_get_contents("/usr/local/aeskey/Key.dat");
		$IV = file_get_contents("/usr/local/aeskey/IV.dat");
		$result = openssl_decrypt(base64_decode($str), "aes-256-cbc", $key, true, $IV);
		$key = null;
		$IV = null;

		return $result;
	}


	function fnAESEncodeYBM($str){
		return base64_encode(openssl_encrypt($str, "aes-256-cbc", ENC_KEY, true, str_repeat(chr(0), 16)));
    }
	function fnAESDecodeYBM($str){
		return openssl_decrypt(base64_decode($str), "aes-256-cbc", ENC_KEY, true, str_repeat(chr(0), 16));
	}
	

	/*
		@auth : 서윤형
		@date : 2017-08-16
		@desc : SHA512 암호화
		@param : 
			- $str : 암호화 할 문자열
		@return : SHA512 암호화 된 문자열
	*/
	function fnSHA512($str){
		$strSHA512 = hash("sha512", $str);
		return $strSHA512;
	}


?>
