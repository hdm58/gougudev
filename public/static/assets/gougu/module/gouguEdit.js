layui.define(['employeepicker','editormd'], function (exports) {
	let layer = layui.layer;
	let table = layui.table;
	let laydate = layui.laydate;
	let form = layui.form;
	let dropdown = layui.dropdown;
	let editor = layui.editormd;
	let employeepicker = layui.employeepicker;
	//是否是对象
	function isObject(val) {
		return typeof val === 'object' && val !== null
	}
	const obj = {
		//文本
		text: function (id, name, real_txt, editPost) {
			let that = this;
			layer.open({
				type: 1,
				title: '请输入内容',
				area: ['500px', '158px'],
				content: '<div style="padding:5px;"><input class="layui-input" id="goguEditInput" value="' + real_txt + '"/></div>',
				btnAlign: 'c',
				btn: ['提交保存'],
				yes: function () {
					let newval = $("#goguEditInput").val();
					if (newval != '') {
						editPost(id, name, newval, newval);
					} else {
						layer.msg('请输入内容');
					}
				}
			})
		},
		//文本
		textarea: function (id, name, real_txt, editPost) {
			let that = this;
			layer.open({
				type: 1,
				title: '请输入内容',
				area: ['800px', '360px'],
				content: '<div style="padding:5px;"><textarea class="layui-textarea" id="goguEditTextarea" style="width: 100%; height: 240px;">' + real_txt + '</textarea></div>',
				btnAlign: 'c',
				btn: ['提交保存'],
				yes: function () {
					let newval = $("#goguEditTextarea").val();
					if (newval != '') {
						editPost(id, name, newval, newval);
					} else {
						layer.msg('请输入内容');
					}
				}
			})
		},
		//员工单选
		employee_one: function (id, name, show_txt, real_txt, editPost) {
			let that = this;
			employeepicker.employeeInit({
				ids:real_txt.toString(),
				names:show_txt,
				type:0,
				callback:function(seleted){
					//that.next().val(seleted.id);
					editPost(id, name, seleted.name, seleted.id);
				}
			});
		},
		//员工多选
		employee_more: function (id, name, show_txt, real_txt, editPost) {
			let that = this;
			employeepicker.employeeInit({
				ids:real_txt.toString(),
				names:show_txt,
				type:1,
				callback:function(seleted){
					let select_id=[],select_name=[];
					for(var a=0; a<seleted.length;a++){
						select_id.push(seleted[a].id);
						select_name.push(seleted[a].name);
					}
					editPost(id, name, select_name.join(','), select_id.join(','));
				}
			});
		},
		//表格单选
		select_type: function (id, name, real_val, data, editPost) {
			let that = this;
			let i = data.length;
			while (i--) {
				if (data[i].id == real_val) {
					data.splice(i, 1);
				}
			}
			if (data.length == 0) {
				layer.msg('无可选择的内容');
				return false;
			}
			layer.open({
				title: '请选择',
				type: 1,
				area: ['500px', '360px'],
				content: '<div style="padding:16px 16px 0"><div id="selectBox"></div></div>',
				success: function () {
					selectable = table.render({
						elem: '#selectBox',
						cols: [
							[{
								type: 'radio',
								title: '选择',
								width: 80
							}, {
								field: 'title',
								title: '选项'
							}]
						],
						data: data
					});
				},
				btn: ['确定'],
				btnAlign: 'c',
				yes: function () {
					var checkStatus = table.checkStatus(selectable.config.id);
					var data = checkStatus.data;
					if (data.length > 0) {
						editPost(id, name, data[0].title, data[0].id);
					}
					else {
						layer.msg('请选择');
					}
				}
			})
		},
		//下拉选择
		dropdown: function (id, name, real_val, data, editPost, is_cancel) {
			let that = this;
			let i = data.length;
			while (i--) {
				if (data[i].id == real_val) {
					data.splice(i, 1);
				}
			}
			if (data.length == 0) {
				layer.msg('无可关联的内容');
				return false;
			}
			if (is_cancel) {
				data.push({ id: 0,title:'取消关联', templet:function(){return '<span style="color:#FF5722">取消关联</span>';} });
			}			
			dropdown.render({
				elem: '#' + name + '_' + id
				, show: true
				, data: data
				, click: function (data, othis) {
					editPost(id, name, data.title, data.id);
				}
			});
		},
		//日期
		date: function (id, name, real_txt, editPost) {
			let that = this;
			laydate.render({
				elem: '#' + name + '_' + id
				, showBottom: false
				, show: true //直接显示
				, value: real_txt
				, done: function (value, date) {
					editPost(id, name, value, value);
				}
			});
		},
		//编辑器
		editor: function (id, name, real_txt, editPost) {
			let that = this,edit;
			layer.open({
				closeBtn: 2,
				title: false,
				type: 1,
				area: ['1080px', '580px'],
				content: '<div style="padding-right:3px"><div id="editorBox" style="margin:0 auto!important;"></div></div>',
				success: function () {
					edit = editor.render('editorBox', {
						markdown: real_txt,
						imageUploadURL: "/api/index/md_upload",
						lineNumbers: false,
						toolbarIcons: function () {
							return [
								"undo", "redo", "bold", "del", "italic", "quote", "h3",
								"list-ul", "list-ol", "hr", "link", "reference-link", "image", "code", "table", "watch"
							];
						},
						height: 520,
					});
				},
				btnAlign: 'c',
				btn: ['提交保存'],
				yes: function () {
					editPost(id, name, edit.getHTML(), edit.getMarkdown());
				}
			})
		},
		//选择任务
		taskPicker:function(callback,where){
			let map = isObject(where)?where:{};
			let taskTable;
			let taskLayer = layer.open({
				title: '选择任务',
				area: ['666px', '580px'],
				type: 1,
				content: '<div class="picker-table">\
					<form class="layui-form pb-2">\
						<div class="layui-input-inline" style="width:480px;">\
						<input type="text" name="keywords" placeholder="任务主题" class="layui-input" autocomplete="off" />\
						</div>\
						<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="task_project">提交搜索</button>\
					</form>\
					<div id="taskTable"></div></div>',
				success: function () {
					taskTable = table.render({
						elem: '#taskTable'
						, url: '/api/project/get_task'
						, page: true //开启分页
						, limit: 10
						, where:map
						, cols: [[
							{ type: 'radio', title: '选择' }
							, { field: 'id', width: 90, title: '编号', align: 'center' }
							, { field: 'title', title: '任务主题' }
							, { field: 'project_name', width: 200, title: '关联项目' }
						]]
					});
					//任务搜索提交
					form.on('submit(task_project)', function (data) {
						let maps = $.extend({}, map, data.field);
						taskTable.reload({ where: maps, page: { curr: 1 } });
						return false;
					});
				},
				btn: ['确定选择','清除数据'],
				btnAlign: 'c',
				btn1: function () {
					var checkStatus = table.checkStatus(taskTable.config.id);
					var data = checkStatus.data;
					if (data.length > 0) {
						callback(data[0]);
						layer.close(taskLayer);
					}
					else {
						layer.msg('请先选择任务');
						return false;
					}
				},
				btn2: function () {
					callback({'id':0,'title':''});
					layer.closeAll();
				}
			})
		}
	};
	exports('gouguEdit', obj);
});  