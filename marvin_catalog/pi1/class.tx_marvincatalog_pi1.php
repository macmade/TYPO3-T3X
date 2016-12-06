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

# $Id: class.tx_marvincatalog_pi1.php 190 2010-01-06 11:16:55Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Frontend plugin PI1 for the 'marvin_catalog' extension.
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_pi1 extends tx_oop_Plugin_Base
{
    protected $_category   = 0;
    protected $_dispatcher = NULL;
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_includeJQuery();
        
        $slider              = new tx_oop_Xhtml_Tag( 'script' );
        $slider[ 'type' ]    = 'text/javascript';
        $slider[ 'src' ]     = t3lib_extMgm::siteRelPath( $this->extKey ) . 'res/js/easyslider/js/easySlider1.7.js';
        $slider[ 'charset' ] = 'utf-8';
        
        $lightBox              = new tx_oop_Xhtml_Tag( 'script' );
        $lightBox[ 'type' ]    = 'text/javascript';
        $lightBox[ 'src' ]     = t3lib_extMgm::siteRelPath( $this->extKey ) . 'res/js/jquery-fancybox/fancybox/jquery.fancybox-1.2.6.pack.js';
        $lightBox[ 'charset' ] = 'utf-8';
        
        $lightBoxStyles              = new tx_oop_Xhtml_Tag( 'link' );
        $lightBoxStyles[ 'type' ]    = 'text/css';
        $lightBoxStyles[ 'rel' ]     = 'stylesheet';
        $lightBoxStyles[ 'media' ]   = 'screen';
        $lightBoxStyles[ 'href' ]    = t3lib_extMgm::siteRelPath( $this->extKey ) . 'res/js/jquery-fancybox/fancybox/jquery.fancybox-1.2.6.css';
        $lightBoxStyles[ 'charset' ] = 'utf-8';
        
        self::$_tsfe->additionalHeaderData[ $this->prefixId . '_slider' ]       = ( string )$slider;
        self::$_tsfe->additionalHeaderData[ $this->prefixId . '_lightbox' ]     = ( string )$lightBox;
        self::$_tsfe->additionalHeaderData[ $this->prefixId . '_lightbox_css' ] = ( string )$lightBoxStyles;
        
        $this->_dispatcher = tx_netfw_Dispatcher::getInstance();
        
        $this->_dispatcher->setRouteDefault( 'index', 'catalog' );
        $this->_dispatcher->setControllerPath( t3lib_extMgm::extPath( $this->extKey ). 'classes' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR );
    }
    
    /**
     * 
     */
    protected function _getPluginContent( tx_oop_Xhtml_Tag $content )
    {
        $this->_dispatcher->setPiBase( $this );
        
        $this->_dispatcher->prefixControllerClassName = 'tx_marvincatalog';
        
        $this->conf[ 'category' ]           = ( int )$this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'rootCategory', 'sDEF' );
        $this->conf[ 'disableZoom' ]        = ( bool )$this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'disableZoom', 'sDEF' );
        $this->conf[ 'bitly.' ]             = array();
        $this->conf[ 'bitly.' ][ 'login' ]  = $this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'login',   'sBITLY' );
        $this->conf[ 'bitly.' ][ 'apiKey' ] = $this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'apiKey',  'sBITLY' );
        $this->conf[ 'mail.' ]              = array();
        $this->conf[ 'mail.' ][ 'subject' ] = $this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'subject', 'sMAIL' );
        $this->conf[ 'mail.' ][ 'message' ] = $this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'message', 'sMAIL' );
        
        $action = ( isset( $this->piVars[ 'action' ] ) ) ? $this->piVars[ 'action' ] : 'catalog-index';
        $data   = $this->_dispatcher->dispatch( $action );
        
        if( is_object( $data ) && $data instanceof tx_oop_Xhtml_Tag ) {
            
            $content->addChildNode( $data );
            
        } else {
            
            $content->addTextData( ( string )$data );
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/pi1/class.tx_marvincatalog_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/pi1/class.tx_marvincatalog_pi1.php']);
}
