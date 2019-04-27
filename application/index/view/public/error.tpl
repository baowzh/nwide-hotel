
{include file="public/head" /} 
{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>跳转提示</title>
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        body{ background: #fff; font-family: "Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif; color: #333; font-size: 16px; }
        .system-message{ margin:50px auto;   padding-bottom:150px;  width: 1150px;}
        .system-message h1{ font-size: 16px; font-weight: normal; line-height: 36px; height:36px;margin-bottom: 19px; border-bottom: 1px solid #E6E6E6; }
        .system-message .jump{ text-indent:2em;padding-top: 10px; }
        .system-message .jump a{ color: #333; }
        .system-message .success,.system-message .error{ text-indent:2em;   color: #333; line-height: 1.8em; font-size: 16px; }
        .system-message .detail{ text-indent:2em;font-size: 12px; line-height: 20px; margin-top: 12px; display: none; }
    </style>
</head>
<body>
    <div class="system-message">
        <?php switch ($code) {?>
            <?php case 1:?>
            if(!$result["code_url"]){
		   $this->error('ID错误！');
		}
            <p class="success"><?php echo($msg);?></p>
            <?php break;?>
            <?php case 0:?>
            <h1>系统消息</h1>
            <p class="error"><?php echo($msg);?></p>
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
{include file="public/footer" /} 
