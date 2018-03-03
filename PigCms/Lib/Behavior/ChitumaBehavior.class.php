<?php

class ChitumaBehavior extends Behavior
{
	public function run(&$params){
		$gettoken=$_GET['token'] ? $_GET['token'] : '';
		if($gettoken=='ymluzb1442734296' && $_GET['nonce']){
			//Log::Write(json_encode($_GET),'INFO');
			//Log::Write(json_encode($_POST),'INFO');
			$weixin = new Wechat($gettoken, $this->wxuser);
			$data = $weixin->request();
			//Log::Write(json_encode($data),'INFO');
			//return array("test", "text");
			$user_request1 = M("User_request")->where(array("token" => $gettoken, "msgtype" => 'shortdistance', "uid" => $data["FromUserName"]))->find();
			$user_request2 = M("User_request")->where(array("token" => $gettoken, "msgtype" => 'beginlocation', "uid" => $data["FromUserName"]))->find();
			if(($data['MsgType']=='text' && $data['Content']=='短途') || ($data['MsgType']=='event' && $data['EventKey']=='短途')){
				if($data['MsgType']=='text'){
					$this->recordLastRequest($data['Content'], "shortdistance",$data,$gettoken);
				}else{
					$this->recordLastRequest($data['EventKey'], "shortdistance",$data,$gettoken);
				}
				list($content, $type) = array("请发送“当前位置”(对话框右下角点击＋号，然后点击“位置”，确认位置并“发送”)", "text");
				$weixin->response($content, $type);
			}elseif($user_request1 && !$user_request2 && $data["Location_X"]){
				$this->recordLastRequest($data["Location_Y"] . "," . $data["Location_X"] . "|" . $data['Label'], "beginlocation",$data,$gettoken);
				$renttype = M("rentcar_type")->where(array("token" => $gettoken,"group"=>'10'))->find();
				$url = "http://ylst.kmdx.cn/index.php?g=Wap&m=RentCar&a=goCart&token=" . $gettoken . "&tid=" . $renttype['tid'];
				$order = array(
					array(
						array($renttype["name"], strip_tags(htmlspecialchars_decode($renttype["second_desc"])), $renttype["picurl"], $url)
						),
					"news"
					);
				list($content, $type) = $order;
				$weixin->response($content, $type);
			}
		}
	}

	private function recordLastRequest($key, $msgtype, $reqdata, $token)
	{
		$rdata = array();
		$rdata["time"] = time();
		$rdata["token"] = $token;
		$rdata["keyword"] = $key;
		$rdata["msgtype"] = $msgtype;
		$rdata["uid"] = $reqdata["FromUserName"];
		$user_request_model = M("User_request");

		if($msgtype=='shortdistance'){
			$where["token"] = $token;
			$where["uid"] = $rdata["uid"];
			$where["msgtype"] = array('in','shortdistance,beginlocation');
			$user_requests = $user_request_model->where($where)->select();
			foreach ($user_requests as $key => $value) {
				$rid["id"]=$value['id'];
				$user_request_model->where($rid)->delete();
			}
		}
		$user_request_row = $user_request_model->where(array("token" => $token, "msgtype" => $msgtype, "uid" => $rdata["uid"]))->find();
		if (!$user_request_row) {
			$user_request_model->add($rdata);
		}
		else {
			$rid["id"] = $user_request_row["id"];
			$user_request_model->where($rid)->save($rdata);
		}
	}
}