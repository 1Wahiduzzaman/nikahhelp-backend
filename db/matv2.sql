/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50733
Source Host           : localhost:3306
Source Database       : mat_v2

Target Server Type    : MYSQL
Target Server Version : 50733
File Encoding         : 65001

Date: 2021-12-19 16:46:19
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of all_notifications
-- ----------------------------
INSERT INTO `all_notifications` VALUES ('1', '2', '82', '98', 'Test', 'Ok', '0', '2021-12-13 06:38:56', '2021-12-13 06:38:56');

-- ----------------------------
-- Table structure for `block_lists`
-- ----------------------------
DROP TABLE IF EXISTS `block_lists`;
CREATE TABLE `block_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `block_by` bigint(20) NOT NULL,
  `block_for` bigint(20) DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single' COMMENT 'single or team',
  `block_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of block_lists
-- ----------------------------
INSERT INTO `block_lists` VALUES ('3', '1', '2', null, 'single', '2021-10-09', '2021-10-09 07:03:20', '2021-10-09 07:03:20', null);

-- ----------------------------
-- Table structure for `candidate_city`
-- ----------------------------
DROP TABLE IF EXISTS `candidate_city`;
CREATE TABLE `candidate_city` (
  `user_id` bigint(20) unsigned NOT NULL,
  `country_id` bigint(20) unsigned NOT NULL,
  `city_id` bigint(20) unsigned NOT NULL,
  `allow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=allowed,0=disallowed',
  UNIQUE KEY `candidate_city_unique` (`user_id`,`country_id`,`city_id`,`allow`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of candidate_city
-- ----------------------------

-- ----------------------------
-- Table structure for `candidate_country_user`
-- ----------------------------
DROP TABLE IF EXISTS `candidate_country_user`;
CREATE TABLE `candidate_country_user` (
  `user_id` bigint(20) NOT NULL,
  `candidate_pre_country_id` bigint(20) NOT NULL,
  `candidate_pre_city_id` bigint(20) DEFAULT NULL,
  `allow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=allowed,0=disallowed',
  UNIQUE KEY `candidate_country_user_unique` (`user_id`,`candidate_pre_country_id`,`candidate_pre_city_id`,`allow`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of candidate_country_user
-- ----------------------------

-- ----------------------------
-- Table structure for `candidate_images`
-- ----------------------------
DROP TABLE IF EXISTS `candidate_images`;
CREATE TABLE `candidate_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `image_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '[1=>avatar,2=>Main image, [3,8] => Additional Image[3,8]]',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_visibility` tinyint(4) NOT NULL DEFAULT '2' COMMENT '[1=>only me,2=>My team, 3=>connected team, 4 => everyone]',
  `disk` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate_images_user_id` (`user_id`),
  CONSTRAINT `candidate_images_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of candidate_images
-- ----------------------------

-- ----------------------------
-- Table structure for `candidate_information`
-- ----------------------------
DROP TABLE IF EXISTS `candidate_information`;
CREATE TABLE `candidate_information` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mobile_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_country_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT 'BD',
  `per_gender` tinyint(4) DEFAULT NULL COMMENT '1=Male,2=Female,3=Others,4=Do not disclose',
  `per_height` double(8,2) DEFAULT NULL,
  `per_employment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_education_level_id` bigint(20) unsigned DEFAULT NULL,
  `per_religion_id` bigint(20) unsigned NOT NULL DEFAULT '63',
  `per_occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_ethnicity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_mother_tongue` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_health_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_nationality` bigint(20) unsigned DEFAULT NULL,
  `per_country_of_birth` bigint(20) unsigned DEFAULT NULL,
  `per_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_current_residence_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_current_residence_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_county` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_telephone_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_post_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_marital_status` enum('single','married','divorced','divorced_with_children','separated','widowed','others') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `per_have_children` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=no,1=yes',
  `per_children` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Json value for children',
  `per_currently_living_with` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_willing_to_relocate` enum('1','2','3','4') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `per_smoker` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `per_language_speak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_hobbies_interests` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_food_cuisine_like` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_things_enjoy` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_thankfull_for` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_about` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pre_partner_age_min` tinyint(4) NOT NULL DEFAULT '18',
  `pre_partner_age_max` tinyint(4) NOT NULL DEFAULT '100',
  `pre_height_min` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '91.44',
  `pre_height_max` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '304.8',
  `pre_has_country_allow_preference` tinyint(1) DEFAULT '1',
  `pre_has_country_disallow_preference` tinyint(1) DEFAULT '0',
  `pre_partner_religions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '63',
  `pre_ethnicities` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pre_study_level_id` bigint(20) unsigned DEFAULT NULL,
  `pre_employment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pre_occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pre_preferred_divorcee` tinyint(1) NOT NULL DEFAULT '0',
  `pre_preferred_divorcee_child` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'divorcee with child',
  `pre_other_preference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pre_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pre_pros_part_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Initial phase, 2= partially complicated, 3= completed',
  `pre_strength_of_character_rate` tinyint(4) DEFAULT NULL,
  `pre_look_and_appearance_rate` tinyint(4) DEFAULT NULL,
  `pre_religiosity_or_faith_rate` tinyint(4) DEFAULT NULL,
  `pre_manners_socialskill_ethics_rate` tinyint(4) DEFAULT NULL,
  `pre_emotional_maturity_rate` tinyint(4) DEFAULT NULL,
  `pre_good_listener_rate` tinyint(4) DEFAULT NULL,
  `pre_good_talker_rate` tinyint(4) DEFAULT NULL,
  `pre_wiling_to_learn_rate` tinyint(4) DEFAULT NULL,
  `pre_family_social_status_rate` tinyint(4) DEFAULT NULL,
  `pre_employment_wealth_rate` tinyint(4) DEFAULT NULL,
  `pre_education_rate` tinyint(4) DEFAULT NULL,
  `pre_things_important_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Initial phase, 2= partially complicated, 3= completed',
  `fi_father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fi_father_profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fi_mother_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fi_mother_profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fi_siblings_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Siblings descriptions',
  `fi_country_of_origin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fi_family_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anybody_can_see` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `only_team_can_see` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `team_connection_can_see` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `per_avatar_url` varchar(2083) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_main_image_url` varchar(2083) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_publish` tinyint(4) NOT NULL DEFAULT '0',
  `data_input_status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `candidate_information_user_id_foreign` (`user_id`),
  KEY `candidate_information_per_religion_id_foreign` (`per_religion_id`),
  KEY `candidate_information_per_education_level_id_foreign` (`per_education_level_id`),
  KEY `candidate_information_pre_study_level_id_foreign` (`pre_study_level_id`),
  CONSTRAINT `candidate_information_per_education_level_id_foreign` FOREIGN KEY (`per_education_level_id`) REFERENCES `study_level` (`id`),
  CONSTRAINT `candidate_information_per_religion_id_foreign` FOREIGN KEY (`per_religion_id`) REFERENCES `religions` (`id`),
  CONSTRAINT `candidate_information_pre_study_level_id_foreign` FOREIGN KEY (`pre_study_level_id`) REFERENCES `study_level` (`id`),
  CONSTRAINT `candidate_information_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of candidate_information
-- ----------------------------
INSERT INTO `candidate_information` VALUES ('4', '78', 'ssa', 'sfds', 'afds', '2021-11-02', '543543543', 'BD', '2', '2.00', '2', '2', '2', '3', '7', '7', '7', '7', '799', '7', '77', '7', '7', '7', '8', '7', null, null, 'single', '0', '2', '2', '1', '0', '2', '2', '2', '2', '2', '2', '18', '100', '91.44', '304.8', '1', '0', '63', '2', '2', '2', '2', '0', '0', '2', '2', '1', '2', '2', '2', '0', '0', '0', '0', '0', '0', null, null, '1', null, null, null, null, null, null, null, '0', '0', '0', null, null, '0', '0');

-- ----------------------------
-- Table structure for `candidate_nationality_user`
-- ----------------------------
DROP TABLE IF EXISTS `candidate_nationality_user`;
CREATE TABLE `candidate_nationality_user` (
  `user_id` bigint(20) NOT NULL,
  `candidate_pre_country_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of candidate_nationality_user
-- ----------------------------

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
-- Table structure for `cities`
-- ----------------------------
DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=inactive, 1=active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_countries_cities` (`country_id`,`name`),
  CONSTRAINT `cities_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cities
-- ----------------------------

-- ----------------------------
-- Table structure for `countries`
-- ----------------------------
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=inactive, 1=active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of countries
-- ----------------------------
INSERT INTO `countries` VALUES ('1', 'US', 'United States', '1', null, null);
INSERT INTO `countries` VALUES ('2', 'CA', 'Canada', '1', null, null);
INSERT INTO `countries` VALUES ('3', 'AF', 'Afghanistan', '1', null, null);
INSERT INTO `countries` VALUES ('4', 'AL', 'Albania', '1', null, null);
INSERT INTO `countries` VALUES ('5', 'DZ', 'Algeria', '1', null, null);
INSERT INTO `countries` VALUES ('6', 'AS', 'American Samoa', '1', null, null);
INSERT INTO `countries` VALUES ('7', 'AD', 'Andorra', '1', null, null);
INSERT INTO `countries` VALUES ('8', 'AO', 'Angola', '1', null, null);
INSERT INTO `countries` VALUES ('9', 'AI', 'Anguilla', '1', null, null);
INSERT INTO `countries` VALUES ('10', 'AQ', 'Antarctica', '1', null, null);
INSERT INTO `countries` VALUES ('11', 'AG', 'Antigua and/or Barbuda', '1', null, null);
INSERT INTO `countries` VALUES ('12', 'AR', 'Argentina', '1', null, null);
INSERT INTO `countries` VALUES ('13', 'AM', 'Armenia', '1', null, null);
INSERT INTO `countries` VALUES ('14', 'AW', 'Aruba', '1', null, null);
INSERT INTO `countries` VALUES ('15', 'AU', 'Australia', '1', null, null);
INSERT INTO `countries` VALUES ('16', 'AT', 'Austria', '1', null, null);
INSERT INTO `countries` VALUES ('17', 'AZ', 'Azerbaijan', '1', null, null);
INSERT INTO `countries` VALUES ('18', 'BS', 'Bahamas', '1', null, null);
INSERT INTO `countries` VALUES ('19', 'BH', 'Bahrain', '1', null, null);
INSERT INTO `countries` VALUES ('20', 'BD', 'Bangladesh', '1', null, null);
INSERT INTO `countries` VALUES ('21', 'BB', 'Barbados', '1', null, null);
INSERT INTO `countries` VALUES ('22', 'BY', 'Belarus', '1', null, null);
INSERT INTO `countries` VALUES ('23', 'BE', 'Belgium', '1', null, null);
INSERT INTO `countries` VALUES ('24', 'BZ', 'Belize', '1', null, null);
INSERT INTO `countries` VALUES ('25', 'BJ', 'Benin', '1', null, null);
INSERT INTO `countries` VALUES ('26', 'BM', 'Bermuda', '1', null, null);
INSERT INTO `countries` VALUES ('27', 'BT', 'Bhutan', '1', null, null);
INSERT INTO `countries` VALUES ('28', 'BO', 'Bolivia', '1', null, null);
INSERT INTO `countries` VALUES ('29', 'BA', 'Bosnia and Herzegovina', '1', null, null);
INSERT INTO `countries` VALUES ('30', 'BW', 'Botswana', '1', null, null);
INSERT INTO `countries` VALUES ('31', 'BV', 'Bouvet Island', '1', null, null);
INSERT INTO `countries` VALUES ('32', 'BR', 'Brazil', '1', null, null);
INSERT INTO `countries` VALUES ('33', 'IO', 'British lndian Ocean Territory', '1', null, null);
INSERT INTO `countries` VALUES ('34', 'BN', 'Brunei Darussalam', '1', null, null);
INSERT INTO `countries` VALUES ('35', 'BG', 'Bulgaria', '1', null, null);
INSERT INTO `countries` VALUES ('36', 'BF', 'Burkina Faso', '1', null, null);
INSERT INTO `countries` VALUES ('37', 'BI', 'Burundi', '1', null, null);
INSERT INTO `countries` VALUES ('38', 'KH', 'Cambodia', '1', null, null);
INSERT INTO `countries` VALUES ('39', 'CM', 'Cameroon', '1', null, null);
INSERT INTO `countries` VALUES ('40', 'CV', 'Cape Verde', '1', null, null);
INSERT INTO `countries` VALUES ('41', 'KY', 'Cayman Islands', '1', null, null);
INSERT INTO `countries` VALUES ('42', 'CF', 'Central African Republic', '1', null, null);
INSERT INTO `countries` VALUES ('43', 'TD', 'Chad', '1', null, null);
INSERT INTO `countries` VALUES ('44', 'CL', 'Chile', '1', null, null);
INSERT INTO `countries` VALUES ('45', 'CN', 'China', '1', null, null);
INSERT INTO `countries` VALUES ('46', 'CX', 'Christmas Island', '1', null, null);
INSERT INTO `countries` VALUES ('47', 'CC', 'Cocos (Keeling) Islands', '1', null, null);
INSERT INTO `countries` VALUES ('48', 'CO', 'Colombia', '1', null, null);
INSERT INTO `countries` VALUES ('49', 'KM', 'Comoros', '1', null, null);
INSERT INTO `countries` VALUES ('50', 'CG', 'Congo', '1', null, null);
INSERT INTO `countries` VALUES ('51', 'CK', 'Cook Islands', '1', null, null);
INSERT INTO `countries` VALUES ('52', 'CR', 'Costa Rica', '1', null, null);
INSERT INTO `countries` VALUES ('53', 'HR', 'Croatia (Hrvatska)', '1', null, null);
INSERT INTO `countries` VALUES ('54', 'CU', 'Cuba', '1', null, null);
INSERT INTO `countries` VALUES ('55', 'CY', 'Cyprus', '1', null, null);
INSERT INTO `countries` VALUES ('56', 'CZ', 'Czech Republic', '1', null, null);
INSERT INTO `countries` VALUES ('57', 'CD', 'Democratic Republic of Congo', '1', null, null);
INSERT INTO `countries` VALUES ('58', 'DK', 'Denmark', '1', null, null);
INSERT INTO `countries` VALUES ('59', 'DJ', 'Djibouti', '1', null, null);
INSERT INTO `countries` VALUES ('60', 'DM', 'Dominica', '1', null, null);
INSERT INTO `countries` VALUES ('61', 'DO', 'Dominican Republic', '1', null, null);
INSERT INTO `countries` VALUES ('62', 'TP', 'East Timor', '1', null, null);
INSERT INTO `countries` VALUES ('63', 'EC', 'Ecudaor', '1', null, null);
INSERT INTO `countries` VALUES ('64', 'EG', 'Egypt', '1', null, null);
INSERT INTO `countries` VALUES ('65', 'SV', 'El Salvador', '1', null, null);
INSERT INTO `countries` VALUES ('66', 'GQ', 'Equatorial Guinea', '1', null, null);
INSERT INTO `countries` VALUES ('67', 'ER', 'Eritrea', '1', null, null);
INSERT INTO `countries` VALUES ('68', 'EE', 'Estonia', '1', null, null);
INSERT INTO `countries` VALUES ('69', 'ET', 'Ethiopia', '1', null, null);
INSERT INTO `countries` VALUES ('70', 'FK', 'Falkland Islands (Malvinas)', '1', null, null);
INSERT INTO `countries` VALUES ('71', 'FO', 'Faroe Islands', '1', null, null);
INSERT INTO `countries` VALUES ('72', 'FJ', 'Fiji', '1', null, null);
INSERT INTO `countries` VALUES ('73', 'FI', 'Finland', '1', null, null);
INSERT INTO `countries` VALUES ('74', 'FR', 'France', '1', null, null);
INSERT INTO `countries` VALUES ('75', 'FX', 'France, Metropolitan', '1', null, null);
INSERT INTO `countries` VALUES ('76', 'GF', 'French Guiana', '1', null, null);
INSERT INTO `countries` VALUES ('77', 'PF', 'French Polynesia', '1', null, null);
INSERT INTO `countries` VALUES ('78', 'TF', 'French Southern Territories', '1', null, null);
INSERT INTO `countries` VALUES ('79', 'GA', 'Gabon', '1', null, null);
INSERT INTO `countries` VALUES ('80', 'GM', 'Gambia', '1', null, null);
INSERT INTO `countries` VALUES ('81', 'GE', 'Georgia', '1', null, null);
INSERT INTO `countries` VALUES ('82', 'DE', 'Germany', '1', null, null);
INSERT INTO `countries` VALUES ('83', 'GH', 'Ghana', '1', null, null);
INSERT INTO `countries` VALUES ('84', 'GI', 'Gibraltar', '1', null, null);
INSERT INTO `countries` VALUES ('85', 'GR', 'Greece', '1', null, null);
INSERT INTO `countries` VALUES ('86', 'GL', 'Greenland', '1', null, null);
INSERT INTO `countries` VALUES ('87', 'GD', 'Grenada', '1', null, null);
INSERT INTO `countries` VALUES ('88', 'GP', 'Guadeloupe', '1', null, null);
INSERT INTO `countries` VALUES ('89', 'GU', 'Guam', '1', null, null);
INSERT INTO `countries` VALUES ('90', 'GT', 'Guatemala', '1', null, null);
INSERT INTO `countries` VALUES ('91', 'GN', 'Guinea', '1', null, null);
INSERT INTO `countries` VALUES ('92', 'GW', 'Guinea-Bissau', '1', null, null);
INSERT INTO `countries` VALUES ('93', 'GY', 'Guyana', '1', null, null);
INSERT INTO `countries` VALUES ('94', 'HT', 'Haiti', '1', null, null);
INSERT INTO `countries` VALUES ('95', 'HM', 'Heard and Mc Donald Islands', '1', null, null);
INSERT INTO `countries` VALUES ('96', 'HN', 'Honduras', '1', null, null);
INSERT INTO `countries` VALUES ('97', 'HK', 'Hong Kong', '1', null, null);
INSERT INTO `countries` VALUES ('98', 'HU', 'Hungary', '1', null, null);
INSERT INTO `countries` VALUES ('99', 'IS', 'Iceland', '1', null, null);
INSERT INTO `countries` VALUES ('100', 'IN', 'India', '1', null, null);
INSERT INTO `countries` VALUES ('101', 'ID', 'Indonesia', '1', null, null);
INSERT INTO `countries` VALUES ('102', 'IR', 'Iran (Islamic Republic of)', '1', null, null);
INSERT INTO `countries` VALUES ('103', 'IQ', 'Iraq', '1', null, null);
INSERT INTO `countries` VALUES ('104', 'IE', 'Ireland', '1', null, null);
INSERT INTO `countries` VALUES ('105', 'IL', 'Israel', '1', null, null);
INSERT INTO `countries` VALUES ('106', 'IT', 'Italy', '1', null, null);
INSERT INTO `countries` VALUES ('107', 'CI', 'Ivory Coast', '1', null, null);
INSERT INTO `countries` VALUES ('108', 'JM', 'Jamaica', '1', null, null);
INSERT INTO `countries` VALUES ('109', 'JP', 'Japan', '1', null, null);
INSERT INTO `countries` VALUES ('110', 'JO', 'Jordan', '1', null, null);
INSERT INTO `countries` VALUES ('111', 'KZ', 'Kazakhstan', '1', null, null);
INSERT INTO `countries` VALUES ('112', 'KE', 'Kenya', '1', null, null);
INSERT INTO `countries` VALUES ('113', 'KI', 'Kiribati', '1', null, null);
INSERT INTO `countries` VALUES ('114', 'KP', 'Korea, Democratic People\'s Republic of', '1', null, null);
INSERT INTO `countries` VALUES ('115', 'KR', 'Korea, Republic of', '1', null, null);
INSERT INTO `countries` VALUES ('116', 'KW', 'Kuwait', '1', null, null);
INSERT INTO `countries` VALUES ('117', 'KG', 'Kyrgyzstan', '1', null, null);
INSERT INTO `countries` VALUES ('118', 'LA', 'Lao People\'s Democratic Republic', '1', null, null);
INSERT INTO `countries` VALUES ('119', 'LV', 'Latvia', '1', null, null);
INSERT INTO `countries` VALUES ('120', 'LB', 'Lebanon', '1', null, null);
INSERT INTO `countries` VALUES ('121', 'LS', 'Lesotho', '1', null, null);
INSERT INTO `countries` VALUES ('122', 'LR', 'Liberia', '1', null, null);
INSERT INTO `countries` VALUES ('123', 'LY', 'Libyan Arab Jamahiriya', '1', null, null);
INSERT INTO `countries` VALUES ('124', 'LI', 'Liechtenstein', '1', null, null);
INSERT INTO `countries` VALUES ('125', 'LT', 'Lithuania', '1', null, null);
INSERT INTO `countries` VALUES ('126', 'LU', 'Luxembourg', '1', null, null);
INSERT INTO `countries` VALUES ('127', 'MO', 'Macau', '1', null, null);
INSERT INTO `countries` VALUES ('128', 'MK', 'Macedonia', '1', null, null);
INSERT INTO `countries` VALUES ('129', 'MG', 'Madagascar', '1', null, null);
INSERT INTO `countries` VALUES ('130', 'MW', 'Malawi', '1', null, null);
INSERT INTO `countries` VALUES ('131', 'MY', 'Malaysia', '1', null, null);
INSERT INTO `countries` VALUES ('132', 'MV', 'Maldives', '1', null, null);
INSERT INTO `countries` VALUES ('133', 'ML', 'Mali', '1', null, null);
INSERT INTO `countries` VALUES ('134', 'MT', 'Malta', '1', null, null);
INSERT INTO `countries` VALUES ('135', 'MH', 'Marshall Islands', '1', null, null);
INSERT INTO `countries` VALUES ('136', 'MQ', 'Martinique', '1', null, null);
INSERT INTO `countries` VALUES ('137', 'MR', 'Mauritania', '1', null, null);
INSERT INTO `countries` VALUES ('138', 'MU', 'Mauritius', '1', null, null);
INSERT INTO `countries` VALUES ('139', 'TY', 'Mayotte', '1', null, null);
INSERT INTO `countries` VALUES ('140', 'MX', 'Mexico', '1', null, null);
INSERT INTO `countries` VALUES ('141', 'FM', 'Micronesia, Federated States of', '1', null, null);
INSERT INTO `countries` VALUES ('142', 'MD', 'Moldova, Republic of', '1', null, null);
INSERT INTO `countries` VALUES ('143', 'MC', 'Monaco', '1', null, null);
INSERT INTO `countries` VALUES ('144', 'MN', 'Mongolia', '1', null, null);
INSERT INTO `countries` VALUES ('145', 'MS', 'Montserrat', '1', null, null);
INSERT INTO `countries` VALUES ('146', 'MA', 'Morocco', '1', null, null);
INSERT INTO `countries` VALUES ('147', 'MZ', 'Mozambique', '1', null, null);
INSERT INTO `countries` VALUES ('148', 'MM', 'Myanmar', '1', null, null);
INSERT INTO `countries` VALUES ('149', 'NA', 'Namibia', '1', null, null);
INSERT INTO `countries` VALUES ('150', 'NR', 'Nauru', '1', null, null);
INSERT INTO `countries` VALUES ('151', 'NP', 'Nepal', '1', null, null);
INSERT INTO `countries` VALUES ('152', 'NL', 'Netherlands', '1', null, null);
INSERT INTO `countries` VALUES ('153', 'AN', 'Netherlands Antilles', '1', null, null);
INSERT INTO `countries` VALUES ('154', 'NC', 'New Caledonia', '1', null, null);
INSERT INTO `countries` VALUES ('155', 'NZ', 'New Zealand', '1', null, null);
INSERT INTO `countries` VALUES ('156', 'NI', 'Nicaragua', '1', null, null);
INSERT INTO `countries` VALUES ('157', 'NE', 'Niger', '1', null, null);
INSERT INTO `countries` VALUES ('158', 'NG', 'Nigeria', '1', null, null);
INSERT INTO `countries` VALUES ('159', 'NU', 'Niue', '1', null, null);
INSERT INTO `countries` VALUES ('160', 'NF', 'Norfork Island', '1', null, null);
INSERT INTO `countries` VALUES ('161', 'MP', 'Northern Mariana Islands', '1', null, null);
INSERT INTO `countries` VALUES ('162', 'NO', 'Norway', '1', null, null);
INSERT INTO `countries` VALUES ('163', 'OM', 'Oman', '1', null, null);
INSERT INTO `countries` VALUES ('164', 'PK', 'Pakistan', '1', null, null);
INSERT INTO `countries` VALUES ('165', 'PW', 'Palau', '1', null, null);
INSERT INTO `countries` VALUES ('166', 'PA', 'Panama', '1', null, null);
INSERT INTO `countries` VALUES ('167', 'PG', 'Papua New Guinea', '1', null, null);
INSERT INTO `countries` VALUES ('168', 'PY', 'Paraguay', '1', null, null);
INSERT INTO `countries` VALUES ('169', 'PE', 'Peru', '1', null, null);
INSERT INTO `countries` VALUES ('170', 'PH', 'Philippines', '1', null, null);
INSERT INTO `countries` VALUES ('171', 'PN', 'Pitcairn', '1', null, null);
INSERT INTO `countries` VALUES ('172', 'PL', 'Poland', '1', null, null);
INSERT INTO `countries` VALUES ('173', 'PT', 'Portugal', '1', null, null);
INSERT INTO `countries` VALUES ('174', 'PR', 'Puerto Rico', '1', null, null);
INSERT INTO `countries` VALUES ('175', 'QA', 'Qatar', '1', null, null);
INSERT INTO `countries` VALUES ('176', 'SS', 'Republic of South Sudan', '1', null, null);
INSERT INTO `countries` VALUES ('177', 'RE', 'Reunion', '1', null, null);
INSERT INTO `countries` VALUES ('178', 'RO', 'Romania', '1', null, null);
INSERT INTO `countries` VALUES ('179', 'RU', 'Russian Federation', '1', null, null);
INSERT INTO `countries` VALUES ('180', 'RW', 'Rwanda', '1', null, null);
INSERT INTO `countries` VALUES ('181', 'KN', 'Saint Kitts and Nevis', '1', null, null);
INSERT INTO `countries` VALUES ('182', 'LC', 'Saint Lucia', '1', null, null);
INSERT INTO `countries` VALUES ('183', 'VC', 'Saint Vincent and the Grenadines', '1', null, null);
INSERT INTO `countries` VALUES ('184', 'WS', 'Samoa', '1', null, null);
INSERT INTO `countries` VALUES ('185', 'SM', 'San Marino', '1', null, null);
INSERT INTO `countries` VALUES ('186', 'ST', 'Sao Tome and Principe', '1', null, null);
INSERT INTO `countries` VALUES ('187', 'SA', 'Saudi Arabia', '1', null, null);
INSERT INTO `countries` VALUES ('188', 'SN', 'Senegal', '1', null, null);
INSERT INTO `countries` VALUES ('189', 'RS', 'Serbia', '1', null, null);
INSERT INTO `countries` VALUES ('190', 'SC', 'Seychelles', '1', null, null);
INSERT INTO `countries` VALUES ('191', 'SL', 'Sierra Leone', '1', null, null);
INSERT INTO `countries` VALUES ('192', 'SG', 'Singapore', '1', null, null);
INSERT INTO `countries` VALUES ('193', 'SK', 'Slovakia', '1', null, null);
INSERT INTO `countries` VALUES ('194', 'SI', 'Slovenia', '1', null, null);
INSERT INTO `countries` VALUES ('195', 'SB', 'Solomon Islands', '1', null, null);
INSERT INTO `countries` VALUES ('196', 'SO', 'Somalia', '1', null, null);
INSERT INTO `countries` VALUES ('197', 'ZA', 'South Africa', '1', null, null);
INSERT INTO `countries` VALUES ('198', 'GS', 'South Georgia South Sandwich Islands', '1', null, null);
INSERT INTO `countries` VALUES ('199', 'ES', 'Spain', '1', null, null);
INSERT INTO `countries` VALUES ('200', 'LK', 'Sri Lanka', '1', null, null);
INSERT INTO `countries` VALUES ('201', 'SH', 'St. Helena', '1', null, null);
INSERT INTO `countries` VALUES ('202', 'PM', 'St. Pierre and Miquelon', '1', null, null);
INSERT INTO `countries` VALUES ('203', 'SD', 'Sudan', '1', null, null);
INSERT INTO `countries` VALUES ('204', 'SR', 'Suriname', '1', null, null);
INSERT INTO `countries` VALUES ('205', 'SJ', 'Svalbarn and Jan Mayen Islands', '1', null, null);
INSERT INTO `countries` VALUES ('206', 'SZ', 'Swaziland', '1', null, null);
INSERT INTO `countries` VALUES ('207', 'SE', 'Sweden', '1', null, null);
INSERT INTO `countries` VALUES ('208', 'CH', 'Switzerland', '1', null, null);
INSERT INTO `countries` VALUES ('209', 'SY', 'Syrian Arab Republic', '1', null, null);
INSERT INTO `countries` VALUES ('210', 'TW', 'Taiwan', '1', null, null);
INSERT INTO `countries` VALUES ('211', 'TJ', 'Tajikistan', '1', null, null);
INSERT INTO `countries` VALUES ('212', 'TZ', 'Tanzania, United Republic of', '1', null, null);
INSERT INTO `countries` VALUES ('213', 'TH', 'Thailand', '1', null, null);
INSERT INTO `countries` VALUES ('214', 'TG', 'Togo', '1', null, null);
INSERT INTO `countries` VALUES ('215', 'TK', 'Tokelau', '1', null, null);
INSERT INTO `countries` VALUES ('216', 'TO', 'Tonga', '1', null, null);
INSERT INTO `countries` VALUES ('217', 'TT', 'Trinidad and Tobago', '1', null, null);
INSERT INTO `countries` VALUES ('218', 'TN', 'Tunisia', '1', null, null);
INSERT INTO `countries` VALUES ('219', 'TR', 'Turkey', '1', null, null);
INSERT INTO `countries` VALUES ('220', 'TM', 'Turkmenistan', '1', null, null);
INSERT INTO `countries` VALUES ('221', 'TC', 'Turks and Caicos Islands', '1', null, null);
INSERT INTO `countries` VALUES ('222', 'TV', 'Tuvalu', '1', null, null);
INSERT INTO `countries` VALUES ('223', 'UG', 'Uganda', '1', null, null);
INSERT INTO `countries` VALUES ('224', 'UA', 'Ukraine', '1', null, null);
INSERT INTO `countries` VALUES ('225', 'AE', 'United Arab Emirates', '1', null, null);
INSERT INTO `countries` VALUES ('226', 'GB', 'United Kingdom', '1', null, null);
INSERT INTO `countries` VALUES ('227', 'UM', 'United States minor outlying islands', '1', null, null);
INSERT INTO `countries` VALUES ('228', 'UY', 'Uruguay', '1', null, null);
INSERT INTO `countries` VALUES ('229', 'UZ', 'Uzbekistan', '1', null, null);
INSERT INTO `countries` VALUES ('230', 'VU', 'Vanuatu', '1', null, null);
INSERT INTO `countries` VALUES ('231', 'VA', 'Vatican City State', '1', null, null);
INSERT INTO `countries` VALUES ('232', 'VE', 'Venezuela', '1', null, null);
INSERT INTO `countries` VALUES ('233', 'VN', 'Vietnam', '1', null, null);
INSERT INTO `countries` VALUES ('234', 'VG', 'Virgin Islands (British)', '1', null, null);
INSERT INTO `countries` VALUES ('235', 'VI', 'Virgin Islands (U.S.)', '1', null, null);
INSERT INTO `countries` VALUES ('236', 'WF', 'Wallis and Futuna Islands', '1', null, null);
INSERT INTO `countries` VALUES ('237', 'EH', 'Western Sahara', '1', null, null);
INSERT INTO `countries` VALUES ('238', 'YE', 'Yemen', '1', null, null);
INSERT INTO `countries` VALUES ('239', 'YU', 'Yugoslavia', '1', null, null);
INSERT INTO `countries` VALUES ('240', 'ZR', 'Zaire', '1', null, null);
INSERT INTO `countries` VALUES ('241', 'ZM', 'Zambia', '1', null, null);
INSERT INTO `countries` VALUES ('242', 'ZW', 'Zimbabwe', '1', null, null);

-- ----------------------------
-- Table structure for `delete_reasons`
-- ----------------------------
DROP TABLE IF EXISTS `delete_reasons`;
CREATE TABLE `delete_reasons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `reason_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason_text` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delete_reasons_user_id_foreign` (`user_id`),
  KEY `delete_reasons_team_id_foreign` (`team_id`),
  CONSTRAINT `delete_reasons_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `delete_reasons_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of delete_reasons
-- ----------------------------

-- ----------------------------
-- Table structure for `failed_jobs`
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for `match_makers`
-- ----------------------------
DROP TABLE IF EXISTS `match_makers`;
CREATE TABLE `match_makers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_gender` tinyint(4) DEFAULT NULL COMMENT '1=Male,2=Female,3=Others,4=Do not disclose',
  `dob` date DEFAULT NULL,
  `per_occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_current_residence_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_current_residence_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_county` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_telephone_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_country_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT 'BD',
  `per_permanent_post_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_document_upload` tinyint(4) DEFAULT NULL COMMENT '0=No,1=Yes',
  `ver_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_document_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_document_frontside` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_document_backside` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_mobile_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_or_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_maker_duration` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_qt` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_six_month_match_qt` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_per_county` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_community` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `have_previous_experience` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_experience` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `can_share_last_three_match` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_one` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_two` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_three` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_avatar_url` varchar(2083) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_main_image_url` varchar(2083) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anybody_can_see` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `only_team_can_see` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `team_connection_can_see` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `is_agree` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `data_input_status` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of match_makers
-- ----------------------------

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
-- Table structure for `migrations`
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('1', '2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` VALUES ('2', '2014_10_12_100000_create_password_resets_table', '1');
INSERT INTO `migrations` VALUES ('3', '2019_05_03_000001_create_customer_columns', '1');
INSERT INTO `migrations` VALUES ('4', '2019_05_03_000002_create_subscriptions_table', '1');
INSERT INTO `migrations` VALUES ('5', '2019_05_03_000003_create_subscription_items_table', '1');
INSERT INTO `migrations` VALUES ('6', '2019_08_19_000000_create_failed_jobs_table', '1');
INSERT INTO `migrations` VALUES ('7', '2019_12_14_000001_create_personal_access_tokens_table', '1');
INSERT INTO `migrations` VALUES ('8', '2021_04_16_174558_create_verify_users_table', '1');
INSERT INTO `migrations` VALUES ('9', '2021_04_20_094432_create_religions_table', '1');
INSERT INTO `migrations` VALUES ('10', '2021_04_20_095703_create_countries_table', '1');
INSERT INTO `migrations` VALUES ('11', '2021_04_20_101118_create_candidate_country_user_table', '1');
INSERT INTO `migrations` VALUES ('12', '2021_04_20_110827_create_study_level_table', '1');
INSERT INTO `migrations` VALUES ('13', '2021_04_20_1618906261_create_candidate_information_table', '1');
INSERT INTO `migrations` VALUES ('14', '2021_04_22_091758_create_cities_table', '1');
INSERT INTO `migrations` VALUES ('15', '2021_04_22_092136_create_candidate_city_table', '1');
INSERT INTO `migrations` VALUES ('16', '2021_04_22_101118_create_candidate_nationality_user_table', '1');
INSERT INTO `migrations` VALUES ('17', '2021_04_26_105228_create_occupations_table', '1');
INSERT INTO `migrations` VALUES ('18', '2021_04_27_094353_create_teams_table', '1');
INSERT INTO `migrations` VALUES ('19', '2021_04_27_104233_create_team_members_table', '1');
INSERT INTO `migrations` VALUES ('20', '2021_04_27_104310_create_team_member_invitations_table', '1');
INSERT INTO `migrations` VALUES ('21', '2021_04_29_073648_create_short_listed_candidates_table', '1');
INSERT INTO `migrations` VALUES ('22', '2021_04_30_061413_add_account_type_to_users_table', '1');
INSERT INTO `migrations` VALUES ('23', '2021_04_30_070602_create_candidate_images_table', '1');
INSERT INTO `migrations` VALUES ('24', '2021_05_03_105203_create_representative_informations_table', '1');
INSERT INTO `migrations` VALUES ('25', '2021_05_17_142358_candidate_information_data_input_status', '1');
INSERT INTO `migrations` VALUES ('26', '2021_05_17_143045_representative_informations_data_input_status', '1');
INSERT INTO `migrations` VALUES ('27', '2021_05_19_115518_add_relationship_column_in_team_members', '1');
INSERT INTO `migrations` VALUES ('28', '2021_05_19_131747_add_relation_and_user_type_columns_in_team_member_invitations', '1');
INSERT INTO `migrations` VALUES ('29', '2021_05_20_123549_create_block_lists_table', '1');
INSERT INTO `migrations` VALUES ('30', '2021_05_25_074035_create_plans_table', '1');
INSERT INTO `migrations` VALUES ('31', '2021_05_26_102406_create_delete_reasons_table', '1');
INSERT INTO `migrations` VALUES ('32', '2021_05_27_125150_create_team_connections_table', '1');
INSERT INTO `migrations` VALUES ('33', '2021_05_27_133954_add_subscription_id_to_teams_table', '1');
INSERT INTO `migrations` VALUES ('34', '2021_05_28_072650_make_responded_by_nullable_in_team_connection', '1');
INSERT INTO `migrations` VALUES ('35', '2021_05_28_133940_replace_enum_columns_to_int_in_team_connections', '1');
INSERT INTO `migrations` VALUES ('36', '2021_05_28_134650_add_column_connection_status_to_team_connection', '1');
INSERT INTO `migrations` VALUES ('37', '2021_06_02_081657_create_notifications_table', '1');
INSERT INTO `migrations` VALUES ('38', '2021_06_21_110028_create_profile_logs_table', '1');
INSERT INTO `migrations` VALUES ('39', '2021_06_30_112457_create_match_makers_table', '1');
INSERT INTO `migrations` VALUES ('40', '2021_07_13_110417_change_pre_height_min_to_candidate_informations_table', '1');

-- ----------------------------
-- Table structure for `notifications`
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('single','team','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'team',
  `team_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of notifications
-- ----------------------------
INSERT INTO `notifications` VALUES ('1', 'Successfully blocked ! you can find this user in your block list', 'single', null, '78', null, '2021-10-09 06:50:30', '2021-10-09 06:50:30');
INSERT INTO `notifications` VALUES ('2', 'Successfully blocked ! you can find this user in your block list', 'single', null, '78', null, '2021-10-09 06:51:41', '2021-10-09 06:51:41');
INSERT INTO `notifications` VALUES ('3', 'Successfully blocked ! you can find this user in your block list', 'single', null, '78', null, '2021-10-09 07:03:20', '2021-10-09 07:03:20');

-- ----------------------------
-- Table structure for `occupations`
-- ----------------------------
DROP TABLE IF EXISTS `occupations`;
CREATE TABLE `occupations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `occupations_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of occupations
-- ----------------------------
INSERT INTO `occupations` VALUES ('1', 'Architect', '0', null, null);
INSERT INTO `occupations` VALUES ('2', 'Actor', '0', null, null);
INSERT INTO `occupations` VALUES ('3', 'Chef/Cook', '0', null, null);
INSERT INTO `occupations` VALUES ('4', 'Designer', '0', null, null);
INSERT INTO `occupations` VALUES ('5', 'Doctor', '0', null, null);
INSERT INTO `occupations` VALUES ('6', 'Electrician', '0', null, null);
INSERT INTO `occupations` VALUES ('7', 'Engineer', '0', null, null);
INSERT INTO `occupations` VALUES ('8', 'Factory worker', '0', null, null);
INSERT INTO `occupations` VALUES ('9', 'Farmer', '0', null, null);
INSERT INTO `occupations` VALUES ('10', 'Fisherman', '0', null, null);
INSERT INTO `occupations` VALUES ('11', 'Journalist', '0', null, null);
INSERT INTO `occupations` VALUES ('12', 'Judge', '0', null, null);
INSERT INTO `occupations` VALUES ('13', 'Lecturer', '0', null, null);

-- ----------------------------
-- Table structure for `password_resets`
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_resets
-- ----------------------------
INSERT INTO `password_resets` VALUES ('raz.abcoder@gmail.com', 'VL8ZmY30vlJ11t9bplGiptND1Ezn6JQTp4vd3PKJpqUFbwxczoMOgVWEtiOt', null);
INSERT INTO `password_resets` VALUES ('raz.abcoder@gmail.com', 'lgTVhIw2UcLEj4ekgUoo8CSzK0o8YDXjNmyVNgaKhZZU9b55w5McEWicsZeS', null);
INSERT INTO `password_resets` VALUES ('raz.abcoder@gmail.com', 'FOLwsGHAmr6hbhO5tFscnTj7q2YfGVi6Ay9JTBE1TJLECFhlrt4Qjx0nr7xE', null);
INSERT INTO `password_resets` VALUES ('raz.abcoder@gmail.com', 'l74y630P75OiOYDuUeyeMAjbWKdilECiDaxaZiqMMky0MWjCM5nc0Uxp7L6T', null);
INSERT INTO `password_resets` VALUES ('raz.abcoder@gmail.com', '9s5TdvcYA0BFAS9LNyeUZhFpsaWOvvoTClegn3PUit6hJguzpQ8CmjMxLUsd', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'rRIZlrFd32bzD9r5qmqY5pStIOxvEK3Mtspm6TfLShOTGVcTQ2cNkJhtgozH', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'L6vbIHfcPk1iySnfhTMBrTVPgPXaDEJndVfO6cVMkqGJofQiBYyNSOYZLgO1', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'c4knZNnsrM8wPY8niOhVPyV9xpM0chJHlqjVSNtkpKgTPb00vjJsVaPHmf4z', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', '2pv7NlEaAVUgouCcdUAqbbhG5m15fpOGvgtNuF1vsnLU0tgAd8NtCrJlKmbq', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'AGmfKkkCbWJa0qgbIZdLzL1Mfm5Nntb0eEG4tXfTTyH5JURsvoIxqKFgSqFE', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'lMXLb67KZ4lWGz4yRI8KweL7saaEoB1Q95eNJ786Dg8hFUxdXcLxYe2GxZjL', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'uGUpNXkSykxfkDKROn5366vQG5r3jI8HMY97yn6HnRXYIbARkfanLpcs9rJT', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'U6z6AYRqbahMOLIdkJW2EPFVuR21k7982Wr31KDDkIkj5fPeqrBUQ81lRALH', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', '9QtBKD91QUpYh9dBtRgdzP3Zwgx4SEyFx1ucPFOwM7P6YzADdohuQdtkMdTP', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'WFKhEMVOrUTPpOOkqSO0gIInSF4NDYhsYS1stz6cU7zqszzUFmQgdxpRYWZV', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'ULWoIKQ0VqMPRjbKitwnqKdkk7MCizh8E4aU9CpaspD5rSNrrkT9QV9fRODK', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'Nd1Q1vEokwneTmoDrngamrIFWqB6WZutGpM1ZLnIXlPVotP4aCWmI0kfs1l8', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'pSlvIAvw9gBE1Y4K90dbttg6i8RcFqJIOWHVxvlhhx4DeKY4d83v2aE5MEHz', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'LsH8DEWlUSmygoA9imKqoGr2RfInqBBMSD1lM55GaJLcYBKuQAPSIEBX9TRr', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', '9JCHStoHIRk6N60kpGB6O3AVLO1VcW4yN6ifqq1uq82J3xDMOGiO55k3kALk', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'ZMEFDs2DCjYseIH4uen1NpNJh71ZgDnLYvOHRP1sRV61XfR4qKaUdIeboSXG', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', '955bVt0YcjcGh7F1b4LTiVuAOq2WbhjI5G91sr2Bscz4ZN5M59tJnemIN74p', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'lWfkGEI5IztBE3gAGpMwDq8Cii9Mio7uTf0iMqIusKrNPFVegN4VBuHkmtGX', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'B9IDzwK6SYSYosnpvLJnMNfk0RP6i7tJNYOolguJONuJVOd0HCiYfBK0cKv1', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', '9atiSUA2sXl3PSV4LoynzKQjHawGXa6Xp6lnUWQGpi56yIxL9rqlca75Ic6w', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'EGg0FfOMtUiiThCDXvN9RFdsqlB4XHG6jq5gUVC6jnVDPq0lMfh2zOvNiB4F', null);
INSERT INTO `password_resets` VALUES ('raz.abcoder@gmail.com', 'KwJhBWyfZpOXcqOK4LtWoA5JiG9F43lRjCGvt2WT2JJftRujtDlPGayGs9vV', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'iN1Vpj9PVuUy2PRDl5FEOFpA2qQ4iurVBAvyg2xXAWU6nfFuDEccACsBQmsw', null);
INSERT INTO `password_resets` VALUES ('raz.doict@gmail.com', 'Ne03A72JsGFv2e3YkEP5x8QboFnP97TWvoWCGi2s0epQAvEeVyFCWCvtrw3U', null);

-- ----------------------------
-- Table structure for `personal_access_tokens`
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for `plans`
-- ----------------------------
DROP TABLE IF EXISTS `plans`;
CREATE TABLE `plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of plans
-- ----------------------------

-- ----------------------------
-- Table structure for `profile_logs`
-- ----------------------------
DROP TABLE IF EXISTS `profile_logs`;
CREATE TABLE `profile_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `visitor_id` bigint(20) unsigned NOT NULL,
  `team_id` bigint(20) unsigned DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_logs_user_id_foreign` (`user_id`),
  KEY `profile_logs_visitor_id_foreign` (`visitor_id`),
  CONSTRAINT `profile_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `profile_logs_visitor_id_foreign` FOREIGN KEY (`visitor_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of profile_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `religions`
-- ----------------------------
DROP TABLE IF EXISTS `religions`;
CREATE TABLE `religions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=inactive, 1=active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of religions
-- ----------------------------
INSERT INTO `religions` VALUES ('1', 'Atheists', '1', null, null);
INSERT INTO `religions` VALUES ('2', 'Agnostics', '1', null, null);
INSERT INTO `religions` VALUES ('3', 'Bahais', '1', null, null);
INSERT INTO `religions` VALUES ('4', 'Buddhists', '1', null, null);
INSERT INTO `religions` VALUES ('5', 'Chinese folk-religionists', '1', null, null);
INSERT INTO `religions` VALUES ('6', 'Christians', '1', null, null);
INSERT INTO `religions` VALUES ('7', 'Confucianists', '1', null, null);
INSERT INTO `religions` VALUES ('8', 'Daoists', '1', null, null);
INSERT INTO `religions` VALUES ('9', 'Ethnoreligionists', '1', null, null);
INSERT INTO `religions` VALUES ('10', 'Hindus', '1', null, null);
INSERT INTO `religions` VALUES ('11', 'Jains', '1', null, null);
INSERT INTO `religions` VALUES ('12', 'Jews', '1', null, null);
INSERT INTO `religions` VALUES ('13', 'Muslims', '1', null, null);
INSERT INTO `religions` VALUES ('14', 'New Religionists', '1', null, null);
INSERT INTO `religions` VALUES ('15', 'Shintoists', '1', null, null);
INSERT INTO `religions` VALUES ('16', 'Sikhs', '1', null, null);
INSERT INTO `religions` VALUES ('17', 'Spiritists', '1', null, null);
INSERT INTO `religions` VALUES ('18', 'Zoroastrians', '1', null, null);

-- ----------------------------
-- Table structure for `representative_informations`
-- ----------------------------
DROP TABLE IF EXISTS `representative_informations`;
CREATE TABLE `representative_informations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_gender` tinyint(4) DEFAULT NULL COMMENT '1=Male,2=Female,3=Others,4=Do not disclose',
  `dob` date DEFAULT NULL,
  `per_occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_current_residence_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_current_residence_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_county` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_telephone_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_country_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT 'BD',
  `per_permanent_post_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_document_upload` tinyint(4) DEFAULT NULL COMMENT '0=No,1=Yes',
  `ver_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_document_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_document_frontside` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_document_backside` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_recommender_mobile_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_avatar_url` varchar(2083) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_main_image_url` varchar(2083) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anybody_can_see` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `only_team_can_see` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `team_connection_can_see` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `is_agree` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No,1=Yes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `data_input_status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of representative_informations
-- ----------------------------
INSERT INTO `representative_informations` VALUES ('1', '58', 'Razd sad', 'asdsad', 'AZS76198', null, null, null, null, null, null, null, null, null, null, null, 'BD', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '2021-10-05 12:25:34', '2021-10-05 12:25:34', null, '0');

-- ----------------------------
-- Table structure for `short_listed_candidates`
-- ----------------------------
DROP TABLE IF EXISTS `short_listed_candidates`;
CREATE TABLE `short_listed_candidates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `shortlisted_by` bigint(20) NOT NULL,
  `shortlisted_for` bigint(20) DEFAULT NULL,
  `shortlisted_date` date DEFAULT NULL,
  `is_block` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of short_listed_candidates
-- ----------------------------

-- ----------------------------
-- Table structure for `study_level`
-- ----------------------------
DROP TABLE IF EXISTS `study_level`;
CREATE TABLE `study_level` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ex.Undergraduate,Graduate / Postgraduate',
  PRIMARY KEY (`id`),
  UNIQUE KEY `study_level` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of study_level
-- ----------------------------
INSERT INTO `study_level` VALUES ('2', 'Graduate');
INSERT INTO `study_level` VALUES ('3', 'Postgraduate');
INSERT INTO `study_level` VALUES ('1', 'Undergraduate');

-- ----------------------------
-- Table structure for `subscription_items`
-- ----------------------------
DROP TABLE IF EXISTS `subscription_items`;
CREATE TABLE `subscription_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `stripe_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_plan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_items_subscription_id_stripe_plan_unique` (`subscription_id`,`stripe_plan`),
  KEY `subscription_items_stripe_id_index` (`stripe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of subscription_items
-- ----------------------------

-- ----------------------------
-- Table structure for `subscriptions`
-- ----------------------------
DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `team_id` bigint(20) unsigned DEFAULT NULL,
  `plan_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_plan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `subscription_expire_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_user_id_stripe_status_index` (`user_id`,`stripe_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of subscriptions
-- ----------------------------

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
-- Table structure for `team_connections`
-- ----------------------------
DROP TABLE IF EXISTS `team_connections`;
CREATE TABLE `team_connections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_team_id` bigint(20) unsigned NOT NULL,
  `to_team_id` bigint(20) unsigned NOT NULL,
  `requested_by` bigint(20) unsigned NOT NULL,
  `responded_by` bigint(20) unsigned DEFAULT NULL,
  `connection_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0=pending,1=accepted,2=rejected',
  `requested_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `team_connections_from_team_id_foreign` (`from_team_id`),
  KEY `team_connections_to_team_id_foreign` (`to_team_id`),
  KEY `team_connections_requested_by_foreign` (`requested_by`),
  KEY `team_connections_responded_by_foreign` (`responded_by`),
  CONSTRAINT `team_connections_from_team_id_foreign` FOREIGN KEY (`from_team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `team_connections_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  CONSTRAINT `team_connections_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `users` (`id`),
  CONSTRAINT `team_connections_to_team_id_foreign` FOREIGN KEY (`to_team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of team_connections
-- ----------------------------
INSERT INTO `team_connections` VALUES ('1', '1', '2', '1', '78', '1', '2021-10-19 21:28:00', '2021-11-13 05:17:40');

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

-- ----------------------------
-- Table structure for `team_members`
-- ----------------------------
DROP TABLE IF EXISTS `team_members`;
CREATE TABLE `team_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `relationship` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_member_unique` (`team_id`,`user_id`),
  KEY `team_members_user_id_foreign` (`user_id`),
  CONSTRAINT `team_members_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `team_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of team_members
-- ----------------------------
INSERT INTO `team_members` VALUES ('1', '2', '78', 'Candidate', 'Candidate', '1', null, null, '');
INSERT INTO `team_members` VALUES ('2', '1', '79', 'Candidate', 'Candidate', '1', null, null, '');
INSERT INTO `team_members` VALUES ('3', '2', '80', 'Candidate', 'Candidate', '1', null, null, '');
INSERT INTO `team_members` VALUES ('4', '3', '78', 'Representative', 'Owner+Admin', '1', '2021-11-27 14:16:07', '2021-11-27 14:16:07', 'Own');
INSERT INTO `team_members` VALUES ('5', '4', '78', 'Representative', 'Owner+Admin', '1', '2021-12-18 11:55:07', '2021-12-18 11:55:07', 'Own');

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

-- ----------------------------
-- Table structure for `teams`
-- ----------------------------
DROP TABLE IF EXISTS `teams`;
CREATE TABLE `teams` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `member_count` tinyint(4) NOT NULL DEFAULT '0',
  `subscription_id` bigint(20) unsigned DEFAULT NULL,
  `subscription_expire_at` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `password` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teams_created_by_foreign` (`created_by`),
  CONSTRAINT `teams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of teams
-- ----------------------------
INSERT INTO `teams` VALUES ('1', '1', 'Test', 'dsadsa', '2', '1', '2022-10-31', '1', 'a', null, '1', '2021-10-29 14:35:34', '2021-10-29 14:35:37');
INSERT INTO `teams` VALUES ('2', '2', 'Team 2', 'sad asd', '3', '1', '2022-10-19', '1', 'a', null, '79', null, null);
INSERT INTO `teams` VALUES ('3', 'b13b8aad-49b5-44ad-a741-359145dce7f0', 'sadsa', 'dsad', '1', null, null, '1', 'a', 'team-logo-3/gallery-547f72fe702524c1a2b1c3135d96a745.jpg', '78', '2021-11-27 14:16:07', '2021-11-27 14:16:07');
INSERT INTO `teams` VALUES ('4', '1b1c6a62-5704-4182-a2cd-e68405e73148', 'sadsa', 'dsad', '1', null, null, '1', 'a', 'team-logo-4/gallery-547f72fe702524c1a2b1c3135d96a745.jpg', '78', '2021-12-18 11:55:07', '2021-12-18 11:55:07');

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('1','2','3','4','5') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_end` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stripe_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_last_four` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `account_type` int(11) NOT NULL DEFAULT '0' COMMENT '0=not selected, 1=candidate, 2=matchmaker , 3=admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_stripe_id_index` (`stripe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'Prof. Juvenal Waelchi', 'knader@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'QJJcPy1jpr', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('2', 'Ms. Nelle Waelchi I', 'hansen.eunice@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'zXPdOrYanP', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('3', 'Julius Steuber', 'dhagenes@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, '1YDCKWpBQ9', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('4', 'Hope Gulgowski', 'okon.agustina@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'l7r4Wt20U2', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('5', 'Brant Hoppe', 'isaac.kirlin@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'gQEp9yLmrH', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('6', 'Priscilla Miller Jr.', 'franecki.gaston@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'I2Ez5fTsr0', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('7', 'Kavon Padberg DDS', 'watson31@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'zcuYKCtYt6', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('8', 'Scarlett Price', 'rutherford.anna@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'DcDdLmhXR3', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('9', 'Zachary Heathcote', 'zemlak.emerald@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'Bhz8I6M7a4', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('10', 'Walton Sanford', 'adelia.mcglynn@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'xq2F0IlN79', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('11', 'Norberto Huel', 'wschulist@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'P4Uc9q2aDL', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('12', 'Felicity Gorczany', 'rolfson.arvid@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'EEQEMip7d5', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('13', 'Mrs. Maryjane Yost', 'cortney36@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'udrkCCPYJj', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('14', 'Miss Elenora Simonis PhD', 'charley17@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'RlsvrOyyzh', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('15', 'Miss Charlotte Johns', 'cbreitenberg@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'v05k2MmsJd', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('16', 'Myra Stokes', 'giovanni.tromp@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'WtBZuFcSkl', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('17', 'Mr. Larry Haley DDS', 'phodkiewicz@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'a7UFvPWm8R', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('18', 'Hailee Gottlieb', 'leuschke.maci@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'D0bnxV9JOe', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('19', 'Ms. Kaela Goodwin', 'tanderson@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, '5GwAs8vPdg', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('20', 'Elvera Collins', 'gstrosin@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'KCMjGyKLuu', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('21', 'Salma Cartwright', 'maynard91@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, '3XLl0wPtKn', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('22', 'Dr. Donald Strosin', 'kameron.botsford@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'ziB9lGzIQy', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('23', 'Norris McCullough', 'jaycee.schamberger@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'alisegqTHI', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('24', 'Carey Satterfield', 'aryanna72@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'MslxGyP8mu', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('25', 'Kamren Russel', 'brakus.shyann@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'MmF6FDk4wH', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('26', 'Prof. Elenora Parker Sr.', 'mcdermott.vanessa@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, '1f6N2ggYNQ', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('27', 'Elvie Braun DVM', 'fsanford@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, '15bZVs2eoB', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('28', 'Toy Thompson', 'njacobson@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'tgPScdwjQc', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('29', 'Dr. Greta Wilderman', 'oma.waelchi@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'oKWZZfK5fW', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('30', 'Stevie Blick III', 'bert.gerlach@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'AQaLC8TIL6', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('31', 'Eleanora Hayes', 'khauck@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'fYEvzg8OyO', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('32', 'Madison Senger', 'kellie.wiza@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'yZ73sWzxh7', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('33', 'Effie Schmeler', 'tierra56@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'sfwIYvjjSZ', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('34', 'Felix Volkman', 'hill.josianne@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'kAXDsARpWK', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('35', 'Talia Ratke', 'sjast@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'FtgbzgWZhz', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('36', 'Jayson Bogan DDS', 'rwisoky@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, '5a4rUIJnZH', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('37', 'Retha Howe IV', 'feeney.filomena@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'cExt9rGkP8', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('38', 'Prof. Adriana Rutherford I', 'abigail.langworth@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, '9IhV43G77d', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('39', 'Felton Greenfelder', 'oschinner@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'PnIjVmyoML', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('40', 'Jackeline Predovic', 'cconn@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'sSiHQp9FMX', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('41', 'Ethyl Bahringer PhD', 'jkunde@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'ahdK4x4Pfm', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('42', 'Mrs. Josianne Ruecker Sr.', 'cbruen@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'oiHIvYFWfo', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('43', 'Jaylen Ryan', 'rodriguez.dewitt@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'isqHodpOlV', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('44', 'Priscilla Will', 'tkilback@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, '0sKHtoBTkN', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('45', 'Bradley Gislason', 'hauck.kariane@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'qDvWqRhmW7', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('46', 'Dr. Clifford Brown', 'donnelly.joana@example.com', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'dRKFSMok5N', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('47', 'Daija Zulauf PhD', 'bruen.vivian@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'HAD58suMRw', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('48', 'Sallie Dickens', 'lschowalter@example.net', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, '6uHNCPaSui', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('49', 'Mya Carter', 'vpadberg@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'KR5D5YpQye', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('50', 'Micheal Lind', 'guiseppe09@example.org', '2021-10-03 16:44:21', '0', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', null, null, 'u9JqDqa8Fm', '2021-10-03 16:44:21', '2021-10-03 16:44:21', null, null, null, null, '0');
INSERT INTO `users` VALUES ('76', null, 'raz.cse08@gmail.com', null, '0', '$2y$10$5XoqWqvviyHIiplw4cd8mOOo5a3ITUQzuza6MlsW80jRBT0IQM1XW', '1', null, null, null, '2021-10-05 14:01:11', '2021-10-05 14:01:11', null, null, null, null, '0');
INSERT INTO `users` VALUES ('77', null, 'raz.abcoder@gmail.com', null, '1', '$2y$04$jkoSo5UAeFex/W7XtTswhuUVsJUMxm7bqVi0b1WNbi/D7mWjx6df.', '1', null, null, null, '2021-10-05 14:01:48', '2021-10-09 07:03:20', null, null, null, null, '1');
INSERT INTO `users` VALUES ('78', null, 'raz.doict@gmail.com', '2021-10-09 07:03:20', '1', '$2y$04$jkoSo5UAeFex/W7XtTswhuUVsJUMxm7bqVi0b1WNbi/D7mWjx6df.', '1', null, null, null, '2021-10-05 15:56:43', '2021-10-12 08:32:54', null, null, null, null, '1');
INSERT INTO `users` VALUES ('79', null, 'raz.abcoder2@gmail.com', null, '1', '$2y$04$wNucncrBraGQk1Tcjgvc8e3E4mO/lVtHStWUGuCM1lkVYF7T5Gavy', '1', null, null, null, '2021-10-05 16:03:22', '2021-10-05 16:03:22', null, null, null, null, '0');
INSERT INTO `users` VALUES ('80', null, 'raz.abcoder3@gmail.com', null, '0', '$2y$04$nXOA6jiCQVlsoqInq1dnEOe65e6DFEk4aR7YUSlFjtva0RD2GKSr6', '1', null, null, null, '2021-10-05 16:04:26', '2021-10-05 16:04:26', null, null, null, null, '0');
INSERT INTO `users` VALUES ('81', null, 'raz.abcoder4@gmail.com', null, '0', '$2y$04$W2yepeie1uq5EPohUnaVzO0UJCoZHjzj1FuGJFLxQwhHcKMQhvfzC', '1', null, null, null, '2021-10-05 16:07:48', '2021-10-05 16:07:48', null, null, null, null, '0');
INSERT INTO `users` VALUES ('82', null, 'student@gmail.com', null, '0', '$2y$10$zU0.arPaDLk8CkgbMMgBf.rk6ZUNuen/ObD9kbJLjhNGYvPi6eEj2', '1', null, null, null, '2021-10-05 16:18:21', '2021-10-05 16:18:21', null, null, null, null, '0');

-- ----------------------------
-- Table structure for `verify_users`
-- ----------------------------
DROP TABLE IF EXISTS `verify_users`;
CREATE TABLE `verify_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of verify_users
-- ----------------------------
INSERT INTO `verify_users` VALUES ('1', '51', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzMyNzk0ODEsImV4cCI6MTYzMzYzOTQ4MSwibmJmIjoxNjMzMjc5NDgxLCJqdGkiOiJjNmZHdDNOcjdkZlllbTF2Iiwic3ViIjo1MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.FCx7TTtY9LwC4imfl4emRt53ynerh64rSYmdXq54gvY', '2021-10-03 16:44:41', '2021-10-03 16:44:41');
INSERT INTO `verify_users` VALUES ('2', '52', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzMzNTE0OTEsImV4cCI6MTYzMzcxMTQ5MSwibmJmIjoxNjMzMzUxNDkxLCJqdGkiOiJJUVRYelh5d1FnRmhseGgyIiwic3ViIjo1MiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.q3rZxZUfnClkzw7SP74XN3qtb6GdCKPrEhtD7DZDnYQ', '2021-10-04 12:44:51', '2021-10-04 12:44:51');
INSERT INTO `verify_users` VALUES ('3', '53', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzMzNTE4MTcsImV4cCI6MTYzMzcxMTgxNywibmJmIjoxNjMzMzUxODE3LCJqdGkiOiJIM1hEZ2VqVkRQUkF3VVlKIiwic3ViIjo1MywicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.J4bsXbsmYj6B62qD3YMWRCObeSLq9O4P9Awb8cHemVQ', '2021-10-04 12:50:17', '2021-10-04 12:50:17');
INSERT INTO `verify_users` VALUES ('4', '54', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzMzNTQ1OTAsImV4cCI6MTYzMzcxNDU5MCwibmJmIjoxNjMzMzU0NTkwLCJqdGkiOiJ2TTJDUGg0cDYybVVyRTNBIiwic3ViIjo1NCwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.Ex2nPwQdInn7hzzNB-O2V8OmlD3Q2x6mCjs5LyjP4kk', '2021-10-04 13:36:30', '2021-10-04 13:36:30');
INSERT INTO `verify_users` VALUES ('5', '55', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzMzNTY3MDcsImV4cCI6MTYzMzcxNjcwNywibmJmIjoxNjMzMzU2NzA3LCJqdGkiOiJpcUJpdzV2clNtOXYwclZXIiwic3ViIjo1NSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.ilhrT8QjzdFMaKwcYzwLwiRexABs1eC-NaxJAanztDk', '2021-10-04 14:11:47', '2021-10-04 14:11:47');
INSERT INTO `verify_users` VALUES ('6', '56', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzMzNTY3MzksImV4cCI6MTYzMzcxNjczOSwibmJmIjoxNjMzMzU2NzM5LCJqdGkiOiI2ZVNtTXVKNmk2TkRMcmxEIiwic3ViIjo1NiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.mUWTEXfx91xhl5I78XWPemNSry2V5rFlSl1OBtsYucU', '2021-10-04 14:12:19', '2021-10-04 14:12:19');
INSERT INTO `verify_users` VALUES ('7', '57', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzMzNTY5NTMsImV4cCI6MTYzMzcxNjk1MywibmJmIjoxNjMzMzU2OTUzLCJqdGkiOiJQVlRhSGpOV1JITGhnZ3ltIiwic3ViIjo1NywicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.e3nmzA7F0lp1bZkYYinPtUyow3K0PSDfCK-1-hUB03Q', '2021-10-04 14:15:53', '2021-10-04 14:15:53');
INSERT INTO `verify_users` VALUES ('8', '58', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzMzNTk3NDAsImV4cCI6MTYzMzcxOTc0MCwibmJmIjoxNjMzMzU5NzQwLCJqdGkiOiJBcnUyZkdrTVBsRmRpVTJmIiwic3ViIjo1OCwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.BGgz17oVu0LqiZ9BdJdTg2xVNEl69nJY53dy_1M2OTo', '2021-10-04 15:02:20', '2021-10-04 15:02:20');
INSERT INTO `verify_users` VALUES ('9', '61', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzMzNjYwOTIsImV4cCI6MTYzMzcyNjA5MiwibmJmIjoxNjMzMzY2MDkyLCJqdGkiOiJXeUx3R01RelZxUTFsV2RMIiwic3ViIjo2MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.A_PuZ0ZT4YQn4r0SYUtEsavwX6hFlNdQ_9zIGUSXsvg', '2021-10-04 16:48:12', '2021-10-04 16:48:12');
INSERT INTO `verify_users` VALUES ('10', '55', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0MzU3MDcsImV4cCI6MTYzMzc5NTcwNywibmJmIjoxNjMzNDM1NzA3LCJqdGkiOiJXb3VQb1k5QnRSOUNLR3VXIiwic3ViIjo1NSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.0l6PvmnAhydDy4TzOIDwAVoDy1mEjnN_0AqYRJ-uoWg', '2021-10-05 12:08:27', '2021-10-05 12:08:27');
INSERT INTO `verify_users` VALUES ('11', '56', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0MzYxODcsImV4cCI6MTYzMzc5NjE4NywibmJmIjoxNjMzNDM2MTg3LCJqdGkiOiJCQWhYYmljWnpIeUg4MTZBIiwic3ViIjo1NiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.xMbASLo2Ynm8jVYOwJZGOaOcyKbyseRANXK5KNyJm1E', '2021-10-05 12:16:27', '2021-10-05 12:16:27');
INSERT INTO `verify_users` VALUES ('12', '57', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0MzYzNTQsImV4cCI6MTYzMzc5NjM1NCwibmJmIjoxNjMzNDM2MzU0LCJqdGkiOiJZaFJjaUI5Ynp6M2EycTVFIiwic3ViIjo1NywicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.fji4yt7t7TiKTawbfTX9sXQjc5juMKFeVy0BDquGlCc', '2021-10-05 12:19:14', '2021-10-05 12:19:14');
INSERT INTO `verify_users` VALUES ('13', '58', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0MzY1OTAsImV4cCI6MTYzMzc5NjU5MCwibmJmIjoxNjMzNDM2NTkwLCJqdGkiOiJvbng3UThySGRtZHV4cFJxIiwic3ViIjo1OCwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.lXDtZopKfkOt1OFXhRr3VWZLHvjUDJkXGcVELa1FjDY', '2021-10-05 12:23:10', '2021-10-05 12:23:10');
INSERT INTO `verify_users` VALUES ('14', '59', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0MzY4OTMsImV4cCI6MTYzMzc5Njg5MywibmJmIjoxNjMzNDM2ODkzLCJqdGkiOiJvb29UZEFNb1IwY1R5QnI5Iiwic3ViIjo1OSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.i7SBar8t2pkZUycuJyapXAVU6du_mYF6ukJkt9aPIW4', '2021-10-05 12:28:13', '2021-10-05 12:28:13');
INSERT INTO `verify_users` VALUES ('16', '61', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0Mzg1NDksImV4cCI6MTYzMzc5ODU0OSwibmJmIjoxNjMzNDM4NTQ5LCJqdGkiOiJ4dVFCRDJjam9QZFdyekdrIiwic3ViIjo2MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.qMaAdyP0i8urPqzyDYATvY2SN3xW3RJaXG3_xNEh76E', '2021-10-05 12:55:49', '2021-10-05 12:55:49');
INSERT INTO `verify_users` VALUES ('17', '62', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0Mzg5NjgsImV4cCI6MTYzMzc5ODk2OCwibmJmIjoxNjMzNDM4OTY4LCJqdGkiOiJXYWdZWkZJVFVIWFBuQlZPIiwic3ViIjo2MiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.HDgPJTP6wjRV9XKfeaU0_V2UCs6WP7jcgHR3D819peE', '2021-10-05 13:02:48', '2021-10-05 13:02:48');
INSERT INTO `verify_users` VALUES ('18', '63', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0MzkwMzAsImV4cCI6MTYzMzc5OTAzMCwibmJmIjoxNjMzNDM5MDMwLCJqdGkiOiJtZlc0VmVDTUlDZ3dORzhYIiwic3ViIjo2MywicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.NjR4nNU6W13W33xqu9tw_jC3abIqI_1bnFiE1hnOwVA', '2021-10-05 13:03:50', '2021-10-05 13:03:50');
INSERT INTO `verify_users` VALUES ('20', '65', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0MzkxNTUsImV4cCI6MTYzMzc5OTE1NSwibmJmIjoxNjMzNDM5MTU1LCJqdGkiOiJJOGtzdWdLTUVOY0tFN3FvIiwic3ViIjo2NSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.UzesAxtChXtoR6RYiOHnLDtDcotmNtSvsaNRblApSo8', '2021-10-05 13:05:55', '2021-10-05 13:05:55');
INSERT INTO `verify_users` VALUES ('21', '66', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0Mzk5ODIsImV4cCI6MTYzMzc5OTk4MiwibmJmIjoxNjMzNDM5OTgyLCJqdGkiOiJza2ExRlowa2RxY1E1ckJQIiwic3ViIjo2NiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.C85eniZzwugwRRJ0CcHfDSJiKLnyIFieLlwf2Df4_6E', '2021-10-05 13:19:42', '2021-10-05 13:19:42');
INSERT INTO `verify_users` VALUES ('22', '67', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDAyOTMsImV4cCI6MTYzMzgwMDI5MywibmJmIjoxNjMzNDQwMjkzLCJqdGkiOiJDa3NVVXMyNERYeEVJTGZpIiwic3ViIjo2NywicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.hQKdatdqfqPBIaWr4QawdRzwOxmlVc_ZgeW9RHr6HXM', '2021-10-05 13:24:53', '2021-10-05 13:24:53');
INSERT INTO `verify_users` VALUES ('23', '68', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDA1NTIsImV4cCI6MTYzMzgwMDU1MiwibmJmIjoxNjMzNDQwNTUyLCJqdGkiOiJFekJFbXdzVWtQQmU1eWY3Iiwic3ViIjo2OCwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.LPWG3olVaj1ddp2mFAHyxSPZx6-QUw5c7J4J65cGzL0', '2021-10-05 13:29:12', '2021-10-05 13:29:12');
INSERT INTO `verify_users` VALUES ('24', '69', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDA3MjAsImV4cCI6MTYzMzgwMDcyMCwibmJmIjoxNjMzNDQwNzIwLCJqdGkiOiJ2YmExRW1lNGtudzlWbllIIiwic3ViIjo2OSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.1NgocLGynfzcOhpEA9QU-DAkxCB44M2qN9GCrTxK5Lc', '2021-10-05 13:32:00', '2021-10-05 13:32:00');
INSERT INTO `verify_users` VALUES ('25', '70', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDA3NDIsImV4cCI6MTYzMzgwMDc0MiwibmJmIjoxNjMzNDQwNzQyLCJqdGkiOiJZaHhPVGJBOGVMNmZodjY0Iiwic3ViIjo3MCwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.P6MiELyL9Xyepj4_4LckjI0yqvWbOEw_xkJMc5LTxpc', '2021-10-05 13:32:22', '2021-10-05 13:32:22');
INSERT INTO `verify_users` VALUES ('26', '71', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDA4NDksImV4cCI6MTYzMzgwMDg0OSwibmJmIjoxNjMzNDQwODQ5LCJqdGkiOiJSRDFvQlY1OXd2cTExZXhMIiwic3ViIjo3MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.phUQeqsViU9-4YKvaAKkGIaq2L_Ibssd7ryZ1j1xeSY', '2021-10-05 13:34:09', '2021-10-05 13:34:09');
INSERT INTO `verify_users` VALUES ('27', '73', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDExMDEsImV4cCI6MTYzMzgwMTEwMSwibmJmIjoxNjMzNDQxMTAxLCJqdGkiOiJzVXE3QlVzZFNQeFNLcklRIiwic3ViIjo3MywicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.3F-V9AFiLhKZ5ZCnY10edQxSWx4szglfyuOxjj1OBr4', '2021-10-05 13:38:21', '2021-10-05 13:38:21');
INSERT INTO `verify_users` VALUES ('28', '74', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDIyNzEsImV4cCI6MTYzMzgwMjI3MSwibmJmIjoxNjMzNDQyMjcxLCJqdGkiOiJIejJTMWRkOXhSQk8wemc4Iiwic3ViIjo3NCwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.MP4Rs9WYIKjDdnEfCsZBR8tH1JJjvoxjF5-1dCLrJ-4', '2021-10-05 13:57:51', '2021-10-05 13:57:51');
INSERT INTO `verify_users` VALUES ('29', '75', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDIzNDIsImV4cCI6MTYzMzgwMjM0MiwibmJmIjoxNjMzNDQyMzQyLCJqdGkiOiJsVTV4NU5rUFpRdlZjZ3JuIiwic3ViIjo3NSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.Fsfu7BOk2q6EZLS6-DYwEKyBqXGBT-u5hxZ0vD1LU_8', '2021-10-05 13:59:02', '2021-10-05 13:59:02');
INSERT INTO `verify_users` VALUES ('30', '76', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDI0NzEsImV4cCI6MTYzMzgwMjQ3MSwibmJmIjoxNjMzNDQyNDcxLCJqdGkiOiJtMGM2cFNMOWliaVBHd3ZjIiwic3ViIjo3NiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.n1TsDOX3dRSclTTEukncZffAjdI0wMHsTUh3-J8K06A', '2021-10-05 14:01:11', '2021-10-05 14:01:11');
INSERT INTO `verify_users` VALUES ('31', '77', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDI1MDgsImV4cCI6MTYzMzgwMjUwOCwibmJmIjoxNjMzNDQyNTA4LCJqdGkiOiJyOGhLZ05zYTVDbHlldnAyIiwic3ViIjo3NywicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.cirKfcPlHiGUDgtR8Yv6i_H_ZLTbcavTQ9rmqZT_R3I', '2021-10-05 14:01:48', '2021-10-05 14:01:48');
INSERT INTO `verify_users` VALUES ('33', '79', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODg4OFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDk4MDIsImV4cCI6MTYzMzgwOTgwMiwibmJmIjoxNjMzNDQ5ODAyLCJqdGkiOiJsV1JIbEJBUWxjSGdGMlpqIiwic3ViIjo3OSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.hTZyOb3_v-PlHpQyxXTKE9shHBMH32KZC2z2YWSKrHQ', '2021-10-05 16:03:22', '2021-10-05 16:03:22');
INSERT INTO `verify_users` VALUES ('34', '80', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODg4OFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NDk4NjYsImV4cCI6MTYzMzgwOTg2NiwibmJmIjoxNjMzNDQ5ODY2LCJqdGkiOiIycHdKbTdldjl1NTk2SU1EIiwic3ViIjo4MCwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.bLzVEfDwyiC_FamFRXleqQeERq_oY8M-MqpBypygjWk', '2021-10-05 16:04:26', '2021-10-05 16:04:26');
INSERT INTO `verify_users` VALUES ('35', '81', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODg4OFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NTAwNjgsImV4cCI6MTYzMzgxMDA2OCwibmJmIjoxNjMzNDUwMDY4LCJqdGkiOiJIT2xmT0RRZkxxalBlUEV6Iiwic3ViIjo4MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.r-zgXhZmBs1KI91gI9GCS-io3ClMGXlB9IMiAU5Bz2I', '2021-10-05 16:07:48', '2021-10-05 16:07:48');
INSERT INTO `verify_users` VALUES ('36', '82', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvcmVnaXN0ZXIiLCJpYXQiOjE2MzM0NTA3MDEsImV4cCI6MTYzMzgxMDcwMSwibmJmIjoxNjMzNDUwNzAxLCJqdGkiOiJ4UW5TNEM0d0wwSFc5RUZsIiwic3ViIjo4MiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.9QQNH_uDUqJAbZcP7bJ-mBAM7jrfbj0jF24GSH0r03s', '2021-10-05 16:18:21', '2021-10-05 16:18:21');
