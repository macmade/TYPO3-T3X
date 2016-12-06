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

# $Id: Country.class.php 165 2009-12-15 13:44:10Z macmade $

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
class tx_marvinaddresses_View_Country extends tx_marvinaddresses_View_Base
{
    const TABLE_CITIES    = 'tx_marvindata_cities';
    const TABLE_ADDRESSES = 'tx_marvinaddresses_addresses';
    
    protected $_countries = array();
    
    public function __construct( tslib_piBase $plugin, array $countries )
    {
        parent::__construct( $plugin );
        
        $this->_countries = $countries;
        
        $this->_countriesMenu( $this->_content );
    }
    
    protected function _countriesMenu( tx_oop_Xhtml_Tag $container )
    {
        $select               = $container->select;
        $select[ 'id' ]       = $this->_plugin->prefixId . '_countriesMenu';
        $select[ 'name' ]     = $this->_plugin->prefixId . '_countriesMenu';
        $select[ 'onchange' ] = 'location.href = $( this ).val()';
        
        $id = ( int )$this->_plugin->piVars[ 'country' ];
        
        if( $id === 0 ) {
            
            $item = $select->option;
            $item->addTextData( $this->_lang->selectCountry );
        }
        
        foreach( $this->_countries as $country ) {
            
            $item            = $select->option;
            $item[ 'value' ] = $this->_link( array( 'action' => 'address-country', 'country' => $country->uid ) );
            
            $type = ( int )$this->_plugin->conf[ 'addressType' ];
            $sql  = 'SELECT COUNT( * ) FROM '
                  . self::TABLE_CITIES . ' AS city,'
                  . self::TABLE_ADDRESSES . ' AS address'
                  . ' WHERE address.deleted = 0 AND address.id_city = city.uid'
                  . ' AND city.id_country = '
                  . $country->uid;
            
            if( $type === 0 ) {
                
                $sql .= ' AND address_type = 1';
                
            } elseif( $type === 1 ) {
                
                $sql .= ' AND address_type = 2';
                
            } elseif( $type === 2 ) {
                
                $sql .= ' AND sav = 1';
            }
            
            $query = self::$_db->query( $sql );
            $count = $query->fetchColumn();
            
            if( $country->uid == $id ) {
                
                $item[ 'selected' ] = 'selected';
            }
            
            $item->addTextData( $country->fullname . ' (' . $count . ')' );
        }
        
        $script           = new tx_oop_Xhtml_Tag( 'script' );
        $script[ 'type' ] = 'text/javascript';
        
        $script->addTextData(
            '
            // <![CDATA[
            
            $( document ).ready(
                function()
                { 
                    $( "#' . $select[ 'id' ] . '" ).selectmenu(
                        { 
                            style: "dropdown"
                        }
                    );
                }
            );
            
            // ]]>
            '
        );
        
        self::$_tsfe->additionalHeaderData[ __METHOD__ ] = ( string )$script;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/Country.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/Country.class.php']);
}
