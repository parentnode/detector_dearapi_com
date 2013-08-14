CREATE TABLE `REGIONAL_DB`.`itemtype_blog` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `html` text default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,

  `item_id` int(11) NOT NULL,
  `language_id` varchar(2) NOT NULL,

  PRIMARY KEY  (`id`),

  KEY `item_id` (`item_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `item_blogs_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_blogs_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `basics_languages` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;