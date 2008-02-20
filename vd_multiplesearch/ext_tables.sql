#
# Table structure for table 'pages'
#
CREATE TABLE pages (
    tx_vdmultiplesearch_enable tinyint(4) DEFAULT '0' NOT NULL,
    tx_vdmultiplesearch_public blob NOT NULL,
    tx_vdmultiplesearch_themes blob NOT NULL,
    tx_vdmultiplesearch_keywords blob NOT NULL,
);

#
# Table structure for table 'tx_vdmultiplesearch_public'
#
CREATE TABLE tx_vdmultiplesearch_public (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    title tinytext NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_vdmultiplesearch_themes'
#
CREATE TABLE tx_vdmultiplesearch_themes (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    title tinytext NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_vdmultiplesearch_keywords'
#
CREATE TABLE tx_vdmultiplesearch_keywords (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    keyword tinytext NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);
