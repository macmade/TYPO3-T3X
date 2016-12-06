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

# $Id: GuideController.php 17 2010-11-05 22:49:44Z jean $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Guide controller
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Controller_GuideController extends Tx_NetExtbase_MVC_Controller_ActionController
{
    protected $_categoryRepository    = NULL;
    protected $_subCategoryRepository = NULL;
    protected $_articleRepository     = NULL;
    protected $_commentRepository     = NULL;
    protected $_tagRepository         = NULL;
    protected $_noteRepository        = NULL;
    protected $_userRepository        = NULL;
    protected $_wordRepository        = NULL;
    
    protected function _getFirstArticle( array $articles )
    {
        foreach( $articles as $article )
        {
            if( $article->getArticleType() === Tx_CpGuide_Domain_Model_Article::TYPE_NORMAL )
            {
                return $article;
            }
        }
        
        return NULL;
    }
    
    public function initializeAction()
    {
        $this->_categoryRepository    = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_CategoryRepository' );
        $this->_subCategoryRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_SubCategoryRepository' );
        $this->_articleRepository     = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_ArticleRepository' );
        $this->_commentRepository     = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_CommentRepository' );
        $this->_tagRepository         = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_TagRepository' );
        $this->_noteRepository        = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_NoteRepository' );
        $this->_userRepository        = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_UserRepository' );
        $this->_wordRepository        = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_WordRepository' );
        $this->_allowedWordRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_AllowedWordRepository' );
    }
    
    /**
     * @default
     */
    public function indexAction()
    {
        $vars = t3lib_div::_GET( 'tx_cpguide_pi1' );
        
        if( isset( $vars[ 'controller' ] ) && $vars[ 'controller' ] !== 'Guide' )
        {
            $action     = $vars[ 'action' ];
            $controller = $vars[ 'controller' ];
            
            unset( $vars[ 'action' ] );
            unset( $vars[ 'controller' ] );
            
            $this->forward
            (
                $action,
                $controller,
                NULL,
                $vars
            );
        }
        
        $categories = $this->_categoryRepository->findAll();
        $user       = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        
        if( !count( $categories ) )
        {
            return;
        }
        
        $category = array_shift( $categories );
        
        if( $user === NULL )
        {
            $this->redirect( 'category', 'Guide', NULL, array( 'category' => $category ) );
        }
        else
        {
            $subCategories = $category->getSubCategories();
            $subCategory   = array_shift( $subCategories );
            
            if( $subCategory )
            {
                $articles = $this->_articleRepository->findBySubCategory( $subCategory );
                $article  = $this->_getFirstArticle( $articles );
                
                if( $article )
                {
                    $this->redirect( 'article', 'Guide', NULL, array( 'article' => $article->getUid() ) );
                }
            }
        }
    }
    
    /**
     * @param Tx_CpGuide_Domain_Model_Category  $category
     */
    public function categoryAction( Tx_CpGuide_Domain_Model_Category $category )
    {
        $user = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        
        if( $user !== NULL )
        {
            $subCategories = $category->getSubCategories();
            $subCategory   = array_shift( $subCategories );
            
            if( $subCategory )
            {
                $articles = $this->_articleRepository->findBySubCategory( $subCategory );
                $article  = $this->_getFirstArticle( $articles );
                
                if( $article )
                {
                    $this->redirect( 'article', 'Guide', NULL, array( 'article' => $article->getUid() ) );
                }
            }
        }
        
        $this->_setPageTitle( $category->getTitle(), true );
        $this->view->assign( 'category', $category );
        $this->view->assign( 'categories', $this->_categoryRepository->findAll() );
        $this->view->assign( 'user', Tx_NetExtbase_Utility_Frontend::getLoginUser() );
        $this->view->assign( 'autoCompleteUrl', t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . '?eID=Tx_CpGuide_Eid_AutoComplete' );
    }
    
    /**
     * @param integer $subCategory
     */
    public function subCategoryAction( $subCategory )
    {
        $subCategory = $this->_subCategoryRepository->findByUid( ( int )$subCategory, false );
        $user        = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        $articles    = $this->_articleRepository->findBySubCategory( $subCategory );
        
        if( !count( $articles ) )
        {
            return;
        }
        
        $article = $this->_getFirstArticle( $articles );
        
        if( !$user )
        {
            $this->redirect( 'login', 'Guide', NULL, array( 'article' => $article->getUid() ) );
        }
        
        $this->redirect( 'article', 'Guide', NULL, array( 'article' => $article->getUid() ) );
    }
    
    /**
     * @param integer $article
     * @param string  $searchWord
     */
    public function articleAction( $article, $searchWord = '' )
    {
        $article     = $this->_articleRepository->findByUid( ( int )$article, false );
        $subCategory = $article->getSubCategory();
        $category    = $subCategory->getCategory();
        $user        = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        
        if( !$user )
        {
            $this->redirect( 'login', 'Guide', NULL, array( 'article' => $article->getUid() ) );
        }
        
        $this->_setPageTitle( $category->getTitle() . ' - ' . $subCategory->getTitle() . ' - ' . $article->getTitle(), true );
        $this->view->assign( 'categories', $this->_categoryRepository->findAll() );
        $this->view->assign( 'category', $category );
        $this->view->assign( 'subCategory', $subCategory );
        $this->view->assign( 'article', $article );
        $this->view->assign( 'user', $user );
        $this->view->assign( 'autoCompleteUrl', t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . '?eID=Tx_CpGuide_Eid_AutoComplete' );
        $this->view->assign( 'searchWord', urldecode( $searchWord ) );
        $this->view->assign( 'adminUserId', $this->settings[ 'adminUserId' ] );
        
        $article->setViews( $article->getViews() + 1 );
    }
    
    /**
     * @param string    $section
     * @param string    $subSection
     */
    public function sectionNumberAction( $section, $subSection )
    {
        foreach( $this->_categoryRepository->findAll() as $category )
        {
            if( $category->getMenuNumber() === $section )
            {
                foreach( $category->getSubCategories() as $subCategory )
                {
                    if( $subCategory->getMenuNumber() === $subSection )
                    {
                        $this->redirect( 'subCategory', 'Guide', NULL, array( 'subCategory' => $subCategory->getUid() ) );
                    }
                }
                
                $this->redirect( 'category', 'Guide', NULL, array( 'category' => $category ) );
            }
        }
        
        $this->redirect( 'index', 'Guide' );
    }
    
    /**
     * @param   array   $search
     * @nocache
     */
    public function searchAction( array $search = array() )
    {
        $user         = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        $allowedWords = $this->_allowedWordRepository->findAll();
        $abbr         = array();
        
        foreach( $allowedWords as $allowedWord )
        {
            $abbr[ strtoupper( $allowedWord->getWord() ) ] = true;
        }
        
        if( !$user )
        {
            $this->redirect( 'index', 'Guide' );
        }
        
        if( isset( $search[ 'word' ] ) && $search[ 'word' ] )
        {
            $words = $this->_wordRepository->findByWord( $search[ 'word' ] );
            
            if( count( $words ) )
            {
                $word          = array_shift( $words );
                $articlesIndex = $word->getArticles();
            }
            
            $terms       = explode( ' ', $search[ 'word' ] );
            $searchTerms = array();
            
            foreach( $terms as $term )
            {
                if( strlen( $term ) <= 3 )
                {
                    $term = strtoupper( $term );
                    
                    if( !isset( $abbr[ $term ] ) )
                    {
                        continue;
                    }
                    else
                    {
                        $searchTerms[] = $term;
                    }
                }
                else
                {
                    $searchTerms[] = $term;
                }
            }
            
            $articlesSearch = $this->_articleRepository->findByWords( $searchTerms );
            $articles       = array();
            
            foreach( $articlesIndex as $article )
            {
                $articles[$article->getUid() ] = $article;
            }
            
            foreach( $articlesSearch as $article )
            {
                $articles[ $article->getUid() ] = $article;
            }
            
            if( count( $articles ) )
            {
                $this->flashMessages->add( sprintf( $this->_lang->searchResult, count( $articles ) ) );
            }
            else
            {
                $this->flashMessages->add( $this->_lang->searchNoResult );
            }
            
            $this->view->assign( 'articles', $articles );
        }
        else
        {
            $this->flashMessages->add( $this->_lang->searchNoWord );
        }
        
        $this->view->assign( 'categories', $this->_categoryRepository->findAll() );
        $this->view->assign( 'user', $user );
        $this->view->assign( 'searchWord', $search[ 'word' ] );
        $this->view->assign( 'autoCompleteUrl', t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . '?eID=Tx_CpGuide_Eid_AutoComplete' );
    }
    
    /**
     * @param integer   $article
     * @param array     $login
     */
    public function loginAction( $article = 0, array $login = array() )
    {
        $subCategory = NULL;
        $category    = NULL;
        $user        = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        
        if( $article > 0 )
        {
            $article     = $this->_articleRepository->findByUid( ( int )$article, false );
            $subCategory = $article->getSubCategory();
            $category    = $subCategory->getCategory();
        }
        
        if( isset( $login[ 'username' ] ) && isset( $login[ 'password' ] ) )
        {
            $_POST[ 'logintype' ] = 'login';
            $_POST[ 'user' ]      = $login[ 'username' ];
            $_POST[ 'pass' ]      = $login[ 'password' ];
            $_POST[ 'pid' ]       = $this->settings[ 'storagePid' ];
            
            $GLOBALS[ 'TSFE' ]->initFEuser();
            
            unset( $_POST[ 'logintype' ] );
            unset( $_POST[ 'user' ] );
            unset( $_POST[ 'pass' ] );
            unset( $_POST[ 'pid' ] );
            
            if( isset( $GLOBALS[ 'TSFE' ]->fe_user->user[ 'uid' ] ) )
            {
                if( $article )
                {
                    $this->redirect( 'article', 'Guide', NULL, array( 'article' => $article->getUid() ) );
                }
                else
                {
                    $this->redirect( 'index', 'Guide' );
                }
            }
            else
            {
                $this->flashMessages->add( $this->_lang->loginError );
            }
        }
        
        if( $user && $article )
        {
            $this->redirect( 'article', 'Guide', NULL, array( 'article' => $article->getUid() ) );
        }
        elseif( $user )
        {
            $this->redirect( 'index', 'Guide' );
        }
        
        if( $article )
        {
            $this->view->assign( 'category', $category );
            $this->view->assign( 'subCategory', $subCategory );
            $this->view->assign( 'article', $article );
        }
        
        $this->view->assign( 'categories', $this->_categoryRepository->findAll() );
        $this->view->assign( 'registerPid', $this->settings[ 'registerPid' ] );
    }
    
    public function homeAction()
    {
        $user      = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        $profile   = $this->_userRepository->findByFrontendUser( $user );
        $articles  = $this->_articleRepository->findAll();
        $favorites = array();
        $notes     = array();
        
        if( $user === NULL )
        {
            $this->redirect( 'index', 'Guide' );
        }
        
        $this->view->assign( 'user',         $user );
        $this->view->assign( 'profile',      $profile );
        $this->view->assign( 'lastModified', $this->_articleRepository->findLastModified() );
        $this->view->assign( 'mostViewed',   $this->_articleRepository->findMostViewed() );
        
        foreach( $articles as $article )
        {
            foreach( $article->getFavorites() as $favorite )
            {
                if( $favorite->getAuthor()->getUid() === $profile->getUid() )
                {
                    $favorites[] = $article;
                }
            }
            
            foreach( $article->getNotes() as $note )
            {
                if( $note->getAuthor()->getUid() === $profile->getUid() )
                {
                    $notes[] = array
                    (
                        'article' => $article,
                        'note'    => $note
                    );
                }
            }
        }
        
        $this->view->assign( 'favorites', $favorites );
        $this->view->assign( 'notes',     $notes );
    }
    
    /**
     * @param integer   $subCategory
     */
    public function showAllAction( $subCategory )
    {
        $subCategory = $this->_subCategoryRepository->findByUid( ( int )$subCategory, false );
        $category    = $subCategory->getCategory();
        $articles    = $this->_articleRepository->findBySubCategory( $subCategory );
        $user        = Tx_NetExtbase_Utility_Frontend::getLoginUser();
     
        if( !$user )
        {
            $this->redirect( 'login', 'Guide', NULL, array( 'article' => $article->getUid() ) );
        }
        $this->_setPageTitle( $category->getTitle() . ' - ' . $subCategory->getTitle(), true );
        $this->view->assign( 'categories', $this->_categoryRepository->findAll() );
        $this->view->assign( 'articles', $articles );
        $this->view->assign( 'category', $category );
        $this->view->assign( 'subCategory', $subCategory );
        $this->view->assign( 'user', $user );
        $this->view->assign( 'autoCompleteUrl', t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . '?eID=Tx_CpGuide_Eid_AutoComplete' );
        $this->view->assign( 'showAll', true );
    }
}
