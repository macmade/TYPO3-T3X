#
# Table structure for table 'tx_slideshow_slideshows'
#
CREATE TABLE tx_slideshow_slideshows (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	pictures longblob NOT NULL,
	dir_path tinytext NOT NULL,
	dir_pictures blob NOT NULL,
	dir_url tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
