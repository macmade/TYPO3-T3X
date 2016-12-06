# $Id: ext_tables.sql 181 2010-01-04 12:33:58Z macmade $

#
# Table structure for table 'tx_marvindata_countries'
#
CREATE TABLE tx_marvindata_countries (
    
    #
    # TYPO3 fields
    #
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(1) unsigned DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l10n_parent int(11) DEFAULT '0' NOT NULL,
    l10n_diffsource mediumtext,
    
    #
    # User fields
    #
    fullname tinytext,
    region int(11) DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_marvindata_cities'
#
CREATE TABLE tx_marvindata_cities (
    
    #
    # TYPO3 fields
    #
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(1) unsigned DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l10n_parent int(11) DEFAULT '0' NOT NULL,
    l10n_diffsource mediumtext,
    
    #
    # User fields
    #
    fullname tinytext,
    id_country int(11) DEFAULT '0' NOT NULL,
    coord_x int(11) DEFAULT '0' NOT NULL,
    coord_y int(11) DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_marvindata_languages'
#
CREATE TABLE tx_marvindata_languages (
    
    #
    # TYPO3 fields
    #
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(1) unsigned DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l10n_parent int(11) DEFAULT '0' NOT NULL,
    l10n_diffsource mediumtext,
    
    #
    # User fields
    #
    fullname tinytext,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
