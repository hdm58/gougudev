<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\validate\NoteCheck;
use app\base\BaseController;
use app\model\Note as NoteList;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Note extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.title|a.content', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['a.delete_time', '=', 0];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $note = NoteList::where($where)
                ->field('a.*,c.title as cate_title')
                ->alias('a')
                ->join('NoteCate c', 'a.cate_id = c.id', 'LEFT')
                ->order('a.create_time desc')
                ->paginate($rows, false, ['query' => $param])
                ->each(function ($item, $key) {
                    $item->start_time = empty($item->start_time) ? '-' : date('Y-m-d', $item->start_time);
                    $item->end_time = empty($item->end_time) ? '-' : date('Y-m-d', $item->end_time);
                });
            return table_assign(0, '', $note);
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

            $param['start_time'] = isset($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $param['end_time'] = isset($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(NoteCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = NoteList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }

                return to_assign();
            } else {
                try {
                    validate(NoteCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $sid = NoteList::strict(false)->field(true)->insertGetId($param);
                if ($sid) {
                    add_log('add', $sid, $param);
                    $users = Db::name('Admin')->where(['status' => 1])->column('id');					
                    sendMessage($users, 1, ['title' => $param['title'], 'from_uid' => $this->uid,'action_id' => $sid]);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $note = Db::name('Note')->where(['id' => $id])->find();
                View::assign('note', $note);
            }
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $note = Db::name('Note')->where(['id' => $id])->find();
        View::assign('note', $note);
        return view();
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $data['delete_time'] = time();
            $data['id'] = $id;
            if (Db::name('Note')->update($data) !== false) {
                add_log('delete', $id);
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
}
