#
# Table structure for table 'tx_cjf_events_groups_mm'
# 
CREATE TABLE tx_cjf_events_groups_mm (
    uid_local int(11) DEFAULT '0' NOT NULL,
    uid_foreign int(11) DEFAULT '0' NOT NULL,
    tablenames varchar(30) DEFAULT '' NOT NULL,
    sorting int(11) DEFAULT '0' NOT NULL,
    
    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_cjf_events'
#
CREATE TABLE tx_cjf_events (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    title tinytext NOT NULL,
    groups int(11) DEFAULT '0' NOT NULL,
    place int(11) DEFAULT '0' NOT NULL,
    date int(11) DEFAULT '0' NOT NULL,
    hour int(11) DEFAULT '0' NOT NULL,
    price tinytext NOT NULL,
    tickets tinytext NOT NULL,
    soldout tinyint(4) DEFAULT '0' NOT NULL,
    tickets_sold tinytext NOT NULL,
    tickets_booked tinytext NOT NULL,
    comments text NOT NULL,
    no_seats tinyint(4) DEFAULT '0' NOT NULL,
    seats tinyint(4) DEFAULT '0' NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cjf_groups'
#
CREATE TABLE tx_cjf_groups (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    name tinytext NOT NULL,
    country tinytext NOT NULL,
    style int(11) DEFAULT '0' NOT NULL,
    description text NOT NULL,
    www tinytext NOT NULL,
    artists blob NOT NULL,
    picture blob NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cjf_artists'
#
CREATE TABLE tx_cjf_artists (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    name_last tinytext NOT NULL,
    name_first tinytext NOT NULL,
    description text NOT NULL,
    www tinytext NOT NULL,
    label tinytext NOT NULL,
    picture blob NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cjf_styles'
#
CREATE TABLE tx_cjf_styles (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    style tinytext NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cjf_places'
#
CREATE TABLE tx_cjf_places (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    sorting int(10) DEFAULT '0' NOT NULL,
    place tinytext NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cjf_clients'
#
CREATE TABLE tx_cjf_clients (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    type int(11) DEFAULT '0' NOT NULL,
    name_first tinytext NOT NULL,
    name_last tinytext NOT NULL,
    address text NOT NULL,
    zip tinytext NOT NULL,
    city tinytext NOT NULL,
    country tinytext NOT NULL,
    email tinytext NOT NULL,
    phone_professionnal tinytext NOT NULL,
    phone_personnal tinytext NOT NULL,
    fax tinytext NOT NULL,
    ip tinytext NOT NULL,
    newsletter int(11) DEFAULT '0' NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cjf_orders'
#
CREATE TABLE tx_cjf_orders (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    id_client int(11) DEFAULT '0' NOT NULL,
    id_event int(11) DEFAULT '0' NOT NULL,
    quantity tinytext NOT NULL,
    price tinytext NOT NULL,
    total tinytext NOT NULL,
    confirmed tinyint(4) DEFAULT '0' NOT NULL,
    processed tinyint(4) DEFAULT '0' NOT NULL,
    type int(11) DEFAULT '0' NOT NULL,
    orderid tinytext NOT NULL,
    transaction_id tinytext NOT NULL,
    pdf tinytext NOT NULL,
    labelprinted tinyint(4) DEFAULT '0' NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cjf_bookings'
#
CREATE TABLE tx_cjf_bookings (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    id_client int(11) DEFAULT '0' NOT NULL,
    id_event int(11) DEFAULT '0' NOT NULL,
    tickets_booked tinytext NOT NULL,
    tickets_sold tinytext NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_cjf_bookings'
#
CREATE TABLE tx_cjf_bookings_sales (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    id_booking int(11) DEFAULT '0' NOT NULL,
    sale_date int(11) DEFAULT '0' NOT NULL,
    tickets_sold tinytext NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);
