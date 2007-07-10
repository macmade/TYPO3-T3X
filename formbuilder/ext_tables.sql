#
# Table structure for table 'tx_formbuilder_datastructure'
#
CREATE TABLE tx_formbuilder_datastructure (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	post_message text NOT NULL,
	be_groups blob NOT NULL,
	recipients text NOT NULL,
	datasend tinyint(3) unsigned DEFAULT '0' NOT NULL,
	submit tinytext NOT NULL,
	preview tinyint(3) unsigned DEFAULT '0' NOT NULL,
	destination blob NOT NULL,
	redirect blob NOT NULL,
	redirect_time tinytext NOT NULL,
	xmlds blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_formbuilder_formdata'
#
CREATE TABLE tx_formbuilder_formdata (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	datastructure blob NOT NULL,
	xmldata text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
