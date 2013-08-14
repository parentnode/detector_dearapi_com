CREATE TABLE `REGIONAL_DB`.`itemtype_bundle_items` (
	`id` int(11) NOT NULL auto_increment,
	`value_pct` int(11) default NULL,

	`bundle_item_id` int(11) NOT NULL,
	`item_id` int(11) NOT NULL,

	PRIMARY KEY  (`id`),

	KEY `bundle_item_id` (`bundle_item_id`),
	KEY `item_id` (`item_id`),

	CONSTRAINT `itemtype_bundle_items_ibfk_1` FOREIGN KEY (`bundle_item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `itemtype_bundle_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;