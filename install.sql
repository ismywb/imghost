DROP TABLE IF EXISTS `imgs`;
CREATE TABLE `imgs` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `img` text NOT NULL,
  `views` int(10) NOT NULL DEFAULT '0',
  `lastaccess` varchar(255) NOT NULL DEFAULT '-1',
  `name` varchar(255) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `filezise` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=latin1;

