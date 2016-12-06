<?php

# $Id$

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Twitter users table
$TCA[ 'tx_facebookconnect_users' ] = array(
    'ctrl'        => $TCA[ 'tx_facebookconnect_users' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_facebookconnect_users' ][ 'feInterface' ],
    'interface'   => array(
        'showRecordFieldList' => 'id_fe_users,facebook_uid,about_me,activities,affiliations,birthday_date,books,current_location_city,current_location_state,current_location_country,current_location_zip,education_history,email_hashes,first_name,fullname,hometown_location_city,hometown_location_state,hometown_location_country,hs_info_hs1_name,hs_info_hs2_name,hs_info_grad_year,hs_info_hs1_id,hs_info_hs2_id,interests,is_app_user,is_blocked,last_name,locale,meeting_for,meeting_sex,movies,music,notes_count,pic,pic_with_logo,pic_big,pic_big_with_logo,pic_small,pic_small_with_logo,pic_square,pic_square_with_logo,political,profile_blurb,profile_update_time,profile_url,proxied_email,quotes,relationship_status,religion,sex,significant_other_id,status_message,status_time,timezone,tv,username,wall_count,website,work_history'
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'id_fe_users' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tx_facebookconnect_users',
            'config' => array(
                'type'          => 'inline',
                'foreign_table' => 'fe_users',
                'maxitems'      => 1,
                'appearance'    => array(
                    'expandSingle' => true
                )
            )
        ),
        'facebook_uid' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:facebook_uid',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'about_me' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:about_me',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'activities' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:activities',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'affiliations' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:affiliations',
            'config' => array(
                'type' => 'flex',
                'ds' => array(
                    'default' => 'FILE:EXT:facebook_connect/flex/affiliations.xml',
                )
            )
        ),
        'birthday_date' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:birthday_date',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'date'
            )
        ),
        'books' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:books',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'current_location_city' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:current_location_city',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'current_location_state' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:current_location_state',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'current_location_country' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:current_location_country',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'current_location_zip' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:current_location_zip',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'education_history' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:education_history',
            'config' => array(
                'type' => 'flex',
                'ds' => array(
                    'default' => 'FILE:EXT:facebook_connect/flex/education_history.xml',
                )
            )
        ),
        'email_hashes' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:email_hashes',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'first_name' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:first_name',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'fullname' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:fullname',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'hometown_location_city' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:hometown_location_city',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'hometown_location_state' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:hometown_location_state',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'hometown_location_country' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:hometown_location_country',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'hs_info_hs1_name' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:hs_info_hs1_name',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'hs_info_hs2_name' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:hs_info_hs2_name',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'hs_info_grad_year' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:hs_info_grad_year',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'hs_info_hs1_id' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:hs_info_hs1_id',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'hs_info_hs2_id' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:hs_info_hs2_id',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'interests' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:interests',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'is_app_user' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:is_app_user',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'is_blocked' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:is_blocked',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'last_name' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:last_name',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'locale' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:locale',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'meeting_for' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:meeting_for',
            'config' => array(
                'type'         => 'select',
                'size'         => '5',
                'minitems'     => '0',
                'maxitems'     => '5',
                'renderMode'   => 'checkbox',
                'items'        => array(
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:meeting_for.friendship', 'Friendship' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:meeting_for.relationship', 'A Relationship' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:meeting_for.dating', 'Dating' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:meeting_for.random', 'Random Play' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:meeting_for.whatever', 'Whatever I can get' )
                )
            )
        ),
        'meeting_sex' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:meeting_sex',
            'config' => array(
                'type'         => 'select',
                'size'         => '2',
                'minitems'     => '0',
                'maxitems'     => '2',
                'renderMode'   => 'checkbox',
                'items'        => array(
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:meeting_sex.male', 'male' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:meeting_sex.female', 'female' )
                )
            )
        ),
        'movies' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:movies',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'music' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:music',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'notes_count' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:notes_count',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'pic' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:pic',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'pic_with_logo' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:pic_with_logo',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'pic_big' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:pic_big',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'pic_big_with_logo' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:pic_big_with_logo',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'pic_small' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:pic_small',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'pic_small_with_logo' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:pic_small_with_logo',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'pic_square' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:pic_square',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'pic_square_with_logo' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:pic_square_with_logo',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'political' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:political',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'profile_blurb' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:profile_blurb',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'profile_update_time' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:profile_update_time',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'datetime'
            )
        ),
        'profile_url' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:profile_url',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'proxied_email' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:proxied_email',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'quotes' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:quotes',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'relationship_status' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:relationship_status',
            'config' => array(
                'type'  => 'select',
                'size'  => '1',
                'items' => array(
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:relationship_status.single', 'Single' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:relationship_status.relationship', 'In a Relationship' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:relationship_status.open', 'In an Open Relationship' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:relationship_status.engaged', 'Engaged' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:relationship_status.married', 'Married' ),
                    array( 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:relationship_status.complicated', 'It\'s Complicated' )
                )
            )
        ),
        'religion' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:religion',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'sex' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:sex',
            'config' => array(
                'type'  => 'input',
                'size'  => '30'
            )
        ),
        'significant_other_id' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:significant_other_id',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'status_message' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:status_message',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'status_time' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:status_time',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'datetime'
            )
        ),
        'timezone' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:timezone',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'tv' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tv',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        ),
        'username' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:username',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'wall_count' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:wall_count',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'website' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:website',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'work_history' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:work_history',
            'config' => array(
                'type' => 'flex',
                'ds' => array(
                    'default' => 'FILE:EXT:facebook_connect/flex/work_history.xml',
                )
            )
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => '--div--;LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tab.user,'
                       .  'hidden;;;;1-1-1,id_fe_users;;;;1-1-1,facebook_uid;;;;1-1-1,username,fullname;;;;1-1-1,first_name,last_name,proxied_email;;;;1-1-1,email_hashes,'
                       .  '--div--;LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tab.infos,'
                       .  'is_app_user;;;;1-1-1,is_blocked,locale;;;;1-1-1,timezone,profile_blurb;;;;1-1-1,profile_update_time,profile_url,status_message;;;;1-1-1,status_time,wall_count;;;;1-1-1,notes_count,'
                       .  '--div--;LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tab.location,'
                       .  'current_location_city;;;;1-1-1,current_location_state,current_location_country,current_location_zip,'
                       .  '--div--;LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tab.about,'
                       .  'hometown_location_city;;;;1-1-1,hometown_location_state,hometown_location_country,website;;;;1-1-1,sex;;;;1-1-1,birthday_date,relationship_status;;;;1-1-1,significant_other_id,meeting_for;;;;1-1-1,meeting_sex,about_me;;;;1-1-1,activities,interests,political;;;;1-1-1,religion,quotes;;;;1-1-1,books,movies,music,tv,'
                       .  '--div--;LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tab.affiliations,'
                       .  'affiliations;;;;1-1-1,'
                       .  '--div--;LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tab.education,'
                       .  'education_history;;;;1-1-1,hs_info_hs1_name;;;;1-1-1,hs_info_hs2_name,hs_info_grad_year;;;;1-1-1,hs_info_hs1_id;;;;1-1-1,hs_info_hs2_id,'
                       .  '--div--;LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tab.work,'
                       .  'work_history;;;;1-1-1,'
                       .  '--div--;LLL:EXT:facebook_connect/lang/tx_facebookconnect_users.xml:tab.media,'
                       .  'pic;;;;1-1-1,pic_with_logo,pic_big;;;;1-1-1,pic_big_with_logo,pic_small;;;;1-1-1,pic_small_with_logo,pic_square;;;;1-1-1,pic_square_with_logo'
         ),
    ),
    'palettes' => array()
);

?>
