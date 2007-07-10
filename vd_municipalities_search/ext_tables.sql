#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_vdmunicipalitiessearch_institution int(11) DEFAULT '0' NOT NULL,
	tx_vdmunicipalitiessearch_municipalities blob NOT NULL,
);

#
# Table structure for table 'tx_vdmunicipalitiessearch_institutions'
#
CREATE TABLE tx_vdmunicipalitiessearch_institutions (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
