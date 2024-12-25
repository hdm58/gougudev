layui.define(['tool'], function (exports) {
	const layer = layui.layer, tool = layui.tool,form=layui.form, table=layui.table,tree = layui.tree;
	const canceldata= {department:"",did:0,id:0,mobile:0,name:"",nickname:"",position_id:0,sex:0,status:0,thumb:"",username:""};
	let select_ids=[];select_names=[];select_array=[];
	const obj = {
		employeeRender:function(){
			var me=this,letterTem='';
			for(var i=0;i<26;i++){
				letterTem+='<span class="layui-letter-span" data-code="'+String.fromCharCode(97+i)+'">'+String.fromCharCode(65+i)+'</span>';
			}
			var tpl='<div style="width:210px; height:388px; border-right:1px solid #eee; overflow-x: hidden; overflow-y: auto; float:left;">\
							<div id="employeeDepament" style="padding:6px 0;"></div>\
						</div>\
						<div style="width:588px; height:388px; user-select:none; overflow-x: hidden; overflow-y: auto; float:left;">\
							<div style="padding:12px 10px 0;"><div style="color:#999; text-align:center;">⇐ 点击左边部门筛选员工，或者点击下面字母筛选</div><div id="letterBar" style="color:#999; text-align:center;">'+letterTem+'</div></div>\
							<div id="employee" style="padding:6px 12px"></div>\
							<div style="padding:10px 15px; border-top:1px solid #eee;;"><strong>已选择</strong><span class="layui-tags-all">全选</span></div>\
							<div id="selectTags" style="padding:10px 15px;">'+me.employeeSelect(0)+'</div>\
						</div>';
			return tpl;
		},
		employeeSelect:function(t){
			var me=this,select_tags='';
			if(me.settings.type == 0){
				select_tags+='<span style="color:#1E9FFF">'+me.settings.names+'</span>';
			}
			else{
				select_ids=[];
				select_names=[];
				for(var a=0;a<select_array.length;a++){
					if(me.settings.fixedid==select_array[a].id && me.settings.fixedid!=0){
						select_tags+='<span class="layui-tags-span">'+select_array[a].name+'</span>';
					}
					else{
						select_tags+='<span class="layui-tags-span">'+select_array[a].name+'<i data-id="'+select_array[a].id+'" class="layui-icon layui-tags-close">ဆ</i></span>';
					}
					if(t==1){
						$('#employee').find('[data-id="'+select_array[a].id+'"]').addClass('on');
					}
					select_ids.push(parseInt(select_array[a].id));
					select_names.push(select_array[a].name);
				}
			}
			console.log(select_ids);
			return select_tags;
		},
		employeeInit: function (options) {
			const opts={
				"title":"选择员工",
				"department_url": "/api/index/get_department_tree",
				"employee_url": "/api/index/get_employee",
				"type":0,//0单人,1多人
				"fixedid":0,
				"ids":"",
				"names":"",
				"ajax_data":[],
				"callback": function(){}
			};
			this.settings = $.extend({}, opts, options);
			var me=this;
			select_ids=[];
			select_names=[];
			select_array=[];
			if(me.settings.ids!='' && me.settings.names!=''){
				select_ids=me.settings.ids.split(',');
				select_names=me.settings.names.split(',');
				for(var m=0;m<select_ids.length;m++){
					select_array.push({id:select_ids[m],name:select_names[m]});
				}
			}
			layer.open({
				type:1,
				title:me.settings.title,
				area:['800px','500px'],
				resize:false,
				content:me.employeeRender(),
				success:function(obj,idx){
						var dataList=[],letterBar=$('#letterBar'),employee = $('#employee'),selectTags = $('#selectTags');
						$.ajax({
							url:me.settings.department_url,
							type:'get',
							success:function(res){			
								//仅节点左侧图标控制收缩
								tree.render({
									elem: '#employeeDepament',
									data: res.trees,
									onlyIconControl: true,  //是否仅允许节点左侧图标控制展开收缩
									click: function(obj){
										var tagsItem='<div style="color:#999; text-align:center;">暂无员工</div>';
										$("#employeeDepament").find('.layui-tree-main').removeClass('on');
										$(obj.elem).find('.layui-tree-main').eq(0).addClass('on');
										letterBar.find('span').removeClass('on');
										$.ajax({
											url:me.settings.employee_url,
											type:'get',
											data:{did:obj.data.id},
											success:function(res){
												me.ajax_data = res.data;
												dataList=me.ajax_data;
												if(dataList.length>2 && me.settings.type == 1){
													$('.layui-tags-all').show();
												}
												else{
													$('.layui-tags-all').hide();
												}
												if(dataList.length>0){
													tagsItem='';
													for(var i=0; i<dataList.length; i++){
														if(select_ids.indexOf(dataList[i].id) == -1){
															tagsItem+='<span class="layui-tags-span" data-idx="'+i+'" data-id="'+dataList[i].id+'">'+dataList[i].name+'</span>';
														}
														else{
															tagsItem+='<span class="layui-tags-span on" data-idx="'+i+'" data-id="'+dataList[i].id+'">'+dataList[i].name+'</span>';
														}
													}
												}
												employee.html(tagsItem);
											}
										})
									}
								});	
								
								letterBar.on("click" ,'span',function(){
									var code=$(this).data('code');
									$(this).addClass('on').siblings().removeClass('on');
									$.ajax({
										url:me.settings.employee_url,
										type:'get',
										data:{id:1},
										success:function(res){	
											me.ajax_data = res.data;
											var letterData=[],tagsItem='<div style="color:#999; text-align:center;">暂无员工</div>';;
											if(me.ajax_data.length>0){
												var tagsItemCode='';
												for(var i=0; i<me.ajax_data.length; i++){
													if(me.ajax_data[i].username.slice(0,1)==code){
														if(select_ids.indexOf(me.ajax_data[i].id) == -1){
															tagsItemCode+='<span class="layui-tags-span" data-idx="'+i+'" data-id="'+me.ajax_data[i].id+'">'+me.ajax_data[i].name+'</span>';
														}
														else{
															tagsItemCode+='<span class="layui-tags-span on" data-idx="'+i+'" data-id="'+me.ajax_data[i].id+'">'+me.ajax_data[i].name+'</span>';
														}
														letterData.push(me.ajax_data[i]);
													}
												}
												dataList=letterData;
												if(dataList.length>2 && me.settings.type == 1){
													$('.layui-tags-all').show();
												}
												else{
													$('.layui-tags-all').hide();
												}
												if(tagsItemCode!=''){
													tagsItem = tagsItemCode;
												}
											}
											employee.html(tagsItem);
										}
									})
								});
								
							}
						})
						
						if(me.settings.type == 1){
							$('.layui-tags-all').on('click',function(){
								for(var a=0; a<dataList.length;a++){
									if(select_ids.indexOf(dataList[a]['id']) == -1){
										select_array.push(dataList[a]);;
									}									
								}
								selectTags.html(me.employeeSelect(1));	
							});
						}					
						
						employee.on('click','.layui-tags-span',function(){
							let item_idx=$(this).data('idx');
							let select_item = me.ajax_data[item_idx];
							if(me.settings.type == 0){
								layer.close(idx);
								console.log(select_item);
								me.settings.callback(select_item);
							}
							else{
								if(select_ids.indexOf(select_item['id']) == -1){
									select_array.push(select_item);
									selectTags.html(me.employeeSelect(1));	
								}
							}					
						});
						
						selectTags.on('click','.layui-tags-close',function(){
							let id=$(this).data('id');
							let new_slected=[];
							$('#employee').find('[data-id="'+id+'"]').removeClass('on');
							for(var i=0;i<select_array.length;i++){
								if(select_array[i].id!=id){
									new_slected.push(select_array[i]);
								}
							}
							select_array=new_slected;
							selectTags.html(me.employeeSelect(1));
						});
						if(me.settings.type == 0){
							$('#layui-layer'+idx).find('.layui-layer-btn0').hide();
						}						
					},
					btn: ['确定添加', '清空已选'],
					btnAlign:'c',
					btn1: function(idx,obj){
						me.settings.callback(select_array);
						layer.close(idx);
					},
					btn2: function(idx,obj){
						if(me.settings.type == 0){
							me.settings.callback(canceldata);
						}
						else{
							me.settings.callback([canceldata]);
						}
						layer.close(idx);
					}
			})	
		}		
	}
	layui.link(rootPath+'module/dtree/dtree.css');
	layui.link(rootPath+'module/dtree/font/dtreefont.css');
	//选择员工单人弹窗	
	$('body').on('click','.picker-one',function () {
		let that = $(this);
		let ids=that.next().val()+'',names = that.val()+'';
		obj.employeeInit({
			ids:ids,
			names:names,
			type:0,
			callback:function(seleted){
				that.val(seleted.name);
				that.next().val(seleted.id);
			}
		});
	});
	
	//选择员工多人人弹窗		
	$('body').on('click','.picker-more',function () {
		let that = $(this);
		let ids=that.next().val()+'',names = that.val()+'';
		obj.employeeInit({
			ids:ids,
			names:names,
			type:1,
			callback:function(seleted){
				let select_id=[],select_name=[];
				for(var a=0; a<seleted.length;a++){
					select_id.push(seleted[a].id);
					select_name.push(seleted[a].name);
				}
				that.val(select_name.join(','));
				that.next().val(select_id.join(','));
			}
		});
	});
	//输出接口
	exports('employeepicker', obj);
});