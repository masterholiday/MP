$(function(){
	
	function resetPopUp(){
		$("#data_popup_main_wrap").html('');
		$("#data_popup_title").html('');
	}
	
	$("#close_popup_btn").click(function(){
		$("#data_popup_bg").hide();
		$("#loader_bg").hide();
		resetPopUp()
	})	
})