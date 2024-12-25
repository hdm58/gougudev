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

class Index extends BaseController
{
    //上传文件
    public function upload()
    {
        $param = get_params();
        if (request()->file('file')) {
            $file = request()->file('file');
        } else {
            return to_assign(1, '没有选择上传文件');
        }
        // dump($file);die;
        // 获取上传文件的hash散列值
        $sha1 = $file->hash('sha1');
        $md5 = $file->hash('md5');
        $rule = [
            'image' => 'jpg,png,jpeg,gif',
            'doc' => 'txt,doc,docx,ppt,pptx,xls,xlsx,pdf',
            'file' => 'zip,gz,7z,rar,tar',
            'video' => 'mpg,mp4,mpeg,avi,wmv,mov,flv,m4v',
        ];
        $fileExt = $rule['image'] . ',' . $rule['doc'] . ',' . $rule['file'] . ',' . $rule['video'];
        //1M=1024*1024=1048576字节
        $fileSize = 100 * 1024 * 1024;
        if (isset($param['type']) && $param['type']) {
            $fileExt = $rule[$param['type']];
        }
        if (isset($param['size']) && $param['size']) {
            $fileSize = $param['size'];
        }
        $validate = \think\facade\Validate::rule([
            'image' => 'require|fileSize:' . $fileSize . '|fileExt:' . $fileExt,
        ]);
        $file_check['image'] = $file;
        if (!$validate->check($file_check)) {
            return to_assign(1, $validate->getError());
        }
        // 日期前綴
        $dataPath = date('Ym');
        $use = 'thumb';
        $filename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file, function () use ($md5) {
            return $md5;
        });
        if ($filename) {
            //写入到附件表
            $data = [];
            $path = get_config('filesystem.disks.public.url');
            $data['filepath'] = $path . '/' . $filename;
            $data['name'] = $file->getOriginalName();
            $data['mimetype'] = $file->getOriginalMime();
            $data['fileext'] = $file->extension();
            $data['filesize'] = $file->getSize();
            $data['filename'] = $filename;
            $data['sha1'] = $sha1;
            $data['md5'] = $md5;
            $data['module'] = \think\facade\App::initialize()->http->getName();
            $data['action'] = app('request')->action();
            $data['uploadip'] = app('request')->ip();
            $data['create_time'] = time();
            $data['user_id'] = $this->uid;
            if ($data['module'] = 'admin') {
                //通过后台上传的文件直接审核通过
                $data['status'] = 1;
                $data['admin_id'] = $data['user_id'];
                $data['audit_time'] = time();
            }
            $data['use'] = request()->has('use') ? request()->param('use') : $use; //附件用处
            $res['id'] = Db::name('file')->insertGetId($data);
            $res['filepath'] = $data['filepath'];
            $res['name'] = $data['name'];
            $res['filename'] = $data['filename'];
            $res['filesize'] = $data['filesize'];
            add_log('upload', $data['user_id'], $data,[],'文件');
            return to_assign(0, '上传成功', $res);
        } else {
            return to_assign(1, '上传失败，请重试');
        }
    }
    //编辑器图片上传
    public function md_upload()
    {
        $param = get_params();
        if (request()->file('editormd-image-file')) {
            $file = request()->file('editormd-image-file');
        } else {
            return to_assign(1, '没有选择上传文件');
        }
        // dump($file);die;
        // 获取上传文件的hash散列值
        $sha1 = $file->hash('sha1');
        $md5 = $file->hash('md5');
        $rule = [
            'image' => 'jpg,png,jpeg,gif',
            'doc' => 'doc,docx,ppt,pptx,xls,xlsx,pdf',
            'file' => 'zip,gz,7z,rar,tar',
        ];
        $fileExt = $rule['image'] . ',' . $rule['doc'] . ',' . $rule['file'];
        //1M=1024*1024=1048576字节
        $fileSize = 2 * 1024 * 1024;
        if (isset($param['type']) && $param['type']) {
            $fileExt = $rule[$param['type']];
        }
        if (isset($param['size']) && $param['size']) {
            $fileSize = $param['size'];
        }
        $validate = \think\facade\Validate::rule([
            'image' => 'require|fileSize:' . $fileSize . '|fileExt:' . $fileExt,
        ]);
        $file_check['image'] = $file;
        if (!$validate->check($file_check)) {
            return to_assign(1, $validate->getError());
        }
        // 日期前綴
        $dataPath = date('Ym');
        $use = 'thumb';
        $filename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file, function () use ($md5) {
            return $md5;
        });
        if ($filename) {
            //写入到附件表
            $data = [];
            $path = get_config('filesystem.disks.public.url');
            $data['filepath'] = $path . '/' . $filename;
            $data['name'] = $file->getOriginalName();
            $data['mimetype'] = $file->getOriginalMime();
            $data['fileext'] = $file->extension();
            $data['filesize'] = $file->getSize();
            $data['filename'] = $filename;
            $data['sha1'] = $sha1;
            $data['md5'] = $md5;
            return json(['success' => 1, 'message' => '上传成功', 'url' => $data['filepath']]);
        } else {
            return json(['success' => 0, 'message' => '上传失败', 'url' => '']);
        }
    }
    //清空缓存
    public function cache_clear()
    {
        \think\facade\Cache::clear();
        return to_assign(0, '系统缓存已清空');
    }

    //获取部门树形节点列表
    public function get_department_tree()
    {
        $department = get_department();
        $list = get_tree($department, 0, 2);
        $data['trees'] = $list;
        return json($data);
    }

    //获取部门树形节点列表2
    public function get_department_select()
    {
        $keyword = get_params('keyword');
        $selected = [];
        if (!empty($keyword)) {
            $selected = explode(",", $keyword);
        }
        $department = get_department();
        $list = get_select_tree($department, 0, 0, $selected);
        return to_assign(0, '', $list);
    }

    //获取子部门所有员工
    public function get_employee($did = 0)
    {
        $did = get_params('did');
        /*
        if ($did == 1) {
            $department = $did;
        } else {
            $department = get_department_son($did);
        }
        */
        $department = get_department_son($did);
        $employee = Db::name('admin')
            ->field('a.id,a.did,a.position_id,a.mobile,a.name,a.nickname,a.sex,a.status,a.thumb,a.username,d.title as department')
            ->alias('a')
            ->join('Department d', 'a.did = d.id')
            ->where(['a.status' => 1])
            ->where('a.did', "in", $department)
            ->select();
        return to_assign(0, '', $employee);
    }

    //获取部门所有员工
    public function get_employee_select()
    {
        $employee = Db::name('admin')->field('id as value,name')->where(['status' => 1])->select();
        return to_assign(0, '', $employee);
    }

    //获取角色列表
    public function get_position()
    {
        $position = Db::name('Position')->field('id,title as name')->where([['status', '=', 1], ['id', '>', 1]])->select();
        return to_assign(0, '', $position);
    }

    //获取工作类型列表
    public function get_work()
    {
        $cate = Db::name('WorkCate')->field('id,title')->where([['status', '=', 1]])->select();
        return to_assign(0, '', $cate);
    }
	
    //获取任务类型列表
    public function get_task_cate()
    {
        $cate = Db::name('TaskCate')->field('id,title')->where([['status', '=', 1]])->select();
        return to_assign(0, '', $cate);
    }

    //获取产品列表
    public function get_product()
    {
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
		$product = Db::name('Product')->field('id,name as title')
			->where(function ($query) use ($whereOr) {
					if (!empty($whereOr))
						$query->whereOr($whereOr);
					})
			->where('delete_time', 0)->select();
        return to_assign(0, '', $product);
    }

    //获取项目列表
    public function get_project($pid = 0)
    {
        $where = [];
        $where[] = ['delete_time', '=', 0];
        if ($pid > 0) {
            $where[] = ['product_id', '=', $pid];
        }
		if(is_super($this->uid) == 0){
			$project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
			$where[]=['id', 'in', $project_ids];
		}
		$project = Db::name('Project')->field('id,name as title')->where($where)->select();
        return to_assign(0, '', $project);
    }

    //文档列表
    public function get_doc_list($kid = 0, $tree = 0)
    {
        if ($tree == 2) {
            $list = Db::name('knowledgeDoc')->where(['knowledge_id' => $kid, 'delete_time' => 0])
                ->field('id,pid as pId,title as name,type,link,knowledge_id,sort,read')
                ->order('sort asc,id asc')
                ->select();
            return to_assign(0, '', $list);
        } else {
            $list = Db::name('knowledgeDoc')->where(['knowledge_id' => $kid, 'delete_time' => 0])
                ->field('id,pid,title,type,knowledge_id,sort,read')
                ->order('sort asc,id asc')
                ->select();
            if ($tree == 1) {
                foreach ($list as $k => &$v) {
                    $v['title'] = sub_str($v['title'], 9);
                }
                $tree = get_tree($list, 0, 4);
                $data['trees'] = $tree;
                return json($data);
            } else {
                return to_assign(0, '', $list);
            }
        }
    }

    //删除消息附件
    public function del_message_interfix()
    {
        $id = get_params("id");
        $detail = Db::name('MessageFileInterfix')->where('id', $id)->find();
        if ($detail['admin_id'] == $this->uid) {
            if (Db::name('MessageFileInterfix')->where('id', $id)->delete() !== false) {
                $data = Db::name('MessageFileInterfix')->where('mid', $detail['mid'])->column('file_id');
                return to_assign(0, "删除成功", $data);
            } else {
                return to_assign(1, "删除失败");
            }
        } else {
            return to_assign(1, "您没权限删除该消息附件");
        }

    }

    // 测试邮件发送
    public function email_test()
    {
        $sender = get_params('email');
        //检查是否邮箱格式
        if (!is_email($sender)) {
            return to_assign(1, '测试邮箱码格式有误');
        }
        $email_config = \think\facade\Db::name('config')->where('name', 'email')->find();
        $config = unserialize($email_config['content']);
        $content = $config['template'];
        //所有项目必须填写
        if (empty($config['smtp']) || empty($config['smtp_port']) || empty($config['smtp_user']) || empty($config['smtp_pwd'])) {
            return to_assign(1, '请完善邮件配置信息！');
        }

        $send = send_email($sender, '测试邮件', $content);
        if ($send) {
            return to_assign(0, '邮件发送成功！');
        } else {
            return to_assign(1, '邮件发送失败！');
        }
    }
}
