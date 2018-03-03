<?php

class ThirdPayCard
{

	public function index($orderid,$paytype='',$third_id=''){
		$where 	= array('orderid'=>$orderid);

		$order = M('Member_card_pay_record')->where($where)->find();
		
		if($order){
			$wecha_id 	= $order['wecha_id'];
			$token 		= $order['token'];

			if($order['paid'] == 1){
				M('Member_card_pay_record')->where($where)->setField('paytime',time());
				if($order['type'] == 1){
					M('Userinfo')->where("wecha_id = '$wecha_id' AND token = '$token'")->setInc('balance',$order['price']);
					if($token=='ymluzb1442734296'){
						$ispesent=false;
						$pesentvalue=0;
						$regpesent=D('rentcar_set')->where(array('token'=>$token,'settype'=>'fillpesent'))->select();
						foreach ($regpesent as $key => $value) {
							if($order['price']>=$value['refield1'] && $order['price']<$value['refield2'] && $value['refield3']>0){
								$ispesent=true;
								$rate=$value['refield3'];
								$pesentvalue=round($order['price']*$rate/100);
								break;
							}
						}
						if($ispesent && $pesentvalue>0){
							$record["orderid"] = date("YmdHis", time()) . mt_rand(1000, 9999);
							$record["ordername"] = "充值赠送";
							$record["createtime"] = time();
							$record["paid"] = 1;
							$record["price"] = $pesentvalue;
							$record["token"] = $token;
							$record["wecha_id"] = $wecha_id;
							$record["type"] = 1;
							$record["cardid"] = 0;
							$result = M("Member_card_pay_record")->add($record);
							M('Userinfo')->where("wecha_id = '$wecha_id' AND token = '$token'")->setInc('balance',$pesentvalue);
						}
					}
				}else{
					$lastid = M('Member_card_use_record')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->order('id DESC')->getField('id');
					if($this->_get('type') == 'coupon'){
						M('Member_card_coupon')->where(array('token'=>$token,'id'=>(int)$this->_get('itemid')))->setInc('usetime',(int)$this->_get('usecount'));
						M('Member_card_use_record')->where(array('token'=>$token,'id'=>$lastid))->setField(array('usecount'=>(int)$this->_get('usecount'),'cat'=>6));
					}elseif($this->_get('type') == 'privelege'){
						M('Member_card_vip')->where(array('token'=>$token,'id'=>(int)$this->_get('itemid')))->setInc('usetime');
						M('Member_card_use_record')->where(array('token'=>$token,'id'=>$lastid))->setField('cat',4);
					}
					
				}
                if(empty($act)){
                	header('Location:'.U('Card/card',array('token'=>$token,'wecha_id'=>$wecha_id,'cardid'=>$order['cardid'])));
                }else{
                	header('Location:'.U('Card/'.$act,array('token'=>$token,'wecha_id'=>$wecha_id,'cardid'=>$order['cardid'])));
                }

                $info       = M('Member_card_set')->where(array('token'=>$token,'cardid'=>$order['cardid']))->find();
                $cardinfo   = M('Member_card_create')->where(array('token'=>$token,'cardid'=>$order['cardid'],'wecha_id'=>$wecha_id))->find();
                
                $href = $this->siteUrl.U('Card/card',array('token'=>$token,'wecha_id'=>$wecha_id,'cardid'=>$order['cardid']));
                //模板消息
				$model      = new templateNews();
                $dataKey    = 'TM00009';
                $dataArr    = array(
                	'href'      	=> $href,
                    'wecha_id'  	=> $wecha_id,
                    'first'         => '您好，你已经成功充值。',
                    'accountType'   => $info['cardname'],
                    'account'       => $cardinfo['number'],
                    'amount'        => $order['price'],
                    'result'        => '充值成功',
                    'remark'        => '会员充值'
                );

                $model->sendTempMsg($dataKey,$dataArr);

			}else{
				exit('支付未完成');
			}
		
		}else{
			exit('订单不存在');
		}

	}

}


?>