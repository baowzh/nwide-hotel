
$(function(){
    $(".show_more_btn").click(function(){
　　　　var status=$(".text").attr("status");
　　　　if(status="1"){
　　　　　　overflow:hidden;
　　　　　　$(".text").addClass(".close_text");
　　　　　　$(".text").attr("status",1);
　　　　　　$(this).html("查看更多");
　　　　}else{
　　　　　　$(".text").removeClass(".close_text");
　　　　　　$(".text").attr("status",0);
　　　　　　$(this).html("收起")
　　　　}
　　　　return false;
　　})
})