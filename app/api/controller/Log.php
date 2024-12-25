<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\model\Log as LogList;
use think\facade\Db;
use think\facade\Session;

class Log extends BaseController
{	
    //获取日志列表
    public function get_list()
    {
		$param = get_params();
		$list = new LogList();
		$content = $list->get_list($param);
		return to_assign(0, '', $content);
    }
	
	//获取日志列表
    public function log_list()
    {
		$param = get_params();
		$list = new LogList();
		$content = $list->log_list($param);
		return to_assign(0, '', $content);
    }
}
