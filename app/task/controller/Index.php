<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\task\controller;

use app\base\BaseController;
use app\model\Task as TaskList;
use app\task\validate\TaskCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $param['uid'] = $this->uid;
            $list = (new TaskList())->list($param);
            return table_assign(0, '', $list);
        } else {
            View::assign('cate', get_work_cate());
            View::assign('task_cate', get_task_cate());
            View::assign('project', get_project($this->uid));
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isPost()) {
            //markdown数据处理
            if (isset($param['table-align'])) {
                unset($param['table-align']);
            }
            if (isset($param['docContent-html-code'])) {
                $param['content'] = $param['docContent-html-code'];
                $param['md_content'] = $param['docContent-markdown-doc'];
                unset($param['docContent-html-code']);
                unset($param['docContent-markdown-doc']);
            }
            if (isset($param['ueditorcontent'])) {
                $param['content'] = $param['ueditorcontent'];
                $param['md_content'] = '';
            }
            if (isset($param['end_time'])) {
                $param['end_time'] = strtotime(urldecode($param['end_time']));
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                $task = (new TaskList())->detail($param['id']);
                try {
                    validate(TaskCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				if (isset($param['flow_status'])) {
					if ($param['flow_status'] == 3) {
						$param['done_ratio'] = 100;
						$param['over_time'] = time();
					}else if ($param['flow_status'] == 1) {
						$param['done_ratio'] = 0;
						$param['over_time'] = 0;
					} else {
						if($task['done_ratio'] == 100){
							$param['done_ratio'] = 80;
						}
						$param['over_time'] = 0;
					}
				}
				else{
					if (isset($param['done_ratio'])) {
						if ($param['done_ratio'] > 0 && $param['done_ratio'] < 100) {
							$param['flow_status'] = 2;
						}					
					}
				}
                $param['update_time'] = time();
                $res = TaskList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param, $task);
                }
                return to_assign();
            } else {
                try {
                    validate(TaskCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $sid = TaskList::strict(false)->field(true)->insertGetId($param);
                if ($sid) {
                    add_log('add', $sid, $param);
                    $log_data = array(
                        'module' => 'task',
                        'task_id' => $sid,
                        'new_content' => $param['title'],
                        'field' => 'new',
                        'action' => 'add',
                        'admin_id' => $this->uid,
                        'create_time' => time(),
                    );
                    Db::name('Log')->strict(false)->field(true)->insert($log_data);
                    $users = $param['director_uid'];
                    sendMessage($users, 21, ['title' => $param['title'],'from_uid' => $this->uid, 'create_time'=>date('Y-m-d H:i:s',time()), 'action_id' => $sid]);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = (new TaskList())->detail($id);
                if (empty($detail)) {
                    return to_assign(1, '任务不存在');
                }
                View::assign('detail', $detail);
            }
            if (isset($param['project_id'])) {
                View::assign('project_id', $param['project_id']);
            }
			View::assign('project', get_project($this->uid));
            View::assign('cate', get_work_cate());
            View::assign('type', get_task_cate());
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
        $detail = (new TaskList())->detail($id);
        if (empty($detail)) {
            return to_assign(1, '任务不存在');
        } else {
            $role_uid = [$detail['admin_id'], $detail['director_uid']];
            $role_edit = 'view';
            if (in_array($this->uid, $role_uid)) {
                $role_edit = 'edit';
            }
            $project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
            if (in_array($detail['project_id'], $project_ids) || in_array($this->uid, $role_uid) || in_array($this->uid, explode(",",$detail['assist_admin_ids'])) || is_super($this->uid) == 1) {
                $file_array = Db::name('FileInterfix')
                ->field('mf.id,mf.topic_id,mf.admin_id,f.name,f.filesize,f.filepath,f.create_time,a.name as admin_name')
                ->alias('mf')
                ->join('File f', 'mf.file_id = f.id', 'LEFT')
                ->join('Admin a', 'mf.admin_id = a.id', 'LEFT')
                ->order('mf.create_time desc')
                ->where(array('mf.topic_id' => $id, 'mf.module' => 'task'))
                ->select()->toArray();

                $link_array = Db::name('LinkInterfix')
                    ->field('i.id,i.topic_id,i.admin_id,i.desc,i.url,a.name as admin_name')
                    ->alias('i')
                    ->join('Admin a', 'i.admin_id = a.id', 'LEFT')
                    ->order('i.create_time desc')
                    ->where(array('i.topic_id' => $id, 'i.module' => 'task', 'i.delete_time' => 0))
                    ->select()->toArray();
					
				$schedule_array = Db::name('Schedule')
					->field('a.*,u.name')
					->alias('a')
					->join('Admin u', 'u.id = a.admin_id')
					->order('a.create_time desc')
					->where(array('a.tid' => $id, 'a.delete_time' => 0))
					->select()->toArray();

				$son_task = Db::name('Task')->where(['pid' => $detail['id']])->select()->toArray();				
				foreach ($son_task as $key => &$vo) {
					$vo['flow_name'] = TaskList::$FlowStatus[(int) $vo['flow_status']];
				}

                View::assign('son_task', $son_task);
                View::assign('detail', $detail);
                View::assign('file_array', $file_array);
                View::assign('link_array', $link_array);
                View::assign('schedule_array', $schedule_array);
                View::assign('role_edit', $role_edit);
                View::assign('id', $id);
                return view();
            }
            else{
                return to_assign(1, '您没权限查看该任务');
            }
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $detail = Db::name('Task')->where('id', $id)->find();
            if ($detail['admin_id'] != $this->uid) {
                return to_assign(1, "你不是该任务的创建人，无权限删除");
            }
			$count_schedule = Db::name('Schedule')->where(['tid'=>$id,'delete_time'=>0])->count();
			if($count_schedule>0){
				return to_assign(1, "该任务已经关联的工作记录，无法删除，如果不需要可以关闭该任务即可");
			}
            if (Db::name('Task')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                $log_data = array(
                    'module' => 'task',
                    'field' => 'delete',
                    'action' => 'delete',
                    'task_id' => $detail['id'],
                    'admin_id' => $this->uid,
                    'old_content' => '',
                    'new_content' => $detail['title'],
                    'create_time' => time(),
                );
                Db::name('Log')->strict(false)->field(true)->insert($log_data);
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
}
