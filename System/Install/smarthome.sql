-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 28, 2017 at 07:48 AM
-- Server version: 5.7.17-log
-- PHP Version: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smarthome`
--

-- --------------------------------------------------------

--
-- Table structure for table `alarm_status`
--

CREATE TABLE `alarm_status` (
  `ID` int(11) NOT NULL,
  `alarm_mode` varchar(100) NOT NULL,
  `alarm_state` tinyint(1) NOT NULL,
  `alarm_time` varchar(100) NOT NULL,
  `alarm_ready` tinyint(1) NOT NULL,
  `alarm_triggered` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `alarm_status`
--

INSERT INTO `alarm_status` (`ID`, `alarm_mode`, `alarm_state`, `alarm_time`, `alarm_ready`, `alarm_triggered`) VALUES
(1, 'Away', 0, '9:17:43 AM', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `buttons`
--

CREATE TABLE `buttons` (
  `ID` int(11) NOT NULL,
  `button_name` varchar(100) NOT NULL,
  `button_address` varchar(200) NOT NULL,
  `button_state` tinyint(1) NOT NULL,
  `button_event` varchar(500) NOT NULL,
  `button_event2` varchar(500) NOT NULL,
  `room` varchar(100) NOT NULL,
  `button_icon` varchar(50) NOT NULL,
  `last_triggered` varchar(25) NOT NULL,
  `last_changed_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `camera_list`
--

CREATE TABLE `camera_list` (
  `ID` int(11) NOT NULL,
  `camera_name` varchar(100) NOT NULL,
  `ip_address` varchar(500) NOT NULL,
  `sensor_assign` int(11) NOT NULL,
  `click_trigger` varchar(200) NOT NULL,
  `alert_color` varchar(20) NOT NULL,
  `room` int(11) NOT NULL,
  `enabled` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `custom_scripts`
--

CREATE TABLE `custom_scripts` (
  `ID` int(11) NOT NULL,
  `script_name` varchar(50) NOT NULL,
  `script_location` varchar(100) NOT NULL,
  `script_type` varchar(10) NOT NULL,
  `enabled` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_cards`
--

CREATE TABLE `dashboard_cards` (
  `ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_name` varchar(100) NOT NULL,
  `card_type` varchar(50) NOT NULL,
  `card_style` varchar(9) NOT NULL,
  `attr1` varchar(500) NOT NULL DEFAULT '',
  `attr2` varchar(500) NOT NULL DEFAULT '',
  `attr3` varchar(500) NOT NULL DEFAULT '',
  `attr4` varchar(500) NOT NULL DEFAULT '',
  `attr5` varchar(500) NOT NULL DEFAULT '',
  `attr6` varchar(500) NOT NULL DEFAULT '',
  `enabled` int(11) NOT NULL DEFAULT '0',
  `index_order` int(11) NOT NULL,
  `default_layout` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dashboard_cards`
--

INSERT INTO `dashboard_cards` (`ID`, `user_id`, `card_name`, `card_type`, `card_style`, `attr1`, `attr2`, `attr3`, `attr4`, `attr5`, `attr6`, `enabled`, `index_order`, `default_layout`) VALUES
(7, 1, 'Room List', 'Roomlist', '0:280', '2:3:4:5:6', '', '', '', '', '', 1, 2, 0),
(8, 1, 'Group List', 'Grouplist', '0:280', '1:2:3:7', '', '', '', '', '', 1, 4, 0),
(10, 1, 'Weather', 'Weather', '0:280', '', '', '', '', '', '', 1, 5, 0),
(43, 1, 'Scene Control', 'Scene Control', '0:280', '3:4:5:6:7:8', '', '', '', '', '', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `data_sensors`
--

CREATE TABLE `data_sensors` (
  `ID` bigint(20) NOT NULL,
  `sensor_name` varchar(100) NOT NULL,
  `sensor_nicename` varchar(50) NOT NULL,
  `sensor_dataTitle_array` varchar(200) NOT NULL,
  `sensor_dataValue_array` varchar(200) NOT NULL,
  `sensor_dataVisible_array` varchar(200) NOT NULL,
  `sensor_type` varchar(50) NOT NULL,
  `room` int(11) NOT NULL,
  `enabled` int(11) NOT NULL,
  `last_update` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `ID` bigint(20) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `deviceXML` varchar(2000) NOT NULL,
  `device_state` tinyint(1) NOT NULL,
  `room` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL,
  `device_icon` varchar(50) NOT NULL,
  `device_method` int(11) NOT NULL DEFAULT '1',
  `timeout` varchar(10) NOT NULL,
  `enable_auto_off` tinyint(1) NOT NULL,
  `last_on_time` varchar(25) NOT NULL,
  `last_off_time` varchar(25) NOT NULL,
  `type` varchar(50) NOT NULL,
  `tags` varchar(200) NOT NULL DEFAULT '',
  `enabled` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `device_groups`
--

CREATE TABLE `device_groups` (
  `ID` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `group_icon` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `device_groups`
--

INSERT INTO `device_groups` (`ID`, `group_name`, `group_icon`) VALUES
(1, 'Lights', 'fa-power-off'),
(2, 'Outdoor', 'fa-power-off'),
(3, 'Smell Good', 'fa-power-off'),
(4, 'Entertainment', 'fa-power-off'),
(6, 'Heating & Cooling', 'fa-power-off'),
(7, 'Blinds', 'fa-power-off');

-- --------------------------------------------------------

--
-- Table structure for table `enabled_services`
--

CREATE TABLE `enabled_services` (
  `ID` int(11) NOT NULL,
  `service_name` varchar(200) NOT NULL,
  `service_attr1` varchar(200) NOT NULL,
  `service_attr2` varchar(200) NOT NULL,
  `service_attr3` varchar(200) NOT NULL,
  `service_attr4` varchar(200) NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `enabled_services`
--

INSERT INTO `enabled_services` (`ID`, `service_name`, `service_attr1`, `service_attr2`, `service_attr3`, `service_attr4`, `enabled`) VALUES
(1, 'Phillips Hue', '', '', '', '', 0),
(2, 'MQTT', 'localhost:1883', '', '', '', 0),
(3, 'Belkin Wemo', '', '', '', '', 0),
(4, 'SqueezeBox', '', '', '', '', 0),
(5, 'Email', 'smtp.gmail.com', '465', '', '', 0),
(6, 'Weather', '', '', '', '', 1),
(7, 'Spotify', '', '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `event_log`
--

CREATE TABLE `event_log` (
  `ID` bigint(20) NOT NULL,
  `event` varchar(200) NOT NULL,
  `tags` varchar(200) NOT NULL DEFAULT '',
  `event_date` varchar(100) NOT NULL,
  `event_read` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `find_sensors_list`
--

CREATE TABLE `find_sensors_list` (
  `ID` bigint(20) NOT NULL,
  `sensor_address` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `history_log`
--

CREATE TABLE `history_log` (
  `ID` bigint(20) NOT NULL,
  `device_id` int(11) NOT NULL,
  `device_type` varchar(20) NOT NULL,
  `state` int(11) NOT NULL,
  `startTime` varchar(20) NOT NULL,
  `endTime` varchar(20) NOT NULL DEFAULT '',
  `reason` varchar(50) NOT NULL,
  `date_added` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `home_rooms`
--

CREATE TABLE `home_rooms` (
  `ID` int(11) NOT NULL,
  `room_name` varchar(50) NOT NULL,
  `room_icon` varchar(24) NOT NULL,
  `guest_access` tinyint(1) NOT NULL,
  `last_active` varchar(100) NOT NULL DEFAULT '0',
  `timeout` int(11) NOT NULL,
  `timeout_last_active` varchar(15) NOT NULL DEFAULT '',
  `timeout_enable` tinyint(1) NOT NULL,
  `autoWakeUpTime` varchar(50) NOT NULL,
  `autoWakeUpLastRan` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `home_rooms`
--

INSERT INTO `home_rooms` (`ID`, `room_name`, `room_icon`, `guest_access`, `last_active`, `timeout`, `timeout_last_active`, `timeout_enable`, `autoWakeUpTime`, `autoWakeUpLastRan`) VALUES
(2, 'Back Office', 'fa-power-off', 1, '1490198134', 1, '', 1, '0|12:01|mon tue wed thu fri', '1483554777'),
(3, 'Bedroom', 'fa-power-off', 1, '1490198136', 15, '', 1, '1|06:00|mon tue wed thu fri', '1488371490'),
(4, 'Main', 'fa-power-off', 1, '1490198135', 15, '', 1, '0|18:00|', '1482841574'),
(5, 'Hallway', 'fa-power-off', 1, '1490629842', 2, '1490629842', 1, '0|18:00|', ''),
(6, 'Kitchen', 'fa-power-off', 1, '1490198129', 3, '', 1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `ifttt`
--

CREATE TABLE `ifttt` (
  `ID` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `if_Array` text NOT NULL,
  `ifThen_Array` text NOT NULL,
  `opperatorArray` varchar(100) NOT NULL DEFAULT '',
  `parenthaseArray` varchar(100) NOT NULL DEFAULT '',
  `last_ran` varchar(25) NOT NULL,
  `delay` varchar(10) NOT NULL DEFAULT '',
  `enabled` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ifttt`
--

INSERT INTO `ifttt` (`ID`, `name`, `if_Array`, `ifThen_Array`, `opperatorArray`, `parenthaseArray`, `last_ran`, `delay`, `enabled`) VALUES
(142, 'TEST', '<IF>Sensor:34:1</IF> Hallway Motion Open/Active<Done><Condition><IF>Sensor:34:0</IF> Hallway Motion Closed/Inactive<Done><Condition>', '<THEN>UINotification:0:MessageModal:Test|Test1|http://localhost/images/logo.png|30</THEN> Show Message Dialog On User: All Users for 30 seconds<Done><Action>', ':+:+++', ':(:|:):+:(:|:):+:|:+:|:+', '1490629842', '18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `iot_nodes`
--

CREATE TABLE `iot_nodes` (
  `ID` int(11) NOT NULL,
  `node_id` varchar(200) NOT NULL,
  `node_type` varchar(25) NOT NULL,
  `room` varchar(100) NOT NULL,
  `time_added` varchar(100) NOT NULL,
  `enabled` int(11) NOT NULL,
  `state` varchar(15) NOT NULL DEFAULT 'Disconnected',
  `last_connected_time` varchar(50) NOT NULL,
  `decription` varchar(200) NOT NULL,
  `notifications` tinyint(1) NOT NULL,
  `error_timeout` smallint(4) NOT NULL,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `signal_strength` varchar(5) NOT NULL DEFAULT '0',
  `SSID` varchar(50) NOT NULL,
  `version` varchar(10) NOT NULL,
  `uptime` varchar(25) NOT NULL,
  `mfDate` varchar(10) NOT NULL,
  `SSID_PASSWORD` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `iot_nodes`
--

INSERT INTO `iot_nodes` (`ID`, `node_id`, `node_type`, `room`, `time_added`, `enabled`, `state`, `last_connected_time`, `decription`, `notifications`, `error_timeout`, `ip_address`, `signal_strength`, `SSID`, `version`, `uptime`, `mfDate`, `SSID_PASSWORD`) VALUES
(28, 'IRTX1_MQTT', 'Transmit', '0', '1485716119', 1, 'Disconnected', '1488374830', 'Empty', 0, 5, '10.0.0.6', '-25', 'NETGEAR_INTERNET', '1', '300', '1-28-17', '***'),
(34, '433RF1_MQTT', 'Transmit', '0', '1487903659', 1, 'Disconnected', '1488374850', 'Empty', 0, 5, '10.0.0.7', '-65', 'NETGEAR_INTERNET', '1', '387', '2-23-17', '***');

-- --------------------------------------------------------

--
-- Table structure for table `iot_recieve_que`
--

CREATE TABLE `iot_recieve_que` (
  `ID` bigint(20) NOT NULL,
  `from_node_id` varchar(200) NOT NULL,
  `time_recieved` varchar(50) NOT NULL,
  `data_recieved` varchar(200) NOT NULL,
  `errors` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `music_data`
--

CREATE TABLE `music_data` (
  `ID` int(11) NOT NULL,
  `song_location` varchar(200) NOT NULL,
  `song_added` varchar(25) NOT NULL,
  `song_type` varchar(25) NOT NULL,
  `song_name` varchar(200) NOT NULL,
  `playlist` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `music_playlists`
--

CREATE TABLE `music_playlists` (
  `ID` int(11) NOT NULL,
  `playlist_name` varchar(50) NOT NULL,
  `date_added` varchar(20) NOT NULL,
  `created_by` varchar(25) NOT NULL,
  `playlist_url` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `music_playlists`
--

INSERT INTO `music_playlists` (`ID`, `playlist_name`, `date_added`, `created_by`, `playlist_url`) VALUES
(2, 'Playlist 1', '02-12-2017', '1', '');

-- --------------------------------------------------------

--
-- Table structure for table `music_servers`
--

CREATE TABLE `music_servers` (
  `ID` int(11) NOT NULL,
  `ip_address` varchar(25) NOT NULL,
  `room_id` int(11) NOT NULL,
  `player_id` varchar(50) NOT NULL DEFAULT '0',
  `enabled` int(11) NOT NULL,
  `player_index` int(11) NOT NULL,
  `player_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(11) NOT NULL,
  `dbase` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `user` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `query` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `col_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `col_type` varchar(64) COLLATE utf8_bin NOT NULL,
  `col_length` text COLLATE utf8_bin,
  `col_collation` varchar(64) COLLATE utf8_bin NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) COLLATE utf8_bin DEFAULT '',
  `col_default` text COLLATE utf8_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `column_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `transformation` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `transformation_options` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `input_transformation` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `settings_data` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

--
-- Dumping data for table `pma__designer_settings`
--

INSERT INTO `pma__designer_settings` (`username`, `settings_data`) VALUES
('root', '{\"angular_direct\":\"direct\",\"snap_to_grid\":\"off\",\"relation_lines\":\"true\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `export_type` varchar(10) COLLATE utf8_bin NOT NULL,
  `template_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `template_data` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `tables` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `db` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sqlquery` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `item_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `item_type` varchar(64) COLLATE utf8_bin NOT NULL,
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `tables` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"smarthome\",\"table\":\"weather_data\"},{\"db\":\"smarthome\",\"table\":\"iot_nodes\"},{\"db\":\"smarthome\",\"table\":\"history_log\"},{\"db\":\"smarthome\",\"table\":\"users\"},{\"db\":\"smarthome\",\"table\":\"ui_notifications\"},{\"db\":\"smarthome\",\"table\":\"shbroker\"},{\"db\":\"smarthome\",\"table\":\"settings\"},{\"db\":\"smarthome\",\"table\":\"scene_events\"},{\"db\":\"smarthome\",\"table\":\"scene\"},{\"db\":\"smarthome\",\"table\":\"music_servers\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `master_table` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `master_field` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `foreign_db` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `foreign_table` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `foreign_field` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `search_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `search_data` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT '0',
  `x` float UNSIGNED NOT NULL DEFAULT '0',
  `y` float UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `display_field` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `prefs` text COLLATE utf8_bin NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

--
-- Dumping data for table `pma__table_uiprefs`
--

INSERT INTO `pma__table_uiprefs` (`username`, `db_name`, `table_name`, `prefs`, `last_update`) VALUES
('root', 'smarthome', 'device_groups', '{\"sorted_col\":\"`device_groups`.`group_icon` ASC\"}', '2017-03-13 13:13:14'),
('root', 'smarthome', 'event_log', '{\"sorted_col\":\"`event_log`.`ID`  DESC\"}', '2016-10-07 00:57:19'),
('root', 'smarthome', 'history_log', '{\"sorted_col\":\"`history_log`.`device_type`  DESC\"}', '2016-11-22 23:46:06'),
('root', 'smarthome', 'music_data', '{\"sorted_col\":\"`ID`  DESC\"}', '2016-12-03 03:09:00'),
('root', 'smarthome', 'music_servers', '{\"sorted_col\":\"`room_id` ASC\"}', '2016-12-26 02:50:59'),
('root', 'smarthome', 'scene_events', '{\"sorted_col\":\"`scene_events`.`ID`  DESC\"}', '2017-02-14 18:54:12'),
('root', 'smarthome', 'sensors', '{\"sorted_col\":\"`sensors`.`sensor_type` ASC\"}', '2016-12-17 23:34:37'),
('root', 'smarthome', 'weather_data', '[]', '2017-03-28 13:24:26'),
('root', 'smarthome', 'whoishome', '{\"sorted_col\":\"`whoishome`.`ID` ASC\"}', '2016-12-05 13:38:00');

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text COLLATE utf8_bin NOT NULL,
  `schema_sql` text COLLATE utf8_bin,
  `data_sql` longtext COLLATE utf8_bin,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') COLLATE utf8_bin DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `config_data` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2016-09-27 13:42:36', '{\"collation_connection\":\"utf8mb4_unicode_ci\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) COLLATE utf8_bin NOT NULL,
  `tab` varchar(64) COLLATE utf8_bin NOT NULL,
  `allowed` enum('Y','N') COLLATE utf8_bin NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `usergroup` varchar(64) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

-- --------------------------------------------------------

--
-- Table structure for table `scene`
--

CREATE TABLE `scene` (
  `ID` int(11) NOT NULL,
  `scene_name` varchar(50) NOT NULL,
  `scene_icon` varchar(25) NOT NULL,
  `scene_enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `scene`
--

INSERT INTO `scene` (`ID`, `scene_name`, `scene_icon`, `scene_enabled`) VALUES
(3, 'Leaving Home', 'fa-power-off', 1),
(7, 'Goonight', 'fa-power-off', 1),
(8, 'Normal', 'fa-power-off', 1);

-- --------------------------------------------------------

--
-- Table structure for table `scene_events`
--

CREATE TABLE `scene_events` (
  `ID` int(11) NOT NULL,
  `event_name` varchar(500) NOT NULL,
  `event_title` varchar(500) NOT NULL,
  `scene_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE `sensors` (
  `ID` bigint(20) NOT NULL,
  `sensor_name` varchar(200) NOT NULL,
  `sensor_address` varchar(200) NOT NULL,
  `sensor_close_address` varchar(200) NOT NULL,
  `sensor_state` tinyint(1) NOT NULL,
  `sensor_kind` varchar(50) NOT NULL,
  `sensor_type` varchar(25) NOT NULL,
  `room` varchar(100) NOT NULL,
  `time_triggered` varchar(50) NOT NULL,
  `last_triggered` varchar(25) NOT NULL DEFAULT '',
  `is_alarmSensorHome` int(1) NOT NULL,
  `is_alarmSensorAway` int(1) NOT NULL,
  `last_changed_by` varchar(50) DEFAULT '',
  `notifications` int(11) NOT NULL DEFAULT '0',
  `already_notified` int(11) DEFAULT '0',
  `enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `ID` int(11) NOT NULL,
  `name` varchar(150) NOT NULL DEFAULT '',
  `home_address` varchar(100) NOT NULL,
  `home_latLong` varchar(500) NOT NULL,
  `city_state` varchar(100) NOT NULL,
  `zip_code` varchar(10) NOT NULL DEFAULT '',
  `outgoing_email_list` varchar(500) NOT NULL DEFAULT '',
  `scan_for_new_sensors` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`ID`, `name`, `home_address`, `home_latLong`, `city_state`, `zip_code`, `outgoing_email_list`, `scan_for_new_sensors`) VALUES
(1, 'Brad Sanders', '', '', '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `shbroker`
--

CREATE TABLE `shbroker` (
  `ID` int(11) NOT NULL,
  `page_name` varchar(35) NOT NULL,
  `proc_id` int(11) NOT NULL,
  `state` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shbroker`
--

INSERT INTO `shbroker` (`ID`, `page_name`, `proc_id`, `state`) VALUES
(1, 'SystemTimer.php', 11632, 1),
(2, 'SystemServices.php', 9788, 1),
(3, 'SystemMonitor.php', 10372, 1),
(4, 'SystemMQTTInbox.php', 7280, 1),
(5, 'HomeStatus.php', 3784, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ui_notifications`
--

CREATE TABLE `ui_notifications` (
  `ID` int(11) NOT NULL,
  `notificationType` varchar(50) NOT NULL,
  `notificatonData` varchar(1000) NOT NULL,
  `user_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `alarm_pin` int(11) NOT NULL DEFAULT '0',
  `last_login` varchar(50) NOT NULL,
  `last_access_ip` varchar(25) NOT NULL,
  `type` varchar(50) NOT NULL,
  `decription` varchar(200) NOT NULL,
  `user_img` varchar(200) NOT NULL,
  `user_workAddress` varchar(100) NOT NULL,
  `user_workLatLong` varchar(500) NOT NULL,
  `enabled` int(11) NOT NULL,
  `user_permissions` varchar(2000) NOT NULL,
  `room_assign` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `user_name`, `username`, `password`, `alarm_pin`, `last_login`, `last_access_ip`, `type`, `decription`, `user_img`, `user_workAddress`, `user_workLatLong`, `enabled`, `user_permissions`, `room_assign`) VALUES
(1, 'Admin Account', 'Admin', '1234', 1234, '1490707899', '172.21.2.77', 'Admin', 'Admin Account', 'Default.png', '', '', 1, ':alarm_access|readedit|:index.php|read|edit|add|delete|:ifttt_simple.php|read|:ifttt_advanced.php|read|edit|add|delete|:manage_buttons.php|read|edit|add|delete|:manage_music.php|read|edit|add|delete|:manage_users.php|read|edit|add|delete|:manage_devices.php|read|edit|add|delete|:manage_sensors.php|read|edit|add|delete|:manage_dataSensors.php|read|edit|add|delete|:events_log.php|read|delete|:manage_settings.php|read|edit|:sensor_history_log.php|read|:device_history_log.php|read|:manage_room.php|read|edit|add|delete|2|3|4|5|6|:manage_cameras.php|read|edit|add|delete|:manage_scene.php|read|edit|add|delete|3|7|8|', 3);

-- --------------------------------------------------------

--
-- Table structure for table `weather_data`
--

CREATE TABLE `weather_data` (
  `city_name` varchar(100) NOT NULL,
  `ID` int(11) NOT NULL,
  `temp` tinyint(3) NOT NULL,
  `humidity` tinyint(3) NOT NULL,
  `heat_index` varchar(5) NOT NULL,
  `temp_min` tinyint(3) NOT NULL,
  `temp_max` tinyint(3) NOT NULL,
  `temp_condition` varchar(50) NOT NULL,
  `sunrise_time` varchar(50) NOT NULL,
  `sunset_time` varchar(50) NOT NULL,
  `wind_speed` varchar(10) NOT NULL,
  `last_updated` varchar(20) NOT NULL,
  `update_interval` varchar(5) NOT NULL,
  `day_added` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `weather_data`
--

INSERT INTO `weather_data` (`city_name`, `ID`, `temp`, `humidity`, `heat_index`, `temp_min`, `temp_max`, `temp_condition`, `sunrise_time`, `sunset_time`, `wind_speed`, `last_updated`, `update_interval`, `day_added`) VALUES
('Salina', 1, 70, 60, '', 70, 70, 'broken clouds', '06:27:25 AM', '06:46:25 PM', '25.28', '1490298317', '30', 'Monday'),
('Salina', 2, 70, 60, '', 70, 70, 'broken clouds', '06:27:25 AM', '06:46:25 PM', '25.28', '1490298317', '30', 'Tuesday'),
('Salina', 3, 60, 60, '68', 70, 70, 'broken clouds', '06:27:25 AM', '06:46:25 PM', '25.28', '1490298317', '30', 'Wednsday'),
('Salina', 4, 75, 53, '77', 75, 75, 'clear sky', '06:27:16 AM', '06:46:30 PM', '19.46', '1490306410', '30', 'Thursday'),
('Salina', 5, 73, 40, '77', 73, 73, 'clear sky', '06:25:43 AM', '06:47:28 PM', '17.22', '1490392210', '30', 'Friday'),
('Salina', 6, 70, 60, '', 70, 70, 'broken clouds', '06:27:25 AM', '06:46:25 PM', '25.28', '1490298465', '30', 'Saturday'),
('Salina', 7, 70, 60, '', 70, 70, 'broken clouds', '06:27:25 AM', '06:46:25 PM', '25.28', '1490298317', '30', 'Sunday');

-- --------------------------------------------------------

--
-- Table structure for table `whoishome`
--

CREATE TABLE `whoishome` (
  `ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `check_method` varchar(100) NOT NULL,
  `check_string` varchar(100) NOT NULL,
  `check_data` varchar(500) NOT NULL,
  `home` int(11) NOT NULL DEFAULT '0',
  `last_at_home` varchar(25) NOT NULL,
  `work` int(11) NOT NULL DEFAULT '0',
  `last_at_work` varchar(25) NOT NULL,
  `enabled` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `whoishome`
--

INSERT INTO `whoishome` (`ID`, `user_id`, `check_method`, `check_string`, `check_data`, `home`, `last_at_home`, `work`, `last_at_work`, `enabled`) VALUES
(1, 1, 'icloud', '||||0.3', '', 0, '', 0, '', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alarm_status`
--
ALTER TABLE `alarm_status`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `buttons`
--
ALTER TABLE `buttons`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `camera_list`
--
ALTER TABLE `camera_list`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `custom_scripts`
--
ALTER TABLE `custom_scripts`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `dashboard_cards`
--
ALTER TABLE `dashboard_cards`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `data_sensors`
--
ALTER TABLE `data_sensors`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `device_groups`
--
ALTER TABLE `device_groups`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `enabled_services`
--
ALTER TABLE `enabled_services`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `event_log`
--
ALTER TABLE `event_log`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `find_sensors_list`
--
ALTER TABLE `find_sensors_list`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `history_log`
--
ALTER TABLE `history_log`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `home_rooms`
--
ALTER TABLE `home_rooms`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ifttt`
--
ALTER TABLE `ifttt`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `iot_nodes`
--
ALTER TABLE `iot_nodes`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `node_id` (`node_id`);

--
-- Indexes for table `iot_recieve_que`
--
ALTER TABLE `iot_recieve_que`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `music_data`
--
ALTER TABLE `music_data`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `music_playlists`
--
ALTER TABLE `music_playlists`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `music_servers`
--
ALTER TABLE `music_servers`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- Indexes for table `scene`
--
ALTER TABLE `scene`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `scene_events`
--
ALTER TABLE `scene_events`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `sensors`
--
ALTER TABLE `sensors`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `shbroker`
--
ALTER TABLE `shbroker`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ui_notifications`
--
ALTER TABLE `ui_notifications`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `weather_data`
--
ALTER TABLE `weather_data`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `whoishome`
--
ALTER TABLE `whoishome`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alarm_status`
--
ALTER TABLE `alarm_status`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `buttons`
--
ALTER TABLE `buttons`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `camera_list`
--
ALTER TABLE `camera_list`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `custom_scripts`
--
ALTER TABLE `custom_scripts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `dashboard_cards`
--
ALTER TABLE `dashboard_cards`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT for table `data_sensors`
--
ALTER TABLE `data_sensors`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `device_groups`
--
ALTER TABLE `device_groups`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `enabled_services`
--
ALTER TABLE `enabled_services`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `event_log`
--
ALTER TABLE `event_log`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `find_sensors_list`
--
ALTER TABLE `find_sensors_list`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `history_log`
--
ALTER TABLE `history_log`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `home_rooms`
--
ALTER TABLE `home_rooms`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `ifttt`
--
ALTER TABLE `ifttt`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;
--
-- AUTO_INCREMENT for table `iot_nodes`
--
ALTER TABLE `iot_nodes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `music_data`
--
ALTER TABLE `music_data`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `music_playlists`
--
ALTER TABLE `music_playlists`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `music_servers`
--
ALTER TABLE `music_servers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `scene`
--
ALTER TABLE `scene`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `scene_events`
--
ALTER TABLE `scene_events`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sensors`
--
ALTER TABLE `sensors`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `shbroker`
--
ALTER TABLE `shbroker`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ui_notifications`
--
ALTER TABLE `ui_notifications`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `whoishome`
--
ALTER TABLE `whoishome`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
