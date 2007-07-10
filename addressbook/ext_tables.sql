#
# Table structure for table 'tx_addressbook_groups'
#
CREATE TABLE tx_addressbook_groups (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_addressbook_addresses_groups_mm'
# 
#
CREATE TABLE tx_addressbook_addresses_groups_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_addressbook_addresses'
#
CREATE TABLE tx_addressbook_addresses (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	firstname tinytext NOT NULL,
	lastname tinytext NOT NULL,
	nickname tinytext NOT NULL,
	jobtitle tinytext NOT NULL,
	department tinytext NOT NULL,
	company tinytext NOT NULL,
	type tinyint(3) DEFAULT '0' NOT NULL,
	picture blob NOT NULL,
	homepage tinytext NOT NULL,
	birthday int(11) DEFAULT '0' NOT NULL,
	groups int(11) DEFAULT '0' NOT NULL,
	phone blob NOT NULL,
	email blob NOT NULL,
	messenger blob NOT NULL,
	address blob NOT NULL,
	notes text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
