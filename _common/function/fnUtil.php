<?php
	/*
		@auth : 서윤형
		@date : 2017-08-18
		@desc : 
			- 페이징 태그 생성
			- fnGetHref() 함수를 이용하여 페이징 관련 파라미터를 제외한 주소값을 가져와서 링크 생성
			- fnPaginator()는 fnGetHref()와 함께 존재해야 함.
		@param : 
			- $totalRecords : 총 레코드 수
			- $recordsPerPage : 한 페이지에 보일 레코드 수
			- $pagePerBlock : 한번에 보일 페이지 블럭 수 ex) 10 ->  1 2 3 4 5 6 7 8 9 10
			- $currentPage : 현재 페이지
		@return : 페이징 태그 생성
	*/
	function fnPaginator($totalRecords, $recordsPerPage, $pagePerBlock, $currentPage){
		$href = fnGetHref(); // 기존 URI + currentPage 를 제외한 파라미터를 문자열로 생성
		$requestURI = $href.((strpos($href,"?")  <= 0) ? "?" : "&"); // $href에 ?가 있으면 끝에 &를 붙이고 없으면 ?를 붙인다.
		
		// 페이징 계산   1000 / 10
		$totalPages = ceil($totalRecords/$recordsPerPage); // 총 페이지 수
		if(!$currentPage) 
			$currentPage = 1;
		$pageIndex = ceil($currentPage/$pagePerBlock)-1;  // 몇 번째 페이지인지 ex) 1페이지 → $pageIndex = 0

		// html 태그 생성
		$pagingHtml = "";
		$pagingHtml .= "<div class='paging'>";
		$pagingHtml .= "<ol>";
		
		if($pageIndex>0) { // 첫번째 페이지($pageIndex = 0)가 아닌 경우 '처음으로', '이전' 버튼 활성화
			$pagingHtml .= "<li class='prev'><a title='맨 이전페이지' href='$requestURI" . "currentPage=1'> &lt;&lt; </a></li>";
			$prevPage = ($pageIndex)*$pagePerBlock;
			$pagingHtml .= "<li class='prev'><a title='이전 페이지' href='$requestURI" . "currentPage=$prevPage'> &lt; </a></li>";
		}else{ // 첫번째 페이지($pageIndex = 0)일 경우 '처음으로', '이전' 버튼 비활성화
			$pagingHtml .= "<li class='prev'><a title='맨 이전페이지' href='#'> &lt;&lt; </a></li>";
			$pagingHtml .= "<li class='prev'><a title='이전 페이지' href='#'> &lt; </a></li>";
		}
		
		$pageEnd=($pageIndex+1)*$pagePerBlock; // 현재 페이지 블럭의 마지막 페이지
		if($pageEnd>$totalPages) 
			$pageEnd=$totalPages; 

		for($setPage=$pageIndex*$pagePerBlock+1;$setPage<=$pageEnd;$setPage++){ // 페이지 생성
			if($setPage==$currentPage){
				$pagingHtml .= "<li class='current'><span>$setPage</span></li>";
			}else{
				$pagingHtml .= "<li><a href='$requestURI" . "currentPage=$setPage'>$setPage</a></li>";
			}
		}

		if($pageEnd<$totalPages){ // 마지막 페이지 블럭이 아닌 경우 '다음', '끝으로' 버튼 활성화
			$nextPage = ($pageIndex+1)*$pagePerBlock+1;
			$pagingHtml .= "<li class='next'><a title='다음 페이지' href='$requestURI" . "currentPage=$nextPage'> &gt; </a></li>";
			$pagingHtml .= "<li class='next'><a title='맨 다음 페이지' href='$requestURI" . "currentPage=$totalPages'> &gt;&gt; </a></li>";
		}else{ // 마지막 페이지 블럭인 경우 '다음', '끝으로' 버튼 비활성화
			$pagingHtml .= "<li class='next'><a title='다음 페이지' href='#'> &gt; </a></li>";
			$pagingHtml .= "<li class='next'><a title='맨 다음 페이지' href='#'> &gt;&gt; </a></li>";
		}
		$pagingHtml .= "</ul>";
		$pagingHtml .= "</div>";

		return $pagingHtml; // 완성된 태그 return
	}

	function fnGetHref(){
//		$href = $_SERVER["PHP_SELF"]; // 현재 페이지 주소
		$href = $_SERVER['SCRIPT_NAME']; // 현재 페이지 주소
		$i=0;		
		foreach($_REQUEST as $key => $data){ // request로 넘어온 값들 체크
			if($data != ''){
				if($key != "currentPage"){ // 페이지 관련 파라미터가 아닌 파라미터로만 문자열 생성
					if($i==0){
						$href.="?"; // 첫 파라미터 앞에는 ? 
					}else{
						$href.="&"; // 첫 파라미터가 아닌 경우에는 &
					}
					$href.=$key."=".$data; // param=value 형태로 주소 값 생성
					$i++; // 아래에서 이동
				}
			}
		}
		return $href;
	}

	function fnGetParams(){
		$params = "";
		$i=0;		
		foreach($_REQUEST as $key => $data){ // request로 넘어온 값들 체크
			if($data != ''){
				if($key != "currentPage"){ // 페이지 관련 파라미터가 아닌 파라미터로만 문자열 생성
					if($i==0){
						$params.="?"; // 첫 파라미터 앞에는 ? 
					}else{
						$params.="&"; // 첫 파라미터가 아닌 경우에는 &
					}
					$params.=$key."=".$data; // param=value 형태로 주소 값 생성
					$i++; // 아래에서 이동
				}
			}
		}

		$params .= ((strpos($params,"?") === false) ? "?" : "&"); // $params ?가 있으면 끝에 &를 붙이고 없으면 ?를 붙인다.
		return $params;
	}


	/*
		@auth : 서윤형
		@date : 2017-08-10
		@desc : 얼럿 메세지 띄우는 함수
		@param : 
			- $strMessage : alert 창에 띄울 메세지 내용
			- $strCommand : 실행할 javascript 명령문
			- $bInEnd :
				true 일 경우 alert 띄운 후 php 프로그램 종료(페이지 종료)
				false 일 경우 alert 띄운 후 계속 진행
	*/
	function fnShowAlertMsg($strMessage, $strCommand, $bInEnd){
		$strHTML = "<script type='text/javascript'>\n";
		if($strMessage != ""){
			$strHTML = $strHTML . " alert('".$strMessage."'); \n";
		}
		if($strCommand != ""){
			$strHTML = $strHTML . " " . $strCommand . "\n";
		}
		$strHTML = $strHTML . "</script>";
		echo $strHTML; // alert 실행
		if($bInEnd){
			exit;
		}
	}


	/*
		@auth : 서윤형
		@date : 2017-08-10
		@desc : 
			- http, https, ftp, telnet, news, mms 가 붙지 않은 $url 값에 http:// 를 붙인다.
			- 프로토콜에 해당되는 문자열이 붙은 경우 그대로 return
		@param : 
			- $url : 주소값 url
	*/
	function fnSetHttp($url){
		if (!trim($url)) return;

		if (!preg_match("/^(http|https|ftp|telnet|news|mms)\:\/\//i", $url))
		$url = "http://" . $url;

		return $url;
	}

	/*
		@auth : 서윤형
		@date : 2017-09-13
		@desc : 
			- 엑셀 내용을 복사해서 textarea 에 붙여넣기 후 전송 한 값을 fnMakeExcelToArray() 함수를 거치면 엑셀 내용을 2차원 배열로 return 한다.
			- ex)	1	2	3	4
					5	6	7	8
					9	10	11	12
				=> Array ( 
							[0] => Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 ) 
							[1] => Array ( [0] => 5 [1] => 6 [2] => 7 [3] => 8 ) 
							[2] => Array ( [0] => 9 [1] => 10 [2] => 11 [3] => 12 ) 
				   )
		@param : 
			- $str : 엑셀 내용을 복사해서 textarea 에 붙여넣기 한 값
	*/
	function fnMakeExcelToArray($str){
		$lineArray = explode("\n", $str);
		for($i=0;$i<sizeof($lineArray);$i++){
			$tabArray = explode("\t", $lineArray[$i]);
			for($j=0;$j<sizeof($tabArray);$j++){
				$lineArray[$i] = $tabArray;
			}
		}
		return $lineArray;
	}

	function fnIsMobile(){
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		$result = false;
		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$userAgent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($userAgent,0,4))){
			$result = true;
		}
		return $result;
	}


	/*
		@auth : 서윤형
		@date : 2017-12-13
		@desc : 
			- 날짜 계산 함수
		@param : 
			- $date : 날짜 ex) 2017-12-13, 2017-12-13 11:00 
			- $dateType : year, month, day
			- $add : +1, -1 
	*/
	function fnCalDate($date, $dateType, $add){
		return date('Y-m-d', strtotime($date.$add.$dateType));
	}

	function fnGetDayOfTTheWeek($strDate){
		$result = '';
		if(!empty($strDate)){
			$strDate = substr($strDate, 0, 10);
			$day = array("일","월","화","수","목","금","토");
			$result = $strDate. ' ('. $day[date('w', strtotime($strDate))] .')';
		}
		return $result;
	}

	/*
		@auth : 남동현
		@date : 2018-08-21
		@desc : 
			- 날짜 일수 차이 계산
		@param : 
			- $tDate : 타겟 날짜
			- $sDate : 기준 날짜
	*/
	function fnDateDiff($tDate, $sDate){
		$startDate	= new DateTime(date('Y-m-d'));				// 오늘 날짜입니다.
		$targetDate	= new DateTime( substr($tDate, 0, 10) );	// 타겟 날짜를 지정합니다.

		if( $sDate != "" ){
			$startDate = new DateTime( substr($sDate, 0, 10) );
		}

		$interval = $startDate->diff($targetDate);
 
		return $interval->days;
	}

	/*
		@auth : 남동현
		@date : 2018-08-24
		@desc : 
			- 메일 발송
		@param : 
			- $fromName		: 발신자
			- $fromEmail	: 발신주소
			- $toName		: 수신자
			- $toEmail		: 수신주소
			- $subject		: 제목
			- $contents		: 메일내용 
	*/
	function fnSendMail($fromName, $fromEmail, $toName, $toEmail, $subject, $contents, $isDebug=0){
		//Configuration
		$smtp_host = "203.231.233.140"; // YBMNET SMTP HOST
		//$smtp_host = "smtp.google.com";
		$port = 25;
		$type = "text/html";
		$charSet = "UTF-8";
		$sendmail_flag = false;
	 
		//Open Socket
		$fp = @fsockopen($smtp_host, $port, $errno, $errstr, 1);
		if($fp){
			//Connection and Greetting
			$returnMessage = fgets($fp, 128);
			if($isDebug)
			print "CONNECTING MSG:".$returnMessage."\n";
			fputs($fp, "HELO YA\r\n");
			$returnMessage = fgets($fp, 128);
			if($isDebug)
			print "GREETING MSG:".$returnMessage."\n";

			// 이부분에 다음과 같이 로긴과정만 들어가면됩니다.
			/*
			fputs($fp, "auth login\r\n");
			fgets($fp,128);
			fputs($fp, base64_encode("hoodist0@gmail.com")."\r\n");
			fgets($fp,128);
			fputs($fp, base64_encode("jeongsu99")."\r\n");
			fgets($fp,128);
			*/

			fputs($fp, "MAIL FROM: <".$fromEmail.">\r\n");
			$returnvalue[0] = fgets($fp, 128);
			fputs($fp, "rcpt to: <".$toEmail.">\r\n");
			$returnvalue[1] = fgets($fp, 128);

			if($isDebug){
				print "returnvalue:";
				print_r($returnvalue);
			}

			//Data
			fputs($fp, "data\r\n");
			$returnMessage = fgets($fp, 128);
			if($isDebug)
			print "data:".$returnMessage;
			fputs($fp, "Return-Path: ".$fromEmail."\r\n");
			$fromName = "=?UTF-8?B?".base64_encode($fromName)."?=";
			$toName = "=?UTF-8?B?".base64_encode($toName)."?=";
			fputs($fp, "From: ".$fromName." <".$fromEmail.">\r\n");
			fputs($fp, "To: ".$toName." <".$toEmail.">\r\n");
			$subject = "=?".$charSet."?B?".base64_encode($subject)."?=";

			fputs($fp, "Subject: ".$subject."\r\n");
			fputs($fp, "Content-Type: ".$type."; charset=\"".$charSet."\"\r\n");
			fputs($fp, "Content-Transfer-Encoding: base64\r\n");
			fputs($fp, "\r\n");
			$contents= chunk_split(base64_encode($contents));

			fputs($fp, $contents);
			fputs($fp, "\r\n");
			fputs($fp, "\r\n.\r\n");
			$returnvalue[2] = fgets($fp, 128);

			//Close Connection
			fputs($fp, "quit\r\n");
			fclose($fp);

			//Message
			if (preg_match("/^250/", $returnvalue[0])&&preg_match("/^250/", $returnvalue[1])){
				$sendmail_flag = true;
			}else {
				$sendmail_flag = false;
				print "NO :".$errno.", STR : ".$errstr;
			}
		}

		if (! $sendmail_flag){
			echo "메일 보내기 실패";
		}
		return $sendmail_flag;
	}


?>