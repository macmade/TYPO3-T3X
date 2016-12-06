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

# $Id: ArticleRepository.php 24 2010-06-21 07:53:48Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Article repository
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  alcoopedia
 */
class Tx_Alcoopedia_Domain_Repository_ArticleRepository extends Tx_NetExtbase_Persistence_Repository
{
    public function findMostViewed( $limit = 5 )
    {
        $query = $this->createQuery();
        
        $query->setOrderings( array( 'views' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING ) );
        $query->setLimit( ( int )$limit );
        
        return $query->execute();
    }
    
    public function findByWords( array $words )
    {
        $query      = $this->createQuery();
        $numWords   = count( $words );
        $constraint = NULL;
        
        if( $numWords === 0 )
        {
            return array();
        }
        
        foreach( $words as $word )
        {
            $wordConstraint = $query->logicalOr
            (
                $query->like( 'title', '%' . $word . '%' ),
                $query->like( 'content', '%' . $word . '%' )
            );
            
            if( is_object( $constraint ) )
            {
                $constraint = $query->logicalAnd
                (
                    $constraint,
                    $wordConstraint
                );
            }
            else
            {
                $constraint = $wordConstraint;
            }
        }
        
        $query->setOrderings( array( 'views' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING ) );
        
        return $query->matching( $constraint )->execute();
    }
}
