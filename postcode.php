<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="https://common.ybmnet.co.kr/postcode/js/postcode.js"></script>
<title>주소 검색 Sample</title>
<script type="text/javascript">
function getZipcodeSearch(){
	ybm.load(function(){
		new ybm.postcode({
			//recordsPerPage: "10",				// default : 10
			//pagePerBlock: "5",				// default : 5
			//layer: false,						// default : false (true 더라도 모바일에선 무조건 팝업으로 띄움)
			//layerTagId: "postcodeLayerPopup",	// default : "" (layerPopup 이 true 일때만 사용)
			oncomplete: function(data){			// callback func
				//console.log(data);			// zipNo, addrPart1, addrDetail
				document.getElementById("zip1").value = data.zipNo;
				document.getElementById("addr1").value = data.addrPart1;
				document.getElementById("addr2").value = data.addrDetail;
			}
		}).open();
	}());
}
</script>
</head>
<body>
<div class="wp_pc">
    <!-- 헤더 -->
    <div id="header">
    <a href="/"><span class="ic home" style="background:url(https://imagesisa.ybmsisa.com/platform/common/footer/addr_logo_ybmnet.png)!important;height:30px!important;width:140px!important"></span></a>
  	<h1>주소검색 샘플</h1>
    </div>
    <!-- 헤더// -->
    <!-- 컨텐츠 -->
    <div id="wrap_content">
		<div class="list_addr">
			<form name="j_form" id="j_form" method="post">
			<table>
				<colgroup>
				<col width="22%">
				<col width="78%">
				</colgroup>
				<tr>
					<td>주소 <span class="c_2">*</span></td>
					<td>
						<div class="mag_b3">
							<input class="i_txt_bg required" name="zip1" id="zip1" type="text" style="width:55%;" value="" readonly > <a href="#" class="btn_cn" onclick="getZipcodeSearch()">주소검색</a>
						</div>
						<div class="mag_b3">
							<input id="addr1" name="addr1" maxlength="100" readonly="" class="required i_txt_bg" type="text" style="width:88%;" value="" >
						</div>
						<div>
							<input id="addr2" name="addr2" maxlength="100" class="i_txt_bg" type="text" style="width:88%;" value="" >
						</div>
					</td> 
				</tr>
			</table>
			</form>
		</div>
	</div>
	<div id="postcodeLayerPopup"></div>
</div>
</body>
</html>