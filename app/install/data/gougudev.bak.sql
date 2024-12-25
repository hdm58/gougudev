/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50644
 Source Host           : localhost:3306
 Source Schema         : house

 Target Server Type    : MySQL
 Target Server Version : 50644
 File Encoding         : 65001

 Date: 16/11/2021 15:16:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for dev_admin
-- ----------------------------
DROP TABLE IF EXISTS `dev_admin`;
CREATE TABLE `dev_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '登录用户名',
  `pwd` varchar(100) NOT NULL DEFAULT '' COMMENT '登录密码',
  `salt` varchar(100) NOT NULL DEFAULT '' COMMENT '密码盐',
  `reg_pwd` varchar(100) NOT NULL DEFAULT '' COMMENT '初始密码',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '员工姓名',
  `mobile` bigint(11) NOT NULL DEFAULT 0 COMMENT '手机号码',
  `sex` int(255) NOT NULL DEFAULT 0 COMMENT '性别1男,2女',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '昵称',
  `thumb` varchar(255) NOT NULL COMMENT '头像',
  `did` int(11) NOT NULL DEFAULT 0 COMMENT '部门id',
  `position_id` int(11) NOT NULL DEFAULT 0 COMMENT '职位id',
  `type` int(1) NOT NULL DEFAULT 1 COMMENT '员工类型：0未设置,1正式,2试用,3实习',
  `desc` text NULL COMMENT '员工个人简介',
  `entry_time` int(11) NOT NULL DEFAULT 0 COMMENT '员工入职日期',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '注册时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新信息时间',
  `last_login_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `login_num` int(11) NOT NULL DEFAULT 0 COMMENT '登录次数',
  `last_login_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除,0禁止登录,1正常,2离职',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工表';

-- ----------------------------
-- Table structure for dev_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `dev_admin_group`;
CREATE TABLE `dev_admin_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT 1,
  `rules` varchar(1000) NULL DEFAULT '' COMMENT '用户组拥有的规则id， 多个规则\",\"隔开',
  `desc` text NULL COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工权限分组表';

-- ----------------------------
-- Records of cms_admin_group
-- ----------------------------
INSERT INTO `dev_admin_group` VALUES (1, '超级员工权限', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100',  '超级员工权限，拥有系统的最高权限，不可修改', 0, 0);
INSERT INTO `dev_admin_group` VALUES (2, '人事总监权限', 1, '2,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,3,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,4,78,79,80,81,82,83,84,85,86,87,5,88,89,90,91,6,92,93,94,95,96,7,97,99,100', '人力资源部门领导的最高管理权限', 0, 0);

-- ----------------------------
-- Table structure for dev_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `dev_admin_log`;
CREATE TABLE `dev_admin_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `type` varchar(80) NOT NULL DEFAULT '' COMMENT '操作类型',
  `action` varchar(80) NOT NULL DEFAULT '' COMMENT '操作动作',
  `subject` varchar(80) NOT NULL DEFAULT '' COMMENT '操作主体',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '操作标题',
  `content` text NULL COMMENT '操作描述',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器',
  `function` varchar(32) NOT NULL DEFAULT '' COMMENT '方法',
  `rule_menu` varchar(255) NOT NULL DEFAULT '' COMMENT '节点权限路径',
  `ip` varchar(64) NOT NULL DEFAULT '' COMMENT '登录ip',
  `param_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作数据id',
  `param` text NULL COMMENT '参数json格式',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0删除 1正常',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工操作日志表';

-- ----------------------------
-- Table structure for dev_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `dev_admin_rule`;
CREATE TABLE `dev_admin_rule`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id',
  `src` varchar(255) NOT NULL DEFAULT '' COMMENT 'url链接',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '日志操作名称',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `menu` int(1) NOT NULL DEFAULT 0 COMMENT '是否是菜单,1是,2不是',
  `sort` int(11) NOT NULL DEFAULT 1 COMMENT '越小越靠前',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态,0禁用,1正常',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '菜单及权限表';


-- ----------------------------
-- Records of dev_admin_rule
-- ----------------------------
INSERT INTO `dev_admin_rule` VALUES (1, 0, 'admin/index/index', '系统', '系统管理', 'icon-jichupeizhi', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (2, 0, 'product/index/index', '产品', '产品管理', 'icon-xiaoshoupin', 1, 2, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (3, 0, 'project/index/index', '项目', '项目管理', 'icon-xiangmuguanli', 1, 3, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (4, 0, 'issues/index/index', '任务', '任务管理', 'icon-renwuguanli', 1, 4, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (5, 0, 'test/index/index', '测试', '测试管理', 'icon-jiaoxuejihua', 1, 5, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (6, 0, 'knowledge/index/index', '知识库', '知识库', 'icon-shujiguanli', 1, 6, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (7, 0, 'doc/index/index', '文档', '接口文档', 'icon-hetongshezhi', 1, 7, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (8, 0, 'statistics/index/index', '统计', '统计分析', 'icon-xiaoshoubaobiao', 1, 8, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (10, 1, '', '系统设置', '系统设置', 'icon-jichupeizhi', 1, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (11, 10, 'admin/conf/index', '系统配置', '系统配置', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (12, 11, 'admin/conf/add', '新建/编辑', '配置项', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (13, 11, 'admin/conf/delete', '删除', '配置项', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (14, 11, 'admin/conf/edit', '编辑', '配置详情', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (15, 10, 'admin/rule/index', '菜单节点', '菜单节点', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (16, 15, 'admin/rule/add', '新建/编辑', '菜单节点', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (17, 15, 'admin/rule/delete', '删除', '菜单节点', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (18, 10, 'admin/role/index', '权限角色', '权限角色', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (19, 18, 'admin/role/add', '新建/编辑', '权限角色', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (20, 18, 'admin/role/delete', '删除', '权限角色', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (21, 10, 'admin/log/index', '操作日志', '操作日志', '', 1, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (22, 10, 'admin/database/database', '数据安全', '备份数据', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (23, 22, 'admin/database/backup', '备份数据表', '备份数据', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (24, 22, 'admin/database/optimize', '优化数据表', '优化数据表', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (25, 22, 'admin/database/repair', '修复数据表', '修复数据表', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (26, 10, 'admin/database/backuplist', '还原数据', '还原数据', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (27, 26, 'admin/database/import', '还原数据表', '还原数据', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (28, 26, 'admin/database/downfile', '下载备份数据', '下载备份数据', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (29, 26, 'admin/database/del', '删除备份数据', '删除备份数据', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (30, 1, '', '基础数据', '基础数据', 'icon-jichushezhi', 1, 2, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (31, 30, 'admin/ncate/index', '公告类型', '公告类型', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (32, 31, 'admin/ncate/add', '新建/编辑', '公告类型', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (33, 31, 'admin/ncate/delete', '删除', '公告类型', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (34, 30, 'admin/wcate/index', '工作类型', '工作类型', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (35, 34, 'admin/wcate/add', '新建/编辑', '报销类型', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (36, 34, 'admin/wcate/check', '设置', '报销类型', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (37, 30, 'admin/kcate/index', '知识类型', '知识类型', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (38, 37, 'admin/kcate/add', '新建/编辑', '知识类型', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (39, 37, 'admin/kcate/delete', '删除', '知识类型', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (40, 1, '', '企业管理', '企业管理', 'icon-qiyeguanli', 1, 3, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (41, 40, 'admin/department/index', '部门架构', '部门', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (42, 41, 'admin/department/add', '新建/编辑', '部门', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (43, 41, 'admin/department/delete', '删除', '部门', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (44, 40, 'admin/position/index', '岗位职称', '岗位职称', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (45, 44, 'admin/position/add', '新建/编辑', '岗位职称', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (46, 44, 'admin/position/delete', '删除', '岗位职称', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (47, 44, 'admin/position/view', '查看', '岗位职称', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (48, 40, 'admin/user/index', '企业员工', '员工', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (49, 48, 'admin/user/add', '新建/编辑', '员工', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (50, 48, 'admin/user/view', '查看', '员工信息', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (51, 48, 'admin/user/set', '设置', '员工状态', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (52, 48, 'admin/user/reset', '重设密码', '员工密码', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (53, 40, 'admin/note/index', '企业公告', '公告', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (54, 53, 'admin/note/add', '新建/编辑', '公告', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (55, 53, 'admin/note/delete', '删除', '公告', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (56, 53, 'admin/note/view', '查看', '公告', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (57, 2, 'product/index/list', '列表', '产品', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (58, 57, 'product/index/add', '新建/编辑', '产品', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (59, 57, 'product/index/delete', '删除', '产品', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (60, 57, 'product/user/set', '设置', '产品状态', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (61, 57, 'product/index/view', '查看', '产品', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (62, 3, 'project/index/list', '列表', '项目', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (63, 62, 'project/index/add', '新建/编辑', '项目', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (64, 62, 'project/index/delete', '删除', '项目', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (65, 62, 'project/user/set', '设置', '项目状态', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (66, 62, 'project/index/view', '查看', '项目', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (67, 4, 'stories/index/list', '列表', '需求', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (68, 67, 'stories/index/add', '新建/编辑', '需求', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (69, 67, 'stories/index/delete', '删除', '需求', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (70, 67, 'stories/user/set', '设置', '需求', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (71, 67, 'stories/index/view', '查看', '需求', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (72, 5, 'task/index/list', '列表', '任务', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (73, 72, 'task/index/add', '新建/编辑', '任务', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (74, 72, 'task/index/delete', '删除', '任务', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (75, 72, 'task/user/set', '设置', '任务', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (76, 72, 'task/index/view', '查看', '任务', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (77, 6, 'test/index/list', '列表', '测试', '', 1, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (78, 77, 'test/index/add', '新建/编辑', '测试', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (79, 77, 'test/index/delete', '删除', '测试', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (80, 77, 'test/user/set', '设置', '测试', '', 2, 1, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (80, 77, 'test/index/view', '查看', '测试', '', 2, 1, 1, 0, 0);

INSERT INTO `dev_admin_rule` VALUES (82, 7, 'knowledge/index/index', '共享知识', '知识库', '', 1, 0, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (83, 7, 'knowledge/index/list', '个人知识', '知识库', '', 1, 0, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (84, 83, 'knowledge/index/add', '新建/编辑', '知识库', '', 2, 0, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (85, 83, 'knowledge/index/delete', '删除', '知识库', '', 2, 0, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (86, 83, 'knowledge/index/view', '详情', '知识库', '', 2, 0, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (87, 83, 'knowledge/index/doc_tree', '知识库文档列表', '知识库文档', '', 2, 0, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (88, 83, 'knowledge/index/doc_add', '新建/编辑', '知识库文档', '', 2, 0, 1, 0, 0);
INSERT INTO `dev_admin_rule` VALUES (89, 83, 'knowledge/index/doc_delete', '删除', '知识库文档', '', 2, 0, 1, 0, 0);

-- ----------------------------
-- Table structure for dev_config
-- ----------------------------
DROP TABLE IF EXISTS `dev_config`;
CREATE TABLE `dev_config`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '配置名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '配置标识',
  `content` text NULL COMMENT '配置内容',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COMMENT = '系统配置表';

-- ----------------------------
-- Records of dev_config
-- ----------------------------
INSERT INTO `dev_config` VALUES (1, '网站配置', 'web', 'a:13:{s:2:\"id\";s:1:\"1\";s:11:\"admin_title\";s:9:\"勾股DEV\";s:5:\"title\";s:9:\"勾股DEV\";s:4:\"logo\";s:52:\"/storage/202111/fc507cc8332d5ef49d9425185e4a9697.jpg\";s:4:\"file\";s:0:\"\";s:6:\"domain\";s:24:\"https://dev.gougucms.com\";s:3:\"icp\";s:23:\"粤ICP备1xxxxxx11号-1\";s:8:\"keywords\";s:9:\"勾股DEV\";s:5:\"beian\";s:29:\"粤公网安备1xxxxxx11号-1\";s:4:\"desc\";s:479:\"勾股办公是一款基于ThinkPHP6 + Layui + MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。 \";s:4:\"code\";s:0:\"\";s:9:\"copyright\";s:32:\"© 2021 gougucms.com MIT license\";s:7:\"version\";s:6:\"1.0.22\";}', 1, 1612514630, 1638010154);
INSERT INTO `dev_config` VALUES (2, '邮箱配置', 'email', 'a:8:{s:2:\"id\";s:1:\"2\";s:4:\"smtp\";s:11:\"smtp.qq.com\";s:9:\"smtp_port\";s:3:\"465\";s:9:\"smtp_user\";s:15:\"gougucms@qq.com\";s:8:\"smtp_pwd\";s:6:\"123456\";s:4:\"from\";s:24:\"勾股CMS系统管理员\";s:5:\"email\";s:18:\"admin@gougucms.com\";s:8:\"template\";s:478:\"勾股办公是一款基于ThinkPHP6 + Layui + MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。\";}', 1, 1612521657, 1637075205);
INSERT INTO `dev_config` VALUES (3, 'Api Token配置', 'token', 'a:5:{s:2:\"id\";s:1:\"3\";s:3:\"iss\";s:15:\"oa.gougucms.com\";s:3:\"aud\";s:7:\"gouguoa\";s:7:\"secrect\";s:7:\"GOUGUOA\";s:7:\"exptime\";s:4:\"3600\";}', 1, 1627313142, 1638010233);
INSERT INTO `dev_config` VALUES (4, '其他配置', 'other', 'a:3:{s:2:\"id\";s:1:\"5\";s:6:\"author\";s:15:\"勾股工作室\";s:7:\"version\";s:13:\"v1.2021.07.28\";}', 1, 1613725791, 1635953640);

-- ----------------------------
-- Table structure for dev_department
-- ----------------------------
DROP TABLE IF EXISTS `dev_department`;
CREATE TABLE `dev_department`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '部门名称',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级部门id',
  `leader_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门负责人ID',
  `phone` varchar(60) NOT NULL DEFAULT '' COMMENT '部门联系电话',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '部门组织';

-- ----------------------------
-- Records of dev_department
-- ----------------------------
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (1, '董事会', 0, 0, '13688888888');
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (2, '人事部', 1, 0, '13688888889');
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (3, '财务部', 1, 0, '13688888898');
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (4, '市场部', 1, 0, '13688888978');
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (5, '销售部', 1, 0, '13688889868');
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (6, '技术部', 1, 0, '13688898858');
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (7, '产品部', 6, 0, '13688888886');
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (8, '设计部', 6, 0, '13688888876');
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (9, '研发部', 6, 0, '13688888666');
INSERT INTO `dev_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (10, '测试部', 6, 0, '13688888666');


-- ----------------------------
-- Table structure for dev_work_cate
-- ----------------------------
DROP TABLE IF EXISTS `dev_work_cate`;
CREATE TABLE `dev_work_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '工作类型名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '工作类型';

-- ----------------------------
-- Records of dev_expense_cate
-- ----------------------------
INSERT INTO `dev_work_cate` VALUES (1, '其他', 1, 1637987189, 0);
INSERT INTO `dev_work_cate` VALUES (2, '产品原型', 1, 1637987199, 0);
INSERT INTO `dev_work_cate` VALUES (3, 'UI设计', 1, 1638088518, 0);
INSERT INTO `dev_work_cate` VALUES (4, '技术开发', 1, 1637987199, 0);
INSERT INTO `dev_work_cate` VALUES (5, '测试', 1, 1637987199, 0);
INSERT INTO `dev_work_cate` VALUES (6, '撰写文档', 1, 1637987199, 0);
INSERT INTO `dev_work_cate` VALUES (7, '需求调研', 1, 1637987199, 0);
INSERT INTO `dev_work_cate` VALUES (8, '需求沟通', 1, 1637987199, 0);
INSERT INTO `dev_work_cate` VALUES (9, '会议', 1, 1637987199, 0);

-- ----------------------------
-- Table structure for dev_file
-- ----------------------------
DROP TABLE IF EXISTS `dev_file`;
CREATE TABLE `dev_file`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(15) NOT NULL DEFAULT '' COMMENT '所属模块',
  `sha1` varchar(60) NOT NULL COMMENT 'sha1',
  `md5` varchar(60) NOT NULL COMMENT 'md5',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `filepath` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径+文件名',
  `filesize` int(10) NOT NULL DEFAULT 0 COMMENT '文件大小',
  `fileext` varchar(10) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mimetype` varchar(100) NOT NULL DEFAULT '' COMMENT '文件类型',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传会员ID',
  `uploadip` varchar(15) NOT NULL DEFAULT '' COMMENT '上传IP',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未审核1已审核-1不通过',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `admin_id` int(11) NOT NULL COMMENT '审核者id',
  `audit_time` int(11) NOT NULL DEFAULT 0 COMMENT '审核时间',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '来源模块功能',
  `use` varchar(255) NULL DEFAULT NULL COMMENT '用处',
  `download` int(11) NOT NULL DEFAULT 0 COMMENT '下载量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '文件表';

-- ----------------------------
-- Table structure for dev_message
-- ----------------------------
DROP TABLE IF EXISTS `dev_message`;
CREATE TABLE `dev_message`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '消息主题',
  `template` tinyint(2) NOT NULL DEFAULT 0 COMMENT '消息模板，用于前端拼接消息',
  `content` text NULL COMMENT '消息内容',
  `from_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送人id',
  `to_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收人id',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '阅览人类型：1 人员 2部门 3岗位 4全部',
  `type_user` text NULL COMMENT '人员ID或部门ID或角色ID，全员则为空',
  `send_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送日期',
  `read_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读时间',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源发件id',
  `fid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '转发或回复消息关联id',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1已删除消息 0垃圾消息 1正常消息',
  `is_draft` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否是草稿：1正常消息 2草稿消息',
  `delete_source` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '垃圾消息来源： 1已发消息 2草稿消息 3已收消息',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `module_name` varchar(30) NOT NULL COMMENT '模块',
  `controller_name` varchar(30) NOT NULL COMMENT '控制器',
  `action_name` varchar(30) NOT NULL COMMENT '方法',
  `action_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作模块数据的id（针对系统消息）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '消息表';

-- ----------------------------
-- Table structure for dev_message_file_interfix
-- ----------------------------
DROP TABLE IF EXISTS `dev_message_file_interfix`;
CREATE TABLE `dev_message_file_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mid` int(11) UNSIGNED NOT NULL COMMENT '消息id',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '消息关联的附件表';

-- ----------------------------
-- Table structure for dev_note
-- ----------------------------
DROP TABLE IF EXISTS `dev_note`;
CREATE TABLE `dev_note`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联分类ID',
  `title` varchar(225) NULL DEFAULT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `md_content` text NOT NULL COMMENT 'markdown内容',
  `src` varchar(100) NULL DEFAULT NULL COMMENT '关联链接',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
  `sort` int(11) NOT NULL DEFAULT 0,
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '展示开始时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '展示结束时间',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '公告';

-- ----------------------------
-- Records of dev_note
-- ----------------------------
INSERT INTO `dev_note` VALUES (1, 1, '欢迎使用勾股OA办公系统', '<p>欢迎使用勾股OA办公系统，勾股办公是一款基于ThinkPHP6 + Layui + MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。</p>', 'https://oa.gougucms.com', 1, 2, 1635696000, 1924876800, 1637984962, 1637984975);

-- ----------------------------
-- Table structure for dev_note_cate
-- ----------------------------
DROP TABLE IF EXISTS `dev_note_cate`;
CREATE TABLE `dev_note_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父类ID',
  `sort` int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '公告分类';

-- ----------------------------
-- Records of dev_note_cate
-- ----------------------------
INSERT INTO `dev_note_cate` VALUES (1, 0, 1, '普通公告', 1637984265, 1637984299);
INSERT INTO `dev_note_cate` VALUES (2, 0, 2, '紧急公告', 1637984283, 1637984310);

-- ----------------------------
-- Table structure for dev_position
-- ----------------------------
DROP TABLE IF EXISTS `dev_position`;
CREATE TABLE `dev_position`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '岗位名称',
  `work_price` int(10) NOT NULL DEFAULT 0 COMMENT '工时单价',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '岗位职称';

-- ----------------------------
-- Records of dev_position
-- ----------------------------
INSERT INTO `dev_position` VALUES (1, '超级岗位', 1000, '超级岗位，不能轻易修改权限', 1, 0, 0);
INSERT INTO `dev_position` VALUES (2, '人事总监', 1000, '人事部的最大领导', 1, 0, 0);

-- ----------------------------
-- Table structure for dev_position_group
-- ----------------------------
DROP TABLE IF EXISTS `dev_position_group`;
CREATE TABLE `dev_position_group`  (
  `pid` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '岗位id',
  `group_id` int(11) NULL DEFAULT NULL COMMENT '权限id',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  UNIQUE INDEX `pid_group_id`(`pid`, `group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '权限分组和岗位的关联表';

-- ----------------------------
-- Records of dev_position_group
-- ----------------------------
INSERT INTO `dev_position_group` VALUES (1, 1, 1635755739, 0);
INSERT INTO `dev_position_group` VALUES (2, 2, 1638007427, 0);

-- ----------------------------
-- Table structure for dev_schedule
-- ----------------------------
DROP TABLE IF EXISTS `dev_schedule`;
CREATE TABLE `dev_schedule`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '工作记录主题',
  `wid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联工作内容类型ID',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目ID',
   `sid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联需求ID',
  `tid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联任务ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建员工ID',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束时间',
  `labor_time` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '工时',
  `labor_type` int(1) NOT NULL DEFAULT 1 COMMENT '工作类型:1案头2外勤',
  `remark` text NOT NULL COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '工作记录';

-- ----------------------------
-- Table structure for dev_work
-- ----------------------------
DROP TABLE IF EXISTS `dev_work`;
CREATE TABLE `dev_work`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '类型：1 日报 2周报 3月报',
  `type_user` text NULL COMMENT '接受人员ID',
  `works` text NULL COMMENT '汇报工作内容',
  `plans` text NULL COMMENT '计划工作内容',
  `remark` text NULL COMMENT '其他事项',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '汇报工作表';

-- ----------------------------
-- Table structure for dev_work_file_interfix
-- ----------------------------
DROP TABLE IF EXISTS `dev_work_file_interfix`;
CREATE TABLE `dev_work_file_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wid` int(11) UNSIGNED NOT NULL COMMENT '汇报工作id',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '汇报工作关联的附件表';

-- ----------------------------
-- Table structure for dev_work_record
-- ----------------------------
DROP TABLE IF EXISTS `dev_work_record`;
CREATE TABLE `dev_work_record`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wid` int(11) UNSIGNED NOT NULL COMMENT '汇报工作id',
  `from_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送人id',
  `to_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收人id',
  `send_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送日期',
  `read_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '汇报工作发送记录表';

-- ----------------------------
-- Table structure for dev_knowledge_cate
-- ----------------------------
DROP TABLE IF EXISTS `dev_knowledge_cate`;
CREATE TABLE `dev_knowledge_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父类ID',
  `sort` int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类标题',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '描述',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '知识文章分类表';

-- ----------------------------
-- Records of dev_knowledge_cate
-- ----------------------------
INSERT INTO `dev_knowledge_cate` VALUES (1, 0, 0, '办公技巧', '', 1637984651, 0);
INSERT INTO `dev_knowledge_cate` VALUES (2, 0, 0, '行业技能', '', 1637984739, 0);

-- ----------------------------
-- Table structure for dev_knowledge
-- ----------------------------
DROP TABLE IF EXISTS `dev_knowledge`;
CREATE TABLE `dev_knowledge`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '作者',
  `cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '描述',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `is_share` int(1) NOT NULL DEFAULT 1 COMMENT '是否公开:1是2否',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:1正常0垃圾箱-1删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '知识库分类表';

-- ----------------------------
-- Table structure for dev_knowledge_doc
-- ----------------------------
DROP TABLE IF EXISTS `dev_knowledge_doc`;
CREATE TABLE `dev_knowledge_doc`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父章节',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `type` int(1) NOT NULL DEFAULT 1 COMMENT '类型:1文档2章节3跳转链接',
  `book_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联文档分类id',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '摘要',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '作者',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '跳转地址',
  `content` text NULL COMMENT '内容',
  `md_content` text NULL COMMENT 'markdown内容',
  `read` int(11) NOT NULL DEFAULT 0 COMMENT '阅读量',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:1正常0垃圾箱-1删除',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '知识库文档表';


-- ----------------------------
-- Table structure for dev_product
-- ----------------------------
DROP TABLE IF EXISTS `dev_product`;
CREATE TABLE `dev_product`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '产品代号',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '产品名称',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `director_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品负责人',
  `test_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '测试负责人',
  `check_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '评审人，如:1,2,3',
  `is_open` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否公开：1是,2否',
  `view_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '白名单,不公开的情况下可查看人ID，如:1,2,3',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除,0关闭,1开启',
  `content` text NULL COMMENT '产品描述',
  `md_content` text NULL COMMENT 'markdown产品描述',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '产品表';


-- ----------------------------
-- Table structure for dev_project
-- ----------------------------
DROP TABLE IF EXISTS `dev_project`;
CREATE TABLE `dev_project`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '项目代号',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '项目名称',
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联产品id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `director_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目负责人',
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目开始时间',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目结束时间',
  `team_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '团队成员，如:1,2,3',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除,0关闭,1开启,2暂停',
  `content` text NULL COMMENT '项目描述',
  `md_content` text NULL COMMENT 'markdown项目描述',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '项目表';


-- ----------------------------
-- Table structure for dev_story
-- ----------------------------
DROP TABLE IF EXISTS `dev_story`;
CREATE TABLE `dev_story`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '需求主题',
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联产品id',
  `project_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `director_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目负责人',
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目开始时间',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目结束时间',
  `note_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人员，如:1,2,3',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '需求类别:1功能2接口3性能4安全5体验6改进7其他',
  `priority` tinyint(1) NOT NULL DEFAULT 1 COMMENT '优先级:1S,2A,3B,4C,5D',
  `is_open` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否公开：1是,否',
  `view_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '白名单,不公开的情况下可查看人ID，如:1,2,3',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除,0禁用,1开启,2关闭',
  `content` text NULL COMMENT '需求描述',
  `md_content` text NULL COMMENT 'markdown需求描述',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '需求表';