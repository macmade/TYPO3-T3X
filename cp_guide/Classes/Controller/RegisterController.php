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

# $Id: RegisterController.php 16 2010-10-18 15:55:17Z jean $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Register controller
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Controller_RegisterController extends Tx_NetExtbase_MVC_Controller_ActionController
{
    protected $_userRepository    = NULL;
    protected $_feUserRepository  = NULL;
    protected $_feGroupRepository = NULL;
    
    public function initializeAction()
    {
        $this->_userRepository    = t3lib_div::makeInstance( 'Tx_CpGuide_Domain_Repository_UserRepository' );
        $this->_feUserRepository  = t3lib_div::makeInstance( 'Tx_NetExtbase_Domain_Repository_FrontendUserRepository' );
        $this->_feGroupRepository = t3lib_div::makeInstance( 'Tx_NetExtbase_Domain_Repository_FrontendUserGroupRepository' );
    }
    
    /**
     * @default
     * @nocache
     */
    public function indexAction()
    {
        $this->view->assign( 'step', 1 );
        $this->view->assign( 'prefixOptions', array( 'M', 'Mme', 'Mlle' ) );
        $this->view->assign( 'accountTypeOptions', array( 'Privé', 'Société' ) );
    }
    
    /**
     * 
     * @param   Tx_CpGuide_Domain_Model_User    $newUser
     * @return  void
     * @nocache
     */
    public function createAction( Tx_CpGuide_Domain_Model_User $newUser = NULL )
    {
        if( $newUser === NULL )
        {
            $this->redirect( 'index', 'Register' );
        }
        
        $feGroup = $this->_feGroupRepository->findByUid( ( int )$this->settings[ 'feGroup' ] );
        $feUser  = new Tx_NetExtbase_Domain_Model_FrontendUser( $newUser->getEmail() );
        
        $feUser->setPid( ( int )$this->settings[ 'storagePid' ] );
        $newUser->setPid( ( int )$this->settings[ 'storagePid' ] );
        
        $feUser->setUsergroup( $feGroup );
        $this->_feUserRepository->add( $feUser );
        $newUser->setFeUser( $feUser );
        $this->_userRepository->add( $newUser );
        $this->redirect
        (
            'password',
            'Register',
            $this->extensionName,
            array
            (
                'username' => $feUser->getUsername()
            )
        );
    }
    
    /**
     * 
     * @return  void
     * @nocache
     */
    public function passwordAction()
    {
        $args = $this->request->getArguments();
        
        if( !isset( $args[ 'username' ] ) || !$args[ 'username' ] )
        {
            $this->redirect( 'index', 'Register' );
        }
        
        $user = $this->_userRepository->findByEmail( $args[ 'username' ] );
        
        if( $user === NULL )
        {
            $this->redirect( 'index', 'Register' );
        }
        
        $this->view->assign( 'user', $user );
        
        if( isset( $args[ 'error' ] ) && $args[ 'error' ] == 1 )
        {
            $this->view->assign( 'error', $this->_lang->registerErrorNoPassword );
        }
        elseif( isset( $args[ 'error' ] ) && $args[ 'error' ] == 2 )
        {
            $this->view->assign( 'error', $this->_lang->registerErrorPasswordMatch );
        }
        
        $this->view->assign( 'step', 2 );
    }
    
    /**
     * 
     * @param   array   $feUser
     * @return  void
     * @nocache
     */
    public function passwordcreateAction( array $feUser = array() )
    {
        $user = $this->_userRepository->findByEmail( $feUser[ 'username' ] );
        
        if( $user === NULL )
        {
            $this->redirect( 'index', 'Register' );
        }
        
        if( !$feUser[ 'password' ] )
        {
            $this->redirect
            (
                'password',
                'Register',
                $this->extensionName,
                array
                (
                    'username' => $feUser[ 'username' ],
                    'error'    => 1
                )
            );
        }
        
        if( $feUser[ 'password' ] !== $feUser[ 'password2' ] )
        {
            $this->redirect
            (
                'password',
                'Register',
                $this->extensionName,
                array
                (
                    'username' => $feUser[ 'username' ],
                    'error'    => 2
                )
            );
        }
        
        if( !count( $feUser ) )
        {
            $this->redirect( 'index', 'Register' );
        }
        
        $user->getFeUser()->setPassword( $feUser[ 'password' ] );
        $this->view->assign( 'user', $user );
        $this->view->assign( 'step', 3 );
        
        $confirmHash = md5( uniqid( microtime(), true ) );
        $mailHeaders = 'From: ' . $this->settings[ 'mail' ][ 'from' ]
                     . chr( 10 )
                     . 'Reply-To: ' . $this->settings[ 'mail' ][ 'replyTo' ];
        
        $user->setConfirmHash( $confirmHash );
        
        $cObj        = t3lib_div::makeInstance( 'tslib_cObj' );
        $confirmLink = t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . $cObj->typoLink_URL
        (
            array
            (
                'parameter'         => $GLOBALS[ 'TSFE' ]->id,
                'additionalParams'  => '&tx_cpguide_pi1[confirmHash]=' . $confirmHash
                                    .  '&tx_cpguide_pi1[controller]=Register'
                                    .  '&tx_cpguide_pi1[action]=confirmemail'
            )
        );
        
        t3lib_div::plainMailEncoded
        (
            $user->getEmail(),
            $this->_lang->confirmEmailSubject,
            sprintf
            (
                $this->_lang->confirmEmailMessage,
                $confirmLink
            ),
            $mailHeaders
        );
        
        $this->view->assign( 'devEmail', sprintf( $this->_lang->confirmEmailMessage, $confirmLink ) );
    }
    
    /**
     * 
     * @param   string  $confirmHash
     * @return  void
     * @nocache
     */
    public function confirmemailAction( $confirmHash )
    {
        $user = $this->_userRepository->findByHash( ( string )$confirmHash );
        
        if( !$user )
        {
            $this->redirect( 'index', 'Register' );
        }
        
        $this->view->assign( 'user', $user );
        $this->view->assign( 'step', 4 );
        $this->view->assign
        (
            'datatrans',
            array
            (
                'amount'     => $this->settings[ 'datatrans' ][ 'amount' ],
                'currency'   => $this->settings[ 'datatrans' ][ 'currency' ],
                'merchantId' => $this->settings[ 'datatrans' ][ 'merchantId' ],
                'url'        => array
                (
                    'pay'     => $this->settings[ 'datatrans' ][ 'url' ][ 'pay' ],
                    'success' => $this->_getLink( 'onlinePaySuccess', array( 'user' => $user->getUid() ) ),
                    'cancel'  => $this->_getLink( 'onlinePayCancel', array( 'user' => $user->getUid() ) ),
                    'error'   => $this->_getLink( 'onlinePayError', array( 'user' => $user->getUid() ) )
                ),
            )
        );
        
        
        // ----- TEMP
        
        $user->setConfirmHash( '' );
        
        $this->view->assign( 'user', $user );
        $this->view->assign( 'step', 5 );
        $this->view->assign( 'guidePid', $this->settings[ 'displayPid' ] );
    }
    
    /**
     * 
     * @param   Tx_CpGuide_Domain_Model_User    $user
     * @return  void
     * @nocache
     */
    public function onlinePaySuccessAction( Tx_CpGuide_Domain_Model_User $user )
    {
        $user->setConfirmHash( '' );
        
        $this->view->assign( 'user', $user );
        $this->view->assign( 'step', 5 );
        $this->view->assign( 'guidePid', $this->settings[ 'displayPid' ] );
    }
    
    /**
     * 
     * @param   Tx_CpGuide_Domain_Model_User    $user
     * @return  void
     * @nocache
     */
    public function onlinePayCancelAction( Tx_CpGuide_Domain_Model_User $user )
    {
        $this->view->assign( 'user', $user );
        $this->view->assign( 'step', 4 );
        $this->view->assign
        (
            'datatrans',
            array
            (
                'amount'     => $this->settings[ 'datatrans' ][ 'amount' ],
                'currency'   => $this->settings[ 'datatrans' ][ 'currency' ],
                'merchantId' => $this->settings[ 'datatrans' ][ 'merchantId' ],
                'url'        => array
                (
                    'pay'     => $this->settings[ 'datatrans' ][ 'url' ][ 'pay' ],
                    'success' => $this->_getLink( 'onlinePaySuccess', array( 'user' => $user->getUid() ) ),
                    'cancel'  => $this->_getLink( 'onlinePayCancel', array( 'user' => $user->getUid() ) ),
                    'error'   => $this->_getLink( 'onlinePayError', array( 'user' => $user->getUid() ) )
                ),
            )
        );
    }
    
    /**
     * 
     * @param   Tx_CpGuide_Domain_Model_User    $user
     * @return  void
     * @nocache
     */
    public function onlinePayErrorAction( Tx_CpGuide_Domain_Model_User $user )
    {
        $errorMessage = t3lib_div::_POST( 'errorMessage' );
        $errorDetail  = t3lib_div::_POST( 'errorDetail' );
        
        if( $errorDetail )
        {
            $errorMessage .= ' (' . $errorDetail . ')';
        }
        
        $error = ( $errorMessage ) ? $errorMessage : 'N/A';
        
        $this->view->assign( 'datatransError', $error );
        $this->view->assign( 'user', $user );
        $this->view->assign( 'step', 4 );
        $this->view->assign
        (
            'datatrans',
            array
            (
                'amount'     => $this->settings[ 'datatrans' ][ 'amount' ],
                'currency'   => $this->settings[ 'datatrans' ][ 'currency' ],
                'merchantId' => $this->settings[ 'datatrans' ][ 'merchantId' ],
                'url'        => array
                (
                    'pay'     => $this->settings[ 'datatrans' ][ 'url' ][ 'pay' ],
                    'success' => $this->_getLink( 'onlinePaySuccess', array( 'user' => $user->getUid() ) ),
                    'cancel'  => $this->_getLink( 'onlinePayCancel', array( 'user' => $user->getUid() ) ),
                    'error'   => $this->_getLink( 'onlinePayError', array( 'user' => $user->getUid() ) )
                ),
            )
        );
    }
}
