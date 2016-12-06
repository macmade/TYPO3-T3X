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

# $Id: SendEmail.class.php 190 2010-01-06 11:16:55Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Category view
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_View_SendEmail extends tx_marvincatalog_View_Base
{
    /**
     * The HTTP version to use
     */
    protected $_httpVersion         = '1.1';
    
    /**
     * The API host
     */
    protected $_apiHost             = 'api.createsend.com';
    
    /**
     * The API port number
     */
    protected $_apiPort             = 80;
    
    /**
     * The URL to the subscriber add method
     */
    protected $_apiSubscriberAddUrl = '/api/api.asmx/Subscriber.AddAndResubscribe';
    
    protected $_catalog    = NULL;
    protected $_categories = NULL;
    protected $_collection = NULL;
    protected $_category   = NULL;
    protected $_watch      = NULL;
    protected $_errors     = array();
    
    public function __construct( tslib_piBase $plugin, $watch )
    {
        parent::__construct( $plugin );
        
        if( !( $watch instanceof stdClass ) ) {
            
            $error = $this->_content->div;
            $error->addTextData( $this->_lang->errorNoRecord );
            
        } else {
            
            $this->_catalog    = tx_marvincatalog_Magento_Catalog_Helper::getInstance();
            $this->_categories = tx_marvincatalog_Magento_Category_Helper::getInstance();
            $this->_collection = $this->_categories->getCategory( ( int )$this->_plugin->conf[ 'category' ] );
            $this->_category   = $this->_categories->getCategoryByKey( $this->_plugin->piVars[ 'category' ] );
            
            $this->_watch = $watch;
            $details      = $this->_content->div;
            $top          = $details->div;
            $left         = $details->div;
            $right        = $details->div;
            
            $this->_setPageTitle( strtoupper( $this->_category->key . ' - ' . $this->_watch->sku ) );
            
            $this->_cssClass( 'mailform', $details );
            $this->_cssClass( 'top', $top );
            $this->_cssClass( 'left', $left );
            $this->_cssClass( 'right', $right );
            
            $left->h2 = $this->_lang->title;
            
            $top->addChildNode( $this->_getRootline() );
            $this->_showWatchImage( $right );
            
            if( isset( $this->_plugin->piVars[ 'submit' ] ) ) {
                
                $this->_validateForm();
                
                if( !count( $this->_errors ) ) {
                    
                    $success = $left->div;
                    
                    $this->_cssClass( 'success', $success );
                    $success->addTextData( $this->_lang->success );
                    
                    $this->_sendEmails();
                    
                    return;
                }
            }
            
            $this->_mailForm( $left );
        }
    }
    
    /**
     * Adds a subscriber
     * 
     * @param   string      The email address to add
     * @param   string      The name of the person
     * @return  void
     * @throws  Exception   If the connection can't be established
     * @throws  Exception   If the HTTP response code is not 200
     * @throws  Exception   If the XML response can't be parsed
     * @throws  Exception   If an API error occured
     */
    protected function _addSubscriber( $email, $name )
    {
        // Establish a connection to the API host
        $connect = fsockopen( $this->_apiHost, $this->_apiPort );
        
        // Subscription REST URL
        $url     = sprintf(
            $this->_apiSubscriberAddUrl . '?ApiKey=%s&ListID=%s&Email=%s&Name=%s',
            $this->_plugin->conf[ 'campaignMonitor.' ][ 'apiKey' ],
            $this->_plugin->conf[ 'campaignMonitor.' ][ 'listId' ],
            urlencode( ( string )$email ),
            urlencode( ( string )$name )
        );
        
        // Checks if the connection was established
        if( !$connect ) {
            
            // Connection error
            throw new Exception( $this->pi_getLL( 'connectionError' ) );
        }
        
        // Newline character
        $nl   = chr( 13 ) . chr( 10 );
        
        // HTTP request
        $req  = 'GET ' . $url . ' HTTP/' . $this->_httpVersion . $nl
              . 'Host: ' . $this->_apiHost . $nl
              . 'Connection: Close' . $nl . $nl;
        
        // Writes the request in the socket
        fwrite( $connect, $req );
        
        // Storage for the response data
        $response    = '';
        
        // Whether the HTTP headers are sent
        $headersSent = false;
        
        // Gets the HTTP status code
        $status      = substr( fgets( $connect, 128 ), -8, 6 );
        
        // Checks the response status
        if( $status !== '200 OK' ) {
            
            // Connection error
            return;
        }
        
        // Process the response
        while( !feof( $connect ) ) {
            
            // Gets a line
            $line = fgets( $connect, 128 );
            
            // Checks if the HTTP headers are sent
            if( $headersSent ) {
                
                // Adds the line to the response
                $response .= $line;
            }
            
            // Checks for the end of the headers
            if( $line === $nl ) {
                
                // Headers are sent - The response will follow
                $headersSent = true;
            }
        }
        
        // Tries to parse the response
        try {
            
            // Creates an XML object from the response
            $xml = @simplexml_load_string( $response );
            
        } catch( Exception $e ) {
            
            // XML error
            return;
        }
        
        // Checks the response code
        if( ( int )$xml->Code != 0 ) {
            
            // API error
            return;
        }
    }
    
    protected function _sendEmails()
    {
        $data = $this->_plugin->piVars;
        
        $this->_addSubscriber( $data[ 'sender' ][ 'email' ], $data[ 'sender' ][ 'name' ] );
        
        if( isset( $data[ 'friend1' ][ 'name' ] ) && $data[ 'friend1' ][ 'name' ] ) {
            
            $this->_sendEmail( $data[ 'friend1' ][ 'name' ], $data[ 'friend1' ][ 'email' ] );
        }
        
        if( isset( $data[ 'friend2' ][ 'name' ] ) && $data[ 'friend2' ][ 'name' ] ) {
            
            $this->_sendEmail( $data[ 'friend2' ][ 'name' ], $data[ 'friend2' ][ 'email' ] );
        }
        
        if( isset( $data[ 'friend3' ][ 'name' ] ) && $data[ 'friend3' ][ 'name' ] ) {
            
            $this->_sendEmail( $data[ 'friend3' ][ 'name' ], $data[ 'friend3' ][ 'email' ] );
        }
    }
    
    protected function _sendEmail( $name, $email )
    {
        $conf = $this->_plugin->conf[ 'mail.' ];
        $data = $this->_plugin->piVars;
        $url = $this->_link(
            array(
                'action'     => 'catalog-watch',
                'collection' => $data[ 'collection' ],
                'watch'      => $data[ 'watch' ]
            )
        );
        
        $message = $conf[ 'subject' ] . chr( 10 ) . str_replace(
            array(
                '${friendName}',
                '${senderName}',
                '${url}',
            ),
            array(
                $name,
                $data[ 'sender' ][ 'name' ],
                t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . $url
            ),
            $conf[ 'message' ]
        );
        
        $this->_plugin->cObj->sendNotifyEmail( $message, $email, '', $data[ 'sender' ][ 'email' ], $data[ 'sender' ][ 'name' ] );
        
        try {
            
            $this->_addSubscriber( $email, $name );
            
        } catch( Exception $e ) {}
    }
    
    protected function _validateForm()
    {
        $data = $this->_plugin->piVars;
        
        $this->_validateFormAddress( 'sender' );
        
        if( ( isset( $data[ 'friend1' ][ 'name' ] ) && $data[ 'friend1' ][ 'name' ] ) || ( isset( $data[ 'friend1' ][ 'email' ] ) && $data[ 'friend1' ][ 'email' ] ) ) {
            
            $this->_validateFormAddress( 'friend1' );
        }
        
        if( ( isset( $data[ 'friend2' ][ 'name' ] ) && $data[ 'friend2' ][ 'name' ] ) || ( isset( $data[ 'friend2' ][ 'email' ] ) && $data[ 'friend2' ][ 'email' ] ) ) {
            
            $this->_validateFormAddress( 'friend2' );
        }
        
        if( ( isset( $data[ 'friend3' ][ 'name' ] ) && $data[ 'friend3' ][ 'name' ] ) || ( isset( $data[ 'friend3' ][ 'email' ] ) && $data[ 'friend3' ][ 'email' ] ) ) {
            
            $this->_validateFormAddress( 'friend3' );
        }
    }
    
    protected function _validateFormAddress( $section )
    {
        $data = $this->_plugin->piVars;
        
        if( !isset( $data[ $section ][ 'name' ] ) || !$data[ $section ][ 'name' ] ) {
            
            $this->_errors[ $section ] = $this->_lang->errorNoName;
            
        } elseif( !isset( $data[ $section ][ 'email' ] ) || !$data[ $section ][ 'email' ] ) {
            
            $this->_errors[ $section ] = $this->_lang->errorNoEmail;
            
        } elseif( !t3lib_div::validEmail( $data[ $section ][ 'email' ] ) ) {
            
            $this->_errors[ $section ] = $this->_lang->errorInvalidEmail;
        }
    }
    
    protected function _mailForm( tx_oop_Xhtml_Tag $container )
    {
        $params           = $this->_plugin->piVars;
        
        unset( $params[ 'sender' ] );
        unset( $params[ 'friend1' ] );
        unset( $params[ 'friend2' ] );
        unset( $params[ 'friend3' ] );
        unset( $params[ 'submit' ] );
        
        $form             = $container->form;
        $form[ 'method' ] = 'post';
        $form[ 'action' ] = $this->_link( $params, false );
        $form[ 'name' ]   = $this->_plugin->prefixId . '_mailform';
        $form[ 'id' ]     = $form[ 'name' ];
        
        $this->_mailFormInputs( 'sender', $form, $this->_lang->nameAndEmail );
        $this->_mailFormInputs( 'friend1', $form, $this->_lang->friendNameAndEmail );
        $this->_mailFormInputs( 'friend2', $form );
        $this->_mailFormInputs( 'friend3', $form );
        
        $submit                 = $form->div;
        $submitInput            = $submit->input;
        $submitInput[ 'type' ]  = 'submit';
        $submitInput[ 'name' ]  = $this->_plugin->prefixId . '[submit]';
        $submitInput[ 'value' ] = $this->_lang->submit;
        
        $this->_cssClass( 'submit', $submit );
    }
    
    protected function _mailFormInputs( $section, tx_oop_Xhtml_Tag $container, $label )
    {
        $data = $this->_plugin->piVars;
        $div  = $container->div;
        
        if( $label ) {
            
            $labelDiv = $div->div;
            $labelDiv->addTextData( $label );
            $this->_cssClass( 'labels', $labelDiv );
        }
        
        if( isset( $this->_errors[ $section ] ) ) {
            
            $error = $div->div;
            
            $this->_cssClass( 'mailError', $error );
            $error->addTextData( $this->_errors[ $section ] );
        }
        
        $fields = $div->div;
        
        $this->_cssClass( $section, $div );
        $this->_cssClass( 'fields', $fields );
        
        $nameInput    = $fields->input;
        $emailInput   = $fields->input;
        
        $nameInput[ 'type' ]  = 'text';
        $emailInput[ 'type' ] = 'text';
        $nameInput[ 'name' ]  = $this->_plugin->prefixId . '[' . $section . '][name]';
        $emailInput[ 'name' ] = $this->_plugin->prefixId . '[' . $section . '][email]';
        $nameInput[ 'size' ]  = '20';
        $emailInput[ 'size' ] = '20';
        
        if( isset( $data[ $section ][ 'name' ] ) ) {
            
            $nameInput[ 'value' ] = $data[ $section ][ 'name' ];
        }
        
        if( isset( $data[ $section ][ 'email' ] ) ) {
            
            $emailInput[ 'value' ] = $data[ $section ][ 'email' ];
        }
    }
    
    protected function _showWatchImage( tx_oop_Xhtml_Tag $container )
    {
        $div              = $container->div;
        $script           = $div->script;
        $script[ 'type' ] = 'text/javascript';
        $div[ 'id' ]      = $this->_plugin->prefixId . '_detailImage';
        
        $this->_cssClass( 'detailImage', $div );
        
        $imgKey          = $this->_plugin->conf[ 'watchDetailsImageKey' ];
        $magentoUrl      = $this->_extConf[ 'magento_url' ];
        $imgPath         = '/media/catalog/product/' . $this->_watch->$imgKey;
        $img             = new tx_oop_Xhtml_Tag( 'img' );
        $img[ 'alt' ]    = $this->_watch->sku;
        $img[ 'title' ]  = $this->_watch->sku;
        $img[ 'src' ]    = $magentoUrl . $imgPath;
        
        if(    isset( $this->_plugin->conf[ 'detailImage.' ][ 'maxWidth' ] )
            && $this->_plugin->conf[ 'detailImage.' ][ 'maxWidth' ]
        ) {
            
            $img[ 'width' ] = $this->_plugin->conf[ 'detailImage.' ][ 'maxWidth' ];
        }
        
        if(    isset( $this->_plugin->conf[ 'detailImage.' ][ 'maxHeight' ] )
            && $this->_plugin->conf[ 'detailImage.' ][ 'maxHeight' ]
        ) {
            
            $img[ 'height' ] = $this->_plugin->conf[ 'detailImage.' ][ 'maxHeight' ];
        }
        
        $div->addChildNode( $img );
        
        $imgPath = t3lib_extMgm::siteRelPath( $this->_plugin->extKey ) . 'res/js/jquery-lightbox/images/';
        
        $script->addTextData(
            '$(
                function()
                {
                    $( "#' . $div[ 'id' ] . ' a" ).lightBox(
                        {
                            imageLoading:   "' . $imgPath . 'lightbox-ico-loading.gif",
                            imageBtnPrev:   "' . $imgPath . 'lightbox-btn-prev.gif",
                            imageBtnNext:   "' . $imgPath . 'lightbox-btn-next.gif",
                            imageBtnClose:  "' . $imgPath . 'lightbox-btn-close.gif",
                            imageBlank:     "' . $imgPath . 'lightbox-blank.gif"
                        }
                    );
                }
            );'
        );
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/Watch.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/Watch.class.php']);
}
