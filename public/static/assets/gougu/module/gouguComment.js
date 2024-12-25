layui.define(['tool','editormd'], function (exports) {
	const layer = layui.layer, tool = layui.tool,editor = layui.editormd;
	const obj = {
		addLink: function (id, topic_id, module, url, desc) {
			let that = this;
			layer.open({
				title: '添加链接',
				type: 1,
				area: ['580px', '240px'],
				content: '<div class="px-4 pt-4"><div class="layui-input-inline mr-3">URL</div><div class="layui-input-inline" style="width:500px;"><input type="text" id="box_url" placeholder="请输入URL" value="' + url + '" class="layui-input" autocomplete="off" /></div></div><div class="px-4 pt-4"><div class="layui-input-inline mr-3">说明 </div><div class="layui-input-inline" style="width:500px;"><input type="text" id="box_desc" placeholder="请输入链接说明" value="' + desc + '" class="layui-input" autocomplete="off" /></div></div>',
				btnAlign: 'c',
				btn: ['提交发布'],
				yes: function () {
					let callback = function (e) {
						if(e.code==0){
							layer.closeAll();
							layer.msg(e.msg);
							if(module == 'project'){
								setTimeout(function(){
									location.reload();
								},2000)	
							}
							else{
								tool.load('/' + module + '/index/view/id/' + topic_id);
							}
						}
						else{
							layer.msg(e.msg);
						}				
					}
					let url = $('#box_url').val();
					let desc = $('#box_desc').val();
					if (url == '') {
						layer.msg('请输入URL');
						return false;
					}
					if (desc == '') {
						layer.msg('请输入链接说明');
						return false;
					}
					let postData = { id: id, topic_id: topic_id, module: module, url: url, desc: desc };
					tool.post("/api/appendix/add_link", postData, callback);
				}
			})
		},
		log: function (topic_id, module) {
			let callback = function (res) {
				if (res.code == 0 && res.data.length > 0) {
					let itemLog = '';
					$.each(res.data, function (index, item) {
						if (item.field == 'content') {
							itemLog += `
							<div class="log-item py-3 border-b">
								<i class="iconfont ${item.icon}"></i>
								<span class="log-name">${item.name}</span>
								<span class="log-content font-gray"> ${item.action}了<strong>${item.title}</strong><i title="对比查看" class="iconfont icon-yuejuan" style="color:#1E9FFF; cursor: pointer;"></i> <span class="font-gray" title="${item.create_time}">${item.times}</span></span>
							</div>
						`;
						}
						else if (item.field == 'file' || item.field == 'link' || item.field == 'user') {
							itemLog += `
								<div class="log-item py-3 border-b">
									<i class="iconfont ${item.icon}"></i>
									<span class="log-name">${item.name}</span>
									<span class="log-content font-gray"> ${item.action}了${item.title}<strong>${item.new_content}</strong><span class="font-gray" title="${item.create_time}">${item.times}</span></span>
								</div>
							`;
						} else if (item.field == 'new' || item.field == 'delete') {
							itemLog += `
								<div class="log-item py-3 border-b">
									<i class="iconfont ${item.icon}"></i>
									<span class="log-name">${item.name}</span>
									<span class="log-content font-gray"> ${item.action}了<strong>${item.title}</strong><span class="font-gray" title="${item.create_time}">${item.times}</span></span>
								</div>
							`;
						}
						else if (item.field == 'document') {
							if (item.action == '修改') {
								itemLog += `
									<div class="log-item py-3 border-b">
										<i class="iconfont ${item.icon}"></i>
										<span class="log-name">${item.name}</span>
										<span class="log-content font-gray"> ${item.action}了${item.title}<strong>${item.remark}</strong><i title="对比查看" class="iconfont icon-yuejuan" style="color:#1E9FFF; cursor: pointer;"></i> <span class="font-gray" title="${item.create_time}">${item.times}</span></span>
									</div>
								`;
							}
							else {
								itemLog += `
									<div class="log-item py-3 border-b">
										<i class="iconfont ${item.icon}"></i>
										<span class="log-name">${item.name}</span>
										<span class="log-content font-gray"> ${item.action}了${item.title}<strong>${item.remark}</strong><span class="font-gray" title="${item.create_time}">${item.times}</span></span>
									</div>
								`;
							}
						}
						else {
							itemLog += `
							<div class="log-item py-3 border-b">
								<i class="iconfont ${item.icon}"></i>
								<span class="log-name">${item.name}</span>
								<span class="log-content font-gray"> 将<strong>${item.title}</strong>从 ${item.old_content} ${item.action}为<strong>${item.new_content}</strong><span class="font-gray" title="${item.create_time}">${item.times}</span></span>
							</div>
						`;
						}
					});
					$("#log_" + module + "_" + topic_id).html(itemLog);
				}
			}
			tool.get("/api/log/get_list", { tid: topic_id, m: module }, callback);
		},
		load: function (topic_id, module) {
			let callback = function (res) {
				if (res.code == 0 && res.data.length > 0) {
					let itemComment = '';
					$.each(res.data, function (index, item) {
						let pAdmin = '', ops = '';
						if (item.padmin_id > 0) {
							pAdmin = '<p class="pt-2"><span>@' + item.pname + '</span></p>';
						}
						if (item.admin_id == GOUGU_DEV.uid) {
							ops = '<a class="mr-4" data-event="edit" data-id="' + item.id + '">编辑</a><a class="mr-4" data-event="del" data-id="' + item.id + '">删除</a>';
						}
						itemComment += `
							<div id="comment_${item.id}" class="comment-item py-3 border-t" data-mdcontent="${item.md_content}">
							<div class="comment-avatar" title="${item.name}">
								<img class="comment-image" src="${item.thumb}">
							</div>
							<div class="comment-body">
								<div class="comment-meta">
									<strong class="comment-name">${item.name}</strong><span class="ml-2 font-gray" title="${item.create_time}">${item.times}${item.update_time}</span>
								</div>
								<div class="comment-content py-2">
									${item.content}${pAdmin}									
								</div>
								<div class="comment-actions">
									<a class="mr-4" data-event="replay" data-id="${item.id}" data-uid="${item.admin_id}">回复</a>${ops}
								</div>
							</div>
						</div>
						`;
					});
					$("#comment_" + module + "_" + topic_id).html(itemComment);
					layer.closeAll();
				}
			}
			tool.post("/api/comment/get_list", { tid: topic_id, m: module }, callback);
		},
		add: function (id, topic_id, pid, padmin_id, module, content, md_content) {
			let that = this;
			let callback = function (res) {
				that.load(topic_id, module);
			}
			if (content == '') {
				layer.msg('请完善评论内容');
				return false;
			}
			let postData = { id: id, topic_id: topic_id, pid: pid, padmin_id: padmin_id, module: module, content: content, md_content: md_content };
			tool.post("/api/comment/add", postData, callback);
		},
		del: function (id, topic_id, module) {
			let that = this;
			layer.confirm('确定删除该评论吗？', {
				icon: 3,
				title: '提示'
			}, function (index) {
				let callback = function (e) {
					layer.msg(e.msg);
					if (e.code == 0) {
						that.load(topic_id, module);
					}
				}
				tool.delete("/api/comment/delete", { id: id }, callback);
				layer.close(index);
			});
		},
		//编辑器
		editor: function (id, topic_id, pid, padmin_id, module, txt) {
			let that = this,edit;
			layer.open({
				closeBtn: 2,
				title: false,
				type: 1,
				area: ['720px', '360px'],
				content: '<div style="padding-right:3px"><div id="editorBox" style="margin:0 auto!important;"></div></div>',
				success: function () {
					edit = editor.render('editorBox', {
						markdown: txt,
						imageUploadURL: "/api/index/md_upload",
						lineNumbers: false,
						watch: false,
						toolbarIcons: function () {
							return [
								"undo", "redo", "bold", "del", "italic", "quote", "h3",
								"list-ul", "list-ol", "hr", "link", "reference-link", "image", "code", "table", "watch"
							];
						},
						height: 300,
					});
				},
				btnAlign: 'c',
				btn: ['提交发布'],
				yes: function () {
					that.add(id, topic_id, pid, padmin_id, module, edit.getHTML(), edit.getMarkdown());
				}
			})
		}
	};
	exports('gouguComment', obj);
});  