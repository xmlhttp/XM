/*
Navicat MySQL Data Transfer

Source Server         : 腾讯云
Source Server Version : 50555
Source Host           : 139.199.221.53:3306
Source Database       : XMPower

Target Server Type    : MYSQL
Target Server Version : 50555
File Encoding         : 65001

Date: 2017-12-07 10:49:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `db_down`
-- ----------------------------
DROP TABLE IF EXISTS `db_down`;
CREATE TABLE `db_down` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) DEFAULT NULL COMMENT '排序ID',
  `treeid` varchar(100) DEFAULT NULL COMMENT '结构ID',
  `newtitle` varchar(200) DEFAULT NULL COMMENT '标题',
  `upfile` varchar(100) DEFAULT '' COMMENT 'part1的bin文件',
  `upfile2` varchar(100) DEFAULT NULL COMMENT 'part2的bin',
  `newdesc` varchar(400) DEFAULT NULL COMMENT '描述',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `putout` tinyint(1) DEFAULT '1' COMMENT '是否启用',
  `isdelete` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_down
-- ----------------------------
INSERT INTO `db_down` VALUES ('1', '1', '1', 'VM-AC.001', '/Web/UploadFile/Down/file/2017-03-04/58ba73644f52f.bin', null, '通天塔', '2017-03-01 12:34:11', '1', '0');
INSERT INTO `db_down` VALUES ('2', '2', '2', 'VM-AC.002', '/Web/UploadFile/Down/file/2017-03-04/58ba77e99d27d.bin', null, '测试', '2017-03-01 16:11:50', '0', '0');
INSERT INTO `db_down` VALUES ('3', '3', '1', 'VM-AC.003', '/Web/UploadFile/Down/file/2017-08-11/598d7138e4d45.bin', null, '第一个测试版本', '2017-03-03 11:13:07', '0', '0');
INSERT INTO `db_down` VALUES ('4', '4', '1', 'test', null, null, 'test', '2017-07-17 17:19:58', '0', '0');
INSERT INTO `db_down` VALUES ('5', '5', '1', '有线设备测试1', '/Web/UploadFile/Down/file/2017-10-30/59f6fe31073fa.bin', null, '11112', '2017-10-30 18:24:27', '0', '0');
INSERT INTO `db_down` VALUES ('6', '6', '1', '有线设备测试221', '/Web/UploadFile/Down/user1/2017-10-30/59f6ff0dec29f.bin', '/Web/UploadFile/Down/user2/2017-10-30/59f6ff0ded23f.bin', '有线设备测试2有线设备测试211', '2017-10-30 18:29:13', '0', '0');
INSERT INTO `db_down` VALUES ('7', '7', '1', '测试1122', '/Web/UploadFile/Down/user1/2017-10-30/59f7001c41ddb.bin', '/Web/UploadFile/Down/user2/2017-10-30/59f7001c42d7b.bin', '测试11测试112211', '2017-10-30 18:33:47', '0', '0');

-- ----------------------------
-- Table structure for `db_orderlist`
-- ----------------------------
DROP TABLE IF EXISTS `db_orderlist`;
CREATE TABLE `db_orderlist` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `No` varchar(30) DEFAULT '' COMMENT '订单编号',
  `type` tinyint(1) DEFAULT '1' COMMENT '订单类型,1充值2退款',
  `Atit` varchar(40) DEFAULT '' COMMENT '充电标题',
  `Adesc` varchar(100) DEFAULT '' COMMENT '充电描述',
  `cuint` int(5) DEFAULT '0' COMMENT '本次单价',
  `amoney` int(8) DEFAULT '0' COMMENT '充值金额',
  `cmoney` int(8) DEFAULT '0' COMMENT '实际充值金额',
  `dmoney` int(8) DEFAULT '0' COMMENT '退款金额',
  `ctime` int(7) DEFAULT '0' COMMENT '当前充电时间秒',
  `cele` int(11) DEFAULT '0' COMMENT '当前充电度',
  `CNo` varchar(30) DEFAULT '' COMMENT '关联订单号',
  `isend` tinyint(1) DEFAULT '0' COMMENT '是否结束,0未结束 1已结束',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `stoptime` datetime DEFAULT NULL COMMENT '结束充电时间',
  `tempid` int(20) DEFAULT '0' COMMENT '充电ID',
  `pid` int(11) DEFAULT '0' COMMENT '设备ID',
  `pname` varchar(80) DEFAULT '' COMMENT '设备名称',
  `sid` int(11) DEFAULT '0' COMMENT '站点id',
  `sname` varchar(80) DEFAULT '' COMMENT '站点名称',
  `psmoney` int(12) DEFAULT '0' COMMENT '设备累计收入金额',
  `pstime` int(20) DEFAULT '0' COMMENT '设备累计充电时间秒',
  `psnum` int(9) DEFAULT '0' COMMENT '设备累计充电次数',
  `psele` int(20) DEFAULT '0' COMMENT '设备累计充电量',
  `ptmoney` int(12) DEFAULT '0' COMMENT '设备累计退款金额',
  `ptnum` int(9) DEFAULT '0' COMMENT '设备累计退款次数',
  `pmoney` int(12) DEFAULT '0' COMMENT '设备累计实际余额',
  `psunum` int(9) DEFAULT '0' COMMENT '设备累计成功充电次数',
  `pfanum` int(9) DEFAULT '0' COMMENT '设备累计失败充电次数',
  `uid` int(11) DEFAULT '0' COMMENT '充电用户ID',
  `nickname` varchar(50) DEFAULT '' COMMENT '充电时用户昵称',
  `usmoney` int(12) DEFAULT '0' COMMENT '用户累计充电金额',
  `ustime` int(20) DEFAULT '0' COMMENT '用户累计充电时间秒',
  `usnum` int(9) DEFAULT '0' COMMENT '用户累计充电次数',
  `usele` int(21) DEFAULT '0' COMMENT '用户累计充电量',
  `utmoney` int(12) DEFAULT '0' COMMENT '用户累计退款',
  `utnum` int(9) DEFAULT '0' COMMENT '用户累计退款次数',
  `umoney` int(12) DEFAULT '0' COMMENT '用户累计实际余额',
  `usunum` int(9) DEFAULT '0' COMMENT '用户累计成功充电次数',
  `ufanum` int(9) DEFAULT '0' COMMENT '用户累计失败充电次数',
  `bid` int(11) DEFAULT '0' COMMENT '商家id',
  `bname` varchar(80) DEFAULT '' COMMENT '商家名称',
  `bsmoney` int(12) DEFAULT '0' COMMENT '商家累计收入金额',
  `bstime` int(20) DEFAULT '0' COMMENT '商家累计充电时间秒',
  `bsnum` int(9) DEFAULT '0' COMMENT '商家累计充电次数',
  `bsele` int(21) DEFAULT '0' COMMENT '用户累计充电量',
  `btmoney` int(12) DEFAULT '0' COMMENT '商家退款累计金额',
  `btnum` int(9) DEFAULT '0' COMMENT '用户累计退款次数',
  `bmoney` int(12) DEFAULT '0' COMMENT '用户累计实际余额',
  `bsunum` int(9) DEFAULT '0' COMMENT '用户累计成功充电次数',
  `bfanum` int(9) DEFAULT '0' COMMENT '用户累计失败充电次数',
  `elecount` int(10) DEFAULT '0' COMMENT '开始电量',
  `eleend` int(10) DEFAULT '0' COMMENT '结束电量',
  `isclose` tinyint(1) DEFAULT '0' COMMENT '是否结束充电，和isend基本一样',
  `isenable` tinyint(4) DEFAULT '0' COMMENT '充电开始前的标识，0为平台写入，1被桩修改。。',
  `isstatus` tinyint(1) DEFAULT '0' COMMENT '标识充电状态，0正常，1过压保护，2断线续充',
  `endcode` int(4) DEFAULT '0' COMMENT '结束代号来源,也就是触发结束充电的原因',
  `endstatus` int(4) DEFAULT '0' COMMENT '触发结束失败代号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_orderlist
-- ----------------------------
INSERT INTO `db_orderlist` VALUES ('1', '123', '1', '111', '', '1', '0', '223', '0', '0', '0', '', '0', '2017-08-23 10:23:12', null, '565', '1', '22323', '1', '1111', '0', '0', '0', '0', '0', '0', '0', '0', '0', '3', 'ttt', '0', '0', '0', '0', '0', '0', '0', '0', '0', '70', '111', '0', '0', '0', '0', null, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `db_pile`
-- ----------------------------
DROP TABLE IF EXISTS `db_pile`;
CREATE TABLE `db_pile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pilenum` varchar(80) DEFAULT '' COMMENT '设备名称',
  `orderid` int(11) DEFAULT NULL COMMENT '排序ID',
  `parentid` int(11) DEFAULT NULL COMMENT '站点ID',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `ptype` tinyint(4) DEFAULT '0' COMMENT '桩状态1充电，0空闲',
  `isenable` tinyint(4) DEFAULT '1' COMMENT '是否启用',
  `isdelete` tinyint(4) DEFAULT '0' COMMENT '是否删除',
  `islink` tinyint(4) DEFAULT '0' COMMENT '是否连线',
  `client_id` varchar(80) DEFAULT '' COMMENT '客户端id',
  `cx` int(5) DEFAULT NULL,
  `cy` int(5) DEFAULT NULL,
  `cr` decimal(5,2) DEFAULT NULL,
  `isnone` tinyint(4) DEFAULT '3' COMMENT '车位状态 0空闲 1暂用 2遮挡 3未知',
  `smoney` int(12) DEFAULT '0' COMMENT '累计收入金额',
  `stime` int(20) DEFAULT '0' COMMENT '累计充电时间秒',
  `snum` int(9) DEFAULT '0' COMMENT '累计充电次数',
  `sele` int(21) DEFAULT '0' COMMENT '累计充电量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_pile
-- ----------------------------
INSERT INTO `db_pile` VALUES ('1', 'VM001', '1', '1', '2016-11-29 23:58:47', '1', '1', '0', '1', '7f0000010ac000000001', '32', '50', '90.00', '3', '0', '1269376', '45', '0');
INSERT INTO `db_pile` VALUES ('2', 'VM002', '2', '1', '2016-11-30 18:11:44', '0', '1', '0', '0', '', '240', '49', '-90.00', '3', '0', '0', '0', '0');
INSERT INTO `db_pile` VALUES ('3', 'VM003', '3', '1', '2016-11-30 18:37:47', '0', '1', '0', '0', '', '29', '98', '90.00', '3', '0', '0', '0', '0');
INSERT INTO `db_pile` VALUES ('4', '33w11', '4', '1', '2017-04-06 18:19:36', '0', '1', '0', '0', '', '239', '98', '-90.00', '3', '0', '0', '0', '0');
INSERT INTO `db_pile` VALUES ('5', 'ttt111', '5', '10', '2017-11-01 11:49:13', '0', '1', '0', '0', '', '42', '-5', '20.00', '3', '0', '0', '0', '0');
INSERT INTO `db_pile` VALUES ('6', '8887', '6', '13', '2017-11-13 14:31:52', '0', '1', '0', '0', '', '53', '50', '90.00', '3', '0', '0', '0', '0');
INSERT INTO `db_pile` VALUES ('7', '忑为', '7', '14', '2017-11-13 17:36:17', '0', '1', '0', '0', '', '74', '71', '0.00', '3', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `db_pnote`
-- ----------------------------
DROP TABLE IF EXISTS `db_pnote`;
CREATE TABLE `db_pnote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `uname` varchar(20) CHARACTER SET utf8 DEFAULT '--' COMMENT '用户昵称',
  `bid` int(10) DEFAULT '0' COMMENT '商家ID',
  `bname` varchar(80) CHARACTER SET utf8 DEFAULT '' COMMENT '商家名称',
  `pid` int(11) NOT NULL,
  `pname` varchar(80) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '桩名',
  `addtime` datetime NOT NULL COMMENT '添加时间',
  `w` int(12) NOT NULL,
  `v` int(7) NOT NULL,
  `a` int(6) NOT NULL,
  `mark` text CHARACTER SET utf8,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of db_pnote
-- ----------------------------
INSERT INTO `db_pnote` VALUES ('1', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-21 07:10:48', '2075', '21751', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('2', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-21 07:15:12', '2075', '21751', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('3', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-21 08:36:20', '2075', '21751', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('4', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-24 08:30:57', '2076', '22078', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('5', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-25 07:47:16', '2076', '22078', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('6', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-25 07:52:32', '2076', '22078', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('7', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-25 07:56:29', '2076', '22078', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('8', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-25 09:29:13', '2076', '22078', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('9', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 02:35:02', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('10', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 05:03:06', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('11', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 05:29:03', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('12', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 05:29:32', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('13', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 05:29:43', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('14', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 05:38:13', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('15', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 05:40:16', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('16', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 05:41:47', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('17', '0', '-', '71', '13829719806', '2', 'VM002', '2017-11-26 05:42:28', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('18', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 05:42:58', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('19', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:00:39', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('20', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:06:25', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('21', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:07:17', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('22', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:09:43', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('23', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:11:48', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('24', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:12:01', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('25', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:12:02', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('26', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:13:53', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('27', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:13:55', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('28', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:26:24', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('29', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:26:25', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('30', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 06:32:18', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('31', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:32:19', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('32', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 06:33:09', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('33', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 06:38:56', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('34', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 06:40:37', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('35', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 06:41:15', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('36', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 06:45:44', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('37', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:46:01', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('38', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:51:28', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('39', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:52:12', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('40', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:52:22', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('41', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:52:32', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('42', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:52:42', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('43', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:52:52', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('44', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:53:02', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('45', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:53:12', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('46', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:53:22', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('47', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:53:32', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('48', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:53:42', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('49', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:53:52', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('50', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:54:02', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('51', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:54:12', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('52', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:54:15', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('53', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:54:52', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('54', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 06:59:43', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('55', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 07:01:58', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('56', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 07:03:57', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('57', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 07:05:59', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('58', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 07:06:09', '2077', '22142', '87', '充电中-记录电表数据');
INSERT INTO `db_pnote` VALUES ('59', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 07:06:19', '2077', '22142', '87', '充电中-记录电表数据');
INSERT INTO `db_pnote` VALUES ('60', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-11-26 07:06:29', '2077', '22142', '87', '充电中-记录电表数据');
INSERT INTO `db_pnote` VALUES ('61', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 07:07:25', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('62', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 07:07:35', '2077', '22142', '87', '电表修改-记录电表数据');
INSERT INTO `db_pnote` VALUES ('63', '0', '-', '71', '13829719806', '1', 'VM001', '2017-11-26 07:25:11', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('64', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-12-03 05:29:32', '2077', '22142', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('65', '0', '-', '71', '13829719806', '1', 'VM001', '2017-12-03 05:41:56', '2077', '22049', '86', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('66', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-12-04 04:42:40', '2078', '22901', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('67', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-12-05 09:18:32', '2078', '22901', '87', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('68', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-12-05 05:07:38', '2079', '22100', '88', '充电中-记录电表数据');
INSERT INTO `db_pnote` VALUES ('69', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-12-06 11:39:16', '2079', '22100', '88', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('70', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-12-06 12:08:56', '2079', '22100', '88', '登录-记录电表数据');
INSERT INTO `db_pnote` VALUES ('71', '3', '陆佳利', '71', '13829719806', '1', 'VM001', '2017-12-07 04:29:41', '2080', '22876', '87', '充电中-记录电表数据');

-- ----------------------------
-- Table structure for `db_pulog`
-- ----------------------------
DROP TABLE IF EXISTS `db_pulog`;
CREATE TABLE `db_pulog` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `tid` int(20) DEFAULT '0' COMMENT 'tempid',
  `bid` int(20) DEFAULT '0' COMMENT '商家ID',
  `bname` varchar(80) DEFAULT '' COMMENT '商家名称',
  `pid` int(20) DEFAULT '0' COMMENT '桩ID',
  `pname` varchar(80) DEFAULT '' COMMENT '桩名称',
  `sid` int(20) DEFAULT '0' COMMENT '站点id',
  `sname` varchar(80) DEFAULT '' COMMENT '站点名称',
  `uid` int(20) DEFAULT '0' COMMENT '充电用户ID',
  `uname` varchar(200) DEFAULT '' COMMENT '用户昵称',
  `cuint` int(5) DEFAULT '0' COMMENT '本次充电单价',
  `cmoney` int(8) DEFAULT '0' COMMENT '本次变化金额',
  `ctime` int(7) DEFAULT '0' COMMENT '本次充电时间，秒',
  `cele` int(11) DEFAULT '0' COMMENT '本次充电电量',
  `addtime` datetime DEFAULT NULL COMMENT '完成时间',
  `psmoney` int(12) DEFAULT '0' COMMENT '该桩累计充电金额分',
  `pstime` int(20) DEFAULT '0' COMMENT '该桩累计提供充电时间秒',
  `psnum` int(9) DEFAULT '0' COMMENT '该桩累计充电次数',
  `psele` int(20) DEFAULT '0' COMMENT '该桩累计充电电量',
  `usmoney` int(12) DEFAULT '0' COMMENT '用户累计花费金额分',
  `ustime` int(20) DEFAULT '0' COMMENT '该用户累计充电时间秒',
  `usnum` int(9) DEFAULT '0' COMMENT '用户累计充电次数',
  `usele` int(20) DEFAULT '0' COMMENT '用户累计充电电量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_pulog
-- ----------------------------
INSERT INTO `db_pulog` VALUES ('1', '2', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '148', '0', '2017-11-21 19:14:13', '0', '148', '1', '0', '0', '148', '1', '0');
INSERT INTO `db_pulog` VALUES ('2', '3', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '58', '0', '2017-11-21 19:15:18', '0', '206', '2', '0', '0', '206', '2', '0');
INSERT INTO `db_pulog` VALUES ('3', '4', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '46', '0', '2017-11-21 19:16:11', '0', '252', '3', '0', '0', '252', '3', '0');
INSERT INTO `db_pulog` VALUES ('4', '5', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '4', '0', '2017-11-21 19:21:53', '0', '256', '4', '0', '0', '256', '4', '0');
INSERT INTO `db_pulog` VALUES ('5', '6', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '3', '0', '2017-11-21 19:36:37', '0', '259', '5', '0', '0', '259', '5', '0');
INSERT INTO `db_pulog` VALUES ('6', '7', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '46', '0', '2017-11-21 19:37:30', '0', '305', '6', '0', '0', '305', '6', '0');
INSERT INTO `db_pulog` VALUES ('7', '8', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '3663', '0', '2017-11-21 20:38:44', '0', '3968', '7', '0', '0', '3968', '7', '0');
INSERT INTO `db_pulog` VALUES ('8', '9', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '24', '0', '2017-11-21 20:39:24', '0', '3992', '8', '0', '0', '3992', '8', '0');
INSERT INTO `db_pulog` VALUES ('9', '10', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '44331', '0', '2017-11-22 08:58:26', '0', '48323', '9', '0', '0', '48323', '9', '0');
INSERT INTO `db_pulog` VALUES ('10', '11', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '214339', '0', '2017-11-24 20:30:57', '0', '262662', '10', '0', '0', '262662', '10', '0');
INSERT INTO `db_pulog` VALUES ('11', '12', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '0', '0', '2017-11-24 20:31:26', '0', '262662', '11', '0', '0', '262662', '11', '0');
INSERT INTO `db_pulog` VALUES ('12', '13', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '5', '0', '2017-11-24 20:32:43', '0', '262667', '12', '0', '0', '262667', '12', '0');
INSERT INTO `db_pulog` VALUES ('13', '14', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '3', '0', '2017-11-24 20:32:52', '0', '262670', '13', '0', '0', '262670', '13', '0');
INSERT INTO `db_pulog` VALUES ('14', '15', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '2', '0', '2017-11-24 20:33:27', '0', '262672', '14', '0', '0', '262672', '14', '0');
INSERT INTO `db_pulog` VALUES ('15', '16', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '156', '0', '2017-11-24 20:36:17', '0', '262828', '15', '0', '0', '262828', '15', '0');
INSERT INTO `db_pulog` VALUES ('16', '17', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '75533', '0', '2017-11-25 17:35:21', '0', '338361', '16', '0', '0', '338361', '16', '0');
INSERT INTO `db_pulog` VALUES ('17', '19', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '7', '0', '2017-11-25 19:49:48', '0', '338368', '17', '0', '0', '338368', '17', '0');
INSERT INTO `db_pulog` VALUES ('18', '20', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '3', '0', '2017-11-25 19:51:57', '0', '338371', '18', '0', '0', '338371', '18', '0');
INSERT INTO `db_pulog` VALUES ('19', '21', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '18', '0', '2017-11-25 19:53:19', '0', '338389', '19', '0', '0', '338389', '19', '0');
INSERT INTO `db_pulog` VALUES ('20', '22', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '3', '0', '2017-11-25 19:53:28', '0', '338392', '20', '0', '0', '338392', '20', '0');
INSERT INTO `db_pulog` VALUES ('21', '23', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '7', '0', '2017-11-25 19:56:13', '0', '338399', '21', '0', '0', '338399', '21', '0');
INSERT INTO `db_pulog` VALUES ('22', '24', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '9', '0', '2017-11-25 19:56:50', '0', '338408', '22', '0', '0', '338408', '22', '0');
INSERT INTO `db_pulog` VALUES ('23', '25', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '4', '0', '2017-11-25 20:08:48', '0', '338412', '23', '0', '0', '338412', '23', '0');
INSERT INTO `db_pulog` VALUES ('24', '26', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '2', '0', '2017-11-25 20:08:55', '0', '338414', '24', '0', '0', '338414', '24', '0');
INSERT INTO `db_pulog` VALUES ('25', '28', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '10', '0', '2017-11-25 21:29:38', '0', '338424', '25', '0', '0', '338424', '25', '0');
INSERT INTO `db_pulog` VALUES ('26', '29', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '4', '0', '2017-11-25 21:29:47', '0', '338428', '26', '0', '0', '338428', '26', '0');
INSERT INTO `db_pulog` VALUES ('27', '30', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '59890', '0', '2017-11-26 14:08:08', '0', '398318', '27', '0', '0', '398318', '27', '0');
INSERT INTO `db_pulog` VALUES ('28', '31', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '8', '0', '2017-11-26 14:08:31', '0', '398326', '28', '0', '0', '398326', '28', '0');
INSERT INTO `db_pulog` VALUES ('29', '33', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '3', '0', '2017-11-26 17:07:27', '0', '398329', '29', '0', '0', '398329', '29', '0');
INSERT INTO `db_pulog` VALUES ('30', '34', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '985', '0', '2017-11-26 17:29:25', '0', '399314', '30', '0', '0', '399314', '30', '0');
INSERT INTO `db_pulog` VALUES ('31', '34', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '5295', '0', '2017-11-26 18:41:15', '0', '404609', '31', '0', '0', '404609', '31', '0');
INSERT INTO `db_pulog` VALUES ('32', '35', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '48953', '0', '2017-11-27 09:01:15', '0', '453562', '32', '0', '0', '453562', '32', '0');
INSERT INTO `db_pulog` VALUES ('33', '36', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '3', '0', '2017-11-27 09:01:32', '0', '453565', '33', '0', '0', '453565', '33', '0');
INSERT INTO `db_pulog` VALUES ('34', '37', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '548353', '0', '2017-12-03 17:29:32', '0', '1001918', '34', '0', '0', '1001918', '34', '0');
INSERT INTO `db_pulog` VALUES ('35', '38', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '36', '0', '2017-12-03 17:43:48', '0', '1001954', '35', '0', '0', '1001954', '35', '0');
INSERT INTO `db_pulog` VALUES ('36', '39', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '5', '0', '2017-12-03 17:44:06', '0', '1001959', '36', '0', '0', '1001959', '36', '0');
INSERT INTO `db_pulog` VALUES ('37', '40', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '4', '0', '2017-12-03 17:44:16', '0', '1001963', '37', '0', '0', '1001963', '37', '0');
INSERT INTO `db_pulog` VALUES ('38', '41', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '4', '0', '2017-12-03 17:44:24', '0', '1001967', '38', '0', '0', '1001967', '38', '0');
INSERT INTO `db_pulog` VALUES ('39', '42', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '5', '0', '2017-12-03 18:07:56', '0', '1001972', '39', '0', '0', '1001972', '39', '0');
INSERT INTO `db_pulog` VALUES ('40', '43', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '80340', '0', '2017-12-04 16:42:54', '0', '1082312', '40', '0', '0', '1082312', '40', '0');
INSERT INTO `db_pulog` VALUES ('41', '44', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '274', '0', '2017-12-04 16:47:41', '0', '1082586', '41', '0', '0', '1082586', '41', '0');
INSERT INTO `db_pulog` VALUES ('42', '45', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '58759', '0', '2017-12-05 09:07:39', '0', '1141345', '42', '0', '0', '1141345', '42', '0');
INSERT INTO `db_pulog` VALUES ('43', '46', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '20290', '0', '2017-12-05 14:46:13', '0', '1161635', '43', '0', '0', '1161635', '43', '0');
INSERT INTO `db_pulog` VALUES ('44', '47', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '83683', '0', '2017-12-06 14:01:23', '0', '1245318', '44', '0', '0', '1245318', '44', '0');
INSERT INTO `db_pulog` VALUES ('45', '48', '71', '13829719806', '1', 'VM001', '1', '广州大观中路充电站', '3', '陆佳利', '1', '0', '24058', '0', '2017-12-06 20:42:40', '0', '1269376', '45', '0', '0', '1269376', '45', '0');

-- ----------------------------
-- Table structure for `db_sitelist`
-- ----------------------------
DROP TABLE IF EXISTS `db_sitelist`;
CREATE TABLE `db_sitelist` (
  `orderid` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitename` varchar(80) NOT NULL,
  `siteadd` varchar(200) DEFAULT NULL,
  `siteinfoadd` varchar(600) DEFAULT NULL,
  `sitetel` varchar(20) DEFAULT NULL,
  `siteimg` varchar(100) DEFAULT NULL,
  `sitex` varchar(32) DEFAULT NULL,
  `sitey` varchar(32) DEFAULT NULL,
  `bsitex` varchar(32) DEFAULT NULL,
  `bsitey` varchar(32) DEFAULT NULL,
  `tsitex` varchar(32) DEFAULT NULL,
  `tsitey` varchar(32) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `isenable` tinyint(4) DEFAULT '1',
  `mark` text,
  `linkpwd` varchar(80) DEFAULT NULL,
  `uint` int(11) DEFAULT '0' COMMENT ' 单价',
  `isdelete` tinyint(4) DEFAULT '0',
  `client_id` varchar(80) DEFAULT NULL,
  `siteimgs` varchar(600) DEFAULT NULL,
  `sitemap` varchar(100) DEFAULT NULL,
  `bid` int(10) DEFAULT NULL COMMENT '所属商家id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_sitelist
-- ----------------------------
INSERT INTO `db_sitelist` VALUES ('1', '1', '广州大观中路充电站', '天河区大观路490号', '广东省广州市天河区大观路490号', '13829719806', '/Web/UploadFile/Site/img/2017-11-02/59fa99542a044.png', '23.165609', '113.415335', '23.171844', '113.421691', '23.165596944266216', '113.41536787610278', '2016-11-29 00:28:53', '1', '大观中路充电站', '123456', '1', '0', null, '|/Web/UploadFile/Site/pl/2017-11-02/59fa994ded3e1.png|/Web/UploadFile/Site/pl/2017-11-02/59fa994e0399c.png|/Web/UploadFile/Site/pl/2017-11-02/59fa994e19161.png|/Web/UploadFile/Site/pl/2017-11-02/59fa994e24ce4.png|/Web/UploadFile/Site/pl/2017-11-02/59fa994e33747.png|', '/Web/UploadFile/Site/map/2017-11-02/59fa99542abfc.jpg', '71');
INSERT INTO `db_sitelist` VALUES ('4', '2', '白云山充电站', '广州市广州大道北梅园路1008号', '广东省广州市广州大道北梅园路1008号', '13829719806', '20161017143615476.jpg', '23.178429', '113.312833', '23.179788', '113.317541', '23.179042647338918', '113.3094365212343', '2016-11-29 00:28:53', '1', '白云山充电站', '1111111', '2', '0', null, '', null, '71');
INSERT INTO `db_sitelist` VALUES ('2', '3', '大学城充电站', '广州市番禺区大学城外环东路121号', '广东省广州市番禺区大学城外环东路121号', '020-78543695', '20161129162034329.png', '23.060984', '113.385305', '23.115223', '113.276017', '23.06077685656366', '113.38469172545595', '2016-11-29 00:20:34', '1', '大学城充电站', '111111', '1', '0', null, '', null, '71');
INSERT INTO `db_sitelist` VALUES ('3', '4', '广州市科韵路充电站', '天河中山大道工业园科韵路20-22号', '广东省广州市天河区中山大道工业园科韵路20-22号', '13829719806', '/Web/UploadFile/Site/img/2016-11-30/583e4a4ebc736.jpg', '23.125043', '113.373688', '23.131055', '113.379982', '23.125031950852247', '113.37351246177705', '2016-11-29 19:41:02', '1', '广州市科韵路充电站', '123456', '2', '0', null, '|/Web/UploadFile/Site/pl/2016-11-30/583e4a09ed91a.jpg|/Web/UploadFile/Site/pl/2016-11-30/583e4a0a0ea99.jpg|/Web/UploadFile/Site/pl/2016-11-30/583e4a0a232c7.jpg|', '/Web/UploadFile/Site/map/2016-11-30/583e4a4ebcf17.jpg', '71');
INSERT INTO `db_sitelist` VALUES ('5', '5', '广州火车站充电站', '广州市越秀区环市西路158号 ', ' 广东省广州市越秀区环市西路158号 ', '020-38596541', '/Web/UploadFile/Site/img/2016-11-30/583e4a4ebc736.jpg', '23.146317', '113.256073', '23.152502', '113.26283', '23.142994902838463', '113.25624973948429', '2016-11-29 23:58:28', '1', '广州火车站充电站', '213123', '1', '0', null, '|/Web/UploadFile/Site/pl/2016-12-16/5853931d904f3.jpg|', '/Web/UploadFile/Site/map/2016-11-30/583e86a446c0c.jpg', '71');
INSERT INTO `db_sitelist` VALUES ('6', '6', '体育中心充电站', '广州市天河区天河路313号', '广东省广州市天河区天河路313号', '020-83961452', '/Web/UploadFile/Site/img/2016-11-30/583e673a74f08.png', '23.13467', '113.328187', '23.139983', '113.330032', '23.134422790188125', '113.32153129729394', '2016-11-30 00:45:00', '1', '体育中心充电站', '111222', '1', '0', null, '|/Web/UploadFile/Site/pl/2016-11-30/583e66ca21cb5.jpg|/Web/UploadFile/Site/pl/2016-11-30/583e66ca2f203.jpg|/Web/UploadFile/Site/pl/2016-11-30/583e66ca4651b.jpg|/Web/UploadFile/Site/pl/2016-11-30/583e66fc0e597.png|/Web/UploadFile/Site/pl/2016-11-30/583e66fe177a3.png|', '/Web/UploadFile/Site/map/2016-11-30/583e918cb953d.jpg', '71');
INSERT INTO `db_sitelist` VALUES ('7', '7', '天河公园充电站', '广州市天河区中山大道天河公园', '广东省广州市天河区中山大道天河公园', '13829719806', '/Web/UploadFile/Site/img/2016-11-30/583ea5ba937ba.jpg', '23.131556', '113.366886', '23.137344', '113.373811', '23.13152880741384', '113.36713336453917', '2016-11-30 18:11:06', '1', '天河公园充电站', '123456', '2', '0', null, '|/Web/UploadFile/Site/pl/2016-11-30/583ea5a566dde.jpg|/Web/UploadFile/Site/pl/2016-11-30/583ea5a577f71.jpg|/Web/UploadFile/Site/pl/2017-02-18/58a82c7cceab3.jpg|', '/Web/UploadFile/Site/map/2017-02-18/58a82c71a79ac.jpg', '71');
INSERT INTO `db_sitelist` VALUES ('8', '8', 'ewerw1', 'ewrerw11111', 'ewrewrerw1', '2332', '/Web/UploadFile/Site/img/2017-04-06/58e5fdf1ab415.jpg', '38.322005', '106.692219', '37.447122', '105.514792', '37.81037837267359', '101.38107635578348', '2017-04-06 16:36:01', '1', '1212', '123', '121', '0', null, '|/Web/UploadFile/Site/pl/2017-04-06/58e6107e6dda8.png|/Web/UploadFile/Site/pl/2017-04-06/58e6107e8d979.png|/Web/UploadFile/Site/pl/2017-04-06/58e6107ea6019.png|', '/Web/UploadFile/Site/map/2017-04-06/58e5fdf1b1d8f.jpg', '71');
INSERT INTO `db_sitelist` VALUES ('9', '9', 'test', 'dsdfa', 'dasf', '213', '/Web/UploadFile/Site/img/2017-04-06/58e60f9746fc1.jpg', '38.322005', '106.692219', '37.447122', '105.514792', '37.81037837267359', '101.38107635578348', '2017-04-06 16:37:01', '1', '123213', '222', '112', '0', null, '|/Web/UploadFile/Site/pl/2017-04-06/58e5fe206d687.png|', '/Web/UploadFile/Site/map/2017-04-06/58e60f974bde1.jpg', '71');
INSERT INTO `db_sitelist` VALUES ('10', '10', 'tet1', '23231', '', '3242341', '/Web/UploadFile/Site/img/2017-11-01/59f943c091fcd.png', '', '', '', '', '23.022178017833056', '114.36986662944986', '2017-11-01 11:43:27', '1', '232131', '2131231', '2', '0', null, '|/Web/UploadFile/Site/pl/2017-11-01/59f943bd153af.png|/Web/UploadFile/Site/pl/2017-11-01/59f943bd1cd8f.png|/Web/UploadFile/Site/pl/2017-11-01/59f943bd2fe3f.png|/Web/UploadFile/Site/pl/2017-11-01/59f943bd3f1ff.png|/Web/UploadFile/Site/pl/2017-11-01/59f943bd4e5c0.png|', '/Web/UploadFile/Site/map/2017-11-01/59f943b0896ab.png', '1');
INSERT INTO `db_sitelist` VALUES ('11', '11', 'test1111111', '111', null, '2222', '/Web/UploadFile/Site/img/2017-11-02/59fa8d25c2843.png', null, null, null, null, '23.347990089771603', '115.26779510578348', '2017-11-02 11:12:37', '1', '12', '222', '120', '0', null, '|/Web/UploadFile/Site/pl/2017-11-02/59fa8d17a8324.png|', '/Web/UploadFile/Site/map/2017-11-02/59fa8d25c2c2b.png', '1');
INSERT INTO `db_sitelist` VALUES ('12', '12', 'ttteee111', '2222', null, '3333', '/Web/UploadFile/Site/img/2017-11-02/59fa8e5880bb3.png', null, null, null, null, '23.991958675764987', '114.56467010578348', '2017-11-02 11:17:44', '1', 'ere', '3232', '2', '0', null, '|/Web/UploadFile/Site/pl/2017-11-02/59fa8e453d52d.png|', '/Web/UploadFile/Site/map/2017-11-02/59fa8e5881383.png', '1');
INSERT INTO `db_sitelist` VALUES ('13', '13', 'ewt', 'qwer', null, '324', '/Web/UploadFile/Site/img/2017-11-13/5a091b99024d1.png', null, null, null, null, '22.700882025979485', '113.33420135578348', '2017-11-13 12:12:09', '1', '324', '323', '122', '0', null, '|/Web/UploadFile/Site/pl/2017-11-13/5a091b854951b.png|/Web/UploadFile/Site/pl/2017-11-13/5a091b8554beb.png|/Web/UploadFile/Site/pl/2017-11-13/5a091b8565016.png|/Web/UploadFile/Site/pl/2017-11-13/5a091b85751ea.png|', '/Web/UploadFile/Site/map/2017-11-13/5a091b9902ca1.jpg', '70');
INSERT INTO `db_sitelist` VALUES ('14', '14', '测试2', '3二', null, '阿斯顿发', '/Web/UploadFile/Site/img/2017-11-13/5a09677bce939.png', null, null, null, null, '23.347990089771603', '113.86154510578348', '2017-11-13 17:35:55', '1', '圣诞树', '213123', '120', '0', null, '|/Web/UploadFile/Site/pl/2017-11-13/5a09675dd1afb.png|/Web/UploadFile/Site/pl/2017-11-13/5a09675de168b.png|/Web/UploadFile/Site/pl/2017-11-13/5a09675def14e.png|/Web/UploadFile/Site/pl/2017-11-13/5a09675e0b0e2.png|/Web/UploadFile/Site/pl/2017-11-27/5a1bf1a99b8fa.png|', '/Web/UploadFile/Site/map/2017-11-13/5a09677bcf109.jpg', '70');

-- ----------------------------
-- Table structure for `db_sms`
-- ----------------------------
DROP TABLE IF EXISTS `db_sms`;
CREATE TABLE `db_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Newtitle` varchar(200) DEFAULT NULL,
  `Newcontent` text,
  `Newdesc` varchar(500) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `targets` tinyint(4) DEFAULT '0' COMMENT '0全部,1安卓，2IOS',
  `putout` tinyint(4) DEFAULT '1',
  `isdelete` tinyint(4) DEFAULT '0',
  `orderid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_sms
-- ----------------------------
INSERT INTO `db_sms` VALUES ('1', '欢迎使用APP', '<p>欢迎使用APP欢迎使用APP欢，迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用，APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使，用APP欢迎使用APP欢迎使用APP欢迎使用APP欢，迎使用APP欢。</p><p>迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢，迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使，用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP欢迎使用APP</p>', '欢迎使用APP欢迎使用APP欢迎使，用APP欢迎使用APP欢迎使用A，PP欢迎使用APP欢迎使用APP欢迎使用APP欢，迎使用APP', '2017-05-06 18:33:13', '0', '1', '0', '1');

-- ----------------------------
-- Table structure for `db_sys_admin`
-- ----------------------------
DROP TABLE IF EXISTS `db_sys_admin`;
CREATE TABLE `db_sys_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(80) DEFAULT NULL COMMENT '登录名',
  `session` varchar(40) DEFAULT NULL COMMENT 'sessionid',
  `passwords` varchar(80) DEFAULT NULL COMMENT '密码',
  `adminClass` int(11) DEFAULT NULL COMMENT '类别',
  `name` varchar(50) DEFAULT NULL COMMENT '昵称',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  `working` tinyint(4) DEFAULT NULL COMMENT '是否启用',
  `ver` varchar(50) DEFAULT '0' COMMENT '所属版本',
  `parts` longtext COMMENT '所有权限',
  `email` varchar(40) DEFAULT '' COMMENT '邮箱',
  `tel` varchar(20) DEFAULT NULL COMMENT '电话',
  `mark` varchar(100) DEFAULT '' COMMENT '说明',
  `zhifu` varchar(100) DEFAULT '' COMMENT '支付账户',
  `actnum` varchar(40) DEFAULT NULL COMMENT '激活码',
  `isact` tinyint(4) DEFAULT '0' COMMENT '是否激活',
  `mchid` varchar(50) DEFAULT '' COMMENT '商户号',
  `mchkey` varchar(100) DEFAULT '' COMMENT '商户KEY',
  `certpath` varchar(150) DEFAULT NULL COMMENT 'cert证书地址',
  `keypath` varchar(150) DEFAULT NULL COMMENT 'key证书地址',
  `smoney` int(12) DEFAULT '0' COMMENT '累计收入金额',
  `stime` int(20) DEFAULT '0' COMMENT '累计充电时间秒',
  `snum` int(9) DEFAULT '0' COMMENT '累计充电次数',
  `sele` int(21) DEFAULT '0' COMMENT '累计充电量',
  `money` int(12) DEFAULT '0' COMMENT '实际可取金额',
  `tmoney` int(12) DEFAULT '0' COMMENT '取款金额',
  `tnum` int(9) DEFAULT '0' COMMENT '取款次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_sys_admin
-- ----------------------------
INSERT INTO `db_sys_admin` VALUES ('1', 'admin', 'b7b169d0813e8c373a393182680dc72c', 'e10adc3949ba59abbe56e057f20f883e', '99', '管理员', '2016-07-15 16:18:24', '1', '0', '', '469100943@qq.com', '13829719806', '223133', '112233', '', '1', null, null, null, null, '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('2', 'super admin', null, '21232f297a57a5a743894a0e4a801fc3', '99', 'uweb', '2016-07-06 14:19:04', '1', '0', '', '#', null, null, '', null, '1', null, null, null, null, '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('69', 'test2', '13c72ed6eb22d42d60ac4d28b641d490', 'e10adc3949ba59abbe56e057f20f883e', '1', '管理测试', '2017-04-05 10:50:12', '1', '0', '', '', null, null, '', null, '1', null, null, null, null, '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('70', 'test1', 'e35607ed881cd34427ce48dbf13ea68e', 'e10adc3949ba59abbe56e057f20f883e', '0', 'cesss', '2017-10-30 07:02:55', '1', '0', '20,21,23,24,25,6,8,9,18,28,27,10,11,4,16,17', '', null, '331', 'ee1', null, '1', null, null, null, null, '0', '0', '0', '0', '9', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('71', '13829719806', 'ac7f1a6876a7a6989ed0b91ba795f9da', 'e10adc3949ba59abbe56e057f20f883e', '0', 'test', '2017-10-31 10:20:40', '1', '0', '1,2,4,11,18,6,8,9,16,17,20,21,25,27,28', '', null, null, '', null, '1', '1309840501', 'RICHCOMM2016RICHCOMM2016RICHCOMM', '/Web/UploadFile/Admin/2017-11-04/59fdd235d7d0b.pem', '/Web/UploadFile/Admin/2017-11-04/59fdd235dbb8c.pem', '0', '1269376', '45', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('72', '213213', null, 'e10adc3949ba59abbe56e057f20f883e', '0', '111223311', '2017-10-31 10:23:44', '1', '0', '1,2,4,11,18,6,8,9,16,17,20,21,25,27,28', '', null, null, '', null, '1', '123', '123213', null, null, '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('73', '111', null, '00b7691d86d96aebd21dd9e138f90840', '0', '111222', '2017-10-31 10:27:46', '1', '0', '1,2,4,11,18,6,8,9,16,17,20,21,25,27,28', '', null, null, '', null, '1', '111222', '111222', null, null, '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('74', '234234', null, '96e79218965eb72c92a549dd5a330112', '0', '123', '2017-10-31 10:28:29', '1', '0', '1,2,4,11,18,6,8,9,16,17,20,21,25,27,28', '', null, null, '', null, '1', '111', '111', null, null, '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('75', '111122221', null, '96e79218965eb72c92a549dd5a330112', '0', '111222', '2017-10-31 10:40:25', '1', '0', '1,2,4,11,18,6,8,9,16,17,20,21,25,27,28', '', null, null, '', null, '1', '12', '12', '/Web/UploadFile/Admin/2017-10-31/59f7e299d2c32.pem', '/Web/UploadFile/Admin/2017-10-31/59f7e299d9994.pem', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('76', '111444', null, '93279e3308bdbbeed946fc965017f67a', '0', '121212', '2017-10-31 10:41:49', '1', '0', '1,2,4,11,18,6,8,9,16,17,20,21,25,27,28', '', null, null, '', null, '1', '121212', '121212', '/Web/UploadFile/Admin/2017-10-31/59f7e2eda126b.pem', '/Web/UploadFile/Admin/2017-10-31/59f7e2eda1653.pem', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('77', '11122345', null, '00b7691d86d96aebd21dd9e138f90840', '0', '111111', '2017-10-31 10:43:02', '1', '0', '1,2,4,11,18,6,8,9,16,17,20,21,25,27,28', '', null, null, '', null, '1', '111222', '111222', '/Web/UploadFile/Admin/2017-10-31/59f7ebec1a8e0.pem', '/Web/UploadFile/Admin/2017-10-31/59f7ebec1b0b0.pem', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('78', '111222333', null, '4297f44b13955235245b2497399d7a93', '0', '123213', '2017-10-31 11:21:37', '1', '0', '1,2,4,11,18,6,8,9,16,17,20,21,25,27,28', '', null, null, '', null, '1', '11', '111', '/Web/UploadFile/Admin/2017-10-31/59f7ed9078cdd.pem', '/Web/UploadFile/Admin/2017-10-31/59f7ec4199b9b.pem', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `db_sys_admin` VALUES ('79', 'ttttt', null, 'e10adc3949ba59abbe56e057f20f883e', '0', '222212', '2017-11-11 07:49:09', '1', '0', '', '', null, '使用12', '', null, '1', '', '', null, null, '0', '0', '0', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `db_sys_aslog`
-- ----------------------------
DROP TABLE IF EXISTS `db_sys_aslog`;
CREATE TABLE `db_sys_aslog` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `tid` int(20) DEFAULT '0' COMMENT 'tempid/申请取款id',
  `type` tinyint(1) DEFAULT '1' COMMENT '取款和充电ID1充电 2取款',
  `bid` int(11) DEFAULT '0' COMMENT '商家ID',
  `bname` varchar(80) DEFAULT '' COMMENT '商家登录名称',
  `cuint` int(5) DEFAULT '0' COMMENT '本次变化单价',
  `cmoney` int(8) DEFAULT '0' COMMENT '本次变化金额',
  `ctime` int(7) DEFAULT '0' COMMENT '本次充电时间',
  `cele` int(11) DEFAULT '0' COMMENT '本次充电电量',
  `addtime` datetime DEFAULT NULL COMMENT '完成时间',
  `bsmoney` int(12) DEFAULT '0' COMMENT '累计充电金额',
  `bstime` int(20) DEFAULT '0' COMMENT '累计充电时间秒',
  `bsnum` int(9) DEFAULT '0' COMMENT '累计充电次数',
  `bsele` int(20) DEFAULT '0' COMMENT '累计充电电量',
  `bmoney` int(12) DEFAULT '0' COMMENT '商家实际金额',
  `btmoney` int(12) DEFAULT '0' COMMENT '商家退款总金额',
  `btnum` int(9) DEFAULT '0' COMMENT '商家取款次数',
  `ssmoney` int(15) DEFAULT '0' COMMENT '平台累计充值金额',
  `sstime` int(20) DEFAULT '0' COMMENT '平台累计充电时间',
  `ssnum` int(9) DEFAULT '0' COMMENT '平台累计充电次数',
  `ssele` int(21) DEFAULT '0' COMMENT '平台累计充电电量',
  `smoney` int(12) DEFAULT '0' COMMENT '平台真实剩余金额',
  `stmoney` int(12) DEFAULT '0' COMMENT '平台累计取款金额',
  `stnum` int(9) DEFAULT '0' COMMENT '平台累计取款次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_sys_aslog
-- ----------------------------
INSERT INTO `db_sys_aslog` VALUES ('1', '2', '1', '71', '13829719806', '1', '0', '148', '0', '2017-11-21 19:14:13', '0', '148', '1', '0', '0', '0', '0', '0', '148', '1', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('2', '3', '1', '71', '13829719806', '1', '0', '58', '0', '2017-11-21 19:15:18', '0', '206', '2', '0', '0', '0', '0', '0', '206', '2', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('3', '4', '1', '71', '13829719806', '1', '0', '46', '0', '2017-11-21 19:16:11', '0', '252', '3', '0', '0', '0', '0', '0', '252', '3', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('4', '5', '1', '71', '13829719806', '1', '0', '4', '0', '2017-11-21 19:21:53', '0', '256', '4', '0', '0', '0', '0', '0', '256', '4', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('5', '6', '1', '71', '13829719806', '1', '0', '3', '0', '2017-11-21 19:36:37', '0', '259', '5', '0', '0', '0', '0', '0', '259', '5', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('6', '7', '1', '71', '13829719806', '1', '0', '46', '0', '2017-11-21 19:37:30', '0', '305', '6', '0', '0', '0', '0', '0', '305', '6', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('7', '8', '1', '71', '13829719806', '1', '0', '3663', '0', '2017-11-21 20:38:44', '0', '3968', '7', '0', '0', '0', '0', '0', '3968', '7', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('8', '9', '1', '71', '13829719806', '1', '0', '24', '0', '2017-11-21 20:39:24', '0', '3992', '8', '0', '0', '0', '0', '0', '3992', '8', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('9', '10', '1', '71', '13829719806', '1', '0', '44331', '0', '2017-11-22 08:58:26', '0', '48323', '9', '0', '0', '0', '0', '0', '48323', '9', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('10', '11', '1', '71', '13829719806', '1', '0', '214339', '0', '2017-11-24 20:30:57', '0', '262662', '10', '0', '0', '0', '0', '0', '262662', '10', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('11', '12', '1', '71', '13829719806', '1', '0', '0', '0', '2017-11-24 20:31:26', '0', '262662', '11', '0', '0', '0', '0', '0', '262662', '11', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('12', '13', '1', '71', '13829719806', '1', '0', '5', '0', '2017-11-24 20:32:43', '0', '262667', '12', '0', '0', '0', '0', '0', '262667', '12', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('13', '14', '1', '71', '13829719806', '1', '0', '3', '0', '2017-11-24 20:32:52', '0', '262670', '13', '0', '0', '0', '0', '0', '262670', '13', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('14', '15', '1', '71', '13829719806', '1', '0', '2', '0', '2017-11-24 20:33:27', '0', '262672', '14', '0', '0', '0', '0', '0', '262672', '14', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('15', '16', '1', '71', '13829719806', '1', '0', '156', '0', '2017-11-24 20:36:17', '0', '262828', '15', '0', '0', '0', '0', '0', '262828', '15', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('16', '17', '1', '71', '13829719806', '1', '0', '75533', '0', '2017-11-25 17:35:21', '0', '338361', '16', '0', '0', '0', '0', '0', '338361', '16', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('17', '19', '1', '71', '13829719806', '1', '0', '7', '0', '2017-11-25 19:49:48', '0', '338368', '17', '0', '0', '0', '0', '0', '338368', '17', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('18', '20', '1', '71', '13829719806', '1', '0', '3', '0', '2017-11-25 19:51:57', '0', '338371', '18', '0', '0', '0', '0', '0', '338371', '18', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('19', '21', '1', '71', '13829719806', '1', '0', '18', '0', '2017-11-25 19:53:19', '0', '338389', '19', '0', '0', '0', '0', '0', '338389', '19', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('20', '22', '1', '71', '13829719806', '1', '0', '3', '0', '2017-11-25 19:53:28', '0', '338392', '20', '0', '0', '0', '0', '0', '338392', '20', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('21', '23', '1', '71', '13829719806', '1', '0', '7', '0', '2017-11-25 19:56:13', '0', '338399', '21', '0', '0', '0', '0', '0', '338399', '21', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('22', '24', '1', '71', '13829719806', '1', '0', '9', '0', '2017-11-25 19:56:50', '0', '338408', '22', '0', '0', '0', '0', '0', '338408', '22', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('23', '25', '1', '71', '13829719806', '1', '0', '4', '0', '2017-11-25 20:08:48', '0', '338412', '23', '0', '0', '0', '0', '0', '338412', '23', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('24', '26', '1', '71', '13829719806', '1', '0', '2', '0', '2017-11-25 20:08:55', '0', '338414', '24', '0', '0', '0', '0', '0', '338414', '24', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('25', '28', '1', '71', '13829719806', '1', '0', '10', '0', '2017-11-25 21:29:38', '0', '338424', '25', '0', '0', '0', '0', '0', '338424', '25', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('26', '29', '1', '71', '13829719806', '1', '0', '4', '0', '2017-11-25 21:29:47', '0', '338428', '26', '0', '0', '0', '0', '0', '338428', '26', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('27', '30', '1', '71', '13829719806', '1', '0', '59890', '0', '2017-11-26 14:08:08', '0', '398318', '27', '0', '0', '0', '0', '0', '398318', '27', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('28', '31', '1', '71', '13829719806', '1', '0', '8', '0', '2017-11-26 14:08:31', '0', '398326', '28', '0', '0', '0', '0', '0', '398326', '28', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('29', '33', '1', '71', '13829719806', '1', '0', '3', '0', '2017-11-26 17:07:27', '0', '398329', '29', '0', '0', '0', '0', '0', '398329', '29', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('30', '34', '1', '71', '13829719806', '1', '0', '985', '0', '2017-11-26 17:29:25', '0', '399314', '30', '0', '0', '0', '0', '0', '399314', '30', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('31', '34', '1', '71', '13829719806', '1', '0', '5295', '0', '2017-11-26 18:41:15', '0', '404609', '31', '0', '0', '0', '0', '0', '404609', '31', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('32', '35', '1', '71', '13829719806', '1', '0', '48953', '0', '2017-11-27 09:01:15', '0', '453562', '32', '0', '0', '0', '0', '0', '453562', '32', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('33', '36', '1', '71', '13829719806', '1', '0', '3', '0', '2017-11-27 09:01:32', '0', '453565', '33', '0', '0', '0', '0', '0', '453565', '33', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('34', '37', '1', '71', '13829719806', '1', '0', '548353', '0', '2017-12-03 17:29:32', '0', '1001918', '34', '0', '0', '0', '0', '0', '1001918', '34', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('35', '38', '1', '71', '13829719806', '1', '0', '36', '0', '2017-12-03 17:43:48', '0', '1001954', '35', '0', '0', '0', '0', '0', '1001954', '35', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('36', '39', '1', '71', '13829719806', '1', '0', '5', '0', '2017-12-03 17:44:06', '0', '1001959', '36', '0', '0', '0', '0', '0', '1001959', '36', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('37', '40', '1', '71', '13829719806', '1', '0', '4', '0', '2017-12-03 17:44:16', '0', '1001963', '37', '0', '0', '0', '0', '0', '1001963', '37', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('38', '41', '1', '71', '13829719806', '1', '0', '4', '0', '2017-12-03 17:44:24', '0', '1001967', '38', '0', '0', '0', '0', '0', '1001967', '38', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('39', '42', '1', '71', '13829719806', '1', '0', '5', '0', '2017-12-03 18:07:56', '0', '1001972', '39', '0', '0', '0', '0', '0', '1001972', '39', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('40', '43', '1', '71', '13829719806', '1', '0', '80340', '0', '2017-12-04 16:42:54', '0', '1082312', '40', '0', '0', '0', '0', '0', '1082312', '40', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('41', '44', '1', '71', '13829719806', '1', '0', '274', '0', '2017-12-04 16:47:41', '0', '1082586', '41', '0', '0', '0', '0', '0', '1082586', '41', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('42', '45', '1', '71', '13829719806', '1', '0', '58759', '0', '2017-12-05 09:07:39', '0', '1141345', '42', '0', '0', '0', '0', '0', '1141345', '42', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('43', '46', '1', '71', '13829719806', '1', '0', '20290', '0', '2017-12-05 14:46:13', '0', '1161635', '43', '0', '0', '0', '0', '0', '1161635', '43', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('44', '47', '1', '71', '13829719806', '1', '0', '83683', '0', '2017-12-06 14:01:23', '0', '1245318', '44', '0', '0', '0', '0', '0', '1245318', '44', '0', '0', '0', '0');
INSERT INTO `db_sys_aslog` VALUES ('45', '48', '1', '71', '13829719806', '1', '0', '24058', '0', '2017-12-06 20:42:40', '0', '1269376', '45', '0', '0', '0', '0', '0', '1269376', '45', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `db_sys_menu`
-- ----------------------------
DROP TABLE IF EXISTS `db_sys_menu`;
CREATE TABLE `db_sys_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(100) DEFAULT NULL,
  `menu_parent` int(11) DEFAULT NULL,
  `menu_url` varchar(100) DEFAULT NULL,
  `orderid` int(11) DEFAULT NULL,
  `putout` tinyint(4) DEFAULT NULL,
  `side` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_sys_menu
-- ----------------------------
INSERT INTO `db_sys_menu` VALUES ('1', '平台信息 ', '0', null, '1', '1', null);
INSERT INTO `db_sys_menu` VALUES ('2', '用户协议', '1', '/System.php?s=/System/ManagerPage/UserAg', '2', '0', null);
INSERT INTO `db_sys_menu` VALUES ('3', '充值记录', '1', '/System.php?s=/System/PSou', '3', '0', null);
INSERT INTO `db_sys_menu` VALUES ('4', '会员动态', '10', '/System.php?s=/System/USou', '12', '1', null);
INSERT INTO `db_sys_menu` VALUES ('6', '运营信息', '0', null, '6', '1', null);
INSERT INTO `db_sys_menu` VALUES ('7', '管理员管理', '13', '/System.php?s=/System/AdminAll', '16', '1', null);
INSERT INTO `db_sys_menu` VALUES ('8', '站点列表', '6', '/System.php?s=/System/SiteListAll', '8', '1', null);
INSERT INTO `db_sys_menu` VALUES ('9', '设备列表', '6', '/System.php?s=/System/PileListAll', '9', '1', null);
INSERT INTO `db_sys_menu` VALUES ('10', '会员信息', '0', null, '10', '1', null);
INSERT INTO `db_sys_menu` VALUES ('11', '会员列表', '10', '/System.php?s=/System/UserAll', '11', '1', null);
INSERT INTO `db_sys_menu` VALUES ('12', '推送列表', '10', '/System.php?s=/System/SmsAll', '13', '0', null);
INSERT INTO `db_sys_menu` VALUES ('13', '系统信息', '0', null, '20', '1', null);
INSERT INTO `db_sys_menu` VALUES ('14', '系统设置', '13', '/System.php?s=/System/ManagerPage/sitesetup', '14', '1', null);
INSERT INTO `db_sys_menu` VALUES ('16', '管理日志', '13', '/System.php?s=/System/Log', '17', '1', null);
INSERT INTO `db_sys_menu` VALUES ('17', '修改密码', '13', '/System.php?s=/System/ManagerPage/ChangePwd', '18', '1', null);
INSERT INTO `db_sys_menu` VALUES ('18', '设备日志', '6', '/System.php?s=/System/Pnote', '19', '1', null);
INSERT INTO `db_sys_menu` VALUES ('19', '硬件版本', '13', '/System.php?s=/System/Down', '15', '1', null);
INSERT INTO `db_sys_menu` VALUES ('20', '商家中心', '0', null, '2', '1', null);
INSERT INTO `db_sys_menu` VALUES ('21', '综合信息', '20', '/System.php?s=/System/Person', '21', '1', null);
INSERT INTO `db_sys_menu` VALUES ('23', '提现申请', '20', '/System.php?s=/System/Person/AddRead', '23', '1', null);
INSERT INTO `db_sys_menu` VALUES ('24', '提现列表', '20', '/System.php?s=/System/Person/GetMoneyAll', '24', '1', null);
INSERT INTO `db_sys_menu` VALUES ('25', '收支列表', '20', '/System.php?s=/System/InCome', '25', '1', null);
INSERT INTO `db_sys_menu` VALUES ('26', '邮箱设置', '13', '/System.php?s=/System/ManagerPage/MailSet', '18', '0', null);
INSERT INTO `db_sys_menu` VALUES ('27', '订单列表', '1', '/System.php?s=/System/OrderList', '28', '1', null);
INSERT INTO `db_sys_menu` VALUES ('28', '交易记录', '6', '/System.php?s=/System/Porder', '27', '1', null);
INSERT INTO `db_sys_menu` VALUES ('29', '统计信息', '1', '/System.php?s=/System/ManagerPage/Count', '14', '1', null);
INSERT INTO `db_sys_menu` VALUES ('30', '充取记录', '1', '/System.php?s=/System/InSome', '26', '1', null);

-- ----------------------------
-- Table structure for `db_sys_money`
-- ----------------------------
DROP TABLE IF EXISTS `db_sys_money`;
CREATE TABLE `db_sys_money` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `bid` int(10) NOT NULL COMMENT '商家ID',
  `money` int(7) DEFAULT '0' COMMENT '取款金额',
  `Account` varchar(100) DEFAULT '' COMMENT '打款账号',
  `desctxt` varchar(300) DEFAULT '' COMMENT '说明',
  `addtime` datetime DEFAULT NULL,
  `prove` varchar(100) DEFAULT '' COMMENT '图片证明',
  `isset` tinyint(4) DEFAULT '0' COMMENT '是否处理',
  `isdone` tinyint(4) DEFAULT '0' COMMENT '是否执行转账',
  `mid` int(10) DEFAULT '0' COMMENT '最后操作管理员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_sys_money
-- ----------------------------

-- ----------------------------
-- Table structure for `db_sys_moneynote`
-- ----------------------------
DROP TABLE IF EXISTS `db_sys_moneynote`;
CREATE TABLE `db_sys_moneynote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `No` varchar(30) DEFAULT NULL COMMENT '交易编号',
  `uid` int(11) DEFAULT NULL COMMENT '商家ID',
  `type` tinyint(1) DEFAULT '0' COMMENT '标识是收入0还是支出1，充电后是收入，取款为支出',
  `typeid` int(11) DEFAULT '0' COMMENT '标识在充电表uson还是取款表sys_money中的ID',
  `Adesc` varchar(300) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `cmoney` decimal(9,2) DEFAULT '0.00' COMMENT '变化金额',
  `kmoney` decimal(9,2) DEFAULT '0.00' COMMENT '当前金额，充电、取款后剩下的',
  `lmoney` decimal(9,2) DEFAULT '0.00' COMMENT '累计收入金额，就是充电的收入',
  `qmoney` decimal(9,2) DEFAULT '0.00' COMMENT '累计取款金额',
  `cele` decimal(9,1) DEFAULT '0.0' COMMENT '变化电量',
  `eele` decimal(9,1) DEFAULT '0.0' COMMENT '累计充电量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_sys_moneynote
-- ----------------------------

-- ----------------------------
-- Table structure for `db_sys_note`
-- ----------------------------
DROP TABLE IF EXISTS `db_sys_note`;
CREATE TABLE `db_sys_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(50) DEFAULT NULL,
  `login_ip` varchar(50) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `login_os` varchar(50) DEFAULT NULL,
  `login_ie` varchar(50) DEFAULT NULL,
  `act` varchar(255) DEFAULT NULL,
  `login_tab` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_sys_note
-- ----------------------------
INSERT INTO `db_sys_note` VALUES ('1', 'admin', '192.168.1.66', '2017-11-22 08:59:06', 'Windows 7', 'Firefox57.0', '登入成功', 'sys_admin');
INSERT INTO `db_sys_note` VALUES ('2', 'admin', '192.168.1.66', '2017-11-25 19:43:49', 'Windows 7', 'Firefox57.0', '登入成功', 'sys_admin');
INSERT INTO `db_sys_note` VALUES ('3', 'admin', '219.137.64.40', '2017-11-27 19:11:33', 'Windows 7', 'Firefox57.0', '登出系统', '');
INSERT INTO `db_sys_note` VALUES ('4', 'admin', '219.137.64.40', '2017-11-27 19:11:49', 'Windows 7', 'Firefox57.0', '登入成功', 'sys_admin');
INSERT INTO `db_sys_note` VALUES ('5', 'admin', '219.137.64.40', '2017-12-02 18:30:23', 'Windows 7', 'Firefox57.0', '登入成功', 'sys_admin');
INSERT INTO `db_sys_note` VALUES ('6', 'admin', '219.137.64.40', '2017-12-03 12:57:03', 'Windows 7', 'Firefox57.0', '登入成功', 'sys_admin');
INSERT INTO `db_sys_note` VALUES ('7', '-', '219.137.66.58', '2017-12-04 17:08:57', 'Windows 7', 'Firefox57.0', '验证码错误', 'sys_admin');
INSERT INTO `db_sys_note` VALUES ('8', '-', '219.137.66.58', '2017-12-04 17:09:04', 'Windows 7', 'Firefox57.0', '验证码错误', 'sys_admin');
INSERT INTO `db_sys_note` VALUES ('9', 'admin', '219.137.66.58', '2017-12-04 17:09:14', 'Windows 7', 'Firefox57.0', '登入成功', 'sys_admin');
INSERT INTO `db_sys_note` VALUES ('10', 'admin', '219.137.66.58', '2017-12-06 13:19:10', 'Windows 7', 'Firefox57.0', '登入成功', 'sys_admin');

-- ----------------------------
-- Table structure for `db_sys_site`
-- ----------------------------
DROP TABLE IF EXISTS `db_sys_site`;
CREATE TABLE `db_sys_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitename` varchar(200) DEFAULT NULL,
  `siteWeb` varchar(100) DEFAULT NULL,
  `ver` int(11) DEFAULT NULL,
  `lock_ip` longtext,
  `cloud_url` varchar(100) DEFAULT NULL,
  `cloud_port` int(6) DEFAULT NULL,
  `islink` tinyint(4) DEFAULT NULL,
  `androidver` int(11) DEFAULT NULL,
  `androidurl` varchar(100) DEFAULT NULL,
  `iosver` int(11) DEFAULT NULL,
  `iosurl` varchar(100) DEFAULT NULL,
  `smtp` varchar(100) DEFAULT NULL,
  `mail` varchar(100) DEFAULT NULL,
  `mailpwd` varchar(20) DEFAULT NULL,
  `tel` varchar(20) DEFAULT '' COMMENT '联系方式',
  `smoney` int(15) DEFAULT '0' COMMENT '累计收入金额',
  `stime` int(20) DEFAULT '0' COMMENT '累计充电时间秒',
  `snum` int(9) DEFAULT '0' COMMENT '累计充电次数',
  `sele` int(21) DEFAULT '0' COMMENT '累计充电量',
  `money` int(12) DEFAULT '0' COMMENT '实际金额',
  `tmoney` int(12) DEFAULT '0' COMMENT '取款总金额',
  `tnum` int(9) DEFAULT '0' COMMENT '取款总次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_sys_site
-- ----------------------------
INSERT INTO `db_sys_site` VALUES ('4', '1111', 'www.vmuui.com', '0', '172.16.88.61', '172.16.88.190', '8282', '1', '1', 'www.android.com', '1', 'www.ios.com', 'smtp.163.com', 'qq469100943@163.com', '701789', '13829719806', '0', '1269376', '45', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `db_sys_userinfo`
-- ----------------------------
DROP TABLE IF EXISTS `db_sys_userinfo`;
CREATE TABLE `db_sys_userinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(50) DEFAULT NULL,
  `upwd` varchar(50) DEFAULT NULL,
  `utype` tinyint(1) DEFAULT '1' COMMENT '用户类型1微信，2支付宝',
  `openid` varchar(50) DEFAULT NULL COMMENT 'openid',
  `sessionid` varchar(50) DEFAULT '',
  `nickname` varchar(50) CHARACTER SET utf8 DEFAULT '未授权',
  `headimg` varchar(200) DEFAULT '/resources/headimg.jpg' COMMENT '头像',
  `addtime` datetime DEFAULT NULL,
  `lastaddtime` datetime DEFAULT NULL,
  `ucheck` tinyint(4) DEFAULT '1',
  `smoney` int(12) DEFAULT '0' COMMENT '累计充电金额',
  `stime` int(20) DEFAULT '0' COMMENT '累计充电时间秒',
  `snum` int(9) DEFAULT '0' COMMENT '累计充电次数',
  `sele` int(21) DEFAULT '0' COMMENT '累计充电量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of db_sys_userinfo
-- ----------------------------
INSERT INTO `db_sys_userinfo` VALUES ('1', '13829719806', 'e10adc3949ba59abbe56e057f20f883e', '1', null, 'dca26ceb8a1373de9f2f28773e4b8d59', '真实姓名', '/resources/headimg.jpg', '2017-05-17 11:14:54', '2017-10-22 03:10:35', '1', '0', '0', '0', '0');
INSERT INTO `db_sys_userinfo` VALUES ('2', '18665063143', '1a5a14d83f6abb3315385a18de354cd1', '1', null, '567bac9cb90c8ecc7ff70a32fd1c6677', '18665063143', '/resources/headimg.jpg', '2017-05-21 01:50:55', '2017-10-17 08:09:49', '1', '0', '0', '0', '0');
INSERT INTO `db_sys_userinfo` VALUES ('3', null, null, '1', 'oxGXz0PYwfePIcftenPx3c8DCRvo', 'OKfIQLP7AaMjG/wZL4tlGg==', '陆佳利', 'https://wx.qlogo.cn/mmopen/vi_32/N1DKiaSbrcCdDjTZgVDhyRXm2t2icSFj071LzESH3N6bIdWLEZbKA8XotwxTbIW8bSffWRpPpurUcfscFQprib3Og/0', '2017-11-01 13:50:58', '2017-12-07 07:46:28', '1', '0', '1269376', '45', '0');

-- ----------------------------
-- Table structure for `db_temp`
-- ----------------------------
DROP TABLE IF EXISTS `db_temp`;
CREATE TABLE `db_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `No` varchar(22) CHARACTER SET utf8 DEFAULT '' COMMENT '订单号',
  `mid` int(20) DEFAULT '0' COMMENT '产生订单ID，tempmoney的id',
  `mname` varchar(80) CHARACTER SET utf8 DEFAULT '' COMMENT '充值时订单名称tempmoney中',
  `bid` int(11) DEFAULT '0' COMMENT '商家id',
  `bname` varchar(80) CHARACTER SET utf8 DEFAULT '' COMMENT '商家名称',
  `uid` int(11) DEFAULT '0' COMMENT '用户ID',
  `uname` varchar(50) CHARACTER SET utf8 DEFAULT '' COMMENT '充电用户名',
  `pid` int(11) DEFAULT NULL COMMENT '桩id',
  `pname` varchar(80) CHARACTER SET utf8 DEFAULT '' COMMENT '桩名',
  `sid` int(11) DEFAULT NULL COMMENT '站点ID',
  `sname` varchar(80) CHARACTER SET utf8 DEFAULT NULL COMMENT '站点名称',
  `uint` int(6) DEFAULT '0' COMMENT '充电单价',
  `smoney` int(8) DEFAULT '0' COMMENT '充值总数',
  `tmoney` int(8) DEFAULT '0' COMMENT '退款总金额',
  `money` int(8) DEFAULT '0' COMMENT '实际金额',
  `elecount` int(10) DEFAULT '0' COMMENT '开始电量',
  `eleend` int(10) DEFAULT '0' COMMENT '结束电量',
  `cpower` int(10) DEFAULT '0' COMMENT '充电度数',
  `isclose` tinyint(4) DEFAULT '0' COMMENT '是否结算，结算后才出现订单，标识该信息是不是有效的',
  `isenable` tinyint(4) DEFAULT '0' COMMENT '充电开始前的标识，0为平台写入，1被桩修改。。',
  `isstatus` tinyint(4) DEFAULT '0' COMMENT '标识充电状态，0正常，1过压保护',
  `endcode` tinyint(4) DEFAULT '0' COMMENT '结束代号来源,也就是触发结束充电的原因',
  `endtxt` varchar(80) CHARACTER SET utf8 DEFAULT '' COMMENT '停止充电失败描述',
  `endfacode` tinyint(4) DEFAULT '0' COMMENT '结束失败代号',
  `endfatxt` varchar(80) CHARACTER SET utf8 DEFAULT '' COMMENT '结束失败说明',
  `startcode` tinyint(4) DEFAULT '0' COMMENT '触发充电失败代号,充电失败的原因',
  `starterrtxt` varchar(80) CHARACTER SET utf8 DEFAULT '' COMMENT '开启充电失败原因',
  `addtime` datetime DEFAULT NULL,
  `lasttime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of db_temp
-- ----------------------------
INSERT INTO `db_temp` VALUES ('1', 'RIC-A6VM8YK9LM', '1', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2075', '0', '1', '1', '0', '50', '', '0', '', '0', '', '2017-11-21 19:07:11', '2017-11-21 07:10:43');
INSERT INTO `db_temp` VALUES ('2', 'RIC-MP728ENHDA', '2', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2075', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-21 19:11:45', '2017-11-21 19:14:13');
INSERT INTO `db_temp` VALUES ('3', 'RIC-MTJG0VDN8C', '3', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2075', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-21 19:14:20', '2017-11-21 19:15:18');
INSERT INTO `db_temp` VALUES ('4', 'RIC-A8A7SIM7J4', '4', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2075', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-21 19:15:25', '2017-11-21 19:16:11');
INSERT INTO `db_temp` VALUES ('5', 'RIC-PI8VOK75IS', '5', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2075', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-21 19:21:48', '2017-11-21 19:21:52');
INSERT INTO `db_temp` VALUES ('6', 'RIC-KL18KQVTUN', '6', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2075', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-21 19:36:34', '2017-11-21 19:36:37');
INSERT INTO `db_temp` VALUES ('7', 'RIC-VYDALYI9H6', '7', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2075', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-21 19:36:44', '2017-11-21 19:37:30');
INSERT INTO `db_temp` VALUES ('8', 'RIC-POFBIM1TQV', '8', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2075', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-21 19:37:41', '2017-11-21 20:38:44');
INSERT INTO `db_temp` VALUES ('9', 'RIC-8ZTUHLV1QH', '9', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2075', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-21 20:39:00', '2017-11-21 20:39:24');
INSERT INTO `db_temp` VALUES ('10', 'RIC-257T6RRYXO', '10', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2075', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-21 20:39:35', '2017-11-26 07:06:29');
INSERT INTO `db_temp` VALUES ('11', 'RIC-QH52ZEKEFV', '11', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '25', '登录结算', '0', '', '0', '', '2017-11-22 08:58:38', '2017-11-24 20:30:57');
INSERT INTO `db_temp` VALUES ('12', 'RIC-NE4IK80CDL', '12', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '0', '0', '0', '1', '1', '0', '0', '', '0', '', '26', '充电反馈信号超时', '2017-11-24 20:31:25', '2017-11-24 20:31:26');
INSERT INTO `db_temp` VALUES ('13', 'RIC-8F1P0N0UD9', '13', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-24 20:32:38', '2017-11-24 20:32:43');
INSERT INTO `db_temp` VALUES ('14', 'RIC-D8WS1ITB9S', '14', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-24 20:32:49', '2017-11-24 20:32:52');
INSERT INTO `db_temp` VALUES ('15', 'RIC-00SE3RE9NW', '15', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-24 20:33:24', '2017-11-24 20:33:27');
INSERT INTO `db_temp` VALUES ('16', 'RIC-21ZAX6LOMJ', '16', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-24 20:33:40', '2017-11-24 20:36:16');
INSERT INTO `db_temp` VALUES ('17', 'RIC-G25W1LL6HK', '17', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-24 20:36:28', '2017-11-25 17:35:21');
INSERT INTO `db_temp` VALUES ('18', 'RIC-4XXD2KRJH3', '18', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '', '0', '', '0', '', '2017-11-25 17:35:32', '2017-11-25 17:35:32');
INSERT INTO `db_temp` VALUES ('19', 'RIC-1JPAR3407U', '19', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 19:49:41', '2017-11-25 19:49:48');
INSERT INTO `db_temp` VALUES ('20', 'RIC-XWTCUPG9XJ', '20', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 19:51:54', '2017-11-25 19:51:57');
INSERT INTO `db_temp` VALUES ('21', 'RIC-HM7K60NSNE', '21', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 19:53:01', '2017-11-25 19:53:19');
INSERT INTO `db_temp` VALUES ('22', 'RIC-W0MOPRFWF4', '22', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 19:53:25', '2017-11-25 19:53:28');
INSERT INTO `db_temp` VALUES ('23', 'RIC-QY7B6X2RJA', '23', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 19:56:06', '2017-11-25 19:56:13');
INSERT INTO `db_temp` VALUES ('24', 'RIC-VIN21H67Y8', '24', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 19:56:41', '2017-11-25 19:56:50');
INSERT INTO `db_temp` VALUES ('25', 'RIC-CA115Z9798', '25', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 20:08:44', '2017-11-25 20:08:48');
INSERT INTO `db_temp` VALUES ('26', 'RIC-K3BS6I5VCX', '26', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 20:08:53', '2017-11-25 20:08:55');
INSERT INTO `db_temp` VALUES ('27', 'RIC-EUASRE2EFX', '27', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '0', '0', '0', '1', '0', '0', '50', '', '0', '', '0', '', '2017-11-25 21:01:56', '2017-11-25 21:02:04');
INSERT INTO `db_temp` VALUES ('28', 'RIC-0W39L15K9Q', '28', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 21:29:28', '2017-11-25 21:29:38');
INSERT INTO `db_temp` VALUES ('29', 'RIC-5QVFFM6EAD', '29', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2076', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 21:29:43', '2017-11-25 21:29:47');
INSERT INTO `db_temp` VALUES ('30', 'RIC-P70A42ZB4E', '30', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2076', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-25 21:29:52', '2017-11-26 14:08:07');
INSERT INTO `db_temp` VALUES ('31', 'RIC-GMV6N0VLYR', '31', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-26 14:08:23', '2017-11-26 14:08:31');
INSERT INTO `db_temp` VALUES ('32', 'RIC-ODJ3ELQXNX', '32', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '', '0', '', '0', '', '2017-11-26 14:08:38', '2017-11-26 14:08:39');
INSERT INTO `db_temp` VALUES ('33', 'RIC-DC0H3GTE7T', '33', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-26 17:07:24', '2017-11-26 17:07:27');
INSERT INTO `db_temp` VALUES ('34', 'RIC-LHOH3JDPO5', '34', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '25', '登录结算', '0', '正常关闭', '0', '', '2017-11-26 17:13:00', '2017-11-26 18:41:15');
INSERT INTO `db_temp` VALUES ('35', 'RIC-FSLA3BOL67', '35', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-26 19:25:21', '2017-11-27 09:01:14');
INSERT INTO `db_temp` VALUES ('36', 'RIC-9AALJPOOVX', '36', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-11-27 09:01:28', '2017-11-27 09:01:32');
INSERT INTO `db_temp` VALUES ('37', 'RIC-G4VR06V8UH', '37', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '25', '登录结算', '0', '', '0', '', '2017-11-27 09:10:19', '2017-12-03 17:29:32');
INSERT INTO `db_temp` VALUES ('38', 'RIC-RZSL26OUES', '38', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-03 17:43:12', '2017-12-03 17:43:48');
INSERT INTO `db_temp` VALUES ('39', 'RIC-OQP2Y4FVNM', '39', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-03 17:44:01', '2017-12-03 17:44:06');
INSERT INTO `db_temp` VALUES ('40', 'RIC-55HH5ZGZIO', '40', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-03 17:44:11', '2017-12-03 17:44:16');
INSERT INTO `db_temp` VALUES ('41', 'RIC-2BD6XE00TI', '41', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-03 17:44:20', '2017-12-03 17:44:24');
INSERT INTO `db_temp` VALUES ('42', 'RIC-FYCTJUI1EG', '42', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2077', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-03 18:07:51', '2017-12-03 18:07:56');
INSERT INTO `db_temp` VALUES ('43', 'RIC-BORSA8N1CO', '43', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2077', '2078', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-03 18:23:54', '2017-12-04 16:42:54');
INSERT INTO `db_temp` VALUES ('44', 'RIC-3Y738ZUWM6', '44', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2078', '2078', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-04 16:43:07', '2017-12-04 16:47:41');
INSERT INTO `db_temp` VALUES ('45', 'RIC-8H539XTQRG', '45', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2078', '2078', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-04 16:48:20', '2017-12-05 09:07:39');
INSERT INTO `db_temp` VALUES ('46', 'RIC-ZKFUCJ3Z0I', '46', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2078', '2078', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-05 09:08:03', '2017-12-05 14:46:13');
INSERT INTO `db_temp` VALUES ('47', 'RIC-NMLRJTPEKI', '47', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2078', '2079', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-05 14:46:40', '2017-12-06 14:01:23');
INSERT INTO `db_temp` VALUES ('48', 'RIC-5RVF604YQG', '48', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '1000', '0', '2079', '2079', '0', '1', '1', '0', '0', '平台停止成功', '0', '正常关闭', '0', '', '2017-12-06 14:01:42', '2017-12-06 20:42:40');
INSERT INTO `db_temp` VALUES ('49', 'RIC-FQHO8XGIFP', '49', '小程序测试充值', '71', '13829719806', '3', '陆佳利', '1', 'VM001', '1', '广州大观中路充电站', '1', '1000', '0', '1000', '2079', '2080', '0', '0', '1', '0', '0', '', '0', '', '0', '', '2017-12-06 20:42:51', '2017-12-07 04:29:41');

-- ----------------------------
-- Table structure for `db_tempmoney`
-- ----------------------------
DROP TABLE IF EXISTS `db_tempmoney`;
CREATE TABLE `db_tempmoney` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '充电用户ID',
  `orderid` varchar(30) NOT NULL COMMENT '订单编号',
  `paySign` varchar(100) NOT NULL,
  `package` varchar(100) NOT NULL,
  `noncestr` varchar(100) NOT NULL,
  `timestamp` varchar(30) NOT NULL,
  `addtime` datetime NOT NULL COMMENT '添加时间',
  `pid` int(11) DEFAULT '0' COMMENT '设备ID',
  `tit` varchar(100) DEFAULT '' COMMENT '订单说明',
  `bid` int(11) DEFAULT NULL,
  `money` int(9) DEFAULT '0' COMMENT '预充电金额',
  `cuint` int(7) DEFAULT '0' COMMENT '本订单充电单价',
  `status` tinyint(1) DEFAULT '0' COMMENT '订单是否有修改',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_tempmoney
-- ----------------------------
INSERT INTO `db_tempmoney` VALUES ('1', '3', 'RIC-A6VM8YK9LM', '68D2809C98819595209B380364705150', 'prepay_id=', '3xjoippm6e1yqmgfwsmilkyvmclg3qyi', '1511262431', '2017-11-21 19:07:11', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('2', '3', 'RIC-MP728ENHDA', '2C5B6BAC13557CD36A59486E4A745EE6', 'prepay_id=', 'mz87akl4ftu9r2f18807kuf1wk8nrth6', '1511262705', '2017-11-21 19:11:45', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('3', '3', 'RIC-MTJG0VDN8C', '8E79973A73A66E793E5951625248BFDB', 'prepay_id=', 'pvzwm0bfvnlrok3vtag3hn1thi5flsln', '1511262859', '2017-11-21 19:14:19', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('4', '3', 'RIC-A8A7SIM7J4', '2AA4C179930614EC40591CAF8AB07AC7', 'prepay_id=', 'ky5g8inykm4g54pc7pbcwmisdqcwxml6', '1511262924', '2017-11-21 19:15:24', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('5', '3', 'RIC-PI8VOK75IS', 'F3541323BD330275DBFD8D89F601834D', 'prepay_id=', '6fgs7xi94plt3czp30e89mlbipzqepkh', '1511263308', '2017-11-21 19:21:48', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('6', '3', 'RIC-KL18KQVTUN', '49EDD2A3D40C2AE4CD297263873FD25B', 'prepay_id=', 'w7bwonbzu15i1kzr6waq0n03epptdb7p', '1511264194', '2017-11-21 19:36:34', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('7', '3', 'RIC-VYDALYI9H6', '7F17A9CDF5D0C69C0F97DB604D62FD28', 'prepay_id=', 'qbyooealtaw920oguplq27c5tutyo8pj', '1511264204', '2017-11-21 19:36:44', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('8', '3', 'RIC-POFBIM1TQV', 'BA1472D057DCBF82D65D40F070E325E0', 'prepay_id=', '5be0nctokp6znvyl9dbnv22bgj465x3r', '1511264261', '2017-11-21 19:37:41', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('9', '3', 'RIC-8ZTUHLV1QH', 'D76C3A71761C96E168A12B3C8CE736DB', 'prepay_id=', 'oivjepmeusbyk1lit6us91ad4oaebrm7', '1511267939', '2017-11-21 20:38:59', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('10', '3', 'RIC-257T6RRYXO', '0FD408612B14CFA753BFE74EA61D6BA1', 'prepay_id=', 'id8he3dp7o8ib32gfgrgrtopgbz47yut', '1511267975', '2017-11-21 20:39:35', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('11', '3', 'RIC-QH52ZEKEFV', '9BEBCBF0473B564B7EB9E76007990C9D', 'prepay_id=', '3xsb0rwlp1ks4un0ns7ejtxc1afkpldy', '1511312318', '2017-11-22 08:58:38', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('12', '3', 'RIC-NE4IK80CDL', '44BEC1416BF714A48FD821360549484A', 'prepay_id=', '54f2x6l63culdx8y9yr8xxzncv280hqk', '1511526685', '2017-11-24 20:31:25', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('13', '3', 'RIC-8F1P0N0UD9', '4ABA63083AD4F3BCBCD55AC3E11C9707', 'prepay_id=', '1jdhqyvef2w3mu81k4hspmdiy17wjlge', '1511526758', '2017-11-24 20:32:38', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('14', '3', 'RIC-D8WS1ITB9S', 'D639A546493A287004D5DEAAEFA1F5D5', 'prepay_id=', 'xkwauxwvd0pyadndhsbcc7uwhzcxjg6w', '1511526769', '2017-11-24 20:32:49', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('15', '3', 'RIC-00SE3RE9NW', '65056AB92628E2767E643B97FCA4F060', 'prepay_id=', 'i02yt5v80fu9e582hgyv89bbwre3ly6a', '1511526804', '2017-11-24 20:33:24', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('16', '3', 'RIC-21ZAX6LOMJ', '529B0B60EAF212C1CA28DAB9E3DCA736', 'prepay_id=', 'y3od5os116cax2boo7vehs25ukx3tssu', '1511526820', '2017-11-24 20:33:40', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('17', '3', 'RIC-G25W1LL6HK', '8A8085C9B7379552169603FB836E4C05', 'prepay_id=', 'fiaizmpgcgqny51iubqm5p34vwmuqk91', '1511526988', '2017-11-24 20:36:28', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('18', '3', 'RIC-4XXD2KRJH3', 'B046171AAED5199D16D39808B4BB71A3', 'prepay_id=', 'infsx9eyonlclvpaoevaryexvn4zpisn', '1511602532', '2017-11-25 17:35:32', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('19', '3', 'RIC-1JPAR3407U', 'C970C7AD0978B893C1F5BB431D96FA62', 'prepay_id=', 'irxtg1ah2ufr6k3xgx6kqw5a6h2r0xl6', '1511610581', '2017-11-25 19:49:41', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('20', '3', 'RIC-XWTCUPG9XJ', '97A7E8778E8C58F5313A97D998C86C20', 'prepay_id=', '6j7p0lo8ljawdf9witwrn2dyvljx68wx', '1511610714', '2017-11-25 19:51:54', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('21', '3', 'RIC-HM7K60NSNE', '4B97DF1469490288FC090303989E5A4D', 'prepay_id=', 'xqphdq392rxmeo9d3m2gkh4mu51ecg1s', '1511610780', '2017-11-25 19:53:00', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('22', '3', 'RIC-W0MOPRFWF4', '4B39D576FA0450CEF6023D54D52F4FE2', 'prepay_id=', 'ywyzalts372ej8heqi42d4wsjczs6x3f', '1511610805', '2017-11-25 19:53:25', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('23', '3', 'RIC-QY7B6X2RJA', 'F0A7263DDDFCBEB016D83BB2ED7A9A16', 'prepay_id=', 'qya8az36nx8u8502td0vzw5pmlfo6jxg', '1511610966', '2017-11-25 19:56:06', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('24', '3', 'RIC-VIN21H67Y8', '1F5E06D300A44294390E846918C53613', 'prepay_id=', 'ztrirg0w94k2iaop8e03me02hn4y9bnp', '1511611001', '2017-11-25 19:56:41', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('25', '3', 'RIC-CA115Z9798', 'DAE262F64642299AE175E0BDD924C463', 'prepay_id=', '8e7vd92nsrtmjt6l9i8vqllgb5jqrg8w', '1511611724', '2017-11-25 20:08:44', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('26', '3', 'RIC-K3BS6I5VCX', '76DEED1CE70D761C00AA0F68C2646B66', 'prepay_id=', 'bqg9dcmv14r6nj3byc78lhptns1z0z5b', '1511611732', '2017-11-25 20:08:52', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('27', '3', 'RIC-EUASRE2EFX', '676473636F9D7D0BCF451CB2B2FBB262', 'prepay_id=', '4z59o1k75cqdk8pwpweowelhm3qsguas', '1511614916', '2017-11-25 21:01:56', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('28', '3', 'RIC-0W39L15K9Q', '8A947DB7EC6B11F7403B8A6DE8A410F4', 'prepay_id=', '3e6q4flmr1i1gaq2cpq8qpahmjml09c2', '1511616567', '2017-11-25 21:29:27', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('29', '3', 'RIC-5QVFFM6EAD', '10B2EB08CF0B4D63AFB86E69DE483414', 'prepay_id=', 'tf9oj2os66k5raqruu502ff45f3449rw', '1511616583', '2017-11-25 21:29:43', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('30', '3', 'RIC-P70A42ZB4E', 'C5379D6278897D909B016BD422966FD8', 'prepay_id=', 'iobjbgf6uxal9sv8o6xmrd2bui01jnv1', '1511616591', '2017-11-25 21:29:51', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('31', '3', 'RIC-GMV6N0VLYR', '5B566A963694E99A5A80F7B795E532BE', 'prepay_id=', 'yy6186w5tbj1c1n8jaiub85ptkc7liro', '1511676503', '2017-11-26 14:08:23', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('32', '3', 'RIC-ODJ3ELQXNX', '6FFC6E4C98B33797BE9898315AC091E7', 'prepay_id=', 'xm31r5ttk6w9gdajsf2ls532msan34zo', '1511676518', '2017-11-26 14:08:38', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('33', '3', 'RIC-DC0H3GTE7T', '32074C5A5FC6AB9EB8EA8296081E1A29', 'prepay_id=', 'lj7svcc9nm879mpqknyealnpzopihdce', '1511687244', '2017-11-26 17:07:24', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('34', '3', 'RIC-LHOH3JDPO5', '2D83EFD007D7E4E0059BCF41429EE1CE', 'prepay_id=', 'zferb9fs0dgt8pov9hpqlruthvauorhj', '1511687580', '2017-11-26 17:13:00', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('35', '3', 'RIC-FSLA3BOL67', '74B1049744DD46F96F6CA797BFFAF2AC', 'prepay_id=', '4k1n06js4x7i22x9bvly13dde4ts615s', '1511695520', '2017-11-26 19:25:20', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('36', '3', 'RIC-9AALJPOOVX', '519212BDEDC0FFE58BDCBBFAD97A5984', 'prepay_id=', '0s81t903jaumcjrcijasu39awzomvdct', '1511744487', '2017-11-27 09:01:27', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('37', '3', 'RIC-G4VR06V8UH', 'D7A0D2880017AC8871A78B9045C87D7D', 'prepay_id=', '7e1mt4ibldlqqsapofpm83bdkcexw6gu', '1511745019', '2017-11-27 09:10:19', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('38', '3', 'RIC-RZSL26OUES', '0C9C14E8D06A53FAEAE54B3D6BE8D32A', 'prepay_id=', 'u3gd02thpvl56fs9q9nkuwmom0giqsjg', '1512294192', '2017-12-03 17:43:12', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('39', '3', 'RIC-OQP2Y4FVNM', '32B67657E7DAFFD709BD908363A51CED', 'prepay_id=', 'as9lhvyjdyvjopv1dsuwto0u9ove2f35', '1512294241', '2017-12-03 17:44:01', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('40', '3', 'RIC-55HH5ZGZIO', '7E9A0A90A596CB9609A8385E0A0ABC36', 'prepay_id=', '4op7p99mnt9leunzyzp48xwvf1vuapql', '1512294251', '2017-12-03 17:44:11', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('41', '3', 'RIC-2BD6XE00TI', '586E6C2184F7F856EB2DD818EA7FAC82', 'prepay_id=', 'befaw9vwiru4n1lb1le7jn7du5mofamx', '1512294260', '2017-12-03 17:44:20', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('42', '3', 'RIC-FYCTJUI1EG', '34BD11B55B70D42107A45B8BCC60A77D', 'prepay_id=', '5dcbtzgilq6czot9cdf7090u7fj2490p', '1512295671', '2017-12-03 18:07:51', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('43', '3', 'RIC-BORSA8N1CO', 'A3DF576838CDFD136433D7A28972390C', 'prepay_id=', 'hqalgthna5qznit03jqp7x79xuivyiid', '1512296634', '2017-12-03 18:23:54', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('44', '3', 'RIC-3Y738ZUWM6', 'AAF807D6A13E7A110CA1ACD972017F8D', 'prepay_id=', 'sop2mzh4a61s8ssq3ol856ux5qr9qps7', '1512376987', '2017-12-04 16:43:07', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('45', '3', 'RIC-8H539XTQRG', '262302522CDF6E9E0F4D545898B1A50D', 'prepay_id=', '9ggy589rmq79z5q8wa253y9ibw8di1tl', '1512377300', '2017-12-04 16:48:20', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('46', '3', 'RIC-ZKFUCJ3Z0I', 'FA2ABC4F032604C266651202A7ACC5C2', 'prepay_id=', '7v3yevgcpz0k9te4ci0mtgl9lzaqe2y9', '1512436083', '2017-12-05 09:08:03', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('47', '3', 'RIC-NMLRJTPEKI', 'C1265AC75B6ADFA9581CB25457B67F11', 'prepay_id=', 'blwlsvgqoj8abjxo8fe5nmry2wxve8w5', '1512456400', '2017-12-05 14:46:40', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('48', '3', 'RIC-5RVF604YQG', '67EA7B2F0C9F3D15E9E8A42835F95B72', 'prepay_id=', '660ktffay246na039527nu50g8rgppji', '1512540102', '2017-12-06 14:01:42', '1', '小程序测试充值', '71', '1000', '1', '1');
INSERT INTO `db_tempmoney` VALUES ('49', '3', 'RIC-FQHO8XGIFP', '11710399B95157F964B3D542259A77F6', 'prepay_id=', '8m7aaxdujkiaxrkshcdm09zwnm43a7gp', '1512564171', '2017-12-06 20:42:51', '1', '小程序测试充值', '71', '1000', '1', '1');

-- ----------------------------
-- Table structure for `db_usou`
-- ----------------------------
DROP TABLE IF EXISTS `db_usou`;
CREATE TABLE `db_usou` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `No` varchar(30) DEFAULT NULL COMMENT '交易编号',
  `type` tinyint(4) DEFAULT '0' COMMENT '0充值，1充电',
  `uname` varchar(80) DEFAULT NULL COMMENT '操作用户',
  `Adesc` varchar(300) DEFAULT NULL COMMENT '操作说明',
  `cnum` decimal(9,2) DEFAULT NULL COMMENT '变化的金额',
  `enum` decimal(9,2) DEFAULT NULL COMMENT '最终可用金额',
  `lznum` decimal(9,2) DEFAULT '0.00' COMMENT '当前累计支出金额',
  `lsnum` decimal(9,2) DEFAULT '0.00' COMMENT '当前累计收入金额',
  `cele` decimal(12,1) DEFAULT '0.0' COMMENT '变化电量',
  `eele` decimal(12,1) DEFAULT '0.0' COMMENT '最终累计电量',
  `addtime` datetime DEFAULT NULL,
  `cid` int(15) DEFAULT '0' COMMENT 'temp id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=455 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_usou
-- ----------------------------
