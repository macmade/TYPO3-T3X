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

# $Id: Base.class.php 182 2010-01-04 12:35:37Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * View abstract
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_addresses
 */
abstract class tx_marvinaddresses_View_Base
{
    private static $_hasStatic = false;
    protected static $_tsfe    = NULL;
    protected static $_db      = NULL;
    protected $_content        = NULL;
    protected $_plugin         = NULL;
    protected $_lang           = NULL;
    
    public function __construct( tslib_piBase $plugin )
    {
        if( self::$_hasStatic === false ) {
            
            self::_setStaticVars();
        }
        
        $this->_plugin  = $plugin;
        $this->_content = new tx_oop_Xhtml_Tag( 'div' );
        $this->_lang    = tx_oop_Lang_Getter::getInstance( 'EXT:' . $this->_plugin->extKey . '/lang/' . get_class( $this ) . '.xml' );
    }
    
    public function __toString()
    {
        return ( string )$this->_content;
    }
    
    private static function _setStaticVars()
    {
        self::$_tsfe      = $GLOBALS[ 'TSFE' ];
        self::$_db        = tx_oop_Database_Layer::getInstance();
        self::$_hasStatic = true;
    }
    
    public function getContent()
    {
        return $this->_content;
    }
    
    protected function _cssClass( $name, tx_oop_Xhtml_Tag $tag )
    {
        $tag[ 'class' ] = 'tx-marvinaddresses-pi1-' . $name;
    }
    
    protected function _link( array $params = array(), $cache = true, $id = 0 )
    {
        $id = ( $id === 0 ) ? self::$_tsfe->id : ( int )$id;
        
        $ts = array(
            'parameter' => $id
        );
        
        if( ( boolean )$cache === false ) {
            
            $ts[ 'no_cache' ] = 1;
            
        } else {
            
            $ts[ 'useCacheHash' ] = 1;
        }
        
        if( count( $params ) ) {
            
            $query = '';
            
            foreach( $params as $key => $value ) {
                
                $query .= '&' . $this->_plugin->prefixId . '[' . $key . ']' . '=' . $value;
            }
            
            $ts[ 'additionalParams' ] = $query;
        }
        
        return $this->_plugin->cObj->typolink_URL( $ts );
    }
    
    protected function _setPageTitle( $title, $replace = false, $separator = ' - ' )
    {
        $title     = ( string )$title;
        $separator = ( string )$separator;
        
        if( ( boolean )$replace == true ) {
            
            $GLOBALS[ 'TSFE' ]->page[ 'title' ] = $title;
	        $GLOBALS[ 'TSFE' ]->indexedDocTitle = $title;
	        
	    } else {
	        
            $GLOBALS[ 'TSFE' ]->page[ 'title' ] .= $separator . $title;
	        $GLOBALS[ 'TSFE' ]->indexedDocTitle .= $separator . $title;
	    }
    }
}
