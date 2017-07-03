<?php
namespace app\lib\controller;
use think\Db;
class User extends Base
{

	/**
	 * 用户添加
	 * @return [type] [description]
	 */
	public function add()
	{
		if($this->request->isAjax())
		{
			// 获取post数据
			$data = $this->request->post();
			// 验证数据
			$result = $this->validate($data, 'User.add');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}
			$data['password'] = md5(md5($data['password']).PAKEY);

			// 存在上传图片
			if($_FILES['avatar']['name']!='')
			{
				$actool = new Tool();
				$data['avatar'] = $actool->imgupload('avatar','avatar');
			}
			else
			{
				unset($data['avatar']);
			}

			$data['role_id'] = join(',',$data['role']);
			$data['add_time'] = request()->time();
			unset($data['role']);

			$re = Db::name('user')->insert($data);
			if($re)
			{
				return $this->success('操作成功，页面即将跳转~',url('user/lists'));
			}
			else
			{
				return $this->error('操作失败，请刷新后重试~');
			}
			exit;
		}
		$role = Db::name('role')->field(true)->select();
		$this->assign('role',$role);
		$this->assign('page_title','用户添加');
		return $this->laview();
	}

	/**
	 * 用户列表
	 * @return [type] [description]
	 */
	public function lists()
	{
		$where = array();
		if(!empty(input('param.username')))
		{
			$username = trim(input('param.username'));
			$where['a.username'] = array('like',"%{$username}%");
		}
		if(!empty(input('param.nickname')))
		{
			$nickname = trim(input('param.nickname'));
			$where['a.nickname'] = array('like',"%{$nickname}%");
		}
		if(!empty(input('param.email')))
		{
			$where['a.email'] = trim(input('param.email'));
		}
		if(!empty(input('param.mobile')))
		{
			$where['a.mobile'] = trim(input('param.mobile'));
		}
		if(!empty(input('param.role')) || input('param.role') != 0)
		{
			$role = trim(input('param.role'));
			$where['a.role_id'] = array('like',"%{$role}%");
		}
		if(!empty(input('param.status')) || input('param.status') != 0)
		{
			$where['a.status'] = trim(input('param.status'));
		}

		$lists = Db::name('user')->alias('a')->field("a.id,a.username,a.nickname,a.email,a.mobile,a.add_time,a.status,(select group_concat(b.name) from think_role b where find_in_set(b.id,a.role_id) ) posname")
			->where($where)->paginate(20,false,array('query'=>input('get.')));

		$this->assign('userlist',$lists);
		$role = Db::name('role')->field(true)->select();
		$this->assign('role',$role);
		$this->assign('page_title','用户列表');
		return $this->laview();
	}


	/**
	 * 设置状态
	 * @return [type] [description]
	 */
	public function status()
	{
		if($this->request->isAjax())
		{
			$id = input('param.id');
			$data = Db::name('user')->find($id);
			if (!$data) $this->error('非法参数');

			if(input('param.value') == '1')
			{
				$data['status'] = '2';
			}
			else
			{
				$data['status'] = '1';
			}

			$re = Db::name('user')->where(['id'=>input('param.id')])->update($data);
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
	 * 用户修改
	 * @return [type] [description]
	 */
	public function edit()
	{
		if($this->request->isAjax())
		{
			// 获取post数据
			$data = $this->request->post();
			// 验证数据
			$result = $this->validate($data, 'User.edit');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			$email = Db::name('user')->where('id','neq',$data['id'])->where('email',$data['email'])->count();
			if($email)
			{
				return $this->error('该邮箱已存在');
			}
			$mobile = Db::name('user')->where('id','neq',$data['id'])->where('mobile',$data['mobile'])->count();
			if($mobile)
			{
				return $this->error('该手机号码已存在');
			}

			// 存在写入密码
			if($data['password']!='')
			{
				$data['password'] = md5(md5($data['password']).PAKEY);
			}
			else
			{
				unset($data['password']);
			}

			// 存在上传图片
			if(!empty($_FILES['avatar']['name']))
			{
				$actool = new Tool();
				$data['avatar'] = $actool->imgupload('avatar','avatar');
			}
			else
			{
				unset($data['avatar']);
			}

			$data['role_id'] = join(',',$data['role']);
			$id = $data['id'];
			unset($data['id']);
			unset($data['role']);

			$re = Db::name('user')->where(['id'=>$id])->update($data);
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
	 * 用户删除
	 * @return [type] [description]
	 */
	public function del()
	{
		if (request()->isAjax())
		{
			$id = input('param.id');
			$data = Db::name('user')->find($id);
			if (!$data) $this->error('非法参数');
			$satus = Db::name('user')->where(array('id'=>$id))->delete();
			if ($satus)
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
}

