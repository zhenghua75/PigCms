<?php 

class apiOauth
{
	public $appId;
	
	public $appSecret;
	
	public $encodingAesKey;
	
	public $verify_ticket;
	
	public $component_verify_ticket;
	
	public $error;
	
	function __construct(){
		// $checkFunc=new checkFunc();
		// if (!function_exists('fdsrejsie3qklwewerzdagf4ds')){exit('error-4');}
		// $checkFunc->cfdwdgfds3skgfds3szsd3idsj();
		$this->weixin_account();
	}
		
	public function weixin_account(){
		
		$account 	= M('Weixin_account')->where(array('type'=>1))->find();
		
		$this->appId 			= $account['appId'];
		
		$this->appSecret 		= $account['appSecret'];
		
		$this->encodingAesKey 	= $account['encodingAesKey'];
		
		$this->component_verify_ticket 	= $account['component_verify_ticket'];
		
	}
	
	public function webOauth($info,$scope=''){
		
		$now 	= time();
								
		//if((empty($info['web_access_token']) && empty($info['web_refresh_token']) && empty($info['web_expires'])) || $info['web_expires'] < $now ){
			
			$redirect_uri 	= "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			
			$codeUrl 		= $this->get_code_url($info,$redirect_uri,$scope);

			if(empty($_GET['code']) && empty($_GET['state'])){

				header("Location: $codeUrl"); 
				
				exit;
				
			}else{
				
				$code 	= $_GET['code'];

				if(!empty($code)){
					
					$res 	= $this->get_web_access_token($info,$code);

					if(empty($res['errcode'])){
						/*
						$save 	= array(
								'web_access_token' 	=> $res['access_token'],
								'web_refresh_token'	=> $res['refresh_token'],
								'web_expires'		=> $res['expires_in']+$now
							);
							
						M('Wxuser')->where("id={$info['id']}")->save($save);
						*/

						$data 	= array(
							'access_token'	=> $res['access_token'],
							'openid'		=> $res['openid'],
						);
						
						return $data;

					}else{

						//$this->error['-4'] = ' 网页授权错误:'.$res['errcode'];
						exit('授权错误，请检查公众号权限和设置');
					}
				}		
			}
		/*	
		}else{

			$res	= $this->get_refresh_web_access_token($info);
			
			if(empty($res['errcode'])){
			
				$save 	= array(
								'web_access_token' 	=> $res['access_token'],
								'web_refresh_token'	=> $res['refresh_token'],
								'web_expires'		=> $res['expires_in']+$now
							);
							
				M('Wxuser')->where("id={$info['id']}")->save($save);
				
				$data 	= array(
							'access_token'	=> $res['access_token'],
							'openid'		=> $res['openid'],
						);
				return $data;

			}else{

				$this->error['-4'] = ' 刷新授权错误:'.$res['errcode'];

			}
				
		}
		*/
			
	}
	
	public function get_fans_info($access_token,$openid){
		
		$url 	= 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		
		$res 	= $this->https_request($url);
		
		return $res;
		
	} 
	
	
	public function get_refresh_web_access_token($info){
		
		if($info['type'] == 1 && $info['winxintype'] == 3 && $info['oauth'] == 1){
			
			$component_access_token 	= $this->get_component_access_token();

			$tokenurl 	= 'https://api.weixin.qq.com/sns/oauth2/component/refresh_token?appid='.$info['appid'].'&grant_type=refresh_token&component_appid='.$this->appId.'&component_access_token='.$component_access_token.'&refresh_token='.$info['web_refresh_token'];
			
		}else{
			
			$tokenurl 	= 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$info['appid'].'&grant_type=refresh_token&refresh_token='.$info['web_refresh_token'];
			
		}	

		$res 	= $this->https_request($tokenurl);
		
		return $res;
		
	}
	
	public function get_web_access_token($info,$code){

		if($info['type'] == 1 && $info['winxintype'] == 3 && $info['oauth'] == 1){
			
			$component_access_token 	= $this->get_component_access_token();
			
			$tokenurl = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid='.$info['appid'].'&code='.$code.'&grant_type=authorization_code&component_appid='.$this->appId.'&component_access_token='.$component_access_token;
			
		}else{
			
			$tokenurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$info['appid']."&secret=".$info['appsecret']."&code=".$code."&grant_type=authorization_code";
			
		}

		$res 	= $this->https_request($tokenurl);

		return $res;
		
	}
	
	public function get_code_url($info,$redirect_uri = '', $scope = '', $state = 'oauth')
	{
		if(empty($scope)){
			if($info['oauthinfo']){
				$scope = 'snsapi_userinfo';
			}else{
				$scope = 'snsapi_base';
			}
		}
		
		$redirect_uri = urlencode($redirect_uri);
		
		$url 	= '';
		
		$url 	=  "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$info['appid']}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$state}";
		
		if($info['type'] == 1 && $info['winxintype'] == 3 && $info['oauth'] == 1){
			
			$url 	.= '&component_appid='.$this->appId;
			
		}
		
		$url .= '#wechat_redirect';

		return $url;
		
	}

	public function update_authorizer_access_token($appid,$info=null){
		
		$now 	= time();
		if(empty($info)){
			$info 	= M('Wxuser')->where("appid='$appid'")->field('id,appid,appsecret,type,authorizer_access_token,authorizer_refresh_token,authorizer_expires,winxintype')->find();
		}

		//if($info['authorizer_expires'] < $now){
			
			if($info['type'] == 1 && $info['winxintype'] == 3  && empty($info['is_domain'])){
			
				$access_token 	= $this->get_component_access_token();

				$url 	= 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='.$access_token;
				
				$data 	= '{
							"component_appid":"'.$this->appId.'" ,
							"authorizer_appid": "'.$info['appid'].'",
							"authorizer_refresh_token":"'.$info['authorizer_refresh_token'].'"
						}';
						
				$res 	= $this->https_request($url,$data);
				
				if($res['authorizer_access_token'] && $res['authorizer_refresh_token']){
					/*
					$save 	= array(
						'authorizer_access_token' 	=> $res['authorizer_access_token'],
						'authorizer_refresh_token'	=> $res['authorizer_refresh_token'],
						'authorizer_expires'		=> $res['expires_in']+$now
					);
					
					M('Wxuser')->where("id={$info['id']}")->save($save);
					*/
					$token 	= $res['authorizer_access_token'];
				}else{
					
					$this->error['-3'] = '获取authorizer_access_token错误:'.$res['errcode'];
					
				}
			}else{
			
				$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$info['appid'].'&secret='.$info['appsecret'];
				
				$res 	= $this->https_request($url_get);

				if($res['errcode']){
					
					$this->error['-2'] = '获取access_token错误:'.$res['errcode'];
					
				}else{
					/*
					$save 	= array(
								'authorizer_access_token' 	=> $res['access_token'],
								'authorizer_expires'		=> $res['expires_in']+$now
							);
	
					M('Wxuser')->where("id={$info['id']}")->save($save);
					*/
					$token 	= $res['access_token'];
				}
			}
			
			return $token;
		//}else{
			//return $info['authorizer_access_token'];
		//}
	}

	public function get_authorizer_info($auth_appid){
		
		$access_token 	= $this->get_component_access_token();
		
		$url 	= 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$access_token;
		
		$data 	= '{
					"component_appid":"'.$this->appId.'",
					"authorizer_appid": "'.$auth_appid.'" 
				}';
				
		$res 	= $this->https_request($url,$data);
		
		return $res['authorizer_info'];
	}
	

	public function get_authorization_info($auth_code){
		
		$access_token 	= $this->get_component_access_token();
		
		$url 	= 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$access_token;
		
		$data 	= '{
					"component_appid":"'.$this->appId.'" ,
					"authorization_code": "'.$auth_code.'"
				}';

		$res 	= $this->https_request($url,$data);
		
		return $res['authorization_info'];
	}
	
	public function start_authorization($redirect_uri){
		
		$access_token 	= $this->get_component_access_token();

		$pre_auth_code 	= $this->get_pre_auth_code($access_token);
		
		//$redirect_uri 	= urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

		$url 			= 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid='.$this->appId.'&pre_auth_code='.$pre_auth_code.'&redirect_uri='.
		urlencode($redirect_uri);
		
		return $url;
	}
	
	public function get_pre_auth_code($access_token){
		
		$url 	= 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$access_token;
		
		$data 	= '{
					"component_appid":"'.$this->appId.'" 
				}';
				
		$res 	= $this->https_request($url,$data);

		return $res['pre_auth_code'];
	}
	
	public function get_component_access_token(){
		
		$now 	 	= time();
		
		$account 	= M('Weixin_account')->field('component_access_token,token_expires')->where('type=1')->find();

		if($account['component_access_token'] == '' || $account['token_expires'] == '' || $account['token_expires'] < $now){
			
			$url 	= 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
			
			$data 	= '{
						"component_appid":"'.$this->appId.'" ,
						"component_appsecret": "'.$this->appSecret.'", 
						"component_verify_ticket": "'.$this->component_verify_ticket.'"
					}';

			$res 	= $this->https_request($url,$data);

			if($res['errcode'] > 0){
				
				$this->error['-1'] = '获取component_access_token错误:'.$res['errcode'];
				
			}else{
				
				M('Weixin_account')->where('type=1')->save(array('component_access_token'=>$res['component_access_token'],'token_expires'=>$now+$res['expires_in']));
				
				$token 	= $res['component_access_token'];
				
			}
			
		}else{
			
			$token 	= $account['component_access_token'];
			
		}

		return $token;
	}
	
	
	public function get_error(){
		dump($this->error);
	}
	
	public function https_request($url, $data = null)
	{
		$curl = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($curl, CURLOPT_SSLVERSION, 3);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		$errorno= curl_errno($curl);
		if ($errorno) {
			return array('curl'=>false,'errorno'=>$errorno);
		}else{
			$res = json_decode($output,1);

			if ($res['errcode']){
				return array('errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']);
			}else{
				return $res;
			}
		}
		curl_close($curl);
	}
}

?>