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

# $Id: Fields.class.php 103 2009-12-01 13:54:50Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Flexform fields.
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_Flexform_Fields
{
    /**
     * Whether the static variables are set or not
     */
    private static $_hasStatic = false;
    
    /**
     * The Magento category helper object
     */
    protected static $_cat     = NULL;
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        if( self::$_hasStatic === false ) {
            
            self::$_cat       = tx_marvincatalog_Magento_Category_Helper::getInstance();
            self::$_hasStatic = true;
        }
    }
    
    /**
     * Gets the list of the magento root categories
     * 
     * @param   array           The parameters array
     " @param   t3lib_TCEforms  The TCE forms object
     * @return  NULL
     */
    public function getMagentoRootCategories( array &$params, t3lib_TCEforms $pObj )
    {
        $categories = self::$_cat->getRootCategories();
        
        foreach( $categories as $category ) {
            
            $name = $category->name;
            
            if( !isset( $category->children ) ) {
                
                $name .= ' (0)';
                
            } else {
                
                $name .= ' (' . count( $category->children ) . ')';
            }
            
            $params[ 'items' ][] = array( $name, $category->id );
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Flexform/Fields.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Flexform/Fields.class.php']);
}

