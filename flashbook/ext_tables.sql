#
# Table structure for table 'tx_flashbook_books'
#
CREATE TABLE tx_flashbook_books (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	width tinytext NOT NULL,
	height tinytext NOT NULL,
	hardcover tinyint(4) DEFAULT '0' NOT NULL,
	transparency tinyint(4) DEFAULT '0' NOT NULL,
	pages longblob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
