<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>yercms内容管理系统</title>
	<link href="__CSS__/base.css" rel="stylesheet" type="text/css"/>
	<link href="__CSS__/style.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="__JS__/jquery-1.7.2.min.js"></script>

</head>

<body class="body">
	<div class="blank10">
	</div>
	<!--logo start-->
	<div class="logo w88">
		<div class="logo_in in_width">
			<h1 class="logo_pic fl"><a href="{:url('index/index')}"><img title="YERSHOP" src="__IMG__/logo.png"  />YERcms</a></h1>			
			<div class="head-search-layout">
				
				
			</div>			
			<div class="log" style="">
				
				{if condition="is_login()"}
			
					 {$UcenterMember.username},
					
					<a href="{:url('User/logout')}" class="tc_a">退出</a>
		
			{else/}
					<a href="{:url('User/login')}" class="login_a">登录</a>|
					<a href="{:url('User/register')}" class="zc_a">注册</a>|
					<a href="{:url('index/index')}" class="zc_a">首页</a>
			{/if}				
			</div>			
			
		</div>
	</div>
	<!--/logo end--> 
	
	<div class="line">
   
    </div>
	   <style type="text/css">
        *{ padding: 0; margin: 0; }
        body{ background: #fff; font-family: "Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif; color: #333; font-size: 16px; }
        .system-message{ padding: 24px 48px; }
        .system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
        .system-message .jump{ padding-top: 10px; }
        .system-message .jump a{ color: #333; }
        .system-message .success,.system-message .error{ line-height: 1.8em; font-size: 36px; }
        .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display: none; }
    </style>
	<div class="member" style="min-height:300px">
        <div class="system-message">
        <?php switch ($code) {?>
            <?php case 1:?>
            <h1>:)</h1>
            <p class="success"><?php echo(strip_tags($msg));?></p>
            <?php break;?>
            <?php case 0:?>
            <h1>:(</h1>
            <p class="error"><?php echo(strip_tags($msg));?></p>
            <?php break;?>
        <?php } ?>
        <p class="detail"></p>
        <p class="jump">
            页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>
        </p>
    </div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
  
   
    </div>
	
		{include file="public/footer" /}
	
	
	
	
	
	
	