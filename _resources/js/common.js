$(function () {

    $(".tab_content").hide();
    $(".tab_content:first").show();

    $("ul.tabs li").click(function () {
        $("ul.tabs li").removeClass("active").css("color", "#8c8c8c");
        //$(this).addClass("active").css({"color": "darkred","font-weight": "bolder"});
        $(this).addClass("active").css("color", "#242424");
        $(".tab_content").hide()
        var activeTab = $(this).attr("rel");
        $("#" + activeTab).toggle()
    });
});



jQuery(function($){

	$('#alert_pop').fadeIn();


	$('#alert_open').click(function(){
		layer_open('alert_pop'); /* 열고자 하는 것의 아이디를 입력 */
		return false;
	});
	$('.layer_close').click(function(){
		$('#alert_pop').fadeOut();
		return false;
	});
	$(document).ready(function(){
});
});



