-- ----------------------------
-- Tables
-- ----------------------------
DROP TABLE IF EXISTS `saipay_order`;
CREATE TABLE `saipay_order` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单ID',
    `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '订单号',
    `order_name` varchar(255) NOT NULL COMMENT '订单名称',
    `order_price` decimal(10,2) unsigned NOT NULL COMMENT '订单金额',
    `pay_price` decimal(10,2) unsigned NOT NULL COMMENT '支付金额',
    `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注留言',
    `pay_method` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付方式',
    `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '付款状态',
    `pay_time` datetime DEFAULT NULL COMMENT '付款时间',
    `trade_no` varchar(255) NOT NULL DEFAULT '0' COMMENT '第三方交易',
    `order_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '订单状态',
    `created_by` int(11) DEFAULT NULL COMMENT '创建者',
    `updated_by` int(11) DEFAULT NULL COMMENT '更新者',
    `create_time` datetime DEFAULT NULL COMMENT '创建时间',
    `update_time` datetime DEFAULT NULL COMMENT '修改时间',
    `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `order_no` (`order_no`)
) ENGINE=InnoDB AUTO_INCREMENT=1 COMMENT='订单记录表' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `saipay_bill`;
CREATE TABLE `saipay_bill` (
   `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
   `order_sn` varchar(255) DEFAULT NULL COMMENT '订单编号',
   `user_id` int(11) DEFAULT '0' COMMENT '用户编号',
   `pay_status` tinyint(1) DEFAULT '0' COMMENT '支付状态',
   `pay_method` tinyint(1) DEFAULT '1' COMMENT '支付类型',
   `money` decimal(10,2) DEFAULT NULL COMMENT '订单金额',
   `message` tinytext COMMENT '订单描述',
   `transaction_id` varchar(255) DEFAULT NULL COMMENT '交易单号',
   `extra` varchar(255) DEFAULT NULL COMMENT '备注',
   `create_time` datetime DEFAULT NULL COMMENT '创建时间',
   `update_time` datetime DEFAULT NULL COMMENT '修改时间',
   `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
   PRIMARY KEY (`id`),
   UNIQUE KEY `unx_order_sn` (`order_sn`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 COMMENT='账单记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of config
-- ----------------------------
INSERT INTO `eb_system_config_group` VALUES (NULL, '支付宝支付', 'alipay_config', '', 1, 1, now(), now(), NULL);
SET @id := LAST_INSERT_ID();
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'app_id', '', 'APPID', 'input', '', 100, '必填-支付宝分配的 app_id');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'app_secret_cert', '', '应用私钥', 'textarea', '', 100, '必填-应用私钥 字符串');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'app_public_cert_path', '', '应用公钥证书', 'uploadFile', '', 100, '必填-应用公钥证书 路径');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'alipay_public_cert_path', '', '支付宝公钥证书', 'uploadFile', '', 100, '必填-支付宝公钥证书 路径');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'alipay_root_cert_path', '', '支付宝根证书', 'uploadFile', '', 100, '必填-支付宝根证书 路径');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'return_url', '', '同步回调地址', 'input', '', 100, '');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'notify_url', '', '异步回调地址', 'input', '', 100, '');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mode', '1', '模式', 'radio', '[{\"label\":\"正常模式\",\"value\":\"0\"},{\"label\":\"沙箱模式\",\"value\":\"1\"},{\"label\":\"服务商模式\",\"value\":\"2\"}]', 100, '');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'service_provider_id', '', '服务商ID', 'input', '', 100, '服务商模式下的服务商 id，当 mode 为服务商模式时使用该参数');

INSERT INTO `eb_system_config_group` VALUES (NULL, '微信支付', 'wxpay_config', '', 1, 1, now(), now(), NULL);
SET @id := LAST_INSERT_ID();
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mch_id', '', '商户号', 'input', '', 100, '必填-商户号，服务商模式下为服务商商户号');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mch_secret_key', '', 'v3商户秘钥', 'input', '', 100, '必填-v3 商户秘钥');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mch_secret_cert', '', '商户私钥', 'uploadFile', '', 100, '必填-商户私钥 ');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mch_public_cert_path', '', '商户公钥证书', 'uploadFile', '', 100, '必填-商户公钥证书路径');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'notify_url', '', '微信回调url', 'input', '', 100, '必填-微信回调url');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mp_app_id', '', '公众号app_id', 'input', '', 100, '选填-公众号 的 app_id');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mini_app_id', '', '小程序app_id', 'input', '', 100, '选填-小程序 的 app_id');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'app_id', '', 'app_id', 'input', '', 100, '选填-app 的 app_id');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'sub_mp_app_id', '', '子公众号app_id', 'input', '', 100, '选填-服务商模式下，子公众号 的 app_id');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'sub_app_id', '', '子app的app_id', 'input', '', 100, '选填-服务商模式下，子 app 的 app_id');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'sub_mini_app_id', '', '子小程序app_id', 'input', '', 100, '选填-服务商模式下，子小程序 的 app_id');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'sub_mch_id', '', '子商户id', 'input', '', 100, '选填-服务商模式下，子商户id');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mode', '0', '模式', 'radio', '[\n  {\n    \"label\":\"正常模式\",\n    \"value\":\"0\"\n  },\n  {\n    \"label\":\"服务商模式\",\n    \"value\":\"2\"\n  }\n]', 100, '选填-默认为正常模式');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'type', 'mini', '关联类型', 'radio', '[{\"label\":\"小程序\", \"value\":\"mini\"},{\"label\":\"公众号\", \"value\":\"mp\"},{\"label\":\"APP\", \"value\":\"app\"}]', 100, '');

INSERT INTO `eb_system_config_group` VALUES (NULL, '银联支付', 'unipay_config', '', 1, 1, now(), now(), NULL);
SET @id := LAST_INSERT_ID();
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mch_id', '', '商户号', 'input', '', 100, '必填-商户号');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mch_secret_key', '', '商户密钥', 'input', '', 100, '选填-商户密钥：为银联条码支付综合前置平台配置');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mch_cert_path', '', '商户公私钥', 'uploadFile', '', 100, '必填-商户公私钥');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mch_cert_password', '', '商户公私钥密码', 'input', '', 100, '必填-商户公私钥密码');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'unipay_public_cert_path', '', '银联公钥证书', 'uploadFile', '', 100, '必填-银联公钥证书路径');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'return_url', '', '回调地址', 'input', '', 100, '必填');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'notify_url', '', '通知地址', 'input', '', 100, '必填');
INSERT INTO `eb_system_config` VALUES (NULL, @id, 'mode', '1', '模式', 'input', '', 100, '必填-固定为1');

-- ----------------------------
-- Records of dict
-- ----------------------------
INSERT INTO `eb_system_dict_type` VALUES (NULL, '支付方式', 'saipay_method', 1, '', 1, 1, now(), now(), NULL);
SET @id := LAST_INSERT_ID();
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '支付宝', '1', 'saipay_method', 100, 1, '', 1, 1, now(), now(), NULL);
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '微信支付', '2', 'saipay_method', 100, 1, '', 1, 1, now(), now(), NULL);
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '抖音支付', '3', 'saipay_method', 100, 2, '', 1, 1, now(), now(), NULL);
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '银联支付', '4', 'saipay_method', 100, 1, '', 1, 1, now(), now(), NULL);

INSERT INTO `eb_system_dict_type` VALUES (NULL, '订单状态', 'saipay_status', 1, '', 1, 1, now(), now(), NULL);
SET @id := LAST_INSERT_ID();
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '已下单', '1', 'saipay_status', 100, 1, '', 1, 1, now(), now(), NULL);
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '已支付', '2', 'saipay_status', 100, 1, '', 1, 1, now(), now(), NULL);
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '已取消', '3', 'saipay_status', 100, 1, '', 1, 1, now(), now(), NULL);
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '已发货', '4', 'saipay_status', 100, 1, '', 1, 1, now(), now(), NULL);
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '已完成', '5', 'saipay_status', 100, 1, '', 1, 1, now(), now(), NULL);

INSERT INTO `eb_system_dict_type` VALUES (NULL, '支付状态', 'saipay_pay', 1, '', 1, 1, now(), now(), NULL);
SET @id := LAST_INSERT_ID();
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '已支付', '1', 'saipay_pay', 100, 1, '', 1, 1, now(), now(), NULL);
INSERT INTO `eb_system_dict_data` VALUES (NULL, @id, '未支付', '2', 'saipay_pay', 100, 1, '', 1, 1, now(), now(), NULL);

-- ----------------------------
-- Table data for eb_system_menu
-- ----------------------------
INSERT INTO `eb_system_menu` VALUES (NULL, 0, '0', 'SAIPAY', 'saipay', 'IconSafe', 'saipay', '', NULL, 2, 1, 'M', 0, NULL, 1, 0, '', 1, 1, now(), now(), NULL);
SET @id := LAST_INSERT_ID();
SET @level := CONCAT('0', ',', @id);
INSERT INTO `eb_system_menu` VALUES (NULL, @id, @level, '订单记录', 'saipay/order', 'IconUnorderedList', 'saipay/order', 'saipay/order/index', NULL, 2, 1, 'M', 0, NULL, 1, 100, '', 1, 1, now(), now(), NULL);
INSERT INTO `eb_system_menu` VALUES (NULL, @id, @level, '账单记录', 'saipay/bill', 'IconUnorderedList', 'saipay/bill', 'saipay/bill/index', NULL, 2, 1, 'M', 0, NULL, 1, 100, '', 1, 1, now(), now(), NULL);
