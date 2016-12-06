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

# $Id: MobileWatch.class.php 195 2010-01-27 08:57:11Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Watch view
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_View_MobileWatch extends tx_marvincatalog_View_Base
{
    protected $_watch      = NULL;
    protected $_categories = NULL;
    
    public function __construct( tslib_piBase $plugin, $watch )
    {
        parent::__construct( $plugin );
        
        $this->_categories = tx_marvincatalog_Magento_Category_Helper::getInstance();
        
        if( isset( $this->_plugin->piVars[ 'category' ] ) ) {
            
            $category = $this->_categories->getCategoryByKey( $this->_plugin->piVars[ 'category' ] );
            
            if( is_object( $category ) ) {
                
                $title = $this->_content->h3;
                $link1 = $title->a;
                
                $title->addTextData( ' &gt; ' );
                
                $link2 = $title->a;
                
                $link1->addTextData( $this->_lang->collections );
                $link2->addTextData( $category->name );
                
                $link1[ 'href' ] = $this->_link( array() );
                $link2[ 'href' ] = $this->_link( array( 'action' => 'mobileCatalog-category', 'category' => $this->_plugin->piVars[ 'category' ] ) );
            }
        }
        
        $this->_watch       = $watch;
        $this->_content->h2 = $this->_watch->name;
        $details            = $this->_content->div;
        $magentoUrl         = $this->_extConf[ 'magento_url' ];
        $imgPath            = $magentoUrl . 'media/catalog/product' . $this->_watch->image;
        $img                = $details->div->img;
        $img[ 'alt' ]       = $this->_watch->sku;
        $img[ 'title' ]     = $this->_watch->name;
        $img[ 'src' ]       = $imgPath;
        $img[ 'width' ]     = $this->_plugin->conf[ 'bigImgWidth' ];
        
        $this->_cssClass( 'details', $details );
        
        $details->h3  = $this->_lang->technical;
        $infos        = $details->div;
        $this->_cssClass( 'infosTable', $infos );
        
        $movement = $infos->div;
        $features = $infos->div;
        $case     = $infos->div;
        $glass    = $infos->div;
        $atm      = $infos->div;
        $bracelet = $infos->div;
        $dial     = $infos->div;
        
        $movementLabel = $movement->div;
        $featuresLabel = $features->div;
        $caseLabel     = $case->div;
        $glassLabel    = $glass->div;
        $atmLabel      = $atm->div;
        $braceletLabel = $bracelet->div;
        $dialLabel     = $dial->div;
        
        $this->_cssClass( 'label', $movementLabel );
        $this->_cssClass( 'label', $featuresLabel );
        $this->_cssClass( 'label', $caseLabel );
        $this->_cssClass( 'label', $glassLabel );
        $this->_cssClass( 'label', $atmLabel );
        $this->_cssClass( 'label', $braceletLabel );
        $this->_cssClass( 'label', $dialLabel );
        
        $movementLabel->addTextData( $this->_lang->movement );
        $featuresLabel->addTextData( $this->_lang->features );
        $caseLabel->addTextData( $this->_lang->getLabel( 'case' ) );
        $glassLabel->addTextData( $this->_lang->glass );
        $atmLabel->addTextData( $this->_lang->atm );
        $braceletLabel->addTextData( $this->_lang->bracelet );
        $dialLabel->addTextData( $this->_lang->dial );
        
        $movement->div = $this->_watch->movement;
        $features->div = $this->_watch->display;
        $case->div = $this->_watch->watchCase;
        $glass->div = $this->_watch->glass;
        $atm->div = $this->_watch->atm;
        $bracelet->div = $this->_watch->bracelet;
        $dial->div = $this->_watch->dial;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/MobileWatch.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/MobileWatch.class.php']);
}
