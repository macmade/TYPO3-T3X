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

# $Id: NoteController.php 2 2010-06-21 07:43:04Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Note controller
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Controller_NoteController extends Tx_NetExtbase_MVC_Controller_ActionController
{
    protected $_articleRepository = NULL;
    protected $_noteRepository    = NULL;
    protected $_userRepository    = NULL;
    
    public function initializeAction()
    {
        $this->_articleRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_ArticleRepository' );
        $this->_noteRepository    = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_NoteRepository' );
        $this->_userRepository    = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_UserRepository' );
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
     * @param   integer                         $articleId
     * @param   Tx_CpGuide_Domain_Model_Note    $newNote
     * @return  void
     * @nocache
     */
    public function createAction( $articleId, Tx_CpGuide_Domain_Model_Note $newNote )
    {
        $article = $this->_articleRepository->findByUid( ( int )$articleId, false );
        $author  = $this->_userRepository->findByFrontendUser( Tx_NetExtbase_Utility_Frontend::getLoginUser() );
        
        if( !$article || !$author )
        {
            $this->_redirect( 'index', 'Guide' );
        }
        
        $newNote->setAuthor( $author );
        $newNote->setPid( $article->getPid() );
        $article->addNote( $newNote );
        $this->_clearPageCache();
        $this->redirect( 'article', 'Guide', NULL, array( 'article' => $articleId ) );
    }
    
    /**
     * 
     * @param   integer     $articleId
     * @param   integer     $noteId
     * @param   boolean     $redirectHome
     * @return  void
     * @nocache
     */
    public function deleteAction( $articleId, $noteId, $redirectHome = false )
    {
        $article = $this->_articleRepository->findByUid( ( int )$articleId, false );
        $note    = $this->_noteRepository->findByUid( ( int )$noteId, false );
        
        $article->removeNote( $note );
        $this->_noteRepository->remove( $note );
        
        if( ( bool )$redirectHome === true || $GLOBALS[ 'TSFE' ]->id == $this->settings[ 'displayPid' ] )
        {
            $this->redirect( 'home', 'Home' );
        }
        else
        {
            $this->redirect( 'article', 'Guide', NULL, array( 'article' => $articleId ) );
        }
    }
}
