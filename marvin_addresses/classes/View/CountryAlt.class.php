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

# $Id: CountryAlt.class.php 182 2010-01-04 12:35:37Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Country view
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_addresses
 */
class tx_marvinaddresses_View_CountryAlt extends tx_marvinaddresses_View_Base
{
    protected $_countries = array();
    
    public function __construct( tslib_piBase $plugin, array $countries )
    {
        parent::__construct( $plugin );
        
        $this->_countries = $countries;
        
        $regions    = $this->_content->div;
        $europe     = new tx_oop_Xhtml_Tag( 'div' );
        $america    = new tx_oop_Xhtml_Tag( 'div' );
        $asia       = new tx_oop_Xhtml_Tag( 'div' );
        $middleEast = new tx_oop_Xhtml_Tag( 'div' );
        
        $europe->h2     = $this->_lang->europe;
        $america->h2    = $this->_lang->america;
        $asia->h2       = $this->_lang->asia;
        $middleEast->h2 = $this->_lang->middleEast;
        
        $this->_cssClass( 'regions', $regions );
        $this->_cssClass( 'region', $europe );
        $this->_cssClass( 'region', $america );
        $this->_cssClass( 'region', $asia );
        $this->_cssClass( 'region', $middleEast );

        if( $this->_showCountries( 0, $europe ) ) {
            
            $regions->addChildNode( $europe );
        }
        
        if( $this->_showCountries( 1, $america ) ) {
            
            $regions->addChildNode( $america );
        }
        
        if( $this->_showCountries( 2, $asia ) ) {
            
            $regions->addChildNode( $asia );
        }
        
        if( $this->_showCountries( 3, $middleEast ) ) {
            
            $regions->addChildNode( $middleEast );
        }
    }
    
    protected function _showCountries( $region, tx_oop_Xhtml_Tag $container )
    {
        $hasCountries = false;
        
        foreach( $this->_countries as $country ) {
            
            if( $country->region == $region ) {
                
                $hasCountries = true;
                
                $link           = $container->div->a;
                $link[ 'href' ] = $this->_link( array( 'action' => 'address-country', 'country' => $country->uid ) );
                
                if( isset( $this->_plugin->piVars[ 'country' ] ) && $country->uid == $this->_plugin->piVars[ 'country' ] ) {
                    
                    $this->_cssClass( 'selected', $link );
                }
                
                $link->addTextData( $country->fullname );
            }
        }
        
        return $hasCountries;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/CountryAlt.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/CountryAlt.class.php']);
}
