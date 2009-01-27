#
# Table structure for table 'tx_ergotheca_tools'
#
CREATE TABLE tx_ergotheca_tools (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	fe_cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	authors varchar(255) DEFAULT '' NOT NULL,
	testyear varchar(255) DEFAULT '' NOT NULL,
	evalfield int(11) unsigned DEFAULT '0' NOT NULL,
	formalization int(11) unsigned DEFAULT '0' NOT NULL,
	evalobject text NOT NULL,
	practicemodel text NOT NULL,
	targetpublic_age int(11) unsigned DEFAULT '0' NOT NULL,
	targetpublic_alt varchar(255) DEFAULT '' NOT NULL,
	bibliography blob NOT NULL,
	keywords blob NOT NULL,
	passation_method int(11) unsigned DEFAULT '0' NOT NULL,
	passation_description text NOT NULL,
	passation_procedure text NOT NULL,
	passation_material text NOT NULL,
	passation_setpos text NOT NULL,
	passation_quotint text NOT NULL,
	comments text NOT NULL,
	sources text NOT NULL,
	links text NOT NULL,
	language int(11) unsigned DEFAULT '0' NOT NULL,
	language_alt varchar(255) DEFAULT '' NOT NULL,
	traduction int(11) unsigned DEFAULT '0' NOT NULL,
	traduction_alt varchar(255) DEFAULT '' NOT NULL,
	traduction_standard int(11) unsigned DEFAULT '0' NOT NULL,
	traduction_standard_alt varchar(255) DEFAULT '' NOT NULL,
	eesp tinyint(3) unsigned DEFAULT '0' NOT NULL,
	eesp_testyear varchar(255) DEFAULT '' NOT NULL,
	usecond text NOT NULL,
	remarks text NOT NULL,
	cost tinytext NOT NULL,
	pictures blob NOT NULL,
	files blob NOT NULL,
	vote_users blob NOT NULL,
	vote_results blob NOT NULL,
	opencontent tinyint(4) unsigned DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'tx_ergotheca_keywords'
#
CREATE TABLE tx_ergotheca_keywords (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	keyword varchar(255) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'tx_ergotheca_researches'
#
CREATE TABLE tx_ergotheca_researches (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	fe_cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	date_start int(11) DEFAULT '0' NOT NULL,
	date_end int(11) DEFAULT '0' NOT NULL,
	tool blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
