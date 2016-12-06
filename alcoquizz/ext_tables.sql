# $Id: ext_tables.sql 25 2010-06-21 08:05:58Z macmade $

#
# Table structure for table 'tx_alcoquizz_result'
#
CREATE TABLE tx_alcoquizz_result (
    
    #
    # Keys
    #
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    
    #
    # TYPO3 fields
    #
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Versionning fields
    #
    t3ver_oid int(11) DEFAULT '0' NOT NULL,
    t3ver_id int(11) DEFAULT '0' NOT NULL,
    t3ver_wsid int(11) DEFAULT '0' NOT NULL,
    t3ver_label varchar(30) DEFAULT '' NOT NULL,
    t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
    t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
    t3ver_count int(11) DEFAULT '0' NOT NULL,
    t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
    t3_origuid int(11) DEFAULT '0' NOT NULL,
    
    #
    # Localization fields
    #
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
    
    #
    # User fields
    #
    complete int(3) DEFAULT '0' NOT NULL,
    I1 tinytext,
    I2 tinytext,
    I3 tinytext,
    I4 tinytext,
    II1 tinytext,
    II2 tinytext,
    II3 tinytext,
    II4 tinytext,
    II53 tinytext,
    II54 tinytext,
    II55 tinytext,
    II56 tinytext,
    II57 tinytext,
    II58 tinytext,
    II59 tinytext,
    II510 tinytext,
    III11 tinytext,
    III12 tinytext,
    III13 tinytext,
    III16 tinytext,
    III17 tinytext,
    III18 tinytext,
    III19 tinytext,
    III110 tinytext,
    III111 tinytext,
    III112 tinytext,
    III21 tinytext,
    III22 tinytext,
    feed_1 tinytext,
    feed_2 tinytext,
    feed_3 tinytext,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_alcoquizz_survey'
#
CREATE TABLE tx_alcoquizz_survey (
    
    #
    # Keys
    #
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    
    #
    # TYPO3 fields
    #
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Versionning fields
    #
    t3ver_oid int(11) DEFAULT '0' NOT NULL,
    t3ver_id int(11) DEFAULT '0' NOT NULL,
    t3ver_wsid int(11) DEFAULT '0' NOT NULL,
    t3ver_label varchar(30) DEFAULT '' NOT NULL,
    t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
    t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
    t3ver_count int(11) DEFAULT '0' NOT NULL,
    t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
    t3_origuid int(11) DEFAULT '0' NOT NULL,
    
    #
    # Localization fields
    #
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
    
    #
    # User fields
    #
    id_result int(11) DEFAULT '0' NOT NULL,
    q1 tinytext,
    q2 tinytext,
    q3 tinytext,
    q4 tinytext,
    q5 tinytext,
    q6 tinytext,
    q7 tinytext,
    q8 tinytext,
    comments text NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
