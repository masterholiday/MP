var html="";
	var headers = new Array(0,0,0,0,0,0,0,0,0,0,0,0);
	var text = new Array(0,0,0,0,0,0,0,0,0,0,0,0);


	
$(function(){
    $(".info").mouseover(function() {
	 var index = $(".info").index(this);
	 
	 if(headers[index]!=0){
	 html = "<div id='head'>"+headers[index]+"</div><div id='tiptext'>"+text[index]+"</div>"
	 }
	 else{
	 index = index+1;
	 html = "<img src='../../images/tips/tip"+index+".png'/>";
	 }
	 $(".tipcontent").empty();
	 $(".tipcontent").append(html);
	$(".tip").show();
    });
	 $(".info").mouseout(function() {
       $(".tip").hide();
    });
});
	 