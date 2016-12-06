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

# $Id: Map.class.php 163 2009-12-14 16:24:31Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Map view
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_addresses
 */
class tx_marvinaddresses_View_Map extends tx_marvinaddresses_View_Base
{
    protected $_cities = array();
    
    public function __construct( tslib_piBase $plugin, array $cities = array() )
    {
        parent::__construct( $plugin );
        
        $this->_cities = $cities;
        
        $this->_buildMapImage( $this->_content );
    }
    
    protected function _buildMapImage( tx_oop_Xhtml_Tag $container )
    {
        $conf     = $this->_plugin->conf[ 'map.' ];
        $dotSize  = $this->_plugin->conf[ 'dotSize' ];
        $dotColor = $this->_plugin->conf[ 'dotColor' ];
        $img      = $this->_plugin->conf[ 'mapImg' ];
        
        $conf[ 'file.' ][ '10' ] = 'IMAGE';
        $conf[ 'file.' ][ '10.' ] = array( 'file' => $img  );
        
        $index = 20;
        
        foreach( $this->_cities as $key => $value ) {
            
            if( $value->coord_x == 0 && $value->coord_y == 0 ) {
                
                continue;
            }
            
            $conf[ 'file.' ][ ( string )$index ]  = 'BOX';
            $conf[ 'file.' ][ ( string )$index . '.' ] = array(
                'dimensions' => $value->coord_x . ',' . $value->coord_y . ',' . $dotSize . ',' . $dotSize,
                'color'      => $dotColor
            );
            
            $index += 10;
        }
        
        $container->addTextData( $this->_plugin->cObj->IMAGE( $conf ) );
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/Map.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/Map.class.php']);
}
