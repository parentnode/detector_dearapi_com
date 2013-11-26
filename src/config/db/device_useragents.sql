CREATE TABLE `SITE_DB`.`device_useragents` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,

  `useragent` text NOT NULL,

  PRIMARY KEY  (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `device_useragents_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;