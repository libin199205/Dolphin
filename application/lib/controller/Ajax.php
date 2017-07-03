<?php
namespace app\lib\controller;
use think\Db;

class Ajax extends Base
{

	/**
	 * 批量启动 禁止 删除
	 * @return [type] [description]
	 */
	public function status()
	{
		if($this->request->isAjax())
		{
			$data = $this->request->post();

			switch ($data['val'])
			{
				case '1':
					$re = Db::name('role')->where(['id'=>['in',$data['ids']]])->update(['status'=>1]);
					break;
				case '2':
					$re = Db::name('role')->where(['id'=>['in',$data['ids']]])->update(['status'=>2]);
					break;
				case '3':
					$re = Db::name('role')->where(['id'=>['in',$data['ids']]])->delete();
					break;
				default:
					break;
			}
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
	}
}
