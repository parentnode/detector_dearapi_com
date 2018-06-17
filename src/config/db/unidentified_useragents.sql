CREATE TABLE `SITE_DB`.`unidentified_useragents` (
  `id` int(11) NOT NULL auto_increment,

  `useragent` text NOT NULL,
  `comment` text NOT NULL DEFAULT '',

  `identified_as` varchar(30) NOT NULL DEFAULT '',
  `identified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
