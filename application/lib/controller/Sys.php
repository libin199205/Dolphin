<?php
namespace app\lib\controller;
use think\Db;
class Sys extends Base
{

	/**
	 * 角色管理
	 * @return [type] [description]
	 */
	public function role()
	{
		$where = array();
		if(!empty(input('param.role')) || input('param.role')!=0)
		{
			$where['id'] = trim(input('param.role'));
		}
		if(!empty(input('param.access')) || input('param.access')!=0)
		{
			$where['access'] = trim(input('param.access'));
		}
		if(!empty(input('param.status')) || input('param.status')!=0)
		{
			$where['status'] = trim(input('param.status'));
		}

		$rolist = Db::name('role')->field(true)->where($where)->paginate(20,false,array('query'=>input('get.')));
		$this->assign('rolist',$rolist);

		$role = Db::name('role')->field(true)->select();
		$this->assign('role',$role);
		$this->assign('page_title','角色管理');
		return $this->laview();
	}


	/**
	 * 添加角色
	 * @return [type] [description]
	 */
	public function add()
	{
		if($this->request->isAjax())
		{
			$data = $this->request->post();
			// 验证数据
			$result = $this->validate($data, 'Sys.add');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			$data['access'] = !empty($data['access'])?1:2;
			$data['status'] = !empty($data['status'])?1:2;
			$data['create_time'] = $this->request->time();

			$re = Db::name('role')->insert($data);
			if($re)
			{
				return $this->success('操作成功');
			}
			else
			{
				return $this->error('操作失败，请刷新后重试');
			}
			exit;
		}
	}

	/**
	 * 角色名称修改
	 * @return [type] [description]
	 */
	public function edit()
	{
		if($this->request->isAjax())
		{
			// 获取post数据
			$data = $this->request->post();
			// 验证数据
			$result = $this->validate($data, 'Sys.edit');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			$name = Db::name('role')->where('id','neq',$data['id'])->where('name',$data['name'])->count();
			if($name)
			{
				return $this->error('该角色名称已存在');
			}

			$id = $data['id'];
			unset($data['id']);
			$data['access'] = !empty($data['access'])?1:2;
			$data['status'] = !empty($data['status'])?1:2;
			$data['update_time'] = $this->request->time();

			$re = Db::name('role')->where(['id'=>$id])->update($data);
			if($re !== false)
			{
				return $this->success('操作成功');
			}
			else
			{
				return $this->error('操作失败，请刷新后重试');
			}
			exit;
		}
	}

	/**
	 * 角色删除操作
	 * @return [type] [description]
	 */
	public function del()
	{
		if($this->request->isAjax())
		{
			$id = input('param.id');
			$data = Db::name('role')->find($id);
			if (!$data) $this->error('非法参数');
			$satus = Db::name('role')->where(array('id'=>$id))->delete();
			if ($satus)
			{
				return $this->success('操作成功');
			}
			else
			{ 
				return $this->error('操作失败，请刷新后重试');
			}
			exit;
			exit;
		}
	}


	/**
	 * 设置角色状态和是否可登陆后台
	 * @return [type] [description]
	 */
	public function accstatus()
	{
		if($this->request->isAjax())
		{
			$id = input('param.id');
			$data = Db::name('role')->find($id);
			if (!$data) $this->error('非法参数');

			if(input('param.value') == '1')
			{
				$data[input('param.field')] = '2';
			}
			else
			{
				$data[input('param.field')] = '1';
			}

			$re = Db::name('role')->where(['id'=>input('param.id')])->update($data);
			if($re)
			{
				return $this->success('操作成功');
			}
			else
			{
				return $this->error('操作失败，请刷新后重试');
			}
		}
	}


	/**
	 * 角色授权管理
	 * @return [type] [description]
	 */
	public function power()
	{
		if($this->request->isAjax())
		{
			if(input('param.id')=='')
			{
				return $this->error('请选择角色进行授权操作');
			}
			
			$data['power'] = !empty(input('param.power/a'))?implode(',', input('param.power/a')):'';

			$re  = Db::name('role')->where(['id'=>input('param.id')])->update($data);
			if($re!==false)
			{
				return $this->success('操作成功');
			}
			else
			{
				return $this->error('操作失败，请刷新后重试');
			}
			exit;
		}
		//权限
        $power=$this->getMenu('all');
		// 获取所有角色
		$role = Db::name('role')->field(true)->select();
		$this->assign(array(
			'role'	=>	$role,
			'power' =>	$power,
		));
		$this->assign('page_title','角色授权管理');
		return $this->laview();
	}
}
