<?php
namespace app\model;

use think\facade\Db;
use think\Model;

class Project extends Model
{
    const ZERO = 0;
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;
    const FORE = 4;

    public static $Status = [
        self::ZERO => '未设置',
        self::ONE => '未开始',
        self::TWO => '进行中',
        self::THREE => '已完成',
        self::FORE => '已关闭',
    ];
    //详情
    public function detail($id)
    {
        $detail = Db::name('Project')->where(['id' => $id])->find();
        if (!empty($detail)) {
            $detail['product_name'] = Db::name('Product')->where(['id' => $detail['product_id']])->value('name');
            $detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
            $detail['director_name'] = Db::name('Admin')->where(['id' => $detail['director_uid']])->value('name');
            $team_admin_ids = Db::name('ProjectUser')->where(['delete_time' => 0,'project_id'=>$id])->column('uid');
            $team_admin_names = Db::name('Admin')->where('id', 'in', $team_admin_ids)->column('name');
            $detail['team_admin_names'] = implode(',', $team_admin_names);
            $detail['status_name'] = self::$Status[(int) $detail['status']];
            $detail['times'] = time_trans($detail['create_time']);
            $detail['users'] = Db::name('ProjectUser')->where(['delete_time' => 0,'project_id'=>$id])->count();
            $detail['comments'] = Db::name('Comment')->where([['module','=','project'],['topic_id','=',$detail['id']],['delete_time','=',0]])->count();
			$detail['logs'] = Db::name('Log')->where(['module' => 'project', 'project_id' => $detail['id']])->count();
        }
        return $detail;
    }
}
