(function($) {

    var	common	= function() {
    };

	common.string	= {
		/**
		 * 문자열의 앞뒤 공백문자를 제거한다.
		 *
		 * @param str 문자열
		 * @return 앞뒤 공백문자가 제거된 문자열
		 */
		/** input 숫자만 입력하게 **/

		trim : function(str) {
			return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
		},

		trim2 : function(str) {
			return str.replace(/(^\s*)|(\s*$)/gi, '').replace(/(^\s*)|(\s*$)/gi, '');
		},

		/**
		 * 문자열의 일부를 지정한 문자열로 교체합니다.
		 *
		 * @param str 문자열
		 * @param findStr 교체 대상 문자열
		 * @param replaceStr 교체할 문자열
		 * @return 지정한 문자열로 교체된 문자열
		 */
		replace : function(str, findStr, replaceStr) {
			if (!str) {
				return str;
			}

			return str.split(findStr).join(replaceStr);

			//return str.replace(new RegExp(findStr, "g"), replaceStr);
		},

		// 숫자 문자열&&계산완료 문자열 교체
		checkNumReplace : function(str, replaceStr) {
			if (str == 'NaN' || str == '0') {
				str = "-";
			}
			return str;
		},


		/**
		 * 패딩문자로 문자열의 왼쪽을 채운다.
		 *
		 * @param src 원본문자열
		 * @param length 문자열의 제한 길이
		 * @param pad 채울 문자
		 * @return 패딩된 문자열
		 */
		lpad : function(src, length, pad) {
			if (!this.hasText(src)) {
				return "";
			}

			var	buffer	= [];
			for (var i = 0; i < length - src.length; i++) {
				buffer.push(pad);
			}

			src	= buffer.join("") + src;

			return src.substring(0, length);
		},

		/**
		 * 문자열이 통화형식(123,456.78)인지 확인한다.
		 *
		 * @param str 문자열
		 * @return 형식이 맞으년 true, 아니면 false
		 */
		isCurrency : function(str) {
			return !str.match(/(-){,1}[^0-9,\.]{1,}/);
		},

		/**
		 * 문자열이 정수형(1234567)인지 확인한다.
		 * @param str 문자열
		 * @return 형식이 맞으년 true, 아니면 false
		 */
		isInteger : function(str) {
			return !str.match(/(-){,1}[^0-9]{1,}/);
		},

		/**
		 * 공백을 제외한 문자열을 가지고 있는지 확인한다.
		 * @param str 체크할 문자열
		 * @return 문자열의 길이가 > 0 이면 true, 아니면 false
		 */
		hasText : function(str) {
			if (!str) {
				return false;
			}

			str	= this.trim(str);
			if ("" == str) {
				return false;
			}

			return true;
		},

		/**
		 * 주어진 문자열이 null 이거나 "" 인지 확인
		 * @param str 비교할 문자열
		 * @return null 이거나 "" 이면 true, 아니면 false
		 */
		isEmpty : function(str) {
			return ((null == str) || ("" == str)) ? true : false;
		},


		/**
		 * 주어진 문자열이 null 이거나 "" 인지 확인
		 * @param str 비교할 문자열
		 * @return null 이거나 "" 이면 true, 아니면 false
		 */
		isCheckStr : function(str, addStr) {

			var result =  ((null == str) || ("" == str) || (addStr == str)) ? true : false;
			if(result){
				return '';
			}else{
				return str;
			}
		},

		/**
		 * 주어진 문자열이 null 또는 "" 가 아닌지 확인
		 * @param str 비교할 문자열
		 * @return null 이거나 "" 이면 true, 아니면 false
		 */
		isNotEmpty : function(str) {
			return ((null == str) || ("" == str)) ? false : true;
		},

		/**
		 * 바이트로 환산한 문자열의 길이값 반환
		 *
		 * @param str 문자열
		 * @return 문자열의 바이트 길이
		 */
		getBytesLength : function(str) {
			var	str_len		= str.length;
			var	byte_cnt	= 0;

			if (str_len != escape(str).length) {
				for (var i = 0; i < str_len; i++) {
					byte_cnt++;

					if (this.isUnicode(str.charAt(i))) {
						byte_cnt++;
					}
				}
			} else {
				byte_cnt	= str_len;
			}

			return byte_cnt;
		},

		/**
		 * 문자가 유니코드인지 확인한다.
		 * @param chr 문자
		 * @return 유니코드이면 true, 아니면 false
		 */
		isUnicode : function(chr) {
			return (escape(chr).length == 6);
		},

		/**
		 * 바이트로 환산한 문자열의 길이값 반환
		 *
		 * @param str 문자열
		 * @return 문자열의 UTF-8 바이트 길이
		 */
		getBytesLengthUTF8 : function(str) {
			if ((null == str) || (0 == str.length)) {
				return 0;
			}

			var	byte_cnt	= 0;

			for (var i = 0; i < str.length; i++) {
				byte_cnt	+= this.charByteSizeUTF8(str.charAt(i));
			}

			return byte_cnt;
		},

		/**
		 * 문자의 유니코드를 분석하여, UTF-8로 변환시 차지하는 byte 수를 리턴한다.
		 *
		 * @param ch 문자
		 * @return 문자의 UTF-8 바이트 길이
		 */
		charByteSizeUTF8 : function (ch) {
			if ((null == ch) || (0 == ch.length)) {
				return 0;
			}

			var	charCode	= ch.charCodeAt(0);

			if (0x00007F >= charCode) {
				return 1;
			} else if (0x0007FF >= charCode) {
				return 2;
			} else if (0x00FFFF >= charCode) {
				return 3;
			} else {
				return 4;
			}
		},

		setComma : function(num) {
			var	str		= num + "";
			var	sep		= str.split('.');
			var	num_int	= sep[0];
			var	num_pnt	= (1 < sep.length) ? ('.' + sep[1]) : '';
			var	rgx		= /(\d+)(\d{3})/;

			while (rgx.test(num_int)) {
				num_int	= num_int.replace(rgx, '$1' + ',' + '$2');
			}

			return num_int + num_pnt;
		},

		removeComma : function(str) {
			return common.string.replace(str, ",", "");
		},

		/**
		 * mobile의 번호에 자동으로 하이푼(-) 추가한다.
		 * */
		autoHypenPhone : function(str, id){

			str = str.replace(/[^0-9]/g, '');
			var tmp = '';
			if( str.length < 4){
				tmp += str;
			}else if(str.length < 7){
				tmp += str.substr(0, 3);
				tmp += '-';
				tmp += str.substr(3);
			}else if(str.length < 11){
				tmp += str.substr(0, 3);
				tmp += '-';
				tmp += str.substr(3, 3);
				tmp += '-';
				tmp += str.substr(6);
			}else{
				tmp += str.substr(0, 3);
				tmp += '-';
				tmp += str.substr(3, 4);
				tmp += '-';
				tmp += str.substr(7);
			}
			$('#'+id).val(tmp);
			//return str;
		},

		autoPasteBox : function(str, id){
			str = str.replace(/[^0-9]/g, '');
			var tmp1 = '';
			var tmp2 = '';
			var tmp3 = '';
			if( str.length >= 9){
				if(str.length == 9){
					//02
					tmp1 = str.substr(0, 2);
					tmp2 = str.substr(2, 3);
					tmp3 = str.substr(5);
				}else if(str.length == 10){
					tmp1 = str.substr(0, 3);
					tmp2 = str.substr(3, 3);
					tmp3 = str.substr(6);
				}else if(str.length == 11){
					tmp1 = str.substr(0, 3);
					tmp2 = str.substr(3, 4);
					tmp3 = str.substr(7);
				}
				$('#'+id+"1").val(tmp1);
				$('#'+id+"2").val(tmp2);
				$('#'+id+"3").val(tmp3);
			}


			//return str;
		},

		// 패스워드 체크
		checkerPassword : function (fnval) {
			var userPw = fnval;
//			var RegExp = /^.*(?=^.{8,32}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/;
//			var RegExp = /^[a-zA-Z0-9]{8,32}$/; //숫자 영문 체크
			var RegExp = /^(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9])(?=.*[0-9]).{8,32}$/;
			var RegExp2 = /(\w)\1\1\1/; //같은문자 4번이상 사용체크
			if(!RegExp.test(userPw) || userPw.indexOf(' ') > -1) {
				if (!RegExp.test(userPw)) {
					alert("비밀번호는 숫자와 영문자 특수문자 조합으로 8~32자리를 사용해야 합니다.");
				}
				  
				if(userPw.indexOf(' ') > -1) {
					alert("비밀번호에 공백을 입력하시면 안됩니다.");
				}
				//alert('비밀번호는 숫자와 영문자 특수문자 조합으로 8~32자리를 사용해야 합니다.');
				return true;
			}
			var chk_num = userPw.search(/[0-9]/g);
			var chk_eng = userPw.search(/[a-z]/ig);
			if(chk_num < 0 || chk_eng < 0) {
				alert('비밀번호는 숫자와 영문자를 혼용하여야 합니다.');
				return true;
			}
			if(RegExp2.test(userPw)) {
				alert('비밀번호에 같은 문자를 4번 이상 사용하실 수 없습니다.');
				return true;
			}
			return false;
		},

		/**
		* 소수점 변환기
		* @param float_val 변환할 값
		* @param chk_num 몇번째 소수점
		*/
		float_change : function(float_val,chk_num){
			var num_format = 10;
			for (var i = 1; i < chk_num; i++) {
				num_format *= 10;
			}
			num_format = parseInt(num_format);
			return parseFloat(Math.round(parseFloat(float_val)*num_format)/num_format);
		},


		/** 
		* 숫자만 입력 
		* @  ex) common.string.onlyNumber($("input[name=roomCount]"));
		**/
		onlyNumber : function(obj){
			obj.attr("style", "ime-mode:disabled");
			obj.keypress(function(){if (event.which && (event.which <= 47 || event.which >= 58) && event.which != 8) {event.preventDefault();}});
			obj.keyup(function(){event = event || window.event;var keyID = (event.which) ? event.which : event.keyCode;if ( keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39 ){return;}else{event.target.value = event.target.value.replace(/[^0-9]/g, "");}});
		},

		/** 공백제거 **/
		nvl:function(val,defVal){
				if(val!=undefined && val!=null && val.length>0){
					return $.trim(val);
				}else{
					return defVal;
				}
		 },


		dummy : function() {
			// not used
		}
	};

	// ajax 통신
	common.http = {
		ajax : function(opts) {

		}
	};

	//공통코드 콤보BOX
	common.sys	= {

		/** combo select option html
		 * list     		: 결과 리스트
		 * optYn       		: 상단 옵션 사용여부(Y, N)
		 * firstOptVal      : 상단 옵션 value
		 * firstOptLable    : 상단 옵션 text
		 * common.sys.setComboOptHtml(list, "Y", "", "전체");
		 * **/
		setComboOptHtml : function(list, optYn, firstOptVal, firstOptLable) {
			var html	= '';
			if( optYn=="Y" ){
				html	+= '<option value="'+firstOptVal+'">'+firstOptLable+'</option>';
			}
			for(var i=0 ; i<list.length; i++){
				html	+= '<option value="'+list[i].cd+'">'+list[i].cdNm+'</option>';
			}
			return html;
		},

		/** combo select option html
		 * optYn       		: 상단 옵션 사용여부(Y, N)
		 * firstOptVal      : 상단 옵션 value
		 * firstOptLable    : 상단 옵션 text
		 * common.sys.setBonbuCombo("bonbuCd", "teamCd", "N", "", "");
		 * **/
		setEmpty : function(optYn, firstOptVal, firstOptLable) {
			var html	= '';
			if( optYn=="Y" ){
				html	+= '<option value="'+firstOptVal+'">'+firstOptLable+'</option>';
			}
			return html;
		},

		/** SbInfo 정보 세팅
		 * obj     		: 파라미터 객체
		 * var param = {
		 *		"sbInfo" 			: "sbInfo"		// SbInfo 정보
		 *		, "sbKind" 			: "sbKind"		// sbKind 정보
		 *		, "optYn"			: "Y"			// 상단 옵션 사용여부(Y, N)
		 *		, "firstOptVal"		: ""			// 상단 옵션  value
		 *		, "firstOptLable"	: "전체"			// 상단 옵션  text
		 * }
		 * common.sys.setSbInfoCreate(param);
		 * **/
		setSbInfoCreate : function(obj) {
			$('#'+obj.sbInfo).html( common.sys.setComboOptHtml(
												common.sys.getSbInfoList( obj.sbKind )
												, obj.optYn
												, obj.firstOptVal
												, obj.firstOptLable));

		},

		// SbInfo data 가져오기
		getSbInfoList : function( sbKind ) {
			var returnVal = "";
			var u = "/common/commonAjaxProc.php";
			var param = {				
				"proc"		: "sbInfoList",
				"sbKind"	: sbKind
			};	

			$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
				success: function(resJson) {
					returnVal = resJson.data;
					console.log(returnVal);
				},
				error: function(e) {
					alert("현재 서버 통신이 원할하지 않습니다.");
//					console.log("[Error]");
//					console.log(e);
					returnVal = "";
				}
			});

			return returnVal;
		},

		/** 부서 정보 세팅
		 * obj     		: 파라미터 객체
		 * var param = {
		 *		"detpLev1" 			: "detpLev1"	// 1detp 부서정보
		 *		, "detpLev2" 		: "detpLev2"	// 2detp 부서정보
		 *		, "detpLev3" 		: "detpLev3"	// 3detp 부서정보
		 *		, "optYn"			: "Y"			// 상단 옵션 사용여부(Y, N)
		 *		, "firstOptVal"		: ""			// 상단 옵션  value
		 *		, "firstOptLable"	: "전체"			// 상단 옵션  text
		 * }
		 * common.sys.setDeptComboCreate(param);
		 * **/
		setDeptComboCreate : function(obj) {


			// 1detp 부서정보 Set
			$('#'+obj.detpLev1).html( common.sys.setComboOptHtml(
												common.sys.getDetpLev1List()
												, obj.optYn
												, obj.firstOptVal
												, obj.firstOptLable));

			//2detp 부서정보 초기화
			$('#'+obj.detpLev2).empty();
			$('#'+obj.detpLev2).html( common.sys.setEmpty( obj.optYn, obj.firstOptVal, obj.firstOptLable) );

			//3detp 부서정보 초기화
			$('#'+obj.detpLev3).empty();
			$('#'+obj.detpLev3).html( common.sys.setEmpty( obj.optYn, obj.firstOptVal, obj.firstOptLable) );

			// 1detp onChange
			$('#'+obj.detpLev1).change(function(){
				//2detp 부서정보 초기화 및 데이터 가져오기
				$('#'+obj.detpLev2).empty();
				$('#'+obj.detpLev2).html( common.sys.setComboOptHtml(
											common.sys.getDetpLev2List( $('#'+obj.detpLev1).val() )
											, obj.optYn
											, obj.firstOptVal
											, obj.firstOptLable));


				//3detp 부서정보 초기화
				$('#'+obj.detpLev3).empty();
				$('#'+obj.detpLev3).html( common.sys.setEmpty( obj.optYn, obj.firstOptVal, obj.firstOptLable) );
			});

			// 2detp onChange
			$('#'+obj.detpLev2).change(function(){
				//3detp 부서정보 초기화 및 데이터 가져오기
				$('#'+obj.detpLev3).empty();
				$('#'+obj.detpLev3).html( common.sys.setComboOptHtml(
											common.sys.getDetpLev3List( $('#'+obj.detpLev1).val(), $('#'+obj.detpLev2).val() )
											, obj.optYn
											, obj.firstOptVal
											, obj.firstOptLable));
			});
		},

		// 1detp data 가져오기
		getDetpLev1List : function() {
			var returnVal = "";
			var u = "/common/commonAjaxProc.php";
			var param = {				
				"proc" : "detpLev1Ajax"
			};	

			$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
				success: function(resJson) {
					returnVal = resJson.data;
				},
				error: function(e) {
					alert("현재 서버 통신이 원할하지 않습니다.");
//					console.log("[Error]");
//					console.log(e);
					returnVal = "";
				}
			});

			return returnVal;
		},
		
		// 2detp data 가져오기
		getDetpLev2List : function( detpLev1 ) {
			var returnVal = "";
			var u = "/common/commonAjaxProc.php";
			var param = {
				"proc"		: "detpLev2Ajax",
				"detpLev1"	: detpLev1
			};

			$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
				success: function(resJson) {					
					returnVal = resJson.data;
				},
				error: function(e) {
					alert("현재 서버 통신이 원할하지 않습니다.");
					returnVal = "";
				}
			});

			return returnVal;
		},

		// 3detp data 가져오기
		getDetpLev3List : function( detpLev1, detpLev2 ) {
			var returnVal = "";
			var u = "/common/commonAjaxProc.php";
			var param = {
				"proc"		: "detpLev3Ajax",
				"detpLev1"	: detpLev1,
				"detpLev2"	: detpLev2
			};

			$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
				success: function(resJson) {					
					returnVal = resJson.data;
				},
				error: function(e) {
					alert("현재 서버 통신이 원할하지 않습니다.");
					returnVal = "";
				}
			});

			return returnVal;
		},


		/** 지역 정보 세팅
		 * obj     		: 파라미터 객체
		 * var param = {
		 *		"areaLev1" 			: "areaLev1"	// 1detp 시도
		 *		, "areaLev2" 		: "areaLev2"	// 2detp 군구
		 *		, "optYn"			: "Y"			// 상단 옵션 사용여부(Y, N)
		 *		, "firstOptVal"		: ""			// 상단 옵션  value
		 *		, "firstOptLable"	: "전체"			// 상단 옵션  text
		 * }
		 * common.sys.setAreaComboCreate(param);
		 * **/
		setAreaComboCreate : function(obj) {
			// 1detp 지역정보 Set
			$('#'+obj.areaLev1).html( common.sys.setComboOptHtml(
												common.sys.getAreaLev1List()
												, obj.optYn
												, obj.firstOptVal
												, obj.firstOptLable));

			//2detp 지역정보 초기화
			$('#'+obj.areaLev2).empty();
			$('#'+obj.areaLev2).html( common.sys.setEmpty( obj.optYn, obj.firstOptVal, obj.firstOptLable) );


			// 1detp onChange
			$('#'+obj.areaLev1).change(function(){
				//2detp 지역정보 초기화 및 데이터 가져오기
				$('#'+obj.areaLev2).empty();
				$('#'+obj.areaLev2).html( common.sys.setComboOptHtml(
											common.sys.getAreaLev2List( $('#'+obj.areaLev1).val() )
											, obj.optYn
											, obj.firstOptVal
											, obj.firstOptLable));
			});
		},

		// 1detp data 가져오기
		getAreaLev1List : function() {
			var returnVal = "";
			var u = "/common/commonAjaxProc.php";
			var param = {				
				"proc" : "areaLev1Ajax"
			};	

			$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
				success: function(resJson) {
					returnVal = resJson.data;
				},
				error: function(e) {
					alert("현재 서버 통신이 원할하지 않습니다.");
//					console.log("[Error]");
//					console.log(e);
					returnVal = "";
				}
			});

			return returnVal;
		},
		
		// 2detp data 가져오기
		getAreaLev2List : function( areaLev1 ) {
			var returnVal = "";
			var u = "/common/commonAjaxProc.php";
			var param = {
				"proc"		: "areaLev2Ajax",
				"areaLev1"	: areaLev1
			};

			$.ajax({ type:'post', url: u, dataType : 'json',data:param, async : false,
				success: function(resJson) {					
					returnVal = resJson.data;
				},
				error: function(e) {
					console.log(e)
					alert("현재 서버 통신이 원할하지 않습니다.");
					returnVal = "";
				}
			});

			return returnVal;
		},
	
	};

	window.common	= common;
})(jQuery);


