<?php
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/_common/function.php';

	$proc = fnNoInjection($_REQUEST['proc']);

	switch($proc){
		case 'write':

			break;
		case 'modify':

			break;
		case 'delete':

			break;
		case 'getSampleListAjax':
			// javascript 에서 url : /sample/proc.php?proc=getSmapleListAjax 와 같이 호출 및 return 받아 사용
			echo json_encode(/*json형태로 리턴할 결과값*/);
			break;
		default:

			break;
			exit;
	}

?>