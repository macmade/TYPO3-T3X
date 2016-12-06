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

# $Id: Helper.class.php 144 2009-12-09 13:18:27Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * TCA helper class
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_data
 */
class tx_marvindata_Tca_Helper
{
    const TABLE_COUNTRIES = 'tx_marvindata_countries';
    const TABLE_CITIES    = 'tx_marvindata_cities';
    
    /**
     * Gets the list of the cities (ordered and prefixed by the country name)
     * 
     * @param   array           The parameters array
     " @param   t3lib_TCEforms  The TCE forms object
     * @return  NULL
     */
    public function getCities( array &$params, t3lib_TCEforms $pObj )
    {
        $pid       = $params[ 'row' ][ 'pid' ];
        $countries = tx_oop_Database_Object::getObjectsByFields( self::TABLE_COUNTRIES, array( 'pid' => $pid ), 'fullname' );
        
        foreach( $countries as $countryId => $country ) {
            
            $cities = tx_oop_Database_Object::getObjectsByFields( self::TABLE_CITIES, array( 'pid' => $pid, 'id_country' => $countryId ), 'fullname' );
            
            foreach( $cities as $cityId => $city ) {
                
                $params[ 'items' ][] = array( $country->fullname . ': ' . $city->fullname, $cityId );
            }
        }
    }
    
    public function mapHelpImage( array &$params, t3lib_TCEforms $pObj )
    {
        $x   = ( int )$params[ 'row' ][ 'coord_x' ];
        $y   = ( int )$params[ 'row' ][ 'coord_y' ] - 5;
        $img = t3lib_extMgm::extRelPath( 'marvin_data' ) . 'res/img/mapworld-help.gif';
        $dot = '<div style="color: #FF0000; padding-left: ' . $x . 'px; padding-top: ' . $y . 'px;">x</div>';
        
        return '<div style="width: 498px; height: 309px; background-image: url( \'' . $img . '\' );">' . $dot . '</div>';
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_data/classes/Tca/Helper.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_data/classes/Tca/Helper.class.php']);
}
