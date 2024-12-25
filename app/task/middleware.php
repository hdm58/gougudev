<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

// 这是系统自动生成的middleware定义文件
return [
    //开启session中间件
    //'think\middleware\SessionInit',
    //验证勾股DEV是否完成安装
    \app\home\middleware\Install::class,
];
