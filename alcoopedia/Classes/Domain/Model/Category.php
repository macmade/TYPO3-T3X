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

# $Id: Category.php 24 2010-06-21 07:53:48Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Category model
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  alcoopedia
 */
class Tx_Alcoopedia_Domain_Model_Category extends Tx_NetExtbase_DomainObject_AbstractEntity
{
    /**
     * Title
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $title    = '';
    
    /**
     * Articles
     * 
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Alcoopedia_Domain_Model_Article>
     */
    protected $articles = NULL;
    
    public function __construct()
    {
        $this->articles = new Tx_Extbase_Persistence_ObjectStorage();
    }
    
    public function addArticle( Tx_Alcoopedia_Domain_Model_Article $article )
    {
        $this->articles->attach( $article );
    }
    
    public function removeArticle( Tx_Alcoopedia_Domain_Model_Article $article )
    {
        $this->articles->detach( $article );
    }
    
    public function removeAllArticles()
    {
        $this->articles = new Tx_Extbase_Persistence_ObjectStorage();
    }
}
