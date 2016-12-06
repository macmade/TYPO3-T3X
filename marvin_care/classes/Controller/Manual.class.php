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
class tx_marvincare_Controller_Manual extends tx_netfw_Controller
{
    const TABLE_MOVEMENTS = 'tx_marvincare_movements';
    const TABLE_MANUALS   = 'tx_marvincare_manuals';
    
    private static $_hasStatic = false;
    protected static $_db      = NULL;
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        if( self::$_hasStatic === false ) {
            
            self::$_db = tx_oop_Database_Layer::getInstance();
        }
    }
    
    /**
     * 
     */
    public function indexAction()
    {
        $movements = tx_oop_Database_Object::getObjectsByFields( self::TABLE_MOVEMENTS, array( 'pid' => $this->getPiBase()->conf[ 'storage' ] ) );
        $content   = new tx_oop_Xhtml_Tag( 'div' );
        $left      = $content->div;
        $right     = $content->div;
        $view      = new tx_marvincare_View_Movement( $this->getPiBase(), $movements );
        
        $left[ 'class' ]  = 'tx-marvincare-pi1-left';
        $right[ 'class' ] = 'tx-marvincare-pi1-right';
        
        $left->addTextData( $view );
        $right->addTextData( $this->getPiBase()->conf[ 'intro' ] );
        
        return $content;
    }
    
    /**
     * 
     */
    public function FilesAction()
    {
        $movements = tx_oop_Database_Object::getObjectsByFields( self::TABLE_MOVEMENTS, array( 'pid' => $this->getPiBase()->conf[ 'storage' ] ) );
        $movement  = tx_oop_Database_Object::getObjectsByFields( self::TABLE_MANUALS, array( 'id_movement' => $this->getPiBase()->piVars[ 'movement' ], 'pid' => $this->getPiBase()->conf[ 'storage' ] ) );
        
        if( !count( $movement ) ) {
            
            return;
        }
        
        $content   = new tx_oop_Xhtml_Tag( 'div' );
        $left      = $content->div;
        $right     = $content->div;
        $leftView  = new tx_marvincare_View_Movement( $this->getPiBase(), $movements );
        $rightView = new tx_marvincare_View_Manual( $this->getPiBase(), array_shift( $movement ) );
        
        $left[ 'class' ]  = 'tx-marvincare-pi1-left';
        $right[ 'class' ] = 'tx-marvincare-pi1-right';
        
        $left->addTextData( $leftView );
        $right->addTextData( $rightView );
        
        return $content;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/classes/Controller/Manual.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/classes/Controller/Manual.class.php']);
}
