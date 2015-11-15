/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 50626
 Source Host           : localhost
 Source Database       : k4g404n

 Target Server Type    : MySQL
 Target Server Version : 50626
 File Encoding         : utf-8

 Date: 11/16/2015 03:35:42 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `access`
-- ----------------------------
DROP TABLE IF EXISTS `access`;
CREATE TABLE `access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `sendmail` varchar(150) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `login` int(11) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `session` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `access`
-- ----------------------------
BEGIN;
INSERT INTO `access` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '0', 'drexmod@gmail.com', '1', '181', '2015-11-16 01:53:13', 'iegvc8c2al0pdmo351l3fsdvg2', '2015-03-15 16:48:36'), ('2', 'superuser', '21232f297a57a5a743894a0e4a801fc3', '0', 'christopher.olanolan@gmail.com', '2', '0', '2015-03-15 21:36:10', null, '2015-03-15 21:36:36'), ('3', 'drex', '21232f297a57a5a743894a0e4a801fc3', '0', 'drexmod@gmail.com.ph', '2', '0', '2015-03-16 00:41:31', null, '2015-03-16 00:41:31');
COMMIT;

-- ----------------------------
--  Table structure for `access_status`
-- ----------------------------
DROP TABLE IF EXISTS `access_status`;
CREATE TABLE `access_status` (
  `access_status_id` int(2) NOT NULL AUTO_INCREMENT,
  `access_status` varchar(64) NOT NULL,
  `access_class` varchar(64) NOT NULL,
  PRIMARY KEY (`access_status_id`),
  UNIQUE KEY `user_status` (`access_status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `access_status`
-- ----------------------------
BEGIN;
INSERT INTO `access_status` VALUES ('1', 'Active', 'ico ico_active active'), ('2', 'Deleted', 'ico ico_delete delete'), ('3', 'Cancelled', 'ico ico_cancelled cancelled'), ('4', 'Pending', 'ico ico_pending pending');
COMMIT;

-- ----------------------------
--  Table structure for `audit_log`
-- ----------------------------
DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE `audit_log` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `user_id` int(9) NOT NULL,
  `created` datetime NOT NULL,
  `module` varchar(255) CHARACTER SET utf8 NOT NULL,
  `action` text CHARACTER SET utf8 NOT NULL,
  `json` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `brand`
-- ----------------------------
DROP TABLE IF EXISTS `brand`;
CREATE TABLE `brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `active` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf32;

-- ----------------------------
--  Records of `brand`
-- ----------------------------
BEGIN;
INSERT INTO `brand` VALUES ('1', 'Nippon', '1'), ('2', 'Rox', '1'), ('3', 'Circuit', '1'), ('4', 'ENCO', '1'), ('5', 'Yokohama', '1'), ('6', 'TOYO Tires', '1'), ('7', 'Enkei', '1'), ('8', 'TEST', '1');
COMMIT;

-- ----------------------------
--  Table structure for `computation`
-- ----------------------------
DROP TABLE IF EXISTS `computation`;
CREATE TABLE `computation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `case` int(11) DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `computation`
-- ----------------------------
BEGIN;
INSERT INTO `computation` VALUES ('1', '1', '1', '3000', '2015-11-15 18:59:52', '1'), ('2', '1', '2', '200', '2015-11-15 23:24:01', '1');
COMMIT;

-- ----------------------------
--  Table structure for `consumptions`
-- ----------------------------
DROP TABLE IF EXISTS `consumptions`;
CREATE TABLE `consumptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `truck_id` int(11) NOT NULL,
  `liters` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `payment` decimal(10,2) DEFAULT NULL,
  `consumption_date` date DEFAULT '0000-00-00',
  `payment_date` date DEFAULT '0000-00-00',
  `active` int(2) DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `consumptions`
-- ----------------------------
BEGIN;
INSERT INTO `consumptions` VALUES ('1', '1', '357.75', '42.90', null, '2015-04-06', '0000-00-00', '1', '2015-04-06 20:42:19'), ('2', '1', '357.29', '43.15', null, '2015-04-01', '0000-00-00', '1', '2015-04-06 21:29:18'), ('3', '1', '20.00', '43.15', null, '2015-04-02', '0000-00-00', '1', '2015-04-06 21:29:28'), ('4', '2', '20.00', '43.50', null, '2015-04-07', '0000-00-00', '1', '2015-04-07 21:27:32'), ('5', '3', '120.00', '43.50', null, '2015-04-22', '0000-00-00', '1', '2015-04-07 21:28:03');
COMMIT;

-- ----------------------------
--  Table structure for `deduction`
-- ----------------------------
DROP TABLE IF EXISTS `deduction`;
CREATE TABLE `deduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deduction_id` int(11) NOT NULL,
  `truck_id` int(11) NOT NULL,
  `personnel_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date_from` date DEFAULT '0000-00-00',
  `date_to` date DEFAULT '0000-00-00',
  `price` decimal(10,2) DEFAULT '0.00',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `deduction`
-- ----------------------------
BEGIN;
INSERT INTO `deduction` VALUES ('1', '1', '1', null, 'Unicorn Insurance', '2015-04-04', '2015-04-27', '19100.00', '2015-04-08 21:34:07', '1'), ('2', '2', '1', null, '', '2015-04-08', '2015-04-08', '36500.50', '2015-04-08 22:26:41', '1'), ('3', '5', '1', null, '', '2015-04-01', '2015-04-30', '1200.00', '2015-04-08 23:24:28', '1'), ('4', '3', '1', '1', '', '2015-10-01', '2015-10-31', '0.50', '2015-10-06 22:01:18', '1');
COMMIT;

-- ----------------------------
--  Table structure for `deduction_type`
-- ----------------------------
DROP TABLE IF EXISTS `deduction_type`;
CREATE TABLE `deduction_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `active` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf32;

-- ----------------------------
--  Records of `deduction_type`
-- ----------------------------
BEGIN;
INSERT INTO `deduction_type` VALUES ('1', 'Insurance', '1'), ('2', 'Amortization', '1'), ('3', 'Payroll', '1'), ('4', 'Emergency Fund', '1'), ('5', 'Garage Rental', '1'), ('6', 'SSS Shares', '1'), ('7', 'PhilHealth Share', '1'), ('8', 'PAGIBIG Share', '1'), ('9', 'Salary Share', '1'), ('11', 'Others', '1'), ('12', 'Extra', '1');
COMMIT;

-- ----------------------------
--  Table structure for `inventory`
-- ----------------------------
DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `brand` int(10) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT 'unit',
  `stocks` int(10) DEFAULT NULL,
  `stock_limit` int(10) DEFAULT '1',
  `purchase_date` date DEFAULT '0000-00-00',
  `supplier` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `inventory`
-- ----------------------------
BEGIN;
INSERT INTO `inventory` VALUES ('1', 'Air Filter', 'Air Filter (NA-503)', '3', '620.50', 'unit', '20', '1', '2015-04-20', '', '2015-04-20 20:49:53', '1'), ('2', 'Battery', '', '4', '1000.00', 'unit', '7', '5', '2015-04-17', '', '2015-04-20 20:52:12', '1');
COMMIT;

-- ----------------------------
--  Table structure for `location`
-- ----------------------------
DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(100) CHARACTER SET latin1 NOT NULL,
  `active` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf32;

-- ----------------------------
--  Records of `location`
-- ----------------------------
BEGIN;
INSERT INTO `location` VALUES ('1', 'Calamba', '1'), ('2', 'Pasig', '1'), ('3', 'Tuguegarao', '1'), ('4', 'Sta. Ana', '1'), ('5', 'San Pablo', '1'), ('6', 'Shuttle', '1'), ('7', 'Batangas City', '1'), ('8', 'Meycauayan', '1'), ('9', 'San Pedro', '1'), ('10', 'Kalawaan', '1'), ('11', 'San Fernando, Pampanga', '1'), ('12', 'La Union', '1'), ('13', 'Metro Manila', '1'), ('14', 'Baguio', '1'), ('15', 'Taguig', '1'), ('16', 'Quezon City', '1');
COMMIT;

-- ----------------------------
--  Table structure for `materials`
-- ----------------------------
DROP TABLE IF EXISTS `materials`;
CREATE TABLE `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `gross_weight` decimal(10,2) DEFAULT '0.00',
  `volume` decimal(10,3) DEFAULT '0.000',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `materials`
-- ----------------------------
BEGIN;
INSERT INTO `materials` VALUES ('1', '570', 'ES C2 GREEN TEA LEMON 355X24', '10.00', '0.019', '2015-11-03 19:38:03', '1'), ('2', '571', 'ES C2 GREEN TEA PEACH 355x24', '10.00', '0.019', '2015-11-03 19:38:53', '1'), ('3', '572', 'ES C2 GREEN TEA PLAIN 500x20', '14.00', '0.024', '2015-11-03 19:39:19', '1');
COMMIT;

-- ----------------------------
--  Table structure for `payment`
-- ----------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `urc_doc` varchar(100) NOT NULL,
  `transaction_id` int(2) NOT NULL,
  `payment` decimal(11,2) NOT NULL DEFAULT '0.00',
  `payment_date` date DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `payment`
-- ----------------------------
BEGIN;
INSERT INTO `payment` VALUES ('1', '7810169482', '2', '14600.00', '2015-10-12', '2015-10-12 20:44:03', '1'), ('2', '7810169482', '2', '15000.00', '2015-10-13', '2015-10-13 21:32:36', '1'), ('3', '7810169485', '1', '10000.00', '2015-10-13', '2015-10-13 21:33:53', '1');
COMMIT;

-- ----------------------------
--  Table structure for `payment_option`
-- ----------------------------
DROP TABLE IF EXISTS `payment_option`;
CREATE TABLE `payment_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `active` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf32;

-- ----------------------------
--  Records of `payment_option`
-- ----------------------------
BEGIN;
INSERT INTO `payment_option` VALUES ('1', 'Unpaid', '1'), ('2', 'Full Payment', '1'), ('3', 'Partial Payment', '1');
COMMIT;

-- ----------------------------
--  Table structure for `personnel`
-- ----------------------------
DROP TABLE IF EXISTS `personnel`;
CREATE TABLE `personnel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `empno` varchar(150) DEFAULT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `address` text,
  `gender` varchar(2) DEFAULT NULL,
  `type` tinyint(4) NOT NULL,
  `sendmail` varchar(150) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `landline` varchar(30) DEFAULT NULL,
  `sss` varchar(30) DEFAULT NULL,
  `pagibig` varchar(30) DEFAULT NULL,
  `tin` varchar(30) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `hire` datetime DEFAULT '0000-00-00 00:00:00',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `personnel`
-- ----------------------------
BEGIN;
INSERT INTO `personnel` VALUES ('1', 'OM0001', 'Christopher', 'Olanolan', 'Liwanag', '', 'M', '1', '', '', '', '', '', '', '1', '2014-11-02 02:41:37', '2015-03-31 21:11:26'), ('2', 'DN0001', 'Johann Christopher', 'Olanolan', 'Dador', '', 'M', '2', '', '', '', '', '', '', '1', '2015-03-01 02:41:46', '2015-04-05 19:35:49'), ('3', 'DN0003', 'Christin Hannah Mae', 'Olanolan', 'Dador', '', 'F', '2', '', '', '', '', '', '', '1', '2014-05-04 02:41:52', '2015-04-05 19:37:04'), ('4', 'OM0002', 'Christopher John', 'Olanolan', 'Dador', 'test', 'M', '1', 'drexmod@gmail.com', '09182462576', 'NCR', 'test', 'test', 'test', '1', '2015-04-01 00:00:00', '2015-04-05 19:36:10'), ('5', 'DN0002', 'John Christopher', 'Olanolan', null, 'Pasig', 'M', '2', '', '', '', '', '', '', '1', '2015-04-05 00:00:00', '2015-04-06 12:03:48');
COMMIT;

-- ----------------------------
--  Table structure for `personnel_type`
-- ----------------------------
DROP TABLE IF EXISTS `personnel_type`;
CREATE TABLE `personnel_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `active` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf32;

-- ----------------------------
--  Records of `personnel_type`
-- ----------------------------
BEGIN;
INSERT INTO `personnel_type` VALUES ('1', 'Operator', '1'), ('2', 'Driver', '1'), ('3', 'Others', '1'), ('4', 'Helper', '1'), ('5', 'Secretary', '1'), ('6', 'Accountant', '1');
COMMIT;

-- ----------------------------
--  Table structure for `requisition`
-- ----------------------------
DROP TABLE IF EXISTS `requisition`;
CREATE TABLE `requisition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) NOT NULL,
  `truck_id` int(11) NOT NULL,
  `personnel_id` int(11) DEFAULT NULL,
  `requisition_date` date DEFAULT '0000-00-00',
  `qty` int(10) DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `requisition`
-- ----------------------------
BEGIN;
INSERT INTO `requisition` VALUES ('1', '1', '1', '1', '2015-04-21', '1', '2015-04-21 20:15:17', '1'), ('2', '2', '1', '0', '2015-04-22', '1', '2015-04-22 22:21:11', '1'), ('3', '2', '1', '2', '2015-10-07', '2', '2015-04-22 22:22:18', '1'), ('4', '1', '3', '0', '2015-04-22', '4', '2015-04-22 22:23:29', '1');
COMMIT;

-- ----------------------------
--  Table structure for `settings`
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `value` varchar(64) DEFAULT NULL,
  `display_name` varchar(64) DEFAULT NULL,
  `display_value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `settings`
-- ----------------------------
BEGIN;
INSERT INTO `settings` VALUES ('1', 'sales_tax', '0.10', 'Sales Tax', '10% Tax'), ('2', 'savings', '0.03', 'Savings', '3% Savings'), ('3', 'fund', '0.01', 'Fund', '1% Fund');
COMMIT;

-- ----------------------------
--  Table structure for `shipment`
-- ----------------------------
DROP TABLE IF EXISTS `shipment`;
CREATE TABLE `shipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `truck_id` int(11) DEFAULT NULL,
  `source` int(11) NOT NULL,
  `destination` int(11) NOT NULL,
  `rate` decimal(11,2) DEFAULT '0.00',
  `shipment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `shipment`
-- ----------------------------
BEGIN;
INSERT INTO `shipment` VALUES ('1', '2147483647', '2', '1', '1', '2', '8361.17', '2015-11-15 15:39:00', '2015-11-15 15:39:00', '1');
COMMIT;

-- ----------------------------
--  Table structure for `transaction`
-- ----------------------------
DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `soa` varchar(255) DEFAULT NULL COMMENT 'SAO number',
  `transaction_date` date NOT NULL DEFAULT '0000-00-00' COMMENT 'Transaction date',
  `urc_doc` varchar(255) DEFAULT NULL COMMENT 'URC document',
  `source` int(11) DEFAULT NULL COMMENT 'Source addres',
  `destination` int(11) DEFAULT NULL COMMENT 'Destination address',
  `truck_id` int(11) NOT NULL COMMENT 'Plate ID',
  `driver_id` int(11) NOT NULL,
  `cs` int(11) DEFAULT '0' COMMENT 'Total number of CS',
  `rate` decimal(11,2) unsigned DEFAULT '0.00' COMMENT 'Rate price',
  `delivered` tinyint(1) DEFAULT '0',
  `delivered_date` date DEFAULT '0000-00-00',
  `paid` tinyint(1) DEFAULT '0',
  `payment` decimal(10,2) DEFAULT '0.00',
  `paid_date` date DEFAULT '0000-00-00',
  `status` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `transaction`
-- ----------------------------
BEGIN;
INSERT INTO `transaction` VALUES ('1', 'MPK 13-09-136', '2015-09-01', '7810169482 / 8810134766', '1', '2', '1', '5', '670', '32.56', '1', '2015-04-10', '3', '1000.00', '2015-04-10', '1', '2015-03-08 21:47:37', '1'), ('2', 'MPK 13-09-135', '2015-09-02', '7810169482', '3', '2', '1', '5', '925', '32.56', '1', '2015-10-09', '3', '1.00', '2015-10-09', '1', '2015-04-10 23:02:01', '1'), ('3', '', '2015-09-01', '', '2', '1', '2', '2', '630', '10.10', '0', '0000-00-00', '2', '0.00', '0000-00-00', '0', '2015-04-10 23:07:05', '1'), ('4', 'MPK 13-09-136', '2015-09-04', '7810169482', '1', '2', '1', '2', '60', '200.00', '1', '2015-10-09', '1', '0.00', '0000-00-00', '1', '2015-09-04 20:53:01', '1'), ('5', '', '2015-09-06', '', '1', '3', '1', '0', '23', '23.00', '0', '0000-00-00', '0', '0.00', '0000-00-00', '0', '2015-09-06 17:12:57', '1');
COMMIT;

-- ----------------------------
--  Table structure for `truck`
-- ----------------------------
DROP TABLE IF EXISTS `truck`;
CREATE TABLE `truck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plate` varchar(100) NOT NULL,
  `truck_type` varchar(100) DEFAULT NULL,
  `truck_model` varchar(100) DEFAULT NULL,
  `operator` varchar(100) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `truck`
-- ----------------------------
BEGIN;
INSERT INTO `truck` VALUES ('1', 'CTZ575', 'Canter', 'FG4X4', '1', '1', '2015-04-05 17:41:41'), ('2', 'CZT459', 'Starex', 'Starex', '4', '1', '2015-04-05 17:41:44'), ('3', 'CTZ576', 'Canter', 'Eco Hybrid', '1', '1', '2015-04-05 18:48:02'), ('4', 'BVF476', 'Canter', 'FG4X4', '1', '1', '2015-09-01 18:48:16');
COMMIT;

-- ----------------------------
--  Table structure for `truck_driver`
-- ----------------------------
DROP TABLE IF EXISTS `truck_driver`;
CREATE TABLE `truck_driver` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `truck_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `assigned` date NOT NULL DEFAULT '0000-00-00',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `truck_driver`
-- ----------------------------
BEGIN;
INSERT INTO `truck_driver` VALUES ('1', '1', '2', '1', '2015-04-01', '2015-04-01 19:49:25'), ('2', '1', '5', '1', '2015-04-06', '2015-03-04 20:51:04'), ('3', '2', '2', '1', '2015-03-26', '2015-04-01 20:53:17'), ('4', '1', '2', '1', '2015-04-06', '2015-04-06 11:56:26'), ('5', '3', '2', '1', '2015-09-07', '2015-09-02 20:04:34'), ('6', '3', '2', '1', '2015-09-30', '2015-09-02 20:04:55'), ('7', '3', '2', '1', '2015-09-14', '2015-09-02 20:05:13'), ('8', '1', '2', '1', '2015-09-07', '2015-09-02 20:05:25'), ('9', '1', '2', '1', '2015-09-01', '2015-09-11 22:17:18'), ('10', '1', '5', '1', '2015-09-01', '2015-09-11 22:20:06'), ('11', '2', '2', '1', '2015-09-01', '2015-09-29 21:41:57'), ('12', '1', '2', '1', '2015-09-04', '2015-10-09 21:16:41'), ('13', '1', '5', '1', '2015-09-02', '2015-10-10 00:38:26');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
