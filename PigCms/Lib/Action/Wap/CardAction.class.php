<?php

class CardAction extends WapAction
{
	public $wecha_id;
	public $thisUser;
	public $isamap;

	public function __construct()
	{
		parent::_initialize();

		if (!defined("RES")) {
			define("RES", THEME_PATH . "common");
		}

		$this->assign("wecha_id", $this->wecha_id);
		$this->token = $this->_get("token");
		$this->thisUser = M("Userinfo")->where(array("token" => $this->_get("token"), "wecha_id" => $this->wecha_id))->find();
		$session_username_name = "token_username_" . $this->token;
		if ((!$this->wecha_id && (ACTION_NAME != "companyMap")) || !$_SESSION[$session_username_name] || $this->thisUser['username']=='') {
			//$this->error("您没有权限使用会员卡，如需使用请关注微信“" . $this->wxuser["wxname"] . "”并回复会员卡", U("Index/index", array("token" => $this->token)));
			$this->error('您没有权限使用会员卡，请先注册或登录会员',U('Index/memberLogin',array('token'=>$this->token)));
		}

		$action = array("index", "companyMap", "payReturn");
		if (!in_array(ACTION_NAME, $action) && !$_GET["cardid"]) {
			$this->error("请领取会员卡后再操作");
		}

		if (C("baidu_map")) {
			$this->isamap = 0;
		}
		else {
			$this->isamap = 1;
			$this->amap = new amap();
		}
	}

	public function index()
	{
		$data = M("Member_card_create");
		$cardByToken = M("Member_card_set")->where(array("token" => $this->token))->order("id ASC")->find();
		$data->where("token='" . $this->token . "' AND cardid<2")->save(array("cardid" => $cardByToken["id"]));
		M("Member_card_exchange")->where("token='" . $this->token . "' AND cardid<2")->save(array("cardid" => $cardByToken["id"]));
		M("Member_card_coupon")->where("token='" . $this->token . "' AND cardid<2")->save(array("cardid" => $cardByToken["id"]));
		M("Member_card_vip")->where("token='" . $this->token . "' AND cardid<2")->save(array("cardid" => $cardByToken["id"]));
		M("Member_card_integral")->where("token='" . $this->token . "' AND cardid<2")->save(array("cardid" => $cardByToken["id"]));
		$member_create_db = M("Member_card_create");
		$cards = $member_create_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->select();
		$cardsByID = array();

		if ($cards) {
			foreach ($cards as $c ) {
				$cardsByID[$c["cardid"]] = $c;
			}
		}

		$cardsCount = count($cards);
		$this->assign("cards", $cards);
		$thisCard["id"] = $cards[0]["cardid"];
		$this->assign("thisCard", $thisCard);
		if ($cardsCount && $_GET["mycard"]) {
			echo "<script>location.href=\"/index.php?g=Wap&m=Card&a=card&wecha_id=" . $this->wecha_id . "&token=" . $this->token . "&cardid=" . $cards[0]["cardid"] . "\";</script>";
		}

		$this->assign("cardsCount", $cardsCount);
		$userinfo_db = M("Userinfo");
		$userInfos = $userinfo_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->select();
		$userScore = 0;

		if ($userInfos) {
			$userScore = intval($userInfos[0]["total_score"]);
		}

		$this->assign("userScore", $userScore);
		$member_card_set_db = M("Member_card_set");
		$allCards = $member_card_set_db->where(array("token" => $this->token))->order("miniscore ASC")->select();

		$nowcard = $member_card_set_db->where(array("token" => $this->token, "id" => intval($thisCard["id"])))->find();
		$newcardid=0;
		$newcardname='';
		foreach ($allCards as $key => $value) {
			if(intval($value["miniscore"]) <= $userScore && intval($value["miniscore"])>$nowcard["miniscore"]){
				$newcardid=$value['id'];
				$newcardname=$value['cardname'];
			}
		}
		if($newcardid){
			$error = -4;
			$this->error("您的积分已经足够升级为：".$newcardname."，请重新填写并审核您的资料", U("Userinfo/index", array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => $newcardid)));
		}

		if ($allCards) {
			$i = 0;
			$now = time();

			foreach ($allCards as $c ) {
				$allCards[$i]["applied"] = ($cardsByID[$c["id"]] ? 1 : 0);
				if ($_GET["mycard"] && !$allCards[$i]["applied"]) {
					unset($allCards[$i]);
				}
				else {
					$owhere = array(
						"token"    => $this->token,
						"wecha_id" => $this->wecha_id,
						"is_open"  => "1",
						"cardid"   => $allCards[$i]["id"],
						"start"    => array("lt", time()),
						"end"      => array("gt", time())
						);
					$allCards[$i]["gifts"] = M("Member_card_gifts")->where($owhere)->count();
					$where = array(
						"token"   => $this->token,
						"cardid"  => $c["id"],
						"is_open" => "1",
						"start"   => array("lt", $now),
						"end"     => array("gt", $now)
						);
					$allCards[$i]["gifts_list"] = M("Member_card_gifts")->where($where)->select();
				}

				$i++;
			}
		}

		$allCardsCount = count($allCards);
		$this->assign("allCards", $allCards);
		$this->assign("allCardsCount", $allCardsCount);
		$thisCompany = M("Company")->where(array("token" => $this->token, "isbranch" => 0, "display" => 1))->find();
		$this->assign("thisCompany", $thisCompany);
		$infoType = "memberCardHome";

		if ($_GET["mycard"]) {
			$infoType = "myCard";
		}

		$focus = M("Member_card_focus")->where(array("token" => $this->_get("token")))->select();

		if ($focus == NULL) {
			$focus = array(
				array("info" => "广告位描述", "img" => "/tpl/static/attachment/focus/tour/4.jpg", "url" => ""),
				array("info" => "广告位描述", "img" => "/tpl/static/attachment/focus/tour/3.jpg", "url" => "")
				);
		}

		$focus = $this->convertLinks($focus);
		$this->assign("flash", $focus);
		$this->assign("infoType", $infoType);
		$this->display();
	}

	public function getLink($url)
	{
		$url = ($url ? $url : "javascript:void(0)");
		$link = str_replace(array("{wechat_id}", "{siteUrl}", "&amp;"), array($this->wecha_id, $this->siteUrl, "&"), $url);
		if (!!strpos($url, "tel") === false && ($url != "javascript:void(0)") && !strpos($url, "wecha_id=")) {
			if (strpos($url, "?")) {
				$link = $link . "&wecha_id=" . $this->wecha_id;
			}
			else {
				$link = $link . "?wecha_id=" . $this->wecha_id;
			}
		}

		return $link;
	}

	public function convertLinks($arr)
	{
		$i = 0;

		foreach ($arr as $a ) {
			if ($a["url"]) {
				$arr[$i]["url"] = $this->getLink($a["url"]);
			}

			$i++;
		}

		return $arr;
	}

	public function companyMap()
	{
		$member_card_create_db = M("Member_card_create");
		$cardsCount = $member_card_create_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->count();
		$this->assign("cardsCount", $cardsCount);
		$this->apikey = C("baidu_map_api");
		$this->assign("apikey", $this->apikey);
		$company_model = M("Company");
		$where = array("token" => $this->token);

		if ($_GET["companyid"]) {
			$where["id"] = intval($_GET["companyid"]);
		}

		$thisCompany = $company_model->where($where)->find();
		$this->assign("thisCompany", $thisCompany);
		$infoType = "companyDetail";
		$this->assign("infoType", $infoType);

		if (!$this->isamap) {
			$this->display();
		}
		else {
			$link = $this->amap->getPointMapLink($thisCompany["longitude"], $thisCompany["latitude"], $thisCompany["name"]);
			header("Location:" . $link);
		}
	}

	public function companyDetail()
	{
		$member_card_set_db = M("Member_card_set");
		$thisCard = $member_card_set_db->where(array("token" => $this->token))->order("id ASC")->find();
		$this->assign("thisCard", $thisCard);
		$member_card_create_db = M("Member_card_create");
		$cardsCount = $member_card_create_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->count();
		$this->assign("cardsCount", $cardsCount);
		$company_model = M("Company");
		$where = array("token" => $this->token, "display" => 1);
		$companies = $company_model->where($where)->order("isbranch asc,taxis ASC")->select();
		$this->assign("companies", $companies);
		$infoType = "companyDetail";
		$this->assign("infoType", $infoType);
		$this->display();
	}

	public function card()
	{
		$cardid = $this->_get("cardid", "intval");
		$this->assign("infoType", "card");
		$member_card_set_db = M("Member_card_set");
		$thisCard = $member_card_set_db->where(array("token" => $this->token, "id" => $cardid))->find();
		if ($cardid && !$thisCard) {
			$this->error("会员卡不存在");
		}

		$this->assign("thisCard", $thisCard);
		$this->assign("card", $thisCard);
		$error = 0;

		if ($thisCard) {
			$userinfo_db = M("Userinfo");
			$where['token']=$this->token;
			$where['_string'] = "wecha_id='".$this->wecha_id."' OR twid='".$this->wecha_id."'";
			$userInfos = $userinfo_db->where($where)->select();
			$userScore = 0;

			if ($userInfos) {
				$userScore = intval($userInfos[0]["total_score"]);
				$userInfo = $userInfos[0];
			}

			$this->assign("userScore", $userScore);
			$member_card_create_db = M("Member_card_create");
			$thisMember = $member_card_create_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => intval($_GET["cardid"])))->find();
			$hasApplied = count($thisMember);

			if (!$hasApplied) {
				$card = M("Member_card_create")->field("id,number")->where("token='" . $this->token . "' and cardid=" . $thisCard["id"] . " and wecha_id = ''")->find();

				if (!$card) {
					$this->error("会员卡暂时缺货");
				}
				else {
					if ((intval($thisCard["miniscore"]) == 0) || (intval($thisCard["miniscore"]) <= $userScore)) {
						// $error = -4;
						// $this->error("要领取会员卡需要重新填写并核实您的资料", U("Userinfo/index", array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => $thisCard["id"])));
	 					if($thisCard['is_check'] == '1'){
	 						$code = $this->_post('code','trim,strtolower');
	 						if($this->_check_code($code) == false){
	 							echo 5;exit;
	 						}
	 					}
	 			
	 					Sms::sendSms($this->token,'有新的会员领了会员卡');
	 					$card=M('Member_card_create')->field('id,number')->where("token='".$this->_get('token')."' and cardid=".$cardid." and wecha_id = ''")->order('id ASC')->find();

	 					if(!$card){
	 						echo 3;exit;
	 					}else {
	 						//
	 						if (intval($thisCard['miniscore'])==0||$userScore>intval($thisCard['minscore'])){
	 							$data['getcardtime']=time();
	 							if ($userinfo){
	 								$update['id']=$userInfos['id'];
	 								M('Userinfo')->where($update)->save($data);
	 							}
	 							$is_card = M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();

								if(empty($is_card)){
									M('Member_card_create')->where(array('id'=>$card['id']))->save(array('wecha_id'=>$this->wecha_id));
									$now 	= time();
									$gwhere = array('token'=>$this->token,'cardid'=>$cardid,'is_open'=>'1','start'=>array('lt',$now),'end'=>array('gt',$now));
									$gifts 	= M('Member_card_gifts')->where($gwhere)->select();

									foreach($gifts as $key=>$value){
										if($value['type'] == "1"){
											//赠积分
											$arr=array();
											$arr['itemid']	= 0;
											$arr['token']	= $this->token;
											$arr['wecha_id']= $this->wecha_id;
											$arr['expense']	= 0;
											$arr['time']	= $now;
											$arr['cat']		= 3;
											$arr['staffid']	= 0;
											$arr['notes']	= '开卡赠送积分';
											$arr['score']	= $value['item_value'];
											
											M('Member_card_use_record')->add($arr);
											M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->setInc('total_score',$arr['score']);
										}else{
											$data['token']		= $this->token;
											$data['wecha_id']	= $this->wecha_id;
											$data['coupon_id']	= $value['item_value'];
											$data['is_use']		= '0';
											$data['cardid']		= $cardid;
											$data['add_time']	= $now;
											//赠卷
											if($value['item_attr'] == '1'){						
												$data['coupon_type']	= '1';
												M('Member_card_coupon_record')->add($data);
											}else{
												$data['coupon_type']	= '2';
												M('Member_card_coupon_record')->add($data);
											}
										}
									}
								}else{
									M('Member_card_create')->where(array('token'=>$this->_get('token'),'wecha_id'=>$this->wecha_id))->delete();
									M('Member_card_create')->where(array('id'=>$card['id']))->save(array('wecha_id'=>$this->wecha_id));
								}
	 							S('fans_'.$this->token.'_'.$this->wecha_id,NULL);
	 						}else {
	 							echo 4;exit;
	 						}
	 					}
					}
					else if ($userScore < intval($thisCard["miniscore"])) {
						$this->error("领取此会员卡需要" . $thisCard["miniscore"] . "积分");
					}
				}
			}
			$thisMember = $member_card_create_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => intval($_GET["cardid"])))->find();
			$this->assign("thisMember", $thisMember);
			$now = time();
			$noticeCount = M("Member_card_notice")->where("cardid=" . $thisCard["id"] . " AND endtime>" . $now)->count();
			$this->assign("noticeCount", $noticeCount);
			$member_card_vip_db = M("Member_card_vip");
			$previlegeCount = $member_card_vip_db->where("cardid=" . $thisCard["id"] . " AND ((type=0 AND statdate<" . $now . " AND enddate>" . $now . ") OR type=1)")->count();
			$this->assign("previlegeCount", $previlegeCount);
			$iwhere = array("token" => $this->token, "wecha_id" => $this->wecha_id, "coupon_type" => "3", "is_use" => "0", "cardid" => $thisCard["id"]);
			$integralCount = M("Member_card_coupon_record")->where($iwhere)->count();
			$cwhere1 = array("token" => $this->token, "wecha_id" => $this->wecha_id, "coupon_type" => "1", "is_use" => "0", "cardid" => $thisCard["id"]);
			$couponCount1 = M("Member_card_coupon_record")->where($cwhere1)->count();
			$cwhere2 = array("token" => $this->token, "wecha_id" => $this->wecha_id, "coupon_type" => "2", "is_use" => "0", "cardid" => $thisCard["id"]);
			$couponCount2 = M("Member_card_coupon_record")->where($cwhere2)->count();
			$recordcount = $integralCount + $couponCount1 + $couponCount2;
			$now = time();
			$where1 = array(
				"token"    => $this->token,
				"cardid"   => $thisCard["id"],
				"attr"     => "0",
				"statdate" => array("lt", $now),
				"enddate"  => array("gt", $now)
				);
			$coupon = M("Member_card_coupon")->where($where1)->count();
			$where2 = array(
				"token"    => $this->token,
				"cardid"   => $thisCard["id"],
				"statdate" => array("lt", $now),
				"enddate"  => array("gt", $now)
				);
			$integral = M("Member_card_integral")->where($where2)->count();
			$couponCount = $coupon + $integral;
			$this->assign("couponCount", $couponCount);
			$todaySigned = $this->_todaySigned();
			$this->assign("todaySigned", $todaySigned);
			$this->assign("userInfo", $userInfo);
		}
		else {
			$this->error("您的积分不够");
		}

		$this->assign("error", $error);
		$now = time();
		$member_card_vip_db = M("Member_card_vip");
		$previlege = $member_card_vip_db->where("cardid=" . $thisCard["id"] . " AND ((type=0 AND statdate<" . $now . " AND enddate>" . $now . ") OR type=1)")->order("create_time desc")->select();

		if ($previlege) {
			$i = 0;

			foreach ($previlege as $n ) {
				$previlege[$i]["info"] = html_entity_decode($n["info"]);
				$i++;
			}
		}

		$this->assign("firstItemID", $list[0]["id"]);
		$this->assign("previlege", $previlege);
		$this->display();
	}

	public function gifts()
	{
		$cardid = $this->_get("cardid", "intval");
		$now = time();
		$where = array(
			"token"   => $this->token,
			"cardid"  => $cardid,
			"is_open" => "1",
			"start"   => array("lt", $now),
			"end"     => array("gt", $now)
			);
		$list = M("Member_card_gifts")->where($where)->select();
		$this->assign("list", $list);
		$this->display();
	}

	public function cards()
	{
		$this->assign("infoType", "card");
		$member_card_set_db = M("Member_card_set");
		$thisCard = $member_card_set_db->where(array("token" => $this->token, "id" => intval($_GET["cardid"])))->find();
		$this->assign("thisCard", $thisCard);
		$this->assign("card", $thisCard);
		$error = 0;

		if ($thisCard) {
			$userinfo_db = M("Userinfo");
			$userInfos = $userinfo_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->select();
			$userScore = 0;

			if ($userInfos) {
				$userScore = intval($userInfos[0]["total_score"]);
				$userInfo = $userInfos[0];
			}

			$this->assign("userScore", $userScore);
			$member_card_create_db = M("Member_card_create");
			$thisMember = $member_card_create_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => intval($_GET["cardid"])))->find();
			$hasApplied = count($thisMember);

			if (!$hasApplied) {
				$card = M("Member_card_create")->field("id,number")->where("token='" . $this->token . "' and cardid=" . $thisCard["id"] . " and wecha_id = ''")->find();

				if (!$card) {
					$this->error("会员卡暂时缺货");
				}
				else {
					// if ((intval($thisCard["miniscore"]) == 0) || (intval($thisCard["miniscore"]) <= $userScore)) {
					// 	$error = -4;
					// 	$this->error("要领取会员卡需要重新填写并核实您的资料", U("Userinfo/index", array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => $thisCard["id"])));
					// }
					// else 
					if ($userScore < intval($thisCard["miniscore"])) {
						$this->error("领取此会员卡需要" . $thisCard["miniscore"] . "积分");
					}
				}
			}
			else {
				$model = new Model();
				$this->assign("thisMember", $thisMember);
				$now = time();
				$noticeCount = M("Member_card_notice")->where("cardid=" . $thisCard["id"] . " AND endtime>" . $now)->count();
				$this->assign("noticeCount", $noticeCount);
				$member_card_vip_db = M("Member_card_vip");
				$previlegeCount = $member_card_vip_db->where("cardid=" . $thisCard["id"] . " AND ((type=0 AND statdate<" . $now . " AND enddate>" . $now . ") OR type=1)")->count();
				$this->assign("previlegeCount", $previlegeCount);
				$integralCount = $model->table(array(C("DB_PREFIX") . "member_card_coupon_record" => "record", C("DB_PREFIX") . "member_card_integral" => "integral"))->where("record.token=\"" . $this->token . "\" AND record.wecha_id=\"" . $this->wecha_id . "\" AND record.is_use=\"0\" AND record.cardid=" . $thisCard["id"] . " AND record.coupon_id=integral.id AND record.coupon_type=\"3\" AND integral.enddate >\"" . $now . "\"")->count();
				$this->assign("integralCount", $integralCount);
				$couponCount1 = $model->table(array(C("DB_PREFIX") . "member_card_coupon_record" => "record", C("DB_PREFIX") . "member_card_coupon" => "coupon"))->where("record.token=\"" . $this->token . "\" AND record.wecha_id=\"" . $this->wecha_id . "\" AND record.is_use=\"0\" AND record.cardid=" . $thisCard["id"] . " AND record.coupon_id=coupon.id AND record.coupon_type=\"1\" AND coupon.enddate >\"" . $now . "\"")->count();
				$this->assign("couponCount1", $couponCount1);
				$couponCount2 = $model->table(array(C("DB_PREFIX") . "member_card_coupon_record" => "record", C("DB_PREFIX") . "member_card_coupon" => "coupon"))->where("record.token=\"" . $this->token . "\" AND record.wecha_id=\"" . $this->wecha_id . "\" AND record.is_use=\"0\" AND record.cardid=" . $thisCard["id"] . " AND record.coupon_id=coupon.id AND record.coupon_type=\"2\" AND coupon.enddate >\"" . $now . "\"")->count();
				$this->assign("couponCount2", $couponCount2);
				$todaySigned = $this->_todaySigned();
				$this->assign("todaySigned", $todaySigned);
				$this->assign("userInfo", $userInfo);
			}
		}
		else {
			$this->error("您的积分不够");
		}

		$this->assign("error", $error);
		$this->display();
	}

	public function cardIntro()
	{
		$member_card_set_db = M("Member_card_set");
		$thisCard = $member_card_set_db->where(array("token" => $this->token, "id" => intval($_GET["cardid"])))->find();
		$this->assign("thisCard", $thisCard);
		$data = M("Member_card_exchange")->where(array("token" => $this->token, "cardid" => $_GET["cardid"]))->find();
		$this->assign("data", $data);
		$company_model = M("Company");
		$where = array("token" => $this->token);
		$thisCompany = $company_model->where($where)->order("isbranch ASC")->find();
		$thisCompany["intro"] = str_replace(array("&lt;", "&gt;", "&quot;", "&amp;nbsp;"), array("<", ">", "\"", " "), $thisCompany["intro"]);
		$this->assign("thisCompany", $thisCompany);
		$this->display();
	}

	public function signscore()
	{
		$userinfo_db = M("Userinfo");
		$userInfos = $userinfo_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->select();
		$userScore = 0;

		if ($userInfos) {
			$userScore = intval($userInfos[0]["total_score"]);
			$userInfo = $userInfos[0];
		}
		$member_card_set_db = M("Member_card_set");
		$thisCard = $member_card_set_db->where(array("token" => $this->token, "id" => intval($_GET["cardid"])))->find();
		$allcards = $member_card_set_db->where(array("token" => $this->token))->order("miniscore ASC")->select();
		$newcardid=0;
		$newcardname='';
		foreach ($allcards as $key => $value) {
			if(intval($value["miniscore"]) <= $userScore && intval($value["miniscore"])>$thisCard["miniscore"]){
				$newcardid=$value['id'];
				$newcardname=$value['cardname'];
			}
		}
		if($newcardid){
			$error = -4;
			$this->error("您的积分已经足够升级为：".$newcardname."，请重新填写并审核您的资料", U("Userinfo/index", array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => $newcardid)));
		}

		$this->assign("userInfo", $userInfo);
		$this->assign("userScore", $userScore);
		$this->assign("thisCard", $thisCard);
		$todaySigned = $this->_todaySigned();
		$this->assign("todaySigned", $todaySigned);
		$cardsign_db = M("Member_card_sign");
		$now = time();
		$day = date("d", $now);
		$year = date("Y", $now);
		$month = date("m", $now);

		if ($_GET["month"]) {
			$month = intval($_GET["month"]);
		}

		$firstday = date("Y-m-01 0:0:0", strtotime($year . "-" . $month));
		$lastday = date("Y-m-d 23:59:59", strtotime("$firstday +1 month -1 day"));
		$firstSecondOfMonth = strtotime($firstday);
		$lastSecondOfMonth = strtotime($lastday);
		$signRecords = $cardsign_db->where("token='" . $this->token . "' AND wecha_id='" . $this->wecha_id . "' AND sign_time>" . $firstSecondOfMonth . " AND sign_time<" . $lastSecondOfMonth)->order("sign_time DESC")->select();
		$this->assign("now_date", date("Y年m月", mktime(0, 0, 0, $month, 1, $year)));
		$this->assign("prev_month", $month == 1 ? 1 : $month - 1);
		$this->assign("next_month", $month == 12 ? 12 : $month + 1);
		$this->assign("signRecords", $signRecords);
		$this->display();
	}

	public function addSign()
	{
		$signed = $this->_todaySigned();

		if ($signed) {
			echo "{\"success\":1,\"msg\":\"您今天已经签到了\"}";
			exit();
		}

		$cardsign_db = M("Member_card_sign");
		$where = array("token" => $this->token, "wecha_id" => $this->wecha_id, "score_type" => 1);
		$sign = $cardsign_db->where($where)->order("sign_time desc")->find();

		if ($sign == NULL) {
			$cardsign_db->add($where);
			$sign = $cardsign_db->where($where)->order("id desc")->find();
		}

		$get_card = M("member_card_create")->where(array("token" => $this->token,"wecha_id" => $this->wecha_id, "cardid" => intval($_GET["cardid"])))->find();

		if (!$get_card) {
			Header("Location: " . C("site_url") . "/" . U("Wap/Card/card", array("token" => $this->token, "wecha_id" => $this->wecha_id)));
			exit("领卡后才可以签到.");
		}

		$set_exchange = M("Member_card_exchange")->where(array("token" => $this->token, "cardid" => intval($_GET["cardid"])))->find();
		$this->assign("set_exchange", $set_exchange);
		$userinfo = M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->find();
		$data["sign_time"] = time();
		$data["is_sign"] = 1;
		$data["score_type"] = 1;
		$data["token"] = $this->token;
		$data["wecha_id"] = $this->wecha_id;
		$data["expense"] = intval($set_exchange["everyday"]);
		$rt = $cardsign_db->where($where)->add($data);

		if ($rt) {
			$da["total_score"] = $userinfo["total_score"] + $data["expense"];
			$da["sign_score"] = $userinfo["sign_score"] + $data["expense"];
			$da["continuous"] = 1;
			M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->save($da);
			echo "{\"success\":1,\"msg\":\"签到成功，成功获取了" . $set_exchange["everyday"] . "个积分\"}";
		}
		else {
			echo "{\"success\":1,\"msg\":\"暂时无法签到\"}";
		}
	}

	public function signdetail()
	{
		$cardid = $this->_get("cardid", "intval");
		$cardsign_db = M("Member_card_exchange");
		$where = array("token" => $this->token, "cardid" => $cardid);
		$sign = $cardsign_db->where($where)->find();
		$this->assign("thisCard", $this->_thisCard());
		$this->assign("sign", $sign);
		$this->display();
	}

	public function _todaySigned()
	{
		$signined = 0;
		$now = time();
		$member_card_sign_db = M("Member_card_sign");
		$where = array("token" => $this->token, "wecha_id" => $this->wecha_id, "score_type" => 1);
		$sign = $member_card_sign_db->where($where)->order("sign_time desc")->find();
		$today = date("Y-m-d", $now);
		$itoday = date("Y-m-d", intval($sign["sign_time"]));
		if ($sign && ($itoday == $today)) {
			$signined = 1;
		}

		return $signined;
	}

	public function _thisCard()
	{
		$member_card_set_db = M("Member_card_set");
		$thisCard = $member_card_set_db->where(array("token" => $this->token, "id" => intval($_GET["cardid"])))->find();
		return $thisCard;
	}

	public function notice()
	{
		$this->assign("infoType", "notice");
		$thisCard = $this->_thisCard();
		$this->assign("thisCard", $thisCard);
		$member_card_notice_db = M("Member_card_notice");
		$now = time();
		$notices = $member_card_notice_db->where("cardid=" . $thisCard["id"] . " AND endtime>" . $now)->order("time desc")->select();

		if ($notices) {
			$i = 0;

			foreach ($notices as $n ) {
				$notices[$i]["content"] = html_entity_decode($n["content"]);
				$i++;
			}
		}

		$this->assign("notices", $notices);
		$this->assign("firstItemID", $notices[0]["id"]);
		$this->display();
	}

	public function previlege()
	{
		$this->assign("infoType", "privelege");
		$thisCard = $this->_thisCard();
		$this->assign("thisCard", $thisCard);
		$now = time();
		$member_card_vip_db = M("Member_card_vip");
		$list = $member_card_vip_db->where("cardid=" . $thisCard["id"] . " AND ((type=0 AND statdate<" . $now . " AND enddate>" . $now . ") OR type=1)")->order("create_time desc")->select();

		if ($list) {
			$i = 0;

			foreach ($list as $n ) {
				$list[$i]["info"] = html_entity_decode($n["info"]);
				$i++;
			}
		}

		$this->assign("firstItemID", $list[0]["id"]);
		$this->assign("list", $list);
		$this->display();
	}

	public function integral()
	{
		$this->assign("infoType", "integral");
		$thisCard = $this->_thisCard();
		$this->assign("thisCard", $thisCard);
		$is_use = ($this->_get("is_use", "intval") ? $this->_get("is_use", "intval") : "0");
		$now = time();
		$where = array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => $thisCard["id"], "coupon_type" => "3", "is_use" => "$is_use");
		$data = M("Member_card_coupon_record")->where($where)->group("coupon_id")->field("id,cardid,coupon_id,coupon_type,add_time,is_use,count(id) as count")->select();

		foreach ($data as $key => $value ) {
			$cwhere = array("token" => $this->token, "cardid" => $value["cardid"], "id" => $value["coupon_id"]);
			$cinfo = M("Member_card_integral")->where($cwhere)->field("info,pic,statdate,enddate,title,integral")->find();
			$cinfo["info"] = html_entity_decode($cinfo["info"]);
			if (($now < $cinfo["enddate"]) && ($cinfo["statdate"] < $now)) {
				$data[$key] = array_merge($value, $cinfo);
			}
			else {
				unset($data[$key]);
			}
		}
		$this->assign("firstItemID", $data[0]["id"]);
		$this->assign("list", $data ? $data : "");
		$this->assign("is_use", $is_use);
		$this->assign("type", $type);
		$this->display();
	}

	public function my_coupon()
	{
		$this->assign("infoType", "coupon");
		$thisCard = $this->_thisCard();
		$this->assign("thisCard", $thisCard);
		$type = ($this->_get("type", "intval") ? $this->_get("type", "intval") : 1);
		$now = time();
		$data = array();

		if ($type == 3) {
			$where = array(
				"token"    => $this->token,
				"cardid"   => $thisCard["id"],
				"statdate" => array("lt", $now),
				"enddate"  => array("gt", $now)
				);
			$data = M("Member_card_integral")->where($where)->order("create_time desc")->select();

			foreach ($data as $k => $n ) {
				$data[$k]["info"] = html_entity_decode($n["info"]);
				$data[$k]["count"] = $this->checkTotal($n, $type);

				if ($n["company_id"] == 0) {
					$data[$k]["company_name"] = "所有门店";
				}
				else {
					$data[$k]["company_name"] = M("Company")->where(array("token" => $this->token, "id" => $n["company_id"]))->getField("name");
				}
			}
		}
		else {
			$where = array(
				"token"    => $this->token,
				"cardid"   => $thisCard["id"],
				"attr"     => "0",
				"statdate" => array("lt", $now),
				"enddate"  => array("gt", $now)
				);

			if ($type == 1) {
				$where["type"] = 1;
			}
			else if ($type == 2) {
				$where["type"] = 0;
			}

			$data = M("Member_card_coupon")->where($where)->order("create_time desc")->select();

			foreach ($data as $k => $n ) {
				$data[$k]["info"] = html_entity_decode($n["info"]);
				$data[$k]["count"] = $this->checkTotal($n, $type);

				if ($n["company_id"] == 0) {
					$data[$k]["company_name"] = "所有门店";
				}
				else {
					$data[$k]["company_name"] = M("Company")->where(array("token" => $this->token, "id" => $n["company_id"]))->getField("name");
				}
			}
		}

		$this->assign("firstItemID", $data[0]["id"]);
		$this->assign("list", $data);
		$this->assign("type", $type);
		$this->display();
	}

	public function checkTotal($coupon, $type)
	{
		if (((0 < $coupon["total"]) && (0 < $coupon["people"])) || (($coupon["total"] == 0) && (0 < $coupon["people"]))) {
			$cwhere = array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => $coupon["cardid"], "coupon_type" => $type, "coupon_id" => $coupon["id"]);
			$count = M("Member_card_coupon_record")->where($cwhere)->count();
			$data = $coupon["people"] - $count;
		}
		else {
			if ((0 < $coupon["total"]) && ($coupon["people"] == 0)) {
				$cwhere = array("token" => $this->token, "cardid" => $coupon["cardid"], "coupon_type" => $type, "coupon_id" => $coupon["id"]);
				$count = M("Member_card_coupon_record")->where($cwhere)->count();
				$data = $coupon["total"] - $count;
			}
			else {
				$data = -1;
			}
		}

		return $data;
	}

	public function action_myCoupon()
	{
		$data["use_time"] = "";
		$data["add_time"] = time();
		$data["coupon_id"] = $this->_post("coupon_id", "intval");
		$data["cardid"] = $this->_post("cardid", "intval");
		$data["token"] = $this->token;
		$data["wecha_id"] = $this->wecha_id;
		$data["coupon_type"] = $this->_post("type", "intval");
		$result = array();
		$now = time();

		if ($data["coupon_type"] == 3) {
			$integral = M("Member_card_integral")->where(array("token" => $this->token, "cardid" => $data["cardid"], "id" => $data["coupon_id"]))->find();

			if ($this->thisUser["total_score"] < $integral["integral"]) {
				$result["err"] = -1;
				$result["info"] = "你的积分不足" . $integral["integral"];
				echo json_encode($result);
				exit();
			}

			if ($this->checkTotal($integral, $data["coupon_type"]) == 0) {
				$result["err"] = -2;
				$result["info"] = "优惠券已经领光了";
				echo json_encode($result);
				exit();
			}

			$data["company_id"] = $integral["company_id"];
		}
		else {
			$where = array("token" => $this->token, "cardid" => $data["cardid"], "id" => $data["coupon_id"]);

			if ($data["coupon_type"] == 1) {
				$where["type"] = 1;
			}
			else if ($data["coupon_type"] == 2) {
				$where["type"] = 0;
			}

			$coupon = M("Member_card_coupon")->where($where)->order("create_time desc")->find();

			if ($this->checkTotal($coupon, $data["coupon_type"]) == 0) {
				$result["err"] = -2;
				$result["info"] = "优惠券已经领光了";
				echo json_encode($result);
				exit();
			}

			$data["company_id"] = $coupon["company_id"];
		}

		$rid = M("Member_card_coupon_record")->add($data);

		if ($rid) {
			if ($data["coupon_type"] == 3) {
				$arr = array();
				$arr["itemid"] = $rid;
				$arr["wecha_id"] = $this->wecha_id;
				$arr["expense"] = 0;
				$arr["time"] = $now;
				$arr["token"] = $this->token;
				$arr["cat"] = 2;
				$arr["score"] = -intval($integral["integral"]);
				$arr["company_id"] = $data["company_id"];
				$arr["cardid"] = $data["cardid"];
				M("Member_card_use_record")->add($arr);
				M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->setDec("total_score", $integral["integral"]);
			}

			$result["err"] = 0;

			if ($data["coupon_type"] == 3) {
				$juan = "礼品券";
			}
			else if ($data["coupon_type"] == 2) {
				$juan = "代金券";
			}
			else {
				$juan = "优惠劵";
			}

			$result["info"] = "您成功领取了一张" . $juan;
			echo json_encode($result);
		}
	}

	public function coupon()
	{
		$this->assign("infoType", "coupon");
		$thisCard = $this->_thisCard();
		$this->assign("thisCard", $thisCard);
		$type = $this->_get("type", "intval");
		$is_use = ($this->_get("is_use", "intval") ? $this->_get("is_use", "intval") : "0");
		$now = time();
		$where = array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => $thisCard["id"], "coupon_type" => $type, "is_use" => "$is_use");
		$data = M("Member_card_coupon_record")->where($where)->group("coupon_id")->field("id,cardid,coupon_id,coupon_type,add_time,is_use,count(id) as count")->select();
		foreach ($data as $key => $value ) {
			$cwhere = array("token" => $this->token, "cardid" => $value["cardid"], "id" => $value["coupon_id"]);
			$cinfo = M("Member_card_coupon")->where($cwhere)->field("info,pic,statdate,enddate,title,price")->find();
			$cinfo["info"] = html_entity_decode($cinfo["info"]);
			if (($now < $cinfo["enddate"]) && ($cinfo["statdate"] < $now)) {
				$data[$key] = array_merge($value, $cinfo);
			}
			else {
				unset($data[$key]);
			}
		}

		$this->assign("firstItemID", $data[0]["id"]);
		$this->assign("list", $data ? $data : "");
		$this->assign("is_use", $is_use);
		$this->assign("type", $type);
		$this->display();
	}

	public function coupon_use()
	{
		$cardid = $this->_get("cardid", "intval");
		$coupon_id = $this->_get("coupon_id", "intval");
		$coupon_type = $this->_get("coupon_type", "intval");
		$info = M("Member_card_coupon_record")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => $cardid, "id" => $coupon_id, "coupon_type" => $coupon_type))->find();

		if ($coupon_type == 3) {
			$coupon = M("Member_card_integral")->where(array("token" => $this->token, "cardid" => $cardid, "id" => $info["coupon_id"]))->field("title,pic,info,company_id")->find();
		}
		else {
			$coupon = M("Member_card_coupon")->where(array("token" => $this->token, "cardid" => $cardid, "id" => $info["coupon_id"]))->field("title,pic,info,company_id")->find();
		}

		$info = array_merge($info, $coupon);
		$this->assign("thisCard", $this->_thisCard());
		$this->assign("type", $coupon_type);
		$this->assign("info", $info);
		$cwhere = array("token" => $this->token);

		if ($info["company_id"] != 0) {
			$cwhere = array("id" => $info["company_id"]);
		}

		$company = M("Company")->where($cwhere)->order("isbranch asc,id desc")->select();
		$this->assign("company", $company);

		if ($coupon_type == 3) {
			$this->display("intergral_use");
		}
		else {
			$this->display();
		}
	}

	public function action_usePrivelege()
	{
		if (IS_POST) {
			$paytype = intval($_POST["paytype"]);
			$itemid = intval($_POST["itemid"]);
			$db = M("Member_card_vip");
			$thisItem = $db->where(array("id" => $itemid))->find();

			if (!$thisItem) {
				echo "{\"success\":-2,\"msg\":\"不存在指定特权\"}";
				exit();
			}

			$member_card_set_db = M("Member_card_set");
			$thisCard = $member_card_set_db->where(array("id" => intval($thisItem["cardid"])))->find();
			$set_exchange = M("Member_card_exchange")->where(array("cardid" => intval($thisCard["id"])))->find();

			if (!$thisCard) {
				echo "{\"success\":-3,\"msg\":\"会员卡不存在\"}";
				exit();
			}

			$userinfo_db = M("Userinfo");
			$thisUser = $this->thisUser;

			if ($paytype == 0) {
				$staff_db = M("Company_staff");
				$thisStaff = $staff_db->where(array("username" => $this->_post("username"), "token" => $thisCard["token"]))->find();

				if ($thisStaff) {
					echo "{\"success\":-7,\"msg\":\"用户名不存在\"}";
					exit();
				}

				if (md5($this->_post("password")) != $thisStaff["password"]) {
					echo "{\"success\":-4,\"msg\":\"商家密码错误\"}";
					exit();
				}
				else {
					$now = time();
					$arr = array();
					$arr["itemid"] = $this->_post("itemid");
					$arr["token"] = $this->token;
					$arr["wecha_id"] = $this->wecha_id;
					$arr["expense"] = $this->_post("money");
					$arr["time"] = $now;
					$arr["cat"] = 4;
					$arr["staffid"] = $thisStaff["id"];
					$arr["notes"] = $this->_post("notes", "trim");
					$arr["score"] = intval($set_exchange["reward"]) * $arr["expense"];
					M("Member_card_use_record")->add($arr);
					$userinfo_db = M("Userinfo");
					$thisUser = $this->thisUser;
					$userArr = array();
					$userArr["total_score"] = $thisUser["total_score"] + $arr["score"];
					$userArr["expensetotal"] = $thisUser["expensetotal"] + $arr["expense"];
					$userinfo_db->where(array("token" => $thisCard["token"], "wecha_id" => $arr["wecha_id"]))->save($userArr);
					$useCount = intval($thisItem["usetime"]) + 1;
					$db->where(array("id" => $itemid))->save(array("usetime" => $useCount));
					echo "{\"success\":1,\"msg\":\"数据提交成功\"}";
				}
			}
			else {
				$arr["itemid"] = $this->_post("itemid");
				$arr["wecha_id"] = $this->wecha_id;
				$arr["expense"] = $_POST["money"];
				$arr["time"] = time();
				$arr["token"] = $this->token;
				$arr["cat"] = 4;
				$arr["staffid"] = 0;
				$arr["usecount"] = 1;
				$set_exchange = M("Member_card_exchange")->where(array("cardid" => intval($thisCard["id"])))->find();
				$arr["score"] = intval($set_exchange["reward"]) * $arr["expense"];

				if ($arr["expense"] <= 0) {
					$this->error("请输入有效的金额");
				}

				$single_orderid = date("YmdHis", time()) . mt_rand(1000, 9999);
				$record["orderid"] = $single_orderid;
				$record["ordername"] = "支付除特权外多余款项";
				$record["paytype"] = "CardPay";
				$record["createtime"] = time();
				$record["paid"] = 0;
				$record["price"] = $arr["expense"];
				$record["token"] = $this->token;
				$record["wecha_id"] = $this->wecha_id;
				$record["type"] = 0;
				$record["cardid"] = $thisCard["id"];
				$result = M("Member_card_pay_record")->add($record);

				if (!$result) {
					$this->error("提交记录失败");
				}

				$this->redirect(U("CardPay/pay", array("from" => "Card", "token" => $this->_get("token"), "wecha_id" => $this->wecha_id, "price" => $arr["expense"], "single_orderid" => $single_orderid, "orderName" => "支付除特权外多余款项", "redirect" => "Card/payReturn|itemid:" . $arr["itemid"] . ",usecount:" . $arr["usecount"] . ",score:" . $arr["score"] . ",type:privelege,act=cards,cardid:" . $thisCard["id"])));
			}
		}
		else {
			echo "{\"success\":-1,\"msg\":\"不是post数据\"}";
		}
	}

	public function action_useIntergral()
	{
		$now = time();
		if (IS_POST) {
			$company_id = $this->_post("company_id", "intval");
			$rwhere = array("token" => $this->token, "wecha_id" => $this->wecha_id, "coupon_type" => $this->_post("coupon_type", "intval"), "id" => $this->_post("record_id", "intval"), "is_use" => "0");
			$r_record = M("Member_card_coupon_record")->where($rwhere)->find();

			if (!$r_record) {
				echo "{\"success\":-8,\"msg\":\"没有找到优惠券\"}";
				exit();
			}

			$itemid = $r_record["coupon_id"];
			$db = M("Member_card_integral");
			$thisItem = $db->where("id=" . $itemid . " AND statdate<" . $now . " AND enddate>" . $now)->find();

			if (!$thisItem) {
				echo "{\"success\":-2,\"msg\":\"不存在指定信息\"}";
				exit();
			}

			$member_card_set_db = M("Member_card_set");
			$thisCard = $member_card_set_db->where(array("id" => intval($thisItem["cardid"])))->find();

			if (!$thisCard) {
				echo "{\"success\":-3,\"msg\":\"会员卡不存在\"}";
				exit();
			}
			$userinfo_db = M("Userinfo");
			$thisUser = $this->thisUser;
			$staff_db = M("Company_staff");
			$staff_where = array("username" => $this->_post("username"), "token" => $this->token, "companyid" => $company_id);
			$thisStaff = $staff_db->where($staff_where)->find();
			if (!$thisStaff) {
				echo "{\"success\":-7,\"msg\":\"商家用户名不存在\"}";
				exit();
			}

			if (md5($this->_post("password")) != $thisStaff["password"]) {
				echo "{\"success\":-4,\"msg\":\"商家密码错误\"}";
				exit();
			}
			else {
				$arr["itemid"] = $itemid;
				$arr["wecha_id"] = $this->wecha_id;
				$arr["time"] = $now;
				$arr["token"] = $thisItem["token"];
				$arr["cat"] = 4;
				$arr["staffid"] = $thisStaff["id"];
				$arr["usecount"] = 1;
				$arr["notes"] = $this->_post("notes", "trim");
				$arr["company_id"] = $company_id;
				$arr["cardid"] = $thisCard["id"];
				M("Member_card_use_record")->add($arr);
				$db->where(array("id" => $itemid))->setInc("usetime", 1);
				M("Member_card_coupon_record")->where($rwhere)->save(array("use_time" => time(), "is_use" => "1"));
				echo "{\"success\":1,\"msg\":\"兑换成功\"}";
			}
		}
		else {
			echo "{\"success\":-1,\"msg\":\"不是post数据\"}";
		}
	}

	public function action_useCoupon()
	{
		$now = time();

		if (IS_POST) {
			$company_id = $this->_post("company_id", "intval");
			$rwhere = array("token" => $this->token, "wecha_id" => $this->wecha_id, "coupon_type" => $this->_post("coupon_type", "intval"), "id" => $this->_post("record_id", "intval"), "is_use" => "0");
			$r_record = M("Member_card_coupon_record")->where($rwhere)->find();

			if (!$r_record) {
				echo "{\"success\":-8,\"msg\":\"没有找到优惠券\"}";
				exit();
			}

			$itemid = $r_record["coupon_id"];
			$paytype = intval($_POST["paytype"]);
			$db = M("Member_card_coupon");
			$thisItem = $db->where("id=" . $itemid . " AND statdate<" . $now . " AND enddate>" . $now)->find();

			if (!$thisItem) {
				echo "{\"success\":-2,\"msg\":\"不存在指定信息\"}";
				exit();
			}

			$member_card_set_db = M("Member_card_set");
			$thisCard = $member_card_set_db->where(array("id" => intval($thisItem["cardid"])))->find();

			if (!$thisCard) {
				echo "{\"success\":-3,\"msg\":\"会员卡不存在\"}";
				exit();
			}

			$userinfo_db = M("Userinfo");
			$thisUser = $this->thisUser;
			$useTime = 1;

			if ($paytype == 0) {
				$staff_db = M("Company_staff");
				$staff_where = array("username" => $this->_post("username"), "token" => $this->token, "companyid" => $company_id);
				$thisStaff = $staff_db->where($staff_where)->find();

				if (!$thisStaff) {
					echo "{\"success\":-7,\"msg\":\"商家用户名不存在\"}";
					exit();
				}

				if (md5($this->_post("password")) != $thisStaff["password"]) {
					echo "{\"success\":-4,\"msg\":\"商家密码错误\"}";
					exit();
				}
				else {
					$arr = array();
					$arr["itemid"] = $itemid;
					$arr["wecha_id"] = $this->wecha_id;
					$arr["expense"] = $this->_post("money");
					$arr["time"] = $now;
					$arr["token"] = $thisItem["token"];
					$arr["cat"] = 1;
					$arr["staffid"] = $thisStaff["id"];
					$arr["usecount"] = $useTime;
					$arr["notes"] = $this->_post("notes", "trim");
					$arr["company_id"] = $company_id;
					$arr["cardid"] = $thisCard["id"];
					$set_exchange = M("Member_card_exchange")->where(array("cardid" => intval($thisCard["id"])))->find();
					$arr["score"] = intval($set_exchange["reward"]) * $arr["expense"];
					M("Member_card_use_record")->add($arr);
					$userArr = array();
					$userArr["total_score"] = $thisUser["total_score"] + $arr["score"];
					$userArr["expensetotal"] = $thisUser["expensetotal"] + $arr["expense"];
					$userinfo_db->where(array("token" => $thisCard["token"], "wecha_id" => $arr["wecha_id"]))->save($userArr);
					$db->where(array("id" => $itemid))->setInc("usetime", 1);
					M("Member_card_coupon_record")->where($rwhere)->save(array("use_time" => time(), "is_use" => "1"));
					echo "{\"success\":1,\"msg\":\"线下支付成功\"}";
					exit();
				}
			}
			else {
				$arr["itemid"] = $itemid;
				$arr["wecha_id"] = $this->wecha_id;
				$arr["expense"] = $_POST["money"];
				$arr["time"] = $now;
				$arr["token"] = $thisItem["token"];
				$arr["cat"] = 1;
				$arr["staffid"] = 0;
				$arr["usecount"] = $useTime;
				$set_exchange = M("Member_card_exchange")->where(array("cardid" => intval($thisCard["id"])))->find();
				$arr["score"] = intval($set_exchange["reward"]) * $arr["expense"];

				if ($arr["expense"] <= 0) {
					$this->error("请输入有效的金额");
				}

				$single_orderid = date("YmdHis", time()) . mt_rand(1000, 9999);
				$record["orderid"] = $single_orderid;
				$record["ordername"] = "支付除优惠劵外多余款项";
				$record["paytype"] = "CardPay";
				$record["createtime"] = time();
				$record["paid"] = 0;
				$record["price"] = $arr["expense"];
				$record["token"] = $this->token;
				$record["wecha_id"] = $this->wecha_id;
				$record["type"] = 0;
				$record["company_id"] = $company_id;
				$record["cardid"] = $thisCard["id"];
				$result = M("Member_card_pay_record")->add($record);
				$db->where(array("id" => $itemid))->setInc("usetime", 1);

				if (!$result) {
					echo "{\"success\":-6,\"msg\":\"提交失败\"}";
				}

				M("Member_card_coupon_record")->where($rwhere)->save(array("use_time" => time(), "is_use" => "1"));
				$this->redirect(U("CardPay/pay", array("from" => "Card", "token" => $this->token, "wecha_id" => $this->wecha_id, "price" => $arr["expense"], "single_orderid" => $single_orderid, "orderName" => "支付除优惠劵外多余款项", "redirect" => "Card/payReturn|itemid:" . $itemid . ",usecount:" . $arr["usecount"] . ",score:" . $arr["score"] . ",type:coupon,act:cards,cardid:" . $thisCard["id"])));
			}
		}
		else {
			echo "{\"success\":-1,\"msg\":\"不是post数据\"}";
		}
	}

	public function expense()
	{
		$userinfo_db = M("Userinfo");
		$userInfos = $userinfo_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->select();
		$userScore = 0;

		if ($userInfos) {
			$userScore = intval($userInfos[0]["total_score"]);
			$userInfo = $userInfos[0];
		}

		$this->assign("userInfo", $userInfo);
		$this->assign("userScore", $userScore);
		$member_card_set_db = M("Member_card_set");
		$thisCard = $member_card_set_db->where(array("token" => $this->token, "id" => intval($_GET["cardid"])))->find();
		$this->assign("thisCard", $thisCard);
		$db = M("Member_card_use_record");
		$now = time();
		$day = date("d", $now);
		$year = date("Y", $now);
		$month = date("m", $now);

		if ($_GET["month"]) {
			$month = intval($_GET["month"]);
		}

		$nowY = date("Y");
		$start = strtotime($nowY . "-" . $month . "-01");
		$last = strtotime(date("Y-m-d", $start) . " +1 month -1 day");
		$records = $db->where("token='" . $this->token . "' AND wecha_id='" . $this->wecha_id . "' AND time>" . $start . " AND time<" . $last)->order("time DESC")->select();
		$this->assign("records", $records);
		$this->display();
	}

	public function paypwd()
	{
		// if ($this->fans) {
		// 	$this->error("无效的用户");
		// 	exit();
		// }
		$cardid = $this->_get("cardid", "intval");
		if (IS_POST) {
			$where = array("token" => $this->token, "wecha_id" => $this->wecha_id);
			$oldpassword = $this->_post("oldpassword", "trim");
			$password = $this->_post("password", "trim");
			$repassword = $this->_post("repassword", "trim");

			if (!$password) {
				$this->error("密码不能为空");
				exit();
			}

			if ($password != $repassword) {
				$this->error("两次密码不一致");
				exit();
			}

			$old = M("Userinfo")->where($where)->getField("paypass");

			if ($old) {
				if (md5($oldpassword) != $old) {
					$this->error("原密码错误".$old);
					exit();
				}
			}

			M("Userinfo")->where($where)->save(array("paypass" => md5($password)));
			$this->success("保存成功",U('Card/cards',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>$cardid)));
		}
		else {
			if($this->thisUser['paypass']){
				$this->assign("ispass", '1');
			}else{
				$this->assign("ispass", '0');
			}
			$this->assign("thisCard", $this->_thisCard());
			$this->display();
		}
	}

	public function bind()
	{
		$where = array("token" => $this->token, "wecha_id" => $this->wecha_id);
		$create = M("Member_card_create")->where($where)->find();

		if (IS_POST) {
			$number = $this->_post("new_number", "trim");
			$staff_db = M("Company_staff");
			$thisStaff = $staff_db->where(array("username" => $this->_post("username"), "token" => $this->token))->find();

			if (M("Member_card_create")->where(array(
	"token"    => $this->token,
	"number"   => $number,
	"wecha_id" => array("neq", "")
	))->find()) {
				$this->error("你绑定卡号已经被占用");
				exit();
			}

			if (!$thisStaff) {
				$this->error("商家名称不存在");
				exit();
			}

			if (md5($this->_post("password")) == $thisStaff["password"]) {
				$where = array("token" => $this->token, "wecha_id" => $this->wecha_id, "id" => $this->_post("createid", "intval"));

				if (M("Member_card_create")->where($where)->save(array("is_bind" => 1, "old_number" => $create["number"], "number" => $number))) {
					$this->success("绑定成功");
				}
				else {
					$this->error("绑定错误，请重新尝试");
				}
			}
			else {
				$this->error("商家密码错误");
				exit();
			}
		}
		else {
			$this->assign("create", $create);
			$this->assign("thisCard", $this->_thisCard());
			$this->display();
		}
	}

	public function addr()
	{
		if (IS_POST) {
			if ($_POST["truename"] || $_POST["tel"] || $_POST["address"]) {
				$this->error("请填写完整的信息");
				exit();
			}

			$where = array("token" => $this->token, "wecha_id" => $this->wecha_id);
			M("Userinfo")->where($where)->save($_POST);
			$this->success("保存成功");
		}
		else {
			$user = M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->find();
			$this->assign("user", $user);
			$this->assign("thisCard", $this->_thisCard());
			$this->display();
		}
	}

	public function topay()
	{
		$config = M("Alipay_config")->where(array("token" => $this->token))->find();
		$info["cardid"] = $this->_get("cardid", "intval");
		$info["token"] = $this->_get("token");
		$info["wecha_id"] = $this->wecha_id;
		$member_card_set_db = M("Member_card_set");
		$member_card_create_db = M("Member_card_create");
		$thisCard = $member_card_set_db->where(array("token" => $this->token, "id" => intval($_GET["cardid"])))->find();
		$card = $member_card_create_db->field("number")->where(array("token" => $info["token"], "wecha_id" => $info["wecha_id"]))->find();
		$company_model = M("Company");
		$cardsCount = $member_card_create_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->count();
		$this->assign("cardsCount", $cardsCount);
		$token = $this->token;
		$thisCompany = $company_model->where("token = '$token'")->find();
		$this->assign("thisCompany", $thisCompany);
		$this->assign("info", $info);
		$this->assign("card", $card);
		$this->assign("thisCard", $thisCard);
		$this->display();
	}

	public function consume()
	{
		$now = time();
		$config = M("Alipay_config")->where(array("token" => $this->token))->find();
		$cardid = $this->_get("cardid", "intval");
		$now = time();
		$thisCard = M("Member_card_set")->where(array("token" => $this->token, "id" => $cardid))->find();
		$useTime = 1;

		if (IS_POST) {
			$rwhere = array(
				"token"       => $this->token,
				"wecha_id"    => $this->wecha_id,
				"coupon_type" => array("lt", "3"),
				"id"          => $this->_post("consume_id", "intval"),
				"is_use"      => "0"
				);
			$r_record = M("Member_card_coupon_record")->where($rwhere)->find();

			if (!$r_record) {
				$r_record["coupon_id"] = 0;
			}

			$itemid = $r_record["coupon_id"];
			$price = $this->_post("price", "floatval");
			$consume_id = $this->_post("consume_id", "intval");
			$pay_type = $this->_post("pay_type", "intval");
			$card_pwd = $this->_post("card_pwd", "trim");
			$com_pwd = $this->_post("com_pwd", "trim");
			$company_id = $this->_post("company_id", "intval");

			if ($pay_type == 1) {
				$paypass = $this->fans["paypass"];
				$arr["itemid"] = $itemid;
				$arr["wecha_id"] = $this->wecha_id;
				$arr["expense"] = $price;
				$arr["time"] = $now;
				$arr["token"] = $this->token;
				$arr["cat"] = 1;
				$arr["staffid"] = 0;
				$arr["usecount"] = 1;
				$set_exchange = M("Member_card_exchange")->where(array("cardid" => $cardid))->find();
				$arr["score"] = intval($set_exchange["reward"]) * $arr["expense"];
				$single_orderid = date("YmdHis", time()) . mt_rand(1000, 9999);
				$record["orderid"] = $single_orderid;
				$record["ordername"] = ($itemid == 0 ? "会员卡现金支付" : "现金支付除优惠劵外的款项");
				$record["paytype"] = "CardPay";
				$record["createtime"] = time();
				$record["paid"] = 0;
				$record["price"] = $arr["expense"];
				$record["token"] = $this->token;
				$record["wecha_id"] = $this->wecha_id;
				$record["company_id"] = $company_id;
				$record["type"] = 0;
				$record["cardid"] = $cardid;
				M("Member_card_coupon")->where(array("id" => $itemid))->setInc("usetime", 1);
				$result = M("Member_card_pay_record")->add($record);
				M("Member_card_coupon_record")->where($rwhere)->save(array("use_time" => time(), "is_use" => "1"));
				$this->redirect(U("CardPay/pay", array("from" => "Card", "token" => $this->token, "wecha_id" => $this->wecha_id, "price" => $arr["expense"], "single_orderid" => $single_orderid, "orderName" => "支付除优惠劵外多余款项", "redirect" => "Card/payReturn|itemid:" . $itemid . ",usecount:" . $arr["usecount"] . ",score:" . $arr["score"] . ",type:coupon,cardid:" . $cardid)));
				exit();
			}
			else {
				$staff_db = M("Company_staff");
				$staff_where = array("username" => $this->_post("username"), "token" => $this->token, "companyid" => $company_id);
				$thisStaff = $staff_db->where($staff_where)->find();

				if ($thisStaff) {
					$this->error("商家用户名不存在");
					exit();
				}

				if (md5($this->_post("password")) == $thisStaff["password"]) {
					$arr = array();
					$arr["itemid"] = $itemid;
					$arr["wecha_id"] = $this->wecha_id;
					$arr["expense"] = $price;
					$arr["time"] = $now;
					$arr["token"] = $this->token;
					$arr["cat"] = 0;
					$arr["notes"] = $this->_post("notes", "trim");
					$arr["staffid"] = $thisStaff["id"];
					$arr["usecount"] = 1;
					$arr["company_id"] = $company_id;
					$arr["cardid"] = $cardid;
					$set_exchange = M("Member_card_exchange")->where(array("cardid" => $cardid))->find();
					$arr["score"] = intval($set_exchange["reward"]) * $arr["expense"];
					$userArr = array();
					$userArr["total_score"] = $this->fans["total_score"] + $arr["score"];
					$userArr["expensetotal"] = $this->fans["expensetotal"] + $arr["expense"];
					M("Member_card_use_record")->add($arr);
					M("Userinfo")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->save($userArr);
					M("Member_card_coupon")->where(array("id" => $itemid))->setInc("usetime", 1);
					M("Member_card_coupon_record")->where($rwhere)->save(array("use_time" => time(), "is_use" => "1"));
					$this->success("支付成功");
					exit();
				}
				else {
					$this->error("商家密码错误!");
					exit();
				}
			}
		}
		else {
			$card = M("Member_card_create")->field("number")->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->find();
			$this->assign("card", $card);
			$company = M("Company")->where(array("token" => $this->token, "display" => 1))->order("isbranch asc,id desc")->select();
			$this->assign("company", $company);
			$this->assign("thisCard", $thisCard);
			$this->display();
		}
	}

	public function getCardCoupon()
	{
		$now = time();
		$cardid = $this->_get("cardid", "intval");
		$company_id = $this->_post("company_id", "intval");
		$where = array(
			"token"       => $this->token,
			"wecha_id"    => $this->wecha_id,
			"cardid"      => $cardid,
			"coupon_type" => array("lt", "3"),
			"is_use"      => "0"
			);
		$data = M("Member_card_coupon_record")->where($where)->field("id,cardid,coupon_id,coupon_type,add_time,is_use")->select();

		foreach ($data as $key => $value ) {
			$cwhere = array("token" => $this->token, "cardid" => $value["cardid"], "id" => $value["coupon_id"]);
			$cinfo = M("Member_card_coupon")->where($cwhere)->field("id,title,company_id,statdate,enddate")->find();
			if (($now < $cinfo["enddate"]) && ($cinfo["statdate"] < $now) && (($cinfo["company_id"] == 0) || ($cinfo["company_id"] == $company_id))) {
				$data[$key] = array_merge($cinfo, $value);
			}
			else {
				unset($data[$key]);
			}
		}

		if ($data) {
			echo json_encode(array("err" => 1, "msg" => ""));
		}
		else {
			echo json_encode(array("err" => 0, "res" => $data));
		}
	}

	public function payAction()
	{
		$price = $_POST["price"];
		$orderid = $this->_get("orderid");
		$record = M("Member_card_pay_record");
		if (($orderid == "") && ($price <= 0)) {
			$this->error("请填写正确的充值金额");
		}

		$token = $this->_get("token");
		$wecha_id = $this->wecha_id;

		if ($orderid != "") {
			$res = $record->where("token = '$token' AND wecha_id = '$wecha_id' AND orderid = $orderid AND paid = 0")->find();

			if ($res) {
				$this->success("提交成功，正在跳转支付页面..", U("Alipay/pay", array("from" => "Card", "orderName" => $res["ordername"], "single_orderid" => $res["orderid"], "token" => $res["token"], "wecha_id" => $res["wecha_id"], "price" => $res["price"])));
			}
			else {
				$this->error("无此订单");
			}
		}

		$_POST["wecha_id"] = $wecha_id;
		$_POST["token"] = $token;
		$_POST["createtime"] = time();
		$_POST["orderid"] = date("YmdHis", time()) . mt_rand(1000, 9999);
		$_POST["ordername"] = $_POST["number"] . " 充值";

		if ($record->create($_POST)) {
			if ($record->add($_POST)) {
				$this->success("提交成功，正在跳转支付页面..", U("Alipay/pay", array("from" => "Card", "orderName" => $_POST["ordername"], "single_orderid" => $_POST["orderid"], "token" => $_POST["token"], "wecha_id" => $_POST["wecha_id"], "price" => $price)));
			}
		}
		else {
			$this->error("系统错误");
		}
	}

	public function payReturn()
	{
		$wecha_id = $this->wecha_id;
		$token = $this->token;
		$record = M("member_card_pay_record");
		$orderid = trim($_GET["orderid"]);

		if ($_GET["nohandle"]) {
			$order = $record->where("orderid = '$orderid' AND token = '$token' AND wecha_id = '$wecha_id'")->find();
			$this->redirect(U("Card/card", array("token" => $this->token, "wecha_id" => $this->wecha_id, "cardid" => $order["cardid"])));
		}
		else {
			ThirdPayCard::index($orderid);
		}
	}

	public function payRecord()
	{
		$token = $this->token;
		$wecha_id = $this->wecha_id;
		$record = M("Member_card_pay_record");
		$member_card_set_db = M("Member_card_set");
		$thisCard = $member_card_set_db->where(array("token" => $token, "id" => intval($_GET["cardid"])))->find();
		$m = $this->_get("month", "intval");

		if ($m) {
			$m = date("m");
		}

		$nowY = date("Y");
		$start = strtotime($nowY . "-" . $m . "-01");
		$last = strtotime(date("Y-m-d", $start) . " +1 month -1 day");
		$list = $record->where("token = '$token' AND wecha_id = '$wecha_id' AND createtime < $last AND createtime > $start")->order("createtime DESC")->select();
		$balance = M("Userinfo")->field("balance")->where("token = '$token' AND wecha_id = '$wecha_id'")->find();
		$member_card_create_db = M("Member_card_create");
		$company_model = M("Company");
		$cardsCount = $member_card_create_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->count();
		$thisCompany = $company_model->where("token = '$token'")->find();
		$this->assign("thisCompany", $thisCompany);
		$this->assign("cardsCount", $cardsCount);
		$this->assign("year", $nowY);
		$this->assign("balance", $balance["balance"]);
		$this->assign("thisCard", $thisCard);
		$this->assign("list", $list);
		$this->assign("cardid", $this->_get("cardid", "intval"));
		$this->display();
	}
}


?>
