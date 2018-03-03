<?php
header("Content-Type: text/html;charset=utf-8");  
//$filename="tempfoo.php";//要解密的文件
function getTopDomain(){
    $host=$_SERVER['HTTP_HOST'];
    $host=strtolower($host);
    if(strpos($host,'/')!==false){
        $parse = @parse_url($host);
        $host = $parse['host'];
    }
    $topleveldomaindb=array('com','edu','gov','int','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','mobi','cc','me');
    $str='';
    foreach($topleveldomaindb as $v){
        $str.=($str ? '|' : '').$v;
    }
    $matchstr="[^\.]+\.(?:(".$str.")|\w{2}|((".$str.")\.\w{2}))$";
    if(preg_match("/".$matchstr."/ies",$host,$matchs)){
        $domain=$matchs['0'];
    }else{
        $domain=$host;
    }
    return $domain;
}
function echoBr()
{
	echo '<br/>------------------------------------------------<br/>';
}
function gfddddafumds($string)
{
	return base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($string)))));
}
function pigcmsd($string){
	$d='kmdx.cn';//C('server_topdomain');
	if (!$d){
		$d=getTopDomain();
	}
	$d=str_replace(array('-','.'),array('',''),$d);
	$dLetters=array();
	$dLength=strlen($d);
	for ($i=0;$i<$dLength;$i++){
		array_push($dLetters,ord(substr($d,$i,1)));
	}
	$dLetters=array_unique($dLetters);
	sort($dLetters,1);
	foreach ($dLetters as $dl){
		$substr=substr($string,$dl,$dl);
		$string=str_replace($substr,'',$string);
	}
	$string=base64_decode($string);
	return $string;
}
//$filename = './PigCms/Lib/ORG/Wechat.class.php';
//$filename = './PigCms/Lib/ORG/WapAction.class.php';
//$filename = './PigCms/Lib/ORG/UserAction.class.php';
//$filename = './PigCms/Lib/ORG/thirdAppMusic.class.php';
//$filename = './PigCms/Lib/ORG/thirdApp.class.php';
//$filename = './PigCms/Lib/ORG/BaseAction.class.php';
//$filename = './PigCms/Lib/ORG/BackAction.class.php';
//$filename = './PigCms/Lib/ORG/AgentAction.class.php';

//$filename = './PigCms/Lib/Action/Wap/YeepayAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/WeixinAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/TenpayComputerAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/TenpayAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/StoreAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/Red_packetAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/InviteAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/DianfuAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/DaofuAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/ChinabankAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/AutumnsAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/AllinpayAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/AlipaytypeAction.class.php';
//$filename = './PigCms/Lib/Action/Wap/AlipayAction.class.php';

//$filename = './PigCms/Lib/Action/User/WallAction.class.php';
//$filename = './PigCms/Lib/Action/User/Red_packetAction.class.php';
//$filename = './PigCms/Lib/Action/User/PlatformAction.class.php';
//./PigCms/Lib/Action/User/test.php
$filename = './PigCms/Lib/Action/Home/WeixinAction.class.php';
//./PigCms/Lib/Action/Admin/test.php
//$filename = './Common/runtime.php';

$lines = file($filename);//0,1,2行
//第一次base64解密
$content="";
if(preg_match("/O00O0O\(\".*\"\)/",$lines[1],$y))
{
    $content=str_replace("O00O0O(\"","",$y[0]);
    $content=str_replace("\")","",$content);
    $content=base64_decode($content);
}
//第一次base64解密后的内容中查找密钥
$content1="";
if(preg_match("/\=\".*\"/",$content,$k)) 
{ 
	$content1=str_replace('="',"",$k[0]); 
	$content1=str_replace('"',"",$content1); 
} 
$T_k1 = substr($content1,0,52);
$T_k2 = substr($content1,52,52);
$Secret = substr($content1,52*2);

//直接还原密文输出
$content2= base64_decode(strtr($Secret,$T_k2,$T_k1));
//echo $content2;
//exit;
$content3="";
if(preg_match("/(\".*\")/",$content2,$f)) 
{ 
	$content3=str_replace('("',"",$f[0]); 
	$content3=str_replace('")',"",$content3); 
} 

$content4 = substr($content3,1);
$content4 = substr($content4,0,strlen($content4)-1);
//$outfilename = 'file.txt';
//$fh = fopen($outfilename, "w");
//echo fwrite($fh, pigcmsd($content3));    // 输出：6
//fclose($fh);
echo pigcmsd($content3);
?>
 