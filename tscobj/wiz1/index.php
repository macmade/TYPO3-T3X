<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2004 macmade.net
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is 
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Wizard to show the TS template hierarchy.
 *
 * @author		Jean-David Gadina (info@macmade.net)
 * @version		2.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *   74:		class tx_tscobj_wiz1 extends t3lib_SCbase
 *  179:		public function __construct
 *  195:		protected static function _initStaticProperties
 *  254:		protected static function _createJsCode
 *  311:		protected function _createCssStyles
 *  344:		protected function _moduleContent
 *  401:		protected function _makeLinks
 *  425:		protected function _getConfigArray
 *  460:		protected function _showTemplate( array &$conf, $pObj = '' )
 *  554:		protected function _updateData( $tsObj )
 *  582:		public function main
 *  650:		public function getContent
 * 
 *				TOTAL FUNCTIONS: 11
 */

// Gets the module configuration
unset( $MCONF );
require( 'conf.php' );

// TYPO3 initialization script
require( $BACK_PATH . 'init.php' );

// TYPO3 template class
require( $BACK_PATH . 'template.php' );

// Includes the locallang file for this module
$LANG->includeLLFile( 'EXT:tscobj/wiz1/locallang.php' );

// Includes the TYPO3 module base class
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );

// Required classes for getting the TS template
require_once( PATH_t3lib . 'class.t3lib_page.php' );
require_once( PATH_t3lib . 'class.t3lib_tstemplate.php' );

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

class tx_tscobj_wiz1 extends t3lib_SCbase
{
    // Extension key
    const EXTKEY                     = 'tscobj';
    
    // Flag to know if the module was inited
    protected static $_inited        = false;
    
    // Backend user object
    protected static $_beUser        = NULL;
    
    // Database object
    protected static $_db            = NULL;
    
    // Language object
    protected static $_lang          = NULL;
    
    // TCA array
    protected static $_tca           = array();
    
    // TCA description array
    protected static $_tcaDescr      = array();
    
    // Client informations
    protected static $_client        = array();
    
    // TYPO3 configuration array
    protected static $_typo3ConfVars = array();
    
    // Back path to TYPO3
    protected static $_backPath      = '';
    
    // Available content objets
    protected static $_cTypes        = array(
        'HTML'             => true,
        'TEXT'             => true,
        'COBJ_ARRAY'       => true,
        'COA'              => true,
        'COA_INT'          => true,
        'FILE'             => true,
        'IMAGE'            => true,
        'IMG_RESOURCE'     => true,
        'CLEARGIF'         => true,
        'CONTENT'          => true,
        'RECORDS'          => true,
        'HMENU'            => true,
        'CTABLE'           => true,
        'OTABLE'           => true,
        'COLUMNS'          => true,
        'HRULER'           => true,
        'IMGTEXT'          => true,
        'CASE'             => true,
        'LOAD_REGISTER'    => true,
        'RESTORE_REGISTER' => true,
        'FORM'             => true,
        'SEARCHRESULT'     => true,
        'USER'             => true,
        'USER_INT'         => true,
        'PHP_SCRIPT'       => true,
        'PHP_SCRIPT_INT'   => true,
        'PHP_SCRIPT_EXT'   => true,
        'TEMPLATE'         => true,
        'MULTIMEDIA'       => true,
        'EDITPANEL'        => true
    );
    
    // Extension path
    protected static $_extPath       = '';
    
    // New line character
    protected static $_NL            = '';
    
    // Tabulation character
    protected static $_TAB           = '';
    
    // Module icons to get
    protected static $_icons         = array(
        'gfx/ol/plusbullet.gif'  => '',
        'gfx/ol/minusbullet.gif' => ''
    );
    
    // Module JavaScript code
    protected static $_jsCode        = '';
    
    // Module CSS styles
    protected static $_cssStyles     = '';
    
    // Variables from the TCE
    protected $_tceData              = array();
    
    // Document object
    protected $_doc                  = NULL;
    
    // Module content
    protected $_content              = array();
    
    // TypoScript configuration array
    protected $_tsConf               = array();
    
    /**
     * Class constructor
     * 
     * @return  NULL
     * @see     _initStaticProperties
     */
    public function __construct()
    {
        // Checks the init flag
        if( !self::$_inited ) {
            
            // Initialize the module properties
            self::_initStaticProperties();
        }
    }
    
    /**
     * Initialize the module static properties
     * 
     * @return  NULL
     * @see     _createJsCode
     */
    protected static function _initStaticProperties()
    {
        // Gets a reference to the backend user object
        self::$_beUser        =  $GLOBALS[ 'BE_USER' ];
        
        // Gets a reference to the backend user object
        self::$_db            =  $GLOBALS[ 'TYPO3_DB' ];
        
        // Gets a reference to the backend user object
        self::$_lang          =  $GLOBALS[ 'LANG' ];
        
        // Gets a reference to the backend user object
        self::$_tca           =& $GLOBALS[ 'TCA' ];
        
        // Gets a reference to the backend user object
        self::$_tcaDescr      =& $GLOBALS[ 'TCA_DESCR' ];
        
        // Gets a reference to the backend user object
        self::$_client        =& $GLOBALS[ 'CLIENT' ];
        
        // Gets a reference to the backend user object
        self::$_typo3ConfVars =& $GLOBALS[ 'TYPO3_CONF_VARS' ];
        
        // Gets a reference to the backend user object
        self::$_backPath      =  $GLOBALS[ 'BACK_PATH' ];
        
        // Sets the extension path
        self::$_extPath       = t3lib_extMgm::extPath( self::EXTKEY );
        
        // Sets the new line character
        self::$_NL            =  chr( 10 );
        
        // Sets the tabulation character
        self::$_TAB           =  chr( 9 );
        
        // Process the module icons
        foreach( self::$_icons as $key => $value ) {
            
            // Creates the image tag for the current icon
            self::$_icons[ $key ] = t3lib_iconWorks::skinImg(
                self::$_backPath,
                $key,
                '',
                1
            );
        }
        
        // Sets the module JS code
        self::$_jsCode        =  self::_createJsCode();
        
        // Sets the init flag
        self::$_inited        =  true;
    }
    
    /**
     * Create the JavaScript code for the module
     * 
     * @return  string  The module JS code
     */
    protected static function _createJsCode()
    {
        // Storage
        $jsCode   = array();
        
        // Starts the script tag
        $jsCode[] = '<script type="text/javascript" charset="utf-8">';
        $jsCode[] = '<!--';
        
        // TYPO3 jump URL function
        $jsCode[] = '   script_ended = 0;';
        $jsCode[] = '   function jumpToUrl( url )';
        $jsCode[] = '   {';
        $jsCode[] = '       document.location = url;';
        $jsCode[] = '   }';
        
        // Swap CSS classes function
        $jsCode[] = 'function tx_tscobj_swapClasses( element )';
        $jsCode[] = '{';
        $jsCode[] = '   if( document.getElementById ) {';
        $jsCode[] = '       var liClass                                     = document.getElementById( element ).className;';
        $jsCode[] = '       document.getElementById( element ).className    = ( liClass == "open" ) ? "closed" : "open";';
        $jsCode[] = '       document.getElementById( element + "-img" ).src = ( liClass == "open" ) ? "' . self::$_icons[ 'gfx/ol/plusbullet.gif' ] . '" : "' . self::$_icons[ 'gfx/ol/minusbullet.gif' ] . '";';
        $jsCode[] = '   }';
        $jsCode[] = '}';
        
        // Expand all function
        $jsCode[] = 'var expanded = 0;';
        $jsCode[] = 'function tx_tscobj_expAll()';
        $jsCode[] = '{';
        $jsCode[] = '   if( document.getElementsByTagName ) {';
        $jsCode[] = '       var listItems = document.getElementsByTagName( "li" );';
        $jsCode[] = '       for( i = 0; i < listItems.length; i++ ) {';
        $jsCode[] = '           listItems[ i ].className = ( expanded ) ? "closed" : "open";';
        $jsCode[] = '           var id                   = listItems[ i ].id;';
        $jsCode[] = '           if( id.substr( id.length - 1, id.length ) == "." ) {';
        $jsCode[] = '               var picture                            = id + "-img";';
        $jsCode[] = '               document.getElementById( picture ).src = ( expanded ) ? "' . self::$_icons[ 'gfx/ol/plusbullet.gif' ] . '" : "' . self::$_icons[ 'gfx/ol/minusbullet.gif' ] . '";';
        $jsCode[] = '           }';
        $jsCode[] = '       }';
        $jsCode[] = '       expanded = ( expanded == 1 ) ? 0 : 1;';
        $jsCode[] = '   }';
        $jsCode[] = '}';
        
        // Ends the script tag
        $jsCode[] = '//-->';
        $jsCode[] = '</script>';
        
        // Returns the JS code
        return implode( self::$_NL, $jsCode );
    }
    
    /**
     * Creates the CSS styles for the module
     * 
     * @return  string  The module CSS styles
     */
    protected function _createCssStyles()
    {
        // Checks if the CSS styles are already processed
        if( !self::$_cssStyles ) {
            
            // Path to the module stylesheet
            $cssPath          = self::$_extPath . 'wiz1/stylesheet.css';
            
            // Read stylesheet
            $styles           = t3lib_div::getURL( $cssPath );
            
            // Replace the color markers with the TYPO3 skin color
            $styles           = str_replace( '###COLOR1###', $this->_doc->bgColor5, $styles );
            $styles           = str_replace( '###COLOR2###', $this->_doc->bgColor6, $styles );
            $styles           = str_replace( '###COLOR3###', $this->_doc->bgColor3, $styles );
            
            // Stores the CSS styles
            self::$_cssStyles = $styles;
        }
        
        // Returns the CSS styles
        return self::$_cssStyles;
    }
    
    /**
     * Creates the module's content
     * 
     * @return  NULL
     * @see     _updateData
     * @see     _getConfigArray
     * @see     _makeLinks
     * @see     _showTemplate
     */
    protected function _moduleContent()
    {
        // Checks if a TS object has been selected
        if( $id = t3lib_div::_GP( 'tsobj' ) ) {
            
            // Updates the flexform data
            $this->_updateData( $id );
            
            // Return to the TCE form
            header( 'Location: ' . t3lib_div::locationHeaderUrl( $this->_tceData[ 'returnUrl' ] ) );
            
        } else {
            
            // TypoScript configuration array
            $this->_tsConf    = $this->_getConfigArray();
            
            // DEBUG ONLY - Shows the TS array
            #t3lib_div::debug( $this->_tsConf, 'TypoScript configuration array' );
            
            // Starts a section
            $this->_content[] = $this->_doc->sectionBegin();
            
            // Module description
            $this->_content[] = '<div style="padding: 5px; border: dashed 1px #666666;">'
                             . self::$_lang->getLL( 'description' )
                             . '<br /><strong>'
                             . self::$_lang->getLL( 'instructions' )
                             . '</strong></div>';
            
            // Spacer
            $this->_content[] = $this->_doc->spacer( 5 );
            
            // Gets the module links
            $links            = $this->_makeLinks();
            
            // Adds the module links
            $this->_content[] = $links;
            
            // Show TS template hierarchy
            $this->_content[] = $this->_showTemplate( $this->_tsConf );
            
            // Adds the module links
            $this->_content[] = $links;
            
            // Ends the section
            $this->_content[] = $this->_doc->sectionEnd();
        }
    }
    
    /**
     * Creates the module links
     * 
     * This function creates the links to return to the TCE forms and
     * to expand/collapse all sections of the TS template.
     * 
     * @return  string  The module links
     */
    protected function _makeLinks()
    {
        // Back link
        $links = '<div><a href="'
               . $this->_tceData[ 'returnUrl' ]
               . '">'
               . self::$_lang->getLL( 'backlink' )
               . '</a><br />'
               . '<a href="javascript:tx_tscobj_expAll();">'
               . self::$_lang->getLL( 'showall' )
               . '</a></div>';
        
        // Returns the links
        return $links;
    }
    
    /**
     * Gets the TS template
     * 
     * This function creates instances of the class needed to render
     * the TS template, and gets it as a multi-dimensionnal array.
     * 
     * @return  array   An array containing all the available TS objects
     */
    protected function _getConfigArray()
    {
        // Creates an instance of the page select class
        $sysPage = t3lib_div::makeInstance( 't3lib_pageSelect' );
        
        // Creates an instance of the TS template class
        $tmpl = t3lib_div::makeInstance( 't3lib_TStemplate' );
        
        // Initialization
        $sysPage->init( true );
        $tmpl->init();
        
        // Avoid some errors
        $tmpl->tt_track = 0;
        
        // Gets the rootline for current PID
        $rootline       = $sysPage->getRootLine( $this->_tceData[ 'pid' ] );
        
        // Parses the TS template
        $tmpl->start( $rootline );
        
        // Returns the TS configuration array
        return $tmpl->setup;
    }
    
    /**
     * Shows the TS template hierarchy
     * 
     * This function displays the TS template hierarchy as HTML list
     * elements. Each section can be expanded/collapsed.
     * 
     * @param   array   &$conf  An array with the TS configuration (passed by reference)
     * @param   string  $pObj   The ID of the TS parent object
     * @return  NULL
     */
    protected function _showTemplate( array &$conf, $pObj = '' )
    {
        // Storage
        $htmlCode = array();
        
        // Starts an HTML list
        $htmlCode[] = '<ul>';
        
        // Storage for the last TS object ID
        $lastKey = '';
        
        // Process each object of the configuration array
        foreach( $conf as $key => &$value ) {
            
            // TS object ID
            $id = $pObj . $key;
            
            // Check if the current object is a container
            if( is_array( $value ) ) {
                
                // Checks if the current object is the configuration of the previous one
                if( !$lastKey || substr( $key, 0, - 1 ) != $lastKey ) {
                    
                    // Not related to the previous object - Process the sub configuration
                    $subContent = $this->_showTemplate( $value, $id );
                    
                    // Check if objects were found
                    if( $subContent ) {
                        
                        // Adds a LI container for the sub elements
                        $htmlCode[] = '<li class="closed" id="'
                                    . $id
                                    . '"><div class="container"><a href="javascript:tx_tscobj_swapClasses(\''
                                    . $id
                                    . '\')"><img id="'
                                    . $id
                                    . '-img" src="'
                                    . self::$_icons[ 'gfx/ol/plusbullet.gif' ]
                                    . '" alt="" hspace="0" vspace="0" border="0" align="middle"></a>&nbsp;'
                                    . $key
                                    . $subContent
                                    . '</div></li>';
                    }
                }
            } elseif( isset( self::$_cTypes[ $value ] ) ) {
                
                // Memorize the current key
                $lastKey    = $key;
                
                // Link parameters
                $linkParams = '?P[returnUrl]='
                            . urlencode( $this->_tceData[ 'returnUrl' ] )
                            . '&P[table]='
                            . $this->_tceData[ 'table' ]
                            . '&P[uid]='
                            . $this->_tceData[ 'uid' ]
                            . '&tsobj='
                            . $id;
                
                // Adds the TS object
                $htmlCode[] = '<li><div class="object"><strong><a href="'
                            . t3lib_div::linkThisScript()
                            . $linkParams
                            . '" title="'
                            . $id
                            . '">'
                            . $key
                            . '</a></strong> ('
                            . $value
                            . ')</div></li>';
            }
        }
        
        // Ends the HTML list
        $htmlCode[] = '</ul>';
        
        // Checks if TS objects have been detected
        if( count( $htmlCode ) > 2 ) {
            
            // Returns the TS hierarchy
            return implode( self::$_NL, $htmlCode );
        }
    }
    
    /**
     * Updates the flexform data
     * 
     * This function updates the flexform data with the selected TS
     * object. If the flexform data does not exist, the function will
     * create it.
     * 
     * @param   string  $tsObj  The TS object path
     * @return  NULL
     */
    protected function _updateData( $tsObj )
    {
        // Creates the base TYPO3 flexform node
        $flexData = new SimpleXMLElement( '<T3FlexForms></T3FlexForms>' );
        
        // Adds the TS object path
        $flexData->data->sDEF->lDEF->object->vDEF = $tsObj;
        
        // Updates the database
        self::$_db->exec_UPDATEquery(
            $this->_tceData[ 'table' ],
            'uid=' . $this->_tceData[ 'uid' ],
            array(
                'pi_flexform' => $flexData->asXml()
            )
        );
    }
    
    /**
     * Creates the page
     * 
     * This function creates the basic page in which the module will
     * take place.
     * 
     * @return  NULL
     * @see     _createCssStyles
     * @see     _moduleContent
     */
    public function main()
    {
        
        // Store variables from TCE
        $this->_tceData          = t3lib_div::_GP( 'P' );
        
        // Creates a document object
        $this->_doc              = t3lib_div::makeInstance( 'mediumDoc' );
        
        // Sets the back path
        $this->_doc->backPath    = self::$_backPath;
        
        // Sets the form tag
        $this->_doc->form        = '<form action="" method="post" enctype="'
                                 . self::$_typo3ConfVars[ 'SYS' ][ 'form_enctype' ]
                                 . '">';
        
        // Sets the module JavaScript code
        $this->_doc->JScode      = self::$_jsCode;
        
        // Adds the CSS styles for the module
        $this->_doc->inDocStyles = $this->_createCssStyles();
        
        // Reads the page access
        $pageInfos               = t3lib_BEfunc::readPageAccess(
            $this->id,
            $this->perms_clause
        );
        
        // Access flag
        $access                  = is_array( $pageInfos ) ? true : false;
        
        // Checks the access
        if( ( $this->id && $access ) || ( self::$_beUser->user[ 'admin' ] && !$this->id ) ) {
            
            // Current user is an administrator
            if( self::$_beUser->user[ 'admin' ] && !$this->id ) {
                
                // Sets the page informations
                $pageInfos = array(
                    'title' => '[root-level]',
                    'uid'   => 0,
                    'pid'   => 0
                );
            }
            
            // Starts the page content
            $this->_content[] = $this->_doc->startPage( self::$_lang->getLL( 'title' ) );
            $this->_content[] = $this->_doc->header( self::$_lang->getLL( 'title' ) );
            $this->_content[] = $this->_doc->spacer( 5 );
            $this->_content[] = $this->_doc->divider( 5 );
            
            // Renders the module content
            $this->_moduleContent();
        }
        
        // Spacer
        $this->_content[] = $this->_doc->spacer( 10 );
        
        // Ends the module page
        $this->_content[] = $this->_doc->endPage();
    }
    
    /**
     * Gets the content of the module
     * 
     * @return  string  The module content
     */
    public function getContent()
    {
        return implode( self::$_NL, $this->_content );
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tscobj/wiz1/index.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tscobj/wiz1/index.php']);
}

// Creates an instance of the module
$SOBE = t3lib_div::makeInstance( 'tx_tscobj_wiz1' );
$SOBE->init();

// Starts the module
$SOBE->main();

// Prints the module content
print( $SOBE->getContent() );
