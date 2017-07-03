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
class Sys extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|角色' => 'require|unique:role',
    ];

    //定义验证提示
    protected $message = [
        'name.require'  => '请输入角色名称',
        'name.unique'   => '该角色名称已存在',
    ];

    //定义验证场景
    protected $scene = [
        // 添加
        'add'   =>  ['name'],
        // 修改
        'edit'  =>  ['name'=>'require'],
    ];
}
