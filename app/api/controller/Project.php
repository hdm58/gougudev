<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\model\Task as TaskList;
use think\facade\Db;
use think\facade\View;

class Project extends BaseController
{
	
	//读取项目
	function get_project()
	{
		$param = get_params();
		$uid = isset($param['uid']) ? $param['uid'] : 0;
		$map = [];
		$map[] = ['delete_time', '=', 0];
		if ($uid > 0 && is_super($uid) == 0){
			$project_ids = Db::name('ProjectUser')->where(['uid' => $uid, 'delete_time' => 0])->column('project_id');
			$map[] = ['id', 'in', $project_ids];
		}
		if (!empty($param['keywords'])) {
            $map[] = ['name', 'like', '%' . $param['keywords'] . '%'];
        }
		$list = Db::name('Project')->field('id,name as title,status')->where($map)->paginate(10, false, ['query' => $param]);
		return table_assign(0, '', $list);
	}
	
    public function view()
    {
        $param = get_params();
        View::assign('id', $param['id']);
        return view('view_' . $param['page']);
    }
    public function get_chart_data()
    {
        $param = get_params();
        $tasks = Db::name('Task')->field('id,plan_hours,end_time,flow_status,over_time')->order('end_time asc')->where([['project_id', '=', $param['project_id']], ['delete_time', '=', 0],['is_bug','=',0]])->select()->toArray();

        $task_count = count($tasks);
        $task_count_ok = Db::name('Task')->where([['project_id', '=', $param['project_id']], ['delete_time', '=', 0],['is_bug','=',0],['flow_status', '>', 2]])->count();
        $task_delay = 0;
        if ($task_count > 0) {
            foreach ($tasks as $k => $v) {
                if (($v['flow_status'] < 3) && ($v['end_time'] < time() - 86400)) {
                    $task_delay++;
                }
                if (($v['flow_status'] == 3) && ($v['end_time'] < $v['over_time'] - 86400)) {
                    $task_delay++;
                }
            }
        }
        $task_pie = [
            'count' => $task_count,
            'count_ok' => $task_count_ok,
            'delay' => $task_delay,
            'ok_lv' => $task_count == 0 ? 100 : round($task_count_ok * 100 / $task_count, 2),
            'delay_lv' => $task_count == 0 ? 100 : round($task_delay * 100 / $task_count, 2),
        ];

        $bugs = Db::name('Task')->field('id,flow_status')->order('end_time asc')->where(['delete_time' => 0, 'is_bug' => 1, 'project_id' => $param['project_id']])->select()->toArray();
        $status_a = $status_b = $status_c = $status_d = $status_e = 0;
        foreach ($bugs as $k => $v) {
            if ($v['flow_status'] == 1) {
                $status_a++;
            } else if ($v['flow_status'] == 2) {
                $status_b++;
            } else if ($v['flow_status'] == 3) {
                $status_c++;
            } else if ($v['flow_status'] == 4) {
                $status_d++;
            } else if ($v['flow_status'] == 5) {
                $status_e++;
            }
        }
        $bug_status = [
            'status_a' => $status_a,
            'status_b' => $status_b,
            'status_c' => $status_c,
            'status_d' => $status_d,
            'status_e' => $status_e,
        ];

        $date_tasks = [];
        if ($tasks) {
            $date_tasks = plan_count($tasks);
        }

        $tasks_ok = Db::name('Task')->field('id,over_time as end_time')->order('over_time asc')->where([['over_time', '>', 0], ['delete_time', '=', 0], ['project_id', '=', $param['project_id']]])->select()->toArray();
        $date_tasks_ok = [];
        if ($tasks_ok) {
            $date_tasks_ok = plan_count($tasks_ok);
        }
        $tids = Db::name('Task')->where(['delete_time' => 0, 'project_id' => $param['project_id']])->column('id');
        $schedules = Db::name('Schedule')->where([['tid', 'in', $tids], ['delete_time', '=', 0]])->select()->toArray();
        $date_schedules = [];
        if ($schedules) {
            $date_schedules = hour_count($schedules);
        }

        $res['task_pie'] = $task_pie;
        $res['bug_status'] = $bug_status;
        $res['date_tasks'] = $date_tasks;
        $res['date_tasks_ok'] = $date_tasks_ok;
        $res['date_schedules'] = $date_schedules;
        to_assign(0, '', $res);
    }

	public function user()
    {
        $param = get_params();
		$project = Db::name('Project')->where(['id' => $param['tid']])->find();
		$users = Db::name('ProjectUser')
				->field('pu.*,a.name,a.mobile,p.title as position,d.title as department')
				->alias('pu')
				->join('Admin a', 'pu.uid = a.id', 'LEFT')
				->join('Department d', 'a.did = d.id', 'LEFT')
				->join('Position p', 'a.position_id = p.id', 'LEFT')
				->order('pu.id asc')
				->where(['pu.project_id' => $param['tid']])
				->select()->toArray();
		if(!empty($users)){
			foreach ($users as $k => &$v) {
				$v['role'] = 0; //普通项目成员
				if ($v['uid'] == $project['admin_id']) {
					$v['role'] = 1; //项目创建人
				}
				if ($v['uid'] == $project['director_uid']) {
					$v['role'] = 2; //项目负责人
				}

				$v['create_time'] = date('Y-m-d', (int) $v['create_time']);
				if($v['delete_time'] > 0){
					$v['delete_time'] = date('Y-m-d', (int) $v['delete_time']);
				}

				$tids = Db::name('Task')->where([['project_id','=',$param['tid']],['delete_time','=',0]])->column('id');
				$schedule_map = [];
        		$schedule_map[] = ['tid','in',$tids];
        		$schedule_map[] = ['delete_time','=',0];
        		$schedule_map[] = ['admin_id','=',$v['uid']];
        		$v['schedules'] = Db::name('Schedule')->where($schedule_map)->count();
        		$v['labor_times'] = Db::name('Schedule')->where($schedule_map)->sum('labor_time');

				$task_map = [];
				$task_map[] = ['project_id','=',$param['tid']];
				$task_map[] = ['delete_time', '=', 0];

				$task_map1 = [
					['admin_id', '=', $v['uid']],
				];
				$task_map2 = [
					['director_uid', '=', $v['uid']],
				];
				$task_map3 = [
					['', 'exp', Db::raw("FIND_IN_SET('{$v['uid']}',assist_admin_ids)")],
				];

				//任务
				$task_map_a = $task_map;
				$task_map_a[] = ['is_bug', '=', 0];
				//任务总数
				$v['tasks_a_total'] = Db::name('Task')
				->where(function ($query) use ($task_map1, $task_map2, $task_map3) {
					$query->where($task_map1)->whereor($task_map2)->whereor($task_map3);
				})
				->where($task_map_a)->count();
				//已完成任务
				$task_map_a[] = ['flow_status', '>', 2]; //已完成
				$v['tasks_a_finish'] = Db::name('Task')->where(function ($query) use ($task_map1, $task_map2, $task_map3) {
					$query->where($task_map1)->whereor($task_map2)->whereor($task_map3);
				})
				->where($task_map_a)->count();
				//未完成任务
				$v['tasks_a_unfinish'] = $v['tasks_a_total'] - $v['tasks_a_finish'];
				$v['tasks_a_pensent'] = "100％";
				if ($v['tasks_a_total'] > 0) {
					$v['tasks_a_pensent'] = round($v['tasks_a_finish'] / $v['tasks_a_total'] * 100, 2) . "％";
				}

				//缺陷
				$task_map_b = $task_map;
				$task_map_b[] = ['is_bug', '=', 1];
				//缺陷总数
				$v['tasks_b_total'] = Db::name('Task')
				->where(function ($query) use ($task_map1, $task_map2, $task_map3) {
					$query->where($task_map1)->whereor($task_map2)->whereor($task_map3);
				})
				->where($task_map_b)->count();
				//已完成缺陷
				$task_map_b[] = ['flow_status', '>', 2]; //已完成
				$v['tasks_b_finish'] = Db::name('Task')->where(function ($query) use ($task_map1, $task_map2, $task_map3) {
					$query->where($task_map1)->whereor($task_map2)->whereor($task_map3);
				})
				->where($task_map_b)->count();
				//未完成缺陷
				$v['tasks_b_unfinish'] = $v['tasks_b_total'] - $v['tasks_b_finish'];
				$v['tasks_b_pensent'] = "100％";
				if ($v['tasks_b_total'] > 0) {
					$v['tasks_b_pensent'] = round($v['tasks_b_finish'] / $v['tasks_b_total'] * 100, 2) . "％";
				}
			}
		}
        to_assign(0, '', $users);
    }

	//新增项目成员
    public function add_user()
    {
        $param = get_params();
        if (request()->isPost()) {
			$has = Db::name('ProjectUser')->where(['uid' => $param['uid'],'project_id'=>$param['project_id']])->find();
			if(!empty($has)){
				to_assign(1, '该员工已经是项目成员');
			}
			$project = Db::name('Project')->where(['id' => $param['project_id']])->find();
			if($this->uid == $project['admin_id'] || $this->uid == $project['director_uid']){
				$param['admin_id'] = $this->uid;
				$param['create_time'] = time();
				$res = Db::name('ProjectUser')->strict(false)->field(true)->insert($param);
				if ($res) {
					$log_data = array(
                        'module' => 'project',
                        'field' => 'user',
                        'action' => 'add',
                        'project_id' => $param['project_id'],
                        'admin_id' => $this->uid,
                        'new_content' => $param['uid'],
                        'create_time' => time(),
                    );
                    Db::name('Log')->strict(false)->field(true)->insert($log_data);
					to_assign();
				}				
			}else{
				to_assign(1, '只有项目创建者和负责人才有权限新增项目成员');
			}
		}
	}

	//移除项目成员
	public function remove_user()
	{
		$param = get_params();
		if (request()->isDelete()) {
			$detail = Db::name('ProjectUser')->where(['id' => $param['id']])->find();
			$project = Db::name('Project')->where(['id' => $detail['project_id']])->find();
			if($this->uid == $project['admin_id'] || $this->uid == $project['director_uid']){
				if($detail['uid'] == $project['admin_id']){
					to_assign(1, '该项目成员是项目的创建者，不能移除');
				}
				if($detail['uid'] == $project['director_uid']){
					to_assign(1, '该项目成员是项目的负责人，需要去除负责人权限才能移除');
				}
				$param['delete_time'] = time();
				if (Db::name('ProjectUser')->update($param) !== false) {	
					$log_data = array(
						'module' => 'project',
						'field' => 'user',
						'action' => 'remove',
						'project_id' => $detail['project_id'],
						'admin_id' => $this->uid,
						'new_content' => $detail['uid'],
						'create_time' => time(),
					);
					Db::name('Log')->strict(false)->field(true)->insert($log_data);			
					return to_assign(0, "移除成功");
				} else {
					return to_assign(1, "移除失败");
				}
			}else{
				to_assign(1, '只有项目创建者和负责人才有权限移除项目成员');
			}
		}else{
			return to_assign(1, "错误的请求");
		}
	}
	//恢复项目成员
	public function recover_user()
	{
		$param = get_params();
		if (request()->isPost()) {
			$detail = Db::name('ProjectUser')->where(['id' => $param['id']])->find();
			$project = Db::name('Project')->where(['id' => $detail['project_id']])->find();
			if($this->uid == $project['admin_id'] || $this->uid == $project['director_uid']){
				$param['delete_time'] = 0;
				if (Db::name('ProjectUser')->update($param) !== false) {		
					$log_data = array(
						'module' => 'project',
						'field' => 'user',
						'action' => 'recover',
						'project_id' => $detail['project_id'],
						'admin_id' => $this->uid,
						'new_content' => $detail['uid'],
						'create_time' => time(),
					);
					Db::name('Log')->strict(false)->field(true)->insert($log_data);			
					return to_assign(0, "恢复成功");
				} else {
					return to_assign(1, "恢复失败");
				}
			}else{
				to_assign(1, '只有项目创建者和负责人才有权限恢复项目成员');
			}
		}else{
			return to_assign(1, "错误的请求");
		}
	}
	
	//编辑阶段
    public function reset_check()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
		$detail = Db::name('Project')->where(['id' => $param['id']])->find();
		if (request()->isPost()) {
			if ($this->uid == $detail['admin_id'] || $this->uid == $detail['director_uid']) {
				$flowNameData = isset($param['flowName']) ? $param['flowName'] : '';
				$flowUidsData = isset($param['chargeIds']) ? $param['chargeIds'] : '';
				$flowIdsData = isset($param['membeIds']) ? $param['membeIds'] : '';
				$flowDateData = isset($param['cycleDate']) ? $param['cycleDate'] : '';
				$flow = [];
				$time_1 = $detail['start_time'];
				$time_2 = $detail['end_time'];
				foreach ($flowNameData as $key => $value) {
					if (!$value) {
						continue;
					}				
					$flowDate = explode('到',$flowDateData[$key]);
					$start_time = strtotime(urldecode(trim($flowDate[0])));
					$end_time = strtotime(urldecode(trim($flowDate[1])));
					if($start_time<$time_1){
						if($key == 0){
							return to_assign(1, '第'.($key+1).'阶段的开始时间不能小于计划开始时间');
						}
						else{
							return to_assign(1, '第'.($key+1).'阶段的开始时间不能小于第'.($key).'阶段的结束时间');
						}
						break;
					}
					if($end_time>$time_2){
						return to_assign(1, '第'.($key+1).'阶段的结束时间不能大于计划结束时间');
						break;
					}
					else{
						$time_1 = $end_time;
					}
					$item = [];
					$item['action_id'] = $id;
					$item['flow_name'] = $value;
					$item['type'] = 1;
					$item['flow_uid'] = $flowUidsData[$key];
					$item['flow_ids'] = $flowIdsData[$key];
					$item['sort'] = $key;
					$item['start_time'] = $start_time;
					$item['end_time'] = $end_time;
					$item['create_time'] = time();
					$flow[]=$item;	
				}
				//删除原来的阶段步骤
				Db::name('Step')->where(['action_id'=>$id,'type'=>1,'delete_time'=>0])->update(['delete_time'=>time()]);
				Db::name('StepRecord')->where(['action_id'=>$id,'type'=>1,'delete_time'=>0])->update(['delete_time'=>time()]);
				$res = Db::name('Step')->strict(false)->field(true)->insertAll($flow);
				if ($res) {
					$checkData=array(
						'action_id' => $id,
						'step_id' => 0,
						'check_uid' => $this->uid,
						'type' => 1,
						'check_time' => time(),
						'status' => 0,
						'create_time' => time()
					);
					$aid = Db::name('StepRecord')->strict(false)->field(true)->insertGetId($checkData);
					$resa = Db::name('Project')->where('id', $id)->strict(false)->field(true)->update(['step_sort'=>0,'update_time'=>time()]);
					add_log('reset', $param['id'], $param,[],'项目阶段');
				}
				return to_assign();
			} else {
				return to_assign(1, '只有创建人或者负责人才有权限编辑');
			}
		}
	}
	
	//审核
    public function step_check()
    {
        $param = get_params();
		$detail = Db::name('Project')->where(['id' => $param['id']])->find();
		//当前审核节点详情
		$step = Db::name('Step')->where(['action_id'=>$detail['id'],'type'=>1,'sort'=>$detail['step_sort'],'delete_time'=>0])->find();
		if ($this->uid != $step['flow_uid']){		
			return to_assign(1,'您没权限操作');
		}
		//审核通过
		if($param['check'] == 1){
			$next_step = Db::name('Step')->where(['action_id'=>$detail['id'],'type'=>1,'sort'=>($detail['step_sort']+1),'delete_time'=>0])->find();
			if($next_step){
				$param['step_sort'] = $next_step['sort'];
				$param['status'] = 2;
			}
			else{
				//不存在下一步审核，审核结束
				$param['status'] = 3;
				$param['step_sort'] = $detail['step_sort']+1;
			}		
			//审核通过数据操作
			$res = Db::name('Project')->strict(false)->field('step_sort,status')->update($param);
			if($res!==false){
				$checkData=array(
					'action_id' => $detail['id'],
					'step_id' => $step['id'],
					'check_uid' => $this->uid,
					'type' => 1,
					'check_time' => time(),
					'status' => $param['check'],
					'create_time' => time()
				);
				$aid = Db::name('StepRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('check', $param['id'], $param,[],'项目阶段');
				return to_assign();
			}
			else{
				return to_assign(1,'操作失败');
			}
		}
		//拒绝审核
		else if($param['check'] == 2){
			//获取上一步的审核信息
			$prev_step = Db::name('Step')->where(['action_id'=>$detail['id'],'type'=>1,'sort'=>($detail['step_sort']-1),'delete_time'=>0])->find();
			if($prev_step){
				//存在上一步审核
				$param['step_sort'] = $prev_step['sort'];
			}
			else{
				//不存在上一步审核，审核初始化步骤
				$param['step_sort'] = 0;				
				$param['status'] = 1;
			}
		}
		$res = Db::name('Project')->strict(false)->field('step_sort,status')->update($param);
		if($res!==false){
			$checkData=array(
				'action_id' => $detail['id'],
				'step_id' => $step['id'],
				'check_uid' => $this->uid,
				'type' => 1,
				'check_time' => time(),
				'status' => $param['check'],
				'content' => $param['content'],
				'create_time' => time()
			);	
			$aid = Db::name('StepRecord')->strict(false)->field(true)->insertGetId($checkData);
			add_log('refue', $param['id'], $param,[],'项目阶段');
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}				
    }
	
    //获取树形任务列表
    public function get_project_task(){
	    $param = get_params();
		$list = Db::name('Task')->withoutField('content,md_content')->where(['project_id'=>$param['project_id'],'delete_time'=>0])->order('id desc')->select()->toArray();
		foreach ($list as $key => &$vo) {
				$vo['director_name'] = '-';
				if ($vo['director_uid'] > 0) {
					$vo['director_name'] = Db::name('Admin')->where(['id' => $vo['director_uid']])->value('name');
				}
                $assist_admin_names = Db::name('Admin')->where([['id', 'in', $vo['assist_admin_ids']]])->column('name');
                if (empty($assist_admin_names)) {
                    $vo['assist_admin_names'] = '-';
                } else {
                    $vo['assist_admin_names'] = implode(',', $assist_admin_names);
                }
                $vo['cate_name'] = Db::name('WorkCate')->where(['id' => $vo['cate']])->value('title');
				$vo['type_name'] = Db::name('TaskCate')->where(['id' => $vo['type']])->value('title');
				$vo['after_num'] = Db::name('Task')->where(['before_task'=>$vo['id'],'delete_time' => 0])->count();
				if($vo['after_num']==1){
					$vo['after_id'] = Db::name('Task')->where(['before_task'=>$vo['id'],'delete_time' => 0])->value('id');
				}
				$vo['delay'] = 0;
				if ($vo['end_time'] > 0) {
					$vo['end_time'] = date('Y-m-d', $vo['end_time']);
					if ($vo['over_time'] > 0 && $vo['flow_status'] < 4) {
						$vo['delay'] = countDays($vo['end_time'], date('Y-m-d', $vo['over_time']));
					}
					if ($vo['over_time'] == 0 && $vo['flow_status'] < 4) {
						$vo['delay'] = countDays($vo['end_time']);
					}
				}
				else{
					$vo['end_time'] = '-';
				}
                $vo['priority_name'] = TaskList::$Priority[(int) $vo['priority']];
                $vo['flow_name'] = TaskList::$FlowStatus[(int) $vo['flow_status']];
		}
        $res['total'] = count($list);
        $res['data'] = table_tree_list(0, $list);
        return table_assign(0, '', $res);
	}
	
    //获取任务列表
    public function get_task(){
	    $param = get_params();
        $param['uid'] = $this->uid;
        $list = (new TaskList())->list($param);
        return table_assign(0, '', $list);
	}
	
	//子任务新增
    public function task_add_son()
    {
		$param = get_params();
		$parent_task_array = admin_parent_task($param['id']);
		if (in_array($param['pid'], $parent_task_array)) {
			return to_assign(1, '子任务不能是该任务的父级以上的任务');
		}
		$after_task_array = admin_after_task_son($param['id']);
		if (in_array($param['pid'], $after_task_array)) {
			return to_assign(1, '子任务不能是该任务本身或其前置任务');
		}
		$res = Db::name('Task')->where(['id'=>$param['id']])->update(['pid'=>$param['pid']]);
		if ($res) {
			return to_assign(0, "操作成功");
		} else {
			return to_assign(1, "操作失败");
		}
	}
	
	//子任务删除
    public function task_del_son()
    {
		$param = get_params();
		$res = Db::name('Task')->where(['id'=>$param['id']])->update(['pid'=>0]);
		if ($res) {
			return to_assign(0, "操作成功");
		} else {
			return to_assign(1, "操作失败");
		}
	}
}
