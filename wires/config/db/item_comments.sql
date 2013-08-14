CREATE TABLE `REGIONAL_DB`.`item_comments` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `comment` text default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ip` varchar(20) NOT NULL,

  `item_id` int(11) NOT NULL,
  `language_id` varchar(2) NOT NULL,

  PRIMARY KEY  (`id`),

  KEY `item_id` (`item_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `item_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `item_comments_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `basics_languages` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
