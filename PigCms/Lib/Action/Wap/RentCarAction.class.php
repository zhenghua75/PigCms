<?php
class RentCarAction extends WapAction{

    public $token;
    public $wecha_id;
    public $type;
    public $bid;
    private $tpl;
    private $info;
    public $weixinUser;
    public $homeInfo;
    public $isamap;
    public function _initialize() {
        parent::_initialize();
        $this->token    = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $this->wecha_id = filter_var($this->wecha_id,FILTER_SANITIZE_STRING);
        $orderid    =   filter_var($this->_get('orderid'),FILTER_SANITIZE_STRING);
        $where   = array('token'=>$this->token,'type'=>$this->type,'bid'=>$this->bid);
        $busines = M('busines')->where($where)->find();
        $type = 'rentcar';

        $session_username_name = "token_username_" . $this->token;
        $username=$_SESSION[$session_username_name];
        $usinfo = M('Userinfo')->where(array('token'=>$this->token,'username'=>$username,'wecha_id'=>$this->wecha_id))->select();
        $cust = M('cust_service')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'uid'=>$usinfo[0]['id']))->find();
        if($cust){
            $custpermission=1;
        }else{
            $custpermission=0;
        }
        $this->assign('custpermission',$custpermission);
        $this->assign('busines',$busines);
        $this->assign('picurl',$busines['picurl']);
        $this->assign('title',$busines['title']);
        $this->assign('token',$this->token);
        $this->assign('wecha_id',$this->wecha_id);
        $this->assign('type',$type);
        $tpl=$this->wxuser;
        $this->tpl=$tpl;

        $this->isamap = 1;
        $this->amap = new amap();
    }

    public function classify(){
        $data       = D('rentcar_type');
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $group      = filter_var($this->_get('group'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token);
        if($group==0){
            $where['group']=array('in','10,20');
        }elseif($group==1){
            $where['group']=array('in','30,40,50');
        }
        $count      = $data->where($where)->count();
        $Page       = new Page($count,5);
        $show       = $Page->show();
        $classify   = $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($classify as $key => $value) {
            if($value['group']=='30'||$value['group']=='40'||$value['group']=='50'){
                $rentitem=D('rentcar_item')->where(array('token'=>$token,'tid'=>$value['tid']))->find();
                $classify[$key]['sid']=$rentitem['sid'];
            }else{
                $classify[$key]['sid']=0;
            }
        }
        $this->assign('count',6);
        $this->assign('page',$show);
        $this->assign('classify',$classify);
        $this->display();
    }

    public function classify_item(){
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $tid        = filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $where    = array('token'=>$token,'tid'=>$tid);
        $b_type =M('rentcar_type');
        $ret_type   = $b_type->where($where)->find();

        $b_item   = M('rentcar_item');
        $count      = $b_item->where($where)->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
        $sec_item   = $b_item->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page',$show);
        $this->assign('sec_item',$sec_item);

        $this->assign('picurl',$ret_type['picurl']);
        $this->assign('title',$ret_type['name']);
        $this->assign('classify',$ret_type);
        $this->display();
    }


    public function project(){
        $data       = D('rentcar_item');
        $tid        = filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $sid        = filter_var($this->_get('sid'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'tid'=>$tid,'sid'=>$sid);
        $t_second   = $data->where($where)->find();
        $this->assign('sec_item',$t_second);
        $this->assign('picurl',$t_second['picurl']);
        $this->display();
    }

    public function goCart(){
        $tb_rentset = D('rentcar_set');
        $data       = D('rentcar_item');
        $tb_renttype= D('rentcar_type');
        $tb_resbook = D('reservebook');
        $tb_request = D('user_request');
        $type       = 'rentcar';
        $tid        = filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $sid        = filter_var($this->_get('sid'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->wecha_id,FILTER_SANITIZE_STRING);

        $rentset = $tb_rentset->where(array('token'=>$token,'settype'=>'ordertime'))->find();
        if($rentset && $rentset['refield1'] && $rentset['refield3']){
            $cur_h=intval(date("H"));
            $cur_m=intval(date("i"));
            if($cur_m<10){
                $tmp1=intval($cur_h.'0'.$cur_m);
            }else{
                $tmp1=intval($cur_h.$cur_m);
            }
            if($rentset['refield2']<10){
                $tmp2=intval($rentset['refield1'].'0'.$rentset['refield2']);
            }else{
                $tmp2=intval($rentset['refield1'].$rentset['refield2']);
            }
            $orderh=intval($rentset['refield3']);
            $daytype=0;
            if($orderh>=24){
                $orderh=$orderh-24;
                $daytype=1;
            }
            if($rentset['refield4']<10){
                $tmp3=intval($orderh.'0'.$rentset['refield4']);
            }else{
                $tmp3=intval($orderh.$rentset['refield4']);
            }
            if($daytype){
                if($tmp1<$tmp2 && $tmp1>$tmp3){
                    $this->error('非常抱歉，已停止预约，请在'.$rentset['refield1'].'点'.$rentset['refield2'].'分至'.$orderh.'点'.$rentset['refield4'].'分进行预约');
                }
            }else{
                if($tmp1<$tmp2 || $tmp1>$tmp3){
                    $this->error('非常抱歉，已停止预约，请在'.$rentset['refield1'].'点'.$rentset['refield2'].'分至'.$orderh.'点'.$rentset['refield4'].'分进行预约');
                }
            }
        }

        $session_username_name = "token_username_" . $token;
        $where_2['token']=$token;
        if($_SESSION[$session_username_name]){
            $where_2['username']=$_SESSION[$session_username_name];
        }
        $where_2['_string']="wecha_id='".$wecha_id."' OR twid='".$wecha_id."'";
        $userinfo = D('userinfo')->where($where_2)->find();
        if(!$userinfo || ($userinfo && $userinfo['username']=='')){
            $this->error('抱歉,请先登录',U('Index/memberLogin',array('token'=>$this->token)));
        }
        $wherecard1['token']=$token;
        $wherecard1['_string']="wecha_id='".$userinfo['wecha_id']."' OR wecha_id='".$userinfo['twid']."'";
        $uinfocard1 = M('Member_card_create')->where($wherecard1)->find();
        if(!$uinfocard1){
            $this->error('请先进入会员卡领取卡片');
        }
        $where_1      = array('token'=>$token,'tid'=>$tid);
        $rttype     = $tb_renttype->where($where_1)->find();
        if($rttype['group']=='10'){
            $where = array('token'=>$token,'tid'=>$tid);
            $second = $data->where($where)->order('sort desc')->select();
        }else{
            $where = array('token'=>$token,'tid'=>$tid,'sid'=>$sid);
            $second = $data->where($where)->find();
        }

        $count      = $tb_resbook->where(array('token'=>$token,'wecha_id'=>$wecha_id,'type'=>$type))->count();

        $where_3['token'] = $token;
        $where_3['uid'] = $wecha_id;
        $where_3['msgtype'] = array('in','shortdistance,beginlocation');
        $usrequst = $tb_request->where($where_3)->select();
        $beginaddr=array();
        foreach ($usrequst as $key => $value) {
            if($value['msgtype']=='beginlocation'){
                $tem1=explode('|', $value['keyword']);
                $beginaddr['name']=$tem1[1];
                $tem2=explode(',', $tem1[0]);
                $beginaddr['Y']=$tem2[0];
                $beginaddr['X']=$tem2[1];
            }
        }
        //测试用，模拟坐标
        // $beginaddr['name']='五三三医院(北辰大道)(公交站)';
        // $beginaddr['X']='20.00012';
        // $beginaddr['Y']='102.23003';
        if($rttype['group']=='10' && count($usrequst)<2){
            $this->error('抱歉，未获取到当前位置！请关闭当前窗口，重新点击“代驾租车”，发送当前位置');
            exit;
        }
        
        $shortaddress=$this->short_address();

        if(IS_POST){
             $_POST['type']       = filter_var($this->_post('type'),FILTER_SANITIZE_STRING);
             $_POST['tid']        = filter_var($this->_post('tid'),FILTER_VALIDATE_INT);
             $_POST['sid']        = filter_var($this->_post('sid'),FILTER_VALIDATE_INT);
             $_POST['token']      = filter_var($this->_post('token'),FILTER_SANITIZE_STRING);
             $_POST['wecha_id']   = filter_var($this->_post('wecha_id'),FILTER_SANITIZE_STRING);
             $_POST['truename']   = trim(filter_var($this->_post('truename'),FILTER_SANITIZE_STRING));
             $_POST['tel']        = filter_var($this->_post('tel'),FILTER_SANITIZE_STRING);
             $_POST['address']    = filter_var($this->_post('address'),FILTER_SANITIZE_STRING);
             $_POST['info']       = filter_var($this->_post('info'),FILTER_SANITIZE_STRING);
             $_POST['productName']= filter_var($this->_post('productName'),FILTER_SANITIZE_STRING);
             $_POST['orderid']    = self::generateOrderSn();
             $_POST['paid']       = 0;
             $_POST['booktime']   = time();
             if($rttype['group']=="10"){
                $_POST['productName'] = $rttype['name'];
                $_POST['address'] = $beginaddr['X'].','.$beginaddr['Y'].'|'.$beginaddr['name'];
                $_POST['age'] = filter_var($this->_post('age'),FILTER_VALIDATE_INT);
                $begindr=filter_var($this->_post('begin'),FILTER_VALIDATE_INT);
                $enddr=filter_var($this->_post('end'),FILTER_VALIDATE_INT);
                $_POST['choose'] = $begindr.'|'.$enddr;
                $_POST['number'] = filter_var($this->_post('number'),FILTER_VALIDATE_INT);

                if($begindr!='10' && $enddr!='10'){
                    $this->error('非常抱歉，目前只支持以芒市市区为起点或者以芒市市区为终点。');
                }
             }elseif($rttype['group']=="20"){
                $_POST['sex'] = filter_var($this->_post('sex'),FILTER_VALIDATE_INT);
                if($_POST['sex']<=0){
                    $this->error('使用天数格式不正确');
                }
             }
            if($rttype['group']=='10'){
                $where_stork = array('token'=>$_POST['token'],'tid'=>$_POST['tid']);
                $where_stork['learntime'] = $_POST['choose'];
                $checkdata = $data->where($where_stork)->find();
            }else{
                $where_stork = array('token'=>$_POST['token'],'tid'=>$_POST['tid'],'sid'=>$_POST['sid']);
                $checkdata = $data->where($where_stork)->find();
            }

            if(!$checkdata){
                $this->error('对不起，您所选择的项目不存在，请重新下单');
            }
            if($_POST['wecha_id'] == '' || $_POST['token'] =='' || $_POST['truename'] == ''){
                exit($this->error('抱歉,请先关注我们的公众号.',
                    U('Index/index',array('token'=>$_POST['token'],'wecha_id'=>$_POST['wecha_id']))));
            }

            if(intval($checkdata['googsnumber']) <= 0){
                exit($this->error('非常遗憾,您来晚了一步.',
                    U('RentCar/classify',array('token'=>$_POST['token'],'wecha_id'=>$_POST['wecha_id'],'group'=>0))));
            }
            if($rttype['group']=="20"){
                $_POST['payprice']    = $_POST['sex']*$checkdata['oneprice'];
            }else{
                $_POST['payprice']    = $checkdata['oneprice'];
            }
            if($rttype['group']=="10"){
                $_POST['orderName']   = $rttype['name'];
                if($_POST['number']){
                    $_POST['payprice']= $this->_post('addrprice');
                }
                $_POST['sid'] = $checkdata['sid'];
            }else{
                $_POST['orderName']   = $checkdata['name'];
            }

            $_POST['rid']         = $_POST['sid'];
            $uinfo = M('Userinfo')->where(array('token'=>$_POST['token'],'wecha_id'=>$this->wecha_id))->field('id,balance,wecha_id,twid')->find();
            // if($uinfo['balance'] < $_POST['payprice']){
            //     $this->error('余额不足，请先充值');
            // }
            $wherecard['token']=$_POST['token'];
            $wherecard['_string']="wecha_id='".$uinfo['wecha_id']."' OR wecha_id='".$uinfo['twid']."'";
            $uinfocard = M('Member_card_create')->where($wherecard)->find();
            if(!$uinfocard){
                $this->error('无法获取余额信息');
            }
            $insertdata  = $tb_resbook->data($_POST)->add();
            if($insertdata){
                $userreqdel = M("User_request");
                $where_req["token"] = $_POST['token'];
                $where_req["uid"] = $this->wecha_id;
                $where_req["msgtype"] = array('in','shortdistance,beginlocation');
                $userreqlists = $userreqdel->where($where_req)->select();
                foreach ($userreqlists as $key => $value) {
                    $delrid["id"]=$value['id'];
                    $userreqdel->where($delrid)->delete();
                }
                if($rttype['group']=="20"){
                    $data->where(array('sid'=>$_POST['sid'],'tid'=>$_POST['tid'],'token'=>$_POST['token']))->setDec('googsnumber');
                }
                if(floatval($_POST['payprice']) == 0){
                    $this->assign('token',$_POST['token']);
                    $this->assign('wecha_id',$_POST['wecha_id']);
                    $savedata['paid'] = 1;
                    $tb_resbook->where(array('id'=>$insertdata ,'token'=>$token))->save($savedata);

                    Sms::sendSms($_POST['token'], "您的会员 {$_POST['truename']},已经购买了{$_POST['orderName']} 并付款成功,金额为{$_POST['payprice']},订单号为{$_POST['orderid']} 。". date('Y-m-d H:i:s',time()));

                    Sms::sendSms($_POST['token'], "尊敬的 {$_POST['truename']},您购买的{$_POST['orderName']} 已经付款成功,金额为{$_POST['payprice']},订单号为{$_POST['orderid']} 。 ". date('Y-m-d H:i:s',time()),$_POST['tel']);

                    $this->redirect(U("RentCar/mylist", array('token'=>$token,'type'=>'rentcar','wecha_id'=>$this->wecha_id)));
                    // echo "<script type='text/javascript'>parent.location.reload();</script>";
                    // exit;
                }else{
                    $rwhere = array(
                        "token"       => $_POST['token'],
                        "wecha_id"    => $_POST['wecha_id'],
                        "coupon_type" => array("lt", "3"),
                        "id"          => $this->_post("consume_id", "intval"),
                        "is_use"      => "0"
                        );
                    $r_record = M("Member_card_coupon_record")->where($rwhere)->find();

                    if (!$r_record) {
                        $r_record["coupon_id"] = 0;
                    }
                    $r_record["coupon_id"] = 0;
                    $itemid = $r_record["coupon_id"];
                    $price = $_POST['payprice'];
                    $consume_id = $r_record["coupon_id"];
                    $thisCompany = M("Company")->where(array("token" =>$_POST['token'], "isbranch" => 0, "display" => 1))->find();
                    $company_id = $thisCompany['id'];

                    $arr["itemid"] = $itemid;
                    $arr["wecha_id"] = $_POST['wecha_id'];
                    $arr["expense"] = $price;
                    $arr["time"] = $now;
                    $arr["token"] = $_POST['token'];
                    $arr["cat"] = 1;
                    $arr["staffid"] = 0;
                    $arr["usecount"] = 1;
                    $set_exchange = M("Member_card_exchange")->where(array("cardid" => $uinfocard['cardid']))->find();
                    $arr["score"] = intval($set_exchange["reward"]) * $arr["expense"];
                    M("Member_card_coupon")->where(array("id" => $itemid))->setInc("usetime", 1);
                    M("Member_card_coupon_record")->where($rwhere)->save(array("use_time" => time(), "is_use" => "1"));  
                    if($uinfo['balance'] < $_POST['payprice']){
                        $this->success("您好,准备跳转到支付页面,请不要重复刷新页面,请耐心等待...", U("Alipay/pay", array("from" => "RentCar", "orderName" => $_POST['orderName'], "single_orderid" => $_POST['orderid'], "token" => $_POST['token'], "wecha_id" => $_POST['wecha_id'], "price" => trim($arr["expense"]),'type'=>$_POST['type'],'tid'=>$_POST['tid'],'sid'=>$_POST['sid'])));
                    }else{
                        $cardordername = ($itemid == 0 ? $_POST['orderName']."-会员卡余额支付" : $_POST['orderName']."-余额支付除优惠劵外的款项");  
                        $this->redirect(U("CardPay/pay", array("from" => "RentCar", "token" => $_POST['token'], "wecha_id" => $_POST['wecha_id'], "price" => $arr["expense"], "single_orderid" => $_POST['orderid'], "orderName" => $cardordername, "redirect" => "RentCar/payReturn|itemid:" . $itemid . ",usecount:" . $arr["usecount"] . ",score:" . $arr["score"] . ",type:coupon,cardid:" . $uinfocard['cardid'])));
                        exit();
                    }
                }
            }else{
                exit($this->error('Sorry,请重新下单.',
                    U('RentCar/classify',array('token'=>$_POST['token'],'wecha_id'=>$_POST['wecha_id'],
                                        'group'=>0))));
            }

        }
        $this->assign('userinfo',$userinfo);
        $this->assign('uinfocard1',$uinfocard1);
        $this->assign('second',$second);
        $this->assign('secondjson',json_encode($second));
        $this->assign('rttype',$rttype);
        $this->assign('count',$count);
        $this->assign('type',$type);
        $this->assign('beginaddr',$beginaddr);
        $this->assign('deftoday',strftime("%Y-%m-%d" ,time()));
        $this->assign('shortaddress',$shortaddress);
        $this->assign('shaddrjson',json_encode($shortaddress));
        $this->display();
    }

    private function  generateOrderSn(){
        date_default_timezone_set('PRC');
        list($msec, $sec) = explode(' ',microtime());
        return date('ymdHis',$sec).substr($msec,2,6);
    }

    public function payReturn(){
        $tb_resbook = D('reservebook');
        $orderid    =   filter_var($this->_get('orderid'),FILTER_SANITIZE_STRING);
        $token      =   filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $checkOrder = $tb_resbook->where(array('orderid'=>$orderid,'token'=>$token))->find();       //根据订单号查出$order
       if($checkOrder){
            if($checkOrder['paid'] == 1){
                $this->assign('type',$checkOrder['type']);
                $this->assign('token',$checkOrder['token']);
                $this->assign('wecha_id',$checkOrder['wecha_id']);
                Sms::sendSms($checkOrder['token'], "您的会员 {$checkOrder['truename']},已经购买了{$checkOrder['orderName']} 并付款成功,金额为{$checkOrder['payprice']},订单号为{$checkOrder['orderid']}。". date('Y-m-d H:i:s',time()));

                Sms::sendSms($checkOrder['token'], "尊敬的 {$checkOrder['truename']},您购买的{$checkOrder['orderName']} 已经付款成功,金额为{$checkOrder['payprice']},订单号为{$checkOrder['orderid']}。 ". date('Y-m-d H:i:s',time()),$checkOrder['tel']);

                $this->redirect(U("RentCar/mylist", array('token'=>$checkOrder['token'],'type'=>$checkOrder['type'],'wecha_id'=>$checkOrder['wecha_id'])));
            }else{
                M('rentcar_item')->where(array('sid'=>$checkOrder['rid'],'type'=>$checkOrder['type'],'token'=>$checkOrder['token']))->setInc('googsnumber');
            }

       }else{

          exit('订单不存在!');
        }

    }

    public function mylist(){
        $tb_resbook = D('reservebook');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->wecha_id,FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'type'=>$type,'wecha_id'=>$wecha_id);

        $count      = $tb_resbook->where($where)->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
        $books      = $tb_resbook->where($where)->order('booktime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($books as $key => $value) {
            $retitem=D('rentcar_item')->where(array('token'=>$token,'sid'=>$value['rid']))->find();
            $rettype=D('rentcar_type')->where(array('token'=>$token,'tid'=>$retitem['tid']))->find();
            $books[$key]['group']=$rettype['group'];
            $tem1=explode('|', $value['address']);
            $books[$key]['address']=$tem1[1];
        }
        $this->assign('page',$show);
        $this->assign('books',$books);
        $this->display();
    }

    public function payMyOrder(){
        $id         = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->wecha_id,FILTER_SANITIZE_STRING);
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'type'=>$type,'wecha_id'=>$wecha_id,'id'=>$id);
        $tb_resbook = D('reservebook');
        $data  = $tb_resbook->where($where)->find();

        $uinfo = M('Userinfo')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->field('id,balance,wecha_id,twid')->find();
        if($uinfo['balance']>0 && $uinfo['balance'] < $data['payprice']){
            $this->error('余额不足，请先充值');
        }
        $wherecard['token']=$token;
        $wherecard['_string']="wecha_id='".$uinfo['wecha_id']."' OR wecha_id='".$uinfo['twid']."'";
        $uinfocard = M('Member_card_create')->where($wherecard)->find();
        if(!$uinfocard){
        $this->error('无法获取余额信息');
        }

        $rtitem = D('rentcar_item')->where(array('token'=>$token,'sid'=>$data['rid']))->find();
        $rttype = D('rentcar_type')->where(array('token'=>$token,'tid'=>$rtitem['tid']))->find();
        if($data){
            if($rttype['group']=="20"){
                $rtitem->where(array('sid'=>$data['rid'],'tid'=>$rtitem['tid'],'token'=>$token))->setDec('googsnumber');
            }
            if(floatval($data['payprice']) == 0){
                $this->assign('token',$token);
                $this->assign('wecha_id',$wecha_id);
                $savedata['paid'] = 1;
                $tb_resbook->where(array('id'=>$data['id'] ,'token'=>$token))->save($savedata);

                Sms::sendSms($_POST['token'], "您的会员 {$_POST['truename']},已经购买了{$_POST['orderName']} 并付款成功,金额为{$_POST['payprice']},订单号为{$_POST['orderid']} 。". date('Y-m-d H:i:s',time()));

                Sms::sendSms($_POST['token'], "尊敬的 {$_POST['truename']},您购买的{$_POST['orderName']} 已经付款成功,金额为{$_POST['payprice']},订单号为{$_POST['orderid']} 。 ". date('Y-m-d H:i:s',time()),$_POST['tel']);

                $this->redirect(U("RentCar/mylist", array('token'=>$token,'type'=>'rentcar','wecha_id'=>$this->wecha_id)));
                // echo "<script type='text/javascript'>parent.location.reload();</script>";
                // exit;
            }else{
                $arr["itemid"] = 0;
                $arr["wecha_id"] = $wecha_id;
                $arr["expense"] = $data['payprice'];
                $arr["time"] = $now;
                $arr["token"] = $token;
                $arr["cat"] = 1;
                $arr["staffid"] = 0;
                $arr["usecount"] = 1; 
                if($uinfo['balance']==0){
                    $this->success("您好,准备跳转到支付页面,请不要重复刷新页面,请耐心等待...", U("Alipay/pay", array("from" => "RentCar", "orderName" => $data['orderName'], "single_orderid" => $data['orderid'], "token" => $token, "wecha_id" => $wecha_id, "price" => trim($data['payprice']),'type'=>$type,'tid'=>$rtitem['tid'],'sid'=>$data['rid'])));
                }elseif($uinfo['balance'] >= $data['payprice']){
                    $cardordername = ($itemid == 0 ? $rtitem['name']."-会员卡余额支付" : $rtitem['name']."-余额支付除优惠劵外的款项"); 
                    $this->redirect(U("CardPay/pay", array("from" => "RentCar", "token" => $token, "wecha_id" => $wecha_id, "price" => $arr["expense"], "single_orderid" => $data['orderid'], "orderName" => $cardordername, "redirect" => "RentCar/payReturn|itemid:" . $arr["itemid"] . ",usecount:" . $arr["usecount"] . ",score:" . $arr["score"] . ",type:coupon,cardid:" . $uinfocard['card_id'])));
                    exit();
                }
            }
        }else{
            exit($this->error('Sorry,请重新下单.',
            U('RentCar/classify',array('token'=>$token,'wecha_id'=>$wecha_id,
                                'group'=>0))));
        }
    }
    public function delOrder(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //echo '此功能只能在微信浏览器中使用';exit;
        }
        $id         = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->wecha_id,FILTER_SANITIZE_STRING);
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $tb_resbook =   M('reservebook');
        $check      = $tb_resbook->where(array('id'=>$id,'token'=>$token,'wecha_id'=>$wecha_id,'type'=>$type))->find();
        if($check){
            $tb_resbook->where(array('id'=>$check['id'],'wecha_id'=>$check['wecha_id'],'type'=>$check['type'],'token'=>$check['token']))->delete();
            $this->success('删除成功',U('RentCar/mylist',array('token'=>$token,'wecha_id'=>$wecha_id,'type'=>$type)));
             exit;
         }else{
            $this->error('非法操作',U('RentCar/mylist',array('token'=>$token,'wecha_id'=>$wecha_id,'type'=>$type)));
             exit;
         }
    }

    public function mgrorders(){
        $data       = D('reservebook');
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $orders1 = $data->where(array('token'=>$token,'remate'=>0,'type'=>'rentcar','paid'=>1))->order('booktime desc')->select();
        $orders2 = $data->where(array('token'=>$token,'remate'=>1,'type'=>'rentcar','paid'=>1))->order('booktime desc')->select();
        $orders3 = $data->where(array('token'=>$token,'type'=>'rentcar','paid'=>0))->order('booktime desc')->select();
        $typelist=array('0'=>'待处理','1'=>'进行中','2'=>'待付款');
        $orders[0]=$orders1;
        $orders[1]=$orders2;
        $orders[2]=$orders3;
        $this->assign('orders',$orders);
        $this->assign('typelist',$typelist);
        $this->display();
    }

    public function orderDetail(){
        $data       = D('reservebook');
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $id         = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $orderdet = $data->where(array('token'=>$token,'type'=>'rentcar','id'=>$id))->find();
        $rentitem = D('rentcar_item')->where(array('token'=>$token,'sid'=>$orderdet['rid']))->find();
        $renttype = D('rentcar_type')->where(array('token'=>$token,'tid'=>$rentitem['tid']))->find();
        $tmp = explode('|', $orderdet['address']);
        $curaddr['name'] = $tmp[1];
        $tmp1 = explode(',', $tmp[0]);
        $curaddr['X'] = $tmp1[0];
        $curaddr['Y'] = $tmp1[1];
        $orderdet['strbooktime']=date('Y-m-d H:i:s',$orderdet['booktime']);

        $tmp = explode('|', $orderdet['choose']);
        $shaddre = $this->short_address();
        $begin = $shaddre[$tmp[0]];
        $end = $shaddre[$tmp[1]];

        if(IS_POST){
            $_POST['remate'] = filter_var($this->_post('remate'),FILTER_VALIDATE_INT);
            $_POST['kfinfo'] = filter_var($this->_post('kfinfo'),FILTER_SANITIZE_STRING);
            $tb_resbook = M('reservebook');
            $check = $tb_resbook->where(array('id'=>$id,'token'=>$token))->find();
            if($check){
                $tb_resbook->where(array('id'=>$check['id'],'token'=>$check['token']))->save($_POST);
                $this->success('保存成功',U('RentCar/mgrorders',array('token'=>$check['token'])));
                exit;
            }else{
                $this->error('非法操作',U('RentCar/mgrorders',array('token'=>$check['token'])));
                exit;
            }
        }

        $this->assign('begin',$begin);
        $this->assign('end',$end);
        $this->assign('curaddr',$curaddr);
        $this->assign('orderdet',$orderdet);
        $this->assign('rentitem',$rentitem);
        $this->assign('renttype',$renttype);
        $this->display();
    }

    public function plist(){
        $this->token=$this->_get('token');
        $reply_info_db=M('Reply_info');
        $config=$reply_info_db->where(array('token'=>$this->token,'infotype'=>'album'))->find();
        if ($config){
            $headpic=$config['picurl'];
        }else {
            $headpic='/tpl/Wap/default/common/css/Photo/banner.jpg';
        }
        $this->assign('headpic',$headpic);

        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $get_id     = M('busines_pic')->field('bid_id,type,ablum_id')->where(array('bid_id'=>$bid,'token'=>$token,'type'=>$type))->find();
        $info=M('Photo')->field('title,picurl,id')->where(array('token'=>$token,'id'=>$get_id['ablum_id']))->find();
    $photo_list=M('Photo_list')->where(array('token'=>$token,'pid'=>$get_id['ablum_id'],'status'=>1))->order('sort desc')->select();
        $this->assign('info',$info);
        $this->assign('photo',$photo_list);
        $this->display();
    }


    public function comments(){
        $data       = D('busines_comment');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'type'=>$type,'bid_id'=>$bid);
        $count      = $data->where($where)->count();
        $Page       = new Page($count,6);
        $show       = $Page->show();
        $comments= $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('count',6);
        $this->assign('comments',$comments);
        $this->display();

    }

    public function comlist(){
        $data       = D('busines_comment');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $cid        = filter_var($this->_get('cid'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'type'=>$type,'cid'=>$cid);
        $comments= $data->where($where)->order('sort desc')->find();
        $this->assign('classify',$comments);
        $this->display();
    }

    public function getaddressmap()
    {
        $this->assign('isamap',$this->isamap);
        $this->display();
    }

    public function short_address(){
        return array('10'=>'芒市城区',
            '11'=>'龙陵',
            '12'=>'勐嘎',
            '13'=>'遮放',
            '14'=>'瑞丽',
            '15'=>'畹町',
            '16'=>'盈江',
            '17'=>'梁河',
            '18'=>'陇川',
            '19'=>'腾冲');
    }
}