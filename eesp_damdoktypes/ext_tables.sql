#
# Table structure for table 'tx_dam'
#
CREATE TABLE tx_dam (
	tx_eespdamdoktypes_doktype int(11) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_eespdamdoktypes_types'
#
CREATE TABLE tx_eespdamdoktypes_types (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	type tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
