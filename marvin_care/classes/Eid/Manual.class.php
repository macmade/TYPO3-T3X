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

# $Id: Manual.class.php 155 2009-12-10 13:53:30Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

require_once( PATH_t3lib . "class.t3lib_page.php" );
require_once( PATH_tslib . "class.tslib_content.php" );

/**
 * Manuals EID class
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_care
 */
class tx_marvincare_Eid_Manual extends tx_marvincare_Eid_Base
{
    const TABLE_MOVEMENTS = 'tx_marvincare_movements';
    const TABLE_MANUALS   = 'tx_marvincare_manuals';
    const TABLE_LANG      = 'tx_marvindata_languages';
    
    protected $_lang = NULL;
    
    public function __construct()
    {
        parent::__construct();
        
        $id = ( int )t3lib_div::_GP( 'movement' );
        
        if( $id === 0 ) {
            
            return;
        }
        
        try {
            
            $this->_loadTca( 'marvin_care', self::TABLE_MOVEMENTS );
            $this->_loadTca( 'marvin_care', self::TABLE_MANUALS );
            $this->_loadTca( 'marvin_data', self::TABLE_LANG );
            
            $movement = new tx_oop_Database_Object( self::TABLE_MOVEMENTS, $id );
            $manuals  = tx_oop_Database_Object::getObjectsByFields( self::TABLE_MANUALS, array( 'id_movement' => $movement->uid ) );
            
            if( !count( $manuals ) ) {
                
                return;
            }
            
            $languages          = array();
            $uploadDir          = str_replace( PATH_site, '', t3lib_div::getFileAbsFileName( 'uploads/tx_marvincare/' ) );
            $manual             = array_shift( $manuals );
            $flex               = simplexml_load_string( $manual->files );
            $files              = $flex->data->sheet->language->field->el->section;
            $this->_content->h2 = $manual->fullname;
            
            foreach( $files as $file ) {
                
                $langId = ( int )$file->itemType->el->field[ 0 ]->value;
                $path = ( string )$file->itemType->el->field[ 1 ]->value;
                
                if( !isset( $languages[ $langId ] ) ) {
                    
                    $languages[ $langId ] = new tx_oop_Database_Object( self::TABLE_LANG, $langId );
                }
                
                $lang            = $languages[ $langId ];
                $link            = $this->_content->div->a;
                $link[ 'href' ]  = $uploadDir . $path;
                $link[ 'title' ] = $lang->fullname;
                
                $link->addTextData( $lang->fullname );
            }
            
        } catch( Exception $e ) {}
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/classes/Eid/Manual.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/classes/Eid/Manual.class.php']);
}
