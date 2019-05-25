/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : 1041rizhi_system

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 06/05/2019 17:09:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for hui_admin
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin`;
CREATE TABLE `hui_admin`  (
  `adminid` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `adminname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `roleid` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `realname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `nickname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `logintime` int(10) UNSIGNED NULL DEFAULT 0,
  `loginip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `addtime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `addpeople` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `status` int(1) NOT NULL COMMENT '状态',
  PRIMARY KEY (`adminid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for hui_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin_log`;
CREATE TABLE `hui_admin_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `action` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `querystring` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `adminid` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `adminname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `logtime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `logtime`(`logtime`) USING BTREE,
  INDEX `adminid`(`adminid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for hui_admin_login_log
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin_login_log`;
CREATE TABLE `hui_admin_login_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `adminname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `logintime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `loginip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `address` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `password` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `loginresult` tinyint(1) NOT NULL DEFAULT 0 COMMENT '登录结果1为登录成功0为登录失败',
  `cause` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for hui_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin_role`;
CREATE TABLE `hui_admin_role`  (
  `roleid` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rolename` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `system` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `disabled` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`roleid`) USING BTREE,
  INDEX `disabled`(`disabled`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_admin_role
-- ----------------------------
INSERT INTO `hui_admin_role` VALUES (1, '超级管理员', '（超级管理员） ', 0, 0);
INSERT INTO `hui_admin_role` VALUES (2, '普通管理', '（普通管理）', 0, 0);

-- ----------------------------
-- Table structure for hui_admin_role_priv
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin_role_priv`;
CREATE TABLE `hui_admin_role_priv`  (
  `roleid` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `m` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `c` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `a` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `data` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `authid` int(6) NULL DEFAULT NULL,
  INDEX `roleid`(`roleid`, `m`, `c`, `a`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of hui_admin_role_priv
-- ----------------------------
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'database', 'public_optimize', '', 27);
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'database', 'public_repair', '', 26);
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'database', 'export_list', '', 31);
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'database', 'init', '', 24);
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'system_manage', 'init', '', 18);
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'admin_manage', 'public_edit_pwd', '', 16);
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'admin_manage', 'public_edit_info', '', 15);
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'role', 'init', '', 9);
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'admin_manage', 'init', '', 8);
INSERT INTO `hui_admin_role_priv` VALUES (2, 'admin', 'database', 'databack_list', '', 25);

-- ----------------------------
-- Table structure for hui_config
-- ----------------------------
DROP TABLE IF EXISTS `hui_config`;
CREATE TABLE `hui_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '配置类型',
  `title` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置说明',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '配置值',
  `fieldtype` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '字段类型',
  `setting` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '字段设置',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  INDEX `type`(`type`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_config
-- ----------------------------
INSERT INTO `hui_config` VALUES (1, 'site_name', 1, '站点名称', 'HuiCmf - 演示站', '', '', 1);
INSERT INTO `hui_config` VALUES (2, 'site_url', 1, '站点跟网址', 'http://localhost:1041/', '', '', 1);
INSERT INTO `hui_config` VALUES (3, 'admin_log', 3, '启用后台管理操作日志', '1', '', '', 1);
INSERT INTO `hui_config` VALUES (4, 'site_keyword', 1, '站点关键字', '错误日志', '', '', 1);
INSERT INTO `hui_config` VALUES (5, 'site_copyright', 1, '网站版权信息', 'Powered By HuiCMF内容管理系统 © 2018-2019 小灰灰工作室', '', '', 1);
INSERT INTO `hui_config` VALUES (6, 'site_beian', 1, '站点备案号', '京ICP备666666号', '', '', 1);
INSERT INTO `hui_config` VALUES (7, 'site_description', 1, '站点描述', '我是描述', '', '', 1);
INSERT INTO `hui_config` VALUES (8, 'site_code', 1, '统计代码', '', '', '', 1);
INSERT INTO `hui_config` VALUES (9, 'admin_prohibit_ip', 3, '禁止登录后台的IP', '', '', '', 1);
INSERT INTO `hui_config` VALUES (10, 'mail_server', 4, 'SMTP服务器', 'smtp.qq.com', '', '', 1);
INSERT INTO `hui_config` VALUES (11, 'mail_port', 4, 'SMTP服务器端口', '25', '', '', 1);
INSERT INTO `hui_config` VALUES (12, 'mail_from', 4, 'SMTP服务器的用户邮箱', '', '', '', 1);
INSERT INTO `hui_config` VALUES (13, 'mail_user', 4, 'SMTP服务器的用户帐号', '', '', '', 1);
INSERT INTO `hui_config` VALUES (14, 'mail_pass', 4, 'SMTP服务器的用户密码', '', '', '', 1);
INSERT INTO `hui_config` VALUES (15, 'mail_inbox', 4, '收件邮箱地址', '', '', '', 1);
INSERT INTO `hui_config` VALUES (16, 'mail_auth', 4, 'AUTH LOGIN验证', '1', '', '', 1);
INSERT INTO `hui_config` VALUES (17, 'login_code', 3, '是否启用后台登录验证码', '1')

-- ----------------------------
-- Table structure for hui_menu
-- ----------------------------
DROP TABLE IF EXISTS `hui_menu`;
CREATE TABLE `hui_menu`  (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `parentid` smallint(6) NOT NULL DEFAULT 0,
  `m` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `c` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `a` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `data` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `listorder` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `display` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `listorder`(`listorder`) USING BTREE,
  INDEX `parentid`(`parentid`) USING BTREE,
  INDEX `module`(`m`, `c`, `a`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 34 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of hui_menu
-- ----------------------------
INSERT INTO `hui_menu` VALUES (1, '内容管理', 0, '', '', '', 'icon-read', 1, 1);
INSERT INTO `hui_menu` VALUES (2, '管理员管理', 0, '', '', '', 'icon-profile', 2, 1);
INSERT INTO `hui_menu` VALUES (3, '会员管理', 0, '', '', '', 'icon-friend', 3, 0);
INSERT INTO `hui_menu` VALUES (4, '模块管理', 0, '', '', '', 'icon-apps', 4, 0);
INSERT INTO `hui_menu` VALUES (5, '个人信息', 0, '', '', '', 'icon-album', 5, 1);
INSERT INTO `hui_menu` VALUES (6, '系统管理', 0, '', '', '', ' icon-settings', 6, 1);
INSERT INTO `hui_menu` VALUES (7, '数据管理', 0, '', '', '', 'icon-lightauto', 7, 1);
INSERT INTO `hui_menu` VALUES (8, '管理员列表', 2, 'admin', 'admin_manage', 'init', '', 1, 1);
INSERT INTO `hui_menu` VALUES (9, '角色管理', 2, 'admin', 'role', 'init', '', 2, 1);
INSERT INTO `hui_menu` VALUES (10, '添加管理员', 8, 'admin', 'admin_manage', 'add', '', 1, 0);
INSERT INTO `hui_menu` VALUES (11, '编辑管理员', 8, 'admin', 'admin_manage', 'edit', '', 2, 0);
INSERT INTO `hui_menu` VALUES (12, '添加角色', 9, 'admin', 'role', 'add', '', 1, 0);
INSERT INTO `hui_menu` VALUES (13, '编辑角色', 9, 'admin', 'role', 'edit', '', 2, 0);
INSERT INTO `hui_menu` VALUES (14, '权限管理', 9, 'admin', 'role', 'role_priv', '', 0, 0);
INSERT INTO `hui_menu` VALUES (15, '修改个人信息', 5, 'admin', 'admin_manage', 'public_edit_info', '', 1, 1);
INSERT INTO `hui_menu` VALUES (16, '修改密码', 5, 'admin', 'admin_manage', 'public_edit_pwd', '', 2, 1);
INSERT INTO `hui_menu` VALUES (18, '系统设置', 6, 'admin', 'system_manage', 'init', '', 0, 1);
INSERT INTO `hui_menu` VALUES (19, '后台菜单管理', 6, 'admin', 'menu', 'init', '', 2, 1);
INSERT INTO `hui_menu` VALUES (20, '菜单排序', 19, 'admin', 'menu', 'order', '', 0, 0);
INSERT INTO `hui_menu` VALUES (21, '添加菜单', 19, 'admin', 'menu', 'add', '', 1, 0);
INSERT INTO `hui_menu` VALUES (22, '编辑菜单', 19, 'admin', 'menu', 'edit', '', 2, 0);
INSERT INTO `hui_menu` VALUES (23, '删除菜单', 19, 'admin', 'menu', 'delete', '', 3, 0);
INSERT INTO `hui_menu` VALUES (24, '数据备份', 7, 'admin', 'database', 'init', '', 1, 1);
INSERT INTO `hui_menu` VALUES (25, '数据还原', 7, 'admin', 'database', 'databack_list', '', 2, 1);
INSERT INTO `hui_menu` VALUES (26, '修复表', 24, 'admin', 'database', 'public_repair', '', 1, 0);
INSERT INTO `hui_menu` VALUES (27, '优化表', 24, 'admin', 'database', 'public_optimize', '', 2, 0);
INSERT INTO `hui_menu` VALUES (28, '数据导入', 25, 'admin', 'database', 'import', '', 1, 0);
INSERT INTO `hui_menu` VALUES (29, '备份文件下载', 25, 'admin', 'database', 'databack_down', '', 2, 0);
INSERT INTO `hui_menu` VALUES (30, '备份文件删除', 25, 'admin', 'database', 'databack_del', '', 3, 0);
INSERT INTO `hui_menu` VALUES (31, '立即备份', 24, 'admin', 'database', 'export_list', '', 0, 0);
INSERT INTO `hui_menu` VALUES (32, ' 后台操作日志', 6, 'admin', 'admin_log', 'init', '', 11, 1);
INSERT INTO `hui_menu` VALUES (33, '后台登录日志', 6, 'admin', 'admin_log', 'admin_login_log_list', '', 12, 1);
INSERT INTO `hui_menu` VALUES (34, '自定义配置', 6, 'admin', 'system_manage', 'user_config_list', '', 2, 1);
INSERT INTO `hui_menu` VALUES (35, '添加配置', 34, 'admin', 'system_manage', 'user_config_add', '', 1, 0);
INSERT INTO `hui_menu` VALUES (36, '编辑配置', 34, 'admin', 'system_manage', 'user_config_edit', '', 2, 0);
INSERT INTO `hui_menu` VALUES (37, '删除配置', 34, 'admin', 'system_manage', 'user_config_del', '', 3, 0);
SET FOREIGN_KEY_CHECKS = 1;
