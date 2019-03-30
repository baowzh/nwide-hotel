	$(function(){
				
				bindEvent();
			});
			
	}
			}
			function bindEvent(){
				$(".p_menue").on('click',function(){
					if($(this).parent(".menue").hasClass("down")){
						$(this).parent(".menue").removeClass("down").addClass("up");
						$(this).find("span").attr("class","folder_icon_on");
					}else{
						$(this).parent(".menue").removeClass("up").addClass("down");
						$(this).find("span").attr("class","folder_icon");
					}
					calculateHeight();
				});
				$(".c_menue_item").on("click",function(){
					if(!$(this).hasClass("checked")){
						$(".nav_tree_list .c_menue_item").removeClass("checked");
						$(this).addClass("checked");
					}
				})
				
			}
			
			
	 $(".search_btn").click(function(){
        var query  = $('.search_right input').val();
        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
        query = query.replace(/^&/g,'');
		if(query){
			query = 'title=' +query ;
        }
        if( url.indexOf('?')>0 ){
            url += '&' + query;
        }else{
            url += '?' + query;
        }
		window.location.href = url;
	  });	