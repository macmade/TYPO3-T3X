# $Id$

#
# Table structure for table 'tx_ymregister_users'
#
CREATE TABLE tx_ymregister_users (
    
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
    id_fe_users int(11) DEFAULT '0' NOT NULL,
    id_tx_facebookconnect_users int(11) DEFAULT '0' NOT NULL,
    lastname tinytext,
    firstname tinytext,
    nickname tinytext,
    zip tinytext,
    age int(11) DEFAULT '0' NOT NULL,
    address text NOT NULL,
    id_tx_netdata_states int(11) DEFAULT '0' NOT NULL,
    id_tx_netdata_cities int(11) DEFAULT '0' NOT NULL,
    phone tinytext,
    confirm_hash tinytext,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
