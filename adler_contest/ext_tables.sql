# $Id$

#
# Table structure for table 'tx_adlercontest_users'
#
CREATE TABLE tx_adlercontest_users (
    
    #
    # TYPO3 fields
    #
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(1) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(1) unsigned DEFAULT '0' NOT NULL,
    
    #
    # User fields
    #
    id_fe_users int(11) unsigned DEFAULT '0' NOT NULL,
    votes int(11) unsigned DEFAULT '0' NOT NULL,
    lastname tinytext NOT NULL,
    firstname tinytext NOT NULL,
    gender char(1) DEFAULT '' NOT NULL,
    address tinytext NOT NULL,
    address2 tinytext NOT NULL,
    city tinytext NOT NULL,
    zip tinytext NOT NULL,
    country int(11) unsigned DEFAULT '0' NOT NULL,
    phone tinytext NOT NULL,
    nationality tinytext NOT NULL,
    birthdate int(11) DEFAULT '0' NOT NULL,
    school_name tinytext NOT NULL,
    school_address tinytext NOT NULL,
    school_country int(11) unsigned DEFAULT '0' NOT NULL,
    employer tinytext NOT NULL,
    employer_country int(11) unsigned DEFAULT '0' NOT NULL,
    age_proof blob NOT NULL,
    school_proof blob NOT NULL,
    project blob NOT NULL,
    sworn tinyint(1) unsigned DEFAULT '0' NOT NULL,
    validated tinyint(1) unsigned DEFAULT '0' NOT NULL,
    confirm_token tinytext NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_adlercontest_votes'
#
CREATE TABLE tx_adlercontest_votes (
    
    #
    # TYPO3 fields
    #
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(1) unsigned DEFAULT '0' NOT NULL,
    
    #
    # User fields
    #
    criteria_1 int(2) DEFAULT '0' NOT NULL,
    criteria_2 int(2) DEFAULT '0' NOT NULL,
    criteria_3 int(2) DEFAULT '0' NOT NULL,
    criteria_4 int(2) DEFAULT '0' NOT NULL,
    criteria_5 int(2) DEFAULT '0' NOT NULL,
    note tinytext NOT NULL,
    id_tx_adlercontest_users int(11) unsigned DEFAULT '0' NOT NULL,
    id_fe_users int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_adlercontest_emails'
#
CREATE TABLE tx_adlercontest_emails (
    
    #
    # TYPO3 fields
    #
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(1) unsigned DEFAULT '0' NOT NULL,
    
    #
    # User fields
    #
    type int(11) unsigned DEFAULT '0' NOT NULL,
    subject tinytext NOT NULL,
    from_email tinytext NOT NULL,
    from_name tinytext NOT NULL,
    reply_to tinytext NOT NULL,
    message text NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
