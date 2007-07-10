#
# Table structure for table 'tx_weledababy_pictures'
#
CREATE TABLE tx_weledababy_pictures (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	feuser blob NOT NULL,
	title tinytext NOT NULL,
	picture blob NOT NULL,
	description text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_weledababy_articles'
#
CREATE TABLE tx_weledababy_articles (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	feuser blob NOT NULL,
	title tinytext NOT NULL,
	article text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_weledababy_profiles_friends_mm'
# 
#
CREATE TABLE tx_weledababy_profiles_friends_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	confirmed int(11) DEFAULT '0' NOT NULL,
	refused int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_weledababy_profiles'
#
CREATE TABLE tx_weledababy_profiles (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	feuser blob NOT NULL,
	birth int(11) DEFAULT '0' NOT NULL,
	description text NOT NULL,
	friends int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_weledababy_babies'
#
CREATE TABLE tx_weledababy_babies (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	feuser blob NOT NULL,
	name tinytext NOT NULL,
	sex int(11) DEFAULT '0' NOT NULL,
	birth int(11) DEFAULT '0' NOT NULL,
	picture blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
