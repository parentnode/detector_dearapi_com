CREATE TABLE `SITE_DB`.`devices_unidentified` (
  `id` int(11) NOT NULL auto_increment,

  `useragent` text NOT NULL,
  `comment` text,

  `identified_as` varchar(11) NOT NULL,
  `identified_at` timestamp NOT NULL default CURRENT_TIMESTAMP,

  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
