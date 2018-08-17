<?php
	/* 
		@auth : 서윤형
		@date : 2017-08-16
		@desc : 파일 업로드 체크 (보안검사, 확장자, 파일크기, MimeType 검사)
		@param : 
			- $objFile : 파일 오브젝트 $_FILES[INPUT NAME]
			- $strFileKind : 파일 검증 종류 - img, excel, 그 외(허용가능 파일)
			- $intFileSizeLimit : 파일 크기 (kb 단위)

		@return : 
			 - 검증 : "true" 문자열
			 - 검증실패 : 검증실패 메세지
	*/
	function fnFileUploadCheck($objFile, $strFileKind, $intFileSizeLimit){

		$strFileName = $objFile["name"];										// 파일 명
		$strFileExt = strtolower(pathinfo($strFileName, PATHINFO_EXTENSION));	// 파일 확장자
		$strFileMime = strtolower(mime_content_type($objFile["tmp_name"]));		// 파일 MIME 타입
		$intFileSize = $objFile["size"] / 1024 ;								// 파일 크기 (KB)

		$arrayNotAllowExt = array("php", "php3", "php4", "htm", "html", "asp", "aspx", "cer", "cdx", "asa", "jsp", "war"); // 서버 공격 위험 확장자
		$arrayNotAllowStr = array(";", "%00", "%zz"); // 서버 우회 공격 위험 문자 (가상의 확장자를 파일명에 넣고  apache허점을 이용한 서버 언어 실행 공격)
		
		$arrayImgExt = array("jpg", "gif", "jpeg", "bmp", "png"); // 이미지 허용 확장자
		$arrayExcelExt = array("xls", "xlsx"); // 엑셀 허용 확장자
		// $arrayAllowExt = array("jpg", "gif", "jpg", "bmp", "png", "xls", "xlsx", "pdf", "doc", "docx", "zip", "ppt", "pptx", "mp3", "csv", "swf"); // 그 외 허용 확장자

		$arrayImgMime = array("image/jpg", "image/pjpeg", "image/jpeg", "image/gif", "image/bmp", "image/png"); // 이미지 허용 MimeType
		$arrayExcelMime = array("application/vnd.ms-excel"); // 엑셀 허용 MimeType
		// $arrayAllowMime = array("image/jpg", "image/pjpeg", "image/jpeg", "image/gif", "image/bmp", "image/png", "application/vnd.ms-excel", "application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/x-zip-compressed", "application/vnd.ms-powerpoint", "audio/mpeg", "application/x-shockwave-flash", "text/plain", "application/octet-stream", "video/x-ms-wmv"); // 그 외 허용 MimeType

		/* ------------- 서버 공격 위험 확장자 필터링 --------------- */
		if(in_array($strFileExt, $arrayNotAllowExt) > 0){
			$checkResult = "보안에 위배되는 파일형식 입니다.";
			return $checkResult;
			exit;
		}else{
			$checkResult = "true";
		}
		/*------------------------------------------------------------*/

		
		/*-------- 서버 우회 공격이 가능한 단어일 경우 필터링 --------*/
		if(in_array($strFileName, $arrayNotAllowStr) > 0){
			$checkResult = "보안에 위배되는 파일형식 입니다.";
			return $checkResult;
			exit;
		}else{
			$checkResult = "true";
		}
		/*------------------------------------------------------------*/


		/*--------------------- 파일 확장자 체크 ---------------------*/
		if($strFileKind=="img"){
			if(in_array($strFileExt, $arrayImgExt)===true){
				$checkResult = "true";
			}else{
				$checkResult = "업로드가 허용되지 않는 파일 형식입니다.[EXT]";
				return $checkResult;
				exit;
			}
		}elseif($strFileKind=="excel"){
			if(in_array($strFileExt, $arrayExcelExt)===true){
				$checkResult = "true";
			}else{
				$checkResult = "업로드가 허용되지 않는 파일 형식입니다.[EXT]";
				return $checkResult;
				exit;
			}
		}/*else{
			if(in_array($strFileExt, $arrayAllowExt)===true){
				$checkResult = "true";
			}else{
				$checkResult = "업로드가 허용되지 않는 파일 형식입니다.[EXT]";
			}
		}*/
		/*------------------------------------------------------------*/

		
		/*----------------------- 파일 크기 검사 ----------------------*/
		if($intFileSize > $intFileSizeLimit){
			$checkResult = "업로드 파일이 허용 사이즈 보다 큽니다.[SIZE]";
			return $checkResult;
			exit;
		}
		/*------------------------------------------------------------*/


		/*-------------------- 파일 MimeType 검사 ---------------------*/
		if($checkResult=="true"){
			if($strFileKind=="img"){
				if(in_array($strFileMime, $arrayImgMime)){
					$checkResult = "true";
				}else{
					$checkResult = "첨부파일은 jpg, jpeg, gif, bmp, png 파일만 업로드 가능합니다.[MIME]";
					return $checkResult;
					exit;
				}
			}elseif($strFileKind=="excel"){
				if(in_array($strFileMime, $arrayExcelMime)){
					$checkResult = "true";
				}else{
					$checkResult = "첨부파일은 xls, xlsx 파일만 업로드 가능합니다.[MIME]";
					return $checkResult;
					exit;
				}
			}/*else{
				if(in_array($strFileMime, $arrayAllowMime)){
					$checkResult = "true";
				}else{
					$checkResult = "업로드가 허용되지 않는 파일 형식입니다.[MIME]";
				}
			}*/
		}
		/*------------------------------------------------------------*/
		return $checkResult;
	}

	/* 
		@auth : 서윤형
		@date : 2018-01-11
		@desc : 파일 업로드
		@param : 
			- $fileObj : 파일 오브젝트 $_FILES[INPUT NAME]
			- $fileType : 파일 검증 종류 - img, excel, 그 외(허용가능 파일)
			- $uploadDir : 파일 업로드 할 경로
			- $sizeLimit : 파일 크기 (kb 단위)
			- $fileVO : 파일 정보를 리턴할 VO 
				(/common/classes/fileModel.class.php 참조)
				(파일 업로드 후 파일정보를 return 할 필요 없다면 fileVO 부분을 삭제 후 성공 파라미터 return 해주면 됨)

		@return : 
			- FileModel 객체 리턴 (fileName, fileOriginName, fileMime, fileSize, uploadDir 정보 담아서)
	*/
	function fnFileUpload($fileObj, $fileType, $uploadDir, $sizeLimit, $fileVO){
		//$uploadDir = '/usr/local/phproot/samplePHP/resources/uploads/'; // 업로드 경로
	
		//$fileObj = $_FILES['fileInput']; // 파일 오브젝트
		$fileName = $fileObj['name']; // 파일명
		$fileExt = pathinfo($fileName, PATHINFO_EXTENSION);  // 확장자
		$fileTmpName = $fileObj['tmp_name']; // 임시폴더 업로드 파일명
		$fileSize = $fileObj['size']; // 파일 사이즈
		$fileOriginName = date('YmdHis').mt_rand().'.'.$fileExt;
		$fileMime = strtolower(mime_content_type($fileObj["tmp_name"]));		// 파일 MIME 타입

		$uploadFile = $uploadDir . $fileOriginName; 

		if(($fileObj['error'] > 0) || ($fileSize <= 0)) { // 파일 사이즈가 0이하거나 PHP 파일 관련 에러가 발생할 경우 FileUploadException 으로 throw
			// ※ $fileObj['error'] - 파일업로드 중 생기는 에러를 출력한다.
			fnShowAlertMsg('FileUploadException : 파일 업로드에 실패하였습니다.', '', true); 
		} else { 
			if(!is_uploaded_file($fileTmpName)) { // HTTP post로 전송된 것인지 체크한다. 
				fnShowAlertMsg('HTTP로 전송된 파일이 아닙니다.', '' ,true); 
			}else { 
				// fnFileUploadCheck(fileObject, fileKind, fileSize(KB));
				$fileCheckResult = fnFileUploadCheck($fileObj, $fileType, $sizeLimit); // 파일 체크 결과 성공 : 'true', 실패 : '실패 관련 메세지'
				if($fileCheckResult=='true'){
					/* 
						move_uploaded_file()
						 - PHP는 파일을 임시폴더에 저장하고 있다가 move_uploaded_file() 을 실행할 때 파일을 검증 후 지정한 업로드 폴더로 이동시킨다.
						 - 이동 후 일정 시간 후 삭제한다.
						 1. 임시 저장되어 있는 파일을 ./uploads 디렉토리로 이동한다.
						 2. 또한 PHP 엔진에서 파일 검증을 해준다.
					*/
					if(move_uploaded_file($fileTmpName, $uploadFile)) { 
						$fileVO->setFileName($fileName);
						$fileVO->setFileSize($fileSize);
						$fileVO->setFilePath($uploadDir);
						$fileVO->setFileOriginName($fileOriginName);
						$fileVO->setFileType($fileMime);
						return $fileVO;
					}else{
						fnShowAlertMsg('파일 업로드 실패입니다.', '' ,true); 
					}
				}else{
					fnShowAlertMsg($fileCheckResult, 'history.back(-1)', true);
				}			
			} 
		}		
	}
?>