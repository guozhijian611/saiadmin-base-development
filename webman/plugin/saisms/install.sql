-- ----------------------------
-- Table structure for saisms_config
-- ----------------------------
DROP TABLE IF EXISTS `saisms_config`;
CREATE TABLE `saisms_config`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `gateway` varchar(50) DEFAULT NULL COMMENT '网关标识',
  `config_name` varchar(50) DEFAULT NULL COMMENT '网关名称',
  `config` varchar(1000) DEFAULT NULL COMMENT '配置',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态',
  `sort` smallint(6) NULL DEFAULT 100 COMMENT '排序',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_by` int(11) NULL DEFAULT NULL COMMENT '创建人',
  `updated_by` int(11) NULL DEFAULT NULL COMMENT '更新人',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unx_gateway`(`gateway`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 COMMENT = '短信配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of saisms_config
-- ----------------------------
INSERT INTO `saisms_config` VALUES (1, 'aliyun', '阿里云', '{\"access_key_id\":\"\",\"access_key_secret\":\"\"}', 1, 100, NULL, 1, 1, '2024-08-19 21:03:33', '2024-08-20 23:43:46', NULL);
INSERT INTO `saisms_config` VALUES (2, 'qcloud', '腾讯云', '{\"sdk_app_id\":\"\",\"secret_id\":\"\"}', 1, 100, NULL, 1, 1, '2024-08-19 22:20:44', '2024-08-19 23:13:03', NULL);
INSERT INTO `saisms_config` VALUES (3, 'qiniu', '七牛云', '{\"secret_key\":\"\",\"access_key\":\"\"}', 1, 200, NULL, 1, 1, '2024-08-19 23:13:51', '2024-08-20 23:34:37', NULL);
INSERT INTO `saisms_config` VALUES (4, 'link', '凌凯短信', '{\"CorpID\":\"\",\"Pwd\":\"\"}', 1, 99, NULL, 1, 1, '2024-08-20 09:07:08', '2024-08-21 08:41:56', NULL);
INSERT INTO `saisms_config` VALUES (5, 'baidu', '百度云', '{\"ak\":\"\",\"sk\":\"\",\"invoke_id\":\"\",\"domain\":\"\"}', 1, 100, NULL, 1, 1, '2024-08-21 08:41:46', '2024-08-21 08:41:46', NULL);

-- ----------------------------
-- Table structure for saisms_record
-- ----------------------------
DROP TABLE IF EXISTS `saisms_record`;
CREATE TABLE `saisms_record`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `gateway` varchar(50) DEFAULT NULL COMMENT '网关',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `code` varchar(20) DEFAULT NULL COMMENT '验证码',
  `content` varchar(500) DEFAULT NULL COMMENT '短信内容',
  `status` varchar(20) DEFAULT NULL COMMENT '发送状态',
  `response` varchar(500) DEFAULT NULL COMMENT '返回结果',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 COMMENT = '短信记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for saisms_tag
-- ----------------------------
DROP TABLE IF EXISTS `saisms_tag`;
CREATE TABLE `saisms_tag`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `tag_name` varchar(50) DEFAULT NULL COMMENT '标签名称',
  `gateway` varchar(50) DEFAULT NULL COMMENT '网关标识',
  `sms_type` tinyint(1) NULL DEFAULT 1 COMMENT '短信类型',
  `template_id` varchar(255) DEFAULT NULL COMMENT '模板编号',
  `content` varchar(255) DEFAULT NULL COMMENT '短信内容',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_by` int(11) NULL DEFAULT NULL COMMENT '创建人',
  `updated_by` int(11) NULL DEFAULT NULL COMMENT '更新人',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 COMMENT = '短信标签' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of eb_system_menu
-- ----------------------------
INSERT INTO `eb_system_menu` VALUES (NULL, 0, '0', '短信平台', 'saisms', 'IconMobile', 'saisms', '', NULL, 2, 1, 'M', 0, NULL, 1, 10, '', 1, 1, '2024-08-19 20:50:30', '2024-08-21 09:11:35', NULL);
SET @id := LAST_INSERT_ID();
SET @level := CONCAT('0', ',', @id);
INSERT INTO `eb_system_menu` VALUES (NULL, @id, @level, '短信配置', 'app/saisms/config', 'IconSettings', 'app/saisms/config', 'saisms/config/index', NULL, 2, 1, 'M', 4, NULL, 1, 0, '', 1, 1, '2024-08-19 20:55:01', '2024-08-21 09:13:25', NULL);
SET @idone := LAST_INSERT_ID();
SET @levelone := CONCAT(@id, ',', @idone);
INSERT INTO `eb_system_menu` VALUES (NULL, @idone, @levelone, '短信配置列表', '/app/saisms/SmsConfig/index', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'index', 1, 0, NULL, 1, 1, '2024-08-19 20:55:01', '2024-08-19 20:55:01', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idone, @levelone, '短信配置保存', '/app/saisms/SmsConfig/save', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'save', 1, 0, NULL, 1, 1, '2024-08-19 20:55:01', '2024-08-19 20:55:01', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idone, @levelone, '短信配置更新', '/app/saisms/SmsConfig/update', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'update', 1, 0, NULL, 1, 1, '2024-08-19 20:55:01', '2024-08-19 20:55:01', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idone, @levelone, '短信配置读取', '/app/saisms/SmsConfig/read', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'read', 1, 0, NULL, 1, 1, '2024-08-19 20:55:01', '2024-08-19 20:55:01', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idone, @levelone, '短信配置修改状态', '/app/saisms/SmsConfig/changeStatus', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'changeStatus', 1, 0, NULL, 1, 1, '2024-08-19 20:55:01', '2024-08-19 20:55:01', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idone, @levelone, '短信配置删除', '/app/saisms/SmsConfig/destroy', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'destroy', 1, 0, NULL, 1, 1, '2024-08-19 20:55:01', '2024-08-19 20:55:01', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idone, @levelone, '短信配置回收', '/app/saisms/SmsConfig/recycle', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'recycle', 1, 0, NULL, 1, 1, '2024-08-19 20:55:01', '2024-08-19 20:55:01', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idone, @levelone, '短信配置恢复', '/app/saisms/SmsConfig/recovery', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'recovery', 1, 0, NULL, 1, 1, '2024-08-19 20:55:01', '2024-08-19 20:55:01', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idone, @levelone, '短信配置销毁', '/app/saisms/SmsConfig/realDestroy', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'realDestroy', 1, 0, NULL, 1, 1, '2024-08-19 20:55:01', '2024-08-19 20:55:01', NULL);

INSERT INTO `eb_system_menu` VALUES (NULL, @id, @level, '短信标签', 'app/saisms/tag', 'IconSelectAll', 'app/saisms/tag', 'saisms/tag/index', NULL, 2, 1, 'M', 6, NULL, 1, 0, '', 1, 1, '2024-08-19 21:00:17', '2024-08-21 09:13:59', NULL);
SET @idtwo := LAST_INSERT_ID();
SET @leveltwo := CONCAT(@id, ',', @idtwo);
INSERT INTO `eb_system_menu` VALUES (NULL, @idtwo, @leveltwo, '短信标签列表', '/app/saisms/SmsTag/index', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'index', 1, 0, NULL, 1, 1, '2024-08-19 21:00:17', '2024-08-19 21:00:17', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idtwo, @leveltwo, '短信标签保存', '/app/saisms/SmsTag/save', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'save', 1, 0, NULL, 1, 1, '2024-08-19 21:00:17', '2024-08-19 21:00:17', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idtwo, @leveltwo, '短信标签更新', '/app/saisms/SmsTag/update', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'update', 1, 0, NULL, 1, 1, '2024-08-19 21:00:17', '2024-08-19 21:00:17', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idtwo, @leveltwo, '短信标签读取', '/app/saisms/SmsTag/read', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'read', 1, 0, NULL, 1, 1, '2024-08-19 21:00:17', '2024-08-19 21:00:17', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idtwo, @leveltwo, '短信标签修改状态', '/app/saisms/SmsTag/changeStatus', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'changeStatus', 1, 0, NULL, 1, 1, '2024-08-19 21:00:17', '2024-08-19 21:00:17', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idtwo, @leveltwo, '短信标签删除', '/app/saisms/SmsTag/destroy', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'destroy', 1, 0, NULL, 1, 1, '2024-08-19 21:00:17', '2024-08-19 21:00:17', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idtwo, @leveltwo, '短信标签回收', '/app/saisms/SmsTag/recycle', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'recycle', 1, 0, NULL, 1, 1, '2024-08-19 21:00:17', '2024-08-19 21:00:17', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idtwo, @leveltwo, '短信标签恢复', '/app/saisms/SmsTag/recovery', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'recovery', 1, 0, NULL, 1, 1, '2024-08-19 21:00:17', '2024-08-19 21:00:17', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idtwo, @leveltwo, '短信标签销毁', '/app/saisms/SmsTag/realDestroy', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'realDestroy', 1, 0, NULL, 1, 1, '2024-08-19 21:00:17', '2024-08-19 21:00:17', NULL);

INSERT INTO `eb_system_menu` VALUES (NULL, @id, @level, '短信记录', 'app/saisms/record', 'IconList', 'app/saisms/record', 'saisms/record/index', NULL, 2, 1, 'M', 5, NULL, 1, 0, '', 1, 1, '2024-08-19 21:00:36', '2024-08-21 09:14:16', NULL);
SET @idthree := LAST_INSERT_ID();
SET @levelthree := CONCAT(@id, ',', @idthree);
INSERT INTO `eb_system_menu` VALUES (NULL, @idthree, @levelthree, '短信记录列表', '/app/saisms/SmsRecord/index', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'index', 1, 0, NULL, 1, 1, '2024-08-19 21:00:36', '2024-08-19 21:00:36', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idthree, @levelthree, '短信记录保存', '/app/saisms/SmsRecord/save', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'save', 1, 0, NULL, 1, 1, '2024-08-19 21:00:36', '2024-08-19 21:00:36', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idthree, @levelthree, '短信记录更新', '/app/saisms/SmsRecord/update', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'update', 1, 0, NULL, 1, 1, '2024-08-19 21:00:36', '2024-08-19 21:00:36', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idthree, @levelthree, '短信记录读取', '/app/saisms/SmsRecord/read', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'read', 1, 0, NULL, 1, 1, '2024-08-19 21:00:36', '2024-08-19 21:00:36', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idthree, @levelthree, '短信记录修改状态', '/app/saisms/SmsRecord/changeStatus', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'changeStatus', 1, 0, NULL, 1, 1, '2024-08-19 21:00:36', '2024-08-19 21:00:36', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idthree, @levelthree, '短信记录删除', '/app/saisms/SmsRecord/destroy', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'destroy', 1, 0, NULL, 1, 1, '2024-08-19 21:00:36', '2024-08-19 21:00:36', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idthree, @levelthree, '短信记录回收', '/app/saisms/SmsRecord/recycle', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'recycle', 1, 0, NULL, 1, 1, '2024-08-19 21:00:36', '2024-08-19 21:00:36', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idthree, @levelthree, '短信记录恢复', '/app/saisms/SmsRecord/recovery', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'recovery', 1, 0, NULL, 1, 1, '2024-08-19 21:00:36', '2024-08-19 21:00:36', NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @idthree, @levelthree, '短信记录销毁', '/app/saisms/SmsRecord/realDestroy', NULL, NULL, NULL, NULL, 1, 1, 'B', 0, 'realDestroy', 1, 0, NULL, 1, 1, '2024-08-19 21:00:36', '2024-08-19 21:00:36', NULL);
