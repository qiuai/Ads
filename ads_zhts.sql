/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50528
Source Host           : localhost:3306
Source Database       : ads_zhts

Target Server Type    : MYSQL
Target Server Version : 50528
File Encoding         : 65001

Date: 2013-11-27 18:15:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `zhts_access`
-- ----------------------------
DROP TABLE IF EXISTS `zhts_access`;
CREATE TABLE `zhts_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `pid` smallint(6) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhts_access
-- ----------------------------
INSERT INTO `zhts_access` VALUES ('2', '1', '1', '0', null);
INSERT INTO `zhts_access` VALUES ('2', '40', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('2', '30', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('3', '1', '1', '0', null);
INSERT INTO `zhts_access` VALUES ('2', '69', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('2', '50', '3', '40', null);
INSERT INTO `zhts_access` VALUES ('3', '50', '3', '40', null);
INSERT INTO `zhts_access` VALUES ('1', '50', '3', '40', null);
INSERT INTO `zhts_access` VALUES ('3', '7', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('3', '39', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('2', '39', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('2', '49', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('4', '1', '1', '0', null);
INSERT INTO `zhts_access` VALUES ('4', '2', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('4', '3', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('4', '4', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('4', '5', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('4', '6', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('4', '7', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('4', '11', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('5', '25', '1', '0', null);
INSERT INTO `zhts_access` VALUES ('5', '51', '2', '25', null);
INSERT INTO `zhts_access` VALUES ('1', '1', '1', '0', null);
INSERT INTO `zhts_access` VALUES ('1', '39', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('1', '69', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('1', '30', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('1', '40', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('1', '49', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('3', '69', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('3', '30', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('3', '40', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('1', '37', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('1', '36', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('1', '35', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('1', '34', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('1', '33', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('1', '32', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('1', '31', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('2', '32', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('2', '31', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('7', '1', '1', '0', null);
INSERT INTO `zhts_access` VALUES ('7', '30', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('7', '40', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('7', '69', '2', '1', null);
INSERT INTO `zhts_access` VALUES ('7', '50', '3', '40', null);
INSERT INTO `zhts_access` VALUES ('7', '39', '3', '30', null);
INSERT INTO `zhts_access` VALUES ('7', '49', '3', '30', null);

-- ----------------------------
-- Table structure for `zhts_form`
-- ----------------------------
DROP TABLE IF EXISTS `zhts_form`;
CREATE TABLE `zhts_form` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhts_form
-- ----------------------------

-- ----------------------------
-- Table structure for `zhts_group`
-- ----------------------------
DROP TABLE IF EXISTS `zhts_group`;
CREATE TABLE `zhts_group` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `title` varchar(50) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhts_group
-- ----------------------------
INSERT INTO `zhts_group` VALUES ('2', 'App', '应用中心', '1222841259', '0', '1', '0', '0');

-- ----------------------------
-- Table structure for `zhts_node`
-- ----------------------------
DROP TABLE IF EXISTS `zhts_node`;
CREATE TABLE `zhts_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `group_id` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhts_node
-- ----------------------------
INSERT INTO `zhts_node` VALUES ('49', 'read', '查看', '1', '', null, '30', '3', '0', '0');
INSERT INTO `zhts_node` VALUES ('40', 'Index', '默认模块', '1', '', '1', '1', '2', '0', '0');
INSERT INTO `zhts_node` VALUES ('39', 'index', '列表', '1', '', null, '30', '3', '0', '0');
INSERT INTO `zhts_node` VALUES ('37', 'resume', '恢复', '1', '', null, '30', '3', '0', '0');
INSERT INTO `zhts_node` VALUES ('36', 'forbid', '禁用', '1', '', null, '30', '3', '0', '0');
INSERT INTO `zhts_node` VALUES ('35', 'foreverdelete', '删除', '1', '', null, '30', '3', '0', '0');
INSERT INTO `zhts_node` VALUES ('34', 'update', '更新', '1', '', null, '30', '3', '0', '0');
INSERT INTO `zhts_node` VALUES ('33', 'edit', '编辑', '1', '', null, '30', '3', '0', '0');
INSERT INTO `zhts_node` VALUES ('32', 'insert', '写入', '1', '', null, '30', '3', '0', '0');
INSERT INTO `zhts_node` VALUES ('31', 'add', '新增', '1', '', null, '30', '3', '0', '0');
INSERT INTO `zhts_node` VALUES ('30', 'Public', '公共模块', '1', '', '2', '1', '2', '0', '0');
INSERT INTO `zhts_node` VALUES ('69', 'Form', '数据管理', '1', '', '1', '1', '2', '0', '2');
INSERT INTO `zhts_node` VALUES ('7', 'User', '后台用户', '1', '', '4', '1', '2', '0', '2');
INSERT INTO `zhts_node` VALUES ('6', 'Role', '角色管理', '1', '', '3', '1', '2', '0', '2');
INSERT INTO `zhts_node` VALUES ('2', 'Node', '节点管理', '1', '', '2', '1', '2', '0', '2');
INSERT INTO `zhts_node` VALUES ('1', 'App', 'Rbac后台管理', '1', '', null, '0', '1', '0', '0');
INSERT INTO `zhts_node` VALUES ('50', 'main', '空白首页', '1', '', null, '40', '3', '0', '0');

-- ----------------------------
-- Table structure for `zhts_role`
-- ----------------------------
DROP TABLE IF EXISTS `zhts_role`;
CREATE TABLE `zhts_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `ename` varchar(5) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parentId` (`pid`),
  KEY `ename` (`ename`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhts_role
-- ----------------------------
INSERT INTO `zhts_role` VALUES ('1', '领导组', '0', '1', '', '', '1208784792', '1254325558');
INSERT INTO `zhts_role` VALUES ('2', '员工组', '0', '1', '', '', '1215496283', '1254325566');
INSERT INTO `zhts_role` VALUES ('7', '演示组', '0', '1', '', null, '1254325787', '0');

-- ----------------------------
-- Table structure for `zhts_role_user`
-- ----------------------------
DROP TABLE IF EXISTS `zhts_role_user`;
CREATE TABLE `zhts_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhts_role_user
-- ----------------------------
INSERT INTO `zhts_role_user` VALUES ('4', '27');
INSERT INTO `zhts_role_user` VALUES ('4', '26');
INSERT INTO `zhts_role_user` VALUES ('4', '30');
INSERT INTO `zhts_role_user` VALUES ('5', '31');
INSERT INTO `zhts_role_user` VALUES ('3', '22');
INSERT INTO `zhts_role_user` VALUES ('3', '1');
INSERT INTO `zhts_role_user` VALUES ('1', '4');
INSERT INTO `zhts_role_user` VALUES ('2', '3');
INSERT INTO `zhts_role_user` VALUES ('7', '2');

-- ----------------------------
-- Table structure for `zhts_user`
-- ----------------------------
DROP TABLE IF EXISTS `zhts_user`;
CREATE TABLE `zhts_user` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(64) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `bind_account` varchar(50) NOT NULL,
  `last_login_time` int(11) unsigned DEFAULT '0',
  `last_login_ip` varchar(40) DEFAULT NULL,
  `login_count` mediumint(8) unsigned DEFAULT '0',
  `verify` varchar(32) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `type_id` tinyint(2) unsigned DEFAULT '0',
  `info` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhts_user
-- ----------------------------
INSERT INTO `zhts_user` VALUES ('1', 'admin', '管理员', '21232f297a57a5a743894a0e4a801fc3', '', '1385463306', '192.168.1.22', '891', '8888', 'liu21st@gmail.com', '备注信息', '1222907803', '1326266696', '1', '0', '');
INSERT INTO `zhts_user` VALUES ('2', 'demo', '演示', 'fe01ce2a7fbac8fafaed7c982a04e229', '', '1254326091', '127.0.0.1', '90', '8888', '', '', '1239783735', '1254325770', '1', '0', '');
INSERT INTO `zhts_user` VALUES ('3', 'member', '员工', 'aa08769cdcb26674c6706093503ff0a3', '', '1326266720', '127.0.0.1', '17', '', '', '', '1253514375', '1254325728', '1', '0', '');
INSERT INTO `zhts_user` VALUES ('4', 'leader', '领导', 'c444858e0aaeb727da73d2eae62321ad', '', '1254325906', '127.0.0.1', '15', '', '', '领导', '1253514575', '1254325705', '1', '0', '');
