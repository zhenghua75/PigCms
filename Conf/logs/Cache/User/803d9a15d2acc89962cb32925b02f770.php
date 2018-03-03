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

<script src="/tpl/static/jquery-1.4.2.min.js"></script>
<script src="/tpl/static/artDialog/jquery.artDialog.js?skin=default"></script>
<script src="/tpl/static/artDialog/plugins/iframeTools.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Common;?>/default_user_com.css" media="all">
<script language="JavaScript">
if (window.top != self){
	window.top.location = self.location;
}
</script>
<script>
function addFee(){
	art.dialog.open('?g=User&m=Alipay&a=add',{lock:true,title:'充值续费',width:600,height:400,yesText:'关闭',background: '#000',opacity: 0.45});
}
function showApiInfo(id,name){
	art.dialog.open('?g=User&m=Index&a=apiInfo&id='+id,{lock:true,title:name+'接口信息',width:830,height:270,yesText:'关闭',background: '#000',opacity: 0.45});
}
function showpreview(token){
  art.dialog.open('?g=User&m=Index&a=preview&token='+token,{lock:true,title:'预览',width:320,height:490,yesText:'关闭',background: '#000',opacity: 0.45});
}
</script>
<div class="content">
<div class="usercontent">

<ul>
  <li>
    <a href="###" class="gold" title="查看资金" >
      <p><strong>账户余额：</strong><?php echo ($thisUser["moneybalance"]); ?></p>
      <p>充值数：<?php echo ($thisUser["money"]); ?></p>
      <p>VIP等级：<?php echo ($thisGroup["name"]); ?></p>
    </a>
  </li>

  <li>
    <a class="addwx" title="添加应用组" onclick="location.href='<?php echo U('Index/add');?>';">添加应用组</a>
  </li>

  <li>
    <a href="index.php?g=User&m=Alipay&a=index" title="会员充值" class="goldbuy">会员充值</a>
  </li>

  <li>
    <div class="qqqun">
      <div class="qt">官方QQ号</div>
      <div class="qt2"><?php echo ($f_qq); ?></div>
    </div>
  </li>
  <li>
    <div class="qqqun1">
        <div class="qt1">到期时间:<?php echo (date("Y-m-d",$viptime)); ?></div>
        <div class="qt3"><a href="###" onclick="addFee()" id="smemberss" class="green"><em>升降级vip续费</em></a></div>
    </div>
  </li>

 <!-- <li class="addli">
    <a class="addbiz" title="添加企业号" onclick="addbiz()">添加企业号</a>
  </li>-->
<script type="text/javascript">
  function addbiz(){
    <?php if(C('open_biz') == 0): ?>alert('请联系站长在后台开启企业号');
    <?php else: ?>
      location.href='<?php echo U('Index/add',array('biz'=>1));?>';<?php endif; ?>
  }
</script>

<div class="clr"></div>
</ul>


        <div class="clr"></div>
      </div>
          <div class="msgWrap">
            <TABLE class="ListProduct" border="0" cellSpacing="0" cellPadding="0" width="100%">
              <THEAD>
                <TR>
                  <TH>分组名称</TH>
                    <TH>移动官网</TH>
                   <TH>微信</TH>
                   <TH>直达号</TH>
                    <TH>支付宝服务窗</TH>
                    <TH>App</TH>
                  <TH>操作</TH>
                </TR>
              </THEAD>
              <TBODY>
                <TR></TR>                
                 <?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><TR>
					  <TD width="76px"><?php echo ($vo["groupname"]); ?> <?php echo ($vo["wxname"]); ?></TD>

                        <TD class="norightborder">　
                            <a href="<?php echo U('Classify/index',array('id'=>$vo['id'],'token'=>$vo['token'],'grouptype'=>'site'));?>" <?php if($vo['tpltypename'] == 'ty_index'): ?>class="btnGray" <?php else: ?>class="btnGreens"<?php endif; ?> >模板设置</a>
                        </TD>
                        <TD class="norightborder">　<a <?php if($vo['wxid'] == '公众号原始id'): ?>class="btnGray" <?php else: ?>class="btnGreens"<?php endif; ?> href="<?php echo U('Index/edit',array('id'=>$vo['id'],'grouptype'=>'weixin')); if($vo["ifbiz"] == 1): ?>&biz=1<?php endif; ?>">公众号</a>　
                        </TD>
                        <TD class="norightborder">　
                            <a href="<?php echo U('Zhida/index',array('id'=>$vo['id'],'token'=>$vo['token'],'grouptype'=>'zhida'));?>" <?php if($vo['status'] == '1'): ?>class="btnGreens" <?php else: ?>class="btnGray"<?php endif; ?> >直达号对接</a>
                        </TD>
                        <TD class="norightborder">　
                            <a href="<?php echo U('Index/edit',array('id'=>$vo['id'],'grouptype'=>'fuwu')); if($vo["ifbiz"] == 1): ?>&biz=1<?php endif; ?>" <?php if($vo['fuwuappid'] == ''): ?>class="btnGray" <?php else: ?>class="btnGreens"<?php endif; ?>>支付宝设置</a>　
                        </TD>
                        <TD class="norightborder">　
                            <a href="<?php echo U('Index/edit',array('id'=>$vo['id'],'grouptype'=>'app')); if($vo["ifbiz"] == 1): ?>&biz=1<?php endif; ?>" <?php if($vo['appname'] == ''): ?>class="btnGray" <?php else: ?>class="btnGreens"<?php endif; ?>>APP对接</a>　
                            <a href="<?php echo U('Classify/index',array('id'=>$vo['id'],'token'=>$vo['token'],'grouptype'=>'app'));?>" <?php if($vo['appname'] == ''): ?>class="btnGray" <?php else: ?>class="btnGreens"<?php endif; ?> >模板设置</a>
                        </TD>
					             <TD class="norightborder">
                        <a <?php if($usertplid == 1): ?>href="<?php echo U('Function/welcome',array('id'=>$vo['id'],'token'=>$vo['token']));?>" <?php else: ?>href="<?php echo U('Function/index',array('id'=>$vo['id'],'token'=>$vo['token']));?>"<?php endif; ?> class="btnGreens">配置</a>
                        <a  href="javascript:drop_confirm('您确定要删除吗?', '<?php echo U('Index/del',array('id'=>$vo['id']));?>');" class="btnBlues">删除</a>
                        <a onclick="showApiInfo(<?php echo ($vo["id"]); ?>,'<?php echo ($vo["wxname"]); ?>')" href="###" class="btnBlues">API接口</a><a onclick="showpreview('<?php echo ($vo["token"]); ?>')" href="###" class="btnGreens">预览</a>
					             </TD>
					</TR><?php endforeach; endif; else: echo "" ;endif; ?>

              </TBODY>
            </TABLE>
            
          </div>
          <br>
          <?php if($demo == 1): ?><div class="alert">
          <p><b>欢迎试用小猪CMS微信多用户营销系统，为了您测试方便，我们已经自动创建了公众号并填充了各类数据，您只需要按照下面步骤操作即可进行测试：</b></p>
          <p>1、<a href="<?php echo U('Index/edit',array('id'=>$wxinfo['id']));?>">点击这里修改您的公众号信息</a></p>
          <p>2、登录您的微信公众平台，按照说明绑定您的微信公众号(<a href="<?php echo U('User/Index/bindHelp',array('id'=>$wxinfo['id'],'token'=>$wxinfo['token']));?>" target="_blank">点击这里查看帮助说明</a>)</p>
          <p>3、如果您需要测试自定义菜单功能，请<a href="<?php echo U('Function/index',array('id'=>$wxinfo['id'],'token'=>$wxinfo['token']));?>">进入功能管理</a>，然后生成自定义菜单，重新关注公众号就会看到自定义菜单了</p>
          <p>就这些，如果碰到任何问题，请您给我们留言，QQ：800022936</p>
          </div><?php endif; ?>
          <div class="cLine">
            <div class="pageNavigator right">
              <div class="pages"><?php echo ($page); ?></div>
            </div>
            <div class="clr"></div>
          </div>
        </div>
        
        <div class="clr"></div>
      </div>
    </div>
  </div>
  <!--底部-->
  	</div>
  	<!--ad start-->
  	<?php if($thisAD): ?><div id="ad1" style="width: 100%; height: 100%; position: fixed; z-index: 1997; top: 0px; left: 0px; overflow: hidden;"><div style="height: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); opacity: 0.65;filter:alpha(opacity=65);">
  	
  	</div></div>
  	<div id="ad2" style="position:fixed; text-align:center; width:100%; top:140px; z-index:30001">
  	<a href="<?php if ($thisAD['url']){ echo ($thisAD["url"]); }else{?>###<?php };?>" target="_blank"><img src="<?php echo ($thisAD["imgs"]); ?>" /></a>
  	</div>
  	<div id="ad3" style="position:fixed; text-align:center; width:100%; top:140px;z-index:30012; background:#f80; opacity:0;filter:alpha(opacity=0);">
  	<div style="height:40px;width:700px;margin:0 auto;z-index:30012;">
  	<div onclick="closeAD()" style="height:45px;width:45px;margin:0 0 0 655px;cursor:pointer;"></div>
  	</div>
  	</div>
  	<script>
  	function closeAD(){
  		$('#ad1').animate({opacity: "hide"}, "slow");
  		$('#ad2').animate({opacity: "hide"}, "slow");
  		$('#ad3').animate({opacity: "hide"}, "slow");
  		$.ajax({url: "/index.php?g=User&m=Index&a=closeAD",dataType: "json"});
  	}
  	</script><?php endif; ?>
  	<!--ad end-->
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