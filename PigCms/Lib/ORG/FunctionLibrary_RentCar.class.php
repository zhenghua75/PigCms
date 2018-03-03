<?php
class FunctionLibrary_RentCar{
	public $sub;
	public $token;
	function __construct($token,$sub) {
		$this->sub=$sub;
		$this->token=$token;
	}
	function index(){
		$db		= M('rentcar_type');
		$where	=array('token'=>$this->token);
		$items 	= $db->where($where)->order('sort DESC')->select();
		if (!$this->sub){
			return array(
			'name'=>'微租车',
			'subkeywords'=>0,
			'sublinks'=>count($items)+2,
			);
		}else {

			$arr=array(
			'name'=>'微租车',
			'subkeywords'=>array(
			),
			'sublinks'=>array(
			),
			);
			$arr['subkeywords'][0]=0;
			$arr['sublinks'][0]=array('name'=>'租车','link'=>'{siteUrl}/index.php?g=Wap&m=RentCar&a=classify&token='.$this->token.'&group=0');
			$arr['subkeywords'][1]=1;
			$arr['sublinks'][1]=array('name'=>'其它','link'=>'{siteUrl}/index.php?g=Wap&m=RentCar&a=classify&token='.$this->token.'&group=1');
			foreach ($items as $key => $value) {
				if($value['group']=='20'){
					$arr['subkeywords'][$key+2]=array('name'=>$value['name'],'keyword'=>$value['name']);
					$arr['sublinks'][$key+2]=array('name'=>$value['name'],'link'=>'{siteUrl}/index.php?g=Wap&m=RentCar&a=classify_item&token='.$this->token.'&tid='.$value['tid']);
				}else{
					$where	=array('token'=>$this->token,'tid'=>$value['tid']);
					$rentitem 	= M('rentcar_item')->where($where)->order('sort DESC')->select();
					$arr['subkeywords'][$key+2]=array('name'=>$value['name'],'keyword'=>$value['name']);
					$arr['sublinks'][$key+2]=array('name'=>$value['name'],'link'=>'{siteUrl}/index.php?g=Wap&m=RentCar&a=goCart&token='.$this->token.'&tid='.$value['tid'].'&sid='.$rentitem[0]['sid']);
				}

			}
			return $arr;
		}	
	}
}