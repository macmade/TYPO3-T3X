# $Id: ext_tables.sql 7 2010-10-18 13:46:27Z jean $

#
# Table structure for table 'tx_cpguide_domain_model_article'
#
CREATE TABLE tx_cpguide_domain_model_article (
    
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
    article_type int(11) unsigned DEFAULT '0' NOT NULL,
    title tinytext,
    pagenum tinytext,
    change_date tinytext,
    change_description text NOT NULL,
    content text NOT NULL,
    comments int(11) unsigned DEFAULT '0' NOT NULL,
    notes int(11) unsigned DEFAULT '0' NOT NULL,
    tags int(11) unsigned DEFAULT '0' NOT NULL,
    favorites int(11) unsigned DEFAULT '0' NOT NULL,
    views int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cpguide_domain_model_comment'
#
CREATE TABLE tx_cpguide_domain_model_comment (
    
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
    content text NOT NULL,
    article int(11) unsigned DEFAULT '0' NOT NULL,
    author int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cpguide_domain_model_note'
#
CREATE TABLE tx_cpguide_domain_model_note (
    
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
    article int(11) unsigned DEFAULT '0' NOT NULL,
    author int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cpguide_domain_model_favorite'
#
CREATE TABLE tx_cpguide_domain_model_favorite (
    
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
    article int(11) unsigned DEFAULT '0' NOT NULL,
    author int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cpguide_domain_model_tag'
#
CREATE TABLE tx_cpguide_domain_model_tag (
    
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
    article int(11) unsigned DEFAULT '0' NOT NULL,
    author int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cpguide_domain_model_user'
#
CREATE TABLE tx_cpguide_domain_model_user (
    
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
    fe_user int(11) unsigned DEFAULT '0' NOT NULL,
    prefix int(11) unsigned DEFAULT '0' NOT NULL,
    firstname tinytext,
    lastname tinytext,
    email tinytext,
    company tinytext,
    account_type int(11) unsigned DEFAULT '0' NOT NULL,
    address1 tinytext,
    address2 tinytext,
    city tinytext,
    zip tinytext,
    country tinytext,
    telephone tinytext,
    fax tinytext,
    mobile tinytext,
    comments int(11) unsigned DEFAULT '0' NOT NULL,
    notes int(11) unsigned DEFAULT '0' NOT NULL,
    tags int(11) unsigned DEFAULT '0' NOT NULL,
    favorites int(11) unsigned DEFAULT '0' NOT NULL,
    confirm_hash tinytext,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cpguide_domain_model_word'
#
CREATE TABLE tx_cpguide_domain_model_word (
    
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
    articles blob NOT NULL,
    word_count int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cpguide_domain_model_allowedword'
#
CREATE TABLE tx_cpguide_domain_model_allowedword (
    
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
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
