CREATE TABLE `REGIONAL_DB`.`itemtype_photo` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,

  `item_id` int(11) NOT NULL,

  PRIMARY KEY  (`id`),

  KEY `item_id` (`item_id`),
  CONSTRAINT `item_photo_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;