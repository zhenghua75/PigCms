<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title>登陆－<?php echo ($f_siteTitle); ?></title>
<meta name="keywords" content="微信帮手 微信公众账号 微信公众平台 微信公众账号开发 微信二次开发 微信接口开发 微信托管服务 微信营销 微信公众平台接口开发"/>
<meta name="description" content="微信公众平台接口开发、托管、营销活动、二次开发"/>
<style>
.contaier{ zoom: 1; }
.contaier:after{ content : '.'; display: block; width: 0; height: 0; visibility: hidden; line-height: 0; font-size: 0; clear: both; }
/*基础*/
body{ font-size:16px; font-family: Century Gothic, \5FAE\8F6F\96C5\9ED1,\5E7C\5706, Arial, Verdana; color: #323232; }
select,input,textarea{ outline: none; font-family: Century Gothic, \5FAE\8F6F\96C5\9ED1,\5E7C\5706, Arial, Verdana; font-size: 16px;color:#323232 }
textarea{ resize: none; overflow: auto;}
a{ text-decoration: none; color: #007DDB; }
a:hover{ text-decoration: underline; }
/*布局*/
.wp{ padding: 0 12px; margin: 0 auto;}
.contaier{ padding-bottom: 18px; padding-top: 120px; width:330px; height:210px; }
.think-login{ float: left; }
.think-form th,.think-form td{ padding: 6px 0; color:#fff}
.think-form th{ font-weight: normal; vertical-align: top; line-height: 32px; padding-right: 9px; text-align: left; }
.think-form .text{ height: 24px; line-height: 24px; padding: 3px; border: 1px solid #ccc; }
.think-form .text:focus{ box-shadow: 0 0 10px rgba(255,255,255,1); }
.think-form .checkbox{ margin-right: 6px; }
.think-form .submit{ background: #348FD4; color: #fff; font-size: 16px; height: 30px; line-height: 21px; padding: 0 24px; vertical-align: middle; border: 0; cursor: pointer;-webkit-border-radius: 5px;outline:none;box-shadow:0 0 #0000ff inset, 0px 1px 3px #0000ff;}
.think-form .submit:hover{ background-color: #2F81BF; }
.think-form select{ height: 32px; padding: 3px; border: 1px solid #ccc; }
.think-form .login .text { height: 24px; width: 250px; line-height: 24px; padding: 3px; border: 1px solid #ccc; }
.think-form .login .verify { height: 24px; width: 150px; line-height: 24px; padding: 3px; border: 1px solid #ccc; }
</style>
</head>
<body>
<div>
    <img id="bg" src="tpl/Home/kmdx/common/images/bglogin4.jpg" width="100%" height="100%" style="right: 0; bottom: 0;position: absolute; top: 0; z-index:-100" />
</div>

<div class="contaier wp">

    <div class="think-login">
        <div class="think-form">
            <form action="<?php echo U('Users/checklogin');?>" method="post" class="login">
                <table>
                    <tbody><tr>
                        <th>用户名</th>
                        <td>
                            <input class="text" type="text" name="username">
                        </td>
                    </tr>
                    <tr>
                        <th>密　码</th>
                        <td>
                            <input class="text" type="password" name="password">
                        </td>
                    </tr>

                    <tr>
                        <th>验证码</th>
                        <td>
                        <script>
                        function refreshImg2(){
                        	document.getElementById("txtCheckCode2").src="/index.php?g=Home&m=Index&a=verifyLogin&s="+Math.random();
                        }
                        </script>
                        <input name="verifycode2" type="text" size="8" style="width:80px;" class="text" maxlength="4" />&nbsp;<img src="<?php echo U('Index/verifyLogin');?>" id="txtCheckCode2"/>&nbsp;<a href="javascript:refreshImg2();" style="color:#fff">看不清？换一张</a></td>
                    </tr>

                    <tr>
                        <th>&nbsp;</th>
                        <td style="text-align:center">
                            <input class="submit" type="submit" value="登录">
                        </td>
                    </tr>
                </tbody></table>
            </form>
        </div>
    </div>
</div>

</body>
</html>