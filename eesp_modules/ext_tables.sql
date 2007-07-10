#
# Table structure for table 'tx_eespmodules_programs'
#
CREATE TABLE tx_eespmodules_programs (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	startyear tinytext NOT NULL,
	term1_startweek int(11) unsigned DEFAULT '0' NOT NULL,
	term2_startweek int(11) unsigned DEFAULT '0' NOT NULL,
	terms longblob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
