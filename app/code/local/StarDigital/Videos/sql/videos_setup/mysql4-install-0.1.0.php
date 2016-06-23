<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `videos` (
  `video_id` int(11) NOT NULL AUTO_INCREMENT,
  `video_name` varchar(100) NOT NULL,
  `video_description` varchar(500) NOT NULL,
  `thumbnail` varchar(100) NOT NULL,
  `video_link` varchar(100) NOT NULL,
  PRIMARY KEY (`video_id`),
  KEY `video_id` (`video_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 