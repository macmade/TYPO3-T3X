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

# $Id: HomeController.php 2 2010-06-21 07:43:04Z macmade $

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
class Tx_CpGuide_Controller_HomeController extends Tx_NetExtbase_MVC_Controller_ActionController
{
    protected $_categoryRepository    = NULL;
    protected $_subCategoryRepository = NULL;
    protected $_articleRepository     = NULL;
    protected $_commentRepository     = NULL;
    protected $_tagRepository         = NULL;
    protected $_noteRepository        = NULL;
    protected $_userRepository        = NULL;
    
    public function initializeAction()
    {
        $this->_categoryRepository    = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_CategoryRepository' );
        $this->_subCategoryRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_SubCategoryRepository' );
        $this->_articleRepository     = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_ArticleRepository' );
        $this->_commentRepository     = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_CommentRepository' );
        $this->_tagRepository         = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_TagRepository' );
        $this->_noteRepository        = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_NoteRepository' );
        $this->_userRepository        = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_UserRepository' );
    }
    
    /**
     * @default
     */
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
}
