# $Id$

#
# Table structure for table 'tx_twitterlogin_users'
#
CREATE TABLE tx_twitterlogin_users (
    
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
    id_fe_users int(11) DEFAULT '0' NOT NULL,
    access_token tinytext,
    twitter_id int(11) DEFAULT '0' NOT NULL,
    fullname tinytext,
    screen_name tinytext,
    location tinytext,
    description tinytext,
    profile_image_url tinytext,
    url tinytext,
    protected tinyint(3) DEFAULT '0' NOT NULL,
    followers_count int(11) DEFAULT '0' NOT NULL,
    profile_background_color tinytext,
    profile_text_color tinytext,
    profile_link_color tinytext,
    profile_sidebar_fill_color tinytext,
    profile_sidebar_border_color tinytext,
    friends_count int(11) DEFAULT '0' NOT NULL,
    created_at int(11) DEFAULT '0' NOT NULL,
    favourites_count int(11) DEFAULT '0' NOT NULL,
    utc_offset int(11) DEFAULT '0' NOT NULL,
    time_zone tinytext,
    profile_background_image_url tinytext,
    profile_background_tile tinyint(3) DEFAULT '0' NOT NULL,
    statuses_count int(11) DEFAULT '0' NOT NULL,
    notifications tinyint(3) DEFAULT '0' NOT NULL,
    verified tinyint(3) DEFAULT '0' NOT NULL,
    following tinyint(3) DEFAULT '0' NOT NULL,
    status_created_at int(11) DEFAULT '0' NOT NULL,
    status_id int(11) DEFAULT '0' NOT NULL,
    status_text tinytext,
    status_source tinytext,
    status_truncated tinyint(3) DEFAULT '0' NOT NULL,
    status_in_reply_to_status_id int(11) DEFAULT '0' NOT NULL,
    status_in_reply_to_user_id int(11) DEFAULT '0' NOT NULL,
    status_favorited tinyint(3) DEFAULT '0' NOT NULL,
    status_in_reply_to_screen_name tinytext,
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid),
);
