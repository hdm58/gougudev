<?php
namespace app\task\validate;

use think\Validate;

class TaskCheck extends Validate
{
    protected $rule = [
        'title' => 'require',
        'plan_hours' => 'require|regex:/^[0-9]+(.[0-9]{1,1})?$/',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '任务主题不能为空',
        'plan_hours.require' => '预估工时不能为空',
        'plan_hours.regex' => '预估工时只能是整数或者1位小数的数字',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title'],
        'hours' => ['plan_hours'],
        'edit' => ['id'],
    ];
}
