#
# Table structure for table 'tx_vdmunicipalities_municipalities'
#
CREATE TABLE tx_vdmunicipalities_municipalities (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	name_lower tinytext NOT NULL,
	name_lower15 tinytext NOT NULL,
	name_upper tinytext NOT NULL,
	name_upper15 tinytext NOT NULL,
	id_state int(11) DEFAULT '0' NOT NULL,
	id_district int(11) DEFAULT '0' NOT NULL,
	id_municipality int(11) DEFAULT '0' NOT NULL,
	surface int(11) DEFAULT '0' NOT NULL,
	objectid int(11) DEFAULT '0' NOT NULL,
	idex2000 int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
