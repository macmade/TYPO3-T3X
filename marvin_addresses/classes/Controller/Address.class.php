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
 * MVC controller
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_addresses
 */
class tx_marvinaddresses_Controller_Address extends tx_netfw_Controller
{
    const TABLE_COUNTRIES = 'tx_marvindata_countries';
    const TABLE_CITIES    = 'tx_marvindata_cities';
    const TABLE_ADDRESSES = 'tx_marvinaddresses_addresses';
    
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
    protected function _getCountries()
    {
        $sql = 'SELECT
                    address.id_city,
                    address.sys_language_uid,
                    address.hidden,
                    address.deleted,
                    address.address_type,
                    address.sav,
                    city.uid,
                    city.fullname,
                    city.id_country,
                    country.uid AS country
                FROM
                    ' . self::TABLE_ADDRESSES . ' AS address,
                    ' . self::TABLE_CITIES    . ' AS city,
                    ' . self::TABLE_COUNTRIES . ' AS country
                WHERE
                    address.id_city              = city.uid
                    AND address.sys_language_uid = 0
                    AND address.hidden           = 0
                    AND address.deleted          = 0
                    AND city.id_country          = country.uid';
        
        $addressType = ( int )$this->getPiBase()->conf[ 'addressType' ];
        
        if( $addressType === 0 ) {
            
            $sql .= ' AND address.address_type = 1';
            
        } elseif( $addressType === 1 ) {
            
            $sql .= ' AND address.address_type = 2';
            
        } elseif( $addressType === 2 ) {
            
            $sql .= ' AND address.sav = 1';
        }
        
        $query     = self::$_db->prepare( $sql );
        $countries = array();
        
        $query->execute();
        
        while( $row = $query->fetchObject() ) {
            
            $country = new tx_oop_Database_Object( self::TABLE_COUNTRIES, $row->country );
            
            $country->localize();
            
            $countries[ $country->fullname ] = $country;
        }
        
        ksort( $countries );
        
        return array_values( $countries );
    }
    
    /**
     * 
     */
    protected function _getCities( $country )
    {
        $sql = 'SELECT
                    address.id_city,
                    address.sys_language_uid,
                    address.hidden,
                    address.deleted,
                    city.uid,
                    city.id_country
                FROM
                    ' . self::TABLE_ADDRESSES . ' AS address,
                    ' . self::TABLE_CITIES    . ' AS city
                WHERE
                    address.id_city              = city.uid
                    AND address.sys_language_uid = 0
                    AND address.hidden           = 0
                    AND address.deleted          = 0
                    AND city.id_country          = ' . $country;
        
        $type = ( int )$this->getPiBase()->conf[ 'addressType' ];
        
        if( $type === 0 ) {
            
            $sql .= ' AND address.address_type = 1';
            
        } elseif( $type === 1 ) {
            
            $sql .= ' AND address.address_type = 2';
            
        } elseif( $type === 2 ) {
            
            $sql .= ' AND address.sav = 1';
        }
        
        $query  = self::$_db->prepare( $sql );
        $cities = array();
        
        $query->execute();
        
        while( $row = $query->fetchObject() ) {
            
            $city                      = new tx_oop_Database_Object( self::TABLE_CITIES, $row->uid );
            $cities[ $city->fullname ] = $city;
            
            $city->localize();
        }
        
        ksort( $cities );
        
        return array_values( $cities );
    }
    
    /**
     * 
     */
    public function _getAddressesByCountry( $id )
    {
        $sql = 'SELECT
                    address.id_city,
                    address.sys_language_uid,
                    address.hidden,
                    address.deleted,
                    address.uid,
                    city.id_country
                FROM
                    ' . self::TABLE_ADDRESSES . ' AS address,
                    ' . self::TABLE_CITIES    . ' AS city
                WHERE
                    address.id_city              = city.uid
                    AND address.sys_language_uid = 0
                    AND address.hidden           = 0
                    AND address.deleted          = 0
                    AND city.id_country          = ' . $id;
        
        $type = ( int )$this->getPiBase()->conf[ 'addressType' ];
        
        if( $type === 0 ) {
            
            $sql .= ' AND address.address_type = 1';
            
        } elseif( $type === 1 ) {
            
            $sql .= ' AND address.address_type = 2';
            
        } elseif( $type === 2 ) {
            
            $sql .= ' AND address.sav = 1';
        }
        
        $query     = self::$_db->prepare( $sql );
        $addresses = array();
        
        $query->execute();
        
        while( $row = $query->fetchObject() ) {
            
            $address                                               = new tx_oop_Database_Object( self::TABLE_ADDRESSES, $row->uid );
            $addresses[ $address->fullname . '-' . $address->uid ] = $address;
            
            $address->localize();
        }
        
        ksort( $addresses );
        
        return array_values( $addresses );
    }
    
    /**
     * 
     */
    public function _getAddressesByCity( $id )
    {
        $fields = array( 'id_city' => $id );
        $cities = array();
        $type   = ( int )$this->getPiBase()->conf[ 'addressType' ];
        
        if( $type === 0 ) {
            
            $fields[ 'address_type' ] = 1;
            
        } elseif( $type === 1 ) {
            
            $fields[ 'address_type' ] = 2;
            
        } elseif( $type === 2 ) {
            
            $fields[ 'sav' ] = 1;
        }
        
        $rows = tx_oop_Database_Object::getObjectsByFields( self::TABLE_ADDRESSES, $fields );
        
        foreach( $rows as $key => $value ) {
            
            $cities[ $value->fullname . '-' . $value->uid ] = $value;
        }
        
        ksort( $cities );
        
        return array_values( $cities );
    }
    
    /**
     * 
     */
    public function indexAction()
    {
        if( $this->getPiBase()->conf[ 'altLayout' ] ) {
            
            $layout  = new tx_marvinaddresses_LayoutAlt();
            $country = new tx_marvinaddresses_View_CountryAlt( $this->getPiBase(), $this->_getCountries() );
            
        } else {
            
            $layout  = new tx_marvinaddresses_Layout();
            $country = new tx_marvinaddresses_View_Country( $this->getPiBase(), $this->_getCountries() );
            $map     = new tx_marvinaddresses_View_Map( $this->getPiBase() );
            
            $layout->getMap()->addChildNode( $map->getContent() );
        }
        
        $layout->getCountry()->addChildNode( $country->getContent() );;
        
        $intro            = $layout->getAddress()->div;
        $intro[ 'class' ] = 'tx-marvinaddresses-pi1-introText';
        
        $intro->addTextData( nl2br( $this->getPiBase()->conf[ 'introText' ] ) );
        
        return $layout;
    }
    
    /**
     * 
     */
    public function countryAction()
    {
        $id = ( int )$this->getPiBase()->piVars[ 'country' ];
        
        if( $id === 0 ) {
            
            return;
        }
        
        try {
            
            new tx_oop_Database_Object( self::TABLE_COUNTRIES, $id );
            
            if( $this->getPiBase()->conf[ 'altLayout' ] ) {
                
                $layout  = new tx_marvinaddresses_LayoutAlt();
                $country = new tx_marvinaddresses_View_CountryAlt( $this->getPiBase(), $this->_getCountries() );
                
            } else {
                
                $layout  = new tx_marvinaddresses_Layout();
                $country = new tx_marvinaddresses_View_Country( $this->getPiBase(), $this->_getCountries() );
                $cities  = $this->_getCities( $id );
                $city    = new tx_marvinaddresses_View_City( $this->getPiBase(), $cities );
                $map     = new tx_marvinaddresses_View_Map( $this->getPiBase(), $cities );
                
                $layout->getCity()->addChildNode( $city->getContent() );
                $layout->getMap()->addChildNode( $map->getContent() );
            }
            
            $address = new tx_marvinaddresses_View_Address( $this->getPiBase(), $this->_getAddressesByCountry( $id ) );
            
            $layout->getCountry()->addChildNode( $country->getContent() );
            $layout->getAddress()->addChildNode( $address->getContent() );
            
            return $layout;
            
        } catch( Exception $e ) {
            
            return;
        }
    }
    
    /**
     * 
     */
    public function cityAction()
    {
        $id = ( int )$this->getPiBase()->piVars[ 'city' ];
        
        if( $id === 0 ) {
            
            return;
        }
        
        try {
            
            $cityObject = new tx_oop_Database_Object( self::TABLE_CITIES, $id );
            
            $this->getPiBase()->piVars[ 'country' ] = $cityObject->id_country;
            
            $layout  = new tx_marvinaddresses_Layout();
            $country = new tx_marvinaddresses_View_Country( $this->getPiBase(), $this->_getCountries() );
            $cities  = $this->_getCities( $cityObject->id_country );
            $city    = new tx_marvinaddresses_View_City( $this->getPiBase(), $cities );
            $map     = new tx_marvinaddresses_View_Map( $this->getPiBase(), array( $cityObject ) );
            $address = new tx_marvinaddresses_View_Address( $this->getPiBase(), $this->_getAddressesByCity( $id ) );
            
            $layout->getCountry()->addChildNode( $country->getContent() );
            $layout->getCity()->addChildNode( $city->getContent() );
            $layout->getMap()->addChildNode( $map->getContent() );
            $layout->getAddress()->addChildNode( $address->getContent() );
            
            return $layout;
            
        } catch( Exception $e ) {
            
            return;
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/Controller/Address.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/Controller/Address.class.php']);
}
