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

# $Id: User.php 7 2010-10-18 13:46:27Z jean $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * User model
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Domain_Model_User extends Tx_NetExtbase_DomainObject_AbstractEntity
{
    /**
     * First name
     * 
     * @var         int
     */
    protected $prefix       = 0;
    
    /**
     * First name
     * 
     * @var         int
     */
    protected $accountType  = 0;
    /**
     * First name
     * 
     * @var         string
     */
    protected $company      = '';
    /**
     * First name
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $firstname    = '';
    
    /**
     * Last name
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $lastname     = '';
    
    /**
     * E-mail
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $email        = '';
    
    /**
     * Address
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $address1     = '';
    
    /**
     * Address
     * 
     * @var         string
     */
    protected $address2     = '';
    
    /**
     * City
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $city         = '';
    
    /**
     * ZIP code
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $zip          = '';
    
    /**
     * Country
     * 
     * @var         string
     * @validate    NotEmpty
     */
    protected $country      = '';
    
    /**
     * Telephone
     * 
     * @var         string
     */
    protected $telephone    = '';
    
    /**
     * Fax
     * 
     * @var         string
     */
    protected $fax          = '';
    
    /**
     * Mobile phone
     * 
     * @var         string
     */
    protected $mobile       = '';
    
    /**
     * Frontend user
     * 
     * @var Tx_NetExtbase_Domain_Model_FrontendUser
     */
    protected $feUser       = NULL;
    
    /**
     * Tags
     * 
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CpGuide_Domain_Model_Tag>
     */
    protected $tags         = NULL;
    
    /**
     * Comments
     * 
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CpGuide_Domain_Model_Comment>
     */
    protected $comments     = NULL;
    
    /**
     * Notes
     * 
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CpGuide_Domain_Model_Note>
     */
    protected $notes        = NULL;
    
    /**
     * Favorites
     * 
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_CpGuide_Domain_Model_Favorite>
     */
    protected $favorites    = NULL;
    
    /**
     * Confirm hash
     * 
     * @var         string
     */
    protected $confirmHash  = '';
    
    public function __construct()
    {
        $this->tags      = new Tx_Extbase_Persistence_ObjectStorage();
        $this->comments  = new Tx_Extbase_Persistence_ObjectStorage();
        $this->notes     = new Tx_Extbase_Persistence_ObjectStorage();
        $this->favorites = new Tx_Extbase_Persistence_ObjectStorage();
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
}
