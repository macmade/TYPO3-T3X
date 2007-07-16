#
# Table structure for table 'tx_vinatura_winetypes'
#
CREATE TABLE tx_vinatura_winetypes (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	feuser blob NOT NULL,
	title tinytext NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_vinatura_wines'
#
CREATE TABLE tx_vinatura_wines (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	feuser blob NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_vinatura_profiles'
#
CREATE TABLE tx_vinatura_profiles (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	feuser blob NOT NULL,
	domain tinytext NOT NULL,
	firstname tinytext NOT NULL,
	cellular tinytext NOT NULL,
	surface tinytext NOT NULL,
	state blob NOT NULL,
	description text NOT NULL,
	prices tinytext NOT NULL,
	whitewines blob NOT NULL,
	redwines blob NOT NULL,
	distribution text NOT NULL,
	restaurants text NOT NULL,
	events text NOT NULL,
	member tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE fe_users (
  title varchar(100) DEFAULT '' NOT NULL
);
