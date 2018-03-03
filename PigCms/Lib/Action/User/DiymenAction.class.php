<?php
class DiymenAction extends UserAction{
	public $thisWxUser;
	public $access_token;
	public function _initialize() {
		parent::_initialize();
		$where=array('token'=>$this->token);
		$this->thisWxUser=M('Wxuser')->where($where)->find();
		
		if($this->thisWxUser['type'] == 0){
			$diyApiConfig=M('Diymen_set')->where($where)->find();
			if( (empty($this->thisWxUser['appid']) || empty($this->thisWxUser['appsecret'])) && (empty($diyApiConfig['appid']) || empty($diyApiConfig['appsecret'])) ){
				$this->error('请先设置AppID和AppSecret再使用本功能','?g=User&m=Index&a=edit&id='.$this->thisWxUser['id']);
			}else{
				$this->thisWxUser['appid'] 		= $diyApiConfig['appid'];
				$this->thisWxUser['appsecret']	= $diyApiConfig['appsecret'];
			}
		}
		
		$apiOauth 	= new apiOauth();
		$this->access_token 	= $apiOauth->update_authorizer_access_token($this->thisWxUser['appid']);
		
	}

	//自定义菜单配置
	public function index(){
		$data=M('Diymen_set')->where(array('token'=>$_SESSION['token']))->find();
		$this->assign('diymen',$data);
		if(IS_POST){
			$_POST['token']=$_SESSION['token'];
			if($data==false){
				$this->all_insert('Diymen_set');
			}else{
				$_POST['id']=$data['id'];
				$this->all_save('Diymen_set');
			}
			M('Wxuser')->where(array('token'=>$this->token))->save(array('appid'=>trim($this->_post('appid')),'appsecret'=>trim($this->_post('appsecret'))));
		}else{
			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();//dump($class);
			foreach($class as $key=>$vo){
				$c=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id']))->order('sort desc')->select();
				$class[$key]['class']=$c;
			}

			$fuwuAppid = M('Wxuser')->where(array('token'=>session('token')))->getField('fuwuappid');

			$this->assign('fuwuAppid',$fuwuAppid);
			$this->assign('class',$class);
			$this->display();
		}
	}


	public function  class_add(){
		if(IS_POST){
			$this->all_insert('Diymen_class','/index');
		}else{
			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();

			$this->assign('class',$class);
			$this->assign('wxsys',$this->_get_sys());
			$this->display();
		}
	}
	public function  class_del(){
		$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$this->_get('id')))->order('sort desc')->find();
		//echo M('Diymen_class')->getLastSql();exit;
		if($class==false){
			$back=M('Diymen_class')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->delete();
			if($back==true){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}else{
			$this->error('请删除该分类下的子分类');
		}

	}
	public function  class_edit(){
		$this->assign('wxsys',$this->_get_sys());
		if(IS_POST){
			$_POST['id']=$this->_get('id');
			if($_POST['menu_type']==1 && $_POST['keyword'] != ''){
				$set = array('url'=>'','wxsys'=>'');
				unset($_POST['wxsys']);
				unset($_POST['url']);
			}else if($_POST['menu_type']==2 && $_POST['url'] != ''){
				$set = array('keyword'=>'','wxsys'=>'');
				unset($_POST['keyword']);
				unset($_POST['wxsys']);
			}else if($_POST['menu_type']==3 && $_POST['wxsys'] != ''){
				$set = array('keyword'=>'','url'=>'');
				unset($_POST['keyword']);
				unset($_POST['url']);
			}

			M('Diymen_class')->where(array('id'=>$_POST['id']))->save($set);
			$this->all_save('Diymen_class','/index?id='.$this->_get('id'));
		}else{
			$data=M('Diymen_class')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->find();
			if($data==false){
				$this->error('您所操作的数据对象不存在！');
			}else{
				$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();//dump($class);
				$this->assign('class',$class);
				$this->assign('show',$data);
			}
			if($data['keyword'] != ''){
				$type = 1;
			}elseif($data['url'] != ''){
				$type = 2;
			}elseif($data['wxsys'] != ''){
				$type = 3;
			}
			$this->assign('type',$type);
			$this->display();
		}
	}

	function _get_sys($type='',$key=''){
		$wxsys 	= array(
				'扫码带提示',
				'扫码推事件',
				'系统拍照发图',
				'拍照或者相册发图',
				'微信相册发图',
				'发送位置',
		);

		if($type == 'send'){
			$wxsys 	= array(
					'扫码带提示'=>'scancode_waitmsg',
					'扫码推事件'=>'scancode_push',
					'系统拍照发图'=>'pic_sysphoto',
					'拍照或者相册发图'=>'pic_photo_or_album',
					'微信相册发图'=>'pic_weixin',
					'发送位置'=>'location_select',
			);
			return $wxsys[$key];
			exit;
		}
		return $wxsys;
	}

	public function  class_send(){
		if(IS_GET){

			$data = '{"button":[';

			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0,'is_show'=>1))->limit(3)->order('sort desc')->select();//dump($class);
			$kcount=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0,'is_show'=>1))->limit(3)->order('sort desc')->count();
			$k=1;

			foreach($class as $key=>$vo){
				//主菜单
				$data.='{"name":"'.$vo['title'].'",';
				$c=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id'],'is_show'=>1))->limit(5)->order('sort desc')->select();
				$count=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id'],'is_show'=>1))->limit(5)->order('sort desc')->count();
				//子菜单
				$vo['url']=$this->convertLink($vo['url']);
				if($c!=false){
					$data.='"sub_button":[';
				}else{
					if($vo['keyword']){
						$data.='"type":"click","key":"'.$vo['keyword'].'"';
					}else if($vo['url']){
						$data.='"type":"view","url":"'.$vo['url'].'"';
					}else if($vo['wxsys']){
						$data.='"type":"'.$this->_get_sys('send',$vo['wxsys']).'","key":"'.$vo['wxsys'].'"';
					}
				}

				$i=1;
				foreach($c as $voo){
					$voo['url']=$this->convertLink($voo['url']);
					if($i==$count){
						if($voo['keyword']){
							$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"}';
						}else if($voo['url']){
							$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"}';
						}else if($voo['wxsys']){
							$data.='{"type":"'.$this->_get_sys('send',$voo['wxsys']).'","name":"'.$voo['title'].'","key":"'.$voo['wxsys'].'"}';
						}
					}else{
						if($voo['keyword']){
							$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"},';
						}else if($voo['url']){
							$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"},';
						}else if($voo['wxsys']){
							$data.='{"type":"'.$this->_get_sys('send',$voo['wxsys']).'","name":"'.$voo['title'].'","key":"'.$voo['wxsys'].'"},';
						}
					}
					$i++;
				}
				if($c!=false){
					$data.=']';
				}

				if($k==$kcount){
					$data.='}';
				}else{
					$data.='},';
				}
				$k++;
			}
			$data.=']}';

			file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->access_token);
			$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token;

			$rt=$this->api_notice_increment($url,$data);

			if($rt['rt']==false){
				$errmsg=GetErrorMsg::wx_error_msg($rt['errorno']);
				$this->error('操作失败,'.$rt['errorno'].':'.$errmsg);
			}else{
				$this->success('操作成功');
			}
			exit;
		}else{
			$this->error('非法操作');
		}
	}

	function api_notice_increment($url, $data){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {
			return array('rt'=>false,'errorno'=>$errorno);
		}else{
			$js=json_decode($tmpInfo,1);
			if ($js['errcode']=='0'){
				return array('rt'=>true,'errorno'=>0);
			}else {
				$errmsg=GetErrorMsg::wx_error_msg($js['errcode']);
				$this->error('发生错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$errmsg);
			}
		}
	}
	function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}

}

	?>
	