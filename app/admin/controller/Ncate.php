<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\admin\controller;

use app\base\BaseController;
use app\admin\validate\NoteCateCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Ncate extends BaseController
{
	//公告类别
    public function index()
    {
        if (request()->isAjax()) {
            $cate = Db::name('NoteCate')->order('create_time asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }

    //公告类别添加
    public function add()
    {
        $param = get_params();
        if (request()->isPost()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(NoteCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $note_array = admin_note_cate_son($param['id']);
                if (in_array($param['pid'], $note_array)) {
                    return to_assign(1, '父级分类不能是该分类本身或其子分类');
                } else {
                    $param['update_time'] = time();
                    $res = Db::name('NoteCate')->strict(false)->field(true)->update($param);
                    if ($res) {
                        add_log('edit', $param['id'], $param);
                    }
                    return to_assign();
                }
            } else {
                try {
                    validate(NoteCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('NoteCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
			$cate = $cate = Db::name('NoteCate')->order('id desc')->select()->toArray();
			$cates = set_recursion($cate);
            if ($id > 0) {
                $detail = Db::name('NoteCate')->where(['id' => $id])->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            View::assign('pid', $pid);
            View::assign('cates', $cates);
            return view();
        }
    }

    //公告类别删除
    public function delete()
    {
		if (request()->isDelete()) {
			$id = get_params("id");
			$cate_count = Db::name('NoteCate')->where(["pid" => $id])->count();
			if ($cate_count > 0) {
				return to_assign(1, "该分类下还有子分类，无法删除");
			}
			$content_count = Db::name('Note')->where(["cate_id" => $id])->count();
			if ($content_count > 0) {
				return to_assign(1, "该分类下还有公告，无法删除");
			}
			if (Db::name('NoteCate')->delete($id) !== false) {
				add_log('delete', $id);
				return to_assign(0, "删除成功");
			} else {
				return to_assign(1, "删除失败");
			}
		}else{
			return to_assign(1, "错误的请求");
		}
    }
}
