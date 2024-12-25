<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\model;
use think\Model;
use think\facade\Db;

class Knowledge extends Model
{
    //详情
    public function detail($id)
    {
		$detail = Db::name('Knowledge')->where(['id' => $id])->find();
        if (!empty($detail)) {
			$detail['user'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
			$detail['cate_name'] = Db::name('KnowledgeCate')->where(['id' => $detail['cate_id']])->value('title');
			$detail['count'] = Db::name('KnowledgeDoc')->where(['knowledge_id'=>$id,'delete_time'=>0])->count();
			$detail['views'] = Db::name('KnowledgeDoc')->where(['knowledge_id'=>$id,'delete_time'=>0])->sum('read');
        }
        return $detail;
    }
}
