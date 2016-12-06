# $Id: ext_tables.sql 126 2009-12-03 14:30:24Z macmade $

#
# Table structure for table 'tx_marvinaddresses_addresses'
#
CREATE TABLE tx_marvinaddresses_addresses (
    
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
    address_type int(1) DEFAULT '0' NOT NULL,
    sav int(1) DEFAULT '0' NOT NULL,
    fullname tinytext,
    id_city int(11) DEFAULT '0' NOT NULL,
    zip tinytext,
    address tinytext,
    address_number tinytext,
    phone tinytext,
    phone2 tinytext,
    fax tinytext,
    email tinytext,
    email2 tinytext,
    url tinytext,
    infos text NOT NULL,
    remarks text NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
