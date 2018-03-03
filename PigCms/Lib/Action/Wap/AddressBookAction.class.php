<?php
class AddressBookAction extends WapAction{
	private $randstr = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

	public function _initialize(){
		parent::_initialize();

		$mainCompany = M('Company')->where("`token`='{$this->token}' AND `isbranch`=0")->find();
		$cid = $mainCompany['id'];
		$proset = M("Product_setting")->where(array('token' => $this->token, 'cid' => $cid))->find();
		$this->assign('productSet', $proset);
	}

	public function index(){
		$token = $this->token;
		$session_username_name = "token_username_" . $this->token;
		$username=$_SESSION[$session_username_name];
		$userInfo = M('Userinfo')->where(array('username' => $username, 'token'=>$this->token))->find();
		$userInfoex = M('User_extinfo')->where(array('uid' => $userInfo['id'],'token'=>$this->token))->find();
		if($userInfoex){
			$truename = "";
			$indusid = $_GET['indusid'];
			$indus = include('./PigCms/Lib/ORG/Industry.php');
			$induscat = $indus['allcat'];
			if(IS_POST){
				$truename = isset($_POST['search_name']) ? htmlspecialchars($_POST['search_name']) : '';
			}
			$where 	= array('token'=>$this->token);
			$where['status']=1;
			if($indusid){
				foreach ($induscat as $r) {
					if($r['id']==$indusid){
						$strindusname=$r['name'];
					}
				}
				$where['industry']=array('like','%'.$strindusname.'%');
			}
			if($truename){
				$where['truename']=array('like','%'.$truename.'%');
			}

			$userextinfo_db = M("User_extinfo");
			$userextinfos = $userextinfo_db->where($where)->select();
	    	$userextinfouid = array();
			$this->assign('userextinfo',$userextinfos);
			foreach ($userextinfos as $rex) {
				array_push($userextinfouid, $rex['uid']);
			}

			$userinfo_db = M("Userinfo");
			$userinfo = $userinfo_db->where(array('token'=>"$token"))->select();
			$userinfofin=array();
			foreach ($userinfo as $row) {
				if(in_array($row['id'], $userextinfouid)){
					$userinfofin[$row['id']]=$row;
				}
			}
			$this->assign('userinfo',$userinfofin);
			$this->assign('induscat',$induscat);
			$this->assign('token',$token);
			$this->assign('indusid',$indusid);
			$this->assign('keyword',$truename);
			$this->display('1_index');
		}else{
			$this->redirect(U('Index/memberLogin', array('token' => $token)));
		}
	}

	public function regist()
	{
		$token = $this->token;
		$set_db=M('Custom_set');
		$set_info = $set_db->where(array('token'=>$token,'keyword'=>'AdrBk'))->find();

		if (IS_POST) {
			$truename = isset($_POST['truename']) ? htmlspecialchars($_POST['truename']) : '';
			$sex = isset($_POST['sex']) ? htmlspecialchars($_POST['sex']) : '';
			$tele = isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '';
			$qqnum = isset($_POST['qqnum']) ? htmlspecialchars($_POST['qqnum']) : '';
			$paddress = isset($_POST['paddress']) ? htmlspecialchars($_POST['paddress']) : '';
			$compname = isset($_POST['compname']) ? htmlspecialchars($_POST['compname']) : '';
			$duty = isset($_POST['duty']) ? htmlspecialchars($_POST['duty']) : '';
			$laincome = isset($_POST['laincome']) ? htmlspecialchars($_POST['laincome']) : '';
			$refer = isset($_POST['refer']) ? htmlspecialchars($_POST['refer']) : '';
			$refertel = isset($_POST['refertel']) ? htmlspecialchars($_POST['refertel']) : '';
			
			if (empty($truename)) {
				$this->error("姓名不能为空!");
			}
			if (empty($tele)) {
				$this->error("手机号码不能为空!");
			}
			if (empty($compname)) {
				$this->error("企业名称不能为空!");
			}

			if($this->wecha_id){
				$userInfo = M('Userinfo')->where(array('wecha_id' => $this->wecha_id,'token'=>$this->token))->find();
			}elseif($this->twid){
				$userInfo = M('Userinfo')->where(array('twid' => $this->twid,'token'=>$this->token))->find();
			}
			if($userInfo){
				$userInfoex1 = M('User_extinfo')->where(array('truename' => $truename,'token'=>$this->token,'tel'=>$tele))->find();
				$userInfoex2 = M('User_extinfo')->where(array('uid' => $userInfo['id'],'token'=>$this->token))->find();
				if($userInfoex1 or $userInfoex2){
					$this->error('您已经注册过，我们会与您联系');
				}else{
	                D('Userinfo')->where(array('id' => $userInfo['id']))->save(array('token'=>$this->token, 'truename' => $truename, 'password' => md5('123456'), 'tel' => $tele, 'qq' => $qqnum, 'sex' => $sex,'username'=>$tele));
	                D("User_extinfo")->add(array('uid' => $userInfo['id'], 'token' => $this->token, 'status' => 0, 'truename' => $truename, 'sex' => $sex, 'tel' => $tele, 
	                	'qq' => $qqnum, 'paddress' => $paddress, 'compname' => $compname, 'compduty' => $duty, 'lastyearincome'=> $laincome,
	                	'referrer' => $refer, 'referrertel' => $refertel, 'regtime'=>time()));
	                $this->success($set_info['succ_info'], U('Index/index', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'twid' => $this->_twid)));
				}
			}else{
				$userInfoex = M('User_extinfo')->where(array('truename' => $truename,'token'=>$this->token,'tel'=>$tele))->find();
				if($userInfoex){
					$this->error('您已经注册过，我们会与您联系');
				}else{
	                $uid = D("Userinfo")->add(array('token'=>$this->token, 'truename' => $truename, 'password' => md5('123456'), 'tel' => $tele, 'qq' => $qqnum, 'sex' => $sex,'username'=>$tele));
	                $twid = $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)} . $uid;
	                D('Userinfo')->where(array('id' => $uid))->save(array('twid' => $twid));
	                session('twid', $twid);
	                $this->twid=$twid;
	                $this->assign('twid',$twid);
	                D("User_extinfo")->add(array('uid' => $uid, 'token' => $this->token, 'status' => 0, 'truename' => $truename, 'sex' => $sex, 'tel' => $tele, 
	                	'qq' => $qqnum, 'paddress' => $paddress, 'compname' => $compname, 'compduty' => $duty, 'lastyearincome'=> $laincome,
	                	'referrer' => $refer, 'referrertel' => $refertel, 'regtime'=>time()));
	            }
	            $this->success($set_info['succ_info'], U('Index/index', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'twid' => $this->_twid)));
			}
		}else{
			$this->assign('set_info',$set_info);
			$this->display('regist');
		}
	}

	public function visitcard(){
		$token = $this->token;
		$uid = $_GET['uid'];
		$session_username_name = "token_username_" . $this->token;
		$username=$_SESSION[$session_username_name];
		$userInfome = M('Userinfo')->where(array('username' => $username,'token'=>$this->token))->find();

		$userextinfo_db = M("User_extinfo");
		$userextinfo = $userextinfo_db->where(array('token'=>"$token",'uid'=>"$uid"))->find();
		$this->assign('userextinfo',$userextinfo);

		$userinfo_db = M("Userinfo");
		$userinfo = $userinfo_db->where(array('token'=>"$token",'id'=>"$uid"))->find();
		$this->assign('userinfo',$userinfo);

		$leave_model =M("leave");
		$where = array("token"=>$this->token,'checked'=>0,'type'=>'m_vcard','message'=>$userInfome['id'],'wecha_id'=>$userinfo['wecha_id']);
        $count = $leave_model->where($where)->count();
        if($count>0){
        	$res = $leave_model->where($where)->save(array('checked'=>1));
        }

        $where = array();
        $where['token']= array('eq',$token);
        $where['uid']= array('in',$userinfo['wecha_id'].','.$userinfo['twid']);
        $forumtopics=M('Forum_topics')->where($where)->select();
        $forumconf=M('Forum_config')->where(array('token'=>$token))->select();
        $forumboard=array();
        foreach ($forumconf as $row) {
        	$forumboard[$row['id']]=$row;
        }

		// $indus = include('./PigCms/Lib/ORG/Industry.php');
		// $induscat = $indus['allcat'];
		// foreach ($induscat as $r) {
		// 	if($r['id']==$userextinfo['industry']){
		// 		$curindus=$r['name'];
		// 	}
		// }
		// $this->assign('curindus',$curindus);
		$this->assign('token',$token);
		$this->assign('forumtopics',$forumtopics);
		$this->assign('forumboard',$forumboard);
		$this->display('1_visitcard');
	}

	public function visitcardme(){
		$token = $this->token;
		$session_username_name = "token_username_" . $this->token;
		$username=$_SESSION[$session_username_name];
		$userInfo = M('Userinfo')->where(array('username' => $username,'token'=>$this->token))->find();
		$userInfoex = M('User_extinfo')->where(array('uid' => $userInfo['id'],'token'=>$this->token))->find();
		$uid=$userInfoex['uid'];
		if($uid){
			$userextinfo_db = M("User_extinfo");
			$userextinfo = $userextinfo_db->where(array('token'=>"$token",'uid'=>"$uid"))->find();
			$this->assign('userextinfo',$userextinfo);

			$userinfo_db = M("Userinfo");
			$userinfo = $userinfo_db->where(array('token'=>"$token",'id'=>"$uid"))->find();
			$this->assign('userinfo',$userinfo);

			// $indus = include('./PigCms/Lib/ORG/Industry.php');
			// $induscat = $indus['allcat'];
			// foreach ($induscat as $r) {
			// 	if($r['id']=$userextinfo['industry']){
			// 		$curindus=$r['name'];
			// 	}
			// }

         	$leave_model =M("leave");
            $where = array("token"=>$this->token,'checked'=>0,'type'=>'m_vcard','message'=>$uid);
            $count = $leave_model->where($where)->count();// 查询满足要求的总记录数

            $forumtopics=M('Forum_topics')->where(array('token'=>$token,'uid'=>$this->wecha_id))->select();
            $forumconf=M('Forum_config')->where(array('token'=>$token))->select();
            $forumboard=array();
            foreach ($forumconf as $row) {
            	$forumboard[$row['id']]=$row;
            }

			//$this->assign('curindus',$curindus);
			$this->assign('token',$token);
			$this->assign('uid',$uid);
			$this->assign('vcardcnt',$count);
			$this->assign('forumtopics',$forumtopics);
			$this->assign('forumboard',$forumboard);
			$this->display('1_visitcardme');
		}else{
			$this->redirect(U('Index/login', array('token' => $token)));
		}
	}

	public function meedit(){
		$token = $this->token;
		$session_username_name = "token_username_" . $this->token;
		$username=$_SESSION[$session_username_name];
		$userInfo = M('Userinfo')->where(array('username' => $username,'token'=>$this->token))->find();
		$userInfoex = M('User_extinfo')->where(array('uid' => $userInfo['id'],'token'=>$this->token))->find();
		$uid=$userInfoex['uid'];
		if (IS_POST) {
			$portrait = isset($_POST['portrait']) ? htmlspecialchars($_POST['portrait']) : '';
			// $skillexp = isset($_POST['skillexp']) ? htmlspecialchars($_POST['skillexp']) : '';
			// $indus = isset($_POST['indus']) ? htmlspecialchars($_POST['indus']) : '';
			// $compintro = isset($_POST['compintro']) ? htmlspecialchars($_POST['compintro']) : '';
			// $focusindus = isset($_POST['focusindus']) ? htmlspecialchars($_POST['focusindus']) : '';
			// $selfintro = isset($_POST['selfintro']) ? htmlspecialchars($_POST['selfintro']) : '';
			// $socialduty = isset($_POST['socialduty']) ? htmlspecialchars($_POST['socialduty']) : '';
			// $hobby = isset($_POST['hobby']) ? htmlspecialchars($_POST['hobby']) : '';
			// $city = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '';
			// $learnexp = isset($_POST['learnexp']) ? htmlspecialchars($_POST['learnexp']) : '';
			$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
			$tel = isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '';
			//$linkname = isset($_POST['linkname']) ? htmlspecialchars($_POST['linkname']) : '';
			//$linkphone = isset($_POST['linkphone']) ? htmlspecialchars($_POST['linkphone']) : '';

            D('Userinfo')->where(array('id' => $uid))->save(array('portrait'=>$portrait));
            D("User_extinfo")->where(array('uid' => $uid))->save(array('email'=>$email, 'tel' => $tel));
            $this->success('修改成功', U('AddressBook/visitcardme', array('token' => $this->token, 'uid'=>$uid)));
		}else{
			$userextinfo_db = M("User_extinfo");
			$userextinfo = $userextinfo_db->where(array('token'=>$token,'uid'=>$uid))->find();
			$this->assign('userextinfo',$userextinfo);

			$userinfo_db = M("Userinfo");
			$userinfo = $userinfo_db->where(array('token'=>$token,'id'=>$uid))->find();
			$this->assign('userinfo',$userinfo);

			$indus = include('./PigCms/Lib/ORG/Industry.php');
			$induscat = $indus['allcat'];
			$this->assign('induscat',$induscat);
			$this->assign('token',$token);
			$this->display('meedit');
		}
	}

	public function passedit(){
		$token = $this->token;
		$session_username_name = "token_username_" . $this->token;
		$username=$_SESSION[$session_username_name];
		$userInfo = M('Userinfo')->where(array('username' => $username,'token'=>$this->token))->find();
		$userInfoex = M('User_extinfo')->where(array('uid' => $userInfo['id'],'token'=>$this->token))->find();
		$uid=$userInfoex['uid'];
		if (IS_POST) {
			if (empty($_POST['password'])) {
				unset($_POST['password']);
				unset($_POST['password2']);
			}else{
				if ($_POST['password'] != $_POST['password2']) {
					$this->error("两次密码不一致");
				}else{
					$_POST['password'] = md5($_POST['password']);
				}
			}
			$sql = D('Userinfo')->where(array('id' => $uid,'token'=>$token))->save(array('password' => $_POST['password']));
			if($sql){
				$this->success('保存成功',U('AddressBook/visitcardme', array('token' => $this->token, 'uid'=>$uid)));
			}else{
				$this->error('保存失败，请重新尝试',U('AddressBook/passedit', array('token' => $this->token, 'uid'=>$uid)));
			}	
		}else{
			$this->assign('Userinfo',$userInfo);
			$this->assign('Userinfoex',$userInfoex);
			$this->display('passedit');
		}
	}
	public function sendcard(){
		$token = $this->token;
		$session_username_name = "token_username_" . $this->token;
		$username=$_SESSION[$session_username_name];
		$userInfo = M('Userinfo')->where(array('username' => $username, 'token'=>$this->token))->find();
		$userInfoex = M('User_extinfo')->where(array('uid' => $userInfo['id'],'token'=>$this->token))->find();
		if($userInfoex){
			$truename = "";
			$indusid = $_GET['indusid'];
			$indus = include('./PigCms/Lib/ORG/Industry.php');
			$induscat = $indus['allcat'];
			if(IS_POST){
				$truename = isset($_POST['search_name']) ? htmlspecialchars($_POST['search_name']) : '';
			}
			$where 	= array('token'=>$this->token);
			$where['status']=1;
			if($indusid){
				foreach ($induscat as $r) {
					if($r['id']==$indusid){
						$strindusname=$r['name'];
					}
				}
				$where['industry']=array('like','%'.$strindusname.'%');
			}
			if($truename){
				$where['truename']=array('like','%'.$truename.'%');
			}
			$getuid=$_GET['uid'];
			if($getuid){
		        $msgarr = array();
		        $msgarr['checked'] = 0;
		        $msgarr['name'] =$userInfoex['truename'];
		        $msgarr['message'] = $getuid;
		        $msgarr['wecha_id'] = $userInfo['wecha_id'];
		        $msgarr['token']=$this->token;
		        $msgarr['time'] =time();
		        $msgarr['type'] ='m_vcard';
		        $leave_model =M("leave");
		        $res=$leave_model->add($msgarr);
				if($res){
					$this->success('转发成功',U('AddressBook/visitcardme', array('token' => $this->token, 'uid'=>$uid)));
				}else{
					$this->error('转发失败，请重新尝试',U('AddressBook/visitcardme', array('token' => $this->token, 'uid'=>$uid)));
				}	
			}else{
				$where['uid']=array('neq',$userInfoex['uid']);
				$userextinfo_db = M("User_extinfo");
				$userextinfos = $userextinfo_db->where($where)->select();
		    	$userextinfouid = array();
				$this->assign('userextinfo',$userextinfos);
				foreach ($userextinfos as $rex) {
					array_push($userextinfouid, $rex['uid']);
				}

				$userinfo_db = M("Userinfo");
				$userinfo = $userinfo_db->where(array('token'=>"$token"))->select();
				$userinfofin=array();
				foreach ($userinfo as $row) {
					if(in_array($row['id'], $userextinfouid)){
						$userinfofin[$row['id']]=$row;
					}
				}
				$this->assign('userinfo',$userinfofin);
				$this->assign('induscat',$induscat);
				$this->assign('token',$token);
				$this->assign('indusid',$indusid);
				$this->assign('keyword',$truename);
				$this->display('sendcard');
			}
		}else{
			$this->redirect(U('Index/memberLogin', array('token' => $token)));
		}
	}
	public function receivcard(){
		$token = $this->token;
		$session_username_name = "token_username_" . $this->token;
		$username=$_SESSION[$session_username_name];
		$userInfo = M('Userinfo')->where(array('username' => $username, 'token'=>$this->token))->find();
		$userInfoex = M('User_extinfo')->where(array('uid' => $userInfo['id'],'token'=>$this->token))->find();
		if($userInfoex){
			$leave_model =M("leave");
            $where = array("token"=>$this->token,'type'=>'m_vcard','message'=>$userInfo['id']);
            $reccard = $leave_model->where($where)->order('checked ASC,time DESC')->select();
            foreach ($reccard as $resd) {
            	$userInfo1 = M('Userinfo')->where(array('wecha_id' => $resd['wecha_id'], 'token'=>$this->token))->find();
            	$userInfoex1 = M('User_extinfo')->where(array('uid' => $userInfo1['id'],'token'=>$this->token))->find();
            	$recall[$userInfo1['id']]=array('id'=>$userInfo1['id'],'portrait'=>$userInfo1['portrait'],'truename'=>$userInfoex1['truename'],
            		'selfintro'=>$userInfoex1['selfintro'],'checked'=>$resd['checked']);
            }
			$this->assign('recall',$recall);
			$this->assign('token',$token);
			$this->display('receivcard');
		}else{
			$this->redirect(U('Index/memberLogin', array('token' => $token)));
		}
	}
}
