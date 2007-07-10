#
# Table structure for table 'tx_eespbooks_books'
#
CREATE TABLE tx_eespbooks_books (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(3) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	bookid int(11) DEFAULT '0' NOT NULL,
	isbn tinytext NOT NULL,
	pubyear int(11) DEFAULT '0' NOT NULL,
	pubplace tinytext NOT NULL,
	pubmanagers text NOT NULL,
	reedition tinyint(4) unsigned DEFAULT '0' NOT NULL,
	price_chf tinytext NOT NULL,
	price_eur tinytext NOT NULL,
	pages int(11) DEFAULT '0' NOT NULL,
	format tinytext NOT NULL,
	physicaldetails tinytext NOT NULL,
	copies int(11) DEFAULT '0' NOT NULL,
	stock int(11) DEFAULT '0' NOT NULL,
	flyers int(11) DEFAULT '0' NOT NULL,
	pdf_locked blob NOT NULL,
	pdf_unlocked blob NOT NULL,
	title tinytext NOT NULL,
	subtitle tinytext NOT NULL,
	authors text NOT NULL,
	collection tinyint(3) unsigned DEFAULT '0' NOT NULL,
	abstract text NOT NULL,
	analysis text NOT NULL,
	bibliography tinytext NOT NULL,
	cover blob NOT NULL,
	original int(11) unsigned DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_eespbooks_stock'
#
CREATE TABLE tx_eespbooks_stock (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	rel_book int(11) unsigned DEFAULT '0' NOT NULL,
	rel_month int(11) unsigned DEFAULT '0' NOT NULL,
	sales_reception int(11) unsigned DEFAULT '0' NOT NULL,
	sales_bill int(11) unsigned DEFAULT '0' NOT NULL,
	sales_free int(11) unsigned DEFAULT '0' NOT NULL,
	students_distribution int(11) unsigned DEFAULT '0' NOT NULL,
	students_exchange int(11) unsigned DEFAULT '0' NOT NULL,
	events_out int(11) unsigned DEFAULT '0' NOT NULL,
	events_back int(11) unsigned DEFAULT '0' NOT NULL,
	send_albert int(11) unsigned DEFAULT '0' NOT NULL,
	send_cid int(11) unsigned DEFAULT '0' NOT NULL,
	back_students int(11) unsigned DEFAULT '0' NOT NULL,
	back_cid int(11) unsigned DEFAULT '0' NOT NULL,
	comments text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_eespbooks_monthes'
#
CREATE TABLE tx_eespbooks_monthes (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	stock_month int(11) DEFAULT '0' NOT NULL,
	stock_year int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
