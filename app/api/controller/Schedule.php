<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\model\Schedule as ScheduleList;
use think\facade\Db;
use think\facade\View;

class Schedule extends BaseController
{
    
    //获取工作记录列表
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['uid'])) {
                $where[] = ['a.admin_id', '=', $param['uid']];
            }
			
			if (!empty($param['project_id'])) {
				$task_ids = Db::name('Task')->where(['delete_time' => 0, 'project_id' => $param['project_id']])->column('id');
				$where[] = ['a.tid', 'in', $task_ids];
			}
            $where[] = ['a.delete_time', '=', 0];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $list = ScheduleList::where($where)
                ->field('a.*,u.name,d.title as department,t.title as task,p.name as project,w.title as work_cate')
                ->alias('a')
                ->join('Admin u', 'a.admin_id = u.id', 'LEFT')
                ->join('Department d', 'u.did = d.id', 'LEFT')
                ->join('Task t', 'a.tid = t.id', 'LEFT')
                ->join('WorkCate w', 'w.id = t.cate', 'LEFT')
                ->join('Project p', 't.project_id = p.id', 'LEFT')
                ->order('a.end_time desc')
                ->paginate($rows, false, ['query' => $param])
                ->each(function ($item, $key) {
                    $item->start_time_a = empty($item->start_time) ? '' : date('Y-m-d', $item->start_time);
                    $item->start_time_b = empty($item->start_time) ? '' : date('H:i', $item->start_time);
                    $item->end_time_a = empty($item->end_time) ? '' : date('Y-m-d', $item->end_time);
                    $item->end_time_b = empty($item->end_time) ? '' : date('H:i', $item->end_time);

                    $item->start_time = empty($item->start_time) ? '' : date('Y-m-d H:i', $item->start_time);
                    $item->end_time = empty($item->end_time) ? '' : date('H:i', $item->end_time);
                });
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //获取任务工作记录列表
    public function get_list()
    {
        $param = get_params();
        $where = array();
        $where['a.tid'] = $param['tid'];
        $where['a.delete_time'] = 0;
        $list = Db::name('Schedule')
            ->field('a.*,u.name')
            ->alias('a')
            ->join('Admin u', 'u.id = a.admin_id')
            ->order('a.create_time desc')
            ->where($where)
            ->select()->toArray();
        foreach ($list as $k => $v) {
            $list[$k]['start_time'] = empty($v['start_time']) ? '' : date('Y-m-d H:i', $v['start_time']);
            $list[$k]['end_time'] = empty($v['end_time']) ? '' : date('H:i', $v['end_time']);
        }
        return to_assign(0, '', $list);
    }

    //查看
    public function view($id)
    {
        $id = get_params('id');
        $schedule = ScheduleList::where(['id' => $id])->find();
        if (!empty($schedule)) {
			$schedule['start_time_b'] = date('H:i', $schedule['start_time']);
            $schedule['end_time_b'] = date('H:i', $schedule['end_time']);
            $schedule['start_time_a'] = date('Y-m-d', $schedule['start_time']);
            $schedule['end_time_a'] = date('Y-m-d', $schedule['end_time']);
			
            $schedule['start_time_1'] = date('H:i', $schedule['start_time']);
            $schedule['end_time_1'] = date('H:i', $schedule['end_time']);
            $schedule['start_time'] = date('Y-m-d', $schedule['start_time']);
            $schedule['end_time'] = date('Y-m-d', $schedule['end_time']);
            $admin = Db::name('Admin')->where(['id' => $schedule['admin_id']])->find();
            $schedule['name'] = $admin['name'];
            $schedule['department'] = Db::name('Department')->where(['id' => $admin['did']])->value('title');
			$schedule['task'] = '-';
			$schedule['work_cate'] = '-';
			$schedule['project'] = '-';
			if($schedule['tid']>0){
				$task = Db::name('Task')->where(['id' => $schedule['tid']])->find();
				$schedule['task'] =$task['title'];
				$schedule['work_cate'] = Db::name('WorkCate')->where(['id' => $task['cate']])->value('title');
				if ($task['project_id'] > 0) {
					$schedule['project'] = Db::name('Project')->where(['id' => $task['project_id'],'delete_time' => 0])->value('name');
				}
			}
        }
        if (request()->isAjax()) {
            return to_assign(0, "", $schedule);
        } else {
            return $schedule;
        }
    }
}
