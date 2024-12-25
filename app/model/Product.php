<?php
namespace app\model;

use think\facade\Db;
use think\Model;

class Product extends Model
{
    const ZERO = 0;
    const ONE = 1;

    public static $Status = [
        self::ZERO => '关闭',
        self::ONE => '开启',
    ];
    //详情
    public function detail($id)
    {
        $detail = Db::name('Product')->where(['id' => $id])->find();
        if (!empty($detail)) {
            $detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
            $detail['director_name'] = Db::name('Admin')->where(['id' => $detail['director_uid']])->value('name');
            $check_admin_names = Db::name('Admin')->where('id', 'in', $detail['check_admin_ids'])->column('name');
            $detail['check_admin_names'] = implode(',', $check_admin_names);
            $detail['status_name'] = self::$Status[(int) $detail['status']];
            $detail['times'] = time_trans($detail['create_time']);
            $detail['logs'] = Db::name('Log')->where(['module' => 'product', 'product_id' => $detail['id']])->count();
            $detail['comments'] = Db::name('Comment')->where(['module' => 1, 'delete_time' => 0, 'topic_id' => $detail['id']])->count();
            $detail['projects'] = Db::name('Project')->where(['delete_time' => 0, 'product_id' => $detail['id']])->count();

            $project_ids = Db::name('Project')->where(['delete_time' => 0, 'product_id' =>$detail['id']])->column('id');
            $task_map = [];
            $task_map[] = ['project_id', 'in', $project_ids];
            $task_map[] = ['delete_time', '=', 0];

            //任务
            $task_cate = Db::name('TaskCate')->where(['status' => 1])->select()->toArray();
            foreach ($task_cate as $k => $v) {
                $task_map[] = ['type', '=', $v['id']];
                $task_cate[$k]['count'] = Db::name('Task')->where($task_map)->count();
            }
            $detail['task_cate'] = $task_cate;
        }
        return $detail;
    }
}
