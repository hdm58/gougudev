<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use app\model\AdminLog;
use app\model\Project;
use app\model\Task;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            //未读消息统计
            $msg_map[] = ['to_uid', '=', $this->uid];
            $msg_map[] = ['read_time', '=', 0];
            $msg_map[] = ['status', '=', 1];
            $msg_count = Db::name('Message')->where($msg_map)->count();
            return to_assign(0, 'ok', ['msg_num' => $msg_count]);
        } else {
            $install = false;
            if (file_exists(CMS_ROOT . 'app/install')) {
                $install = true;
            }

            //公告相关
            $now_time = time();
            $note_list = Db::name('Note')
                ->field('a.*,c.title as cate_title')
                ->alias('a')
                ->join('note_cate c', 'a.cate_id = c.id')
                ->where([['a.delete_time', '=', 0], ['start_time', '<=', $now_time], ['end_time', '>=', $now_time]])
                ->order('a.id desc')
                ->limit(5)
                ->select()->toArray();
            foreach ($note_list as $key => $val) {
                $note_list[$key]['create_time'] = date('Y-m-d H:i', $val['create_time']);
            }

            //项目相关
            $project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
            $project_list = Db::name('Project')->where([['delete_time', '=', 0], ['id', 'in', $project_ids]])->order('status asc,id desc')->limit(10)->select()->toArray();
            foreach ($project_list as $k => &$v) {
                $v['director_name'] = Db::name('Admin')->where(['id' => $v['director_uid']])->value('name');
                $v['status_name'] = Project::$Status[(int) $v['status']];
                $v['plan_time'] = date('Y-m-d', $v['start_time']) . '至' . date('Y-m-d', $v['end_time']);

                $task_map = [];
                $task_map[] = ['project_id', '=', $v['id']];
                $task_map[] = ['delete_time', '=', 0];
                $v['tasks'] = Db::name('Task')->where($task_map)->count();
            }

            $project_count = Db::name('Project')->where([['delete_time', '=', 0], ['id', 'in', $project_ids]])->count();
            $project_count_ok = Db::name('Project')->where([['delete_time', '=', 0], ['id', 'in', $project_ids], ['status', '=', 3]])->count();

            $project = [
                'list' => $project_list,
                'count' => $project_count,
                'count_ok' => $project_count_ok,
                'count_no' => $project_count - $project_count_ok,
                'count_lv' => $project_count == 0 ? 100 : round($project_count_ok * 100 / $project_count, 2),
            ];

            //任务相关
            $task_map1 = [
                ['admin_id', '=', $this->uid],
            ];
            $task_map2 = [
                ['director_uid', '=', $this->uid],
            ];
            $task_map3 = [
                ['', 'exp', Db::raw("FIND_IN_SET('{$this->uid}',assist_admin_ids)")],
            ];
			
			$taskOr =[$task_map1,$task_map2,$task_map3];
            $task_list = Db::name('Task')
                ->where(function ($query) use ($taskOr) {
				if (!empty($taskOr))
					$query->whereOr($taskOr);
				})
                ->where([['delete_time', '=', 0]])
                ->order('flow_status asc')
                ->order('id desc')
                ->limit(10)->select()->toArray();
            foreach ($task_list as $k => &$v) {
                $v['end_time'] = date('Y-m-d', $v['end_time']);
                $v['priority_name'] = Task::$Priority[(int) $v['priority']];
                $v['flow_name'] = Task::$FlowStatus[(int) $v['flow_status']];
                $v['type_name'] = Db::name('TaskCate')->where(['id' => $v['type']])->value('title');
            }

            $task_count = Db::name('Task')
                ->where(function ($query) use ($taskOr) {
				if (!empty($taskOr))
					$query->whereOr($taskOr);
				})
                ->where([['delete_time', '=', 0]])->count();

            $task_count_ok = Db::name('Task')
                ->where(function ($query) use ($taskOr) {
				if (!empty($taskOr))
					$query->whereOr($taskOr);
				})
                ->where([['delete_time', '=', 0], ['flow_status', '>', 2]])->count();

			$delay_map1 = [];
			$delay_map2 = [];
			$delay_map1[] = ['flow_status', '<', 3];
			$delay_map1[] = ['end_time', '<', time()];
			$delay_map1[] = ['delete_time', '=', 0];
			
			$delay_map2[] = ['flow_status', '=', 3];
			$delay_map2[] = ['delete_time', '=', 0];
			
            $task_delay_1 = Db::name('Task')
                ->where(function ($query) use ($taskOr) {
				if (!empty($taskOr))
					$query->whereOr($taskOr);
				})
				->where($delay_map1)->count();
            $task_delay_2 = Db::name('Task')
                ->where(function ($query) use ($taskOr) {
				if (!empty($taskOr))
					$query->whereOr($taskOr);
				})
				->where(function ($query) {
					$query->whereRaw('over_time >= end_time + 86400');
				})
				->whereColumn('end_time','<','over_time')
				->where($delay_map2)->count();
			$task_delay = $task_delay_1+$task_delay_2;
            $task = [
                'list' => $task_list,
                'count' => $task_count,
                'count_ok' => $task_count_ok,
                'count_no' => $task_count - $task_count_ok,
                'count_lv' => $task_count == 0 ? 100 : round($task_count_ok * 100 / $task_count, 2),
                'delay' => $task_delay,
                'delay_lv' => $task_count == 0 ? 100 : round($task_delay * 100 / $task_count, 2),
            ];

            //知识相关
            $knowledge_map1 = [
                ['admin_id', '=', $this->uid],
                ['is_share', '=', 2],
            ];
            $knowledge_map2 = [
                ['is_share', '=', 1],
            ];
            $knowledge_list = Db::name('Knowledge')
                ->where(function ($query) use ($knowledge_map1, $knowledge_map2) {
                    $query->where($knowledge_map1)->whereor($knowledge_map2);
                })
                ->where('delete_time', 0)
                ->order('id desc')
                ->limit(10)->select()->toArray();
            foreach ($knowledge_list as $k => &$v) {
                $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
                $v['admin_name'] = Db::name('Admin')->where(['id' => $v['admin_id']])->value('name');
                $v['cate_name'] = Db::name('KnowledgeCate')->where(['id' => $v['cate_id']])->value('title');
                $v['views'] = Db::name('KnowledgeDoc')->where([['delete_time','=',0],['knowledge_id','=',$v['id']]])->sum('read');
				$v['sections'] = Db::name('KnowledgeDoc')->where([['delete_time','=',0],['knowledge_id','=',$v['id']]])->count();
            }

            //工时相关
            $work_count = Db::name('Schedule')->where(['delete_time' => 0,'admin_id'=>$this->uid])->count();
            $work_hours = Db::name('Schedule')->where(['delete_time' => 0,'admin_id'=>$this->uid])->sum('labor_time');
            $work = [
                'count' => $work_count,
                'hours' => $work_hours,
            ];

            $count = [
                'product' => Db::name('Product')->where(['delete_time' => 0])->count(),
                'projects' => Db::name('Project')->where(['delete_time' => 0])->count(),
                'tasks' => Db::name('Task')->where(['delete_time' => 0])->count(),
                'knowledges' => Db::name('Knowledge')->where(['delete_time' => 0])->count(),
            ];

            View::assign([
                'install' => $install,
                'note_list' => $note_list,
                'project' => $project,
                'task' => $task,
                'knowledge_list' => $knowledge_list,
                'work' => $work,
                'count' => $count,
                'TP_VERSION' => \think\facade\App::version(),
            ]);
            return View();
        }
    }

    //查看公告
    public function note_detail()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $note = Db::name('Note')->where(['id' => $id])->find();
        $note['admin_name'] = Db::name('Admin')->where(['id' => $note['admin_id']])->value('name');
		Db::name('Note')->where(['id' => $id])->inc('read')->update();
        View::assign('note', $note);
        return view();
    }

    //系统操作日志
    public function log_list()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $log = new AdminLog();
            $content = $log->get_log_list($param);
            return table_assign(0, '', $content);
        } else {
            return view('home@log/log_list');
        }
    }

    //获取chart数据
    public function get_view_data()
    {
        $param = get_params();
        $first_time = time();
        $second_time = $first_time - 86400;
        $three_time = $first_time - 86400 * 365;
        $begin_first = strtotime(date('Y-m-d', $first_time) . " 00:00:00");
        $end_first = strtotime(date('Y-m-d', $first_time) . " 23:59:59");
        $begin_second = strtotime(date('Y-m-d', $second_time) . " 00:00:00");
        $end_second = strtotime(date('Y-m-d', $second_time) . " 23:59:59");
        $begin_three = strtotime(date('Y-m-d', $three_time) . " 00:00:00");
        $data_first = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_first,$end_first")->select();
        $data_second = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_second,$end_second")->select();
        $data_three = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_three,$end_first")->select();

        //获取员工活跃数据
        $times = strtotime("-30 day");
        $where = [];
        //$where[] = ['uid','<>',1];
        $where[] = ['create_time', '>', $times];
        $content = Db::name('AdminLog')->field("id,uid,name")->where($where)->select();
        $logs = array();
        foreach ($content as $index => $value) {
            $uid = $value['uid'];
            if (empty($logs[$uid])) {
                $logs[$uid]['count'] = 1;
                $logs[$uid]['name'] = $value['name'];
            } else {
                $logs[$uid]['count'] += 1;
            }
        }
        $counts = array_column($logs, 'count');
        array_multisort($counts, SORT_DESC, $logs);
        //攫取前10
        $data_logs = array_slice($logs, 0, 10);
        return to_assign(0, '', ['data_first' => hour_document($data_first), 'data_second' => hour_document($data_second), 'data_three' => date_document($data_three), 'data_logs' => $data_logs]);
    }

    public function errorShow()
    {
        echo '错误';
    }

}
