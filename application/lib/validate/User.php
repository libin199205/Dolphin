<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\lib\validate;

use think\Validate;

/**
 * 用户验证器
 * @package app\admin\validate
 * @author 蔡伟明 <314013107@qq.com>
 */
class User extends Validate
{
    //定义验证规则
    protected $rule = [
        'username|用户名' => 'require|alphaNum|unique:user',
        'nickname|昵称'   => 'require',
        'email|邮箱'      => 'email|unique:user',
        'mobile|手机号'   => 'regex:^1\d{10}|unique:user',
        'role|角色'       => 'require',
        'password|密码'   => 'require|length:3,20',
    ];

    //定义验证提示
    protected $message = [
        'username.require' => '请输入用户名',
        'nickname.require' => '请输入昵称',
        'email.require'    => '邮箱不能为空',
        'email.email'      => '邮箱格式不正确',
        'email.unique'     => '该邮箱已存在',
        'password.require' => '密码不能为空',
        'password.length'  => '密码长度3-20位',
        'mobile.require'   => '请输入手机号码',
        'mobile.regex'     => '手机号不正确',
    ];

    //定义验证场景
    protected $scene = [
        //添加
        'add'   =>  [
                        'username'  =>  'require|unique:user', 
                        'nickname'  =>  'require',
                        'email'     =>  'require|email|unique:user', 
                        'mobile'    =>  'require|regex:^1\d{10}|unique:user',
                        'role'      =>  'require',
                        'password'  =>  'require|length:3,20',
                    ],
        //更新
        'edit'  =>  [
                        'nickname'  =>  'require',
                        'email'     =>  'require|email',
                        'mobile'    =>  'require|regex:^1\d{10}',
                        'role'      =>  'require',
                        'password'  =>  'length:3,20',
                    ],
        //登录
        'signin'=>  ['username' => 'require', 'password' => 'require'],
    ];
}
