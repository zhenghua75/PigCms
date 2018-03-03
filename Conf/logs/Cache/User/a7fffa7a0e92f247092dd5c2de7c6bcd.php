<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> <?php echo ($f_siteTitle); ?> <?php echo ($f_siteName); ?></title>
<meta name="keywords" content="<?php echo ($f_metaKeyword); ?>" />
<meta name="description" content="<?php echo ($f_metaDes); ?>" />
<meta http-equiv="MSThemeCompatible" content="Yes" />
<script>var SITEURL='';</script>
<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/style_2_common.css?BPm" />
<script src="<?php echo RES;?>/js/common.js" type="text/javascript"></script>
</head>
<body id="nv_member" class="pg_CURMODULE">
<div class="topbg">
<div class="top">
<div class="toplink">
<style>
<?php if($usertplid == 1): ?>.topbg{background:url(<?php echo ($staticPath); ?>/tpl/static/newskin/images/top.gif) repeat-x; height:101px; /*box-shadow:0 0 10px #000;-moz-box-shadow:0 0 10px #000;-webkit-box-shadow:0 0 10px #000;*/}
.top {
    margin: 0px auto; width: 988px; height: 101px;
}

.top .toplink{ height:30px; line-height:27px; color:#999; font-size:12px;}
.top .toplink .welcome{ float:left;}
.top .toplink .memberinfo{ float:right;}
.top .toplink .memberinfo a{ color:#999;}
.top .toplink .memberinfo a:hover{ color:#F90}
.top .toplink .memberinfo a.green{ background:none; color:#0C0}

.top .logo {width: 990px; color: #333; height:70px; font-size:16px;}
.top h1{ font-size:25px;float:left; width:230px; margin:0; padding:0; margin-top:6px; }
.top .navr {WIDTH:750px; float:right; overflow:hidden;}
.top .navr ul{ width:850px;}
.navr li {text-align:center; float: left; height:70px; font-size:1em; width:103px; margin-right:5px;}
.navr li a {width:103px; line-height:70px; float: left; height:100%; color: #333; font-size: 1em; text-decoration:none;}
.navr li a:hover { background:#ebf3e4;}
.navr li.menuon {background:#ebf3e4; display:block; width:103px;}
.navr li.menuon a{color:#333;}
.navr li.menuon a:hover{color:#333;}
.banner{height:200px; text-align:center; border-bottom:2px solid #ddd;}
.hbanner{ background: url(img/h2003070126.jpg) center no-repeat #B4B4B4;}
.gbanner{ background: url(img/h2003070127.jpg) center no-repeat #264C79;}
.jbanner{ background: url(img/h2003070128.jpg) center no-repeat #E2EAD5;}
.dbanner{ background: url(img/h2003070129.jpg) center no-repeat #009ADA;}
.nbanner{ background: url(img/h2003070130.jpg) center no-repeat #ffca22;}
<?php else: ?>

.topbg{BACKGROUND-IMAGE: url(<?php echo ($staticPath); echo ltrim(RES,'.');?>/images/top.png);BACKGROUND-REPEAT: repeat-x; height:124px; box-shadow:0 0 10px #000;-moz-box-shadow:0 0 10px #000;-webkit-box-shadow:0 0 10px #000;}
.top {
	MARGIN: 0px auto; WIDTH: 988px; HEIGHT: 124px;
}

.top .toplink{ height:27px; line-height:27px; color:#999; font-size:12px;}
.top .toplink .welcome{ float:left;}
.top .toplink .memberinfo{ float:right;}
.top .toplink .memberinfo a{ color:#999;}
.top .toplink .memberinfo a:hover{ color:#F90}
.top .toplink .memberinfo a.green{ background:none; color:#0C0}

.top .logo {WIDTH: 990px;COLOR: #333; height:70px;  FONT-SIZE:16px; PADDING-TOP:25px}
.top h1{ font-size:25px; margin-top:20px; float:left; width:230px; margin:0; padding:0;}
.top .navr {WIDTH:750px; float:right; overflow:hidden; margin-top:10px;}
.top .navr ul{ width:850px;}
.navr LI {TEXT-ALIGN:center;FLOAT: left;HEIGHT:32px;FONT-SIZE:1em;width:103px; margin-right:5px;}
.navr LI A {width:103px;TEXT-ALIGN:center; LINE-HEIGHT:30px; FLOAT: left;HEIGHT:32px;COLOR: #333; FONT-SIZE: 1em;TEXT-DECORATION: none
}
.navr LI A:hover {BACKGROUND: url(<?php echo ($staticPath); echo ltrim(RES,'.');?>/images/navhover.gif) center no-repeat;color:#009900;}
.navr LI.menuon {BACKGROUND: url(<?php echo ($staticPath); echo ltrim(RES,'.');?>/images/navon.gif) center no-repeat; display:block;width:103px;HEIGHT:32px;}
.navr LI.menuon a{color:#FFF;}
.navr LI.menuon a:hover{BACKGROUND: url(img/navon.gif) center no-repeat;}
.banner{height:200px; text-align:center; border-bottom:2px solid #ddd;}
.hbanner{ background: url(img/h2003070126.jpg) center no-repeat #B4B4B4;}
.gbanner{ background: url(img/h2003070127.jpg) center no-repeat #264C79;}
.jbanner{ background: url(img/h2003070128.jpg) center no-repeat #E2EAD5;}
.dbanner{ background: url(img/h2003070129.jpg) center no-repeat #009ADA;}
.nbanner{ background: url(img/h2003070130.jpg) center no-repeat #ffca22;}<?php endif; ?>
</style>
<div class="welcome">欢迎使用意路视通平台!</div>
    <div class="memberinfo"  id="destoon_member">	
		<?php if($_SESSION[uid]==false): ?><a href="<?php echo U('Index/login');?>">登录</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="<?php echo U('Index/login');?>">注册</a>
			<?php else: ?>
			你好,<a href="/#" hidefocus="true"  ><span style="color:red"><?php echo (session('uname')); ?></span></a>（uid:<?php echo (session('uid')); ?>）
			<!--<a href="/#" onclick="Javascript:window.open('<?php echo U('System/Admin/logout');?>','_blank')" >退出</a>--><?php endif; ?>	
	</div>
</div>
    <div class="logo">
        <div style="float:left"></div>
            <h1><a href="/" title="意路视通平台"><img src="<?php echo ($f_logo); ?>" height="55" /></a></h1>
            <div class="navr">
        <ul id="topMenu">           
            <li <?php if((ACTION_NAME == 'index') and (GROUP_NAME == 'Home')): ?>class="menuon"<?php endif; ?> ><a href="/">首页</a></li>

                <!--<li <?php if((ACTION_NAME) == "fc"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/fc');?>">功能介绍</a></li>-->
                <!--<li <?php if((ACTION_NAME) == "about"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/about');?>">关于我们</a></li>-->
                <!--<li <?php if((ACTION_NAME) == "price"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/price');?>">资费说明</a></li>-->
                <!--<li <?php if((ACTION_NAME) == "common"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/common');?>">微信导航</a></li>-->
                <li <?php if((ACTION_NAME) == "index"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('User/Index/index');?>">管理中心</a></li>
                <!--<li <?php if((ACTION_NAME) == "help"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/help');?>">帮助中心</a></li>-->
                <li <?php if((ACTION_NAME) == "useredit"): ?>class="menuon"<?php endif; ?>> <a class="secnav_1" href="<?php echo U('Index/useredit');?>">帐户设置</a> </li>
                <li <?php if((ACTION_NAME) == "logout"): ?>class="menuon"<?php endif; ?>> <a href="/#" onclick="Javascript:window.open('<?php echo U('System/Admin/logout');?>','_blank')" >退出</a> </li>
            </ul>
        </div>
        </div>
    </div>
</div>
<div id="aaa"></div>


<div id="mu" class="cl"></div>
</div>
</div>
<div id="aaa"></div>

<div id="wp" class="wp">
    <?php if($usertplid == 0): ?><link href="<?php echo RES;?>/css/style.css?id=103" rel="stylesheet" type="text/css" />
  <?php else: ?>
    <link href="<?php echo RES;?>/css/style-<?php echo ($usertplid); ?>.css?id=103" rel="stylesheet" type="text/css" /><?php endif; ?>
 <div class="contentmanage">
    <div class="developer">
       <div class="appTitle normalTitle">
           <div class="usercontent">
               <h2>管理平台</h2>
           <!--<ul>
               <li>移动官网</li>
               <li>微信公众号</li>
               <li><a href="<?php echo U('Zhida/index',array('token'=>$token));?>">百度直达号</a></li>
               <li><a href="<?php echo U('Zhida/index',array('token'=>$token));?>">支付宝服务窗</a></li>
               <li>App</li>
               </ul>-->
               </div>
        <div class="accountInfo">
        
        </div>
        <div class="clr"></div>
      </div>
      <div class="tableContent">
        <!--左侧功能菜单-->
        <div class="sideBar">
          <div class="catalogList">
            <ul class="<?php if($usertplid != 0): ?>newskin<?php endif; ?>">
            	<!--<li class="subCatalogList"> <a class="secnav_1" href="<?php echo U('Index/useredit');?>">修改密码</a> </li>-->
				<!--<li class=" subCatalogList "> <a class="secnav_2" href="<?php echo U('Index/index');?>">我的公众号</a></li>-->
				<!--<li class=" subCatalogList "> <a class="secnav_3" href="<?php echo U('Index/add');?>">添加公众号</a> </li>-->
				<li class=" subCatalogList "> <a class="secnav_4" href="<?php echo U('Alipay/index');?>">会员充值</a> </li>
				<li class=" subCatalogList "> <a class="secnav_5" href="<?php echo U('Alipay/vip');?>">升降级</a> </li>
				<li class=" subCatalogList "> <a class="secnav_6" href="<?php echo U('Sms/index');?>">短信管理</a> </li>
				<?php if($thisUser['invitecode']): ?><li class=" subCatalogList "> <a class="secnav_7" href="<?php echo U('Index/invite');?>">邀请朋友</a> </li><?php endif; ?>
        <li class=" subCatalogList "> <a class="secnav_8" href="<?php echo U('Index/switchTpl');?>">切换模板</a> </li>
        <!--<li class=" subCatalogList "> <a class="secnav_9" href="<?php if(C('open_biz') == 0): ?>javascript:alert('请联系站长在后台开启企业号');<?php else: echo U('Index/add',array('biz'=>1)); endif; ?>">添加企业号</a> </li>-->
            </ul>
          </div>
        </div>

 <script src="/tpl/static/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="/tpl/static/artDialog/jquery.artDialog.js?skin=default"></script>
<script src="/tpl/static/artDialog/plugins/iframeTools.js"></script>
<script src="/tpl/static/upyun.js?2013"></script>
<div class="content">
          <div class="cLineB"><h4><?php if($_GET['grouptype'] == 'weixin'): ?>添加微信公众号<?php elseif($_GET['grouptype'] == 'fuwu'): ?>支付宝服务窗<?php else: ?>App<?php endif; ?></h4></div>
          <form method="post" action="<?php echo U('User/Index/upsave');?>" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
          <div class="msgWrap">
            <table class="userinfoArea" border="0" cellspacing="0" cellpadding="0" width="100%">
              <thead>
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th><span class="red">*</span>公众号名称：</th>
                  <td><input type="text" required="" class="px" name="wxname" value="<?php echo ($info["wxname"]); ?>" tabindex="1" size="25">
                  </td>
                </tr>
              </thead>
              
              <tbody>
                <?php if($_GET['biz'] != 1): ?><tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th><span class="red">*</span>公众号原始id：</th>
                  <td><input type="text" required="" name="wxid" value="<?php echo ($info["wxid"]); ?>" onmouseup="this.value=this.value.replace('_430','')" class="px" tabindex="1" size="25">　<span class="red">请认真填写，错了不能修改。</span>比如：gh_423dwjkeww3 <a href="/tpl/static/getoid.htm" target="_blank"><img src="<?php echo RES;?>/images/help.png" class="vm helpimg" title="帮助"></a></td>
                </tr>
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th><span class="red">*</span>微信号：</th>
                  <td><input type="text" required="" name="weixin" value="<?php echo ($info["weixin"]); ?>" class="px" tabindex="1" size="25">　比如：lentu123 </td>
                </tr>
                  <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th>头像地址（url）:</th>
                  <td><input class="px" name="headerpic" id="pic" value="<?php echo ($info["headerpic"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('pic',200,200,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('pic')">预览</a></td>
                </tr>
                 
                 <input type="hidden" name="token" value="<?php echo ($info["token"]); ?>" class="px" tabindex="1" size="40">
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th><span class="red"></span>AppID（公众号）：</th>
                  <td><input type="text" name="appid" value="<?php echo ($info["appid"]); ?>" class="px" tabindex="1" size="25">　用于自定义菜单等高级功能，可以不填 </td>
                </tr>
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th><span class="red"></span>AppSecret：</th>
                  <td><input type="text" name="appsecret" value="<?php echo ($info["appsecret"]); ?>" class="px" tabindex="1" size="25">　用于自定义菜单等高级功能，可以不填 </td>
                </tr>
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th><span class="red"></span>消息加密方式：</th>
                  <td><select id="winxintype" name="encode">                  
                  <option value="0" <?php if(($info["encode"]) == "0"): ?>selected<?php endif; ?>>明文模式</option>
                  <option value="1" <?php if(($info["encode"]) == "1"): ?>selected<?php endif; ?>>兼容模式</option>
                  <option value="2" <?php if(($info["encode"]) == "2"): ?>selected<?php endif; ?>>安全模式</option>
                  </select>　 </td>
                </tr>
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th><span class="red"></span>AesEncodingKey：</th>
                  <td><input type="text" name="aeskey" value="<?php echo ($info["aeskey"]); ?>" class="px" tabindex="1" size="55">　加密消息的AesEncodingKey </td>
                </tr>
                
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th><span class="red"></span>微信号类型：</th>
                  <td><select id="winxintype" name="winxintype">                  
                  <option value="1" <?php if(($info["winxintype"]) == "1"): ?>selected<?php endif; ?>>订阅号</option>
                  <option value="2" <?php if(($info["winxintype"]) == "2"): ?>selected<?php endif; ?>>服务号</option>
                  <option value="3" <?php if(($info["winxintype"]) == "3"): ?>selected<?php endif; ?>>认证服务号</option>
                  </select>　认证服务号是指每年向微信官方交300元认证费的公众号 </td>
                </tr>
                
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th><span class="red">*</span>地区</th>
                  <td>
                  省：<input type="text" class="px" id="province" value="<?php echo ($info["province"]); ?>" name="province" tabindex="1" size="20">  市：<input id="city" value="<?php echo ($info["city"]); ?>" type="text" name="city" class="px" tabindex="1" size="20">
               （此处关联公交等本地化查询）
                  </td>
                </tr>
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th>公众号邮箱：</th>
                  <td><input type="text" required="" class="px" tabindex="1" value="<?php echo ($info["qq"]); ?>" name="qq" size="25"></td>
                </tr>
                 <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th>粉丝数：</th>
                  <td><input type="text" required="" name="wxfans" value="<?php echo ($info["wxfans"]); ?>" class="px" tabindex="1" size="25"></td>
                </tr>
             
                <tr <?php if($_GET['grouptype'] != 'weixin'): ?>style="display:none"<?php endif; ?>>
                  <th>类型：</th>
                  <td><select id="type" name="type">
                  <option value="1,情感" <?php if(($info["typeid"]) == "1"): ?>selected<?php endif; ?> >情感</option>
                  <option value="2,数码" <?php if(($info["typeid"]) == "2"): ?>selected<?php endif; ?> >数码</option>
                  <option value="3,娱乐" <?php if(($info["typeid"]) == "3"): ?>selected<?php endif; ?> >娱乐</option>
                  <option value="4,IT" <?php if(($info["typeid"]) == "4"): ?>selected<?php endif; ?> >IT</option>
                  <option value="5,数码" <?php if(($info["typeid"]) == "5"): ?>selected<?php endif; ?> >数码</option>
                  <option value="6,购物" <?php if(($info["typeid"]) == "6"): ?>selected<?php endif; ?> >购物</option>
                  <option value="7,生活" <?php if(($info["typeid"]) == "7"): ?>selected<?php endif; ?> >生活</option>
                  <option value="8,服务" <?php if(($info["typeid"]) == "8"): ?>selected<?php endif; ?> >服务</option>
                  </select></td>
                </tr>
                
                <tr>
                    <td colspan="2"><br /></td>
                </tr>


                <tr <?php if($_GET['grouptype'] != 'fuwu'): ?>style="display:none"<?php endif; ?>>
                    <th>AppID（服务窗）:</th>
                    <td><input type="text" name="fuwuappid" value="<?php echo ($info["fuwuappid"]); ?>" class="px" size="25" />　用于支付宝服务窗，可以不填</td>
                </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>应用名称:</th>
                    <td><input type="text" name="appname" value="<?php echo ($info["appname"]); ?>" class="px" size="25" /></td>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>应用图标（url）:</th>
                    <td><input class="px" name="appicon" id="appicon" value="<?php echo ($info["appicon"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('appicon',114,114,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('appicon')">预览</a>
                        png格式; 建议尺寸192x192px
                        APP安装到手机上，显示在手机桌面的图标
                    </td>
                    </tr>
                   <!-- <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>开机画面（url）:</th>
                    <td><input class="px" name="apploading" id="apploading" value="<?php echo ($info["apploading"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploading',720,1280,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploading')">预览</a>
                        9-Patch PNG格式; 建议尺寸720x1280px
                        点击APP图标打开APP，看到的第一张图片
                    </td>
                    </tr>-->
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <td></td>
                    <th>安卓开机画面，点击APP图标打开APP，看到的第一张图片。</th>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>PNG格式; 尺寸200x320</th>
                    <td><input class="px" name="apploadingSmall" id="apploadingSmall" value="<?php echo ($info["apploadingSmall"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploadingSmall',200,320,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploadingSmall')">预览</a>
                        
                    </td>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>PNG格式; 尺寸320x480</th>
                    <td><input class="px" name="apploadingMedium" id="apploadingMedium" value="<?php echo ($info["apploadingMedium"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploadingMedium',320,480,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploadingMedium')">预览</a>
                        
                    </td>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>PNG格式; 尺寸480x800</th>
                    <td><input class="px" name="apploadingLarge" id="apploadingLarge" value="<?php echo ($info["apploadingLarge"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploadingLarge',480,800,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploadingLarge')">预览</a>
                        
                    </td>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>PNG格式; 尺寸720x1280</th>
                    <td><input class="px" name="apploadingXlarge" id="apploadingXlarge" value="<?php echo ($info["apploadingXlarge"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploadingXlarge',720,1280,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploadingXlarge')">预览</a>
                        
                    </td>
                    </tr>
                    
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <td></td>
                    <th>苹果开机画面，点击APP图标打开APP，看到的第一张图片。</th>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>PNG格式; 尺寸320x480</th>
                    <td><input class="px" name="apploadingDefault" id="apploadingDefault" value="<?php echo ($info["apploadingDefault"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploadingDefault',320,480,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploadingDefault')">预览</a>
                        
                    </td>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>PNG格式; 尺寸640x960</th>
                    <td><input class="px" name="apploadingDefault2x" id="apploadingDefault2x" value="<?php echo ($info["apploadingDefault2x"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploadingDefault2x',640,960,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploadingDefault2x')">预览</a>
                        
                    </td>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>PNG格式; 尺寸640x1136</th>
                    <td><input class="px" name="apploadingDefault568h2x" id="apploadingDefault568h2x" value="<?php echo ($info["apploadingDefault568h2x"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploadingDefault568h2x',640,1136,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploadingDefault568h2x')">预览</a>
                        
                    </td>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>PNG格式; 尺寸750x1334</th>
                    <td><input class="px" name="apploadingDefault667h" id="apploadingDefault667h" value="<?php echo ($info["apploadingDefault667h"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploadingDefault667h',750,1334,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploadingDefault667h')">预览</a>
                        
                    </td>
                    </tr>
                    <tr <?php if($_GET['grouptype'] != 'app'): ?>style="display:none"<?php endif; ?>>
                    <th>PNG格式; 尺寸1242x2208</th>
                    <td><input class="px" name="apploadingDefault736h" id="apploadingDefault736h" value="<?php echo ($info["apploadingDefault736h"]); ?>" size="60">    <script src="/tpl/static/upyun.js?2013"></script><a href="###" onclick="upyunPicUpload('apploadingDefault736h',1242,2208,'<?php echo ($token); ?>')" class="a_upload">上传</a> <a href="###" onclick="viewImg('apploadingDefault736h')">预览</a>
                        
                    </td>
                    </tr><?php endif; ?>
                <tr>
                  <th></th>
                  <td><button type="submit" class="btnGreen" id="saveSetting">保存</button></td>
                </tr>
              </tbody>
            </table>
            
          </div>
          </form>
        </div>
        
        <div class="clr"></div>
      </div>
    </div>
  </div>
  <!--底部-->
  	</div>
</div>
</div>
</div>

<style>
.IndexFoot {
	BACKGROUND-COLOR: #333; WIDTH: 100%; HEIGHT: 39px
}
.foot{ width:988px; margin:0px auto; font-size:12px; line-height:39px;}
.foot .foot_page{ float:left; width:600px;color:white;}
.foot .foot_page a{ color:white; text-decoration:none;}
#copyright{ float:right; width:380px; text-align:right; font-size:12px; color:#FFF;}
</style>
<div class="IndexFoot" style="height:120px;clear:both">
<div class="foot" style="padding-top:20px;">
<div class="foot_page" >
<a href="<?php echo ($f_siteUrl); ?>"><?php echo ($f_siteName); ?></a><br/>
帮助您快速搭建属于自己的营销平台,构建自己的客户群体。
</div>
<div id="copyright" style="color:white;">
	<?php echo ($f_siteName); ?>(c)版权所有 <a href="http://www.miibeian.gov.cn" target="_blank" style="color:white"><?php echo C('ipc');?></a><br/>
	技术支持：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo ($f_qq); ?>&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo ($f_qq); ?>:51" alt="联系我吧" title="联系我吧"/></a>

</div>
    </div>
</div>
<!-- 帮助悬浮开始 -->
<?php $data_g=GROUP_NAME; $textHelp=1; if (C('server_topdomain')=='pigcms.cn'){ $textHelp=0; }else{ $users=M('Users')->where(array('id'=>$_SESSION['uid']))->find(); if($users['sysuser']){ $textHelp=0; }else{ if(C('close_help')){ $data_g='notingh'; } } } $data = array( 'key' => C('server_key'), 'domain' => C('server_topdomain'), 'is_text' => $textHelp, 'data_g' => $data_g, 'data_m' => MODULE_NAME, 'data_a' => ACTION_NAME ); if(function_exists('curl_init')){ $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, 'http://up.pigcms.cn/oa/admin.php?m=help&c=view&a=get_list&'.http_build_query($data)); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); curl_setopt($ch, CURLOPT_HEADER, 0); $content = curl_exec($ch);curl_close($ch); }else{ $content = file_get_contents('http://up.pigcms.cn/oa/admin.php?m=help&c=view&a=get_list&'.http_build_query($data)); } $content = json_decode($content,true); ?>
<?php if(!empty($content)): ?><link href="<?php echo ($staticPath); ?>/tpl/static/help_xuanfu/css/zuoce.css" type="text/css" rel="stylesheet"/>
	<div class="zuoce zuoce_clear">
		<div id="Layer1">
			<a href="javascript:"><img src="<?php echo ($staticPath); ?>/tpl/static/help_xuanfu/images/ou_03.png"/></a>
		</div>
		<div id="Layer2" style="display:none;">
			<p class="xiangGuan zuoce_clear">相关帮助</p>
			<?php if(is_array($content)): $i = 0; $__LIST__ = $content;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><p class="lianjie zuoce_clear"><a href="javascript:openwin('/index.php?g=User&m=Index&a=help&helpParm=/oa/admin_help_<?php echo ($list['id']); ?>.html',768,960)" <?php if($list['is_video'] == 1): ?>class="video"<?php else: ?>class="writing"<?php endif; ?>><?php echo ($list["title"]); ?></a></p><?php endforeach; endif; else: echo "" ;endif; ?>
			<!--p class="anNiuo clear"><a href="#">进入帮助中心</a></p-->
			<p class="anNiut zuoce_clear"><a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo C('site_qq');?>&site=qq&menu=yes" target="_blank">在线客服</a></p>
		</div>
	</div>
	<script type="text/javascript">
		window.onload = function(){
			var oDiv1 = document.getElementById('Layer1');
			var oDiv2 = document.getElementById('Layer2');
			oDiv1.onclick = function(){
				oDiv2.style.display = oDiv2.style.display == 'block' ? 'none' : 'block';
			}
		}
		function openwin(url,iHeight,iWidth){
			var iTop = (window.screen.availHeight-30-iHeight)/2,iLeft = (window.screen.availWidth-10-iWidth)/2;
			window.open(url, "newwindow", "height="+iHeight+", width="+iWidth+", toolbar=no, menubar=no,top="+iTop+",left="+iLeft+",scrollbars=yes, resizable=no, location=no, status=no");
		}
	</script><?php endif; ?>
<!-- 帮助悬浮结束 -->
<div style="display:none">
<?php echo ($alert); ?> 
<?php echo base64_decode(C('countsz'));?>
<!-- <script src="http://s15.cnzz.com/stat.php?id=5524076&web_id=5524076" language="JavaScript"></script>
 --></div>

</body>

<?php if(MODULE_NAME == 'Function' && ACTION_NAME == 'welcome'){ ?>
<script src="<?php echo ($staticPath); ?>/tpl/static/myChart/js/echarts-plain.js"></script>

<script type="text/javascript">


    var myChart = echarts.init(document.getElementById('main')); 
   

    var option = {
        title : {
            text: '<?php if($charts["ifnull"] == 1): ?>本月关注和文本请求数据统计示例图(您暂时没有数据)<?php else: ?>本月关注和文本请求数据统计<?php endif; ?>',
            x:'left'
        },
        tooltip : {
            trigger: 'axis'
        },
        legend: {
            data:['文本请求数','关注数'],
            x: 'right'
        },
        toolbox: {
            show : false,
            feature : {
                mark : {show: false},
                dataView : {show: false, readOnly: false},
                magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                restore : {show: false} ,
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        xAxis : [
            {
                type : 'category',
                boundaryGap : false,
                data : [<?php echo ($charts["xAxis"]); ?>]
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'文本请求数',
                type:'line',
                tiled: '总量',
                data: [<?php echo ($charts["text"]); ?>]
            },    
            {
                "name":'关注数',
                "type":'line',
                "tiled": '总量',
                data:[<?php echo ($charts["follow"]); ?>]
            }
       

        ]

    };                   

    myChart.setOption(option); 


    var myChart2 = echarts.init(document.getElementById('pieMain')); 
               
    var option2 = {
        title : {
            text: '<?php if($pie["ifnull"] == 1): ?>7日内粉丝行为分析示例图(您暂时没有数据)<?php else: ?>7日内粉丝行为分析<?php endif; ?>',
            x:'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        toolbox: {
            show : false,
            feature : {
                mark : {show: true},
                dataView : {show: true, readOnly: false},
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        series : [
            {
                name:'粉丝行为统计',
                type:'pie',
                radius : ['50%', '70%'],
                itemStyle : {
                    normal : {
                        label : {
                            show : false
                        },
                        labelLine : {
                            show : false
                        }
                    },
                    emphasis : {
                        label : {
                            show : true,
                            position : 'center',
                            textStyle : {
                                fontSize : '18',
                                fontWeight : 'bold'
                            }
                        }
                    }
                },
                data:[ 
                    <?php echo ($pie["series"]); ?>
                
                ]
            }
        ]
    };
     myChart2.setOption(option2,true); 


    var myChart3 = echarts.init(document.getElementById('pieMain2')); 
    var option3 = {
        title : {
            text: '<?php if($sex_series["ifnull"] == 1): ?>会员性别统计示例图(您暂时没有数据)<?php else: ?>会员性别统计<?php endif; ?>',
            x:'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        toolbox: {
            show : false,
            feature : {
                mark : {show: true},
                dataView : {show: true, readOnly: false},
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        series : [
            {
                name:'会员性别统计',
                type:'pie',
                radius : ['50%', '70%'],
                itemStyle : {
                    normal : {
                        label : {
                            show : false
                        },
                        labelLine : {
                            show : false
                        }
                    },
                    emphasis : {
                        label : {
                            show : true,
                            position : 'center',
                            textStyle : {
                                fontSize : '18',
                                fontWeight : 'bold'
                            }
                        }
                    }
                },
                data:[
                  <?php echo ($sex_series['series']); ?>
                ]
            }
        ]
    };                     

  myChart3.setOption(option3,true); 



    </script>
<?php } ?>
</html>