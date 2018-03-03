<?php
class RentCarAction extends UserAction{


    public function _initialize() {
        parent::_initialize();
        $_POST['token'] = session('token');
		$this->canUseFunction('RentCar');
    }

    public function index(){
        $data       = D('rentcar_set');
        $where      = array('token'=>session('token'));

        for($i=0;$i<24;$i++){
            $hours[$i]=$i;
        }
        for($k=0;$k<60;$k++){
            $minutes[$k]=$k;
        }
        $ordertime=array();
        $regpesent=array();
        $fillpesent=array();
        $ordertimeday=0;
        $setlist = $data->where($where)->order('settype,sort desc')->select();
        foreach ($setlist as $key => $value) {
            if($value['settype']=="ordertime"){
                $ordertime=$value;
                $tmp=intval($ordertime['refield3']);
                if($tmp>24){
                    $ordertimeday=24;
                    $ordertime['refield3']=$tmp-$ordertimeday;
                }
            }
            if($value['settype']=="regpesent"){
                $regpesent=$value;
            }
            if($value['settype']=="fillpesent"){
                $fillpesent[$key]=$value;
            }
        }
        if(IS_POST){
            $setdata=array();
            $setdata['refield1'] = filter_var($this->_post('orderbeginh'),FILTER_VALIDATE_INT);
            $setdata['refield2'] = filter_var($this->_post('orderbeginm'),FILTER_VALIDATE_INT);
            $setdata['refield3'] = filter_var($this->_post('orderendh'),FILTER_VALIDATE_INT);
            $setdata['refield4'] = filter_var($this->_post('orderendm'),FILTER_VALIDATE_INT);
            if($ordertime){
                $daytype=filter_var($this->_post('daytype'),FILTER_VALIDATE_INT);
                if($daytype==24){
                    $setdata['refield3']=$setdata['refield3']+$daytype;
                }
                $rec1=$data->where(array('token'=>$ordertime['token'],'id'=>$ordertime['id']))->save($setdata);
            }else{
                $setdata['token'] = session('token');
                $setdata['settype'] = 'ordertime';
                $setdata['sort'] = 0;
                $daytype=filter_var($this->_post('daytype'),FILTER_VALIDATE_INT);
                if($daytype==24){
                    $setdata['refield3']=$setdata['refield3']+$daytype;
                }
                $rec1=$data->data($setdata)->add();
            }
            $setdata=array();
            if($regpesent){
                $setdata['refield1'] = filter_var($this->_post('regpesent'),FILTER_VALIDATE_INT);
                $rec2=$data->where(array('token'=>$regpesent['token'],'id'=>$regpesent['id']))->save($setdata);
            }else{
                $setdata['token'] = session('token');
                $setdata['settype'] = 'regpesent';
                $setdata['sort'] = 0;
                $setdata['refield1'] = filter_var($this->_post('regpesent'),FILTER_VALIDATE_INT);
                $rec2=$data->data($setdata)->add();
            }
            $setdata=array();
            $items = $_REQUEST['add'];
            $new_item = array();
            foreach($items as $key=>$value){
                foreach($value as $k=>$v){
                    if($v==''){
                        exit($this->success('充值赠送设置各数据项不能有空值',U("RentCar/index",array('token'=>session('token')))));
                    }
                    $new_item[$k][$key] = $v;
                }
            }
            $data->where(array('token'=>session('token'),'settype'=>'fillpesent'))->delete();
            foreach ($new_item as $key => $value) {
                $setdata['token'] = session('token');
                $setdata['settype'] = 'fillpesent';
                $setdata['sort'] = $value['sort'];
                $setdata['refield1'] = $value['floor'];
                $setdata['refield2'] = $value['ceil'];
                $setdata['refield3'] = $value['pesent'];
                $rec3=$data->data($setdata)->add();
            }
            if($rec1 || $rec2 || $rec3){
                exit($this->success('保存成功',U("RentCar/index",array('token'=>session('token')))));
            }else{
                exit($this->error('保存失败',U("RentCar/index",array('token'=>session('token')))));
            }
        }
        $this->assign('ordertime',$ordertime);
        $this->assign('regpesent',$regpesent);
        $this->assign('fillpesent',$fillpesent);
        $this->assign('hours',$hours);
        $this->assign('minutes',$minutes);
        $this->assign('ordertimeday',$ordertimeday);
        $this->display();
    }

    public function delpresent(){
        $token = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $id  = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $rentset = M('rentcar_set');
        $find = array('id'=>$id,'type'=>'fillpesent','token'=>$token);
        $result = $rentset->where($find)->find();
         if($result){
            $rentset->where($find)->delete();
            $this->success('删除成功',U("RentCar/index",array('token'=>session('token'))));
             exit;
         }else{
         exit($this->error('非法操作,请稍候再试',U("RentCar/index",array('token'=>session('token')))));
         }
    }

    public function classify(){
        $data       = D('busines_main');
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>session('token'),'type'=>$type);
        $count      = $data->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();

        $patterns = array();
        $patterns[0] = '/m=Business/';
        $replacements = array();
        $replacements[0] = "m=Business&type=$type";
        $show =  preg_replace($patterns, $replacements, $show);


        $busines_main     = $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
       // var_dump($show);
        $i = 0;
        foreach($busines_main  as $val){
            $busines = M("busines")->where(array('token'=>session('token'),'bid'=>$val['bid_id']))->field('mtitle')->find();
            array_push($busines_main[$i],$busines['mtitle']);
             unset($busines);
             ++$i;
        }
        //var_dump($busines_main);
        $this->assign('page',$show);
        $this->assign('busines_main',$busines_main);
        $this->display();
    }

    public function classify_add(){
        $t_busines = M("busines");
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where = array('token'=>session('token'),'type'=>$type);
        $busines_list = $t_busines->where($where)->order('sort desc')->field('bid,mtitle')->select();
        $this->assign('busines_list',$busines_list);
        $t_busines_main = D('busines_main');
        $mid  = filter_var($this->_get('mid'),FILTER_VALIDATE_INT);
        $where_2 = array('token'=>session('token'),'type'=>$type,'mid'=>$mid);
        $busines_main = $t_busines_main->where($where_2)->find();
        if(IS_POST){
            $filters = array(
                'name'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
                'main_desc'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                )
            );

            $check = filter_var_array($_POST,$filters);
            if(!$check){
                exit($this->error('表单包含敏感字符!'));
            }else{
                $_POST['token'] = session('token');
                if(!$t_busines_main->create()){
                    exit($this->error($t_busines_main->getError()));
                }else{
                    $mid = filter_var($this->_post('mid'),FILTER_VALIDATE_INT);
                    $status = filter_var($this->_post('status'),FILTER_SANITIZE_STRING);
                    if('edit'==$status && $mid != ''){
                        $o =  $t_busines_main->where(array('mid'=>$mid, 'token'=>session('token'),'type'=>$type))->save($_POST);
                        if($o){
                            exit($this->success('修改成功',U("Business/classify",array('token'=>session('token'),'type'=>$type))));
                        }else{
                            exit($this->error('修改失败',U("Business/classify",array('token'=>session('token'),'type'=>$type))));
                        }
                    }else{

                        if($id=$t_busines_main->data($_POST)->add()){
                            $this->success('添加成功',U("Business/classify",array('token'=>session('token'),'type'=>$type)));exit;
                        }else{
                    exit($this->error('务器繁忙,添加失败,请稍候再试',U("Business/classify",array('token'=>session('token'),'type'=>$type))));
                        }
                    }//edit & add
                }

            }
        }
        $this->assign('busines_main',$busines_main);
        $this->display();
    }

    public function classify_del(){
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $mid  = filter_var($this->_get('mid'),FILTER_VALIDATE_INT);
        $t_busines_main = M('busines_main');

        $find = array('mid'=>$mid,'type'=>$type,'token'=>session('token'));
        $result = $t_busines_main->where($find)->find();
         if($result){
            $t_busines_main->where(array('mid'=>$result['mid'],'type'=>$result['type'],'token'=>session('token')))->delete();
            exit($this->success('删除成功',U("Business/classify",array('token'=>session('token'),'type'=>$result['type']))));
         }else{
            exit($this->error('非法操作,请稍候再试',U("Business/classify",array('token'=>session('token'),'type'=>$type))));
         }
    }

    public function project(){
        $data       = D('rentcar_type');
        $where      = array('token'=>session('token'));
        $count      = $data->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $rentcar_type     = $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $patterns = array();
        $patterns[0] = '/m=RentCar/';
        $replacements = array();
        $replacements[0] = "m=RentCar";
        $show =  preg_replace($patterns, $replacements, $show);
        $this->assign('renttypecnt',count($rentcar_type));
        $this->assign('page',$show);
        $this->assign('rentcar_type',$rentcar_type);
        $this->display();
    }

    public function project_add(){
        $t_rentcar_type = D('rentcar_type');
        $tid  = filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $where_2 = array('token'=>session('token'),'tid'=>$tid);
        $rentcar_type = $t_rentcar_type->where($where_2)->find();
        $rentcar_typeall = $t_rentcar_type->where(array('token'=>session('token')))->select();
        $rent_server=$this->rentcar_service();
        if(!$tid){
            foreach ($rentcar_typeall as $key => $value) {
                unset($rent_server[$value['group']]);
            }
        }

        if(IS_POST){
            $filters = array(
                'name'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
                'second_desc'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                )
            );

            $check = filter_var_array($_POST,$filters);
            if(!$check){
                exit($this->error('表单包含敏感字符!'));
            }else{
                $_POST['token'] = session('token');
                if(!$t_rentcar_type->create()){
                    exit($this->error($t_rentcar_type->getError()));
                }else{
                    $tid = filter_var($this->_post('tid'),FILTER_VALIDATE_INT);
                    $status = filter_var($this->_post('status'),FILTER_SANITIZE_STRING);
                    $_POST['name']=filter_var($this->_post('name'),FILTER_SANITIZE_STRING);
                    if('edit'==$status && $tid != ''){
                        $o =  $t_rentcar_type->where(array('tid'=>$tid, 'token'=>session('token')))->save($_POST);
                        if($o){
                            exit($this->success('修改成功',U("RentCar/project",array('token'=>session('token')))));
                        }else{
                            exit($this->error('修改失败',U("RentCar/project",array('token'=>session('token')))));
                        }
                    }else{
                        if($id=$t_rentcar_type->data($_POST)->add()){
                            $this->success('添加成功',U("RentCar/project",array('token'=>session('token'))));exit;
                        }else{
                        exit($this->error('务器繁忙,添加失败,请稍候再试',U("RentCar/project",array('token'=>session('token')))));
                        }
                    }//edit & add
                }

            }
        }
        $this->assign('rent_server',$rent_server);
        $this->assign('rentcar_type',$rentcar_type);
        $this->display();
    }

    public function project_del(){
        $tid  = filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $t_rentcar_type = M('rentcar_type');
        $t_rentcar_item = M('rentcar_item');

        $find = array('tid'=>$tid, 'token'=>session('token'));
        $result = $t_rentcar_item->where($find)->count();
        if($result>0){
            $this->error('该类型下还有车型，请先将车型删除，才能删除该类型',U("RentCar/project",array('token'=>session('token'))));
        }else{
            $find = array('tid'=>$tid, 'token'=>session('token'));
            $result = $t_rentcar_type->where($find)->find();
             if($result){
                $t_rentcar_type->where(array('tid'=>$result['tid'],'token'=>session('token')))->delete();
                exit($this->success('删除成功',U("RentCar/project",array('token'=>session('token')))));
             }else{
                exit($this->error('非法操作,请稍候再试',U("RentCar/project",array('token'=>session('token')))));
             }
        }
    }

    public function project_item(){
        $tid  = filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $data       = D('rentcar_item');
        $where      = array('token'=>session('token'),'tid'=>$tid);
        $count      = $data->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $rentcar_item     = $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $patterns = array();
        $patterns[0] = '/m=RentCar/';
        $replacements = array();
        $replacements[0] = "m=RentCar";
        $show =  preg_replace($patterns, $replacements, $show);

        foreach ($rentcar_item as $key => $value) {
            $typename = M("rentcar_type")->where(array('token'=>session('token'),'tid'=>$value['tid']))->field('name')->find();
            $rentcar_item[$key]['typename']=$typename['name'];
            unset($typename);
        }

        $this->assign('page',$show);
        $this->assign('rentcar_item',$rentcar_item);
        $this->assign('tid',$tid);
        $this->display();
    }

    public function project_item_add(){
        $tid  = filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $sid  = filter_var($this->_get('sid'),FILTER_VALIDATE_INT);
        $t_rentcar_type = M("rentcar_type");
        $t_rentcar_item = D('rentcar_item');

        if(!$sid){
            $renttype=$t_rentcar_type->where(array('token'=>session('token'),'tid'=>$tid))->find();
            $itemcnt = $t_rentcar_item->where(array('token'=>session('token'),'tid'=>$tid))->count();
            if(($renttype['group']=='30' || $renttype['group']=='40') && $itemcnt>0){
                $this->error('该类型只允许添加一个项目');
            }
        }

        $where = array('token'=>session('token'),'tid'=>$tid);
        $rentcar_type_list = $t_rentcar_type->where($where)->find();
        $this->assign('rentcar_type',$rentcar_type_list);

        $where_2 = array('token'=>session('token'),'tid'=>$tid,'sid'=>$sid);
        $rentcar_item = $t_rentcar_item->where($where_2)->find();
        $addr=explode('|', $rentcar_item['learntime']);

        $addresslist=$this->short_address();
        if(IS_POST){
            $filters = array(
                'name'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
                'main_desc'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                )
            );

            $check = filter_var_array($_POST,$filters);
            if(!$check){
                exit($this->error('表单包含敏感字符!'));
            }else{
                $_POST['token'] = session('token');
                if($rentcar_type_list['group']=='10'){
                    $_POST['name']=$addresslist[$_POST['beginp']].'--'.$addresslist[$_POST['endp']];
                    $_POST['learntime']=$_POST['beginp'].'|'.$_POST['endp'];
                }
                if(!$t_rentcar_item->create()){
                    exit($this->error($t_rentcar_item->getError()));
                }else{
                    $sid = filter_var($this->_post('sid'),FILTER_VALIDATE_INT);
                    $tid = filter_var($this->_post('tid'),FILTER_VALIDATE_INT);
                    $status = filter_var($this->_post('status'),FILTER_SANITIZE_STRING);
                    if('edit'==$status && $sid != ''){
                        $o =  $t_rentcar_item->where(array('sid'=>$sid, 'token'=>session('token')))->save($_POST);
                        if($o){
                            exit($this->success('修改成功',U("RentCar/project_item",array('token'=>session('token'),'tid'=>$tid))));
                        }else{
                            exit($this->error('修改失败',U("RentCar/project_item",array('token'=>session('token'),'tid'=>$tid))));
                        }
                    }else{

                        if($sid=$t_rentcar_item->data($_POST)->add()){
                            $this->success('添加成功',U("RentCar/project_item",array('token'=>session('token'),'tid'=>$tid)));exit;
                        }else{
                        exit($this->error('务器繁忙,添加失败,请稍候再试',U("RentCar/project_item",array('token'=>session('token'),'tid'=>$tid))));
                        }
                    }//edit & add
                }

            }
        }
        $this->assign('rentcar_item',$rentcar_item);
        $this->assign('addresslist',$addresslist);
        $this->assign('addr',$addr);
        $this->display();
    }

    public function project_item_del(){
        $tid  = filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $sid  = filter_var($this->_get('sid'),FILTER_VALIDATE_INT);
        $t_rentcar_item = M('rentcar_item');
        $find = array('sid'=>$sid,'tid'=>$tid, 'token'=>session('token'));
        $result = $t_rentcar_item->where($find)->find();
         if($result){
            $t_rentcar_item->where(array('sid'=>$result['sid'],'tid'=>$result['tid'],'token'=>session('token')))->delete();
            exit($this->success('删除成功',U("RentCar/project_item",array('token'=>session('token'),'tid'=>$tid))));
         }else{
            exit($this->error('非法操作,请稍候再试',U("RentCar/project_item",array('token'=>session('token'),'tid'=>$tid))));
         }
    }

    public function poster(){
        $data       = D('busines_pic');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>session('token'),'type'=>$type);
        $count      = $data->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $busines_pic= $data->where($where)->order('pid desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $i = 0;
        $j = 0;
        foreach($busines_pic  as $val){
            $busines = M("busines")->where(array('token'=>session('token'),'bid'=>$val['bid_id']))->field('mtitle')->find();
            $photo   = M('photo')->where(array('token'=>session('token'),'id'=>$val['ablum_id']))->field('title')->find();
            array_push($busines_pic[$i],$busines['mtitle']);
            array_push($busines_pic[$j],$photo['title']);
             unset($busines);
             unset($photo);
             ++$j;
             ++$i;
        }
        $patterns = array();
        $patterns[0] = '/m=Business/';
        $replacements = array();
        $replacements[0] = "m=Business&type=$type";
        $show =  preg_replace($patterns, $replacements, $show);

        $this->assign('page',$show);
        $this->assign('busines_pic',$busines_pic);
        $this->display();
    }

    public function poster_add(){
        $t_busines = M("busines");
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where = array('token'=>session('token'),'type'=>$type);
        $busines_list = $t_busines->where($where)->order('sort desc')->field('bid,mtitle')->select();
        $this->assign('busines_list',$busines_list);
        $photo = M('photo')->where(array('token'=>session('token'),'status'=>1))->order('id desc')->field('id,title')->select();
        $this->assign('photo',$photo);
        $t_busines_second = D('busines_pic');
        $pid  = filter_var($this->_get('pid'),FILTER_VALIDATE_INT);
        $where_2 = array('token'=>session('token'),'type'=>$type,'pid'=>$pid);
        $busines_second = $t_busines_second->where($where_2)->find();
        if(IS_POST){
            $filters = array(
                'picurl_1'=>array(
                    'filter'=>FILTER_VALIDATE_URL
                ),
                'picurl_2'=>array(
                    'filter'=>FILTER_VALIDATE_URL
                ),
                'picurl_3'=>array(
                    'filter'=>FILTER_VALIDATE_URL
                ),
                'picurl_4'=>array(
                    'filter'=>FILTER_VALIDATE_URL
                ),
                'picurl_5'=>array(
                    'filter'=>FILTER_VALIDATE_URL
                )
            );

            $check = filter_var_array($_POST,$filters);
            if(!$check){
                exit($this->error('包含特殊字符,请检查后再提交.',U("Business/poster",array('token'=>session('token'),'type'=>$type))));
            }else{
                $_POST['token'] = session('token');
                if(!$t_busines_second->create()){
                    exit($this->error($t_busines_second->getError()));
                }else{
                    $pid = filter_var($this->_post('pid'),FILTER_VALIDATE_INT);
                    $status = filter_var($this->_post('status'),FILTER_SANITIZE_STRING);

                    if('edit'==$status && $pid != ''){
                        $o =  $t_busines_second->where(array('pid'=>$pid, 'token'=>session('token'),'type'=>$type))->save($_POST);
                        if($o){
                            exit($this->success('修改成功',U("Business/poster",array('token'=>session('token'),'type'=>$type))));
                        }else{
                            exit($this->error('修改失败',U("Business/poster",array('token'=>session('token'),'type'=>$type))));
                        }
                    }else{

                        if($id=$t_busines_second->data($_POST)->add()){
                            $this->success('添加成功',U("Business/poster",array('token'=>session('token'),'type'=>$type)));exit;
                        }else{

                exit($this->error('务器繁忙,添加失败,请稍候再试',U("Business/poster",array('token'=>session('token'),'type'=>$type))));
                        }
                    }//edit & add
                }

            }
        }
        $this->assign('busines_second',$busines_second);
        $this->display();
    }

    public function poster_del(){
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $pid  = filter_var($this->_get('pid'),FILTER_VALIDATE_INT);
        $t_busines_main = M('busines_pic');
        $find = array('pid'=>$pid,'type'=>$type,'token'=>session('token'));
        $result = $t_busines_main->where($find)->find();
         if($result){
            $t_busines_main->where(array('pid'=>$result['pid'],'type'=>$result['type'],'token'=>session('token')))->delete();
            exit($this->success('删除成功',U("Business/poster",array('token'=>session('token'),'type'=>$result['type']))));
         }else{
           exit($this->error('非法操作！请稍候再试',U("Business/poster",array('token'=>session('token'),'type'=>$type))));
         }
    }

    public function comments(){
        $data       = D('busines_comment');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>session('token'),'type'=>$type);
        $count      = $data->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $comments= $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $patterns = array();
        $patterns[0] = '/m=RentCar/';
        $replacements = array();
        $replacements[0] = "m=RentCar&type=$type";
        $show =  preg_replace($patterns, $replacements, $show);

        $this->assign('page',$show);
        $this->assign('type',$type);
        $this->assign('comments',$comments);
        $this->display();

    }


    public function comments_add(){
        $t_busines_comment = D('busines_comment');
        $cid  = filter_var($this->_get('cid'),FILTER_VALIDATE_INT);
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where_2 = array('token'=>session('token'),'type'=>$type,'cid'=>$cid);
        $comments = $t_busines_comment->where($where_2)->find();
        if(IS_POST){
            $filters = array(
                'name'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
                'face_picurl'=>array(
                    'filter'=>FILTER_VALIDATE_URL
                ),
                'position'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
                'face_desc'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
                'comment'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                )
            );

            $check = filter_var_array($_POST,$filters);
            if(!$check){
          exit($this->error('表单包含不允许字符.',U("RentCar/comments",array('token'=>session('token'),'type'=>$type))));
            }else{
                $_POST['token'] = session('token');
                if(!$t_busines_comment->create()){
                    exit($this->error($t_busines_comment->getError()));
                }else{
                    $cid = filter_var($this->_post('cid'),FILTER_VALIDATE_INT);
                    $status = filter_var($this->_post('status'),FILTER_SANITIZE_STRING);

                    if('edit'==$status && $cid != ''){
                        $o =  $t_busines_comment->where(array('cid'=>$cid, 'token'=>session('token'),'type'=>$type))->save($_POST);
                        if($o){
                            exit($this->success('修改成功',U("RentCar/comments",array('token'=>session('token'),'type'=>$type))));
                        }else{
                            exit($this->error('修改失败',U("RentCar/comments",array('token'=>session('token'),'type'=>$type))));
                        }
                    }else{

                        if($id=$t_busines_comment->data($_POST)->add()){
                            $this->success('添加成功',U("RentCar/comments",array('token'=>session('token'),'type'=>$type)));exit;
                        }else{
                    exit($this->error('服务器繁忙,添加失败,请稍候再试',U("RentCar/comments",array('token'=>session('token'),'type'=>$type))));
                        }
                    }//edit & add
                }

            }
        }
        $this->assign('comments',$comments);
        $this->assign('type',$type);
        $this->display();
    }

    public function comments_del(){
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $cid  = filter_var($this->_get('cid'),FILTER_VALIDATE_INT);
        $t_busines_main = M('busines_comment');
        $find = array('cid'=>$cid,'type'=>$type,'token'=>session('token'));
        $result = $t_busines_main->where($find)->find();
         if($result){
            $t_busines_main->where(array('cid'=>$result['cid'],'type'=>$result['type'],'token'=>session('token')))->delete();
            exit($this->success('删除成功',U("RentCar/comments",array('token'=>session('token'),'type'=>$result['type']))));
         }else{
            exit($this->error('非法操作！请稍候再试',U("RentCar/comments",array('token'=>session('token'),'type'=>$type))));
         }
    }

    public function orders(){
        $t_reservebook = M('Reservebook');
        $type          = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where         = array('token'=>session('token'),'type'=>$type,"orderid!=''");
        $remate          = filter_var($this->_post('remate'),FILTER_SANITIZE_STRING);
        $booktime          = filter_var($this->_post('booktime'),FILTER_SANITIZE_STRING);
        $rttype          = filter_var($this->_post('rttype'),FILTER_SANITIZE_STRING);
        $where['remate']=array('neq','3');
        if($remate!=''){
            $where['remate']=array('eq',$remate);
        }
        if($booktime!=''){
            $begintime=strtotime($booktime.' 00:00:00');
            $endtime=strtotime($booktime.' 23:59:59');
            $where['booktime']=array('between',array($begintime,$endtime));
        }

        $count         = $t_reservebook->where($where)->count();
        $Page          = new Page($count,50);
        $show          = $Page->show();
        $books = $t_reservebook->where($where)->order('booktime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
                $patterns = array();
        $patterns[0] = '/m=RentCar/';
        $replacements = array();
        $replacements[0] = "m=RentCar&type=$type";
        $show =  preg_replace($patterns, $replacements, $show);

        foreach ($books as $key => $value) {
            $paymode=M('member_card_pay_record')->where(array('token'=>session('token'),'orderid'=>$value['orderid']))->find();
            if($value['paid']==1){
                if($paymode){
                    $books[$key]['paymode']='余额支付';
                }else{
                    $books[$key]['paymode']='现金支付';
                }
            }else{
                $books[$key]['paymode']='';
            }
        }

        $this->assign('page',$show);
        $this->assign('books',$books);
        $this->assign('type',$type);
        $this->assign('count',$t_reservebook->where($where)->count());
        $where2 = array('token'=>session('token'),'type'=>$type,'paid'=>1);
        $where3 = array('token'=>session('token'),'type'=>$type,'paid'=>0);
        $where4 = array('token'=>session('token'),'type'=>$type,'remate'=>0);
        $where5 = array('token'=>session('token'),'type'=>$type,'remate'=>1);
        $where6 = array('token'=>session('token'),'type'=>$type,'remate'=>2);
        $this->assign('ok_count',$t_reservebook->where($where2)->count());
        $this->assign('lose_count',$t_reservebook->where($where3)->count());
        $this->assign('call_count',$t_reservebook->where($where4)->count());
        $this->assign('ing_count',$t_reservebook->where($where5)->count());
        $this->assign('fin_count',$t_reservebook->where($where6)->count());
        $this->display();
    }

    public function order_del(){
        $id             = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $type           = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $t_reservebook  = M('Reservebook');
        $where          = array('id'=>$id,'token'=>session('token'),'type'=>$type);
        $check          = $t_reservebook->where($where)->find();
        $payrec         = M('member_card_pay_record')->where(array('orderid'=>$check['orderid'],'token'=>session('token')))->find();
        if(!empty($check)){
            if($check['paid']==0){
                $t_reservebook->where(array('id'=>$check['id'],'token'=>session('token'),'type'=>$type))->save(array('remate'=>'3'));
                $this->success('撤单成功，未付款，不需要退任何费用',U("RentCar/orders",array('token'=>session('token'),'type'=>$type)));
            }else{
                if($payrec){
                    $uinfo=M('Userinfo')->where(array('token'=>session('token'),'wecha_id'=>$check['wecha_id']))->find();
                    if($uinfo){
                        $newpayrec['orderid']=$payrec['orderid'];
                        $newpayrec['ordername']='撤单退余额';
                        $newpayrec['createtime']=time();
                        $newpayrec['paid']='1';
                        $newpayrec['price']=$check['payprice'];
                        $newpayrec['token']=$payrec['token'];
                        $newpayrec['wecha_id']=$payrec['wecha_id'];
                        $newpayrec['module']=$payrec['module'];
                        $newpayrec['type']='1';
                        $newpayrec['company_id']=$payrec['company_id'];
                        $newpayrec['card_id']=$payrec['card_id'];
                        M('member_card_pay_record')->data($newpayrec)->add();
                        $curbalance=$uinfo['balance']+$check['payprice'];
                        M('Userinfo')->where(array('id'=>$uinfo['id']))->save(array('balance'=>$curbalance));
                    }
                    $t_reservebook->where(array('id'=>$check['id'],'token'=>session('token'),'type'=>$type))->save(array('remate'=>'3'));
                    $this->success('撤单成功，会员余额已退',U("RentCar/orders",array('token'=>session('token'),'type'=>$type)));
                }else{
                    $t_reservebook->where(array('id'=>$check['id'],'token'=>session('token'),'type'=>$type))->save(array('remate'=>'3'));
                    $this->success('撤单成功，现金支付请自行退费',U("RentCar/orders",array('token'=>session('token'),'type'=>$type)));
                }
            }
            exit;
        }else{
            $this->error('非法操作！',U("RentCar/orders",array('token'=>session('token'),'type'=>$type)));
            exit;
        }
    }

    public function orders_list(){
        $id             = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $type           = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $token          = session('token');
        $where          = array('id'=>$id,'token'=>$token,'type'=>$type);
        $t_reservebook  = M('reservebook');
        $userinfo       = $t_reservebook->where($where)->find();

        $where1         = array('sid'=>$userinfo['rid'],'token'=>$token);
        $rentitem       = M('rentcar_item')->where($where1)->find();
        $where2         = array('tid'=>$rentitem['tid'],'token'=>$token);
        $renttype       = M('rentcar_type')->where($where2)->find();

        $tmp1=explode('|', $userinfo['address']);
        $shortaddr=$this->short_address();
        $tmp2=explode('|', $userinfo['choose']);

        $this->assign('userinfo',$userinfo);
        $this->assign('curaddress',$tmp1[1]);
        $this->assign('beginar',$shortaddr[$tmp2[0]]);
        $this->assign('endar',$shortaddr[$tmp2[1]]);
        $this->assign('group',$renttype['group']);
        if(IS_POST){

            $id     = filter_var($this->_post('id'),FILTER_VALIDATE_INT);
            $type   = filter_var($this->_post('type'),FILTER_VALIDATE_INT);
            $token  = session('token');
            $where  =  array('id'=>$id,'token'=>$token);
            if((int)$this->_post('remate') == 1){
                $_POST['paid'] = 1;
            }
            $ok     = $t_reservebook->where($where)->save($_POST);
            if($ok){
              $this->assign('ok',1);
            }else{
                $this->assign('ok',2);
            }
            echo "<script type='text/javascript'>parent.location.reload();</script>";
        }
       $this->display();
    }

    public function custservice(){
        $where = array('token'=>$this->token,'wecha_id'=>array('neq',''));
        $custflag = $this->_post('custflag','intval');
        $tel = $this->_post('tel','trim');
        if(!empty($tel)){
            $where['tel'] = array('like',$tel.'%');
        }
        $where['username']=array('neq','');
        $userlist = M('Userinfo')->where($where)->select();
        $alluser = array();
        foreach($userlist as $key=>$value){
            $cust=D("cust_service")->where(array('token'=>$this->token,'uid'=>$value['id']))->find();
            if(!$custflag){
                if($cust){
                    $alluser[$key]=$value;
                }
            }else{
                if(!$cust){
                    $alluser[$key]=$value;
                }
            }
        }
        $Page = new Page(count($alluser),20);
        $this->assign('alluser',array_slice($alluser,$Page->firstRow,$Page->listRows));
        $this->assign('page',$Page->show());
        $this->assign('flag',$custflag);
        $this->display();
    }

    public function custservice_add(){
        $id      = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $token   = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $t_cust  = D('cust_service');
        $where   = array('id'=>$id,'token'=>$token);
        $uinfo   = M('Userinfo')->where($where)->find();
        $where1   = array('uid'=>$id,'token'=>$token);
        $check   = $t_cust->where($where1)->find();
        if(empty($check)){
            $t_cust->data(array('uid'=>$uinfo['id'],'token'=>$uinfo['token'],'wecha_id'=>$uinfo['wecha_id']))->add();
            $this->success('授权成功',U("RentCar/custservice",array('token'=>$token)));
            exit;
        }else{
            $this->error('非法操作！',U("RentCar/custservice",array('token'=>$token)));
            exit;
        }
    }

    public function custservice_del(){
        $id      = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $token   = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $t_cust  = D('cust_service');
        $where   = array('id'=>$id,'token'=>$token);
        $check   = $t_cust->where($where)->find();
        if(!empty($check)){
            $t_cust->where(array('uid'=>$check['uid'],'token'=>session('token')))->delete();
            $this->success('移除授权成功',U("RentCar/custservice",array('token'=>$token)));
            exit;
        }else{
            $this->error('非法操作！',U("RentCar/custservice",array('token'=>$token)));
            exit;
        }
    }

    public function rentcar_service(){
        return array('10'=>'短途',
            '20'=>'长途',
            '30'=>'代驾',
            '40'=>'跑腿',
            '50'=>'洗车');
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