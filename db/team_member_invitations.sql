/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50733
Source Host           : localhost:3306
Source Database       : mat_v2

Target Server Type    : MYSQL
Target Server Version : 50733
File Encoding         : 65001

Date: 2021-12-18 18:34:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `team_member_invitations`
-- ----------------------------
DROP TABLE IF EXISTS `team_member_invitations`;
CREATE TABLE `team_member_invitations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) unsigned NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relationship` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Pending','Accepted','Canceled') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  KEY `team_member_invitations_team_id_foreign` (`team_id`),
  CONSTRAINT `team_member_invitations_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of team_member_invitations
-- ----------------------------
INSERT INTO `team_member_invitations` VALUES ('1', '1', 'raz.doict@gmail.com', null, 'dsf', 'dsf', '0', '2021-12-18 11:59:50', '2021-12-18 11:59:50', 'fds', 'dsa', 'Pending');
INSERT INTO `team_member_invitations` VALUES ('2', '1', 'raz.doict@gmail.com', null, 'dsf', 'dsf', '0', '2021-12-18 11:59:50', '2021-12-18 11:59:50', 'fds', 'dsa', 'Pending');
INSERT INTO `team_member_invitations` VALUES ('3', '1', 'raz.doict@gmail.com', null, 'dsf', 'dsfsadsa', '0', '2021-12-18 12:03:20', '2021-12-18 12:03:20', 'fds', 'dsa', 'Pending');
INSERT INTO `team_member_invitations` VALUES ('4', '1', 'raz.doict@gmail.com', null, 'dsf', 'dsfsadsadsad', '0', '2021-12-18 12:05:28', '2021-12-18 12:05:28', 'fds', 'dsa', 'Pending');
INSERT INTO `team_member_invitations` VALUES ('5', '1', 'raz.abcoder@sdsd.sds', null, 'dsf', 'dsfsadsadsad', '0', '2021-12-18 12:05:28', '2021-12-18 12:20:03', 'fds', 'dsa', 'Pending');
INSERT INTO `team_member_invitations` VALUES ('6', '1', 'raz.abcoder@sdsd.sds', null, 'dsf', 'dsfsadsadsad asdsadsa', '0', '2021-12-18 12:06:43', '2021-12-18 12:19:33', 'fds', 'dsa', 'Pending');
