<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
use think\facade\Db;

//读取后置任务的ids
function admin_after_task_son($task_id = 0, $list = [])
{
    $task_ids = Db::name('Task')->where([['before_task','in',$task_id]])->column('id');
	if(!empty($task_ids)){
		$new_list = array_merge($list, $task_ids);
		$list = admin_after_task_son($task_ids, $new_list);
	}
	return $list;
}

 //读取父任务的ids
function admin_parent_task($task_id = 0, $list = [])
{
    $pids = Db::name('Task')->where([['pid','in',$task_id]])->column('id');
	if(!empty($pids)){
		$new_list = array_merge($list, $pids);
		$list = admin_parent_task($pids, $new_list);
	}
	return $list;
}