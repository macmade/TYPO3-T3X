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

# $Id: Manual.class.php 183 2010-01-04 12:36:28Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * MVC controller
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_care
 */
class tx_marvincare_View_Manual extends tx_marvincare_View_Base
{
    const TABLE_LANGUAGES = 'tx_marvindata_languages';
    
    protected $_manual = array();
    
    public function __construct( tslib_piBase $plugin, tx_oop_Database_Object $manual )
    {
        parent::__construct( $plugin );
        
        $this->_manual = $manual;
        
        $this->_content->h2 = $manual->fullname;
        $this->_showFiles( $this->_content->div );
        
        $this->_setPageTitle( $manual->fullname );
    }
    
    protected function _showFiles( tx_oop_Xhtml_Tag $container )
    {
        $xml = simplexml_load_string( $this->_manual->files );
        
        foreach( $xml->data->sheet->language->field->el->section as $manual ) {
            
            $langId = ( int )$manual->itemType->el->field[ 0 ]->value;
            $file   = ( string )$manual->itemType->el->field[ 1 ]->value;
            
            try {
                
                $lang           = new tx_oop_Database_Object( self::TABLE_LANGUAGES, $langId );
                $link           = $container->div->a;
                $link[ 'href' ] = str_replace( PATH_site, '', t3lib_div::getFileAbsFileName( 'uploads/tx_marvincare/' . $file ) );
                
                $link->addTextData( $lang->fullname );
                
            } catch( Exception $e ) {
                
                return;
            }
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/classes/View/Manual.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/classes/View/Manual.class.php']);
}
