<?php
class ForumAction extends UserAction{
	
	
	public function _initialize() {
		parent::_initialize();
			$this->canUseFunction('forum');
		}
		
	//帖子管理
	public function index(){
		$topics = M('Forum_topics');
		$token = $this->token;
		$where = array('token'=>$token);
		$count      = $topics->where( $where )->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
		$list = $topics->where( $where )->order('status ASC,createtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$typeconf = M('Forum_config');
		$fconf=$typeconf->where( $where )->select();
		foreach ($list as $k=>$v){
			$list[$k]['content'] = htmlspecialchars_decode($v['content']);
			foreach ($fconf as $fc) {
				if($fc['id']==$list[$k]['fid']){
					$list[$k]['forumname'] = $fc['forumname'];
				}
			}
		}
		
		$this->assign('tabid',1);
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
		
	}
	
	//评论管理
	public function comment(){
	
		$comment = M('Forum_comment');

		$token = $this->token;
		$where = array('token'=>$token);
		$count      = $comment->where( $where )->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
		$list = $comment->where( $where )->order('status ASC,createtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		foreach ($list as $k=>$v){
		
			$list[$k]['content'] = htmlspecialchars_decode($v['content']);
		}
		
		$this->assign('tabid',2);
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	
	}
	//消息管理
	public function message(){
	
		$message = M('Forum_message');

		$token = $this->token;
		$where = array('token'=>$token);
		$count      = $message->where( $where )->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
		$list = $message->where( $where )->order('createtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();		
		
		$this->assign('tabid',3);
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	
	}
	//删除消息
	public function delMessage(){
	
		$id = $this->_request('id','intval');

		$token = $this->token;
		if(empty($id)){
			$this->error('请勾选要删除的内容');
		}
		
		if(is_array($id)){
		
			$id = implode(',',$id);
			$where = "token = '$token' AND id in($id)";
			
		}else{	
			$where = "token = '$token' AND id = $id";
		}
		
		if(M('Forum_message')->where( $where )->delete()){
		
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
		
	}
	//删除评论
	public function delComment(){
	
		$id = $this->_request('id','intval');

		$token = $this->token;
		if(empty($id)){
			$this->error('请勾选要删除的内容');
		}
		
		if(is_array($id)){
		
			$id = implode(',',$id);
			$where = "token = '$token' AND id in($id)";
			
		}else{	
			$where = "token = '$token' AND id = $id";
		}
		
		if(M('Forum_comment')->where( $where )->delete()){
		
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
		
	}
	
	//审核帖子
	public function checkTopics(){
	
		$id = $this->_request('id','intval');
		
		$token = $this->token;
				
		if(empty($id)){
			$this->error('请勾选要通过审核的内容');
		}
		
		if(is_array($id)){
		
			$id = implode(',',$id);
			$where = "token = '$token' AND id in($id)";
			
		}else{	
			$where = "token = '$token' AND id = $id";
		}
		
		if(M('Forum_topics')->where( $where )->setField('status',1)){
		
			$this->success('审核成功');
		}else{
			$this->error('审核失败');
		}
	
	}
	
	//删除帖子
	public function delTopics(){
	
		$id = $this->_get('id','intval');

		$token = $this->token;
		

	//查帖子信息
		$topics = M('Forum_topics');
		$data = $topics->field('photos')->where("token = '$token' AND id = $id")->find();
	//删除硬盘上图片	
		if($data['photos'] != NULL){
			$photos = explode(',',$data['photos']);
			$photoNum = count($photos);
			for($i=0;$i<$photoNum;$i++){
				$site = C('site_url').'/';
				$photos[$i] = str_replace("$site",'',$photos[$i]);
				$res = @unlink($photos[$i]);
			}
		}
	
	
	//删除对应评论
		$comment = M('Forum_comment');
		$res2 = $comment->where("tid = $id AND token = '$token'")->delete();
	//删除对应的消息
		$message = M('Forum_message');
		$res3 = $message->where("token = '$token' AND tid = $id")->delete();
	//删除帖子记录
		$res4 = $topics->where("token = '$token' AND id = $id")->delete();
		

		$this->success('删除成功');

	
	}
	
	
	//论坛配置页面
	public function config(){
		$token = $this->token;
		$fid=$_GET['id'];
		if($fid){
			$conf = M('Forum_config')->where(array('token' => $token,'id'=>$fid))->find();
			$logo = M('Home')->field('logo')->where("token = '$token'")->find();
			if($conf['logo'] == NULL){
				if($logo['logo'] == NULL){
					$conf['logo'] = '/tpl/Wap/default/common/images/forum/face.png';
				}else{
					$conf['logo'] = $logo['logo'];
				}
			}
			$this->assign('tabid',4);
			$this->assign('wxname',$wxname['wxname']);
			$this->assign('conf',$conf);
			$this->assign('token',$token);
		}else{
			$logo = M('Home')->field('logo')->where("token = '$token'")->find();
			if($logo['logo'] == NULL){
				$conf['logo'] = '/tpl/Wap/default/common/images/forum/face.png';
			}else{
				$conf['logo'] = $logo['logo'];
			}
			$this->assign('tabid',4);
			$this->assign('conf',$conf);
			$this->assign('token',$token);
		}
		$this->assign('id',$fid);
		$this->display();
	}
	
	//论坛配置提交处理
	public function set(){
		$token = $this->token;
		$fid=$_GET['id'];
		if(!$fid)
			$fid=0;
		$data['forumname'] = $this->_post('forumname');
		$data['logo'] = $this->_post('logo');
		$data['intro'] = $this->_post('intro');
		$data['picurl'] = $this->_post('picurl');
		$data['bgurl'] = $this->_post('bgurl');
		$data['ischeck'] = $this->_post('ischeck','intval');
		$data['comcheck'] = $this->_post('comcheck','intval');
		$data['isopen'] = $this->_post('isopen','intval');
		$data['isjoin'] = $this->_post('isjoin','intval');
		$data['token'] = $token;
		
		$conf = M('Forum_config');
		$res = $conf->field('id')->where(array('token' => $token,'id'=>$fid))->find();
		
		if(empty($res)){
			if($conf->create()){
			
					if($conf->add($data)){
						$this->success('操作成功',U('Forum/typeconfig',array('token'=>$token)));
					}else{
						$this->error('操作失败');
					}
			}else{
				$this->error('系统错误');
			}
		}else{
			if($conf->where(array('token' => $token,'id'=>$fid))->setField($data)){
				$this->success('操作成功',U('Forum/typeconfig',array('token'=>$token)));
			}else{
				$this->error('操作失败');
			}
		}
	}
	
	//审核评论
	public function checkComment(){
		
		$id = $this->_request('id','intval');
		
		$token = $this->token;
				
		if(empty($id)){
			$this->error('请勾选要通过审核的内容');
		}
		
		if(is_array($id)){
		
			$id = implode(',',$id);
			$where = "token = '$token' AND id in($id)";
			
		}else{	
			$where = "token = '$token' AND id = $id";
		}
		
		if(M('Forum_comment')->where( $where )->setField('status',1)){
		
			$this->success('审核成功');
		}else{
			$this->error('审核失败');
		}
	
	
	}
	
	public function typeconfig(){
		$homedb = M('Forum_home');
		$forumhome = $homedb->where(array('token' =>$this->token))->find();
		if(IS_POST){
			if(count($forumhome)>0){//修改
				$where = array('token'=>$this->token);	
				$homedb->where($where)->save($_POST);
				$this->success('修改成功',U('Forum/typeconfig',array('token'=>$this->token)));
			}else{//添加				
				if($homedb->create($_POST)){
					$homedb->add($_POST);
					$this->success('添加成功',U('Forum/typeconfig',array('token'=>$this->token)));
				}else{
					echo $this->error($homedb->getError());
				}
			}
		}
		else{
			$typeconf = M('Forum_config');
			$token = $this->token;
			$list = $typeconf->where(array('token' =>$token))->order('id')->select();
			$this->assign('list',$list);
			$this->assign('tabid',4);
			$this->assign('forumhome',$forumhome);
			$this->display();
		}
	}


}