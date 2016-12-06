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

# $Id: Movement.class.php 183 2010-01-04 12:36:28Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Movement view
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_care
 */
class tx_marvincare_View_Movement extends tx_marvincare_View_Base
{
    protected $_movements = array();
    
    public function __construct( tslib_piBase $plugin, array $movements )
    {
        parent::__construct( $plugin );
        
        $this->_movements = $movements;
        
        $auto   = $this->_content->div;
        $quartz = $this->_content->div;
        
        $this->_cssClass( 'auto', $auto );
        $this->_cssClass( 'quartz', $quartz );
        
        $auto->h2   = $this->_lang->titleAuto;
        $quartz->h2 = $this->_lang->titleQuartz;
        
        $this->_showMovements( 0, $auto );
        $this->_showMovements( 1, $quartz );
    }
    
    protected function _showMovements( $type, tx_oop_Xhtml_Tag $container )
    {
        foreach( $this->_movements as $key => $value ) {
            
            if( $value->movement_type != $type ) {
                
                continue;
            }
            
            $link           = $container->div->a;
            $link[ 'href' ] = $this->_link(
                array(
                    'action'   => 'files',
                    'movement' => $value->uid
                )
            );
            
            $link->addTextData( $value->fullname );
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/classes/View/Movement.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/classes/View/Movement.class.php']);
}
