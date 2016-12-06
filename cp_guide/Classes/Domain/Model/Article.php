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

# $Id: Article.php 7 2010-10-18 13:46:27Z jean $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Article model
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Domain_Model_Article extends Tx_NetExtbase_DomainObject_AbstractEntity
{
    const TYPE_NORMAL    = 0x00;
    const TYPE_SEPARATOR = 0x01;
    const TYPE_NOTE      = 0x02;
    
    /**
     * 
     */
    protected $_subCategory             = NULL;
    
    /**
     * 
     */
    protected $_subCategoryRepository   = NULL;
    
    /**
     * 
     */
    protected $_userRepository          = NULL;
    
    /**
     * 
     */
    protected $_noteRepository          = NULL;
    
    /**
     * 
     */
    protected $_commentRepository       = NULL;
    
    /**
     * 
     */
    protected $_favoriteRepository      = NULL;
    
    /**
     * Title
     * 
     * @var         integer
     */
    protected $articleType              = 0;
    
    /**
     * L18n parent
     * 
     * @var         integer
     */
    protected $l18nParent               = 0;
    
    /**
     * Language ID
     * 
     * @var         integer
     */
    protected $sysLanguageUid           = 0;
    
    /**
     * Title
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $title                    = '';
    
    /**
     * Title
     * 
     * @var         string
     */
    protected $pagenum                  = '';
    
    /**
     * Title
     * 
     * @var         DateTime
     */
    protected $changeDate               = '';
    
    /**
     * Title
     * 
     * @var         string
     */
    protected $changeDescription        = '';
    
    /**
     * Content
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $content                  = '';
    
    /**
     * Tags
     * 
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CpGuide_Domain_Model_Tag>
     */
    protected $tags                     = NULL;
    
    /**
     * Comments
     * 
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CpGuide_Domain_Model_Comment>
     */
    protected $comments                 = NULL;
    
    /**
     * Notes
     * 
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CpGuide_Domain_Model_Note>
     */
    protected $notes                    = NULL;
    
    /**
     * Notes
     * 
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CpGuide_Domain_Model_Favorite>
     */
    protected $favorites                = NULL;
    
    /**
     * Views
     * 
     * @var         integer
     */
    protected $views                    = 0;
    
    public function __construct()
    {
        $this->tags                   = new Tx_Extbase_Persistence_ObjectStorage();
        $this->comments               = new Tx_Extbase_Persistence_ObjectStorage();
        $this->notes                  = new Tx_Extbase_Persistence_ObjectStorage();
        $this->favorites              = new Tx_Extbase_Persistence_ObjectStorage();
        $this->_subCategoryRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_SubCategoryRepository' );
        $this->_userRepository        = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_UserRepository' );
        $this->_noteRepository        = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_NoteRepository' );
        $this->_commentRepository     = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_CommentRepository' );
        $this->_favoriteRepository    = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_FavoriteRepository' );
    }
    
    public function __wakeup()
    {
        $this->_subCategoryRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_SubCategoryRepository' );
        $this->_userRepository        = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_UserRepository' );
        $this->_noteRepository        = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_NoteRepository' );
        $this->_commentRepository     = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_CommentRepository' );
        $this->_favoriteRepository    = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_FavoriteRepository' );
    }
    
    public function getSubCategory()
    {
        if( !is_object( $this->_subCategory ) )
        {
            $this->_subCategory = $this->_subCategoryRepository->findByUid( $this->pid, false );
        }
        
        return $this->_subCategory;
    }
    
    public function getMenuNumber()
    {
        if( strstr( $this->title, '.' ) )
        {
            $num = ( int )substr( $this->title, 0, strpos( $this->title, '.' ) );
            
            if( $num > 0 )
            {
                return $num;
            }
        }
        
        return '';
    }
    
    public function getMenuTitle()
    {
        if( strstr( $this->title, '.' ) )
        {
            return substr( $this->title, strpos( $this->title, '.' ) + 1 );
        }
        
        return $this->title;
    }
    
    public function getUserNotes()
    {
        if( $this->getSysLanguageUid() !== 0 )
        {
            $allNotes = $this->_noteRepository->findByArticleId( $this->getL18nParent() );
        }
        else
        {
            $allNotes = $this->notes;
        }
        
        $author = $this->_userRepository->findByFrontendUser( Tx_NetExtbase_Utility_Frontend::getLoginUser() );
        $notes  = array();
        
        if( is_object( $author ) )
        {
            foreach( $allNotes as $note )
            {
                if( $note->getAuthor()->getUid() === $author->getUid() )
                {
                    $notes[] = $note;
                }
            }
        }
        
        return $notes;
    }
    
    public function getComments()
    {
        if( $this->getSysLanguageUid() !== 0 )
        {
            $comments = $this->_commentRepository->findByArticleId( $this->getL18nParent() );
        }
        else
        {
            $comments = $this->comments;
        }
        
        $array = array();
        
        foreach( $comments as $comment )
        {
            $array[] = $comment;
        }
        
        return array_reverse( $array );
    }
    
    public function addTag( Tx_CpGuide_Domain_Model_Tag $tag )
    {
        $this->tags->attach( $tag );
    }
    
    public function removeTag( Tx_CpGuide_Domain_Model_Tag $tag )
    {
        $this->tags->detach( $tag );
    }
    
    public function removeAllTags()
    {
        $this->tags = new Tx_Extbase_Persistence_ObjectStorage();
    }
    
    public function addComment( Tx_CpGuide_Domain_Model_Comment $comment )
    {
        $this->comments->attach( $comment );
    }
    
    public function removeComment( Tx_CpGuide_Domain_Model_Comment $comment )
    {
        $this->comments->detach( $comment );
    }
    
    public function removeAllComments()
    {
        $this->comments = new Tx_Extbase_Persistence_ObjectStorage();
    }
    
    public function addNote( Tx_CpGuide_Domain_Model_Note $note )
    {
        $this->notes->attach( $note );
    }
    
    public function removeNote( Tx_CpGuide_Domain_Model_Note $note )
    {
        $this->notes->detach( $note );
    }
    
    public function removeAllNotes()
    {
        $this->notes = new Tx_Extbase_Persistence_ObjectStorage();
    }
    
    public function addFavorite( Tx_CpGuide_Domain_Model_Favorite $favorite )
    {
        $this->favorites->attach( $favorite );
    }
    
    public function removeFavorite( Tx_CpGuide_Domain_Model_Favorite $favorite )
    {
        $this->favorites->detach( $favorite );
    }
    
    public function removeAllFavorites()
    {
        $this->favorites = new Tx_Extbase_Persistence_ObjectStorage();
    }
    
    public function getFavorites()
    {
        if( $this->getSysLanguageUid() !== 0 )
        {
            return $this->_favoriteRepository->findByArticleId( $this->getL18nParent() );
        }
        
        return $this->favorites;
    }
    
    public function getFavoriteStatus()
    {
        $favorites = $this->getFavorites();
        $user      = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        
        if( $user === NULL )
        {
            return false;
        }
        
        foreach( $favorites as $favorite )
        {
            if( $favorite->getAuthor()->getFeUser()->getUid() === $user->getUid() )
            {
                return true;
            }
        }
        
        return false;
    }
}
