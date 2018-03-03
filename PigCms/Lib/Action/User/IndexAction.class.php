<?php
class IndexAction extends UserAction{
	//公众帐号列表
	public function index(){
		$oauthUrl	= '';
		$account	= M('Weixin_account')->where('type=1')->find();
		if(!empty($account['appId']) && !empty($account['appSecret']) && !empty($account['token']) && !empty($account['encodingAesKey'])){
			$apiOauth 	= new apiOauth();
			$redirect_uri 	= U('Index/oauth_back','','','',true);
			$oauthUrl  	= $apiOauth->start_authorization($redirect_uri);
			//echo $oauthUrl;die;
			$this->assign('oauthUrl',$oauthUrl);
		}
		
		if (class_exists('demoImport')&&!$oauthUrl){
			$this->assign('demo',1);
			//
			$token=$this->get_token();
			$pigSecret=$this->get_token(20,0,1);
			$wxinfo=M('wxuser')->where(array('uid'=>intval(session('uid'))))->find();
			if (!$wxinfo){
				$demoImport=new demoImport(session('uid'),$token,$pigSecret);
			}
			$wxinfo=M('wxuser')->where(array('uid'=>intval(session('uid'))))->find();
			$this->assign('wxinfo',$wxinfo);
			//
			$this->assign('token',$token);
		}
		//
		$where['uid']=session('uid');
		$group=D('User_group')->select();
		foreach($group as $key=>$val){
			$groups[$val['id']]['did']=$val['diynum'];
			$groups[$val['id']]['cid']=$val['connectnum'];
		}
		$this->assign(array('hasFuwu'=>$this->check_allow_Function('Fuwu'),'hasWeixin'=>$this->check_allow_Function('Weixin')));
		
		unset($group);
		$db=M('Wxuser');
		$count=$db->where($where)->count();
		$page=new Page($count,20);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		if ($info){
			foreach ($info as $item){
				if (!$item['appid']&&$apiinfo['appid']&&$apiinfo['appsecret']){
					$apiinfo=M('Diymen_set')->where(array('token'=>$item['token']))->find();
					$db->where(array('id'=>$item['id']))->save(array('appid'=>$apiinfo['appid'],'appsecret'=>$apiinfo['appsecret']));
				}else {
					$diymen=M('Diymen_set')->where(array('token'=>$item['token']))->find();
					if (!$diymen&&$item['appid']&&$item['appsecret']){
					M('Diymen_set')->add(array('token'=>$item['token'],'appid'=>$item['appid'],'appsecret'=>$item['appsecret']));
					}
				}
				//
			}
		}
		
		$this->assign('thisGroup',$this->userGroup);
		$this->assign('info',$info);
		$this->assign('group',$groups);
		$this->assign('page',$page->show());
		//
		if (!isset($_SESSION['closeAD'])){
			$ads=M('Renew')->where('id>0')->order('id DESC')->limit(0,5)->select();
			$thisAD=$ads[rand(0,4)];
			if ($thisAD['imgs']){
			$this->assign('thisAD',$thisAD);
			}
		}
		
		//
		if (count($info)==1&&$info[0]['wxid']=='demo'&&class_exists('demoImport')&&!$oauthUrl){
			$this->assign('info',$info[0]);
			$this->display('bindTip');
		}else {
			$this->display();
		}
	}

	public function closeAD(){
		$_SESSION['closeAD']=1;
	}
	//
	public function get_token($randLength=6,$attatime=1,$includenumber=0){
		if ($includenumber){
			$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
		}else {
			$chars='abcdefghijklmnopqrstuvwxyz';
		}
		$len=strlen($chars);
		$randStr='';
		for ($i=0;$i<$randLength;$i++){
			$randStr.=$chars[rand(0,$len-1)];
		}
		$tokenvalue=$randStr;
		if ($attatime){
			$tokenvalue=$randStr.time();
		}
		return $tokenvalue;
	}
	//添加公众帐号
	public function add(){
		$randLength=6;
		$chars='abcdefghijklmnopqrstuvwxyz';
		$len=strlen($chars);
		$randStr='';
		for ($i=0;$i<$randLength;$i++){
			$randStr.=$chars[rand(0,$len-1)];
		}
		$tokenvalue=$randStr.time();
		$this->assign('tokenvalue',$tokenvalue);
		$this->assign('email',time().'@yourdomain.com');
		//地理信息
		if (C('baidu_map_api')){
			//$locationInfo=json_decode(file_get_contents('http://api.map.baidu.com/location/ip?ip='.$_SERVER['REMOTE_ADDR'].'&coor=bd09ll&ak='.C('baidu_map_api')),1);
			///$this->assign('province',$locationInfo['content']['address_detail']['province']);
			//$this->assign('city',$locationInfo['content']['address_detail']['city']);
			//var_export($locationInfo);
		}
	
		$this->display();
	}
	public function edit(){

		$id=$this->_get('id','intval');
		$where['uid']=session('uid');
		$where['id']=$id;
		$res=M('Wxuser')->where($where)->find();
		$this->assign('info',$res);
		$this->assign('fuwu',$this->check_allow_Function('Fuwu'));
		$this->display();
	}
	
	public function bindEdit(){
		$where['uid']=session('uid');
		$res=M('Wxuser')->where($where)->find();
		$this->assign('info',$res);
		$this->display();
	}
	
	public function del(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		$thisWxUser=M('Wxuser')->where(array('id'=>$where['id']))->find();
		$users=M('Users')->where(array('id'=>$thisWxUser['uid']))->find();
		if(D('Wxuser')->where($where)->delete()){
			M('Users')->field('wechat_card_num')->where(array('id'=>session('uid')))->setDec('wechat_card_num');
			if ($this->isAgent){
				$wxuserCount=M('Wxuser')->where(array('agentid'=>$this->thisAgent['id']))->count();
				M('Agent')->where(array('id'=>$this->thisAgent['id']))->save(array('wxusercount'=>$wxuserCount));
				if ($this->thisAgent['wxacountprice']&&time()-$thisWxUser['createtime']<5*24*3600){
					
					$pricebyMonth=intval($this->thisAgent['wxacountprice'])/12;
					$month=($users['viptime']-time())/(30*24*3600);
					$month=intval($month);
					$price=$month*$pricebyMonth;
					//
					M('Agent')->where(array('id'=>$this->thisAgent['id']))->setInc('moneybalance',$price);
					M('Agent_expenserecords')->add(array('agentid'=>$this->thisAgent['id'],'amount'=>$price,'des'=>$this->user['username'].'(uid:'.$this->user['id'].')删除公众号'.$thisWxUser['wxname'].'_'.$month.'个月','status'=>1,'time'=>time()));
				}
			}
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function apiInfo(){
		$thisWx=apiInfo::info($this->user['id'],intval($_GET['id']));
		$this->assign('info',$thisWx);




		if($this->check_allow_Function('Fuwu') && ALI_FUWU_GROUP && $thisWx['fuwuappid'])
		{
			$this->assign('fuwu',true);
		}
		else
		{
			$this->assign('fuwu',false);
		}
		$this->display();
	}
	public function upsave(){
		S('wxuser_'.$this->token,NULL);
		M('Diymen_set')->where(array('token'=>$this->token))->save(array('appid'=>trim($this->_post('appid')),'appsecret'=>trim($this->_post('appsecret'))));
		//
		$db=D('Wxuser');
		if (isset($_POST['demoStep'])){
			$back='/bindHelp?id='.intval($_POST['id']);
		}else {
			$back='';
		}
		if($db->create()===false){
			$this->error($db->getError());
		}else{
			$id=$db->save();
			if($id){
				if (isset($_POST['demoStep'])){
					header('Location:'.$this->siteUrl.U('Index/bindHelp',array('id'=>intval($_POST['id']))));
				}else {
					$appid 	= $this->_post('appid','trim');
					M('Wxuser')->where(array('appid'=>$appid,'id'=>array('neq',$this->_post('id','intval'))))->save(array('appid'=>$appid.'_no'));
					$this->success('操作成功',U('index'));
				}
				
			}else{
				$this->error('操作失败',U('index'));
			}
		}
		
	}
	
	public function insert(){
		if ($this->_post('biz','intval') && C('open_biz') == 1) {
			$_POST['wxid'] = uniqid('id_');
			$_POST['weixin'] = uniqid('wx_');
			$_POST['ifbiz'] = 1;

		}else if($this->_post('goldbuy','intval')){
			$_POST['wxid'] = uniqid('id_');
			$_POST['weixin'] = uniqid('wx_');
		}
		$data=M('User_group')->field('wechat_card_num')->where(array('id'=>session('gid')))->find();
		$users=M('Users')->where(array('id'=>session('uid')))->find();
		//
		if ($this->isAgent){
			$needPay=0;
			if (($users['viptime']-$users['createtime']-20)>$this->reg_validDays*24*3600||$this->reg_validDays>30){
				$needPay=1;
			}
			if ($needPay){
				$pricebyMonth=intval($this->thisAgent['wxacountprice'])/12;
				$month=($users['viptime']-time())/(30*24*3600);
				$month=intval($month);
				$price=$month*$pricebyMonth;
				$price=intval($price);
				if ($price>$this->thisAgent['moneybalance']){
					$this->error('请联系您的站长处理');
				}
			}
		}
		// 
		if($users['wechat_card_num']<$data['wechat_card_num']){
			
		}else{
			$this->error('您的VIP等级所能创建的公众号数量已经到达上线，请购买后再创建',U('User/Index/index'));exit();
		}
		//$this->alli_nsert('Wxuser');
		//
		$db=D('Wxuser');
		if ($this->isAgent){
			$_POST['agentid']=$this->thisAgent['id'];
		}
		//
		$randLength=43;
		$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$len=strlen($chars);
		$randStr='';
		for ($i=0;$i<$randLength;$i++){
			$randStr.=$chars[rand(0,$len-1)];
		}
		$aeskey=$randStr;
		$_POST['aeskey']=$aeskey;
		$_POST['encode']=0;
		//
		$pigSecret=$this->get_token(20,0,1);
		$_POST['pigsecret']=$pigSecret;
		if($db->create()===false){
			$this->error($db->getError());
		}else{
			$id=$db->add();
			if($id){
				if ($this->isAgent){
					$wxuserCount=M('Wxuser')->where(array('agentid'=>$this->thisAgent['id']))->count();
					M('Agent')->where(array('id'=>$this->thisAgent['id']))->save(array('wxusercount'=>$wxuserCount));
					if ($this->thisAgent['wxacountprice']){
						//试用期内不扣费
						if ($needPay){
							$pricebyMonth=intval($this->thisAgent['wxacountprice'])/12;
							$month=($users['viptime']-time())/(30*24*3600);
							$month=intval($month);
							$price=$month*$pricebyMonth;
							M('Agent')->where(array('id'=>$this->thisAgent['id']))->setDec('moneybalance',$price);
							M('Agent_expenserecords')->add(array('agentid'=>$this->thisAgent['id'],'amount'=>(0-$price),'des'=>$this->user['username'].'(uid:'.$this->user['id'].')添加公众号'.$_POST['wxname'].'('.$month.'个月)','status'=>1,'time'=>time()));
						}
					}
				}
				M('Users')->field('wechat_card_num')->where(array('id'=>session('uid')))->setInc('wechat_card_num');
				$this->addfc();
				M('Diymen_set')->add(array('appid'=>trim($this->_post('appid')),'token'=>$this->_post('token'),'appsecret'=>trim($this->_post('appsecret'))));
				//
				$appid 	= $this->_post('appid','trim');
				M('Wxuser')->where(array('appid'=>$appid,'id'=>array('neq',$id)))->save(array('appid'=>$appid.'_no'));
				$this->success('操作成功',U('Index/index'));
			}else{
				$this->error('操作失败',U('Index/index'));
			}
		}
		
	}
	
	//功能
	public function autos(){
		$this->display();
	}
	
	public function addfc(){
		$token_open=M('Token_open');
		$open['uid']=session('uid');
		$open['token']=$_POST['token'];
		$gid=session('gid');
		if (C('agent_version')&&$this->agentid){
			//$fun=M('Agent_function')->field('funname,gid,isserve')->where('`gid` <= '.$gid.' AND agentid='.$this->agentid)->select();
			$fun=M('User_group')->field('func')->where('`id` = '.$gid.' AND agentid='.$this->agentid)->select();
		}else {
			//$fun=M('Function')->field('funname,gid,isserve')->where('`gid` <= '.$gid)->select();
			$fun=M('User_group')->field('func')->where('`id` = '.$gid)->select();
		}
		foreach($fun as $key=>$vo){
			$queryname.=$vo['func'].',';
		}
		$open['queryname']=rtrim($queryname,',');
		$token_open->data($open)->add();
	}
	
	public function usersave(){
		$pwd=$this->_post('password');
		if($pwd!=false){
			$data['password']=md5($pwd);
			$data['id']=$_SESSION['uid'];
			if(M('Users')->save($data)){
				$this->success('密码修改成功！',U('Index/index'));
			}else{
				$this->error('密码修改失败！',U('Index/index'));
			}
		}else{
			$this->error('密码不能为空!',U('Index/useredit'));
		}
	}
	//处理关键词
	public function handleKeywords(){
		$Model = new Model();
		//检查system表是否存在
		$keyword_db=M('Keyword');
		$count = $keyword_db->where('pid>0')->count();
		//
		$i=intval($_GET['i']);
		//
		if ($i<$count){
			$img_db=M($data['module']);
			$back=$img_db->field('id,text,pic,url,title')->limit(9)->order('id desc')->where($like)->select();
			//
			$rt=$Model->query("CREATE TABLE IF NOT EXISTS `tp_system_info` (`lastsqlupdate` INT( 10 ) NOT NULL ,`version` VARCHAR( 10 ) NOT NULL) ENGINE = MYISAM CHARACTER SET utf8");
			$this->success('关键词处理中:'.$row['des'],'?g=User&m=Create&a=index');
		}else {
			exit('更新完成，请测试关键词回复');
		}
	}
	public function bindHelp(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		$thisWx=apiInfo::info($this->user['id'],intval($_GET['id']));
		$this->assign('wxuser',$thisWx);
		$this->display();
	}
	public function bindTip(){
		if (class_exists('demoImport')){
			$this->assign('demo',1);
			//
			$token=$this->get_token();
			$pigSecret=$this->get_token(20,0,1);
			$wxinfo=M('wxuser')->where(array('uid'=>intval(session('uid'))))->find();
			if (!$wxinfo){
				$demoImport=new demoImport(session('uid'),$token,$pigSecret);
			}
			$wxinfo=M('wxuser')->where(array('uid'=>intval(session('uid'))))->find();
			$this->assign('wxinfo',$wxinfo);
			//
			$this->assign('token',$token);
		}
		$this->display();
	}
	public function switchTPl()
	{


		if(IS_AJAX){
			$id = (int)$this->_post('value');

			if($id == 1){
				if(M("Users")->where(array("id"=>(int)session('uid')))->setField('usertplid',1)){
					echo 200;
				}else{
					echo 500;
				}

			}else{
				if(M("Users")->where(array("id"=>(int)session('uid')))->setField('usertplid',0)){
					echo 200;
				}else{
					echo 500;
				}
			}

		}else{
			$this->display();
		}
	}


	public function qiye(){
		$where = array('uid' => intval(session('uid')), 'id' => intval($_GET['id']));
		$wxinfo=M('Wxuser')->where($where)->find();
		if(empty($wxinfo)){
			$this->error('参数错误,请稍后再试~');
		}
		$oa = new oa($wxinfo);
		$url = $oa->url();

		$this->assign("oaurl",$url);
		$this->display();
	}
	public function help(){
		$this->assign('helpParm',$_GET['helpParm']);
		$this->display();
	}
	
	
	public function oauth(){ //发起授权
		$apiOauth 	= new apiOauth();
		$redirect_uri 	= U('Index/oauth_back','','','',true);
		$oauthUrl  	= $apiOauth->start_authorization($redirect_uri);
		//echo $oauthUrl;die;
		$this->assign('oauthUrl',$oauthUrl);
		$this->display();
	}
	
	public function oauth_back(){
		$ac_id 		= intval($_GET['ac_id']);
		$auth_code 	= $_GET['auth_code'];
		$expires_in = $_GET['expires_in'];
		if(!empty($auth_code) && !empty($expires_in)){
			
			$apiOauth 	= new apiOauth();
			
			$authorization_info = $apiOauth->get_authorization_info($auth_code);
			
			$authorizer_info 	= $apiOauth->get_authorizer_info($authorization_info['authorizer_appid']);
			
			$appid 				= $authorization_info['authorizer_appid'];
			
			$where 	= array('uid'=>session('uid'));
			
			if(!empty($ac_id)){
				$where['id'] 	= $ac_id;
			}else{
				$where['appid'] = $appid;
			}
			
			//file_put_contents('authorization_info.txt',json_encode($authorization_info));
			//file_put_contents('authorizer_info.txt',json_encode($authorizer_info));
			$wxinfo 	= M('Wxuser')->where($where)->find();
			if($wxinfo){
				$save 	= array();
				$save['type'] 			= 1;
				$save['encode'] 		= 2;
				$save['wxid'] 			= $authorizer_info['user_name'];
				$save['wxname'] 		= $authorizer_info['nick_name'];
				$save['weixin'] 		= $authorizer_info['alias'];
				$save['headerpic'] 		= empty($authorizer_info['head_img'])?'':$authorizer_info['head_img'];
				
				$service_type 		= $authorizer_info['service_type_info']['id'];
				$verify_type		= $authorizer_info['verify_type_info']['id'];
				
				if(($service_type == 0 || $service_type == 1 ||  $service_type == 2) && $verify_type == 0){
					$save['winxintype'] = 3;
				}else if($service_type == 2 && $verify_type == -1){
					$save['winxintype'] = 2;
				}else if(($service_type == 0 || $service_type == 1) && $verify_type == -1){
					$save['winxintype'] = 1;
				}
				
				$save['appid'] 						= $authorization_info['authorizer_appid'];
				$save['authorizer_access_token'] 	= $authorization_info['authorizer_access_token'];
				$save['authorizer_refresh_token'] 	= $authorization_info['authorizer_refresh_token'];
				$save['authorizer_expires'] 		= $authorization_info['expires_in']+time();
				
				if(M('Wxuser')->where($where)->save($save)){

					$update 	= array(
						'appid'	=> $save['appid'].'_no',
						'authorizer_access_token'=>'',
						'authorizer_refresh_token'=>'',
						'authorizer_expires'=>0
					);
					M('Wxuser')->where("appid = '{$save['appid']}' AND id != {$wxinfo['id']}")->save($update);
					$status 	= true;
				}
				
			}else{
				$status 	= $this->add_authorizer($authorizer_info,$authorization_info);
			}
	
			if($status){
				$this->success('公众号授权成功',U('Index/index'));
			}else{
				$this->error('公众号授权失败',U('Index/index'));
			}
		}else{
			$this->error('授权错误',U('Index/oauth'));
		}
	}
	
	
	public function add_authorizer($authorizer_info,$authorization_info){
		$res 	= array();
		$res['type'] 		= 1;//公众号管理类型0 手动添加，1 第三方授权
		$res['uid'] 		= session('uid');
		$res['wxid'] 		= $authorizer_info['user_name'];
		$res['wxname'] 		= $authorizer_info['nick_name'];
		$res['weixin'] 		= $authorizer_info['alias'];
		$res['headerpic'] 	= empty($authorizer_info['head_img'])?'':$authorizer_info['head_img'];
		$service_type 		= $authorizer_info['service_type_info']['id'];
		$verify_type		= $authorizer_info['verify_type_info']['id'];
		
		if(($service_type == 0 || $service_type == 1 ||  $service_type == 2) && $verify_type == 0){
			$res['winxintype'] = 3;
		}else if($service_type == 2 && $verify_type == -1){
			$res['winxintype'] = 2;
		}else if(($service_type == 0 || $service_type == 1) && $verify_type == -1){
			$res['winxintype'] = 1;
		}
		
		$res['appid'] 						= $authorization_info['authorizer_appid'];
		$res['authorizer_access_token'] 	= $authorization_info['authorizer_access_token'];
		$res['authorizer_refresh_token'] 	= $authorization_info['authorizer_refresh_token'];
		$res['authorizer_expires'] 			= $authorization_info['expires_in']+time();
		
		
		$data 	= M('User_group')->field('wechat_card_num')->where(array('id'=>session('gid')))->find();
		$users 	= M('Users')->where(array('id'=>session('uid')))->find();

		if ($this->isAgent){
			$needPay=0;
			if (($users['viptime']-$users['createtime']-20)>$this->reg_validDays*24*3600||$this->reg_validDays>30){
				$needPay=1;
			}
			if ($needPay){
				$pricebyMonth=intval($this->thisAgent['wxacountprice'])/12;
				$month=($users['viptime']-time())/(30*24*3600);
				$month=intval($month);
				$price=$month*$pricebyMonth;
				$price=intval($price);
				if ($price>$this->thisAgent['moneybalance']){
					$this->error('请联系您的站长处理');
				}
			}
		}

		if($users['wechat_card_num']>=$data['wechat_card_num']){
			$this->error('您的VIP等级所能创建的公众号数量已经到达上线，请购买后再创建',U('User/Index/index'));exit();
		}

		$db=D('Wxuser');
		
		if ($this->isAgent){
			$res['agentid']=$this->thisAgent['id'];
		}
		
		$randLength=43;
		$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$len=strlen($chars);
		$randStr='';
		for ($i=0;$i<$randLength;$i++){
			$randStr.=$chars[rand(0,$len-1)];
		}
		$aeskey=$randStr;
		$res['aeskey']=$aeskey;
		$res['encode']=2;

		$randLength=6;
		$chars='abcdefghijklmnopqrstuvwxyz';
		$len=strlen($chars);
		$randStr='';
		for ($i=0;$i<$randLength;$i++){
			$randStr.=$chars[rand(0,$len-1)];
		}
		$tokenvalue=$randStr.time();
		$res['token'] 		= $tokenvalue;
	
		$pigSecret=$this->get_token(20,0,1);
		$res['pigsecret'] 	= $pigSecret;

		$id=$db->add($res);
		if (C('server_topdomain')=='pigcms.cn'){
			$demoImport=new demoImport(session('uid'),$res['token'],$pigSecret,$id);
		}
		
		if($id){
			if ($this->isAgent){
				$wxuserCount=M('Wxuser')->where(array('agentid'=>$this->thisAgent['id']))->count();
				M('Agent')->where(array('id'=>$this->thisAgent['id']))->save(array('wxusercount'=>$wxuserCount));
				if ($this->thisAgent['wxacountprice']){
					//试用期内不扣费
					if ($needPay){
						$pricebyMonth=intval($this->thisAgent['wxacountprice'])/12;
						$month=($users['viptime']-time())/(30*24*3600);
						$month=intval($month);
						$price=$month*$pricebyMonth;
						M('Agent')->where(array('id'=>$this->thisAgent['id']))->setDec('moneybalance',$price);
						M('Agent_expenserecords')->add(array('agentid'=>$this->thisAgent['id'],'amount'=>(0-$price),'des'=>$this->user['username'].'(uid:'.$this->user['id'].')添加公众号'.$res['wxname'].'('.$month.'个月)','status'=>1,'time'=>time()));
					}
				}
			}
			$save 	= array(
				'appid'	=> $res['appid'].'_no',
				'authorizer_access_token'=>'',
				'authorizer_refresh_token'=>'',
				'authorizer_expires'=>0
			);
			M('Wxuser')->where("appid='{$res['appid']}' AND id != $id")->save($save);
			return true;
		}else{
			return false;
		}
	}
	public function preview(){
		$pretoken=trim($_GET['token']);
		$this->assign('pretoken',$pretoken);
		$this->display();
	}
    
    public function InsertDeviceToken(){             
        $devicetoken = trim($_GET['devicetoken']);
        $apptoken = trim($_GET['apptoken']);
        if(!empty($devicetoken)&& !empty($apptoken)){
            $count=M('devicetoken')->where(array('devicetoken'=>$devicetoken,'apptoken'=>$apptoken))->count();
            if($count<1){
                M('devicetoken')->add(array('devicetoken'=>$devicetoken,'apptoken'=>$apptoken));
            }
        }
    }
}
?>