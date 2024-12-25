<?php
namespace app\model;

use think\facade\Db;
use think\Model;

class Task extends Model
{
    const ZERO = 0; //#648A8D
    const ONE = 1; //#4AC8BE
    const TWO = 2; //#409CDE
    const THREE = 3; //#C0DB38
    const FOUR = 4; //#4DCE58
    const FIVE = 5; //#FEC939
    const SIX = 6; //#8838DA
    const SEVEN = 7; //#FD6206
    const EIGHT = 8; //#F03347
    const NINE = 9; //#A38B82

    public static $Priority = [
        self::ZERO => '未设置',
        self::ONE => '低',
        self::TWO => '中',
        self::THREE => '高',
        self::FOUR => '紧急',
    ];
    public static $FlowStatus = [
        self::ZERO => '未设置',
        self::ONE => '未开始',
        self::TWO => '进行中',
        self::THREE => '已完成',
        self::FOUR => '已拒绝',
        self::FIVE => '已关闭',
    ];

    //列表
    function list($param) {
        $where = [];
		$whereOr=[];
        $map1 = [];
        $map2 = [];
        $map3 = [];
        $map4 = [];
        if (!empty($param['project_id'])) {
            $where[] = ['project_id', '=', $param['project_id']];
        } else {
            $project_ids = Db::name('Project')->where(['director_uid' => $param['uid'], 'delete_time' => 0])->column('id');
            $map1[] = ['admin_id', '=', $param['uid']];
            $map2[] = ['director_uid', '=', $param['uid']];
            $map3[] = ['', 'exp', Db::raw("FIND_IN_SET({$param['uid']},assist_admin_ids)")];
            $map4[] = ['project_id', 'in', $project_ids];
			//1、任务创建人，2，任务负责人，3任务协助人，4任务所属项目的负责人
			if(is_super($param['uid']) == 0){
				$whereOr =[$map1,$map2,$map3,$map4];
			}
        }
		
		
        if (!empty($param['type'])) {
            $where[] = ['type', '=', $param['type']];
        }
        if (!empty($param['flow_status'])) {
            $where[] = ['flow_status', '=', $param['flow_status']];
        }
        if (!empty($param['priority'])) {
            $where[] = ['priority', '=', $param['priority']];
        }
        if (!empty($param['cate'])) {
            $where[] = ['cate', '=', $param['cate']];
        }
        if (!empty($param['director_uid'])) {
            $where[] = ['director_uid', '=', $param['director_uid']];
        }
		if (!empty($param['is_delay'])) {
			if ($param['is_delay']==1) {
				$where[] = ['flow_status', 'in', [1,2]];
				$where[] = ['end_time', '>', time()-86400];
			}
			if ($param['is_delay']==2) {
				$where[] = ['flow_status', 'in', [1,2]];
				$where[] = ['end_time', '<=', time()-86400];
			}
		}
		if (!empty($param['assist_admin_ids'])) {
            $where[] = ['', 'exp', Db::raw("FIND_IN_SET({$param['assist_admin_ids']},assist_admin_ids)")];
        }
		if (!empty($param['set_pid'])) {
            $where[] = ['pid', '=', 0];
            $where[] = ['id', '<>', $param['set_pid']];
            $where[] = ['before_task', '<>', $param['set_pid']];
        }		
        if (!empty($param['keywords'])) {
            $where[] = ['title|content', 'like', '%' . $param['keywords'] . '%'];
        }
        $where[] = ['delete_time', '=', 0];
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('Task')->where(function ($query) use ($whereOr) {
			if (!empty($whereOr))
				$query->whereOr($whereOr);
			})->where($where)
            ->withoutField('content,md_content')
            ->order('flow_status asc,id desc')
            ->paginate($rows, false, ['query' => $param])
            ->each(function ($item, $key) {
                $item['director_name'] = Db::name('Admin')->where(['id' => $item['director_uid']])->value('name');
                $assist_admin_names = Db::name('Admin')->where([['id', 'in', $item['assist_admin_ids']]])->column('name');
                $item['cate_name'] = Db::name('WorkCate')->where(['id' => $item['cate']])->value('title');
                $item['type_name'] = Db::name('TaskCate')->where(['id' => $item['type']])->value('title');
                if (empty($assist_admin_names)) {
                    $item['assist_admin_names'] = '-';
                } else {
                    $item['assist_admin_names'] = implode(',', $assist_admin_names);
                }
                $item['end_time'] = date('Y-m-d', $item['end_time']);
                $item['delay'] = 0;
                if ($item['over_time'] > 0 && $item['flow_status'] < 4) {
                    $item['delay'] = countDays($item['end_time'], date('Y-m-d', $item['over_time']));
                }
                if ($item['over_time'] == 0 && $item['flow_status'] < 4) {
                    $item['delay'] = countDays($item['end_time']);
                }
				
				$item['project_name'] = '-';
				if ($item['project_id'] > 0) {
					$item['project_name'] = Db::name('Project')->where(['id' => $item['project_id'],'delete_time' => 0])->value('name');
                }
                $item['priority_name'] = self::$Priority[(int) $item['priority']];
                $item['flow_name'] = self::$FlowStatus[(int) $item['flow_status']];
                return $item;
            });
        return $list;
    }
    //详情
    public function detail($id)
    {
        $detail = Db::name('Task')->where(['id' => $id])->find();
        if (!empty($detail)) {
            $detail['product_name'] = '';
            $detail['project_name'] = '';
            if ($detail['project_id'] > 0) {
                $project = Db::name('Project')->where(['id' => $detail['project_id']])->field('product_id,name')->find();
                $detail['product_name'] = Db::name('Product')->where(['id' => $project['product_id']])->value('name');
                $detail['project_name'] = $project['name'];
            }
            $detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
            $detail['director_name'] = Db::name('Admin')->where(['id' => $detail['director_uid']])->value('name');
            $detail['work_hours'] = Db::name('Schedule')->where(['delete_time' => 0, 'tid' => $detail['id']])->sum('labor_time');
            $detail['cate_name'] = Db::name('WorkCate')->where(['id' => $detail['cate']])->value('title');
            $detail['type_name'] = Db::name('TaskCate')->where(['id' => $detail['type']])->value('title');
            $detail['logs'] = Db::name('Log')->where(['module' => 'task', 'task_id' => $detail['id']])->count();
            $detail['comments'] = Db::name('Comment')->where(['module' => 4, 'delete_time' => 0, 'topic_id' => $detail['id']])->count();
            $detail['assist_admin_names'] = '';
            if (!empty($detail['assist_admin_ids'])) {
                $assist_admin_names = Db::name('Admin')->where('id', 'in', $detail['assist_admin_ids'])->column('name');
                $detail['assist_admin_names'] = implode(',', $assist_admin_names);
            }
            $detail['priority_name'] = self::$Priority[(int) $detail['priority']];
            $detail['flow_name'] = self::$FlowStatus[(int) $detail['flow_status']];
            $detail['times'] = time_trans($detail['create_time']);
            $detail['end_time'] = date('Y-m-d', $detail['end_time']);
            $detail['delay'] = 0;
            if ($detail['over_time'] > 0 && $detail['flow_status'] < 4) {
                $detail['delay'] = countDays($detail['end_time'], date('Y-m-d', $detail['over_time']));
            }
            if ($detail['over_time'] == 0 && $detail['flow_status'] < 4) {
                $detail['delay'] = countDays($detail['end_time']);
            }
        }
        return $detail;
    }
}
