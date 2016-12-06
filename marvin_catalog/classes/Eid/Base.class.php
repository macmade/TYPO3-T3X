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

# $Id: Base.class.php 103 2009-12-01 13:54:50Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Magento EID abstract
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
abstract class tx_marvincatalog_Eid_Base
{
    protected $_db   = NULL;
    protected $_conf = array();
    
    public function __construct()
    {
        if( !isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] ) ) {
            
            throw new tx_marvincatalog_Eid_Exception(
                'The extension is not configured properly.',
                tx_marvincatalog_Eid_Exception::EXCEPTION_BAD_CONFIGURATION
            );
        }
        
        $this->_conf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] );
        
        $this->_db = t3lib_div::makeInstance( 't3lib_DB' );
        
        $this->_db->sql_pconnect( $this->_conf[ 'db_host' ], $this->_conf[ 'db_user' ], $this->_conf[ 'db_pass' ] );
        $this->_db->sql_select_db( $this->_conf[ 'db_name' ] );
    }
}
