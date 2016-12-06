# $Id$

#
# Table structure for table 'tx_netdata_states'
#
CREATE TABLE tx_netdata_states (
    
    #
    # TYPO3 fields
    #
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    
    #
    # User fields
    #
    country_code varchar(3) DEFAULT '' NOT NULL,
    fullname tinytext NOT NULL,
    shortname tinytext NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_netdata_cities'
#
CREATE TABLE tx_netdata_cities (
    
    #
    # TYPO3 fields
    #
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    
    #
    # User fields
    #
    id_tx_netdata_states int(11) DEFAULT '0' NOT NULL,
    fullname tinytext NOT NULL,
    shortname tinytext NOT NULL,
    district_name tinytext NOT NULL,
    district_number int(11) DEFAULT '0' NOT NULL,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);

