<?php
	include "function/fnSecurity.php";
	include "function/fnEnc.php";
	include "function/fnFile.php";
	include "function/fnUtil.php";


// 넘어온 파라미터에 대해 옵션값에 맞게 확인 및 처리
// 2018-01-21 신승국
function optionProc($value, $optValue){
	// 필수값 확인
	if(isset($optValue['notnull']) && $optValue['notnull'] && $value === ''){
		showMsg('필수값이 존재하지 않습니다.', 'back');
	}

	if(isset($optValue['type']) && $optValue['type'] === 'int'){
		$value = injectionProc($value);

		if(!is_numeric($value)){
			showMsg('유효하지 않은 값입니다.', 'back');
		}

		$value = (int)$value;

		if(isset($optValue['min']) && $value < $optValue['min']){
			showMsg('유효하지 않은 값입니다.', 'back');
		}

		// max가 0일때는 체크하지 않음(무한대)
		if(isset($optValue['max']) && $optValue['max'] !== 0 && $value > $optValue['max']){
			showMsg('유효하지 않은 값입니다.', 'back');
		}
	}else{
		if(isset($optValue['min']) && strlen($value) < $optValue['min']){
			showMsg('유효하지 않은 값입니다.', 'back');
		}

		if(isset($optValue['max']) && strlen($value) > $optValue['max']){
			showMsg('유효하지 않은 값입니다.', 'back');
		}
		// 인젝션처리후 문자열 길이가 달라질 수 있기 때문에 인젝션처리를 나중에 함
		$value = injectionProc($value);
	}
	return $value;
}

// 파라미터로 넘어온 값 처리
// 2018-01-21 신승국
function requestProc(){
	// 파라미터로 넘어온 값들에 대한 처리
	foreach($_REQUEST as $key => $value){
		// 변수명 설정
		$keyName = 'p' . ucfirst($key);

		if(is_array($value)){
			// 체크박스처리
			foreach ($value as $key1 => $value1){
				$value[$key1] = $value1;
			}
			$GLOBALS[$keyName] = $value;
		}else{
			$GLOBALS[$keyName] = $value;
		}
	}
}
?>