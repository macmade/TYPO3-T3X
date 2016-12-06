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

# $Id: Article.php 24 2010-06-21 07:53:48Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Article model
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  alcoopedia
 */
class Tx_Alcoopedia_Domain_Model_Article extends Tx_NetExtbase_DomainObject_AbstractEntity
{
    protected $_category           = NULL;
    
    /**
     * Title
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $title    = '';
    
    /**
     * Content
     * 
     * @var         string
     */
    protected $content  = '';
    
    /**
     * Content
     * 
     * @var         integer
     */
    protected $views    = 0;
    
    public function getCategory()
    {
        if( !is_object( $this->_category ) )
        {
            $repository      = t3lib_div::makeInstance( 'Tx_Alcoopedia_Domain_Repository_CategoryRepository' );
            $this->_category = $repository->findByArticle( $this );
        }
        
        return $this->_category;
    }
}
