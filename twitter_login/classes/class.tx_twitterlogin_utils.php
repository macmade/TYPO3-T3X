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

# $Id$

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Twitter utilities for TYPO3
 *
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  twitter_login
 */
final class tx_twitterlogin_utils
{
    /**
     * The HTTP protocol version for the request on the Twitter API
     */
    const HTTP_VERSION  = '1.1';
    
    /**
     * The hostname for the Twitter API
     */
    const TWITTER_HOST  = 'twitter.com';
    
    /**
     * The port for the Twitter API connection
     */
    const TWITTER_PORT  = 80;
    
    /**
     * The URL of the Twitter REST service to verify the user's credidentials
     */
    const TWITTER_LOGIN = '/account/verify_credentials.xml';
    
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance   = NULL;
    
    /**
     * Whether to use CURL to connect to the twitter API
     */
    protected static $_useCurl  = false;
    
    /**
     * 
     */
    private function __construct()
    {
        // Checks for the extension global configuration
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'twitter_login' ] ) ) {
            
            // Gets the configuration as an array
            $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'twitter_login' ] );
            
            // Checks if the connection method is defined
            if( isset( $extConf[ 'connection' ] ) && $extConf[ 'connection' ] === 'curl' ) {
                
                // We will use CURL
                self::$_useCurl = true;
            }
        }
    }
    
    /**
     * 
     */
    public static function getInstance()
    {
        if( !is_object( self::$_instance ) ) {
            
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * 
     */
    protected function _getTwitterUserInfosWithSocket( $username, $password )
    {
        // Opens a socket to Twitter
        $connect = fsockopen ( self::TWITTER_HOST, self::TWITTER_PORT );
        
        // Checks for a valid socket
        if( !$connect ) {
            
            return NULL;
        }
        
        // New line character for the HTTP protocol
        $nl   = chr( 13 ) . chr( 10 );
        
        // Creates the HTTP request
        $req  = 'GET ' . self::TWITTER_LOGIN . ' HTTP/' . self::HTTP_VERSION . $nl
              . 'Host: ' . self::TWITTER_HOST . $nl;
        
        if( $password !== '' ) {
            
              $req .= 'Authorization: Basic ' . base64_encode( $username . ':' . $password ) . $nl;
        }
        
        $req .= 'Connection: Close' . $nl . $nl;
        
        // Sends the request in the socket
        fwrite( $connect, $req );
        
        // Storage
        $response    = '';
        $headersSent = false;
        
        // Gets the HTTP response status
        $status      = substr( fgets( $connect, 128 ), -8, 6 );
        
        // Checks if the page was found
        if( $status !== '200 OK' ) {
            
            return '';
        }
        
        // Reads the socket contents
        while( !feof( $connect ) ) {
            
            // Reads a line
            $line = fgets( $connect, 128 );
            
            // Checks if we are reading the headers or the reponse
            if( $headersSent ) {
                
                // Adds the current data line to the response string
                $response .= $line;
            }
            
            // Checks for the end of the headers
            if( $line === $nl ) {
                
                // The response headers have been sent
                $headersSent = true;
            }
        }
        
        // Returns the XML data
        return $response;
    }
    
    /**
     * 
     */
    protected function _getTwitterUserInfosWithCurl( $username, $password )
    {
        // Creates the CURL resource
        $curl = curl_init();
        
        // Sets the CURL options
        curl_setopt( $curl, CURLOPT_HEADER,         false );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_HTTP_VERSION,   ( ( self::HTTP_VERSION === '1.1' ) ? CURL_HTTP_VERSION_1_1 : CURL_HTTP_VERSION_1_0 ) );
        curl_setopt( $curl, CURLOPT_URL,            self::TWITTER_HOST . self::TWITTER_LOGIN );
        
        if( $password !== '' ) {
            
            curl_setopt( $curl, CURLOPT_USERPWD,  $username . ':' . $password );
            curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        }
        
        // Gets the response from Twitter
        $response = curl_exec( $curl );
        
        // Closes the CURL resource
        curl_close( $curl );
        
        // Returns the XML data
        return $response;
    }
    
    /**
     * 
     */
    public function getTwitterUserInfos( $username, $password = '' )
    {
        // Checks the connection method for the Twitter API
        if( self::$_useCurl === true && function_exists( 'curl_init' ) ) {
            
            // CURL
            $xmlData = self::_getTwitterUserInfosWithCurl( ( string )$username, ( string )$password );
            
        } else {
            
            // Socket
            $xmlData = self::_getTwitterUserInfosWithSocket( ( string )$username, ( string )$password );
        }
        
        try {
            
            // Loads the response XML data
            $xml = simplexml_load_string( $xmlData );
            
        } catch( Exception $e ) {
            
            // Invalid reponse
            return NULL;
        }
        
        // Checks for an error
        if( !is_object( $xml ) || isset( $xml->error ) ) {
            
            return NULL;
        }
        
        // Returns the Twitter user infos
        return $xml;
    }
    
    /**
     * 
     */
    protected function updateUserRecordFromXml( tx_oop_Database_Object $user, SimpleXMLElement $infos )
    {
        // Set integer fields
        $user->twitter_id                     = ( int )$infos->id;
        $user->followers_count                = ( int )$infos->followers_count;
        $user->friends_count                  = ( int )$infos->friends_count;
        $user->favourites_count               = ( int )$infos->favourites_count;
        $user->utc_offset                     = ( int )$infos->utc_offset;
        $user->statuses_count                 = ( int )$infos->statuses_count;
        $user->status_id                      = ( int )$infos->status->id;
        $user->status_in_reply_to_status_id   = ( int )$infos->status->in_reply_to_status_id;
        $user->status_in_reply_to_user_id     = ( int )$infos->status->status_in_reply_to_user_id;
        
        // Set text fields
        $user->fullname                       = ( string )$infos->name;
        $user->screen_name                    = ( string )$infos->screen_name;
        $user->location                       = ( string )$infos->location;
        $user->description                    = ( string )$infos->description;
        $user->profile_image_url              = ( string )$infos->profile_image_url;
        $user->url                            = ( string )$infos->url;
        $user->protected                      = ( string )$infos->protected;
        $user->profile_background_color       = ( string )$infos->profile_background_color;
        $user->profile_text_color             = ( string )$infos->profile_text_color;
        $user->profile_link_color             = ( string )$infos->profile_link_color;
        $user->profile_sidebar_fill_color     = ( string )$infos->profile_sidebar_fill_color;
        $user->profile_sidebar_border_color   = ( string )$infos->profile_sidebar_border_color;
        $user->time_zone                      = ( string )$infos->time_zone;
        $user->profile_background_image_url   = ( string )$infos->profile_background_image_url;
        $user->profile_background_tile        = ( string )$infos->profile_background_tile;
        $user->status_text                    = ( string )$infos->status->text;
        $user->status_source                  = ( string )$infos->status->source;
        $user->status_in_reply_to_screen_name = ( string )$infos->status->in_reply_to_screen_name;
        
        // Set date fields
        $user->created_at                     = strtotime( ( string )$infos->created_at );
        $user->status_created_at              = strtotime( ( string )$infos->status->created_at );
        
        // Set boolean fields
        $user->notifications                  = ( ( string )$infos->notifications     === 'false') ? 0 : 1;
        $user->verified                       = ( ( string )$infos->verified          === 'false') ? 0 : 1;
        $user->following                      = ( ( string )$infos->following         === 'false') ? 0 : 1;
        $user->status_truncated               = ( ( string )$infos->status->truncated === 'false') ? 0 : 1;
        $user->status_favorited               = ( ( string )$infos->status->favorited === 'false') ? 0 : 1;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_login/classes/class.tx_twitterlogin_utils.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_login/classes/classes.tx_twitterlogin_utils.php']);
}
