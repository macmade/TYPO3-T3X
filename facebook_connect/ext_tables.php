<?php

# $Id$

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Configures the frontend plugin
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 1, true );

// Adds the static TS template
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/pi1/', 'Facebook Connect Configuration' );

// Adds the frontend plugin wizard icon
tx_oop_Typo3_Utils::addPluginWizardIcon( $_EXTKEY, 1 );

// Adds the save & new button
tx_oop_Typo3_Utils::addSaveAndNewButton( $_EXTKEY, 'tx_facebookconnect_users' );

// Twitter users table
$TCA[ 'tx_facebookconnect_users' ] = array(
    'ctrl' => array (
        'title'             => 'LLL:EXT:' . $_EXTKEY . '/lang/tx_facebookconnect_users.xml:tx_facebookconnect_users',
        'label'             => 'facebook_uid',
        'label_alt'         => 'fullname',
        'label_alt_force'   => true,
        'tstamp'            => 'tstamp',
        'crdate'            => 'crdate',
        'cruser_id'         => 'cruser_id',
        'default_sortby'    => 'ORDER BY fullname, facebook_uid',
        'delete'            => 'deleted',
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_facebookconnect_users.php',
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_facebookconnect_users.gif',
        'dividers2tabs'     => 1,
        'enablecolumns'     => array (
            'disabled' => 'hidden'
        )
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'id_fe_users,facebook_uid,about_me,activities,affiliations,birthday_date,books,current_location_city,current_location_state,current_location_country,current_location_zip,education_history,email_hashes,first_name,fullname,hometown_location_city,hometown_location_state,hometown_location_country,hs_info_hs1_name,hs_info_hs2_name,hs_info_grad_year,hs_info_hs1_id,hs_info_hs2_id,interests,is_app_user,is_blocked,last_name,locale,meeting_for,meeting_sex,movies,music,notes_count,pic,pic_with_logo,pic_big,pic_big_with_logo,pic_small,pic_small_with_logo,pic_square,pic_square_with_logo,political,profile_blurb,profile_update_time,profile_url,proxied_email,quotes,relationship_status,religion,sex,significant_other_id,status_message,status_time,timezone,tv,username,wall_count,website,work_history'
    )
);

?>
