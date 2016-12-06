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

# $Id: AutoComplete.php 7 2010-10-18 13:46:27Z jean $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * EID for the autocompletion
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Eid_AutoComplete extends Tx_NetExtbase_Eid_Base
{
    protected $_word         = '';
    protected $_lang         = 0;
    protected $_autoComplete = array();
    
    public function __construct()
    {
        $this->_word = ( string )t3lib_div::_GP( 'q' );
        $this->_lang = ( int )t3lib_div::_GP( 'L' );
        tslib_eidtools::connectDB();
    }
    
    public function execute()
    {
        if( !$this->_word )
        {
            return;
        }
        
        $word  = preg_replace( '/ +/', ' ', $this->_word );
        $words = explode( ' ', $word );
        $word  = $GLOBALS[ 'TYPO3_DB' ]->escapeStrForLike( $word, 'tx_cpguide_domain_model_word' );
        $where = 'word LIKE "' . $word . '%" AND word_count=' . count( $words ) . ' AND NOT hidden AND NOT deleted AND sys_language_uid = ' . $this->_lang;
        
        $res  = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery
        (
            '*',
            'tx_cpguide_domain_model_word',
            $where
        );
        
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) )
        {
            $this->_autoComplete[] = $row[ 'word' ];
        }
    }
    
    public function __toString()
    {
        return implode( chr( 10 ), $this->_autoComplete );
    }
}
