INSERT INTO `users_access_points` (`id`, `name`, `file`) VALUES 
(10, 'Login', 'FRAMEWORK_PATH/admin/index.php'),
(20, 'Frontpage', 'FRAMEWORK_PATH/admin/front/index.php'),

(30, 'Users - levels', 'FRAMEWORK_PATH/admin/access/levels.php'),
(31, 'Users - menu', 'FRAMEWORK_PATH/admin/access/menu.php'),
(32, 'Users - points', 'FRAMEWORK_PATH/admin/access/points.php'),
(33, 'Users', 'FRAMEWORK_PATH/admin/access/users.php'),

(40, 'Basics - countries', 'FRAMEWORK_PATH/admin/basics/countries.php'),
(41, 'Basics - languages', 'FRAMEWORK_PATH/admin/basics/languages.php'),
(42, 'Basics - contenttypes', 'FRAMEWORK_PATH/admin/basics/contenttypes.php'),
(43, 'Basics - brands', 'FRAMEWORK_PATH/admin/basics/brands.php'),

(80, 'Devices', 'GLOBAL_PATH/admin/devices/devices.php'),
(81, 'Unidentified devices', 'GLOBAL_PATH/admin/devices/devices_unidentified.php');


INSERT INTO `users_access_levels` (`id`, `name`, `notes`) VALUES 
(1, 'Developer', '');


INSERT INTO `users_access_level_points` (`id`, `level_id`, `point_id`, `action`) VALUES 
(DEFAULT, 1, 30, 'list'),
(DEFAULT, 1, 30, 'view'),
(DEFAULT, 1, 30, 'edit'),
(DEFAULT, 1, 30, 'new'),
(DEFAULT, 1, 30, 'delete'),
(DEFAULT, 1, 31, 'list'),
(DEFAULT, 1, 31, 'view'),
(DEFAULT, 1, 31, 'edit'),
(DEFAULT, 1, 31, 'new'),
(DEFAULT, 1, 31, 'delete'),
(DEFAULT, 1, 31, 'structure_update'),
(DEFAULT, 1, 32, 'list'),
(DEFAULT, 1, 32, 'edit'),
(DEFAULT, 1, 33, 'list'),
(DEFAULT, 1, 33, 'view'),
(DEFAULT, 1, 33, 'edit'),
(DEFAULT, 1, 33, 'new'),
(DEFAULT, 1, 33, 'delete'),

(DEFAULT, 1, 40, 'list'),
(DEFAULT, 1, 40, 'view'),
(DEFAULT, 1, 40, 'new'),
(DEFAULT, 1, 40, 'delete'),
(DEFAULT, 1, 41, 'list'),
(DEFAULT, 1, 41, 'view'),
(DEFAULT, 1, 41, 'new'),
(DEFAULT, 1, 41, 'delete'),
(DEFAULT, 1, 42, 'list'),
(DEFAULT, 1, 42, 'view'),
(DEFAULT, 1, 42, 'edit'),
(DEFAULT, 1, 42, 'new'),
(DEFAULT, 1, 42, 'delete'),
(DEFAULT, 1, 43, 'list'),
(DEFAULT, 1, 43, 'view'),
(DEFAULT, 1, 43, 'edit'),
(DEFAULT, 1, 43, 'new'),
(DEFAULT, 1, 43, 'delete'),

(DEFAULT, 1, 80, 'list'),
(DEFAULT, 1, 80, 'view'),
(DEFAULT, 1, 80, 'edit'),
(DEFAULT, 1, 80, 'new'),
(DEFAULT, 1, 80, 'delete'),
(DEFAULT, 1, 81, 'list'),
(DEFAULT, 1, 81, 'view'),
(DEFAULT, 1, 81, 'select_device'),
(DEFAULT, 1, 81, 'add_to_device'),
(DEFAULT, 1, 81, 'new_device'),
(DEFAULT, 1, 81, 'delete');


INSERT INTO `users` (`id`, `user_id`, `access_level_id`) VALUES 
('1', 'b53a4e0b-1a56-4ecd-a19c-2c08d6a523fd', 1),
('2', 'f3ca9089-0cf7-458c-be11-c4033c91cdd4', 1);


INSERT INTO `users_menu` (`id`, `name`, `url`, `relation`, `sequence`) VALUES 
(48, 'Devices', '', 0, 53),
	(47, 'Devices', 'GLOBAL_PATH/admin/devices/devices.php', 48, 0),
	(46, '----', '', 48, 2),
	(45, 'Unidentified devices', 'GLOBAL_PATH/admin/devices/devices_unidentified.php', 48, 4),

(40, '----', '', 0, 54),
(30, 'Basics', '', 0, 55),
	(31, 'Countries', 'FRAMEWORK_PATH/admin/basics/countries.php', 30, 0),
	(32, 'Languages', 'FRAMEWORK_PATH/admin/basics/languages.php', 30, 1),
	(33, '----', '', 30, 2),
	(35, 'Contenttypes', 'FRAMEWORK_PATH/admin/basics/contenttypes.php', 30, 3),
	(36, 'Brands', 'FRAMEWORK_PATH/admin/basics/brands.php', 30, 4),

(20, '----', '', 0, 56),
(10, 'System', '', 0, 57),
	(11, 'Menu', 'FRAMEWORK_PATH/admin/access/menu.php', 10, 0),
	(12, 'Users', 'FRAMEWORK_PATH/admin/access/users.php', 10, 1),
	(13, 'Access', '', 10, 2),
		(14, 'Levels', 'FRAMEWORK_PATH/admin/access/levels.php', 13, 0),
		(15, 'Points', 'FRAMEWORK_PATH/admin/access/points.php', 13, 1);
