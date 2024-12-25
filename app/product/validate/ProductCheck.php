<?php
namespace app\product\validate;
use think\facade\Db;
use think\Validate;

class ProductCheck extends Validate
{
	// 自定义验证规则
	protected function checkOne($value,$rule,$data=[])
	{
		$count = Db::name('Product')->where(['name'=>$data['name'],'delete_time'=>0])->count();
		return $count == 0 ? true : false;
	}
	
    protected $rule = [
        'name' => 'require|checkOne',
        'id' => 'require',
    ];

    protected $message = [
        'name.require' => '产品名称不能为空',
        'name.checkOne' => '同样的产品名称已经存在',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['name'],
        'edit' => ['id'],
    ];
}
