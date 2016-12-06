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

/**
 * Frontend plugin PI3 for the 'ym_register' extension.
 *
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  ym_register
 */
class tx_ymregister_pi3 extends tx_oop_Plugin_Base
{
    /**
     * Gets the plugin content
     * 
     * @param   tx_oop_Xhtml_Tag    The plugin's content container
     * @return  NULL
     */
    protected function _getPluginContent( tx_oop_Xhtml_Tag $content )
    {
        if( !self::$_tsfe->loginUser ) {
            
            return;
            
        }
        
        if( $movieId = self::$_tsfe->fe_user->getKey( 'ses', 'movieUserId' ) ) {
            
            $moviesRes = self::$_t3Db->exec_SELECTquery(
                'uid',
                'tx_ymvideo_xml',
                'usr_id=' . self::$_t3Db->fullQuoteStr( $movieId, 'tx_ymvideo_xml' )
            );
            
            $movie = self::$_t3Db->sql_fetch_assoc( $moviesRes );
            
            if( !$movie ) {
                
                return;
            }
            
            self::$_t3Db->exec_UPDATEquery(
                'tx_ymvideo_xml',
                'usr_id=' . self::$_t3Db->fullQuoteStr( $movieId, 'tx_ymvideo_xml' ),
                array( 'usr_id' => self::$_tsfe->fe_user->user[ 'uid' ] )
            );
            
            self::$_tsfe->fe_user->setKey( 'ses', 'movieUserId', '0' );
            
            $this->_addJavaScriptCode();
            
            $content->div     = $this->_lang->confirmation;
            $fbLink           = $content->div->a;
            $fbLink[ 'href' ] = 'javascript:FacebookStreamPublishVote(' . $movie[ 'uid' ] . ', ' . self::$_tsfe->fe_user->user[ 'uid' ] . ')';
            
            $fbLink->addTextData( $this->_lang->fbPublish );
            
        } elseif( $movieId = t3lib_div::_GET( 'movieUid' ) ) {
            
            $moviesRes = self::$_t3Db->exec_SELECTquery(
                'uid',
                'tx_ymvideo_xml',
                'usr_id=' . ( int )$movieId
            );
            
            $movie = self::$_t3Db->sql_fetch_assoc( $moviesRes );
            
            if( !$movie ) {
                
                return;
            }
            
            $this->_addJavaScriptCode();
            
            $content->div     = $this->_lang->confirmation;
            $fbLink           = $content->div->a;
            $fbLink[ 'href' ] = 'javascript:FacebookStreamPublishVote(' . $movie[ 'uid' ] . ', ' . self::$_tsfe->fe_user->user[ 'uid' ] . ')';
            
            $fbLink->addTextData( $this->_lang->fbPublish );
        }
    }
    
    /**
     * 
     */
    protected function _addJavaScriptCode()
    {
        // Creates the script tag for the Facebook JavaScript library
        $facebookJs              = new tx_oop_Xhtml_Tag( 'script' );
        $facebookJs[ 'type' ]    = 'text/javascript';
        $facebookJs[ 'charset' ] = 'utf-8';
        $facebookJs[ 'src' ]     = $this->conf[ 'facebookJs' ];
        
        // Adds the script tag to the page's headers
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ __CLASS__ . '_FeatureLoader' ] = ( string )$facebookJs;
        
        // Creates the script tag for the Facebook JavaScript library
        $facebookHelperJs              = new tx_oop_Xhtml_Tag( 'script' );
        $facebookHelperJs[ 'type' ]    = 'text/javascript';
        $facebookHelperJs[ 'charset' ] = 'utf-8';
        $facebookHelperJs[ 'src' ]     = t3lib_extMgm::siteRelPath( 'ym_voting' ) . 'pi1/js/facebook.js';
        
        // Adds the script tag to the page's headers
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ __CLASS__ . '_FBHelper' ] = ( string )$facebookHelperJs;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ym_register/pi3/class.tx_ymregister_pi3.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ym_register/pi3/class.tx_ymregister_pi3.php']);
}
