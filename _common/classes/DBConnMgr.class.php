<?php
	/*
		@auth : 서윤형
		@date : 2017-08-10
		@description : DB 커넥션 클래스 및 쿼리 관련 함수

		@modified : 2017-09-04 (서윤형)
			- fnSQLExecute() : 트랜잭션 기능 추가
	*/
	class DBConnMgr extends PDO{
		public $dbInfo; // DB Driver 정보
		public $dbUser; // DB 아이디
		public $dbPw; // DB 패스워드

		public $_conn; // connection 
		public $_result; // 결과값
		
		/* 생성자 - 전달받은 정보를 전역 변수에 담는다 */
		public function __construct($dbInfo, $dbUser, $dbPw){ 
			$this->dbInfo = $dbInfo;
			$this->dbUser = $dbUser;
			$this->dbPw = $dbPw;
		}

		/* DB connection open */
		function fnOpenDB(){
			$this->_conn = null;
			try{
				$this->_conn = new PDO ($this->dbInfo, $this->dbUser, $this->dbPw);
				$this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 예외를 처리 한다. 
				$this->_conn->setAttribute(PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE, true); // 예외를 처리 한다. 
			} catch (Exception $e) {
				//$error =$this->_conn->errorInfo();
				//die("Connection 시작 중 오류가 발생했습니다. : SQL Error={$error[0]}, DB Error={$error[1]}, Message={$error[2]}");
				die('Connection 시작 중 오류가 발생했습니다. <br><br>' . $e->getMessage());
			}
		}

		/* DB connection close */
		function fnCloseDB(){
			try{
				if($this->_conn) { 
					$this->_conn = null;
				}
			}catch(Exception $e){
				die('Connection 종료 중 오류가 발생했습니다. <br><br> ' . $e->getMessage());
			}
		}

		/* SELECT 쿼리용 함수 - arrRows[i]["컬럼명"] 으로 사용 가능 */
		function fnSQLSelect($sqlText){
			$this->fnOpenDB(); // DB connect - $this->_conn 에 커넥션 정보가 담긴다.
			try{
				$this->_result = $this->_conn->prepare($sqlText); 
				$this->_result->execute(); 
				$this->_result = $this->_result->fetchAll(PDO::FETCH_ASSOC);
			}catch(Exception $e){
				echo '- [Message] : '. $e->getMessage() . '<br>';
				echo '- [File] : '. $e->getTrace()[1]['file'] . '<br>';
				echo '- [Line] : '. $e->getTrace()[1]['line'] . '<br>';
				echo '- [Query] : '. $sqlText .'<br><br><br><br><br>';
				echo '- [Error] : 쿼리를 실행할 수 없습니다.<br><br>'; 
			}finally{
				$this->fnCloseDB(); // $this->_conn 을 null 처리 하여 커넥션 close 처리
			}			
			return $this->_result; // arrayName[rowCnt][ColName] 형태로 return (PDO::FETCH_ASSOC)
		}


		/* 
			insert, update, delete 쿼리용 함수 - 반영된 row 카운트를 return 해준다. 
			- transaction처리 기능 추가($sqlText에 여러 쿼리를 한번에 넣어서 실행할 경우 문제가 없으면 전부 실행, 문제가 있으면 rollback처리 -2017-09-04 서윤형
		*/
		function fnSQLExecute($sqlText){
			$this->fnOpenDB(); // DB connect - $this->_conn 에 커넥션 정보가 담긴다.
			// 전달된 쿼리문 실행 후 $this->_result 에 결과 저장(쿼리 실행 결과 반영된 row 카운트)
			try{
				$this->_conn->beginTransaction();
				$this->_result = $this->_conn->exec($sqlText); 
				$this->_conn->commit();
			}catch(Exception $e){
				echo '- [Message] : '. $e->getMessage() . '<br>';
				echo '- [File] : '. $e->getTrace()[1]['file'] . '<br>';
				echo '- [Line] : '. $e->getTrace()[1]['line'] . '<br>';
				echo '- [Query] : '. $sqlText .'<br><br><br><br><br>';
				echo '- [Error] : 데이터를 삽입할 수 없습니다. rollback 처리됩니다.<br><br>'; 
				$this->_conn->rollback();
			}finally{
				$this->fnCloseDB(); // $this->_conn 을 null 처리 하여 커넥션 close 처리
			}
			return $this->_result; // 쿼리 실행 결과 반영된 row 카운트 return
		}

		/* 
			트랜잭션 처리를 개별적으로 할 경우 사용할 함수 
				fnBeginTransaction();
				fnCommit();
				fnRollback();
			fnSQLExecute() 와 함께 사용할 필요 없다.
		*/
		function fnBeginTransaction(){
			$this->fnOpenDB();
			try{
				$this->_conn->beginTransaction();
			}catch(Exception $e){
				die('트랜잭션을 실행하던 중 오류가 발생했습니다.<br><br>' . $e);
			}
		}

		function fnCommit(){
			try{
				$this->_conn->commit();
			}catch(Exception $e){
				echo '- [Message] : '. $e->getMessage() . '<br>';
				echo '- [File] : '. $e->getTrace()[1]['file'] . '<br>';
				echo '- [Line] : '. $e->getTrace()[1]['line'] . '<br>';
				echo '- [Query] : '. $sqlText .'<br><br><br><br><br>';
				die('커밋을 실행하던 중 오류가 발생했습니다.<br><br>' . $e);
			}
		}

		function fnRollback(){
			try{
				$this->_conn->rollback();
			}catch(Exception $e){
				die('롤백을 실행하던 중 오류가 발생했습니다.<br><br>' . $e);
			}
		}

		
		

		/*
			DBConnMgr 객체 내의 변수에 할당된 메모리를 모두 회수한다.
			데이터베이스 이용 최종 단계에서 실행 후 DBConnMgr 의 객체변수도 null 처리 해주면 완벽히 메모리가 수거된다.

			기본 적으로 fnSQLSelect()나 fnSQLExecute()를 사용하면 커넥션은 자동으로 해제해 주지만
			클래스 자체를 null처리 하거나 unset()함수를 사용하더라도, 클래스 내부에서 사용된 변수들은 메모리 해제가 바로 되지 않는다. 
			즉, 바로 모든 메모리를 수거하고자 할 때 사용한다.

			사용 페이지에서 
				$객체변수명->fnClear();
				$객체변수명 = null;
			과 같이 사용.
		*/
		function fnClear(){
			$this->_conn = null;
			$this->_result = null;
			$this->dbInfo = null;
			$this->dbUser = null;
			$this->dbPw = null;			
		}
		function fnSQLPrepare($sqlText, $paramArray, $sqlMethod=''){
			$this->fnOpenDB(); // DB connect - $this->_conn 에 커넥션 정보가 담긴다.
			try{
				if($sqlMethod=='IUD'){
					if(stripos($sqlText, 'EXEC ')===0){
						$sqlText = substr($sqlText, 5);
					}
					$sqlText = 'DECLARE @result INT; EXEC @result = '.$sqlText.'; SELECT @result AS result;';
				}
				$this->_result = $this->_conn->prepare($sqlText);
				$this->_result->execute($paramArray); 
				$this->_result = $this->_result->fetchAll(PDO::FETCH_ASSOC);
			}catch(Exception $e){
				echo '- [Message] : '. $e->getMessage() . '<br>';
				echo '- [File] : '. $e->getTrace()[1]['file'] . '<br>';
				echo '- [Line] : '. $e->getTrace()[1]['line'] . '<br>';
				echo '- [Query] : '. $sqlText .'<br>';
				echo '- [Param] : ';
				print_r($e->getTrace()[0]['args'][0]);
				echo '<br><br><br><br><br>';
				echo '- [Error] : 쿼리를 실행할 수 없습니다.<br><br>'; 
			}finally{
				$this->fnCloseDB(); // $this->_conn 을 null 처리 하여 커넥션 close 처리
			}
			return $this->_result; // arrayName[rowCnt][ColName] 형태로 return (PDO::FETCH_ASSOC)
		}


	}
?>