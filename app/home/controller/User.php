<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;
use app\base\BaseController;
use app\admin\validate\AdminCheck;
use think\exception\ValidateException;
use think\facade\Session;
use think\facade\Db;
use think\facade\View;

class User extends BaseController
{
	public function setting()
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
        View::assign('admin', get_admin($this->uid));
        View::assign('config', $config);
        View::assign('TP_VERSION',\think\facade\App::version());
        return View();
    }
    //修改个人信息
    public function edit_personal()
    {
		if (request()->isAjax()) {
            $param = get_params();
            $uid = $this->uid;
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($param);
            $session_admin = get_config('app.session_admin');
            Session::set($session_admin, Db::name('Admin')->where(['id' => $uid])->find());
            return to_assign();
        }
		else{
			$admin = Db::name('Admin')->where('id',$this->uid)->find();
			$admin['department'] = Db::name('Department')->where('id',$admin['did'])->value('title');
			$admin['position'] = Db::name('Position')->where('id',$admin['position_id'])->value('title');
			return view('', [
				'admin' => $admin,
			]);
		}
    }

    //修改密码
    public function edit_password()
    {
		if (request()->isAjax()) {
            $param = get_params();
            try {
                validate(AdminCheck::class)->scene('editPwd')->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            $uid = $this->uid;
			
			$admin = Db::name('Admin')->where(['id' => $uid])->find();
			$old_psw = set_password($param['old_pwd'], $admin['salt']);
			if ($admin['pwd'] != $old_psw) {
				return to_assign(1, '旧密码错误');
			}

			$salt = set_salt(20);
			$new_pwd = set_password($param['pwd'], $salt);
			$data = [
				'reg_pwd' => '',
				'salt' => $salt,
				'pwd' => $new_pwd,
				'update_time' => time(),
			];
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($data);
            $session_admin = get_config('app.session_admin');
            Session::set($session_admin, Db::name('admin')->find($uid));
            return to_assign();
        }
		else{
			return view('', [
				'admin' => get_admin($this->uid),
			]);
		}
    }

    //保存密码修改
    public function password_submit()
    {
        if (request()->isAjax()) {
            $param = get_params();
            try {
                validate(AdminCheck::class)->scene('editpwd')->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            $admin = get_admin($this->uid);
            if (set_password($param['old_pwd'], $admin['salt']) !== $admin['pwd']) {
                return to_assign(1, '旧密码不正确!');
            }
            unset($param['username']);
            $param['salt'] = set_salt(20);
            $param['pwd'] = set_password($param['pwd'], $param['salt']);
            Db::name('Admin')->where(['id' => $admin['id'],
            ])->strict(false)->field(true)->update($param);
            $session_admin = get_config('app.session_admin');
            Session::set($session_admin, Db::name('admin')->find($admin['id']));
            return to_assign();
        }
    }
	

}
