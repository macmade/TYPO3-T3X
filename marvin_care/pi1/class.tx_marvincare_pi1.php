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

# $Id: class.tx_marvincare_pi1.php 183 2010-01-04 12:36:28Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Frontend plugin PI1 for the 'marvin_care' extension.
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_care
 */
class tx_marvincare_pi1 extends tx_oop_Plugin_Base
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
        
        $this->_dispatcher->setRouteDefault( 'index', 'manual' );
        $this->_dispatcher->setControllerPath( t3lib_extMgm::extPath( $this->extKey ). 'classes' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR );
    }
    
    /**
     * 
     */
    protected function _getPluginContent( tx_oop_Xhtml_Tag $content )
    {
        $this->_dispatcher->setPiBase( $this );
        
        $this->_dispatcher->prefixControllerClassName = 'tx_marvincare';
        
        $this->conf[ 'storage' ] = ( int )$this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'storage', 'sDEF' );
        $this->conf[ 'intro' ]   = ( string )$this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'intro', 'sTEXT' );
        
        $action = ( isset( $this->piVars[ 'action' ] ) ) ? $this->piVars[ 'action' ] : 'manual-index';
        $data   = $this->_dispatcher->dispatch( $action );
        
        if( is_object( $data ) && $data instanceof tx_oop_Xhtml_Tag ) {
            
            $content->addChildNode( $data );
            
        } else {
            
            $content->addTextData( ( string )$data );
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/pi1/class.tx_marvincare_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_care/pi1/class.tx_marvincare_pi1.php']);
}
