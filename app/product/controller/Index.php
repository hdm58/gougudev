<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\product\controller;

use app\base\BaseController;
use app\model\Product as ProductList;
use app\product\validate\ProductCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();

            $map1 = [
                ['admin_id', '=', $this->uid],
            ];
            $map2 = [
                ['director_uid', '=', $this->uid],
            ];
            $map3 = [
                ['', 'exp', Db::raw("FIND_IN_SET({$this->uid},check_admin_ids)")],
            ];
            $map4 = [
                ['is_open', '=', 2],
            ];
			
			$whereOr=[];
			if(is_super($this->uid) == 0){
				$whereOr =[$map1,$map2,$map3,$map4];
			}
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $list = ProductList::withoutField('content,md_content')
                ->where(function ($query) use ($whereOr) {
				if (!empty($whereOr))
					$query->whereOr($whereOr);
				})
                ->where('delete_time', 0)
                ->order('id desc')
                ->paginate($rows, false, ['query' => $param])
                ->each(function ($item, $key) {
                    $item->director_name = Db::name('Admin')->where(['id' => $item->director_uid])->value('name');
                    $item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
                    $item->status_name = ProductList::$Status[(int) $item->status];
                    $item->projects = Db::name('Project')->where(['delete_time' => 0, 'product_id' => $item->id])->count();

                    $project_ids = Db::name('Project')->where(['delete_time' => 0, 'product_id' => $item->id])->column('id');
                    $task_map = [];
                    $task_map[] = ['project_id', 'in', $project_ids];
                    $task_map[] = ['delete_time', '=', 0];

                    //任务
                    $task_map_a = $task_map;
                    $task_map_a[] = ['is_bug', '=', 0];
                    //任务总数
                    $item->tasks_a_total = Db::name('Task')->where($task_map_a)->count();
                    //已完成任务
                    $task_map_a[] = ['flow_status', '>', 2]; //已完成
                    $item->tasks_a_finish = Db::name('Task')->where($task_map_a)->count();
                    //未完成任务
                    $item->tasks_a_unfinish = $item->tasks_a_total - $item->tasks_a_finish;
                    if ($item->tasks_a_total > 0) {
                        $item->tasks_a_pensent = round($item->tasks_a_finish / $item->tasks_a_total * 100, 2) . "％";
                    } else {
                        $item->tasks_a_pensent = "100％";
                    }

                    //缺陷
                    $task_map_b = $task_map;
                    $task_map_b[] = ['is_bug', '=', 1];
                    //缺陷总数
                    $item->tasks_b_total = Db::name('Task')->where($task_map_b)->count();
                    //已完成缺陷
                    $task_map_b[] = ['flow_status', '>', 2]; //已完成
                    $item->tasks_b_finish = Db::name('Task')->where($task_map_b)->count();
                    //未完成缺陷
                    $item->tasks_b_unfinish = $item->tasks_b_total - $item->tasks_b_finish;
                    if ($item->tasks_b_total > 0) {
                        $item->tasks_b_pensent = round($item->tasks_b_finish / $item->tasks_b_total * 100, 2) . "％";
                    } else {
                        $item->tasks_b_pensent = "100％";
                    }
                });
            return table_assign(0, '', $list);
        } else {
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
            if (!empty($param['id']) && $param['id'] > 0) {
                $product = (new ProductList())->detail($param['id']);
                if ($this->uid == $product['admin_id'] || $this->uid == $product['director_uid']) {
                    try {
                        validate(ProductCheck::class)->scene('edit')->check($param);
                    } catch (ValidateException $e) {
                        // 验证失败 输出错误信息
                        return to_assign(1, $e->getError());
                    }
                    $param['update_time'] = time();
                    $res = ProductList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                    if ($res) {
                        add_log('edit', $param['id'], $param, $product);
                    }
                    return to_assign();
                } else {
                    return to_assign(1, '只有创建人或者负责人才有权限编辑');
                }
            } else {
                try {
                    validate(ProductCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $sid = ProductList::strict(false)->field(true)->insertGetId($param);
                if ($sid) {
                    add_log('add', $sid, $param);
                    $log_data = array(
                        'module' => 'product',
                        'product_id' => $sid,
                        'new_content' => $param['name'],
                        'field' => 'new',
                        'action' => 'add',
                        'admin_id' => $this->uid,
                        'create_time' => time(),
                    );
                    Db::name('Log')->strict(false)->field(true)->insert($log_data);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = (new ProductList())->detail($id);
                if (empty($detail)) {
                    return to_assign(1, '产品不存在');
                }
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
        $detail = (new ProductList())->detail($id);
        if (empty($detail)) {
            return to_assign(1, '产品不存在');
        } else {
            $role_uid = [$detail['admin_id'], $detail['director_uid']];
            $role_edit = 'view';
            if (in_array($this->uid, $role_uid)) {
                $role_edit = 'edit';
            }
            $view_uid = $role_uid;
            if (!empty($detail['check_admin_ids'])) {
                $view_uid = array_merge($role_uid, explode(",", $detail['check_admin_ids']));
            }
            if (!in_array($this->uid, $view_uid) && $detail['is_open'] == 1 && is_super($this->uid) == 0) {
                return to_assign(1, '您没权限查看该产品');
            }
            $file_array = Db::name('FileInterfix')
                ->field('i.id,i.topic_id,i.admin_id,f.name,f.filesize,f.filepath,a.name as admin_name')
                ->alias('i')
                ->join('File f', 'i.file_id = f.id', 'LEFT')
                ->join('Admin a', 'i.admin_id = a.id', 'LEFT')
                ->order('i.create_time desc')
                ->where(array('i.topic_id' => $id, 'i.module' => 'product'))
                ->select()->toArray();
            $link_array = Db::name('LinkInterfix')
                ->field('i.id,i.topic_id,i.admin_id,i.desc,i.url,a.name as admin_name')
                ->alias('i')
                ->join('Admin a', 'i.admin_id = a.id', 'LEFT')
                ->order('i.create_time desc')
                ->where(array('i.topic_id' => $id, 'i.module' => 'product', 'delete_time' => 0))
                ->select()->toArray();
            View::assign('detail', $detail);
            View::assign('file_array', $file_array);
            View::assign('link_array', $link_array);
            View::assign('role_edit', $role_edit);
            View::assign('id', $id);
            return view();
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $count = Db::name('Project')->where([['product_id', '=', $id], ['delete_time', '=', 0]])->count();
            if ($count > 0) {
                return to_assign(1, "该产品下有关联项目，无法删除");
            }
            $detail = Db::name('Product')->where('id', $id)->find();
            if ($detail['admin_id'] != $this->uid) {
                return to_assign(1, "你不是该产品的创建人，无权限删除");
            }
            if (Db::name('Product')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                $log_data = array(
                    'module' => 'product',
                    'field' => 'delete',
                    'action' => 'delete',
                    'product_id' => $detail['id'],
                    'admin_id' => $this->uid,
                    'old_content' => '',
                    'new_content' => $detail['name'],
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
