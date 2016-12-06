<?php

# $Id$

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Twitter users table
$TCA[ 'tx_twitterlogin_users' ] = array(
    
    'ctrl'        => $TCA[ 'tx_twitterlogin_users' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_twitterlogin_users' ][ 'feInterface' ],
    'interface'   => array(
        'showRecordFieldList' => 'id_fe_users,twitter_id,fullname,screen_name,location,description,profile_image_url,url,protected,followers_count,profile_background_color,profile_text_color,profile_link_color,profile_sidebar_fill_color,profile_sidebar_border_color,friends_count,created_at,favourites_count,utc_offset,time_zone,profile_background_image_url,profile_background_tile,statuses_count,notifications,verified,following,,status_created_at,status_id,status_text,status_source,status_truncated,,status_in_reply_to_status_id,status_in_reply_to_user_id,status_favorited,status_in_reply_to_screen_name'
    ),
    'columns' => array(
        'id_fe_users' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:id_fe_users',
            'config' => array(
                'type'          => 'inline',
                'foreign_table' => 'fe_users',
                'maxitems'      => 1,
                'appearance'    => array(
                    'expandSingle' => true
                )
            )
        ),
        'twitter_id' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:twitter_id',
            'config'  => array(
                'type' => 'input',
                'size' => '10'
            )
        ),
        'access_token' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:access_token',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'fullname' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:fullname',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'screen_name' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:screen_name',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'location' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:location',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'description' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:description',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'profile_image_url' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:profile_image_url',
            'config'  => array(
                'type'     => 'input',
                'size'     => '30',
                'max'      => '255',
                'checkbox' => '',
                'eval'     => 'trim',
                'wizards'  => array(
                    '_PADDING' => 2,
                    'link'     => array(
                        'type'         => 'popup',
                        'title'        => 'Link',
                        'icon'         => 'link_popup.gif',
                        'script'       => 'browse_links.php?mode=wizard',
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
                    )
                )
            )
        ),
        'url' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:url',
            'config'  => array(
                'type'     => 'input',
                'size'     => '30',
                'max'      => '255',
                'checkbox' => '',
                'eval'     => 'trim',
                'wizards'  => array(
                    '_PADDING' => 2,
                    'link'     => array(
                        'type'         => 'popup',
                        'title'        => 'Link',
                        'icon'         => 'link_popup.gif',
                        'script'       => 'browse_links.php?mode=wizard',
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
                    )
                )
            )
        ),
        'protected' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:protected',
            'config'  => array(
                'type' => 'check'
            )
        ),
        'followers_count' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:followers_count',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'profile_background_color' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:profile_background_color',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'profile_text_color' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:profile_text_color',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'profile_link_color' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:profile_link_color',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'profile_sidebar_fill_color' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:profile_sidebar_fill_color',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'profile_sidebar_border_color' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:profile_sidebar_border_color',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'friends_count' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:friends_count',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'created_at' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:created_at',
            'config'  => array(
                'type'     => 'input',
                'size'     => '10',
                'max'      => '20',
                'eval'     => 'datetime',
                'checkbox' => '0',
                'default'  => '0'
            )
        ),
        'favourites_count' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:favourites_count',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'utc_offset' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:utc_offset',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'time_zone' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:time_zone',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'profile_background_image_url' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:profile_background_image_url',
            'config'  => array(
                'type'     => 'input',
                'size'     => '30',
                'max'      => '255',
                'checkbox' => '',
                'eval'     => 'trim',
                'wizards'  => array(
                    '_PADDING' => 2,
                    'link'     => array(
                        'type'         => 'popup',
                        'title'        => 'Link',
                        'icon'         => 'link_popup.gif',
                        'script'       => 'browse_links.php?mode=wizard',
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
                    )
                )
            )
        ),
        'profile_background_tile' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:profile_background_tile',
            'config'  => array(
                'type' => 'check'
            )
        ),
        'statuses_count' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:statuses_count',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
            )
        ),
        'notifications' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:notifications',
            'config'  => array(
                'type' => 'check'
            )
        ),
        'verified' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:verified',
            'config'  => array(
                'type' => 'check'
            )
        ),
        'following' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:following',
            'config'  => array(
                'type' => 'check'
            )
        ),
        'status_created_at' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:status_created_at',
            'config'  => array(
                'type'     => 'input',
                'size'     => '10',
                'max'      => '20',
                'eval'     => 'datetime',
                'checkbox' => '0',
                'default'  => '0'
            )
        ),
        'status_id' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:status_id',
            'config'  => array(
                'type' => 'input',
                'size' => '10'
            )
        ),
        'status_text' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:status_text',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'status_source' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:status_source',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'status_truncated' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:status_truncated',
            'config'  => array(
                'type' => 'check'
            )
        ),
        'status_in_reply_to_status_id' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:status_in_reply_to_status_id',
            'config'  => array(
                'type' => 'input',
                'size' => '10'
            )
        ),
        'status_in_reply_to_user_id' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:status_in_reply_to_user_id',
            'config'  => array(
                'type' => 'input',
                'size' => '10'
            )
        ),
        'status_favorited' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:status_favorited',
            'config'  => array(
                'type' => 'check'
            )
        ),
        'status_in_reply_to_screen_name' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:status_in_reply_to_screen_name',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        )
    ),
    
    'types' => array(
        '0' => array(
            'showitem' => '--div--;LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:tab.user,'
                       .  'screen_name;;;;1-1-1,fullname,twitter_id,access_token,created_at,id_fe_users;;;;1-1-1,'
                       
                       .  '--div--;LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:tab.infos,'
                       .  'location;;;;1-1-1,utc_offset,time_zone,description;;;;1-1-1,url,followers_count;;;;1-1-1,friends_count,statuses_count,favourites_count,notifications;;;;1-1-1,verified,following,'
                       
                       .  '--div--;LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:tab.profile,'
                       .  'profile_image_url;;;;1-1-1,profile_background_image_url,profile_background_color,profile_text_color;;;;1-1-1,profile_link_color,profile_sidebar_fill_color,profile_sidebar_border_color,profile_background_tile;;;;1-1-1,'
                       
                       .  '--div--;LLL:EXT:twitter_login/lang/tx_twitterlogin_users.xml:tab.status,'
                       .  'status_id;;;;1-1-1,status_created_at,status_text,status_source,status_truncated;;;;1-1-1,status_favorited,status_in_reply_to_status_id;;;;1-1-1,status_in_reply_to_user_id,status_in_reply_to_screen_name'
        ),
    ),
    
    'palettes' => array()
);

?>
