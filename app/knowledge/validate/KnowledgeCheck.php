<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\knowledge\validate;

use think\Validate;

class KnowledgeCheck extends Validate
{
    protected $rule = [
        'title' => 'require',
        'desc' => 'require',
        'id' => 'require',
        'cate_id' => 'require',
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'cate_id.require' => '所属分类为必选',
        'id.require' => '缺少更新条件',
        'desc.require' => '描述不能为空',
    ];

    protected $scene = [
        'add' => ['title', 'cate_id', 'content', 'desc'],
        'edit' => ['title', 'cate_id', 'content', 'id', 'desc'],
    ];
}
