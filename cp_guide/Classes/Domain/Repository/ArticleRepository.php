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

# $Id: ArticleRepository.php 7 2010-10-18 13:46:27Z jean $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Article repository
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Domain_Repository_ArticleRepository extends Tx_NetExtbase_Persistence_Repository
{
    public function remove( $article )
    {
        $article->removeAllTags();
        $article->removeAllComments();
        $article->removeAllNotes();
        
        parent::remove( $article );
    }
    
    public function findAll()
    {
        $query = $this->createQuery();
        
        $query->getQuerySettings()->setRespectStoragePage( false );
        
        return $query->execute();
    }
    
    public function findBySubCategory( Tx_CpGuide_Domain_Model_SubCategory $subCategory )
    {
        return $this->findAllByPid( $subCategory->getUid() );
    }
    
    public function findByWords( array $words )
    {
        $numWords   = count( $words );
        $queryAnd   = $this->createQuery();
        $queryOr    = $this->createQuery();
        
        $queryAnd->getQuerySettings()->setRespectStoragePage( false );
        $queryOr->getQuerySettings()->setRespectStoragePage( false );
        $queryAnd->getQuerySettings()->setRespectSysLanguage( false );
        $queryOr->getQuerySettings()->setRespectSysLanguage( false );
        
        $constraintAnd = NULL;
        $constraintOr  = NULL;
        
        if( $numWords === 0 )
        {
            return array();
        }
        
        foreach( $words as $word )
        {
            $wordConstraintAnd = $queryAnd->logicalOr
            (
                $queryAnd->like( 'title',   '%' . $word . '%' ),
                $queryAnd->like( 'content', '%' . $word . '%' )
            );
            $wordConstraintOr = $queryOr->logicalOr
            (
                $queryOr->like( 'title',   '%' . $word . '%' ),
                $queryOr->like( 'content', '%' . $word . '%' )
            );
            
            if( is_object( $constraintAnd ) && is_object( $constraintOr ) )
            {
                $constraintAnd = $queryAnd->logicalAnd
                (
                    $constraintAnd,
                    $wordConstraintAnd
                );
                $constraintOr = $queryOr->logicalOr
                (
                    $constraintOr,
                    $wordConstraintOr
                );
            }
            else
            {
                $constraintAnd = $wordConstraintAnd;
                $constraintOr  = $wordConstraintOr;
            }
        }
        
        $articles = $queryAnd->matching( $constraintAnd )->execute();
        
        if( !count( $articles ) )
        {
            $articles = $queryOr->matching( $constraintOr )->execute();
        }
        
        foreach( $articles as $key => $article )
        {
            if( $article->getSysLanguageUid() != $GLOBALS[ 'TSFE' ]->sys_language_uid )
            {
                unset( $articles[ $key ] );
            }
        }
        
        return $articles;
    }
    
    public function findLastModified()
    {
        $query = $this->createQuery();
        
        $query->getQuerySettings()->setRespectStoragePage( false );
        $query->setOrderings( array( 'change_date' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING ) );
        $query->setLimit( 1 );
        
        $last  = $query->execute();
        $date  = $last[ 0 ]->getChangeDate();
        
        if( $date === NULL )
        {
            return array();
        }
        
        $ts    = strtotime( $date->format( 'r' ) );
        $query = $this->createQuery();
        
        $query->getQuerySettings()->setRespectStoragePage( false );
        $query->matching( $query->equals( 'change_date', $ts ) );
        
        return $query->execute();
    }
    
    public function findMostViewed( $limit = 5 )
    {
        $query = $this->createQuery();
        
        $query->getQuerySettings()->setRespectStoragePage( false );
        
        $query->setOrderings( array( 'views' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING ) );
        
        return $query->setLimit( ( int )$limit )->execute();
    }
}
