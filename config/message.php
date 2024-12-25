<?php

// +----------------------------------------------------------------------
// | 消息模板
// +----------------------------------------------------------------------

return [
    // 系统消息模板
    'template'  => [
        1 => [
			'title'       => '{from_user}发了一个新『公告』，请及时查看',
            'content' => '您有一个新公告：{title}。',
            'link' => '<a class="link-a" data-href="/note/index/view/id/{action_id}">查看详情</a>',
        ],
        21 => [
			'title'       => '{from_user}分配了一个新任务给你，请及时处理',
            'content' => '{from_user}在{create_time}给你分配了一个新任务『{title}』，请及时跟进处理。',
			'link' => '<a class="link-a" data-href="/task/index/detail/id/{action_id}">查看详情</a>',
        ],
        22 => [
			'title'       => '{from_user}转了一个任务给你，请及时处理',
            'content' => '{from_user}在{create_time}给你转了一个任务『{title}』，请及时跟进处理。',
			'link' => '<a class="link-a" data-href="/task/index/detail/id/{action_id}">查看详情</a>',
        ]
	]
];
