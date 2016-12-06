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

# $Id: Layout.class.php 163 2009-12-14 16:24:31Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Layout
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_addresses
 */
class tx_marvinaddresses_Layout extends tx_oop_Xhtml_Tag
{
    protected $_menu    = NULL;
    protected $_country = NULL;
    protected $_city    = NULL;
    protected $_map     = NULL;
    protected $_address = NULL;
    
    public function __construct()
    {
        parent::__construct( 'div' );
        
        $this->_attribs[ 'class' ] = 'tx-marvinaddresses-pi1-layout';
        
        $this->_menu    = $this->div;
        $this->_country = $this->_menu->div;
        $this->_city    = $this->_menu->div;
        $this->_map     = $this->_menu->div;
        $this->_address = $this->div;
        
        $this->_menu[ 'id' ]    = 'tx_marvinaddresses_pi1_menu';
        $this->_country[ 'id' ] = 'tx_marvinaddresses_pi1_countries';
        $this->_city[ 'id' ]    = 'tx_marvinaddresses_pi1_cities';
        $this->_map[ 'id' ]     = 'tx_marvinaddresses_pi1_map';
        $this->_address[ 'id' ] = 'tx_marvinaddresses_pi1_address';
    }
    
    public function getMenu()
    {
        return $this->_menu;
    }
    
    public function getCountry()
    {
        return $this->_country;
    }
    
    public function getCity()
    {
        return $this->_city;
    }
    
    public function getMap()
    {
        return $this->_map;
    }
    
    public function getAddress()
    {
        return $this->_address;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/Layout.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/Layout.class.php']);
}
