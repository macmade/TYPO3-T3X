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

# $Id: FavoriteRepository.php 2 2010-06-21 07:43:04Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Favorite repository
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Domain_Repository_FavoriteRepository extends Tx_NetExtbase_Persistence_Repository
{
    public function findByUser( Tx_CpGuide_Domain_Model_User $user )
    {
        $query = $this->createQuery();
        
        $query->matching
        (
            $query->equals
            (
                'author',
                $user->getUid()
            )
        );
        
        return $query->execute();
    }
    
    public function findByArticleId( $uid )
    {
        $uid   = ( int )$uid;
        $query = $this->createQuery();
        
        $query->getQuerySettings()->setRespectStoragePage( false );
        $query->getQuerySettings()->setRespectSysLanguage( false );
        
        $query->matching( $query->equals( 'article', $uid ) );
        
        return $query->execute();
    }
}
