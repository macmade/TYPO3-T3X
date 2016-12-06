# $Id: ext_tables.sql 150 2009-12-10 10:29:12Z macmade $

#
# Table structure for table 'tx_marvincare_manuals'
#
CREATE TABLE tx_marvincare_manuals (
    
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
    id_movement int(11) DEFAULT '0' NOT NULL,
    files blob NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_marvincare_movements'
#
CREATE TABLE tx_marvincare_movements (
    
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
    
    #
    # User fields
    #
    fullname tinytext,
    movement_type tinyint(1) unsigned DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
