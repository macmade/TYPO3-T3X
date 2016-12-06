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

# $Id: CategoryRepository.php 24 2010-06-21 07:53:48Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Category repository
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  alcoopedia
 */
class Tx_Alcoopedia_Domain_Repository_CategoryRepository extends Tx_NetExtbase_Persistence_Repository
{
    public function findByArticle( Tx_Alcoopedia_Domain_Model_Article $article )
    {
        $articleId = $article->getUid();
        $query     = $this->createQuery();
        $table     = $query->getSource()->getSelectorName();
        
        $query->statement
        (
            'SELECT * FROM tx_alcoopedia_category_article_mm as mm, ' . $table . ' WHERE ' . $table . '.uid = mm.uid_local AND mm.uid_foreign = ' . $articleId
        );
        
        $rows = $query->execute();
        
        if( is_array( $rows ) )
        {
            return array_shift( $rows );
        }
        
        return NULL;
    }
}
