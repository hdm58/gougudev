<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\admin\controller;

use app\base\BaseController;
use app\admin\validate\TaskCateCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Tcate extends BaseController
{
	//任务类别
    public function index()
    {
        if (request()->isAjax()) {
            $cate = Db::name('TaskCate')->order('id asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }
    //任务类别添加
    public function add()
    {
		if (request()->isPost()) {
            $param = get_params();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(TaskCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $data['update_time'] = time();
                $res = Db::name('TaskCate')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(TaskCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('TaskCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
    }
	
    //任务类别设置
    public function check()
    {
		if (request()->isPost()) {
			$param = get_params();
			$res = Db::name('TaskCate')->strict(false)->field('id,status')->update($param);
			if ($res) {
				if($param['status'] == 0){
					add_log('disable', $param['id'], $param);
				}
				else if($param['status'] == 1){
					add_log('recover', $param['id'], $param);
				}
				return to_assign();
			}
			else{
				return to_assign(0, '操作失败');
			}
		}else{
			return to_assign(0, '错误的请求');
		}
    }

   
   
}
