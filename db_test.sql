/*
 Navicat Premium Data Transfer

 Source Server         : docker-shotel
 Source Server Type    : MySQL
 Source Server Version : 80023
 Source Host           : localhost:3306
 Source Schema         : db_test

 Target Server Type    : MySQL
 Target Server Version : 80023
 File Encoding         : 65001

 Date: 18/09/2021 09:36:53
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone_number` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES ('admin@gmail.com', 'admin updated', 'Ha Noi', '0123456789', '$2y$10$pKh0nZwH006X/UZGnhN.WeDSk39LzYAZqTWemS86iARYpqh4aV1zG');
INSERT INTO `users` VALUES ('user@gmail.com', 'user', 'Ha Nam', '0123456787', '$2y$10$M9juWzMBK.m.hVqzhOcuX.JWUSmFA5pvVhhSb/EXiNQZj4cXNr/jy');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
