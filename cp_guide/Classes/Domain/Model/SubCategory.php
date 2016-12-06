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

# $Id: SubCategory.php 2 2010-06-21 07:43:04Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * SubCategory model
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Domain_Model_SubCategory extends Tx_NetExtbase_DomainObject_AbstractEntity
{
    /**
     * @var string
     */
    protected $title                                = '';
    protected $_localizedTitle                      = '';
    protected $_category                            = NULL;
    protected $_alternativePageLanguageRepository   = NULL;
    protected $_articles                            = array();
    
    public function getMenuNumber()
    {
        $title = $this->getTitle();
        
        return substr( $title, 0, strpos( $title, '.' ) );
    }
    
    public function getMenuTitle()
    {
        $title = $this->getTitle();
        
        return substr( $title, strpos( $title, '.' ) + 1 );
    }
    
    public function getCategory()
    {
        if( !is_object( $this->_category ) ) {
            
            $repos           = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_CategoryRepository' );
            $this->_category = $repos->findByUid( $this->pid, false );
        }
        
        return $this->_category;
    }
    
    public function getArticles()
    {
        if( !count( $this->_articles ) ) {
            
            $repos           = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_ArticleRepository' );
            $this->_articles = $repos->findBySubCategory( $this );
        }
        
        return $this->_articles;
    }
    
    public function getTitle()
    {
        if( $this->_alternativePageLanguageRepository === NULL )
        {
            $this->_alternativePageLanguageRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_AlternativePageLanguageRepository' );
            $alternativePageLanguage                  = $this->_alternativePageLanguageRepository->findForPageId( $this->getUid() );
            
            if( $alternativePageLanguage !== NULL )
            {
                $this->_localizedTitle   = $alternativePageLanguage->getTitle();
            }
        }
        
        return ( $this->_localizedTitle ) ? $this->_localizedTitle : $this->title;
    }
}
