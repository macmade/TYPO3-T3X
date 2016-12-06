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

# $Id: MobileCatalog.class.php 195 2010-01-27 08:57:11Z macmade $

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
class tx_marvincatalog_Controller_MobileCatalog extends tx_netfw_Controller
{
    protected $_categories = NULL;
    protected $_catalog    = NULL;
    protected $_plugin     = NULL;
    protected $_category   = 0;
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_categories = tx_marvincatalog_Magento_Category_Helper::getInstance();
        $this->_catalog    = tx_marvincatalog_Magento_Catalog_Helper::getInstance();
        $this->_plugin     = $this->getPiBase();
        $this->_category   = $this->_plugin->conf[ 'category' ];
    }
    
    /**
     * 
     */
    public function indexAction()
    {
        $view = new tx_marvincatalog_View_MobileCollection( $this->_plugin, $this->_categories->getRootCategories() );
        
        return $view->getContent();
    }
    
    /**
     * 
     */
    public function categoryAction()
    {
        if( !isset( $this->_plugin->piVars[ 'category' ] ) ) {
            
            return NULL;
        }
        
        $category = $this->_categories->getCategoryByKey( $this->_plugin->piVars[ 'category' ] );
        
        if( !is_object( $category ) ) {
            
            return NULL;
        }
        
        $view = new tx_marvincatalog_View_MobileCategory( $this->_plugin, $category );
        
        return $view->getContent();
    }
    
    /**
     * 
     */
    public function watchAction()
    {
        if( !isset( $this->_plugin->piVars[ 'watch' ] ) ) {
            
            return NULL;
        }
        
        $watch = $this->_catalog->getWatchByKey( $this->_plugin->piVars[ 'watch' ] );
        
        if( !is_object( $watch ) ) {
            
            return NULL;
        }
        
        $view = new tx_marvincatalog_View_MobileWatch( $this->_plugin, $watch );
        
        return $view->getContent();
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Controller/Catalog.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Controller/Catalog.class.php']);
}
