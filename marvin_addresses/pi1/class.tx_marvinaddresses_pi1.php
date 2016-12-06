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

# $Id: class.tx_marvinaddresses_pi1.php 182 2010-01-04 12:35:37Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Frontend plugin PI1 for the 'marvin_addresses' extension.
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_addresses
 */
class tx_marvinaddresses_pi1 extends tx_oop_Plugin_Base
{
    protected $_category   = 0;
    protected $_dispatcher = NULL;
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_dispatcher = tx_netfw_Dispatcher::getInstance();
        
        $this->_dispatcher->setRouteDefault( 'index', 'address' );
        $this->_dispatcher->setControllerPath( t3lib_extMgm::extPath( $this->extKey ). 'classes' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR );
    }
    
    /**
     * 
     */
    protected function _getPluginContent( tx_oop_Xhtml_Tag $content )
    {
        $this->_jQuerySetup();
        $this->_dispatcher->setPiBase( $this );
        
        $this->_dispatcher->prefixControllerClassName = 'tx_marvinaddresses';
        
        $this->conf[ 'storage' ]     = ( int )$this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'storage', 'sDEF' );
        $this->conf[ 'addressType' ] = ( int )$this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'addressType', 'sDEF' );
        $this->conf[ 'altLayout' ]   = ( bool )$this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'altLayout', 'sDEF' );
        $this->conf[ 'introText' ]   = ( string )$this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'intro', 'sTEXT' );
        
        $action = ( isset( $this->piVars[ 'action' ] ) ) ? $this->piVars[ 'action' ] : 'address-index';
        $data   = $this->_dispatcher->dispatch( $action );
        
        if( is_object( $data ) && $data instanceof tx_oop_Xhtml_Tag ) {
            
            $content->addChildNode( $data );
            
        } else {
            
            $content->addTextData( ( string )$data );
        }
    }
    
    protected function _jQuerySetup()
    {
        $this->_includeJQuery();
        $this->_includeJQueryUi();
        
        $script      = new tx_oop_Xhtml_Tag( 'script' );
        $linkTheme   = new tx_oop_Xhtml_Tag( 'link' );
        $link        = new tx_oop_Xhtml_Tag( 'link' );
        
        $linkTheme[ 'rel' ]       = 'stylesheet';
        $linkTheme[ 'href' ]      = t3lib_extMgm::siteRelPath( $this->extKey ) . 'res/css/black-tie/jquery-ui-1.7.2.custom.css';
        $linkTheme[ 'media' ]     = 'screen';
        $linkTheme[ 'charset' ]   = 'utf-8';
        $link[ 'rel' ]            = 'stylesheet';
        $link[ 'href' ]           = t3lib_extMgm::siteRelPath( $this->extKey ) . 'res/css/ui.selectmenu.css';
        $link[ 'media' ]          = 'screen';
        $link[ 'charset' ]        = 'utf-8';
        $script[ 'type' ]         = 'text/javascript';
        $script[ 'src' ]          = t3lib_extMgm::siteRelPath( $this->extKey ) . 'res/js/ui.selectmenu.js';
        $script[ 'charset' ]      = 'utf-8';
        
        self::$_tsfe->additionalHeaderData[ __CLASS__ . '_cssTheme' ] = ( string )$linkTheme;
        self::$_tsfe->additionalHeaderData[ __CLASS__ . '_css' ]      = ( string )$link;
        self::$_tsfe->additionalHeaderData[ __CLASS__ . '_js' ]       = ( string )$script;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/pi1/class.tx_marvinaddresses_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_addresses/pi1/class.tx_marvinaddresses_pi1.php']);
}
