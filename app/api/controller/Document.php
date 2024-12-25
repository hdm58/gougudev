<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\model\Document as DocumentList;
use think\facade\Db;
use think\facade\View;

class Document extends BaseController
{
    //获取文档列表
    public function get_list()
    {
        $param = get_params();
        $model = new DocumentList();
        $list = $model->get_list($param);
        return to_assign(0, '', $list);
    }

    //添加修改
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
                $param['update_time'] = time();
                $detail = (new DocumentList())->detail($param['id']);
                $res = DocumentList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res) {
                    $log_data = array(
                        'module' => 'document',
                        'field' => 'document',
                        'action' => 'edit',
                        'document_id' => $param['id'],
                        'admin_id' => $this->uid,
                        'old_content' => $detail['content'],
                        'new_content' => $param['content'],
                        'remark' => $param['title'],
                        'create_time' => time(),
                    );
                    Db::name('Log')->strict(false)->field(true)->insert($log_data);
                }
                return to_assign();
            } else {
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $sid = DocumentList::strict(false)->field(true)->insertGetId($param);
                if ($sid) {
                    $log_data = array(
                        'module' => 'document',
                        'field' => 'document',
                        'action' => 'add',
                        'document_id' => $sid,
                        'admin_id' => $this->uid,
                        'remark' => $param['title'],
                        'create_time' => time(),
                    );
                    Db::name('Log')->strict(false)->field(true)->insert($log_data);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = (new DocumentList())->detail($id);
                if (empty($detail)) {
                    return to_assign(1, '文档不存在');
                }
                View::assign('project_id', $detail['topic_id']);
                View::assign('detail', $detail);
            }
            if (isset($param['project_id'])) {
                View::assign('project_id', $param['project_id']);
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
        $detail = (new DocumentList())->detail($id);
        if (empty($detail)) {
            return to_assign(1, '文档不存在');
        } else {
            View::assign('detail', $detail);
            View::assign('id', $id);
            return view();
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $detail = (new DocumentList())->detail($id);
            if (DocumentList::where('id', $id)->update(['delete_time' => time()]) !== false) {
                $log_data = array(
                    'module' => $detail['module'],
                    'field' => 'document',
                    'action' => 'delete',
                    'document_id' => $id,
                    'admin_id' => $this->uid,
                    'remark' => $detail['title'],
                    'new_content' => '',
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
