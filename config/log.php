<?php

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------
return [
    // 默认日志记录通道
    'default' => env('log.channel', 'file'),
    // 日志记录级别
    'level' => [],
    // 日志类型记录的通道 ['error'=>'email',...]
    'type_channel' => [],
    // 关闭全局日志写入
    'close' => false,
    // 全局日志处理 支持闭包
    'processor' => null,

    // 日志通道列表
    'channels' => [
        'file' => [
            // 日志记录方式
            'type' => 'File',
            // 日志保存目录
            'path' => '',
            // 单文件日志写入
            'single' => false,
            // 独立日志级别
            'apart_level' => [],
            // 最大日志文件数量
            'max_files' => 0,
            // 使用JSON格式记录
            'json' => false,
            // 日志处理
            'processor' => null,
            // 关闭通道日志写入
            'close' => false,
            // 日志输出格式化
            'format' => '[%s][%s] %s',
            // 是否实时写入
            'realtime_write' => false,
        ],
        // 其它日志通道配置
    ],
    'type_action' => [
        'login' => '登录',
        'upload' => '上传',
        'down' => '下载',
        'bak' => '备份',
        'optimize' => '优化',
        'repair' => '修复',
        'reduction' => '还原',
        'import' => '导入',
        'export' => '导出',
        'add' => '创建',
        'edit' => '编辑',
        'view' => '查看',
        'save' => '保存',
        'delete' => '删除',
        'send' => '发送',
        'hang' => '挂起',
        'disable' => '禁用',
        'recover' => '恢复',
        'remove' => '移除',
        'apply' => '申请',
        'check' => '审核通过',
        'refue' => '审核拒绝',
        'back' => '撤销',
        'tovoid' => '作废',
        'reset' => '重新设置',
    ],

];
