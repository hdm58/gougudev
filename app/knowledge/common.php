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
//读取知识库分类
function knowledge_cate()
{
    $cate = Db::name('KnowledgeCate')->where(['status' => 1])->select()->toArray();
    return $cate;
}

function knowledge_doc($kid=0)
{
    $list = Db::name('KnowledgeDoc')->where(['knowledge_id' => $kid,'delete_time' => 0])->select()->toArray();
    return $list;
}