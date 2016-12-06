<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 netinfluence - Jean-David Gadina (macmade@netinfluence.com)         #
# All rights reserved                                                          #
#                                                                              #
# This script is part of the TYPO3 project. The TYPO3 project is free          #
# software. You can redistribute it and/or modify it under the terms of the    #
# GNU General Public License as published by the Free Software Foundation,     #
# either version 2 of the License, or (at your option) any later version.      #
#                                                                              #
# The GNU General Public License can be found at:                              #
# http://www.gnu.org/copyleft/gpl.html.                                        #
#                                                                              #
# This script is distributed in the hope that it will be useful, but WITHOUT   #
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or        #
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for    #
# more details.                                                                #
#                                                                              #
# This copyright notice MUST APPEAR in all copies of the script!               #
################################################################################

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Includes the Facebook API
require_once( t3lib_extMgm::extPath( 'facebook_connect' ) . 'lib/facebook-platform/facebook.php' );

/**
 * Facebook profile helper class
 *
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  facebook_connect
 */
class tx_facebookconnect_profileHelper
{
    /**
     * The database table with the Facebook users' profiles
     */
    const TABLE_PROFILES = 'tx_facebookconnect_users';
    
    /**
     * The Facebook API object
     */
    protected $_api     = NULL;
    
    /**
     * The database object for the user profile
     */
    protected $_profile = NULL;
    
    /**
     * 
     */
    public function __construct( Facebook $api, tx_oop_Database_Object $profile )
    {
        // Stores the API and database objects
        $this->_api     = $api;
        $this->_profile = $profile;
        
        // Checks for a valid database object
        if( $this->_profile->getTableName() != self::TABLE_PROFILES ) {
            
            // Error - Database object is from a wrong table
            throw new tx_facebookconnect_pi1_profileHelper_Exception(
                'Incorect database object. The record must come from the ' . self::TABLE_PROFILES . ' database table.',
                tx_facebookconnect_pi1_profileHelper_Exception::EXCEPTION_INVALID_OBJECT
            );
        }
    }
    
    /**
     * 
     */
    public function updateProfileInfos()
    {
        // Gets the user infos from the Facebook API
        $infos = $this->_api->api_client->users_getInfo(
            $this->_profile->facebook_uid,
            'uid,about_me,activities,affiliations,birthday,birthday_date,books,'
          . 'current_location,education_history,email_hashes,first_name,hometown_location,'
          . 'hs_info,interests,is_app_user,is_blocked,last_name,locale,meeting_for,'
          . 'meeting_sex,movies,music,name,notes_count,pic,pic_with_logo,pic_big,'
          . 'pic_big_with_logo,pic_small,pic_small_with_logo,pic_square,pic_square_with_logo,'
          . 'political,profile_blurb,profile_update_time,profile_url,proxied_email,'
          . 'quotes,relationship_status,religion,sex,significant_other_id,status,'
          . 'timezone,tv,username,wall_count,website,work_history'
        );
        
        // Checks for a valid API return
        if( !is_array( $infos ) || !isset( $infos[ 0 ] ) ) {
            
            // Invalid API return
            return false;
        }
        
        // Updates the 'normal' fields,meaning not the flexform nightmare... ; )
        $this->_profile->first_name                = $infos[ 0 ][ 'first_name' ];
        $this->_profile->last_name                 = $infos[ 0 ][ 'last_name' ];
        $this->_profile->fullname                  = $infos[ 0 ][ 'name' ];
        $this->_profile->about_me                  = $infos[ 0 ][ 'about_me' ];
        $this->_profile->activities                = $infos[ 0 ][ 'activities' ];
        $this->_profile->birthday_date             = mktime( $infos[ 0 ][ 'birthday_date' ] );
        $this->_profile->books                     = $infos[ 0 ][ 'books' ];
        $this->_profile->current_location_city     = $infos[ 0 ][ 'current_location' ][ 'city' ];
        $this->_profile->current_location_country  = $infos[ 0 ][ 'current_location' ][ 'country' ];
        $this->_profile->current_location_state    = $infos[ 0 ][ 'current_location' ][ 'state' ];
        $this->_profile->current_location_zip      = $infos[ 0 ][ 'current_location' ][ 'zip' ];
        $this->_profile->email_hashes              = $infos[ 0 ][ 'email_hashes' ];
        $this->_profile->hometown_location_city    = $infos[ 0 ][ 'hometown_location' ][ 'city' ];
        $this->_profile->hometown_location_state   = $infos[ 0 ][ 'hometown_location' ][ 'state' ];
        $this->_profile->hometown_location_country = $infos[ 0 ][ 'hometown_location' ][ 'country' ];
        $this->_profile->interests                 = $infos[ 0 ][ 'interests' ];
        $this->_profile->is_app_user               = $infos[ 0 ][ 'is_app_user' ];
        $this->_profile->is_blocked                = $infos[ 0 ][ 'is_blocked' ];
        $this->_profile->locale                    = $infos[ 0 ][ 'locale' ];
        $this->_profile->meeting_for               = $infos[ 0 ][ 'meeting_for' ];
        $this->_profile->meeting_sex               = $infos[ 0 ][ 'meeting_sex' ];
        $this->_profile->movies                    = $infos[ 0 ][ 'movies' ];
        $this->_profile->music                     = $infos[ 0 ][ 'music' ];
        $this->_profile->notes_count               = $infos[ 0 ][ 'notes_count' ];
        $this->_profile->pic                       = $infos[ 0 ][ 'pic' ];
        $this->_profile->pic_with_logo             = $infos[ 0 ][ 'pic_with_logo' ];
        $this->_profile->pic_big                   = $infos[ 0 ][ 'pic_big' ];
        $this->_profile->pic_big_with_logo         = $infos[ 0 ][ 'pic_big_with_logo' ];
        $this->_profile->pic_small                 = $infos[ 0 ][ 'pic_small' ];
        $this->_profile->pic_small_with_logo       = $infos[ 0 ][ 'pic_small_with_logo' ];
        $this->_profile->pic_square                = $infos[ 0 ][ 'pic_square' ];
        $this->_profile->pic_square_with_logo      = $infos[ 0 ][ 'pic_square_with_logo' ];
        $this->_profile->political                 = $infos[ 0 ][ 'political' ];
        $this->_profile->profile_blurb             = $infos[ 0 ][ 'profile_blurb' ];
        $this->_profile->profile_update_time       = $infos[ 0 ][ 'profile_update_time' ];
        $this->_profile->profile_url               = $infos[ 0 ][ 'profile_url' ];
        $this->_profile->proxied_email             = $infos[ 0 ][ 'proxied_email' ];
        $this->_profile->quotes                    = $infos[ 0 ][ 'quotes' ];
        $this->_profile->relationship_status       = $infos[ 0 ][ 'relationship_status' ];
        $this->_profile->religion                  = $infos[ 0 ][ 'religion' ];
        $this->_profile->sex                       = $infos[ 0 ][ 'sex' ];
        $this->_profile->significant_other_id      = $infos[ 0 ][ 'significant_other_id' ];
        $this->_profile->status_message            = $infos[ 0 ][ 'status' ][ 'message' ];
        $this->_profile->status_time               = $infos[ 0 ][ 'status' ][ 'time' ];
        $this->_profile->timezone                  = $infos[ 0 ][ 'timezone' ];
        $this->_profile->tv                        = $infos[ 0 ][ 'tv' ];
        $this->_profile->username                  = $infos[ 0 ][ 'username' ];
        $this->_profile->wall_count                = $infos[ 0 ][ 'wall_count' ];
        $this->_profile->website                   = $infos[ 0 ][ 'website' ];
        $this->_profile->hs_info_hs1_name          = $infos[ 0 ][ 'hs_info' ][ 'hs1_name' ];
        $this->_profile->hs_info_hs2_name          = $infos[ 0 ][ 'hs_info' ][ 'hs2_name' ];
        $this->_profile->hs_info_hs1_name          = $infos[ 0 ][ 'hs_info' ][ 'grad_year' ];
        $this->_profile->hs_info_hs1_id            = $infos[ 0 ][ 'hs_info' ][ 'hs1_id' ];
        $this->_profile->hs_info_hs2_id            = $infos[ 0 ][ 'hs_info' ][ 'hs2_id' ];
        
        // Checks for affiliations
        if( is_array( $infos[ 0 ][ 'affiliations' ] ) && count( $infos[ 0 ][ 'affiliations' ] ) ) {
            
            // Updates the affiliation flexform
            $this->_profile->affiliations = $this->_getAffiliations( $infos[ 0 ][ 'affiliations' ] );
        }
        
        // Checks for education history
        if( is_array( $infos[ 0 ][ 'education_history' ] ) && count( $infos[ 0 ][ 'education_history' ] ) ) {
            
            // Updates the education history flexform
            $this->_profile->education_history = $this->_getEducationHistory( $infos[ 0 ][ 'education_history' ] );
        }
        
        // Checks for work history
        if( is_array( $infos[ 0 ][ 'work_history' ] ) && count( $infos[ 0 ][ 'work_history' ] ) ) {
            
            // Updates the work history flexform
            $this->_profile->work_history = $this->_getWorkHistory( $infos[ 0 ][ 'work_history' ] );
        }
        
        // Updates the profile database object
        $this->_profile->commit();
        
        // Profile was successfully updated
        return true;
    }
    
    /**
     * 
     */
    protected function _createFlexformBase()
    {
        // Base XML for a TYPO3 flexform field
        $base = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'
              . '<T3FlexForms>'
              . '<data>'
              . '<sheet index="sDEF">'
              . '<language index="lDEF">'
              . '<field index="fields">'
              . '<el index="el">'
              . '</el>'
              . '</field>'
              . '</language>'
              . '</sheet>'
              . '</data>'
              . '</T3FlexForms>';
        
        // Returns a SimpleXML object for a flexform field
        return simplexml_load_string( $base );
    }
    
    /**
     * 
     */
    protected function _getAffiliations( array $infos )
    {
        // Gets the basic flexform field
        $flex      = $this->_createFlexformBase();    
        $el        = $flex->data->sheet->language->field->el;
        $sectionId = 1;
        
        // Process each section element
        foreach( $infos as $info ) {
            
            // Adds the element structure
            $section  = $el->addChild( 'section' );
            $itemType = $section->addChild( 'itemType' );
            $toggle   = $section->addChild( 'itemType', '0' );
            $subEl    = $itemType->addChild( 'el' );
            
            // Adds the index keys
            $section[ 'index' ]  = $sectionId;
            $itemType[ 'index' ] = 'field';
            $toggle[ 'index' ]   = '_TOGGLE';
            
            // Create the necessary fields
            $type   = $subEl->addChild( 'field' );
            $year   = $subEl->addChild( 'field' );
            $name   = $subEl->addChild( 'field' );
            $nid    = $subEl->addChild( 'field' );
            $status = $subEl->addChild( 'field' );
            
            // Adds the index keys to the fields
            $type[ 'index' ]   = 'type';
            $year[ 'index' ]   = 'year';
            $name[ 'index' ]   = 'name';
            $nid[ 'index' ]    = 'nid';
            $status[ 'index' ] = 'status';
            
            // Adds the values
            $typeValue   = $type->addChild( 'value', $info[ 'type' ] );
            $yearValue   = $year->addChild( 'value', $info[ 'year' ] );
            $nameValue   = $name->addChild( 'value', $info[ 'name' ] );
            $nidValue    = $nid->addChild( 'value', $info[ 'nid' ] );
            $statusValue = $status->addChild( 'value', $info[ 'status' ] );
            
            // Adds the index keys for the values
            $typeValue[ 'index' ]   = 'vDEF';
            $yearValue[ 'index' ]   = 'vDEF';
            $nameValue[ 'index' ]   = 'vDEF';
            $nidValue[ 'index' ]    = 'vDEF';
            $statusValue[ 'index' ] = 'vDEF';
            
            // Next section element
            $sectionId++;
        }
        
        // Returns the flexform XML data
        return $flex->asXML();
    }
    
    /**
     * 
     */
    protected function _getEducationHistory( array $infos )
    {
        $flex      = $this->_createFlexformBase();    
        $el        = $flex->data->sheet->language->field->el;
        $sectionId = 1;
        
        foreach( $infos as $info ) {
            
            $section  = $el->addChild( 'section' );
            $itemType = $section->addChild( 'itemType' );
            $toggle   = $section->addChild( 'itemType', '0' );
            $subEl    = $itemType->addChild( 'el' );
            
            $section[ 'index' ]  = $sectionId;
            $itemType[ 'index' ] = 'field';
            $toggle[ 'index' ]   = '_TOGGLE';
            
            $name           = $subEl->addChild( 'field' );
            $year           = $subEl->addChild( 'field' );
            $degree         = $subEl->addChild( 'field' );
            $concentrations = $subEl->addChild( 'field' );
            
            $name[ 'index' ]           = 'name';
            $year[ 'index' ]           = 'year';
            $degree[ 'index' ]         = 'degree';
            $concentrations[ 'index' ] = 'concentrations';
            
            $nameValue   = $name->addChild( 'value', $info[ 'name' ] );
            $yearValue   = $year->addChild( 'value', $info[ 'year' ] );
            $degreeValue = $degree->addChild( 'value', $info[ 'degree' ] );
            
            $nameValue[ 'index' ]   = 'vDEF';
            $yearValue[ 'index' ]   = 'vDEF';
            $degreeValue[ 'index' ] = 'vDEF';
            
            $concentrationsEl            = $concentrations->addChild( 'el' );
            $concentrationsEl[ 'index' ] = 'el';
            
#            if( is_array( $info[ 'concentrations' ] ) && count( $info[ 'concentrations' ] ) ) {
#                
#                $concentrationId = 1;
#                
#                foreach( $info[ 'concentrations' ] as $concentration ) {
#                    
#                    $concentrationSection  = $concentrationEl->addChild( 'section' );
#                    $concentrationItemType = $concentrationSection->addChild( 'itemType' );
#                    $concentrationToggle   = $concentrationSection->addChild( 'itemType', '0' );
#                    $concentrationSubEl    = $concentrationItemType->addChild( 'el' );
#                    
#                    $concentrationSection[ 'index' ]  = $concentrationId;
#                    $concentrationItemType[ 'index' ] = 'item';
#                    $concentrationToggle[ 'index' ]   = '_TOGGLE';
#                    
#                    $concentrationField            = $concentrationSubEl->addChild( 'field' );
#                    $concentrationField[ 'index' ] = 'value';
#                    
#                    $concentrationFieldValue            = $concentrationField->addChild( 'value', $concentration );
#                    $concentrationFieldValue[ 'index' ] = 'vDEF';
#                    
#                    $concentrationId++;
#                }
#            }
            
            $sectionId++;
        }
        
        return $flex->asXML();
    }
    
    /**
     * 
     */
    protected function _getWorkHistory( array $infos )
    {
        $flex      = $this->_createFlexformBase();    
        $el        = $flex->data->sheet->language->field->el;
        $sectionId = 1;
        
        foreach( $infos as $info ) {
            
            $section  = $el->addChild( 'section' );
            $itemType = $section->addChild( 'itemType' );
            $toggle   = $section->addChild( 'itemType', '0' );
            $subEl    = $itemType->addChild( 'el' );
            
            $section[ 'index' ]  = $sectionId;
            $itemType[ 'index' ] = 'field';
            $toggle[ 'index' ]   = '_TOGGLE';
            
            $city        = $subEl->addChild( 'field' );
            $state       = $subEl->addChild( 'field' );
            $country     = $subEl->addChild( 'field' );
            $company     = $subEl->addChild( 'field' );
            $description = $subEl->addChild( 'field' );
            $position    = $subEl->addChild( 'field' );
            $start       = $subEl->addChild( 'field' );
            $end         = $subEl->addChild( 'field' );
            
            $city[ 'index' ]        = 'location_city';
            $state[ 'index' ]       = 'location_state';
            $country[ 'index' ]     = 'location_country';
            $company[ 'index' ]     = 'company_name';
            $description[ 'index' ] = 'description';
            $position[ 'index' ]    = 'position';
            $start[ 'index' ]       = 'start_date';
            $end[ 'index' ]         = 'end_date';
            
            $cityValue        = $city->addChild( 'value', $info[ 'location' ][ 'city' ] );
            $stateValue       = $state->addChild( 'value', $info[ 'location' ][ 'state' ] );
            $countryValue     = $country->addChild( 'value', $info[ 'location' ][ 'country' ] );
            $companyValue     = $company->addChild( 'value', $info[ 'company_name' ] );
            $descriptionValue = $description->addChild( 'value', $info[ 'description' ] );
            $positionValue    = $position->addChild( 'value', $info[ 'position' ] );
            $startValue       = $start->addChild( 'value', $info[ 'start_date' ] );
            $endValue         = $end->addChild( 'value', $info[ 'end_date' ] );
            
            $cityValue[ 'index' ]        = 'vDEF';
            $stateValue[ 'index' ]       = 'vDEF';
            $countryValue[ 'index' ]     = 'vDEF';
            $companyValue[ 'index' ]     = 'vDEF';
            $descriptionValue[ 'index' ] = 'vDEF';
            $positionValue[ 'index' ]    = 'vDEF';
            $startValue[ 'index' ]       = 'vDEF';
            $endValue[ 'index' ]         = 'vDEF';
            
            $sectionId++;
        }
        
        return $flex->asXML();
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/facebook_connect/classes/class.tx_facebookconnect_pi1_profileHelper.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/facebook_connect/classes/class.tx_facebookconnect_pi1_profileHelper.php']);
}
