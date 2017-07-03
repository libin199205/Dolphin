<?php
namespace app\lib\controller;
use think\Controller;
use think\request;
use think\Db;
class Tool extends Controller
{

    /**
     * 图片上传
     * @param 
     * @return \think\response\Json|void
     */
    public function imgupload( $name = '',$dir = '' )
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($name);

        $from = DS.'images';
        if ($dir == 'avatar')
        {
            $from = DS.'images'.DS.'avatar';
        }

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>1048576,'ext'=>'jpg,bmp,jpeg,png'])->move(ROOT_PATH . 'public' . DS . 'upload' . $from);

        if($info)
        {
            // 成功上传后 获取上传信息
            return $from.DS.$info->getSaveName();
            exit;
        }
        else
        {
            return $file->getError();
            exit;
        }
    }


    /**
     * 显示用户信息到前台
     * @return [type] [description]
     */
    public function showuser()
    {
        if($this->request->isAjax())
        {
            if(empty(input('param.id')) || input('param.id')=='')
            {
                return $this->error('非法操作');
            }

            $re = Db::name('user')->field(true)->where(['id'=>input('param.id')])->find();
            return $re;
            exit;
        }
    }


    /**
     * 显示角色信息到前台
     * @return [type] [description]
     */
    public function showrole()
    {
        if($this->request->isAjax())
        {
            if(empty(input('param.id')) || input('param.id')=='')
            {
                return $this->error('非法操作');
            }

            $re = Db::name('role')->field(true)->where(['id'=>input('param.id')])->find();
            return $re;
            exit;
        }
    }

    /**
     * 实时显示权限值
     * @return [type] [description]
     */
    public function showpower()
    {
        if($this->request->isAjax())
        {
            if(empty(input('param.id')) || input('param.id')=='')
            {
                return $this->error('非法操作');
            }

            $re = Db::name('role')->field('power')->where(['id'=>input('param.id')])->find();
            return $re;
            exit;
        }
    }


}