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

# $Id: LoginController.php 7 2010-10-18 13:46:27Z jean $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Login controller
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 * @default
 */
class Tx_CpGuide_Controller_LoginController extends Tx_NetExtbase_MVC_Controller_ActionController
{
    protected $_userRepository = NULL;
    
    public function initializeAction()
    {
        $this->_userRepository = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_UserRepository' );
    }
    
    /**
     * @param   boolean     $login
     * @default
     * @nocache
     */
    public function indexAction( $login = false )
    {
        $user    = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        $profile = NULL;
        
        if( $user !== NULL )
        {
            $profile = $this->_userRepository->findByFrontendUser( $user );
        }
        
#        if( $user && $GLOBALS[ 'TSFE' ]->id == $this->settings[ 'homePid' ] )
#        {
#            $this->redirect( 'home', 'Guide', NULL, array(), $this->settings[ 'displayPid' ] );
#        }
        
        if( $user && $login )
        {
            $this->redirect( 'index', 'Guide' );
        }
        
        $this->view->assign( 'user',    $user );
        $this->view->assign( 'profile', $profile );
    }
    
    /**
     * @nocache
     */
    public function logoutAction()
    {
        $user = Tx_NetExtbase_Utility_Frontend::getLoginUser();
        $pid  = $this->settings[ 'displayPid' ];
        
        if( $user !== NULL )
        {
            $GLOBALS[ 'TSFE' ]->fe_user->logoff();
        }
        
        if( $GLOBALS[ 'TSFE' ]->id == $this->settings[ 'displayPid' ] )
        {
            $this->redirect( 'index', 'Guide' );
        }
        else
        {
            $this->redirect( 'index', 'Login' );
        }
    }
    
    /**
     * @param   array   $login
     * @nocache
     */
    public function loginAction( array $login = array() )
    {
        if( isset( $login[ 'username' ] ) && isset( $login[ 'password' ] ) )
        {
            $_POST[ 'logintype' ] = 'login';
            $_POST[ 'user' ]      = $login[ 'username' ];
            $_POST[ 'pass' ]      = $login[ 'password' ];
            $_POST[ 'pid' ]       = $this->settings[ 'storagePid' ];
            
            $GLOBALS[ 'TSFE' ]->initFEuser();
            
            unset( $_POST[ 'logintype' ] );
            unset( $_POST[ 'user' ] );
            unset( $_POST[ 'pass' ] );
            unset( $_POST[ 'pid' ] );
        }
        
        #$this->redirect( 'index', 'Login', NULL, array( 'login' => true ) );
    }
}
