/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50733
Source Host           : localhost:3306
Source Database       : mat_v2

Target Server Type    : MYSQL
Target Server Version : 50733
File Encoding         : 65001

Date: 2021-12-18 21:48:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `chats`
-- ----------------------------
DROP TABLE IF EXISTS `chats`;
CREATE TABLE `chats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) DEFAULT NULL,
  `sender` bigint(20) DEFAULT NULL,
  `receiver` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of chats
-- ----------------------------
INSERT INTO `chats` VALUES ('1', '1', '78', '79', '2021-10-11 16:47:15', null);
INSERT INTO `chats` VALUES ('5', '1', '78', '80', null, null);
INSERT INTO `chats` VALUES ('6', '2', '80', '81', null, null);
INSERT INTO `chats` VALUES ('7', '1', '81', '78', null, null);

-- ----------------------------
-- Table structure for `messages`
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) DEFAULT NULL,
  `chat_id` bigint(20) DEFAULT NULL,
  `sender` bigint(20) DEFAULT NULL,
  `receiver` bigint(20) DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `attachment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `seen` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of messages
-- ----------------------------
INSERT INTO `messages` VALUES ('1', '1', '1', '78', '79', 'sadas', null, '0', '2021-10-11 16:47:15', null);
INSERT INTO `messages` VALUES ('2', '2', '1', '79', '78', 'sad', null, '0', '2021-10-12 16:47:21', null);
INSERT INTO `messages` VALUES ('3', '1', '7', '78', '81', 'raz', null, '0', '2021-10-12 16:47:28', null);
INSERT INTO `messages` VALUES ('4', '1', '1', '78', '79', 'Raz 2', null, '0', '2021-10-14 21:40:53', null);
INSERT INTO `messages` VALUES ('5', '1', '5', '78', '80', 'ok', null, '0', '2021-10-14 00:11:20', null);
INSERT INTO `messages` VALUES ('6', '1', '1', '80', '81', 'sdsadsa', null, '0', '2021-10-07 00:11:26', null);
INSERT INTO `messages` VALUES ('7', '1', '5', '80', '78', 'ok 2', null, '0', '2021-10-15 00:08:22', null);
INSERT INTO `messages` VALUES ('8', '1', '1', '78', '80', 'Heloooooooooooo', null, '0', '2021-10-18 15:31:29', '2021-10-18 15:31:29');
INSERT INTO `messages` VALUES ('9', '1', '1', '100', '101', 'Heloooooooooooo', null, '0', '2021-10-18 15:32:08', '2021-10-18 15:32:08');
INSERT INTO `messages` VALUES ('10', '1', null, '100', null, 'Heloooooooooooo', null, '0', '2021-10-18 16:14:43', '2021-10-18 16:14:43');
INSERT INTO `messages` VALUES ('11', '1', null, '100', null, 'Heloooooooooooo', null, '0', '2021-10-18 16:15:22', '2021-10-18 16:15:22');
INSERT INTO `messages` VALUES ('12', '1', null, '100', null, 'Heloooooooooooo', null, '0', '2021-10-18 16:15:28', '2021-10-18 16:15:28');

-- ----------------------------
-- Table structure for `team_chats`
-- ----------------------------
DROP TABLE IF EXISTS `team_chats`;
CREATE TABLE `team_chats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_team_id` bigint(20) DEFAULT NULL,
  `to_team_id` bigint(20) DEFAULT NULL,
  `sender` bigint(20) DEFAULT NULL,
  `receiver` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of team_chats
-- ----------------------------
INSERT INTO `team_chats` VALUES ('1', '1', '100', '78', '82', '2021-10-24 12:46:53', '2021-10-24 12:46:53');
INSERT INTO `team_chats` VALUES ('2', '1', '2', '78', '81', '2021-10-26 13:53:45', '2021-10-26 13:53:45');
INSERT INTO `team_chats` VALUES ('3', '1', '2', null, null, '2021-10-27 14:34:18', '2021-10-27 14:34:18');
INSERT INTO `team_chats` VALUES ('4', '1', '2', '78', '79', '2021-10-27 14:35:23', '2021-10-27 14:35:23');

-- ----------------------------
-- Table structure for `team_messages`
-- ----------------------------
DROP TABLE IF EXISTS `team_messages`;
CREATE TABLE `team_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) DEFAULT NULL,
  `sender` bigint(20) DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `attachment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `seen` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of team_messages
-- ----------------------------
INSERT INTO `team_messages` VALUES ('1', '1', '78', 'sadas', null, null, '2021-10-11 16:47:15', null);
INSERT INTO `team_messages` VALUES ('2', '1', '78', 'sad', null, null, '2021-10-12 16:47:21', null);
INSERT INTO `team_messages` VALUES ('3', '2', '78', 'raz', null, null, '2021-10-12 16:47:28', null);
INSERT INTO `team_messages` VALUES ('4', '1', '80', 'aa', null, null, '2021-10-14 21:28:05', null);
INSERT INTO `team_messages` VALUES ('5', '1', '80', 'aaaa', null, null, '2021-10-14 22:28:12', null);
INSERT INTO `team_messages` VALUES ('6', '1', '100', 'Heloooooooooooo', null, null, '2021-10-18 16:15:55', '2021-10-18 16:15:55');
INSERT INTO `team_messages` VALUES ('7', '1', '100', 'Heloooooooooooo', null, null, '2021-10-18 16:18:13', '2021-10-18 16:18:13');

-- ----------------------------
-- Table structure for `team_to_team_messages`
-- ----------------------------
DROP TABLE IF EXISTS `team_to_team_messages`;
CREATE TABLE `team_to_team_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_chat_id` bigint(20) DEFAULT NULL,
  `from_team_id` bigint(20) DEFAULT NULL,
  `to_team_id` bigint(20) DEFAULT NULL,
  `sender` bigint(20) DEFAULT NULL,
  `body` longtext COLLATE utf8_unicode_ci,
  `attachment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `seen` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of team_to_team_messages
-- ----------------------------
INSERT INTO `team_to_team_messages` VALUES ('1', '1', '2', '1', '78', 'Heloooooooooooo', null, '1', '2021-10-24 12:50:15', '2021-10-28 13:21:40');
INSERT INTO `team_to_team_messages` VALUES ('2', '1', '2', '1', '78', 'Heloooooooooooo', null, '1', '2021-10-24 12:51:20', '2021-10-28 13:21:40');
INSERT INTO `team_to_team_messages` VALUES ('3', '2', '1', '2', '78', 'Heloooooooooooo', null, '1', '2021-10-24 13:19:51', '2021-10-28 13:21:40');
INSERT INTO `team_to_team_messages` VALUES ('4', '1', '1', '100', '78', 'Heloooooooooooo', null, '0', '2021-10-27 14:15:19', '2021-10-27 14:15:19');

-- ----------------------------
-- Table structure for `team_to_team_private_messages`
-- ----------------------------
DROP TABLE IF EXISTS `team_to_team_private_messages`;
CREATE TABLE `team_to_team_private_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_chat_id` bigint(20) DEFAULT NULL,
  `from_team_id` bigint(20) DEFAULT NULL,
  `to_team_id` bigint(20) DEFAULT NULL,
  `sender` bigint(20) DEFAULT NULL,
  `receiver` bigint(20) DEFAULT NULL,
  `body` longtext COLLATE utf8_unicode_ci,
  `attachment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `seen` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of team_to_team_private_messages
-- ----------------------------
INSERT INTO `team_to_team_private_messages` VALUES ('1', '1', '1', '100', '78', '101', 'Heloooooooooooo', null, '0', '2021-10-24 12:50:15', '2021-10-24 12:50:15');
INSERT INTO `team_to_team_private_messages` VALUES ('2', '1', '1', '100', '78', '102', 'Heloooooooooooo', null, '0', '2021-10-24 12:51:20', '2021-10-24 12:51:20');
INSERT INTO `team_to_team_private_messages` VALUES ('3', '1', '1', '100', '78', '103', 'Heloooooooooooo', null, '0', '2021-10-24 13:19:51', '2021-10-24 13:19:51');
INSERT INTO `team_to_team_private_messages` VALUES ('4', '1', '1', '2', '78', '79', 'okkkkkkkkk', null, '1', '2021-10-27 14:21:06', '2021-10-27 14:21:06');
INSERT INTO `team_to_team_private_messages` VALUES ('5', '1', '1', '2', '78', '79', 'okkkkkkkkk', null, '1', '2021-10-27 14:25:38', '2021-10-27 14:25:38');
INSERT INTO `team_to_team_private_messages` VALUES ('6', '3', '1', '2', '78', '79', 'okkkkkkkkk', null, '1', '2021-10-27 14:34:18', '2021-10-27 14:34:18');
INSERT INTO `team_to_team_private_messages` VALUES ('7', '4', '1', '2', '78', '79', 'okkkkkkkkk', null, '0', '2021-10-27 14:35:23', '2021-10-27 14:35:23');
INSERT INTO `team_to_team_private_messages` VALUES ('8', '4', '1', '2', '78', '79', 'okkkkkkkkk', null, '0', '2021-10-27 14:35:34', '2021-10-27 14:35:34');
INSERT INTO `team_to_team_private_messages` VALUES ('9', '4', '1', '2', '78', '79', 'okkkkkkkkk', null, '0', '2021-12-17 16:22:41', '2021-12-17 16:22:41');
INSERT INTO `team_to_team_private_messages` VALUES ('10', '4', '1', '2', '78', '79', 'okkkkkkkkk', null, '0', '2021-12-17 16:22:52', '2021-12-17 16:22:52');
INSERT INTO `team_to_team_private_messages` VALUES ('11', '4', '1', '2', '78', '79', 'okkkkkkkkk', null, '0', '2021-12-17 16:23:37', '2021-12-17 16:23:37');
