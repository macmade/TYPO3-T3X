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

# $Id: ArticleController.php 24 2010-06-21 07:53:48Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Article controller
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  alcoopedia
 */
class Tx_Alcoopedia_Controller_ArticleController extends Tx_NetExtbase_MVC_Controller_ActionController
{
    protected $_categoryRepository  = NULL;
    protected $_articleRepository   = NULL;
    protected $_wordRepository      = NULL;
    
    protected function _globalAssigns()
    {
        $tags    = array();
        $words   = $this->_wordRepository->findMostSearched();
        $count   = 0;
        $percent = 300;
        
        foreach( $words as $word )
        {
            $count    = $word->getCount();
            $percent -= 10;
            
            $tags[] = array
            (
                'word'      => $word->getWord(),
                'fontSize'  => $percent . '%'
            );
        }
        
        shuffle( $tags );
        
        $this->view->assign( 'mostViewed', $this->_articleRepository->findMostViewed() );
        $this->view->assign( 'tags',       $tags );
        $this->view->assign( 'scores',     $scores );
        
        
    }
    
    public function initializeAction()
    {
        $this->_categoryRepository = t3lib_div::makeInstance( 'Tx_Alcoopedia_Domain_Repository_CategoryRepository' );
        $this->_articleRepository  = t3lib_div::makeInstance( 'Tx_Alcoopedia_Domain_Repository_ArticleRepository' );
        $this->_wordRepository     = t3lib_div::makeInstance( 'Tx_Alcoopedia_Domain_Repository_WordRepository' );
    }
    
    /**
     * @default
     * @nocache
     */
    public function indexAction()
    {
        $this->view->assign( 'categories', $this->_categoryRepository->findAll() );
        $this->_globalAssigns();
    }
    
    /**
     * @param   Tx_Alcoopedia_Domain_Model_Category $category
     * @param   Tx_Alcoopedia_Domain_Model_Article  $article
     * @nocache
     */
    public function viewAction( Tx_Alcoopedia_Domain_Model_Category $category, Tx_Alcoopedia_Domain_Model_Article $article )
    {
        $this->view->assign( 'category',   $category );
        $this->view->assign( 'article',    $article );
        $this->_globalAssigns();
        
        $article->setViews( $article->getViews() + 1 );
    }
    
    /**
     * @param   array       $search
     * @param   boolean     $fromTagCloud
     * @nocache
     */
    public function searchAction( array $search, $fromTagCloud = false )
    {
        if( !isset( $search[ 'word' ] ) || !$search[ 'word' ] )
        {
            $this->forward( 'index', 'Article' );
        }
        
        $words   = t3lib_div::trimExplode( ' ', $search[ 'word' ] );
        $results = $this->_articleRepository->findByWords( $words );
        
        if( count( $results ) )
        {
            foreach( $words as $searchWord )
            {
                if( $word = $this->_wordRepository->findOneByWord( $searchWord ) )
                {
                    if( $fromTagCloud == false )
                    {
                        $word->setCount( $word->getCount() + 1 );
                    }
                }
                else
                {
                    $word = t3lib_div::makeInstance( 'Tx_Alcoopedia_Domain_Model_Word' );
                    
                    $word->setWord( $searchWord );
                    $word->setCount( 1 );
                    
                    $this->_wordRepository->add( $word );
                }
            }
        }
        
        $this->view->assign( 'searchWord', $search[ 'word' ] );
        $this->view->assign( 'articles',   $results );
        $this->_globalAssigns();
    }
    
    /**
     * @param   string  $tag
     * @nocache
     */
    public function tagAction( $tag )
    {
        $this->forward( 'search', 'Article', NULL, array( 'search' => array( 'word' => $tag ), 'fromTagCloud' => true ) );
    }
}
