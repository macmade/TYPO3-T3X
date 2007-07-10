#
# Table structure for table 'tx_weblinks_categories'
#
CREATE TABLE tx_weblinks_categories (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	parent int(11) unsigned DEFAULT '0' NOT NULL,
	incharge blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_weblinks_links'
#
CREATE TABLE tx_weblinks_links (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	category blob NOT NULL,
	url tinytext NOT NULL,
	bug tinyint(4) unsigned DEFAULT '0' NOT NULL,
	target tinyint(3) unsigned DEFAULT '0' NOT NULL,
	description text NOT NULL,
	picture blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
