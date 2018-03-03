<?php
class FunctionLibrary_AddressBook{
	public $sub;
	public $token;
	function __construct($token,$sub) {
		$this->sub=$sub;
		$this->token=$token;
	}
	function index(){
		if (!$this->sub){
			return array(
			'name'=>'通讯录',
			'subkeywords'=>0,
			'sublinks'=>3,
			);
		}else {
			$arr=array(
			'name'=>'通讯录',
			'subkeywords'=>array(
			),
			'sublinks'=>array(
			),
			);

			$arr['subkeywords'][0]=0;
			$arr['sublinks'][0]=array('name'=>'找人','link'=>'{siteUrl}/index.php?g=Wap&m=AddressBook&a=index&token='.$this->token);
			$arr['subkeywords'][1]=0;
			$arr['sublinks'][1]=array('name'=>'入会申请','link'=>'{siteUrl}/index.php?g=Wap&m=AddressBook&a=regist&token='.$this->token);
			$arr['subkeywords'][2]=0;
			$arr['sublinks'][2]=array('name'=>'我','link'=>'{siteUrl}/index.php?g=Wap&m=AddressBook&a=visitcardme&token='.$this->token);

			return $arr;
		}	
	}
}