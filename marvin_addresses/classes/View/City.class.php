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

# $Id: City.class.php 165 2009-12-15 13:44:10Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * City view
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_addresses
 */
class tx_marvinaddresses_View_City extends tx_marvinaddresses_View_Base
{
    const TABLE_ADDRESSES = 'tx_marvinaddresses_addresses';
    
    protected $_cities = array();
    
    public function __construct( tslib_piBase $plugin, array $cities )
    {
        parent::__construct( $plugin );
        
        $this->_cities = $cities;
        
        $this->_countriesMenu( $this->_content );
    }
    
    protected function _countriesMenu( tx_oop_Xhtml_Tag $container )
    {
        $select               = $container->select;
        $select[ 'id' ]       = $this->_plugin->prefixId . '_citiesMenu';
        $select[ 'name' ]     = $this->_plugin->prefixId . '_citiesMenu';
        $select[ 'onchange' ] = 'location.href = $( this ).val()';
        
        $id = ( int )$this->_plugin->piVars[ 'city' ];
        
        if( $id === 0 ) {
            
            $item = $select->option;
            $item->addTextData( $this->_lang->selectCity );
        }
        
        foreach( $this->_cities as $city ) {
            
            $item            = $select->option;
            $item[ 'value' ] = $this->_link( array( 'action' => 'address-city', 'city' => $city->uid ) );
            
            $type = ( int )$this->_plugin->conf[ 'addressType' ];
            $sql  = 'SELECT COUNT( * ) FROM '
                  . self::TABLE_ADDRESSES
                  . ' WHERE deleted = 0 AND id_city = '
                  . $city->uid;
        
            if( $type === 0 ) {
                
                $sql .= ' AND address_type = 1';
                
            } elseif( $type === 1 ) {
                
                $sql .= ' AND address_type = 2';
                
            } elseif( $type === 2 ) {
                
                $sql .= ' AND sav = 1';
            }
            
            $query = self::$_db->query( $sql );
            $count = $query->fetchColumn();
            
            if( $city->uid == $id ) {
                
                $item[ 'selected' ] = 'selected';
            }
            
            $item->addTextData( $city->fullname . ' (' . $count . ')' );
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
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/City.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/classes/View/City.class.php']);
}
