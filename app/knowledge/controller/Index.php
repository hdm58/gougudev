<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\knowledge\controller;

use app\base\BaseController;
use app\model\Knowledge as KnowledgeList;
use app\knowledge\validate\KnowledgeCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.title|a.keywords|a.desc|a.content|c.title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['cate_id'])) {
                $where[] = ['a.cate_id', '=', $param['cate_id']];
            }
			if ($param['share'] == 1) {
                $where[] = ['a.is_share', '=', 1];
            }
			else{
				$where[] = ['a.admin_id', '=', $this->uid];
			}
            $where[] = ['a.delete_time', '=', 0];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $content = KnowledgeList::where($where)
                ->field('a.*,c.id as cate_id,a.id as id,c.title as cate_title,a.title as title,u.name as user')
                ->alias('a')
                ->join('KnowledgeCate c', 'a.cate_id = c.id')
                ->join('admin u', 'a.admin_id = u.id','LEFT')
                ->order('a.create_time desc')
                ->paginate($rows, false, ['query' => $param])
				->each(function ($item, $key) {
					$item->views = Db::name('KnowledgeDoc')->where([['delete_time','=',0],['knowledge_id','=',$item->id]])->sum('read');
					$item->sections = Db::name('KnowledgeDoc')->where([['delete_time','=',0],['knowledge_id','=',$item->id]])->count();
				});
            return table_assign(0, '', $content);
        } else {			
			$eid = isset($param['eid']) ? $param['eid'] : 0;
			$kid = isset($param['kid']) ? $param['kid'] : 0;
			View::assign('eid', $eid);
			View::assign('kid', $kid);
            return view();
        }
    }

    public function list()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.title|a.keywords|a.desc|a.content|c.title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['cate_id'])) {
                $where[] = ['a.cate_id', '=', $param['cate_id']];
            }
            $where[] = ['a.delete_time', '=', 0];
            $where[] = ['a.is_share', '=', 1];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $content = KnowledgeList::where($where)
                ->field('a.*,c.id as cate_id,a.id as id,c.title as cate_title,a.title as title,u.name as user')
                ->alias('a')
                ->join('KnowledgeCate c', 'a.cate_id = c.id')
                ->join('admin u', 'a.admin_id = u.id','LEFT')
                ->order('a.create_time desc')
                ->paginate($rows, false, ['query' => $param]);
            return table_assign(0, '', $content);
        } else {			
			$eid = isset($param['eid']) ? $param['eid'] : 0;
			$kid = isset($param['kid']) ? $param['kid'] : 0;
			View::assign('eid', $eid);
			View::assign('kid', $kid);
            return view();
        }
    }

    //添加&&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isPost()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(KnowledgeCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = KnowledgeList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign(0,'编辑成功',$param['id']);
            } else {
                try {
                    validate(KnowledgeCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
                $param['create_time'] = time();
                $kid = KnowledgeList::strict(false)->field(true)->insertGetId($param);
                if ($kid) {
                    add_log('add', $kid, $param);
                }
                return to_assign(0,'添加成功',$kid);
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $knowledge = Db::name('Knowledge')->where(['id' => $id])->find();
                View::assign('knowledge', $knowledge);
            }
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $id = get_params("id");
        $detail = (new KnowledgeList())->detail($id);
		if($detail['admin_id']!=$this->uid){
			return to_assign(1, "您不是该知识库创建者，无权限操作");
		}
        View::assign('detail', $detail);
        return view();
    }
    //删除
    public function delete()
    {
        $id = get_params("id");
        $data['id'] = $id;
        $data['delete_time'] = time();
        if (Db::name('Knowledge')->update($data) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
	
    //文党添加&&编辑
    public function doc_add()
    {
		$param = get_params();
        if (request()->isPost()){			
			if (isset($param['table-align'])) {
				unset($param['table-align']);
			}
			if (isset($param['docContent-html-code'])) {
				$param['content'] = $param['docContent-html-code'];
				$param['md_content'] = $param['docContent-markdown-doc'];
				unset($param['docContent-html-code']);
				unset($param['docContent-markdown-doc']);
			}
            if (isset($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
                $res = Db::name('KnowledgeDoc')->strict(false)->field(true)->update($param);
                $aid = $param['id'];
                if ($res) {
                    add_log('edit', $param['id'], $param);
					return to_assign();
                }
				else {
					return to_assign(1, '操作失败');
				}
            } else {
				$param['admin_id'] = $this->uid;
                $param['create_time'] = time();
				if (isset($param['content'])) {
					$param['desc'] = getDescriptionFromContent($param['content'], 100);
				}
				$aid = Db::name('KnowledgeDoc')->strict(false)->field(true)->insertGetId($param);
				if ($aid) {
					add_log('add', $aid, $param);
					return to_assign(0,'保存成功',$aid);
				}
				else {
					return to_assign(1, '操作失败');
				}
            }
        }
		else{
			$id = empty($param['id']) ? 0 : $param['id'];
			$pid = empty($param['pid']) ? 0 : $param['pid'];
			View::assign('id', $id);
			View::assign('pid', $pid);
			if ($id > 0) {
				$detail = Db::name('KnowledgeDoc')->where(['id'=>$id])->find();
				if(!empty($detail)){
					$file_array = Db::name('File')
						->field('f.id,f.admin_id,f.name,f.filesize,f.filepath,a.name as admin_name')
						->alias('f')
						->join('Admin a', 'f.admin_id = a.id', 'LEFT')
						->order('f.create_time desc')
						->where([['f.id','in', $detail['file_ids']]])
						->select()->toArray();
					$detail['file_array'] = $file_array;
				}
				View::assign('detail', $detail);
			}
			return view();
		}

    }

    //删除文档
    public function doc_delete()
    {
        $id = get_params("id");
        $count = Db::name('KnowledgeDoc')->where(["pid" => $id,"delete_time"=>0])->count();
        if ($count > 0) {
            return to_assign(1, "该分类下还有子文档，无法删除");
        }
        $data['id'] = $id;
        $data['delete_time'] = time();
        if (Db::name('KnowledgeDoc')->update($data) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
	
	
    /*
     *文档详情
     */
    public function doc_detail()
    {
        $param = get_params();
		$kid = empty($param['kid']) ? 0 : $param['kid'];
		$id = empty($param['id']) ? 0 : $param['id'];
		if (request()->isAjax()) {
			$info = Db::name('KnowledgeDoc')->where(['id'=>$id])->find();
			if(!empty($info)){
				$file_array = Db::name('File')
					->field('f.id,f.admin_id,f.name,f.filesize,f.filepath,a.name as admin_name')
					->alias('f')
					->join('Admin a', 'f.admin_id = a.id', 'LEFT')
					->order('f.create_time desc')
					->where([['f.id','in', $info['file_ids']]])
					->select()->toArray();
				$info['file_array'] = $file_array;
			}
			Db::name('KnowledgeDoc')->where(['id'=>$id])->inc('read')->update();
			add_log('view', $id);
			return to_assign(0, '', $info);
		}
		else{
			$where = array();
			$where[] = ['knowledge_id', '=', $kid];
			$where[] = ['delete_time', '=', 0];
			$list = Db::name('KnowledgeDoc')->where($where)
				->field('id,pid,title,type,knowledge_id,link,read,sort,create_time,update_time')
				->order('sort asc,id asc')
				->select()->toArray();
			$detail = (new KnowledgeList())->detail($kid);
			$info=[];
			if($id==0 && !empty($list)){
				$id = $list[0]['id'];
			}
			$info = Db::name('KnowledgeDoc')->where(['id'=>$id])->find();
			if(!empty($info)){
				$file_array = Db::name('File')
					->field('f.id,f.admin_id,f.name,f.filesize,f.filepath,a.name as admin_name')
					->alias('f')
					->join('Admin a', 'f.admin_id = a.id', 'LEFT')
					->order('f.create_time desc')
					->where([['f.id','in', $info['file_ids']]])
					->select()->toArray();
				$info['file_array'] = $file_array;
			}
			Db::name('KnowledgeDoc')->where(['id'=>$id])->inc('read')->update();
			View::assign([
				'detail' => $detail,
				'info' => $info,
				'kid' => $kid,
				'id' => $id
			]);
			add_log('view', $id);
			return View();
		}
    }
	
}
