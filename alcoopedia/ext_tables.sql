# $Id: ext_tables.sql 24 2010-06-21 07:53:48Z macmade $

#
# Table structure for table 'tx_alcoopedia_domain_model_category'
#
CREATE TABLE tx_alcoopedia_domain_model_category (
    
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
    title tinytext,
    articles int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_alcoopedia_domain_model_article'
#
CREATE TABLE tx_alcoopedia_domain_model_article (
    
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
    title tinytext,
    content text NOT NULL,
    views int(32) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_alcoopedia_domain_model_word'
#
CREATE TABLE tx_alcoopedia_domain_model_word (
    
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
    word tinytext,
    count int(32) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_alcoopedia_category_article_mm'
#
CREATE TABLE tx_alcoopedia_category_article_mm (
    
    #
    # Keys
    #
    uid int(10) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    
    #
    # Relation fields
    #
    uid_local int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
    tablenames varchar(255) DEFAULT '' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # TYPO3 fields
    #
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
