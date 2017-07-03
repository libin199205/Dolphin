<?php
// +----------------------------------------------------------------------
// | LiebPHP [ PHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 
// +----------------------------------------------------------------------
// | 官方网站: 
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------


namespace app\lib\model;

use think\Model;
use think\Db;

/**
 * 后台用户模型
 * @package app\admin\model
 */
class User extends Model
{
	// 设置当前模型对应的完整数据表名称
	protected $table = '__USER__';


	/**
	 * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @param bool $rememberme 记住登录
     * @author 蔡伟明 <314013107@qq.com>
     * @return bool|mixed
     */
	public function login($username = '', $password = '', $rememberme = false)
	{
		$username = trim($username);
		$password = trim($password);
		
		// 匹配登录方式
		if (preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $username))
		{
			// 邮箱登录
			$map['email'] = $username;
		}
		elseif (preg_match("/^1\d{10}$/", $username))
		{
			// 手机号登录
			$map['mobile'] = $username;
		}
		else
		{
			// 用户名登录
			$map['username'] = $username;
		}

		$map['status'] = 1;

		// 查找用户
		$user = $this::get($map);
		if(!$user)
		{
			$this->error = '用户不存在或被禁用！';
		}
		else
		{
			// 检查是否分配用户组
			if ($user['role_id'] == 0)
			{
				$this->error = '禁止访问，原因：未分配角色！';
				return false;
			}

			// 检查是否限制后台登陆
			$role = Db::name('role')->field('access')->where(['id'=>['in',$user['role_id']]])->select();
			$access = array_column($role, 'access');
			if(!in_array('1', $access))
			{
				$this->error = '禁止访问，原因：用户所在角色禁止访问后台！';
				return false;
			}

			// 检查密码
			if($user['password'] != md5(md5($password).PAKEY) )
			{
				$this->error = '禁止访问，原因：密码不正确！';
				return false;
			}
			else
			{
				$uid = $user->id;

				// 更新登录信息
				$user->last_login_time = request()->time();
				$user->last_login_ip   = get_client_ip();
				$user->login_num	   = $user->login_num+1;
				if ($user->save())
				{
					// 自动登录
					return $this->autoLogin($this::get($uid), $rememberme);
				}
				else
				{
					// 更新登录信息失败
					$this->error = '登录信息更新失败，请重新登录！';
					return false;
				}
			}
		}
	}


	/**
	 * 自动登录
	 * @param object $user 用户对象
	 * @param bool $rememberme 是否记住登录，默认7天
	 * @return bool|int
	 */
	public function autoLogin($user, $rememberme = false)
	{

		// 保存用户节点权限
		$menu_auth=isset($user->role_id)?$user->role_id:'';

		$postion = Db::name('role')->where(['access'=>'1','status'=>'1'])->where(array('id'=>array('in',$menu_auth)))->select();
		$arr=array();
		foreach ($postion as $key=>$row){
			//合并权限
			$arr=array_filter(array_unique(array_merge(explode(',', $row['power']),$arr)));
		}


		// 记录登录SESSION和COOKIES
		$auth = array(
			'id'				=>	$user->id,
			'username'			=>	$user->username,
			'nickname'			=>	$user->nickname,
			'role_id'			=>	$user->role_id,
			'last_login_time' 	=>	$user->last_login_time,
			'last_login_ip'		=>	get_client_ip(),
			'power'				=>	$arr,
		);
		session('user_auth', $auth);
		session('user_auth_sign', $this->dataAuthSign($auth));

		// 记住登录
		if ($rememberme)
		{
			$signin_token = $user->username.$user->id.$user->last_login_time;
			cookie('uid', $user->id, 24 * 3600 * 7);
			cookie('signin_token', $this->dataAuthSign($signin_token), 24 * 3600 * 7);
		}
		return $user->id;
	}


	/**
     * 数据签名认证
     * @param array $data 被认证的数据
     * @return string 签名
     */
	public function dataAuthSign($data = [])
	{
		// 数据类型检测
		if(!is_array($data))
		{
			$data = (array)$data;
		}

		// 排序
		ksort($data);
		// url编码并生成query字符串
		$code = http_build_query($data);
		// 生成签名
		$sign = sha1($code);
		return $sign;
	}

	/**
     * 判断是否登录
     * @author 蔡伟明 <314013107@qq.com>
     * @return int 0或用户id
     */
	public function isLogin()
	{
		$user = session('user_auth');
		if (empty($user))
		{
			// 判断是否记住登录
			if (cookie('?uid') && cookie('?signin_token'))
			{
				$user = $this::get(cookie('uid'));
				if ($user)
				{
					$signin_token = $this->dataAuthSign($user->username.$user->id.$user->last_login_time);
					if (cookie('signin_token') == $signin_token)
					{
						// 自动登录
						$this->autoLogin($user, true);
						return $user->id;
					}
				}
			};
			return 0;
		}
		else
		{
			return session('user_auth_sign') == $this->dataAuthSign($user) ? $user['id'] : 0;
		}
	}

}