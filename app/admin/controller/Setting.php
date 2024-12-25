<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\admin\controller;

use app\base\BaseController;
use think\facade\Db;
use think\facade\View;

class Setting extends BaseController
{
    public function index()
    {
        $install = false;
        if (file_exists(CMS_ROOT . 'app/install')) {
            $install = true;
        }
		View::assign('install', $install);
		$conf = Db::name('Config')->where('id', 1)->find();
        $config = [];
        if ($conf['content']) {
            $config = unserialize($conf['content']);
        }
        View::assign('config', $config);
        View::assign('TP_VERSION',\think\facade\App::version());
        return View();
    }

    public function errorShow()
    {
        echo '错误';
    }

}
