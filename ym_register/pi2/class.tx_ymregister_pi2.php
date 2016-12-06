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
 * Frontend plugin PI2 for the 'ym_register' extension.
 *
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  ym_register
 */
class tx_ymregister_pi2 extends tx_oop_Plugin_Base
{
    /**
     * 
     */
    const TABLE_USERS    = 'fe_users';
    /**
     * 
     */
    const TABLE_PROFILES = 'tx_ymregister_users';
    /**
     * 
     */
    const TABLE_TWITTER  = 'tx_twitterlogin_users';
    /**
     * 
     */
    const TABLE_FACEBOOK = 'tx_facebookconnect_users';
    
    /**
     * 
     */
    protected $_user     = NULL;
    
    /**
     * 
     */
    protected $_profile  = NULL;
    
    /**
     * 
     */
    protected $_facebook = NULL;
    
    /**
     * 
     */
    protected $_twitter  = NULL;
    
    /**
     * 
     */
    protected $_template = NULL;
    
    /**
     * 
     */
    protected function _checkPluginSettings()
    {
        if( !isset( $this->conf[ 'templateFile' ] ) ) {
            
            return false;
        }
        
        return true;
    }
    
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
        
        if( $this->_checkPluginSettings() == false ) {
            
            $content->strong = $this->_lang->errorSettings;
            return;
        }
        
        $this->_user = new tx_oop_Database_Object( self::TABLE_USERS, self::$_tsfe->fe_user->user[ 'uid' ] );
        
        $profiles = tx_oop_Database_Object::getObjectsWhere( self::TABLE_PROFILES, 'id_fe_users=' . $this->_user->uid );
        $facebook = tx_oop_Database_Object::getObjectsWhere( self::TABLE_FACEBOOK, 'id_fe_users=' . $this->_user->uid );
        $twitter  = tx_oop_Database_Object::getObjectsWhere( self::TABLE_TWITTER,  'id_fe_users=' . $this->_user->uid );
        
        if( !count( $profiles ) ) {
            
            return;
        }
        
        $this->_profile = array_shift( $profiles );
        
        if( count( $facebook ) ) {
            
            $this->_facebook = array_shift( $facebook );
        }
        
        if( count( $twitter ) ) {
            
            $this->_twitter = array_shift( $twitter );
        }
        
        $this->_template = $this->_getFrontendTemplate( $this->conf[ 'templateFile' ] );
        
        $content->div = $this->_template->userForm;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ym_register/pi2/class.tx_ymregister_pi2.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ym_register/pi2/class.tx_ymregister_pi2.php']);
}
