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

# $Id: class.tx_carmainmenu_pi1.php 65 2010-06-21 08:57:48Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Frontend plugin PI1 for the 'car_mainmenu' extension.
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  car_mainmenu
 */
class tx_carmainmenu_pi1 extends tslib_piBase
{
    /**
     * The plugin's prefix
     */
    public $prefixId        = __CLASS__;
    
    /**
     * The path to this script relative to the extension dir
     */
    public $scriptRelPath   = 'pi1/class.tx_carmainmenu_pi1.php';
    
    /**
     * The extension key
     */
    public $extKey          = 'car_mainmenu';
    
    /**
     * CHash setting
     */
    public $pi_checkCHash   = true;
    
    /**
     * 
     */
    protected function _buildMenu( $side )
    {
        $conf  = $this->conf[ $side . '.' ];
        $pages = array_slice( explode( ',', $conf[ 'pidList' ] ), 0, $conf[ 'maxBigItems' ] + 2 );
        
        $menu = array(
            'special'   => 'list',
            'special.'  => array( 'value' => implode( ',', $pages ) ),
            '1'         => 'GMENU',
            '1.'        => array(
                'NO.' => array()
            )
        );
        
        $no           =& $menu[ '1.' ][ 'NO.' ];
        $no[ '10' ]   =  'TEXT';
        $no[ '10.' ]  =  array();
        $text         =& $no[ '10.' ];
        
        $text[ 'text.' ]     = array( 'field' => 'title' );
        $text[ 'fontFile' ]  = $conf[ 'font' ];
        $text[ 'align' ]     = 'center';
        $text[ 'fontColor' ] = $conf[ 'color' ];
        
        if( isset( $conf[ 'niceText' ] ) && $conf[ 'niceText' ] ) {
            
            $text[ 'niceText' ] = 1;
        }
        
        $images    = array();
        $offset    = array();
        $wrap      = array();
        $xy        = array();
        $fontSize  = array();
        $imagePath = t3lib_extMgm::siteRelPath( $this->extKey ) . 'buttons/' . $side;
        
        for( $i = 0; $i < $conf[ 'maxBigItems' ] + 2; $i++ ) {
            
            $spanStyle = ( $i < $conf[ 'maxBigItems' ] ) ? ' style="display: block;"' : '';
            $wrap[]    = '<span class="item-' . $i . '"' . $spanStyle . '> | </span>';
            
            if( $side == 'right' && $i == 0 ) {
                
                $images[] = $imagePath . '-special.gif';
                
            } elseif( $i == $conf[ 'maxBigItems' ] - 1 ) {
                
                $images[] = $imagePath . '-end-' . ( count( $pages ) - ( $i + 1 ) ) . '.gif';
                
            } elseif( $i == $conf[ 'maxBigItems' ] - 2 && count( $pages ) == $conf[ 'maxBigItems' ] - 1 ) {
                
                $images[] = $imagePath . '-end-0.gif';
                
            } elseif( $i == $conf[ 'maxBigItems' ] ) {
                
                $images[] = $imagePath . '-small-0.gif';
                
            } elseif( $i == $conf[ 'maxBigItems' ] + 1 ) {
                
                $images[] = $imagePath . '-small-1.gif';
                
            } else {
                
                $images[] = $imagePath . '.gif';
            }
            
            if( $i < $conf[ 'maxBigItems' ] ) {
                
                $xy[]       = $conf[ 'width' ] . ',' . $conf[ 'height' ];
                $fontSize[] = $conf[ 'fontSize' ];
                $offset[]   = $conf[ 'leftOffset' ] . ',' . ceil( ( $conf[ 'height' ] / 2 ) + ( $conf[ 'fontSize' ] / 2 ) );
                
            } else {
                
                $xy[]       = $conf[ 'smallWidth' ] . ',' . $conf[ 'smallHeight' ];
                $fontSize[] = $conf[ 'smallFontSize' ];
                $offset[]   = $conf[ 'smallLeftOffset' ] . ',' . ceil( ( $conf[ 'smallHeight' ] / 2 ) + ( $conf[ 'smallFontSize' ] / 2 ) + 2 );
            }
            
            if( $side === 'right' && $i === $conf[ 'maxBigItems' ] - 1 ) {
                
                $text[ 'fontColor' ] = $conf[ 'colorAct' ];
            }
        }
        
        if( isset($_GET['macmade']) )
        {
            t3lib_div::debug($images);
        }
        $no[ 'wrap' ]       = implode( '||', $wrap );
        $no[ 'XY' ]         = implode( '||', $xy );
        $text[ 'fontSize' ] = implode( '||', $fontSize );
        $text[ 'offset' ]   = implode( '||', $offset );
        $no[ '5' ]          = 'IMAGE';
        $no[ '5.' ]         = array( 'file' => implode( '||', $images ) );
        
        $imagesActive = array();
        
        foreach( $images as $image ) {
            
            $imagesActive[] = str_replace( $imagePath, t3lib_extMgm::siteRelPath( $this->extKey ) . 'buttons/active/' . $side, $image );
        }
        
        unset( $text );
        unset( $no );
        
        $act = $conf[ 'actType' ];
        
        $menu[ '1.' ][ $act ]                               = 1;
        $menu[ '1.' ][ $act . '.' ]                         = $menu[ '1.' ][ 'NO.' ];
        $menu[ '1.' ][ $act . '.' ][ '5.' ][ 'file' ]       = implode( '||', $imagesActive );
        $menu[ '1.' ][ $act . '.' ][ '10.' ][ 'fontColor' ] = $conf[ 'colorAct' ];
        
        return $menu;
    }
    
    /**
     * Returns the content of the plugin
     * 
     * This function initialises the plugin 'tx_carmainmenu_pi1', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param   string  The content object
     * @param   array   The TS setup
     * @return  string  The content of the plugin
     */
    public function main( $content, array $conf )
    {
        // Plugin shouldn't be cached
        $this->pi_USER_INT_obj = 1;
        
        // Stores the TS configuration
        $this->conf = $conf;
        
        // Plugin initialization
        $this->pi_setPiVarDefaults();
        
        $left  = $this->_buildMenu( 'left' );
        $right = $this->_buildMenu( 'right' );
        
        $content = '<div class="left">'
                 . $this->cObj->HMENU( $left )
                 . '</div>'
                 . '<div class="left">'
                 . $this->cObj->HMENU( $right )
                 . '</div>';
        
        // Returns the plugin's content
        return $this->pi_wrapInBaseClass( $content );
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/car_mainmenu/pi1/class.tx_carmainmenu_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/car_mainmenu/pi1/class.tx_carmainmenu_pi1.php']);
}
