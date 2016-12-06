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

# $Id: Address.class.php 182 2010-01-04 12:35:37Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Distributor view
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_addresses
 */
class tx_marvinaddresses_View_Address extends tx_marvinaddresses_View_Base
{
    const TABLE_CITIES = 'tx_marvindata_cities';
    const TABLE_COUNTRIES = 'tx_marvindata_countries';
    
    protected $_addresses = array();
    protected $_page      = 0;
    
    public function __construct( tslib_piBase $plugin, array $addresses )
    {
        parent::__construct( $plugin );
        
        $this->_addresses = $addresses;
        
        if( isset( $this->_plugin->piVars[ 'page' ] ) ) {
            
            $this->_page = ( int )$this->_plugin->piVars[ 'page' ];
        }
        
        if( !count( $this->_addresses ) ) {
            
            return;
        }
        
        if( !isset( $this->_addresses[ $this->_page ] ) ) {
            
            $this->_page = 0;
        }
        
        $this->_showBrowser();
        $this->_showAddress( $this->_addresses[ $this->_page ] );
    }
    
    protected function _showBrowser()
    {
        $browser = $this->_content->div;
        
        $this->_cssClass( 'browser', $browser );
        
        if( isset( $this->_addresses[ $this->_page - 1 ] ) ) {
            
            $params           = $this->_plugin->piVars;
            $params[ 'page' ] = $this->_page - 1;
            
            $link           = new tx_oop_Xhtml_Tag( 'a' );
            $link[ 'href' ] = $this->_link( $params );
            
            $link->addChildNode( $this->_createButtonImage( 'previous' )  );
            $browser->div->addChildNode( $link );
            
        } else {
            
            #$browser->div->addChildNode( $this->_createButtonImage( 'previousOff' ) );
        }
        
        if( isset( $this->_addresses[ $this->_page + 1 ] ) ) {
            
            $params           = $this->_plugin->piVars;
            $params[ 'page' ] = $this->_page + 1;
            
            $link           = new tx_oop_Xhtml_Tag( 'a' );
            $link[ 'href' ] = $this->_link( $params );
            
            $link->addChildNode( $this->_createButtonImage( 'next' )  );
            $browser->div->addChildNode( $link );
            
        } else {
            
            #$browser->div->addChildNode( $this->_createButtonImage( 'nextOff' ) );
        }
    }
    
    protected function _createButtonImage( $name )
    {
        $img  = new tx_oop_Xhtml_Tag( 'span' );
        $conf = $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'image.' ];
        
        if(    isset( $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'class' ] )
            && $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'class' ]
        ) {
            $container[ 'class' ] = $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'class' ];
        }
        
        $img->addTextData( $this->_plugin->cObj->IMAGE( $conf ) );
        $this->_cssClass( 'button-image', $img );
        
        return $img;
    }
    
    protected function _urlLink( $url )
    {
        $conf = array(
            'parameter' => $url,
            'extTarget' => '_blank'
        );
        
        return $this->_plugin->cObj->typolink( $url, $conf );
    }
    
    protected function _emailLink( $email )
    {
        $conf = array(
            'parameter' => $email
        );
        
        return $this->_plugin->cObj->typolink( $email, $conf );
    }
    
    protected function _showAddress( $address )
    {
        $city    = new tx_oop_Database_Object( self::TABLE_CITIES, $address->id_city );
        $country = new tx_oop_Database_Object( self::TABLE_COUNTRIES, $city->id_country );
        
        $this->_content->h2 = $city->fullname . ' - ' . $country->fullname;
        $this->_content->h3 = $address->fullname;
        
        $this->_setPageTitle( $address->fullname . ' - ' . $city->fullname . ' - ' . $country->fullname );
        
        $infos   = $this->_content->div;
        $numbers = $this->_content->div;
        $web     = $this->_content->div;
        
        $this->_cssClass( 'infos', $infos );
        $this->_cssClass( 'numbers', $numbers );
        $this->_cssClass( 'web', $web );
        
        $infos->div = $address->address . ' ' . $address->address_number;
        $infos->div = $address->zip . ' ' . $city->fullname;
        
        if( $address->phone ) {
            
            $numbers->div = $address->phone;
        }
        
        if( $address->phone2 ) {
            
            $numbers->div = $address->phone2;
        }
        
        if( $address->fax ) {
            
            $numbers->div = $address->fax;
        }
        
        if( $address->email ) {
            
            $web->div = $this->_emailLink( $address->email );
        }
        
        if( $address->email2 ) {
            
            $web->div = $this->_emailLink( $address->email2 );
        }
        
        if( $address->url ) {
            
            $web->div = $this->_urlLink( $address->url );
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/Address.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/Address.class.php']);
}
