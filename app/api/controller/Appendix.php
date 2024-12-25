<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Appendix extends BaseController
{
    //添加修改
    public function add()
    {
        $param = get_params();
        $param['create_time'] = time();
        $param['admin_id'] = $this->uid;
        $fid = Db::name('FileInterfix')->strict(false)->field(true)->insertGetId($param);
        if ($fid) {
            $log_data = array(
                'module' => $param['module'],
                'field' => 'file',
                'action' => 'upload',
                $param['module'] . '_id' => $param['topic_id'],
                'admin_id' => $this->uid,
                'old_content' => '',
                'new_content' => $param['file_name'],
                'create_time' => time(),
            );
            Db::name('Log')->strict(false)->field(true)->insert($log_data);
            return to_assign(0, '', $fid);
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $detail = Db::name('FileInterfix')->where('id', $id)->find();
            if (Db::name('FileInterfix')->where('id', $id)->delete() !== false) {
                $file_name = Db::name('File')->where('id', $detail['file_id'])->value('name');
                $log_data = array(
                    'module' => $detail['module'],
                    'field' => 'file',
                    'action' => 'delete',
                    $detail['module'] . '_id' => $detail['topic_id'],
                    'admin_id' => $this->uid,
                    'new_content' => $file_name,
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

    //链接添加修改
    public function add_link()
    {
        $param = get_params();
        $validate = \think\facade\Validate::rule([
            'url' => 'url',
        ]);
        if (!$validate->check($param)) {
            return to_assign(1, $validate->getError());
        }
        if (!empty($param['id']) && $param['id'] > 0) {
            $param['update_time'] = time();
            $res = Db::name('LinkInterfix')->where('id', $param['id'])->strict(false)->field(true)->update($param);
            if ($res) {
                $log_data = array(
                    'module' => $param['module'],
                    'field' => 'link',
                    'action' => 'edit',
                    $param['module'] . '_id' => $param['topic_id'],
                    'admin_id' => $this->uid,
                    'old_content' => $param['url'],
                    'new_content' => $param['desc'],
                    'create_time' => time(),
                );
                Db::name('Log')->strict(false)->field(true)->insert($log_data);
                return to_assign(0, '编辑成功');
            }
        } else {
            $param['create_time'] = time();
            $param['admin_id'] = $this->uid;
            $lid = Db::name('LinkInterfix')->strict(false)->field(true)->insertGetId($param);
            if ($lid) {
                $log_data = array(
                    'module' => $param['module'],
                    'field' => 'link',
                    'action' => 'add',
                    $param['module'] . '_id' => $param['topic_id'],
                    'admin_id' => $this->uid,
                    'new_content' => $param['desc'],
                    'create_time' => time(),
                );
                Db::name('Log')->strict(false)->field(true)->insert($log_data);
                return to_assign(0, '添加成功', $lid);
            }
        }
    }

    //删除
    public function delete_link()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $detail = Db::name('LinkInterfix')->where('id', $id)->find();
            if (Db::name('LinkInterfix')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                $log_data = array(
                    'module' => $detail['module'],
                    'field' => 'link',
                    'action' => 'delete',
                    $detail['module'] . '_id' => $detail['topic_id'],
                    'admin_id' => $this->uid,
                    'new_content' => $detail['desc'],
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
