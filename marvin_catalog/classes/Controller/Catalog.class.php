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

# $Id: Catalog.class.php 180 2010-01-04 12:33:28Z macmade $

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
class tx_marvincatalog_Controller_Catalog extends tx_netfw_Controller
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
        $id = $this->_category;
        
        if( isset( $this->getPiBase()->piVars[ 'collection' ] ) ) {
            
            $key = $this->getPiBase()->piVars[ 'collection' ];
            
            if( $col = $this->_categories->getCategoryByKey( $key ) ) {
                
                $id = $col->id;
            }
        }
        
        $content    = new tx_oop_Xhtml_Tag( 'div' );
        $collection = new tx_marvincatalog_View_Collection(
            $this->_plugin,
            $this->_categories->getRootCategories(),
            $id
        );
        
        $content->addTextData( $collection );
        
        return $content;
    }
    
    public function categoryAction()
    {
        $content  = new tx_oop_Xhtml_Tag( 'div' );
        $category = new tx_marvincatalog_View_Category(
            $this->_plugin,
            $this->_categories->getCategoryByKey( $this->_plugin->piVars[ 'category' ] )
        );
        
        $content->addTextData( $category );
        
        return $content;
    }
    
    public function watchAction()
    {
        $content  = new tx_oop_Xhtml_Tag( 'div' );
        $watch    = new tx_marvincatalog_View_Watch(
            $this->_plugin,
            $this->_catalog->getWatchByKey( $this->_plugin->piVars[ 'watch' ] )
        );
        
        $content->addTextData( $watch );
        
        return $content;
    }
    
    public function sendEmailAction()
    {
        $content  = new tx_oop_Xhtml_Tag( 'div' );
        $email    = new tx_marvincatalog_View_SendEmail(
            $this->_plugin,
            $this->_catalog->getWatchByKey( $this->_plugin->piVars[ 'watch' ] )
        );
        
        $content->addTextData( $email );
        
        return $content;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Controller/Catalog.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Controller/Catalog.class.php']);
}
