<?php
class PagingUtil {
	/*
		@auth : 서윤형
		@date : 2017-08-18
		@desc : 
			- 페이징 클래스			

			- 함수 설명
				1. fnGetHref() : 함수를 이용하여 페이징 관련 파라미터를 제외한 주소값을 가져와서 링크 생성
				2. fnPaginator() : fnGetHref() 함수와 객체 생성 시 입력한 값들을 이용하여 페이징 태그를 완성
				3. getBeginRow() : 현재 페이지에 해당되는 레코드의 처음 값 return
				4. getEndRow() : 현재 페이지에 해당되는 레코드의 마지막 값 return

			- 사용법
				페이징을 사용 할 페이지에서
				1. $객체변수명 = enw PagingUtil($currentPage, $totalRecords, $pagePerBlock, $recordsPerPage); 로 객체를 생성한다.

				2. $객체변수명->getBegin() 을 할 경우 현재 페이지에 해당되는 레코드의 처음 값을 return
				3. $객체변수명->getEnd() 를 할 경우 현재 페이지에 해당되는 레코드의 마지막 값을 return
					한 페이지에 10개의 레코드가 보이는 게시판의 경우
						1페이지 → 1~10
						2페이지 → 11~20
						3페이지 → 21~30
					을 가져와야 한다. 그 처음 값, 마지막 값을 뜻한다. (list 쿼리 조건절에 사용할 변수)

				4. $객체변수명->fnPaginator() 를 할 경우 객체 생성 시 입력 한 값들을 이용하여 페이징 태그를 생성

		@param : 
			- $currentPage : 현재 페이지
			- $totalRecords : 총 레코드 수
			- $pagePerBlock : 한 블럭에 보일 페이지 수 ex) 10 → < 1 2 3 4 5 6 7 8 9 10 >
			- $recordsPerPage : 한 페이지에 보일 레코드 수

			- $begin : 현재 페이지에 해당되는 레코드의 처음 값 
			- $end : 현재 페이지에 해당되는 레코드의 마지막 값 
	*/
	private $currentPage; 
	private $totalRecords;
	private $pagePerBlock;
	private $recordsPerPage;
	
	private $begin;
	private $end;
	
	/* 생성자 - 전달받은 정보를 이용해 파라미터를 만들고 전역변수에 담는다. */
	public function __construct($currentPage, $totalRecords, $pagePerBlock, $recordsPerPage){ 
		if($currentPage==''){
			$currentPage = 1;
		}

		$this->currentPage = $currentPage;
		$this->totalRecords = $totalRecords;
		$this->recordsPerPage = $recordsPerPage;
		$this->pagePerBlock = $pagePerBlock;

		$this->begin = ($currentPage -1) * $recordsPerPage;
		$this->end = $this->begin + $recordsPerPage;
		$this->begin = $this->begin + 1;
	}

	
	function fnPaginator(){
		$href = $this->fnGetHref(); // 기존 URI + currentPage 를 제외한 파라미터를 문자열로 생성
		$requestURI = $href.((strpos($href,'?')  <= 0) ? '?' : '&'); // $href에 ?가 있으면 끝에 &를 붙이고 없으면 ?를 붙인다.
		
		// 페이징 계산   1000 / 10
		$totalPages = ceil($this->totalRecords/$this->recordsPerPage); // 총 페이지 수
		if(!$this->currentPage) 
			$this->currentPage = 1;
		$pageIndex = ceil($this->currentPage/$this->pagePerBlock)-1;  // 몇 번째 페이지인지 ex) 1페이지 → $pageIndex = 0
		
		// html 태그 생성
		$pagingHtml = '';
		$pagingHtml .= '<div>';
		$pagingHtml .= '<ul class="pagination">';
		
		if($pageIndex>0) { // 첫번째 페이지($pageIndex = 0)가 아닌 경우 '처음으로', '이전' 버튼 활성화
			$pagingHtml .= '<li><a href="' . $requestURI . 'currentPage=1"><<</a></li>';
			$prevPage = ($pageIndex)*$this->pagePerBlock;
			$pagingHtml .= '<li><a href="' . $requestURI . 'currentPage=' . $prevPage . '"><</a></li>';
		}else{ // 첫번째 페이지($pageIndex = 0)일 경우 '처음으로', '이전' 버튼 비활성화
			$pagingHtml .= '<li><a href="' . $requestURI . 'currentPage=1"><<</a></li>';
			$pagingHtml .= '<li class="disabled"><a href="#"><</a></li>';
		}
		
		$pageEnd=($pageIndex+1)*$this->pagePerBlock; // 현재 페이지 블럭의 마지막 페이지
		if($pageEnd>$totalPages) 
			$pageEnd=$totalPages; 

		for($setPage=$pageIndex*$this->pagePerBlock+1;$setPage<=$pageEnd;$setPage++){ // 페이지 생성
			if($setPage==$this->currentPage){
				$pagingHtml .= '<li class="active"><a href="#">' . $setPage . '</a></li>';
			}else{
				$pagingHtml .= '<li><a href="' . $requestURI . 'currentPage=' . $setPage . '">' . $setPage . '</a></li>';
			}
		}

		if($pageEnd<$totalPages){ // 마지막 페이지 블럭이 아닌 경우 '다음', '끝으로' 버튼 활성화
			$nextPage = ($pageIndex+1)*$this->pagePerBlock+1;
			$pagingHtml .= '<li><a href="' . $requestURI . 'currentPage=' . $nextPage . '">></a></li>';
			$pagingHtml .= '<li><a href="' . $requestURI . 'currentPage=' . $totalPages . '">>></a></li>';
		}else{ // 마지막 페이지 블럭인 경우 '다음', '끝으로' 버튼 비활성화
			$pagingHtml .= '<li class="disabled"><a href="#">></a></li>';
			$pagingHtml .= '<li><a href="' . $requestURI . 'currentPage=' . $totalPages . '">>></a></li>';
		}
		$pagingHtml .= '</ul>';
		$pagingHtml .= '</div>';

		return $pagingHtml; // 완성된 태그 return
	}


	private function fnGetHref(){
		$href = $_SERVER['PHP_SELF']; // 현재 페이지 주소
		$i=0;		
		foreach($_REQUEST as $key => $data){ // request로 넘어온 값들 체크
			if($key != 'currentPage'){ // 페이지 관련 파라미터가 아닌 파라미터로만 문자열 생성
				if($i==0){
					$href.='?'; // 첫 파라미터 앞에는 ? 
				}else{
					$href.='&'; // 첫 파라미터가 아닌 경우에는 &
				}
				$href.=$key.'='.$data; // param=value 형태로 주소 값 생성
			}
			$i++;
		}
		return $href;
	}


	public function getBegin(){
		return $this->begin;
	}


	public function getEnd(){
		return $this->end;
	}
}
?>