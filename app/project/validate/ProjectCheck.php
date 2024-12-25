<?php
namespace app\project\validate;
use think\Validate;
use think\facade\Db;
class ProjectCheck extends Validate
{
	// 自定义验证规则
	protected function checkOne($value,$rule,$data=[])
	{
		$count = Db::name('Project')->where(['name'=>$data['name'],'delete_time'=>0])->count();
		return $count == 0 ? true : false;
	}
    protected $rule = [
        'name'       => 'require|checkOne',
        'code'       => 'alphaNum|length:5,10',
        'id'         => 'require'
    ];

    protected $message = [
        'name.require'           => '项目名称不能为空',
        'name.checkOne'            => '同样的项目名称已经存在',
        'id.require'             => '缺少更新条件',
    ];

    protected $scene = [
        'add'       => ['name','code'],
        'edit'      => ['id']
    ];
}