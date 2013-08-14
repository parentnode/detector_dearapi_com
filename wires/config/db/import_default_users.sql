INSERT INTO `users_access_points` (`id`, `name`, `file`) VALUES 
(10, 'Login', 'FRAMEWORK_PATH/admin/index.php'),
(20, 'Frontpage', 'FRAMEWORK_PATH/admin/front/index.php'),

(30, 'Users - levels', 'FRAMEWORK_PATH/admin/access/levels.php'),
(31, 'Users - menu', 'FRAMEWORK_PATH/admin/access/menu.php'),
(32, 'Users - points', 'FRAMEWORK_PATH/admin/access/points.php'),
(33, 'Users', 'FRAMEWORK_PATH/admin/access/users.php'),

(40, 'Basics - countries', 'FRAMEWORK_PATH/admin/basics/countries.php'),
(41, 'Basics - languages', 'FRAMEWORK_PATH/admin/basics/languages.php'),
(42, 'Basics - itemtypes', 'FRAMEWORK_PATH/admin/basics/itemtypes.php'),

(50, 'Library', 'FRAMEWORK_PATH/admin/items/items.php'),
(51, 'Add content', 'FRAMEWORK_PATH/admin/items/items_new.php'),

(60, 'Pages', 'FRAMEWORK_PATH/admin/sites/navigation.php'),

(70, 'Tags', 'FRAMEWORK_PATH/admin/items/tags.php'),
(75, 'Price groups', 'FRAMEWORK_PATH/admin/items/price_group.php');


INSERT INTO `users_access_levels` (`id`, `name`, `notes`) VALUES 
(1, 'Developer', ''),
(2, 'Service', ''),
(3, 'Admin', ''),
(4, 'Translater', '');


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
(DEFAULT, 1, 40, 'edit'),
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

(DEFAULT, 1, 50, 'list'),
(DEFAULT, 1, 50, 'view'),
(DEFAULT, 1, 50, 'edit'),
(DEFAULT, 1, 50, 'file'),
(DEFAULT, 1, 50, 'delete'),
(DEFAULT, 1, 50, 'tags'),
(DEFAULT, 1, 50, 'description_edit'),
(DEFAULT, 1, 50, 'prices_edit'),

(DEFAULT, 1, 51, 'new'),

(DEFAULT, 1, 60, 'list'),
(DEFAULT, 1, 60, 'view'),
(DEFAULT, 1, 60, 'edit'),
(DEFAULT, 1, 60, 'new'),
(DEFAULT, 1, 60, 'delete'),

(DEFAULT, 1, 70, 'list'),
(DEFAULT, 1, 70, 'view'),
(DEFAULT, 1, 70, 'edit'),
(DEFAULT, 1, 70, 'new'),
(DEFAULT, 1, 70, 'delete'),

(DEFAULT, 1, 75, 'list'),
(DEFAULT, 1, 75, 'view'),
(DEFAULT, 1, 75, 'edit'),
(DEFAULT, 1, 75, 'new'),
(DEFAULT, 1, 75, 'delete');


INSERT INTO `users` (`id`, `user_id`, `access_level_id`) VALUES 
('1', 'b53a4e0b-1a56-4ecd-a19c-2c08d6a523fd', 1),
('2', 'f3ca9089-0cf7-458c-be11-c4033c91cdd4', 1);


INSERT INTO `users_menu` (`id`, `name`, `url`, `relation`, `sequence`) VALUES 
(80, 'Library', 'FRAMEWORK_PATH/admin/items/items.php', 0, 1),
(81, 'Add content', 'FRAMEWORK_PATH/admin/items/items_new.php', 0, 2),
(82, '----', '', 0, 3),
(70, 'Pages', 'FRAMEWORK_PATH/admin/sites/navigation.php', 0, 4),
(65, 'Tags', 'FRAMEWORK_PATH/admin/items/tags.php', 0, 5),

(60, '----', '', 0, 6),
(55, 'Price groups', 'FRAMEWORK_PATH/admin/items/price_group.php', 0, 7),

(40, '----', '', 0, 54),
(30, 'Basics', '', 0, 55),
	(31, 'Countries', 'FRAMEWORK_PATH/admin/basics/countries.php', 30, 0),
	(32, 'Languages', 'FRAMEWORK_PATH/admin/basics/languages.php', 30, 1),
	(33, '----', '', 30, 2),
	(34, 'Itemtypes', 'FRAMEWORK_PATH/admin/basics/itemtypes.php', 30, 3),

(20, '----', '', 0, 56),
(10, 'System', '', 0, 57),
	(11, 'Menu', 'FRAMEWORK_PATH/admin/access/menu.php', 10, 0),
	(12, 'Users', 'FRAMEWORK_PATH/admin/access/users.php', 10, 1),
	(13, 'Access', '', 10, 2),
		(14, 'Levels', 'FRAMEWORK_PATH/admin/access/levels.php', 13, 0),
		(15, 'Points', 'FRAMEWORK_PATH/admin/access/points.php', 13, 1);
