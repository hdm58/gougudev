<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
/**
======================
 *模块数据获取公共文件
======================
 */
use think\facade\Db;


//读取后台菜单列表
function admin_menu()
{
    $menu = Db::name('AdminRule')->where(['menu' => 1,'status'=>1])->order('sort asc,id asc')->select()->toArray();
    return $menu;
}

//读取权限节点列表
function admin_rule()
{
    $rule = Db::name('AdminRule')->where(['status'=>1])->order('sort asc,id asc')->select()->toArray();
    return $rule;
}

//读取权限分组列表
function admin_group()
{
    $group = Db::name('AdminGroup')->order('id desc')->select()->toArray();
    return $group;
}

//读取指定权限分组菜单详情
function admin_group_info($id)
{
    $rule = Db::name('AdminGroup')->where(['id' => $id])->value('rules');
	$rules = explode(',', $rule);
    return $rules;
}

//读取公告分类列表
function admin_note_cate()
{
    $cate = Db::name('NoteCate')->order('id desc')->select()->toArray();
    return $cate;
}

//读取公告分类子分类ids
function admin_note_cate_son($id = 0, $is_self = 1)
{
    $cate = admin_note_cate();
    $cate_list = get_data_node($cate, $id);
    $cate_array = array_column($cate_list, 'id');
    if ($is_self == 1) {
        //包括自己在内
        $cate_array[] = $id;
    }
    return $cate_array;
}

//读取知识分类分类
function admin_knowledge_cate()
{
    $cate = Db::name('KnowledgeCate')->order('id desc')->select()->toArray();
    return $cate;
}

//读取知识分类子分类ids
function admin_knowledge_cate_son($id = 0, $is_self = 1)
{
    $cate = admin_knowledge_cate();
    $cate_list = get_data_node($cate, $id);
    $cate_array = array_column($cate_list, 'id');
    if ($is_self == 1) {
        //包括自己在内
        $cate_array[] = $id;
    }
    return $cate_array;
}


