<?php
final class payHandle {
	public $from;
	public $db;
	public $payType;
	public $token;
	public function __construct($token,$from,$paytype='tenpay') {
	
		$this->from=$from;
		$this->from=$from?$from:'Groupon';
		$this->from=$this->from!='groupon'?$this->from:'Groupon';
		switch (strtolower($this->from)){
			
			case 'groupon':
			case 'store':
				$this->db=M('Product_cart');
				break;
			case 'repast':
				$this->db=M('Dish_order');
				break;
			case 'dishout':
				$this->db=M('Dish_order');
				break;
			case 'hotels':
				$this->db=M('Hotels_order');
				break;
			case 'business':
				$this->db=M('Reservebook');
				break;
			case 'rentcar':
				$this->db=M('Reservebook');
				break;
			case 'card':
				$this->db=M('Member_card_pay_record');
				break;
			case 'medical':
				$this->db=M('Medical_user');
				break;
			case 'unitary':
				$this->db=M('unitary_order');
				break;
			case 'livingcircle':
				$this->db=M('livingcircle_mysellerorder');
				break;
			case 'bargain':
				$this->db=M('bargain_order');
				break;
			case 'crowdfunding':
				$this->db=M('Crowdfunding_order');
				break;
			case 'seckill' :
				$this->db=M('Seckill_book');
				break;
			case 'micrstore' :
				$this->db = M('Micrstore');
				break;
			default:
				
				break;
		}
		$this->token=$token;
		$this->payType=$paytype;
	}
	public function getFrom(){
		return $this->from;
	}
	public function beforePay($id){
		if(strtolower($this->from)=='repast'){
		  $wherearr=array('token'=>$this->token,'tmporderid'=>$id);
		}else{
		  $wherearr=array('token'=>$this->token,'orderid'=>$id);
		}

		$thisOrder=$this->db->where($wherearr)->find();
		switch (strtolower($this->from)){
			case 'business':
				$price=$thisOrder['payprice'];
				break;
			case 'rentcar':
				$price=$thisOrder['payprice'];
				break;
		   case 'repast':
			    if(($thisOrder['advancepay']>0) && !($thisOrder['paycount']>0)){
		           $price=$thisOrder['advancepay'];
		        }else{
				   $price=$thisOrder['price']-$thisOrder['havepaid'];
				}
				break;
		  default:
				$price=$thisOrder['price'];
				break;
		}
		if (key_exists('third_id',$thisOrder)){
			return array('orderid'=>$thisOrder['orderid'],'price'=>$price,'wecha_id'=>$thisOrder['wecha_id'],'token'=>$thisOrder['token'],'paid'=>$thisOrder['paid'],'third_id'=>$thisOrder['third_id']);
		}else {
			return array('orderid'=>$thisOrder['orderid'],'price'=>$price,'wecha_id'=>$thisOrder['wecha_id'],'token'=>$thisOrder['token'],'paid'=>$thisOrder['paid'],'transactionid'=>$thisOrder['transactionid']);
			
		}
		
	}
	public function afterPay($id,$third_id='',$transaction_id='') {
		$thisOrder=$this->beforePay($id);
		if(empty($thisOrder)){
			exit('订单不存在！');
		}else if($thisOrder['paid']){
			exit('此订单已付款，请勿重复操作！');
		}
		$wecha_id=$thisOrder['wecha_id'];
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/DataPig/conf/4'.$this->token.'.txt',json_encode($thisOrder));
		if($this->payType != 'daofu' && $this->payType != 'dianfu'){
			$member_card_create_db=M('Member_card_create');
			$userCard=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->find();
			$userinfo_db=M('Userinfo');
			if ($userCard){
				$member_card_set_db=M('Member_card_set');
				$thisCard=$member_card_set_db->where(array('id'=>intval($userCard['cardid'])))->find();
				if ($thisCard){
					$set_exchange = M('Member_card_exchange')->where(array('cardid'=>intval($thisCard['id'])))->find();
					//
					$arr['token']=$this->token;
					$arr['wecha_id']=$wecha_id;
					$arr['expense']=$thisOrder['price'];
					$arr['time']=time();
					$arr['cat']=99;
					$arr['staffid']=0;
					$arr['score']=intval($set_exchange['reward'])*$arr['expense'];
					
					if(isset($_GET['redirect'])){
						$infoArr = explode('|',$_GET['redirect']);
						
						$param = explode(',',$infoArr[1]);
						if($param){
							foreach ($param as $pa){
								$pas=explode(':',$pa);
								if($pas[0] == 'itemid'){
									$arr['itemid']=$pas[1];
								}
							}
						}
						
					}
					
					M('Member_card_use_record')->add($arr);

					$thisUser = $userinfo_db->where(array('token'=>$thisCard['token'],'wecha_id'=>$arr['wecha_id']))->find();
					$userArr=array();
					$userArr['total_score']=$thisUser['total_score']+$arr['score'];
					$userArr['expensetotal']=$thisUser['expensetotal']+$arr['expense'];
					$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$arr['wecha_id']))->save($userArr);
				}
			}
			$data_order['paid'] = 1;
		}
		//
		$order_model=$this->db;
		$data_order['paytype'] = $this->payType;

		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/DataPig/conf/3'.$this->token.'.txt',json_encode($thisOrder));
	
		if (key_exists('third_id',$thisOrder)){
		$data_order['third_id'] = $third_id;
		}else {
			$data_order['transactionid']=$third_id;
		}
		
		//$order_model->where(array('orderid'=>$id))->setField('paid',1);
		
		$where_arr=array('orderid'=>$id);
		if (strtolower($this->from)=='repast'){
		  $where_arr=array('tmporderid'=>$id);
		}
		$order_model->where($where_arr)->data($data_order)->save();

		if (strtolower($this->getFrom())=='groupon'){
			
			$order_model->where(array('orderid'=>$thisOrder['orderid']))->save(array('transactionid'=>$transaction_id,'paytype'=>$this->payType));
			
		}
		
		if($_GET['pl']){
			$database_platform_pay = D('Platform_pay');
			$data_platform_pay['orderid'] = $thisOrder['orderid'];
			$data_platform_pay['price'] = $thisOrder['price'];
			$data_platform_pay['wecha_id'] = $thisOrder['wecha_id'];
			$data_platform_pay['token'] = $thisOrder['token'];
			$data_platform_pay['from'] = $this->from;
			$data_platform_pay['time'] = $_SERVER['REQUEST_TIME'];
			$database_platform_pay->data($data_platform_pay)->add();
		}
		
		return $thisOrder;
	}
}
?>