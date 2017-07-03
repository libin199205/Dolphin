<?php
namespace app\lib\controller;
use think\Controller;
use think\request;
use think\Db;
class Base extends Controller
{
	/**
     * 初始化
     */
	protected function _initialize()
	{
		$request = request::instance();
		// 判断是否登录，并定义用户ID常量
		defined('UID') or define('UID', $this->isLogin());
		
		// 菜单
		$powerId = implode(',',session('user_auth.power'));
		$menu=$this->getMenu($powerId);

		// 当前方法URL地址
		$tSign=strtolower($request->module().'/'.$request->controller().'/'.$request->action());

		// 当前方法URL地址id
		$tSignId=Db::name('menu')->where(array('url_value'=>$tSign))->value('id');
		
		// 面包屑导航
		$crmNav=$this->crumbTrail($tSignId);

		//免权限控制(开发时注释)
		$no_power=array('lib/index/index');
		if (!in_array($tSign, $no_power)){
			//权限控制
			$this->checkPower($tSign);
		}


		$this->assign(array(
			'troller'	=>  $request->module().'/'.$request->controller(),
			'menu'		=>	$menu,		//菜单
			'tSign'		=>	$tSign,		//当前地址
			'crmNav'	=>	$crmNav,	//面包屑导航
		));
	}


	/**
	 * 检查是否登录，没有登录则跳转到登录页面
	 * @author 蔡伟明 <314013107@qq.com>
	 * @return int
	 */
	final protected function isLogin()
	{
		// 判断是否登录
		if ($uid = is_signin())
		{
			// 已登录
			return $uid;
		}
		else
		{
			// 未登录
			$this->redirect('login/index');
		}
	}



	/**
	 * 加载视图
	 * @param  string $filename 中部内容
	 * @return [type]           [description]
	 */
	public function laview($filename="")
	{
 		echo $this->fetch('public/header');
 		echo $this->fetch($filename);
 		return $this->fetch('public/footer');
 	}



 	/**
	 * 递归获菜单
	 * @param string $powerId 权限id
	 * @param string $pid 父级分类id
	 * @param bool $all  查询模式，false查询显示的，true查询全部
	 * @param array $datas 临时存储
	 * @return \Think\mixed
	 */
	protected function getMenu($powerId,$pid='0',$all=false,$datas=array())
	{
		if(!$all)
		{
			if($powerId=='all')
			{
				$where=array('pid'=>$pid);
			}
			else
			{
				$where=array('pid'=>$pid,'show'=>1,'id'=>array('in',$powerId));
			}
		}
		else
		{
			if($powerId=='all')
			{
				$where=array('pid'=>$pid);
			}
			else
			{
				$where=array('pid'=>$pid,'id'=>array('in',$powerId));
			}
		}
		$datas=Db::name('menu')->where($where)->order('sort asc')->select();
		if (count($datas)>0)
		{
			foreach ($datas as $key=>$row)
			{
				$datas[$key]['sub']=$this->getMenu($powerId,$row['id'],$all);
			}
		}
		return $datas;
	}

	/**
	 * 根据分类id向上回溯构造面包屑
	 * @param  $id 要进行向上回溯用的分类id
	 * @param  $categories 由所有分类组成的数组
	 * @param  $data 用于保存结果的数组，传入一个空数组就好
	 */
	protected function crumbTrail($id,$categories=array(), &$data=array())
	{
		$category = Db::name('menu')->where(array('id'=>$id))->find();
		if( $category['pid'] != 0 ) 
		{  // 这里的 0 是根节点id（root节点id）
			$this->crumbTrail($category['pid'],$categories, $data);
		}
		$data[$category['id']] = $category;
		return $data;
	}


	/**
	 * 权限控制
	 * @param $thisLink 方法地址
	 */
	protected function checkPower($thisLink)
	{
		$menu_id= Db::name('menu')->where(array('url_value'=>$thisLink))->value('id');
		if (!in_array($menu_id, session('user_auth.power'))){
			//$this->redirect('/');
			$this->error('你没有权限');
		}
	}
}