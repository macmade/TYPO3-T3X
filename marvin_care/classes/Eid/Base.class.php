<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 netinfluence                                                        #
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

# $Id: Base.class.php 153 2009-12-10 13:11:35Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * EID abstract
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_care
 */
abstract class tx_marvincare_Eid_Base
{
    protected $_db      = NULL;
    protected $_content = NULL;
    
    public function __construct()
    {
        $this->_db          = tx_oop_Database_Layer::getInstance();
        $this->_content     = new tx_oop_Xhtml_Tag( 'div' );
        $feUserObj          = tslib_eidtools::initFeUser();
        $temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
        $TSFE               = new $temp_TSFEclassName($TYPO3_CONF_VARS, $page, 0, true);
        
        $TSFE->connectToDB();
        
        $GLOBALS['TSFE'] = $TSFE;
        
        $GLOBALS[ 'TYPO3_DB' ]->admin_get_tables();
    }
    
    public function __toString()
    {
        return ( string )$this->_content;
    }
    
    protected function _loadTca( $extKey, $tableName )
    {
        if( !isset( $GLOBALS[ 'TCA' ] ) || !is_array( $GLOBALS[ 'TCA' ] ) ) {
            
            $GLOBALS[ 'TCA' ] = array();
        }
        
        if( isset( $GLOBALS[ 'TCA' ][ $tableName ] ) ) {
            
            return;
        }
        
        $_EXTKEY = $extKey;
        
        require_once( t3lib_extMgm::extPath( $extKey ) . 'ext_tables.php' );
        
        if( $TCA[ $tableName ] ) {
            
            $GLOBALS[ 'TCA' ][ $tableName ] = $TCA[ $tableName ];
            
            t3lib_div::loadTca( $tableName );
        }
    }
}
