#
# Table structure for table 'tx_classifiedsmacmade_categories'
#
CREATE TABLE tx_classifiedsmacmade_categories (
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
	parent int(11) unsigned DEFAULT '0' NOT NULL,
	validation tinyint(3) unsigned DEFAULT '0' NOT NULL,
	description tinytext NOT NULL,
	icon blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_classifiedsmacmade_ads'
#
CREATE TABLE tx_classifiedsmacmade_ads (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	fe_cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	adtype int(11) unsigned DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	subtitle tinytext NOT NULL,
	category int(11) unsigned DEFAULT '0' NOT NULL,
	description text NOT NULL,
	price tinytext NOT NULL,
	currency int(11) unsigned DEFAULT '0' NOT NULL,
	price_best int(11) unsigned DEFAULT '0' NOT NULL,
	price_undefined int(11) unsigned DEFAULT '0' NOT NULL,
	pictures blob NOT NULL,
	views int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_classifiedsmacmade_categories_mm'
#
CREATE TABLE tx_classifiedsmacmade_categories_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);