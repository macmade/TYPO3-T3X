<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 Jean-David Gadina (info@macmade.net)
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
 * Module 'Ressource location' for the 'vd_ressourcelocation' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - INIT
 *        :     function init
 *        : function main
 * 
 * SECTION:     2 - MAIN
 *        :     function menuConfig
 *        :     function moduleContent
 * 
 *              TOTAL FUNCTIONS: 
 */

// Default initialization of the module
unset( $MCONF );
require_once( 'conf.php' );
require_once( $BACK_PATH . 'init.php' );
require_once( $BACK_PATH . 'template.php' );
$LANG->includeLLFile( 'EXT:vd_municipalities/mod1/locallang.xml' );
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
$BE_USER->modAccess( $MCONF, 1 );

// Import helper class
require_once( t3lib_extMgm::extPath( 'vd_municipalities' ) . 'class.tx_vdmunicipalities_import.php' );

class  tx_vdmunicipalities_module1 extends t3lib_SCbase
{
    // Page informations
    var $pageinfo = array();
    
    // Extension configuration
    var $extConf  = array();
    
    // Import helper
    var $import   = NULL;
    
    // Extension table
    var $extTable = 'tx_vdmunicipalities_municipalities';
    
    
    
    /***************************************************************
     * SECTION 1 - INIT
     *
     * Base module functions.
     ***************************************************************/
    
    /**
     * Initialization of the class.
     * 
     * @return      Void
     */
    function init()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
        
        // Get extension configuration
        $this->extConf = unserialize( $TYPO3_CONF_VARS['EXT']['extConf']['vd_municipalities'] );
        
        // New instance of the import helper class
        $this->import = t3lib_div::makeInstance( 'tx_vdmunicipalities_import' );
        
        // Init
        parent::init();
    }
    
    /**
     * Creates the page.
     * 
     * This function creates the basic page in which the module will
     * take place.
     * 
     * @return      Void
     */
    function main()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
        
        // Access check
        $this->pageinfo = t3lib_BEfunc::readPageAccess( $this->id, $this->perms_clause );
        $access = is_array( $this->pageinfo ) ? 1 : 0;
        
        if( ( $this->id && $access ) || ( $BE_USER->user[ 'admin' ] && !$this->id ) ) {
            
            // Draw the header
            $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath = $BACK_PATH;
            $this->doc->form     = '<form action="" method="POST">';
            
            // JavaScript
            $this->doc->JScode = '
                <script type="text/javascript" language="Javascript" charset="iso-8859-1">
                    <!--
                    script_ended = 0;
                    function jumpToUrl(URL) {
                        document.location = URL;
                    }
                    //-->
                </script>
            ';
            $this->doc->postCode = '
                <script type="text/javascript" language="Javascript" charset="iso-8859-1">
                    <!--
                    script_ended = 1;
                    if(top.fsMod) {
                        top.fsMod.recentIds["web"] = ' . intval( $this->id ) . ';
                    }
                    //-->
                </script>
            ';
            
            // Build current path
            $headerSection = $this->doc->getHeader(
                'pages',
                $this->pageinfo,
                $this->pageinfo[ '_thePath' ]
            )
            . '<br>'
            . $LANG->sL( 'LLL:EXT:lang/locallang_core.php:labels.path' )
            . ': '
            . t3lib_div::fixed_lgd_pre( $this->pageinfo[ '_thePath' ], 50 );
            
            // Start page content
            $this->content .= $this->doc->startPage( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->header( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->spacer( 5 );
            $this->content .= $this->doc->section( '', $headerSection );
            $this->content .= $this->doc->divider( 5 );
            
            // Render content
            $this->moduleContent();
            
            // Adds shortcut
            if( $BE_USER->mayMakeShortcut() ) {
                $this->content .= $this->doc->spacer( 20 )
                               .  $this->doc->section(
                                    '',
                                    $this->doc->makeShortcutIcon(
                                        'id',
                                        implode( ',', array_keys( $this->MOD_MENU ) ),
                                        $this->MCONF[ 'name' ]
                                    )
                                  );
            }
            
            // Spacer
            $this->content .= $this->doc->spacer(10);
            
        } else {
            
            // No access
            $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath = $BACK_PATH;
            $this->content      .= $this->doc->startPage( $LANG->getLL( 'title' ) );
            $this->content      .= $this->doc->header( $LANG->getLL ( 'title' ) );
            $this->content      .= $this->doc->spacer( 5 );
            $this->content      .= $LANG->getLL( 'noaccess' );
            $this->content      .= $this->doc->spacer( 10 );
        }
    }
    
    
    
    
    
    /***************************************************************
     * SECTION 2 - MAIN
     *
     * Main module functions.
     ***************************************************************/
    
    /**
     * Prints the page.
     * 
     * This function closes the page, and writes the final
     * rendered content.
     * 
     * @return      Void
     */
    function printContent()
    {
        // Ends the page and writes module content
        $this->content .= $this->doc->endPage();
        echo( $this->content );
    }
    
    /**
     * Writes some code.
     * 
     * This function writes a text wrapped inside an HTML tag.
     * @param       string      $text               The text to write
     * @param       string      $tag                The HTML tag to write
     * @param       array       $style              An array containing the CSS styles for the tag
     * @return      string      The text wrapped in the HTML tag
     */
    function writeHtml( $text, $tag = 'div', $class = false, $style = false, $params = array() )
    {
        
        // Create style tag
        if ( is_array( $style ) ) {
            $styleTag = ' style="' . implode( '; ', $style ) . '"';
        }
        
        // Create class tag
        if ( isset( $class ) ) {
            $classTag = ' class="' . $class . '"';
        }
        
        // Tag parameters
        if( count( $params ) ) {
            $paramsTags = ' ' . $this->api->div_writeTagParams( $params );
        }
        
        // Return HTML text
        return '<' . $tag . $classTag . $styleTag . $paramsTags . '>' . $text . '</' . $tag . '>';
    }
    
    /**
     * Creates the module's content.
     * 
     * This function creates the module's content.
     * 
     * @return	NULL
     * @see     wsConnect
     * @see     writeHtml
     */
    function moduleContent()
    {
        global $LANG;
        
        // Storage
        $htmlCode   = array();
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Checks extension configuration
        if( isset( $this->extConf[ 'wsdl' ] ) && $this->extConf[ 'wsdl' ] &&
            isset( $this->extConf[ 'soapOperation' ] ) && $this->extConf[ 'soapOperation' ] &&
            isset( $this->extConf[ 'soapParams' ] ) && $this->extConf[ 'soapParams' ] &&
            isset( $this->extConf[ 'xmlNodes' ] ) && $this->extConf[ 'xmlNodes' ] ) {
            
            // Adds the title
            $sectionTitle = $LANG->getLL( 'import.title' );
            
            // Connect to the web service
            $htmlCode[]   = $this->wsConnect();
            
        } else {
            
            // Error message - Extension is not configured
            $sectionTitle = $LANG->getLL( 'extconf.error.title' );
            $htmlCode[]   = $this->writeHtml( $LANG->getLL( 'extconf.error.msg' ), 'div', 'typo3-red' );
            $htmlCode[]   = $this->doc->spacer( 10 );
            $htmlCode[]   = $this->writeHtml( '<img src="res/extconf.gif" alt="extconf" width="573" height="302" />' );
        }
        
        // Add content
        $this->content .= $this->doc->spacer( 10 ) . $this->doc->section(
            $sectionTitle,
            implode( chr( 10 ), $htmlCode )
        );
    }
    
    /**
     * Connect to the web service
     * 
     * This function will try to connect to the web service and will try to
     * parse the result as XML. If everything is ok, it will display the list
     * of the municipalities containes in the web service data.
     * 
     * @return  string  The list of the municipalities, or an error if the web service is unreachable
     * @see     showMunicipalities
     * @see     writeHtml
     */
    function wsConnect()
    {
        global $LANG;
        
        // Set connection informations
        $this->import->setWsdl( $this->extConf[ 'wsdl' ] );
        $this->import->setSoapOperation( $this->extConf[ 'soapOperation' ] );
        $this->import->setSoapParameters( $this->extConf[ 'soapParams' ] );
        $this->import->setXmlNodes( $this->extConf[ 'xmlNodes' ] );
        
        // Try to get data from web service
        if( $this->import->getData() ) {
            
            // Try to parse the XML data
            if( $this->import->parseXml() ) {
                
                // Show available municipalities
                return $this->showMunicipalities();
                
            } else {
                
                // Get XML exception infos
                $xmlErrorCode = ( int )$this->import->getSimpleXmlExceptionCode();
                $xmlErrorMsg  = $this->import->getSimpleXmlExceptionMsg();
                
                // Error message
                $error = $this->writeHtml( $this->writeHtml( $LANG->getLL( 'import.error.title' ), 'strong', 'typo3-red' ) )
                       . $this->writeHtml( sprintf( $LANG->getLL( 'import.error.xml' ), $xmlErrorCode ,$xmlErrorMsg ), 'div', 'typo3-red' );
            
                return $error;
            }
            
        } else {
            
            // Get HTTP error infos
            $httpStatus = ( int )$this->import->getResponseStatus();
            $httpMsg    = $this->import->getResponseMsg();
            
            // Error message
            $error = $this->writeHtml( $this->writeHtml( $LANG->getLL( 'import.error.title' ), 'strong', 'typo3-red' ) )
                   . $this->writeHtml( sprintf( $LANG->getLL( 'import.error.http' ), $httpStatus, $httpMsg ), 'div', 'typo3-red' );
            
            return $error;
        }
    }
    
    /**
     * Show the municipalities
     * 
     * This function will produce the list of the municipalities contained in
     * the data from the web service.
     * 
     * @return  string  The list of the municipalities
     * @see     importMunicipalities
     * @see     writeHtml
     */
    function showMunicipalities()
    {
        global $LANG;
        
        // Storage
        $htmlCode   = array();
        $rows       = array();
        
        // Title
        $htmlCode[] = $this->writeHtml( $this->writeHtml( $LANG->getLL( 'import.view.title' ), 'strong' ) );
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Check import action
        if( t3lib_div::_POST( 'submit' ) ) {
            
            // Import/Update municipalities
            $htmlCode[] = $this->importMunicipalities();
            $htmlCode[] = $this->doc->spacer( 10 );
            
        } else {
            
            // Import input
            $htmlCode[] = '<input name="submit" id="submit" type="submit" value="' . $LANG->getLL( 'import.submit' ) . '" />';
            $htmlCode[] = $this->doc->spacer( 10 );
        }
        
        // Start table
        $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
        
        // Headers style
        $headerStyle = array( 'font-weight: bold' );
        
        // Table headers
        $htmlCode[] = '<tr>';
        $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'municipality.id' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'municipality.name_lower' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'municipality.name_upper' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'municipality.district' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'municipality.state' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'municipality.surface' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'municipality.objectid' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'municipality.idex2000' ), 'td', false, $headerStyle );
        $htmlCode[] = '</tr>';
        
        // Status icons
        $iconOk     = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_ok2.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        $iconError  = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_fatalerror.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        
        // Process data from the web service
        foreach( $this->import as $key => $value ) {
            
            // Checks if the municipality is already in the database
            $inDatabase = t3lib_BEfunc::getRecordsByField(
                $this->extTable,
                'id_municipality',
                $key,
                'AND name_lower=' . $GLOBALS[ 'TYPO3_DB' ]->fullQuoteStr( $value[ 'nom_min' ], $this->extTable )
            );
            
            // TR parameters
            $trParams = ' bgcolor="' . $this->doc->bgColor4 . '"';
            
            // Storage
            $row   = array();
            
            // Status icon
            $icon = ( $inDatabase ) ? $iconOk : $iconError;
            
            // Add columns
            $row[] = $this->writeHtml( $icon, 'td');
            $row[] = $this->writeHtml( $key, 'td');
            $row[] = $this->writeHtml( $value[ 'nom_min' ], 'td' );
            $row[] = $this->writeHtml( $value[ 'nom_maj' ], 'td' );
            $row[] = $this->writeHtml( $value[ 'no_dis_can' ], 'td' );
            $row[] = $this->writeHtml( $value[ 'no_canton' ], 'td' );
            $row[] = $this->writeHtml( $value[ 'surface' ], 'td' );
            $row[] = $this->writeHtml( $value[ 'objectid' ], 'td' );
            $row[] = $this->writeHtml( $value[ 'idex2000' ], 'td' );
            
            // Add full row
            $rows[ $key ] = '<tr' . $trParams . '>' . implode( chr( 10 ), $row ) . '</tr>';
        }
        
        // Sort rows based on municipality name
        ksort( $rows );
        $htmlCode[] = implode( chr( 10 ), $rows );
        
        // End table
        $htmlCode[] = '</table>';
        
        // Return full content
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * Import or update the municipalities table with data from the web service.
     * 
     * This function will update existing records with the informations contained
     * in the web service (to avoid duplicates) and insert unexisting records.
     * 
     * @return  string  A status message
     * @see     writeHtml
     */
    function importMunicipalities()
    {
        global $LANG;
        
        // Process data from the web service
        foreach( $this->import as $key => $value ) {
            
            // Try to get an existing record
            $record = t3lib_BEfunc::getRecordsByField(
                $this->extTable,
                'id_municipality',
                $key
            );
            
            // Fields to import/update
            $fields = array(
                'tstamp'          => time(),
                'name_lower'      => $value[ 'nom_min' ],
                'name_lower15'    => $value[ 'n_a_min_15' ],
                'name_upper'      => $value[ 'nom_maj' ],
                'name_upper15'    => $value[ 'n_a_maj_15' ],
                'id_state'        => $value[ 'no_canton' ],
                'id_district'     => $value[ 'no_dis_can' ],
                'id_municipality' => $value[ 'no_com_can' ],
                'surface'         => $value[ 'surface' ],
                'objectid'        => $value[ 'objectid' ],
                'idex2000'        => $value[ 'idex2000' ]
            );
            
            // Checks for an existing record
            if( is_array( $record ) ) {
                
                // Update record with new infos
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery(
                    $this->extTable,
                    'uid=' . $record[ 0 ][ 'uid' ],
                    $fields
                );
                
            } else {
                
                // Add creation date and creation user
                $fields[ 'crdate' ]    = $fields[ 'tstamp' ];
                $fields[ 'cruser_id' ] = $GLOBALS[ 'BE_USER' ]->user[ 'uid' ];
                
                // Insert new record
                $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery(
                    $this->extTable,
                    $fields
                );
            }
        }
        
        // Return succes message
        return $this->writeHtml( $LANG->getLL( 'import.success' ), 'div', 'typo3-red' );
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_municipalities/mod1/index.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_municipalities/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance( 'tx_vdmunicipalities_module1' );
$SOBE->init();

// Include files
foreach( $SOBE->include_once as $INC_FILE ) {
    include_once( $INC_FILE );
}

// Launch module
$SOBE->main();
$SOBE->printContent();
