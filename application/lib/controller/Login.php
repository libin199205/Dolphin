<?php
namespace app\lib\controller;
use think\Controller;
use think\Db;
use app\lib\model\User as UserModel;
class Login extends Controller
{
	/**
     * 用户登录
     * @return mixed|void
     */
	public function index()
	{
		if ($this->request->isAjax())
		{
			// 获取post数据
			$data = $this->request->post();
			
			$rememberme = isset($data['remember-me']) ? true : false;

			// 验证数据
			$result = $this->validate($data, 'User.signin');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			// 验证 验证码
			$verify = input('post.captcha');
			if(!captcha_check($verify))
			{
				return $this->error('验证码错误');
			};
			
			// 登录
			$UserModel = new UserModel;
			$uid = $UserModel->login($data['username'], $data['password'], $rememberme);
			if ($uid)
			{
				$this->success('登录成功，页面即将跳转~', url('index/index'));
			}
			else
			{
				$this->error($UserModel->getError());
			}
			exit;
		}
		return is_signin() ? $this->redirect('index/index') : $this->fetch('public/login');
	}

	/**
	 * 退出登录
	 * @return [type] [description]
	 */
	public function logout()
	{
		session(null);
		cookie('uid', null);
		cookie('signin_token', null);
		return $this->redirect('login/index');
	}

}