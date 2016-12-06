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

# $Id: CommentController.php 2 2010-06-21 07:43:04Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Comment controller
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Controller_CommentController extends Tx_NetExtbase_MVC_Controller_ActionController
{
    protected $_articleRepository = NULL;
    protected $_userRepository    = NULL;
    protected $_commentRepository = NULL;
    
    public function initializeAction()
    {
        $this->_articleRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_ArticleRepository' );
        $this->_userRepository    = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_UserRepository' );
        $this->_commentRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_CommentRepository' );
    }
    
    /**
     * @default
     */
    public function voidAction()
    {
        $this->redirect( 'index', 'Guide' );
    }
    
    /**
     * 
     * @param   integer                             $articleId
     * @param   Tx_CpGuide_Domain_Model_Comment     $newComment
     * @return  void
     * @nocache
     */
    public function createAction( $articleId, Tx_CpGuide_Domain_Model_Comment $newComment )
    {
        $article = $this->_articleRepository->findByUid( ( int )$articleId, false );
        $author  = $this->_userRepository->findByFrontendUser( Tx_NetExtbase_Utility_Frontend::getLoginUser() );
        
        if( !$article || !$author )
        {
            $this->_redirect( 'index', 'Guide' );
        }
        
        $newComment->setAuthor( $author );
        $newComment->setPid( $article->getPid() );
        $article->addComment( $newComment );
        $this->_clearPageCache();
        $this->redirect( 'article', 'Guide', NULL, array( 'article' => $articleId ) );
    }
    
    /**
     * 
     * @param   integer                             $article
     * @param   integer                             $comment
     * @return  void
     * @nocache
     */
    public function deleteAction( $article, $comment )
    {
        $article   = $this->_articleRepository->findByUid( ( int )$article, false );
        $comment   = $this->_commentRepository->findByUid( ( int )$comment, false );
        
        if( !$article || !$comment )
        {
            $this->redirect( 'index', 'Guide' );
        }
        
        $article->removeComment( $comment );
        $this->_commentRepository->remove( $comment );
        
        $this->redirect( 'article', 'Guide', NULL, array( 'article' => $article->getUid() ) );
    }
}
