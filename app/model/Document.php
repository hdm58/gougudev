<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);
namespace app\model;
use think\facade\Db;
use think\Model;

class Document extends Model
{
    public function get_list($param = [])
    {
        $where = array();
        $where['a.module'] = $param['m'];
		$where['a.topic_id'] = $param['tid'];
        $where['a.delete_time'] = 0;
       // $rows = empty($param['limit']) ? get_config('app.pages') : $param['limit'];
        $content = Db::name('Document')
			->field('a.*,u.name,u.thumb')
            ->alias('a')
            ->join('Admin u', 'u.id = a.admin_id')
            ->order('a.create_time desc')
            ->where($where)
            ->select()->toArray();
        foreach ($content as $k => &$v) {
			$v['create_time'] = date('Y-m-d H:i',$v['create_time']);
			if($v['update_time']>0){
				$v['update_time'] = date('Y-m-d H:i',$v['update_time']);
			}
			else{
				$v['update_time'] = '-';
			}
        }
        return $content;
    }
	
	//详情
    public function detail($id)
    {
        $detail = Db::name('Document')->where(['id' => $id])->find();
        if (!empty($detail)) {
			$file_array = Db::name('File')
                ->field('f.id,f.admin_id,f.name,f.filesize,f.filepath,a.name as admin_name')
                ->alias('f')
                ->join('Admin a', 'f.admin_id = a.id', 'LEFT')
                ->order('f.create_time desc')
                ->where([['f.id','in', $detail['file_ids']]])
                ->select()->toArray();
			$detail['file_array'] = $file_array;
			$detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
			$detail['times'] = time_trans($detail['create_time']);
        }
        return $detail;
    }
}
