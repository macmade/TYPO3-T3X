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
    note int(2) DEFAULT '0' NOT NULL,
    id_tx_adlercontest_users int(11) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
