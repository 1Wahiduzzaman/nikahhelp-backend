/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50733
Source Host           : localhost:3306
Source Database       : mat_v2

Target Server Type    : MYSQL
Target Server Version : 50733
File Encoding         : 65001

Date: 2021-12-25 18:36:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `all_notifications`
-- ----------------------------
DROP TABLE IF EXISTS `all_notifications`;
CREATE TABLE `all_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) DEFAULT NULL,
  `sender` bigint(20) DEFAULT NULL,
  `receiver` bigint(20) DEFAULT NULL,
  `title` varchar(511) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `seen` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of all_notifications
-- ----------------------------
INSERT INTO `all_notifications` VALUES ('1', '2', '82', '98', 'Test', 'Ok', '0', '2021-12-13 06:38:56', '2021-12-13 06:38:56');
INSERT INTO `all_notifications` VALUES ('2', '1', '101', '98', 'Test', 'Ok', '0', '2021-12-25 12:30:18', '2021-12-25 12:30:18');
INSERT INTO `all_notifications` VALUES ('3', '1', '101', '98', 'Test', 'Ok', '0', '2021-12-25 12:31:01', '2021-12-25 12:31:01');
INSERT INTO `all_notifications` VALUES ('4', '2', '101', '98', 'Test', 'Ok', '0', '2021-12-25 12:35:13', '2021-12-25 12:35:13');
