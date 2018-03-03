<?php

class WapAction extends BaseAction
{
	public $token;
	public $wecha_id;
	public $fans;
	public $homeInfo;
	public $bottomeMenus;
	public $wxuser;
	public $user;
	public $group;
	public $company;
	public $shareScript;
	public $sign;
	private $_appid;
	private $_secret;
	private $_redirect_uri;
	public $owxuser;

	protected function _initialize()
	{
		parent::_initialize();
		$this->_appid = C("appid");
		$this->_secret = C("secret");
		$this->time = 0;
		$this->token = $this->_get("token");
		if (strlen($this->token)) {
			$_SESSION["wap_token"] = $this->token;
		}

		if (!$this->token) {
			$this->token = $_SESSION["wap_token"];
		}

		if (!$_SESSION["wap_token"]) {
			$this->token = $_SESSION["wap_token"];
		}

		if (!$this->token && !strpos(MODULE_NAME, "Drp") === false) {
			$id = $this->_get("id");

			if ($id) {
				$did = M("Distributor_store")->where(array("id" => $id))->getField("did");
				$this->token = M("Distributor")->where(array("id" => $did))->getField("token");
			}
		}

		$this->assign("token", $this->token);

		if (!$this->token) {
			exit("no token");
		}

		if ($this->token && !preg_match("/^[0-9a-zA-Z]{3,42}$/", $this->token)) {
			exit("error token");
		}

		$this->wxuser = S("wxuser_" . $this->token);
		if (!$this->wxuser || 1) {
			$this->wxuser = D("Wxuser")->where(array("token" => $this->token))->find();
			S("wxuser_" . $this->token, $this->wxuser);
		}

		$this->owxuser = $this->wxuser;
		$this->assign("wxuser", $this->wxuser);
		$fake = 0;
		if ((($this->wxuser["winxintype"] != 3) || ($this->wxuser["oauth"] == 0)) && $this->_appid && $this->_secret) {
			if (!$this->isAgent) {
				$this->wxuser["appid"] = trim($this->_appid);
				$this->wxuser["appsecret"] = trim($this->_secret);
			}
			else {
				$this->wxuser["appid"] = $this->thisAgent["appid"];
				$this->wxuser["appsecret"] = $this->thisAgent["appsecret"];
			}

			$fake = 1;
		}

		$toAuth = 0;
		if ((C("server_topdomain") == "pigcms.cn") && (C("site_url") != "http://demo2.pigcms.cn")) {
			$toAuth = 1;
		}
		else {
			$toAuth = $this->wxuser["oauth"];
		}

		if ((C("server_topdomain") == "pigcms.cn") && ($this->wxuser["winxintype"] < 3)) {
			$this->wxuser["appid"] = $this->_appid;
			$this->wxuser["appsecret"] = $this->_secret;
			$this->wxuser["oauth"] = 0;
			$this->wxuser["is_domain"] = 1;
			$fake = 1;
		}

		$session_openid_name = "token_openid_" . $this->token;
		$session_fakeopenid_name = "token_fakeopenid_" . $this->token;
		$session_reopenid_name = "token_reopenid_" . $this->token;
		$session_oauthed_name = "token_oauthed_" . $this->token;
		$session_username_name = "token_username_" . $this->token;
		$getUserInfoModules = getUserInfoModule::index();
		$getUserinfo = 0;
		if ($_GET["rget"] || intval($_GET["ali"])) {
			$_SESSION["otherSource"] = 1;
			$toAuth = 0;
			$this->wxuser["oauthinfo"] = 0;
		}

		if ($_SESSION["otherSource"]) {
			$toAuth = 0;
		}

		if ($this->wxuser["oauthinfo"] && !$_SESSION[$session_oauthed_name]) {
			if ($_SESSION[$session_openid_name]) {
				$fansInfo = M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $_SESSION[$session_openid_name]))->find();

				if ($toAuth) {
					if (!$fansInfo || !$fansInfo["wechaname"] || !$fansInfo["portrait"]) {
						unset($_SESSION[$session_openid_name]);
						$getUserinfo = 1;
					}
				}
			}
			else {
				if ($_SESSION[$session_reopenid_name] && $_SESSION[$session_reopenid_name]) {
					$fansInfo = M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $_SESSION[$session_reopenid_name]))->find();
					if (!$fansInfo || !$fansInfo["wechaname"] || !$fansInfo["portrait"]) {
						unset($_SESSION[$session_openid_name]);
						unset($_SESSION[$session_reopenid_name]);
						$getUserinfo = 1;
					}
				}
				else {
					$getUserinfo = 1;
				}
			}
		}

		$this->isFuwu = 0;
		$this->isWechat = 0;
		$userAgent = strtolower($_SERVER["HTTP_USER_AGENT"]);

		if (strpos($userAgent, "alipayclient")) {
			$this->isFuwu = 1;
		}
		else if (strpos($userAgent, "micromessenger")) {
			$this->isWechat = 1;
		}

		if (!M("Weixin_account")->where(array("type" => 1))->find()) {
			M("Wxuser")->where("1")->save(array("type" => 0));
		}

		if (!$_SESSION[$session_openid_name] || !$_SESSION[$session_openid_name]) {
			if ($this->isFuwu) {
				if ((!$_GET["wecha_id"] || (urldecode($_GET["wecha_id"]) == "{wechat_id}")) && ($_GET["wecha_id"] != "no") && ($toAuth == 1)) {
					$this->wecha_id = FuwuOAuth::index($this->token);
				}
				else {
					$this->wecha_id = $_GET["wecha_id"];
				}

				$_SESSION[$session_openid_name] = $this->wecha_id;
				$_SESSION[$session_oauthed_name] = 1;
			}
			elseif($this->isWechat) {
				$apiOauth = new apiOauth();
				if ((!$_GET["wecha_id"] || (urldecode($_GET["wecha_id"]) == "{wechat_id}")) && ($_GET["wecha_id"] != "no") && $this->wxuser["appid"] && ((($this->wxuser["type"] == 0) && ($this->wxuser["appsecret"] != "")) || ($this->wxuser["type"] == 1)) && ($toAuth == 1)) {
					$token_info = $apiOauth->webOauth($this->wxuser);
				}
				if ($token_info) {
					$this->wecha_id = $token_info["openid"];

					if ($fake) {
						if ($_SESSION[$session_fakeopenid_name]) {
							$this->wecha_id = $_SESSION[$session_fakeopenid_name];
						}
						else {
							$fansInfo = M("Userinfo")->where(array("token" => $this->token, "fakeopenid" => $openid))->find();

							if ($fansInfo) {
								$this->wecha_id = $fansInfo["wecha_id"];
							}
						}
					}

					if ($this->wxuser["oauthinfo"]) {
						$jsonui = $apiOauth->get_fans_info($token_info["access_token"], $token_info["openid"]);
						if ($jsonui["openid"] && $jsonui["openid"]) {
							if ($fansInfo) {
								$exist = $fansInfo;
							}
							else {
								$exist = M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->getField("id");
							}

							$datainfo["wechaname"] = str_replace(array("'", "\\"), array(""), $jsonui["nickname"]);
							$datainfo["sex"] = $jsonui["sex"];
							$datainfo["portrait"] = $jsonui["headimgurl"];
							$datainfo["token"] = $this->token;
							$datainfo["wecha_id"] = $jsonui["openid"];
							$datainfo["city"] = $jsonui["city"];
							$datainfo["province"] = $jsonui["province"];

							if ($fake) {
								$datainfo["wecha_id"] = $this->wecha_id;
								$datainfo["fakeopenid"] = $jsonui["openid"];
							}

							if ($exist) {
								$datainfo["truename"] = $datainfo["wechaname"];
								M("Userinfo")->where(array("id" => $exist))->save($datainfo);
							}
							else {
								$datainfo["truename"] = $datainfo["wechaname"];
								M("Userinfo")->add($datainfo);
							}
						}
						else {
							$this->error("授权不对哦<br>" . $this->wxuser["appid"] . "<br>" . $this->wxuser["appsecret"] . "<br>" . $jsonui["errcode"], "#");
							exit();
						}
					}

					$_SESSION[$session_openid_name] = $this->wecha_id;
					$_SESSION[$session_oauthed_name] = 1;
				}
				else {
					$this->wecha_id = $this->_get("wecha_id");
					if ($fake && $toAuth && !$_GET["isappinstalled"]) {
						$_SESSION[$session_fakeopenid_name] = $this->wecha_id;
					}

					if (!$toAuth) {
						$_SESSION[$session_openid_name] = $this->wecha_id;
					}

					if ($_GET["wecha_id"] && strlen($_GET["wecha_id"]) && $toAuth) {
						$get_parms = $_GET;
						unset($get_parms["wecha_id"]);
						$get_parm_str = "";

						if ($get_parms) {
							$comma = "";

							foreach ($get_parms as $gpk => $gpv ) {
								$get_parm_str .= $comma . $gpk . "=" . $gpv;
								$comma = "&";
							}
						}

						$get_parm_str .= "&g=" . GROUP_NAME . "&m=" . MODULE_NAME . "&a=" . ACTION_NAME;
						$_SESSION[$session_reopenid_name] = $this->wecha_id;
						header("Location:" . $this->siteUrl . "/index.php?" . $get_parm_str);
						exit();
					}
				}
			}else{
				if($_SESSION[$session_openid_name]){
					$userinfoaot = M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $_SESSION[$session_openid_name]))->find();
					if($userinfoaot){
						$this->wecha_id = $_SESSION[$session_openid_name];
					}else{
						unset($_SESSION[$session_openid_name]);
						unset($_SESSION[$session_username_name]);
						unset($_SESSION[$session_fakeopenid_name]);
						unset($_SESSION[$session_reopenid_name]);
						unset($_SESSION[$session_oauthed_name]);
					}
				}
			}
		}
		else {
			$this->wecha_id = $_SESSION[$session_openid_name];
		}

		if ($this->wecha_id && !preg_match("/^[0-9a-zA-Z_\-\s]{3,82}$/", $this->wecha_id)) {
			exit("error openid:" . $this->wecha_id);
		}

		if (!$this->wecha_id) {
			$this->wecha_id = $this->_get("wecha_id");
		}

		$this->assign("wecha_id", $this->wecha_id);
		$fansInfo = S("fans_" . $this->token . "_" . $this->wecha_id);

		if (!$fansInfo) {
			$fansInfo = M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->find();
		}

		$advanceInfo = M("Wechat_group_list")->where(array("token" => $this->token, "openid" => $this->wecha_id))->find();

		if ($advanceInfo) {
			$fansInfo["nickname"] = $advanceInfo["nickname"];

			if (!$fansInfo["wechaname"]) {
				$fansInfo["wechaname"] = $advanceInfo["nickname"];
			}

			$fansInfo["sex"] = $advanceInfo["sex"];
			$fansInfo["province"] = $advanceInfo["province"];
			$fansInfo["city"] = $advanceInfo["city"];
		}

		S("fans_" . $this->token . "_" . $this->wecha_id, $fansInfo);
		$this->fans = $fansInfo;
		$this->assign("fans", $fansInfo);
		$homeInfo = S("homeinfo_" . $this->token);
		if (!$homeInfo || 1) {
			$homeInfo = M("home")->where(array("token" => $this->token))->find();
			S("homeinfo_" . $this->token, $homeInfo);
		}

		$this->homeInfo = $homeInfo;
		$this->assign("homeInfo", $this->homeInfo);
		$catemenu = S("bottomMenus_" . $this->token);
		if (!$catemenu || 1) {
			$catemenu_db = M("catemenu");
			$catemenu = $catemenu_db->where(array("token" => $this->token, "status" => 1))->order("orderss desc")->select();
			S("bottomMenus_" . $this->token, $catemenu);
		}

		$menures = array();

		if ($catemenu) {
			$res = array();
			$rescount = 0;

			foreach ($catemenu as $val ) {
				$val["url"] = $this->getLink($val["url"]);
				$res[$val["id"]] = $val;

				if ($val["fid"] == 0) {
					$val["vo"] = array();
					$menures[$val["id"]] = $val;
					$menures[$val["id"]]["k"] = $rescount;
					$rescount++;
				}
			}

			foreach ($catemenu as $val ) {
				$val["url"] = $this->getLink($val["url"]);

				if (0 < $val["fid"]) {
					array_push($menures[$val["fid"]]["vo"], $val);
				}
			}
		}

		$catemenu = $menures;
		$this->bottomeMenus = $catemenu;
		$this->assign("catemenu", $this->bottomeMenus);
		$radiogroup = $homeInfo["radiogroup"];

		if ($radiogroup == false) {
			$radiogroup = 0;
		}

		$cateMenuFileName = "tpl/Wap/default/Index_menuStyle" . $radiogroup . ".html";
		$this->assign("cateMenuFileName", $cateMenuFileName);
		$this->assign("radiogroup", $radiogroup);
		$this->user = S("user_" . $this->wxuser["uid"]);
		if (!$this->user || 1) {
			$this->user = D("Users")->find(intval($this->wxuser["uid"]));
			S("user_" . $this->wxuser["uid"], $this->user);
		}

		$this->assign("user", $this->user);
		$this->group = S("group_" . $this->user["gid"]);
		if (!$this->group || 1) {
			$this->group = M("User_group")->where(array("id" => intval($this->user["gid"])))->find();
			S("group_" . $this->user["gid"], $this->group);
		}

		$this->assign("group", $this->group);
		$this->company = S("company_" . $this->token);
		if (!$this->company || 1) {
			$company_db = M("company");
			$this->company = $company_db->where(array("token" => $this->token, "isbranch" => 0))->find();
			S("company_" . $this->token, $this->company);
		}

		$this->assign("company", $this->company);
		$this->copyright = $this->group["iscopyright"];
		$this->assign("iscopyright", $this->copyright);
		$this->assign("siteCopyright", C("copyright"));
		$this->assign("copyright", $this->copyright);
		$share = new WechatShare($this->wxuser, $this->wecha_id);
		$this->shareScript = $share->getSgin();
		$this->assign("shareScript", $this->shareScript);

		$session_username_name = "token_username_" . $this->token;
		$arrmodulename=array("Card","Store","Forum","RentCar");
		$arrlogincontrol['Card']=array("index");
		$arrlogincontrol['Store']=array("index");
		$arrlogincontrol['Forum']=array("add","myMessage","commentAdd");
		$arrlogincontrol['RentCar']=array("goCart");
		if(!$_SESSION[$session_username_name]){
			if(GROUP_NAME=='Wap' && in_array(MODULE_NAME, $arrmodulename) && in_array(ACTION_NAME, $arrlogincontrol[MODULE_NAME])){
				session('R' , $_SERVER['REQUEST_URI']);
				if($this->isFuwu||$this->isWechat){
					$reginfo = M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->find();
					if($reginfo && $reginfo['username']!=''){
						$_SESSION[$session_username_name]=$reginfo['username'];
					}else{
						$this->assign('nonregist',0);
						$this->redirect(U("Index/memberReg", array("token" => $this->token)));
					}
				}else{
					$this->assign('nonregist',0);
					$this->redirect(U("Index/memberLogin", array("token" => $this->token)));
				}
			}elseif($this->token=='azixvv1429697429'){
				$this->assign("nonregist",1);
				if(GROUP_NAME=='Wap' && MODULE_NAME=='Index' && ACTION_NAME!='memberLogin'){
					session('R' , $_SERVER['REQUEST_URI']);
					$this->redirect(U("Index/memberLogin", array("token" => $this->token)));
				}
			}
		}elseif(GROUP_NAME=='Wap' && in_array(MODULE_NAME, $arrmodulename) && in_array(ACTION_NAME, $arrlogincontrol[MODULE_NAME])){
			session('R' , $_SERVER['REQUEST_URI']);
			if($this->isFuwu||$this->isWechat){
				$reginfo = M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->find();
				if($reginfo && $reginfo['username']!=$_SESSION[$session_username_name]){
					unset($_SESSION[$session_username_name]);
					$this->assign('nonregist',0);
					$this->redirect(U("Index/memberReg", array("token" => $this->token)));
				}
			}
		}
	}

	public function getLink($url)
	{
		$url = ($url ? $url : "javascript:void(0)");
		$urlArr = explode(" ", $url);
		$urlInfoCount = count($urlArr);

		if (1 < $urlInfoCount) {
			$itemid = intval($urlArr[1]);
		}

		if ($this->strExists($url, "刮刮卡")) {
			$link = "/index.php?g=Wap&m=Guajiang&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id;

			if ($itemid) {
				$link .= "&id=" . $itemid;
			}
		}
		else if ($this->strExists($url, "大转盘")) {
			$link = "/index.php?g=Wap&m=Lottery&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id;

			if ($itemid) {
				$link .= "&id=" . $itemid;
			}
		}
		else if ($this->strExists($url, "优惠券")) {
			$link = "/index.php?g=Wap&m=Coupon&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id;

			if ($itemid) {
				$link .= "&id=" . $itemid;
			}
		}
		else if ($this->strExists($url, "刮刮卡")) {
			$link = "/index.php?g=Wap&m=Guajiang&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id;

			if ($itemid) {
				$link .= "&id=" . $itemid;
			}
		}
		else if ($this->strExists($url, "商家订单")) {
			if ($itemid) {
				$link = $link = "/index.php?g=Wap&m=Host&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id . "&hid=" . $itemid;
			}
			else {
				$link = "/index.php?g=Wap&m=Host&a=Detail&token=" . $this->token . "&wecha_id=" . $this->wecha_id;
			}
		}
		else if ($this->strExists($url, "万能表单")) {
			if ($itemid) {
				$link = $link = "/index.php?g=Wap&m=Selfform&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id . "&id=" . $itemid;
			}
		}
		else if ($this->strExists($url, "相册")) {
			$link = "/index.php?g=Wap&m=Photo&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id;

			if ($itemid) {
				$link = "/index.php?g=Wap&m=Photo&a=plist&token=" . $this->token . "&wecha_id=" . $this->wecha_id . "&id=" . $itemid;
			}
		}
		else if ($this->strExists($url, "全景")) {
			$link = "/index.php?g=Wap&m=Panorama&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id;

			if ($itemid) {
				$link = "/index.php?g=Wap&m=Panorama&a=item&token=" . $this->token . "&wecha_id=" . $this->wecha_id . "&id=" . $itemid;
			}
		}
		else if ($this->strExists($url, "会员卡")) {
			$link = "/index.php?g=Wap&m=Card&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id;
		}
		else if ($this->strExists($url, "商城")) {
			$link = "/index.php?g=Wap&m=Product&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id;
		}
		else if ($this->strExists($url, "订餐")) {
			$link = "/index.php?g=Wap&m=Product&a=dining&dining=1&token=" . $this->token . "&wecha_id=" . $this->wecha_id;
		}
		else if ($this->strExists($url, "团购")) {
			$link = "/index.php?g=Wap&m=Groupon&a=grouponIndex&token=" . $this->token . "&wecha_id=" . $this->wecha_id;
		}
		else if ($this->strExists($url, "首页")) {
			$link = "/index.php?g=Wap&m=Index&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id;
		}
		else if ($this->strExists($url, "网站分类")) {
			$link = "/index.php?g=Wap&m=Index&a=lists&token=" . $this->token . "&wecha_id=" . $this->wecha_id;

			if ($itemid) {
				$link = "/index.php?g=Wap&m=Index&a=lists&token=" . $this->token . "&wecha_id=" . $this->wecha_id . "&classid=" . $itemid;
			}
		}
		else if ($this->strExists($url, "图文回复")) {
			if ($itemid) {
				$link = "/index.php?g=Wap&m=Index&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id . "&id=" . $itemid;
			}
		}
		else if ($this->strExists($url, "LBS信息")) {
			$link = "/index.php?g=Wap&m=Company&a=map&token=" . $this->token . "&wecha_id=" . $this->wecha_id;

			if ($itemid) {
				$link = "/index.php?g=Wap&m=Company&a=map&token=" . $this->token . "&wecha_id=" . $this->wecha_id . "&companyid=" . $itemid;
			}
		}
		else if ($this->strExists($url, "DIY宣传页")) {
			$link = "/index.php/show/" . $this->token;
		}
		else if ($this->strExists($url, "婚庆喜帖")) {
			if ($itemid) {
				$link = "/index.php?g=Wap&m=Wedding&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id . "&id=" . $itemid;
			}
		}
		else if ($this->strExists($url, "投票")) {
			if ($itemid) {
				$link = "/index.php?g=Wap&m=Vote&a=index&token=" . $this->token . "&wecha_id=" . $this->wecha_id . "&id=" . $itemid;
			}
		}
		else {
			$link = str_replace(array("{wechat_id}", "{siteUrl}", "&amp;", "{changjingUrl}"), array($this->wecha_id, $this->siteUrl, "&", "http://www.weihubao.com"), $url);
			if (!!strpos($url, "tel") === false && ($url != "javascript:void(0)") && !strpos($url, "wecha_id=")) {
				if (strpos($url, "?")) {
					$link = $link . "&wecha_id=" . $this->wecha_id;
				}
				else {
					$link = $link . "?wecha_id=" . $this->wecha_id;
				}
			}
		}

		return $link;
	}

	public function strExists($haystack, $needle)
	{
		return !strpos($haystack, $needle) === false;
	}

	public function curlGet($url)
	{
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}

	public function memberNotice($message, $style)
	{
		if (C("STATICS_PATH")) {
			$staticPath = "";
		}
		else {
			$staticPath = "http://s.404.cn";
		}

		if ($style) {
			$message = ($message ? "您好，您还没有关注我们的公众号,关注后才能继续喔。" : $message);
			$gzhurl = M("Home")->where(array("token" => $this->token))->getField("gzhurl");
			$wxname = $this->wxuser["wxname"];
			$subBtn = "";

			if (!$gzhurl) {
				$subBtn = "<a href=\"" . $gzhurl . "\" class=\"flatbtn\">快捷关注</a>";
				$subBtn2 = "<div class=\"wxname\"><a href=\"" . $gzhurl . "\">快捷关注</a></div>";
			}

			$memberNotice = "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"$staticPath/tpl/static/Plugin/fans.css\" />\r\n\t\t<div class=\"_fly\" id=\"fly_page\">\r\n\t\t\t<p>$message &nbsp; ( 公众号：$wxname )</p>\r\n\t\t\t$subBtn2\r\n\t\t\t<div class=\"wxname close\"><a href=\"javascript:pageClose();\">关闭</a></div>\r\n\t\t</div>\r\n\t<script type=\"text/javascript\" src=\"$staticPath/tpl/static/Plugin/topNotice.js\"></script>\r\n\t<script src=\"$staticPath/tpl/static/Plugin/jquery.leanModal.min.js\"></script>\r\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"$staticPath/tpl/static/Plugin/leanModal.css\" />\r\n\t<div id=\"memberNoticeBox\" style=\"display: none; position: fixed; opacity: 1; z-index: 11000; left: 50%; margin-left: -170px; top: 110px;\">\r\n\t\t<h1>提醒</h1>\r\n\t\t<div class=\"txtfield\">$message &nbsp; ( 公众号：$wxname )</div>\r\n\t\t$subBtn\r\n\t\t<a class=\"flatbtn btnC hidemodal\">关闭</a>\r\n\t</div>\r\n\t <div id=\"lean_overlay\" style=\"display: none; opacity: 0.45;\"></div>\r\n\t<script type=\"text/javascript\">\r\n\t$(function(){\r\n\t\t$('#modaltrigger_notice').leanModal({\r\n\t\t\ttop:110,\r\n\t\t\toverlay:0.45,\r\n\t\t\tcloseButton:\".hidemodal\"\r\n\t\t});\r\n\t});\r\n\t</script>";
		}
		else {
			if ($this->wecha_id) {
				$href = U("Index/memberReg", array("token" => $this->token));
			}
			else {
				$href = U("Index/memberLogin", array("token" => $this->token));
			}

			$message = ($message ? "您是游客身份，点击这里登录/注册" : $message);
			$memberNotice = "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"$staticPath/tpl/static/Plugin/fans.css\" />\r\n\t<div id=\"TopTipHolder\"><div id=\"TopTip\"><a href=\"$href\">$message</a></div><div id=\"TopTipClose\" title=\"关闭\"></div></div>\r\n\t<script type=\"text/javascript\" src=\"$staticPath/tpl/static/Plugin/topNotice.js\"></script>\r\n\t<script src=\"$staticPath/tpl/static/Plugin/jquery.leanModal.min.js\"></script>\r\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"$staticPath/tpl/static/Plugin/leanModal.css\" />\r\n\t<div id=\"memberNoticeBox\" style=\"display: none; position: fixed; opacity: 1; z-index: 11000; left: 50%; margin-left: -170px; top: 110px;\">\r\n\t\t<h1>提醒</h1>\r\n\t\t<div class=\"txtfield\"><a href=\"$href\">$message</a></div>\r\n\t\t<a href=\"$href\" class=\"flatbtn\">注册/登录</a>\r\n\t\t<a class=\"flatbtn btnC hidemodal\">取消</a>\r\n\t</div>\r\n\t <div id=\"lean_overlay\" style=\"display: none; opacity: 0.45;\"></div>\r\n\t<script type=\"text/javascript\">\r\n\t$(function(){\r\n\t\t$('#modaltrigger_notice').leanModal({\r\n\t\t\ttop:110,\r\n\t\t\toverlay:0.45,\r\n\t\t\tcloseButton:\".hidemodal\"\r\n\t\t});\r\n\t});\r\n\t</script>";
		}

		$this->assign("memberNotice", $memberNotice);
	}

	private function redirect_uri()
	{
		return urlencode($this->_redirect_uri);
	}

	protected function getCardInfo($token, $wecha_id)
	{
		$wecha_id = ($wecha_id ? $wecha_id : $this->wecha_id);
		$token = ($token ? $token : $this->token);
		$where = array("token" => $token, "wecha_id" => $wecha_id);
		$number = M("Member_card_create")->where($where)->getField("number");

		if (!$number) {
			return NULL;
		}

		$cardInfo = M("Userinfo")->where($where)->field("balance,total_score")->find();
		return array("number" => $number, "balance" => $cardInfo["balance"], "score" => $cardInfo["total_score"]);
	}

	protected function isSubscribe()
	{
		$wecha_id = $this->wecha_id;
		if (($this->owxuser["appid"] == "") || (($this->owxuser["type"] == 0) && ($this->owxuser["appsecret"] == ""))) {
			if (($this->owxuser["winxintype"] == 1) || ($this->owxuser["winxintype"] == 2)) {
				if ($wecha_id) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			if (($this->owxuser["winxintype"] == 3) || ($this->owxuser["winxintype"] == 4)) {
				$apiOauth = new apiOauth();
				$access_token = $apiOauth->update_authorizer_access_token($this->owxuser["appid"], $this->owxuser);
				$url = "https://api.weixin.qq.com/cgi-bin/user/info?openid=" . $wecha_id . "&access_token=" . $access_token;
				$classData = json_decode($this->curlGet($url));

				if ($classData->subscribe == 0) {
					return false;
				}
				else {
					return true;
				}
			}
			else {
				if (($this->owxuser["winxintype"] == 1) || ($this->owxuser["winxintype"] == 2)) {
					if ($wecha_id) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					return false;
				}
			}
		}
	}
}


?>
