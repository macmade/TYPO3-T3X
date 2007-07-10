#
# Table structure for table 'tx_workbook_companies'
#
CREATE TABLE tx_workbook_companies (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	logo blob NOT NULL,
	priority int(11) DEFAULT '0' NOT NULL,
	services blob NOT NULL,
	www tinytext NOT NULL,
	contact_person tinytext NOT NULL,
	contact_email tinytext NOT NULL,
	address text NOT NULL,
	zip tinytext NOT NULL,
	city tinytext NOT NULL,
	country int(11) DEFAULT '0' NOT NULL,
	phone tinytext NOT NULL,
	fax tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_workbook_services'
#
CREATE TABLE tx_workbook_services (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_workbook_emphasis'
#
CREATE TABLE tx_workbook_emphasis (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_workbook_categories'
#
CREATE TABLE tx_workbook_categories (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_workbook_references'
#
CREATE TABLE tx_workbook_references (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	priority int(11) DEFAULT '0' NOT NULL,
	launch int(11) DEFAULT '0' NOT NULL,
	www tinytext NOT NULL,
	status int(11) DEFAULT '0' NOT NULL,
	screenshots blob NOT NULL,
	casestory tinytext NOT NULL,
	emphasis blob NOT NULL,
	categories blob NOT NULL,
	manager int(11) DEFAULT '0' NOT NULL,
	developers blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
