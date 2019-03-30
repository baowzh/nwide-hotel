
			//开启广告
         if(open_ad==="1"){
		       $("#video").addClass("ad"); 
			   $("#video").attr('src',ad_url); 
			   $("#video").trigger('play'); 
				  //播放结束事件
				document.getElementById("video").onended=function(){
					        $(".danmakuTimer").hide();  
							 $("#video").attr('src',video_url);  
						     $("#video").removeClass('class',"ad");  
						     $("#video").trigger('play'); 

							 if(auth=="0"){
									   myVid.ontimeupdate=function(){
										console.log(this.currentTime);
											if(this.currentTime>previewtime){
												  //没有权限仅限试看
												  $("#buy").show(); 
												  $("#video").trigger('pause');
												
											}
										};
							 }else{
							 }

				};
		   }
		   //关闭广告
		   if(open_ad==="2"){
		                     $("#video").attr('src',video_url);  
						     $("#video").removeClass('class',"ad");  
						     $("#video").trigger('play'); 
	                  
							 if(auth=="0"){
									   myVid.ontimeupdate=function(){
										console.log(this.currentTime);
											if(this.currentTime>previewtime){
												  //没有权限仅限试看
												  $("#buy").show(); 
												  $("#video").trigger('pause');
												
											}
										};
							 }else{

							 }
		   
		   }	
			
			$("#video").trigger('play');