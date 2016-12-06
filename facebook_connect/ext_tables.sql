# $Id$

#
# Table structure for table 'tx_facebookconnect_users'
#
CREATE TABLE tx_facebookconnect_users (
    
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
    facebook_uid tinytext,
    about_me text NOT NULL,
    activities text NOT NULL,
    affiliations blob NOT NULL,
    # SECTION flexform:
    #   - type
    #   - year
    #   - name
    #   - nid
    #   - status
    birthday_date int(11) DEFAULT '0' NOT NULL,
    books text NOT NULL,
    current_location_city tinytext,
    current_location_state tinytext,
    current_location_country tinytext,
    current_location_zip tinytext,
    education_history blob NOT NULL,
    # SECTION flexform:
    #   - year
    #   - name
    #   - concentrations
    #   - degree
    email_hashes blob NOT NULL,
    first_name tinytext,
    fullname tinytext,
    hometown_location_city tinytext,
    hometown_location_state tinytext,
    hometown_location_country tinytext,
    hs_info_hs1_name tinytext,
    hs_info_hs2_name tinytext,
    hs_info_grad_year tinytext,
    hs_info_hs1_id tinytext,
    hs_info_hs2_id tinytext,
    interests text NOT NULL,
    is_app_user tinyint(3) DEFAULT '0' NOT NULL,
    is_blocked tinyint(3) DEFAULT '0' NOT NULL,
    last_name tinytext,
    locale tinytext,
    meeting_for blob NOT NULL,
    meeting_sex blob NOT NULL,
    movies text NOT NULL,
    music text NOT NULL,
    notes_count int(11) DEFAULT '0' NOT NULL,
    pic tinytext,
    pic_with_logo tinytext,
    pic_big tinytext,
    pic_big_with_logo tinytext,
    pic_small tinytext,
    pic_small_with_logo tinytext,
    pic_square tinytext,
    pic_square_with_logo tinytext,
    political text NOT NULL,
    profile_blurb text NOT NULL,
    profile_update_time int(11) DEFAULT '0' NOT NULL,
    profile_url tinytext,
    proxied_email tinytext,
    quotes text NOT NULL,
    relationship_status tinytext,
    religion text NOT NULL,
    sex tinytext,
    significant_other_id int(32) DEFAULT '0' NOT NULL,
    status_message tinytext,
    status_time int(11) DEFAULT '0' NOT NULL,
    timezone int(11) DEFAULT '0' NOT NULL,
    tv text NOT NULL,
    username tinytext,
    wall_count int(11) DEFAULT '0' NOT NULL,
    website tinytext,
    work_history blob NOT NULL,
    # SECTION flexform:
    #   - location
    #   - company_name
    #   - description
    #   - position
    #   - start_date
    #   - end_date
    
    #
    # Database options
    #
    PRIMARY KEY (uid),
    KEY parent (pid)
);
