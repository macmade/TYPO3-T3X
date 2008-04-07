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
 * Plugin 'Gallery / macmade.net' for the 'eesp_ws_modules' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.1
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *        :     function main( $content, $conf )
 * 
 *              TOTAL FUNCTIONS: 
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

// Modules getters
require_once( t3lib_extMgm::extPath( 'eesp_ws_modules' ) . 'class.tx_eespwsmodules_listgetter.php' );
require_once( t3lib_extMgm::extPath( 'eesp_ws_modules' ) . 'class.tx_eespwsmodules_singlegetter.php' );

class tx_eespwsmodules_pi1 extends tslib_pibase
{
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_eespwsmodules_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_eespwsmodules_pi1.php';
    
    // The extension key
    var $extKey             = 'eesp_ws_modules';
    
    // Version of the Developer API required
    var $apimacmade_version = 4.0;
    
    // Check plugin hash
    var $pi_checkCHash      = true;
    
    // Configuration array
    var $conf               = array();
    
    // Plugin variables
    var $piVars             = array();
    
    // Instance of the Developer API
    var $api                = NULL;
    
    // Instance of the module getter
    var $modGetter          = NULL;
    
    // Storage for the modules
    var $modules            = array();
    
    // Storage for the module dates
    var $dates              = array();
    
    // Total number of modules
    var $modCount           = array();
    
    // Modules informations
    var $modInfos           = array();
    
    // New line character
    var $NL                 = '';
    
    // Collapse picture
    var $collapsePicture    = '';
    
    // Current URL
    var $url                = '';
    
    // Current date
    var $curDate            = '';
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin "tx_mozsearch_pi1", and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param       $content            The content object
     * @param       $conf               The TS setup
     * @return      The content of the plugin.
     */
    function main( &$content, &$conf )
    {
        // New instance of the macmade.net API
        $this->api =& tx_apimacmade::newInstance(
            'tx_apimacmade',
            array(
                &$this
            )
        );
        
        // Gets the current URL
        $this->url     = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        
        // Gets the current date
        $this->curDate = time();
        
        // Set default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Load locallang labels
        $this->pi_loadLL();
        
        // Sets the new line character
        $this->NL = chr( 10 );
        
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Checks some values from the configuration
        if( isset( $this->conf[ 'year' ] )
            && isset( $this->conf[ 'yearsNumber' ] )
            && isset( $this->conf[ 'defaultYear' ] )
            && isset( $this->conf[ 'wsdlUrl' ] )
            && isset( $this->conf[ 'soapOperationList' ] )
            && isset( $this->conf[ 'soapOperationSingle' ] )
            && isset( $this->conf[ 'templateFile' ] )
            && isset( $this->conf[ 'sections' ] )
            && isset( $this->conf[ 'modes' ] )
            && isset( $this->conf[ 'holidays' ] )
        ) {
            
            // Builds the view (single or list)
            $content = ( isset( $this->piVars[ 'showModule' ] ) ) ? $this->singleView() : $this->listView();
            
        } else {
            
            // Displays the error message
            $content = $this->api->fe_makeStyledContent(
                'div',
                'exception',
                $this->pi_getLL( 'ts-error' )
            );
        }
        
        // Returns the content
        return $this->pi_wrapInBaseClass( $content );
    }
    
    /**
     * Set configuration array
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     * 
     * @return      Void
     */
    function setConfig()
    {
        // Mapping array for PI flexform
        $flex2conf = array(
            'year'                 => 'sDEF:year',
            'yearsNumber'          => 'sDEF:years_number',
            'defaultYear'          => 'sDEF:default_year',
            'emptySearchAtStart'   => 'sDEF:empty_search',
            'endDateNbWeek'        => 'sDEF:end_date_nbweek',
            'startWithCurrentDate' => 'sDEF:current_date',
            'wsdlUrl'              => 'sWSDL:wsdl_url',
            'soapOperationList'    => 'sWSDL:soap_operation_list',
            'soapOperationSingle'  => 'sWSDL:soap_operation_single',
            'collapseListItems'    => 'sDISPLAY:collapse_list_items',
            'scriptaculous.'       => array(
                'appear' => 'sDISPLAY:appear',
                'fade'   => 'sDISPLAY:fade'
            )
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->conf,
            $this->piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'EESP / Modules (WSDL): configuration array');
    }
    
    /**
     * 
     */
    function listView()
    {
        // Initialize the template object
        $this->api->fe_initTemplate( $this->conf[ 'templateFile' ] );
        
        // Storage for the template markers
        $markers = array();
        
        // Includes the calendar script
        $this->includeCalendarScript();
        
        // Checks for the collapse option
        if( $this->conf[ 'collapseListItems' ] ) {
            
            // Include Scriptaculous
            $this->api->fe_includeScriptaculousJs();
            
            // Builds the appear scripts
            $this->buildAppearScript();
        }
        
        // Builds the option form
        $markers[ '###LIST_OPTIONS###' ] = $this->optionsForm();
        
        // Checks the display type
        if( isset( $this->piVars[ 'display' ] ) && $this->piVars[ 'display' ] == 1 ) {
            
            // Display modules by modules
            $listMethod     = 'modulesListByModule';
            
        } else {
            
            // Display modules by dates
            $listMethod     = 'modulesListByDate';
        }
        
        // Checks if the module list must be displayed
        if( $this->conf[ 'emptySearchAtStart' ]
            || isset( $this->piVars[ 'submit' ] )
        ) {
            
            try {
                
                // Initialize the SOAP operations
                $this->soapListRequest();
                
            } catch( Exception $e ) {
                
                // Displays the error message
                $content = $this->api->fe_makeStyledContent(
                    'div',
                    'exception',
                    sprintf(
                        $this->pi_getLL( 'exception' ),
                        $e->getMessage()
                    )
                );
                
                return $content;
            }
            
            // Builds the module list
            $modulesList = $this->$listMethod();
            $modCount    = $this->api->fe_makeStyledContent(
                                'div',
                                'modCount',
                                sprintf( $this->pi_getLL( 'modules-number' ), count( $this->modCount ) )
                           );
            
        } else {
            
            // Do not display the module list at first time
            $modulesList = '';
            $modCount    = '';
        }
        
        // Checks for modules
        if( count( $this->modCount ) > 0 ) {
            
            // Display the list of the modules
            $markers[ '###MODCOUNT###' ]    = $modCount;
            
            // Display the list of the modules
            $markers[ '###LIST_RESULT###' ] = $modulesList;
            
        } else {
            
            // No modules were found
            $markers[ '###MODCOUNT###' ]    = '';
            $markers[ '###LIST_RESULT###' ] = $modCount;
        }
        
        // Return content
        return $this->api->fe_renderTemplate( $markers, '###LIST###' );
    }
    
    /**
     * 
     */
    function singleView()
    {
         // Initialize the template object
        $this->api->fe_initTemplate( $this->conf[ 'templateFile' ] );
        
        // Storage for the template markers
        $markers = array();
        
        // Includes the calendar script
        $this->includeCalendarScript();
        
        // Checks for the collapse option
        if( $this->conf[ 'collapseListItems' ] ) {
            
            // Include Scriptaculous
            $this->api->fe_includeScriptaculousJs();
            
            // Builds the appear scripts
            $this->buildAppearScript();
        }
            
        try {
            
            // Initialize the SOAP operations
            $this->soapListRequest();
            $this->soapSingleRequest();
            
        } catch( Exception $e ) {
            
            // Displays the error message
            $content = $this->api->fe_makeStyledContent(
                'div',
                'exception',
                sprintf(
                    $this->pi_getLL( 'exception' ),
                    $e->getMessage()
                )
            );
            
            return $content;
        }
        
        // Builds the option form
        $markers[ '###SINGLE_OPTIONS###' ] = $this->optionsForm();
        
        // Shows the details
        $markers[ '###MODULE###' ]         = $this->showModule();
        
        // Shows the dates
        $markers[ '###DATES###' ]          = $this->modulesListByDate();
        
        // Return content
        return $this->api->fe_renderTemplate( $markers, '###SINGLE###' );
    }
    
    /**
     * 
     */
    function soapListRequest()
    {
        // Checks the display mode
        if( isset( $this->piVars[ 'display' ] ) && $this->piVars[ 'display' ] == 1 ) {
            
            // Method to use to get the modules
            $getModulesMethod      = 'getModulesByModule';
            
            // Argument to pass to the helper class
            $helperClassDisplayArg = true;
            
        } else {
            
            // Method to use to get the modules
            $getModulesMethod      = 'getModulesByDate';
            
            // Argument to pass to the helper class
            $helperClassDisplayArg = false;
        }
        
        try {
            
            // Gets an instance of the module getter
            $this->modGetter =& $this->api->newInstance(
                'tx_eespwsmodules_listGetter',
                array(
                    $this->conf[ 'wsdlUrl' ],
                    $this->conf[ 'soapOperationList' ],
                    $helperClassDisplayArg
                )
            );
            
            $startDate = '';
            $endDate   = '';
            
            if( $this->conf[ 'startWithCurrentDate' ] ) {
                
                // Start date
                $startDate = date( $this->conf[ 'dateFormat' ], $this->curDate );
                
                if( $this->conf[ 'endDateNbWeek' ] ) {
                    
                    // End date
                    $endDate = date(
                        $this->conf[ 'dateFormat' ],
                        $this->curDate + ( $this->conf[ 'endDateNbWeek' ] * ( 3600 * 24 * 7 ) )
                    );
                }
            }
            
            // Gets values from the option form
            $year    = ( isset( $this->piVars[ 'year' ] ) )       ? $this->piVars[ 'year' ]       : $this->conf[ 'defaultYear' ];
            $section = ( isset( $this->piVars[ 'section' ] ) )    ? $this->piVars[ 'section' ]    : '';
            $mode    = ( isset( $this->piVars[ 'mode' ] ) )       ? $this->piVars[ 'mode' ]       : '';
            $module  = ( isset( $this->piVars[ 'showModule' ] ) ) ? $this->piVars[ 'showModule' ] : '';
            $start   = ( isset( $this->piVars[ 'start' ] ) )      ? $this->piVars[ 'start' ]      : $startDate;
            $end     = ( isset( $this->piVars[ 'end' ] ) )        ? $this->piVars[ 'end' ]        : $endDate;
            
            // Sets the SOAP parameters
            $this->modGetter->setSoapArg( 'FourD_arg1', $year );
            $this->modGetter->setSoapArg( 'FourD_arg2', $section );
            $this->modGetter->setSoapArg( 'FourD_arg3', $mode );
            $this->modGetter->setSoapArg( 'FourD_arg4', $module );
            $this->modGetter->setSoapArg( 'FourD_arg5', $start );
            $this->modGetter->setSoapArg( 'FourD_arg6', $end );
            
            // Initialization of the SOAP request
            $this->modGetter->soapRequest();
            
            // Gets the modules
            $this->$getModulesMethod();
        
            // Removes the modules getter to free some memory, as it's not needed anymore
            unset( $this->modGetter );
            
        } catch( Exception $e ) {
            
            throw $e;
        }
    }
    
    /**
     * 
     */
    function soapSingleRequest()
    {
        try {
            
            // Gets an instance of the module getter
            $this->modGetter =& $this->api->newInstance(
                'tx_eespwsmodules_singleGetter',
                array(
                    $this->conf[ 'wsdlUrl' ],
                    $this->conf[ 'soapOperationSingle' ]
                )
            );
            
            // Sets the SOAP parameters
            $this->modGetter->setSoapArg( 'FourD_arg1', $this->piVars[ 'showModule' ] );
            $this->modGetter->setSoapArg( 'FourD_arg2', '' );
            
            // Initialization of the SOAP request
            $this->modGetter->soapRequest();
            
        } catch( Exception $e ) {
            
            throw $e;
        }
    }
    
    /**
     * 
     */
    function getModulesByDate()
    {
        // Process each module returned by the module getter
        foreach( $this->modGetter as $id => $date ) {
            
            // Gets the date informations
            $year     = date( 'Y', $date );
            $month    = date( 'n', $date );
            $week     = date( 'W', $date );
            $day      = date( 'w', $date );
            $meridiem = date( 'a', $date );
            
            // Checks the storage place for the current year
            if( !isset( $this->dates[ $year ] ) ) {
                
                // Creates the storage place
                $this->dates[ $year ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Gets a reference to the entries
                $yearArray =& $this->dates[ $year ][ 'entries' ];
            }
            
            // Checks the storage place for the current month
            if( !isset( $yearArray[ $month ] ) ) {
                
                // Creates the storage place
                $yearArray[ $month ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Gets a reference to the entries
                $monthArray =& $yearArray[ $month ][ 'entries' ];
            }
            
            // Checks the storage place for the current week
            if( !isset( $monthArray[ $week ] ) ) {
                
                // Creates the storage place
                $monthArray[ $week ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Gets a reference to the entries
                $weekArray =& $monthArray[ $week ][ 'entries' ];
            }
            
            // Checks the storage place for the current day
            if( !isset( $weekArray[ $day ] ) ) {
                
                // Creates the storage place
                $weekArray[ $day ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Gets a reference to the entries
                $dayArray =& $weekArray[ $day ][ 'entries' ];
            }
            
            // Checks the storage place for the current meridiem
            if( !isset( $dayArray[ $meridiem ] ) ) {
                
                // Creates the storage place
                $dayArray[ $meridiem ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Gets a reference to the entries
                $meridiemArray =& $dayArray[ $meridiem ][ 'entries' ];
            }
            
            // Checks if the module already exists
            if( !isset( $this->modules[ $id ] ) ) {
                
                // Stores the module informations
                $this->modules[ $id ] = array(
                    'number'   => $this->modGetter->number,
                    'credits'  => $this->modGetter->credits,
                    'domain'   => $this->modGetter->domain,
                    'section'  => $this->modGetter->section,
                    'type'     => $this->modGetter->type,
                    'incharge' => $this->modGetter->incharge,
                    'title'    => $this->modGetter->title
                );
            }
            
            // Store the date's specific informations
            $meridiemArray[ $id ] =  array(
                'comments' => $this->modGetter->comments,
                'date'     => $date
            );
            
            // Adds a reference to the common module informations
            $meridiemArray[ $id ][ 'common' ] =& $this->modules[ $id ];
            
            // Increase all counters
            $this->modCount[ $id ] = true;
            $this->dates[ $year ][ 'count' ]++;
            $this->dates[ $year ][ 'entries' ][ $month ][ 'count' ]++;
            $this->dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'count' ]++;
            $this->dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ][ 'count' ]++;
            $this->dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ][ 'entries' ][ $meridiem ][ 'count' ]++;
        }
        
        return true;
    }
    
    function getModulesByModule()
    {
        // Process each module returned by the module getter
        foreach( $this->modGetter as $id => $date ) {
            
            // Stores the module informations
            $this->modules[ $id ] = array(
                'common' => array(
                    'number'   => $this->modGetter->number,
                    'credits'  => $this->modGetter->credits,
                    'domain'   => $this->modGetter->domain,
                    'section'  => $this->modGetter->section,
                    'type'     => $this->modGetter->type,
                    'incharge' => $this->modGetter->incharge,
                    'title'    => $this->modGetter->title
                ),                
                'dates'    => $date,
                'comments' => $this->modGetter->comments
            );
            
            // Counter for modules
            $this->modCount[ $id ] = true;
        }
        
        return true;
    }
    
    /**
     * 
     */
    function buildAppearScript()
    {
        // Storage
        $script = array();
        
        // JavaScript code
        $script[] = '<script type="text/javascript" charset="utf-8">';
        $script[] = '// <![CDATA[';
        $script[] = '';
        $script[] = '   // Storage for infos DIVs';
        $script[] = '   var tx_eexpwsmodules_pi1_infosDivs = new Array();';
        $script[] = '   ';
        $script[] = '   /**';
        $script[] = '    * Shows an info DIV';
        $script[] = '    * ';
        $script[] = '    * @param   int     id      The ID of the DIV';
        $script[] = '    * @return  void';
        $script[] = '    */';
        $script[] = '   function tx_eespwsmodules_pi1_showInfoDiv( id )';
        $script[] = '   {';
        $script[] = '       // Gets the element ID';
        $script[] = '       var elementId = "' . $this->prefixId . '_modInfos_" + id';
        $script[] = '       ';
        $script[] = '       // Checks if the DIV has already been processed';
        $script[] = '       if( tx_eexpwsmodules_pi1_infosDivs[ id ] ) {';
        $script[] = '           ';
        $script[] = '           // Fade effect';
        $script[] = '           Effect.' . $this->conf[ 'scriptaculous.' ][ 'fade' ] . '( elementId );';
        $script[] = '           ';
        $script[] = '           // Sets the display flag';
        $script[] = '           tx_eexpwsmodules_pi1_infosDivs[ id ] = false;';
        $script[] = '           ';
        $script[] = '       } else {';
        $script[] = '           ';
        $script[] = '           // Appear effect';
        $script[] = '           Effect.' . $this->conf[ 'scriptaculous.' ][ 'appear' ] . '( elementId );';
        $script[] = '           ';
        $script[] = '           // Sets the display flag';
        $script[] = '           tx_eexpwsmodules_pi1_infosDivs[ id ] = true;';
        $script[] = '       }';
        $script[] = '   }';
        $script[] = '// ]]>';
        $script[] = '</script>';
        
        // Adds the script to the TYPO3 headers
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_eespwsmodules_pi1_appearScript' ] = implode( $this->NL, $script );
        
        return true;
    }
    
    /**
     * 
     */
    function moduleInfos( $id, &$module, $byModule = false )
    {
        // Suffix for the template sections
        $tmplSuffix = ( $byModule ) ? '_BYMOD' : '_BYDATE';
        
        // Storage for the template markers
        $markers    = array();
        
        // Checks if the module is an holiday module
        if( ( int )$module[ 'common' ][ 'number' ] === ( int )$this->conf[ 'holidays' ] ) {
            
            // Sets the markers
            $markers[ '###TITLE_LABEL###' ] = $this->pi_getLL( 'label-title' );
            $markers[ '###TITLE_VALUE###' ] = $this->api->fe_makeStyledContent(
                'span',
                'module-title',
                $module[ 'common' ][ 'title' ]
            );
            
            // Sets the template section
            $templateSection                = '###LIST_MODINFOS_HOLIDAYS###';
            
        } else {
            
            // Checks if the infos for the current module already exists
            if( !isset( $this->modInfos[ $id ] ) ) {
                
                // Gets the common markers
                $this->modInfos[ $id ] = $this->moduleInfosMarkers( $id, $module );
            }
            
            // Gets a reference to the markers
            $markers = $this->modInfos[ $id ];
            
            // Specific markers for the view by date
            if( !$byModule ) {
                
                // Checks the value for comments
                if( $module[ 'comments' ] && !is_array( $module[ 'comments' ] ) ) {
                    
                    // Adds the comments for the current date
                    $markers[ '###COMMENTS_VALUE###' ] = $this->api->fe_makeStyledContent( 'span', 'module-comments', $module[ 'comments' ] );
                    $markers[ '###COMMENTS_LABEL###' ] = $this->pi_getLL( 'label-comments' );
                    
                } else {
                    
                    // Adds the comments for the current date
                    $markers[ '###COMMENTS_VALUE###' ] = '';
                    $markers[ '###COMMENTS_LABEL###' ] = '';
                }
            }
            
            // Specific markers for the view by module
            if( $byModule ) {
                
                // Adds the dates for the current module
                $markers[ '###DATES_LABEL###' ] = $this->pi_getLL( 'label-dates' );
                $markers[ '###DATES_VALUE###' ] = $this->moduleInfosDateTable( $module[ 'dates' ], $module[ 'comments' ] );
            }
            
            // Checks if the collapse option is set
            if( $this->conf[ 'collapseListItems' ] ) {
                
                // Checks if the collapse picture needs to be processed
                if( !$this->collapsePicture ) {
                    
                    // Builds the collapse picture
                    $this->collapsePicture = $this->cObj->IMAGE( $this->conf[ 'collapsePicture.' ] );
                }
                
                // ID for the info DIV
                $infosDivId = microtime( true ) * 10000;
                
                // Adds the collapse picture with the link
                $markers[ '###COLLAPSE_PICTURE###' ] = '<a href="javascript:tx_eespwsmodules_pi1_showInfoDiv( '
                                                     . $infosDivId
                                                     . ' );" title="'
                                                     . $this->pi_getLL( 'collapse-title' )
                                                     . '">'
                                                     . $this->collapsePicture
                                                     . '</a>';
                
                // TypoLink for the module view
                $typoLink = array(
                    'parameter'        => $GLOBALS[ 'TSFE' ]->id,
                    'useCacheHash'     => 1,
                    'title'            => $module[ 'common' ][ 'number' ],
                    'additionalParams' => $this->api->fe_typoLinkParams(
                        array(
                            'showModule' => $id
                        )
                    )
                );
                
                // Adds the link
                $markers[ '###LINK###' ] = $this->cObj->typoLink(
                    $this->api->fe_renderTemplate(
                        $markers,
                        '###LIST_MODINFOS_COLLAPSE_LINK' . $tmplSuffix . '###'
                    ),
                    $typoLink
                );
                
                // Adds the collapsed content
                $markers[ '###COLLAPSE_CONTENT###' ] = $this->api->fe_makeStyledContent(
                    'div',
                    'module-infoDiv',
                    $this->api->fe_renderTemplate(
                        $markers,
                        '###LIST_MODINFOS_COLLAPSE_CONTENT' . $tmplSuffix . '###'
                    ),
                    1,
                    false,
                    false,
                    array(
                        'id'    => $this->prefixId . '_modInfos_' . $infosDivId,
                        'style' => 'display: none'
                    )
                );
                
                // Sets the template section
                $templateSection = '###LIST_MODINFOS_COLLAPSE' . $tmplSuffix . '###';
                
            } else {
                
                // TypoLink for the module view
                $typoLink = array(
                    'parameter'        => $GLOBALS[ 'TSFE' ]->id,
                    'useCacheHash'     => 1,
                    'title'            => $module[ 'common' ][ 'number' ],
                    'additionalParams' => $this->api->fe_typoLinkParams(
                        array(
                            'showModule' => $id
                        )
                    )
                );
                
                // Adds the link
                $markers[ '###LINK###' ] = $this->cObj->typoLink(
                    $this->api->fe_renderTemplate( $markers, '###LIST_MODINFOS_LINK' . $tmplSuffix . '###' ),
                    $typoLink
                );
                
                // Sets the template section
                $templateSection = '###LIST_MODINFOS' . $tmplSuffix . '###';
            }
        }
        
        // Returns the info div
        return $this->api->fe_makeStyledContent( 'div', 'module-infos', $this->api->fe_renderTemplate( $markers, $templateSection ) );
    }
    
    /**
     * 
     */
    function moduleInfosMarkers( $id, &$module )
    {
        // Storage for the template markers
        $markers = array();
        
        // Sets the values
        $markers[ '###TITLE_VALUE###' ]    = $this->api->fe_makeStyledContent( 'span', 'module-title',    $module[ 'common' ][ 'title' ] );
        $markers[ '###NUMBER_VALUE###' ]   = $this->api->fe_makeStyledContent( 'span', 'module-number',   $module[ 'common' ][ 'number' ] );
        $markers[ '###CREDITS_VALUE###' ]  = $this->api->fe_makeStyledContent( 'span', 'module-credits',  $module[ 'common' ][ 'credits' ] );
        $markers[ '###DOMAIN_VALUE###' ]   = $this->api->fe_makeStyledContent( 'span', 'module-domain',   $module[ 'common' ][ 'domain' ] );
        $markers[ '###SECTION_VALUE###' ]  = $this->api->fe_makeStyledContent( 'span', 'module-section',  $module[ 'common' ][ 'section' ] );
        $markers[ '###TYPE_VALUE###' ]     = $this->api->fe_makeStyledContent( 'span', 'module-type',     $module[ 'common' ][ 'type' ] );
        $markers[ '###INCHARGE_VALUE###' ] = $this->api->fe_makeStyledContent( 'span', 'module-incharge', implode( ', ', $module[ 'common' ][ 'incharge' ] ) );
        
        // Sets the labels
        $markers[ '###TITLE_LABEL###' ]    = $this->pi_getLL( 'label-title' );
        $markers[ '###NUMBER_LABEL###' ]   = $this->pi_getLL( 'label-number' );
        $markers[ '###CREDITS_LABEL###' ]  = $this->pi_getLL( 'label-credits' );
        $markers[ '###DOMAIN_LABEL###' ]   = $this->pi_getLL( 'label-domain' );
        $markers[ '###SECTION_LABEL###' ]  = $this->pi_getLL( 'label-section' );
        $markers[ '###TYPE_LABEL###' ]     = $this->pi_getLL( 'label-type' );
        $markers[ '###INCHARGE_LABEL###' ] = $this->pi_getLL( 'label-incharge' );
        
        // Return the markers
        return $markers;
    }
    
    /**
     * 
     */
    function moduleInfosDateTable( &$dates, &$comments )
    {
        // Checks the input arrays
        if( is_array( $dates ) && is_array( $comments ) && count( $dates ) ) {
            
            // Storage
            $htmlCode = array();
            
            // Starts the table
            $htmlCode[] = $this->api->fe_makeStyledContent(
                'table',
                'calendar-table',
                false,
                1,
                false,
                true,
                array(
                    'border'      => '0',
                    'width'       => '100%',
                    'cellspacing' => '0',
                    'cellpadding' => '0',
                    'align'       => 'center'
                )
            );
            
            // Starts the table header
            $htmlCode[] = '<thead>';
            $htmlCode[] = '<tr>';
            
            // Writes the headers
            $htmlCode[] = $this->api->fe_makeStyledContent( 'th', 'date', $this->pi_getLL( 'label-dates' ) );
            
            // Ends the table headers
            $htmlCode[] = '</tr>';
            $htmlCode[] = '</thead>';
            
            // Starts the table body
            $htmlCode[] = '<tbody>';
            
            // Process each date
            foreach( $dates as $ts ) {
                
                // Starts the row
                $htmlCode[] = '<tr>';
                
                // Comments for the current date
                $comment  = ( isset( $comments[ $ts ] ) && $comments[ $ts ] ) ? $this->api->fe_makeStyledContent( 'div', 'comment', $comments[ $ts ] ) : '';
                
                // Full date
                $fullDate = strftime( $this->conf[ 'dateFormatStrftime' ], $ts ) . ' - ' . $this->pi_getLL( date( 'a', $ts ) );
                
                // Current date
                $date = $this->api->fe_makeStyledContent(
                    'div',
                    'date',
                    $fullDate
                );
                
                // Writes the date
                $htmlCode[] = $this->api->fe_makeStyledContent(
                    'td',
                    'date',
                    $date . $comment
                );
                
                // Ends the row
                $htmlCode[] = '<tr>';
            }
            
            // Ends the table
            $htmlCode[] = '</tbody>';
            $htmlCode[] = '</table>';
            
            // Returns the date table
            return $this->api->fe_makeStyledContent( 'div', 'module-infos-dates', implode( $this->NL, $htmlCode ) );
        }
    }
    
    /**
     * 
     */
    function includeCalendarScript()
    {
        // Container for the calendar scripts
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_eespwsmodules_pi1_calendar' ] = '';
        
        // Gets a reference to the container
        $js =& $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_eespwsmodules_pi1_calendar' ]; 
        
        // Adds the calendar script
        $js .= '<script src="'
            .  t3lib_extMgm::siteRelPath( $this->extKey )
            .  'res/calendar/calendar.js'
            .  '" type="text/javascript"></script>';
        
        // Default language
        $siteLang = 'en';
        
        // Checks for a TS defined language
        if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'config.' ][ 'language' ] ) ) {
            
            // Gets the website language
            $siteLang = $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'config.' ][ 'language' ];
        }
        
        // Gets the path of the lang file
        $langFile = t3lib_extMgm::extPath( $this->extKey )
                  . 'res/calendar/calendar-'
                  . $siteLang
                  . '.js';
        
        // Checks if the language file exists
        if( @file_exists( $langFile ) ) {
            
            // Adds the language file
            $js .= $this->NL
                .  '<script src="'
                .  t3lib_extMgm::siteRelPath( $this->extKey )
                .  'res/calendar/calendar-'
                .  $siteLang
                .  '.js'
                .  '" type="text/javascript"></script>';
            
        } else {
            
            // Adds the language file
            $js .= $this->NL
                .  '<script src="'
                .  t3lib_extMgm::siteRelPath( $this->extKey )
                .  'res/calendar/calendar-en.js'
                .  '" type="text/javascript"></script>';
        }
        
        // Adds the setup file
        $js .= $this->NL
            .  '<script src="'
            .  t3lib_extMgm::siteRelPath( $this->extKey )
            .  'res/calendar/calendar-setup.js'
            .  '" type="text/javascript"></script>';
        
        return true;
    }
    
    /**
     * 
     */
    function calendarField( $name, $defaultValue = '' )
    {
        // ID of the input field
        $inputId = $this->prefixId . '_' . $name;
        
        // ID of the picture
        $picId   = $this->prefixId . '_' . $name . '_pic';
        
        // Input value
        $value   = ( isset( $this->piVars[ $name ] ) ) ? $this->piVars[ $name ] : $defaultValue;
        
        // Input tag
        $input   = '<input name="'
                 . $this->prefixId
                 . '['
                 . $name
                 . ']" id="'
                 . $inputId
                 . '" type="text" size="'
                 . $this->conf[ 'calendar.' ][ 'inputSize' ]
                 . '" readonly="readonly" value="'
                 . $value
                 . '"/>';
        
        // Parameters for the picture
        $this->conf[ 'calendar.' ][ 'picture.' ][ 'titleText' ] = $this->pi_getLL( 'cal-title' );
        $this->conf[ 'calendar.' ][ 'picture.' ][ 'params' ]    = 'style="cursor: pointer;" id="'
                                                                . $picId
                                                                . '"';
        
        // Builds the picture
        $picture = $this->cObj->IMAGE( $this->conf[ 'calendar.' ][ 'picture.' ] );
        
        // Builds the delete picture
        $delete  = '<a href="'
                 . $this->url
                 . '#" onclick="javascript:document.getElementById(\''
                 . $inputId
                 . '\').value=\'\'; return true;">'
                 . $this->cObj->IMAGE( $this->conf[ 'calendar.' ][ 'deletePicture.' ] )
                 . '</a>';
        
        // Builds the script for the current field
        $script  = '<script type="text/javascript" charset="utf-8">'
                 . $this->NL
                 . '// <![CDATA['
                 . $this->NL
                 . 'Calendar.setup('
                 . $this->NL
                 . '{'
                 . $this->NL
                 . 'inputField  : "'
                 . $inputId
                 . '",'
                 . $this->NL
                 . 'ifFormat    : "'
                 . $this->conf[ 'calendar.' ][ 'format' ]
                 . '",'
                 . $this->NL
                 . 'button      : "'
                 . $picId
                 . '",'
                 . $this->NL
                 . 'align       : "'
                 . $this->conf[ 'calendar.' ][ 'align' ]
                 . '",'
                 . $this->NL
                 . 'singleClick : true'
                 . $this->NL
                 . '}'
                 . $this->NL
                 . ');'
                 . $this->NL
                 . '// ]]>'
                 . $this->NL
                 . '</script>';
        
        // Returns the input
        return $input
             . $this->NL
             . $picture
             . $this->NL
             . $delete
             . $this->NL
             . $script;
    }
    
    /**
     * 
     */
    function yearSelect()
    {
        // Storage
        $select = array();
        
        // Starts the select tag
        $select[] = '<select name="'
                  . $this->prefixId
                  . '[year]" size="1">';
        
        // Gets the start and end years
        $startYear = ( int )$this->conf[ 'year' ] - ( int )$this->conf[ 'yearsNumber' ];
        $endYear   = ( int )$this->conf[ 'year' ];
        
        // Process each year
        for( $i = $startYear; $i <= $endYear; $i++ ) {
            
            // Checks for incoming value from the plugin variables
            if( isset( $this->piVars[ 'year' ] ) ) {
                
                // Selected item state
                $selected = ( $i === ( int )$this->piVars[ 'year' ] ) ? ' selected="selected"' : '';
                
            } else {
                
                // Selected item state
                $selected = ( $i === ( int )$this->conf[ 'defaultYear' ] ) ? ' selected="selected"' : '';
                
            }
            
            // Adds the option tag
            $select[] = '<option value="'
                      . $i
                      . '"'
                      . $selected
                      . '>'
                      . $i
                      . '</option>';
        }
        
        // Ends the select tag
        $select[] = '</select>';
        
        // Returns the full select
        return implode( chr( 10 ), $select );
    }
    
    /**
     * 
     */
    function displaySelect()
    {
        // Storage
        $select = array();
        
        // Starts the select tag
        $select[] = '<select name="'
                  . $this->prefixId
                  . '[display]" size="1">';
        
        // Process each display options
        for( $i = 0; $i < 2; $i++ ) {
            
            // Checks for incoming value from the plugin variables
            if( isset( $this->piVars[ 'display' ] ) ) {
                
                // Selected item state
                $selected = ( $i === ( int )$this->piVars[ 'display' ] ) ? ' selected="selected"' : '';
                
            }
            
            // Adds the option tag
            $select[] = '<option value="'
                      . $i
                      . '"'
                      . $selected
                      . '>'
                      . $this->pi_getLL( 'options-display-' . $i )
                      . '</option>';
        }
        
        // Ends the select tag
        $select[] = '</select>';
        
        // Returns the full select
        return implode( chr( 10 ), $select );
    }
    
    /**
     * 
     */
    function sectionSelect()
    {
        // Storage
        $select = array();
        
        // Starts the select tag
        $select[] = '<select name="'
                  . $this->prefixId
                  . '[section]" size="1">';
        
        // Gets the available sections
        $sections = explode( ',', $this->conf[ 'sections' ] );
        
        // Adds a blank option
        $select[] = '<option value=""></option>';
        
        // Process each display options
        foreach( $sections as $section ) {
            
            // Checks for incoming value from the plugin variables
            if( isset( $this->piVars[ 'section' ] ) ) {
                
                // Selected item state
                $selected = ( $section === $this->piVars[ 'section' ] ) ? ' selected="selected"' : '';
                
            }
            
            // Adds the option tag
            $select[] = '<option value="'
                      . $section
                      . '"'
                      . $selected
                      . '>'
                      . $section
                      . '</option>';
        }
        
        // Ends the select tag
        $select[] = '</select>';
        
        // Returns the full select
        return implode( chr( 10 ), $select );
    }
    
    /**
     * 
     */
    function modeSelect()
    {
        // Storage
        $select = array();
        
        // Starts the select tag
        $select[] = '<select name="'
                  . $this->prefixId
                  . '[mode]" size="1">';
        
        // Gets the available modes
        $modes = explode( ',', $this->conf[ 'modes' ] );
        
        // Adds a blank option
        $select[] = '<option value=""></option>';
        
        // Process each display options
        foreach( $modes as $mode ) {
            
            // Checks for incoming value from the plugin variables
            if( isset( $this->piVars[ 'mode' ] ) ) {
                
                // Selected item state
                $selected = ( $mode === $this->piVars[ 'mode' ] ) ? ' selected="selected"' : '';
                
            }
            
            // Adds the option tag
            $select[] = '<option value="'
                      . $mode
                      . '"'
                      . $selected
                      . '>'
                      . $mode
                      . '</option>';
        }
        
        // Cleanup
        unset( $i );
        
        // Ends the select tag
        $select[] = '</select>';
        
        // Returns the full select
        return implode( chr( 10 ), $select );
        
        // Cleanup
        unset( $key );
        unset( $value );
    }
    
    /**
     * 
     */
    function optionsForm()
    {
        // Storage
        $htmlCode = array();
        $markers  = array();
        
        // TypoLink configuration for the form action
        $typoLink   = array(
            'parameter'    => $GLOBALS[ 'TSFE' ]->id,
            'useCacheHash' => 1
        );
            
        $startDate = '';
        $endDate   = '';
        
        if( $this->conf[ 'startWithCurrentDate' ] ) {
            
            // Start date
            $startDate = date( $this->conf[ 'dateFormat' ], $this->curDate );
            
            if( $this->conf[ 'endDateNbWeek' ] ) {
                
                // End date
                $endDate = date(
                    $this->conf[ 'dateFormat' ], 
                    $this->curDate + ( $this->conf[ 'endDateNbWeek' ] * ( 3600 * 24 * 7 ) )
                );
            }
        }
        
        // Gets the form action URL
        $formAction = $this->cObj->typoLink_URL( $typoLink );
        
        // Starts the form tag
        $htmlCode[] = '<form action="'
                    . $formAction
                    . '" method="post" enctype="'
                    . $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ]
                    . '" id="'
                    . $this->prefixId
                    . '_optionsForm'
                    . '" name="'
                    . $this->prefixId
                    . '_optionsForm'
                    . '">';
        
        // Adds the year select
        $markers[ '###YEAR###' ] = $this->api->fe_makeStyledContent(
            'div',
            'years',
            '<label>' . $this->pi_getLL( 'options-year' ) . '</label> ' . $this->yearSelect()
        );
        
        // Adds the display select
        $markers[ '###DISPLAY###' ] = $this->api->fe_makeStyledContent(
            'div',
            'display',
            '<label>' . $this->pi_getLL( 'options-display' ) . '</label> ' . $this->displaySelect()
        );
        
        // Adds the section select
        $markers[ '###SECTION###' ] = $this->api->fe_makeStyledContent(
            'div',
            'sections',
            '<label>' . $this->pi_getLL( 'options-sections' ) . '</label> ' . $this->sectionSelect()
        );
        
        // Adds the mode select
        $markers[ '###MODE###' ] = $this->api->fe_makeStyledContent(
            'div',
            'modes',
            '<label>' . $this->pi_getLL( 'options-modes' ) . '</label> ' . $this->modeSelect()
        );
        
        // Adds the start calendar field
        $markers[ '###START###' ] = $this->api->fe_makeStyledContent(
            'div',
            'startDate',
            '<label>' . $this->pi_getLL( 'options-start' ) . '</label> ' . $this->calendarField( 'start', $startDate )
        );
        
        // Adds the end calendar field
        $markers[ '###END###' ] = $this->api->fe_makeStyledContent(
            'div',
            'endDate',
            '<label>' . $this->pi_getLL( 'options-end' ) . '</label> ' . $this->calendarField( 'end', $endDate )
        );
        
        // Adds the form submit input
        $markers[ '###SUBMIT###' ] = $this->api->fe_makeStyledContent(
            'div',
            'submit',
            '<input name="' . $this->prefixId . '[submit]" type="submit" value="' . $this->pi_getLL( 'options-submit' ) . '" />'
        );
        
        // Adds the form fields
        $htmlCode[] = $this->api->fe_renderTemplate( $markers, '###OPTIONS###' );
        
        // Close the form tag
        $htmlCode[] = '</form>';
        
        // Returns the form
        return $this->api->fe_makeStyledContent(
            'div',
            'options',
            implode( $this->NL, $htmlCode )
        );
    }
    
    function modulesListByDate()
    {
        // Storage
        $htmlCode = array();
        
        // Starts the table
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'table',
            'calendar-table',
            false,
            1,
            false,
            true,
            array(
                'border'      => '0',
                'width'       => '100%',
                'cellspacing' => '0',
                'cellpadding' => '0',
                'align'       => 'center'
            )
        );
        
        // Starts the table header
        $htmlCode[] = '<thead>';
        $htmlCode[] = '<tr>';
        
        // Writes the headers
        $htmlCode[] = $this->api->fe_makeStyledContent( 'th', 'month',    $this->pi_getLL( 'headers-month' ) );
        $htmlCode[] = $this->api->fe_makeStyledContent( 'th', 'week',     $this->pi_getLL( 'headers-week' ) );
        $htmlCode[] = $this->api->fe_makeStyledContent( 'th', 'day',      $this->pi_getLL( 'headers-day' ) );
        $htmlCode[] = $this->api->fe_makeStyledContent( 'th', 'meridiem', $this->pi_getLL( 'headers-meridiem' ) );
        $htmlCode[] = $this->api->fe_makeStyledContent( 'th', 'modules',  $this->pi_getLL( 'headers-modules' ) );
        
        // Ends the table headers
        $htmlCode[] = '</tr>';
        $htmlCode[] = '</thead>';
        
        // Starts the table body
        $htmlCode[] = '<tbody>';
        
        // Process each year in the module array
        foreach( $this->dates as $year => &$monthArray ) {
            
            // Adds the year separation
            $htmlCode[] = '<tr>'
                        . $this->api->fe_makeStyledContent(
                            'td',
                            'year-separation',
                            $year,
                            1,
                            false,
                            false,
                            array(
                                'colspan' => '5'
                            )
                          )
                        . '</tr>';
            
            // Process each month
            foreach( $monthArray[ 'entries' ] as $month => &$weeksArray ) {
                
                // Gets the month label
                $monthLabel = $this->pi_getLL( 'month-' . $month );
                
                // Sets the new month flag
                $newMonth   = true;
                
                // Process each week
                foreach( $weeksArray[ 'entries' ] as $week => &$daysArray ) {
                    
                    // Sets the new week flag
                    $newWeek = true;
                    
                    // Process each day
                    foreach( $daysArray[ 'entries' ] as $day => &$meridiemArray ) {
                        
                        // Gets the day label
                        $dayLabel = $this->pi_getLL( 'day-' . $day );
                        
                        // Sets the new day flag
                        $newDay   = true;
                        
                        // Process each meridiem
                        foreach( $meridiemArray[ 'entries' ] as $meridiem => &$moduleArray ) {
                            
                            // Gets the meridiem label
                            $meridiemLabel = $this->pi_getLL( 'meridiem-' . $meridiem );
                            
                            // Sets the new meridiem flag
                            $newMeridiem   = true;
                            
                            // Process each module
                            foreach( $moduleArray[ 'entries' ] as $id => &$module ) {
                                
                                // Starts a table row
                                $htmlCode[] = '<tr>';
                                
                                // Checks for a new month
                                if( $newMonth ) {
                                    
                                    // Adds the month column
                                    $htmlCode[] = $this->api->fe_makeStyledContent(
                                        'td',
                                        'month',
                                        $monthLabel,
                                        1,
                                        false,
                                        false,
                                        array(
                                            'rowspan' => $weeksArray[ 'count' ]
                                        )
                                    );
                                    
                                    // Resets the new month flag
                                    $newMonth = false;
                                }
                                
                                // Checks for a new week
                                if( $newWeek ) {
                                    
                                    // Adds the week column
                                    $htmlCode[] = $this->api->fe_makeStyledContent(
                                        'td',
                                        'week',
                                        $week,
                                        1,
                                        false,
                                        false,
                                        array(
                                            'rowspan' => $daysArray[ 'count' ]
                                        )
                                    );
                                    
                                    // Resets the new week flag
                                    $newWeek = false;
                                }
                                
                                // Checks for a new day
                                if( $newDay ) {
                                    
                                    // Adds the day column
                                    $htmlCode[] = $this->api->fe_makeStyledContent(
                                        'td',
                                        'day',
                                        $anchor . $dayLabel . '<br />' . date( $this->conf[ 'dateFormat' ], $module[ 'date' ] ),
                                        1,
                                        false,
                                        false,
                                        array(
                                            'rowspan' => $meridiemArray[ 'count' ]
                                        )
                                    );
                                    
                                    // Resets the new day flag
                                    $newDay = false;
                                }
                                
                                // Checks for a new meridiem
                                if( $newMeridiem ) {
                                    
                                    // Adds the meridiem column
                                    $htmlCode[] = $this->api->fe_makeStyledContent(
                                        'td',
                                        'meridiem-' . $meridiem,
                                        $meridiemLabel,
                                        1,
                                        false,
                                        false,
                                        array(
                                            'rowspan' => $moduleArray[ 'count' ]
                                        )
                                    );
                                    
                                    // Resets the new meridiem flag
                                    $newMeridiem = false;
                                }
                                
                                // CSS class name for the module
                                $class = ( ( string )$module[ 'common' ][ 'domain' ] === ( string )$this->conf[ 'holiday' ] ) ? 'holiday' : 'domain-' . $module[ 'common' ][ 'domain' ];
                                
                                // Adds the module
                                $htmlCode[] = $this->api->fe_makeStyledContent(
                                    'td',
                                    $class,
                                    $this->moduleInfos( $id, $module )
                                );
                                
                                // Ends the row
                                $htmlCode[] = '</tr>';
                                
                                // Removes the module to free some memory
                                unset( $this->dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ][ 'entries' ][ $meridiem ][ 'entries' ][ $id ] );
                            }
                            
                            // Removes the meridiem to free some memory
                            unset( $this->dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ][ 'entries' ][ $meridiem ] );
                        }
                        
                        // Removes the day to free some memory
                        unset( $this->dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ] );
                    }
                    
                    // Removes the week to free some memory
                    unset( $this->dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ] );
                }
                
                // Removes the month to free some memory
                unset( $this->dates[ $year ][ 'entries' ][ $month ] );
            }
            
            // Removes the year to free some memory
            unset( $this->dates[ $year ] );
        }
        
        // Ends the table
        $htmlCode[] = '</tbody>';
        $htmlCode[] = '</table>';
        
        // Returns the table
        return $this->api->fe_makeStyledContent( 'div', 'calendar', implode( $this->NL, $htmlCode ) );
    }
    
    function modulesListByModule()
    {
        $htmlCode = array();
        
        foreach( $this->modules as $id => $module ) {
            
            $htmlCode[] = $this->moduleInfos( $id, $module, true );
        }
        
        return $this->api->fe_makeStyledContent( 'div', 'modules', implode( $this->NL, $htmlCode ) );
    }
    
    /**
     * 
     */
    function showModule()
    {
        // Storage for the template markers
        $markers = array();
        
        // Module informations
        $modInfos = array(
            'title'         => ( $value = $this->modGetter->title )                         ? $value : '-',
            'number'        => ( $value = $this->modGetter->number )                        ? $value : '-',
            'sections'      => ( $value = implode( ', ', $this->modGetter->sections ) )     ? $value : '-',
            'incharge'      => ( $value = implode( '<br />', $this->modGetter->incharge ) ) ? $value : '-',
            'domain'        => ( $value = $this->modGetter->domain )                        ? $value : '-',
            'type'          => ( $value = $this->modGetter->type )                          ? $value : '-',
            'credits'       => ( $value = $this->modGetter->credits )                       ? $value : '-',
            'formation'     => ( $value = $this->modGetter->formation )                     ? $value : '-',
			'level'         => ( $value = $this->modGetter->level )                         ? $value : '-',
			'organisation'  => ( $value = $this->modGetter->organisation )                  ? $value : '-',
			'language'      => ( $value = $this->modGetter->language )                      ? $value : '-',
			'prerequisites' => ( $value = $this->modGetter->prerequisites )                 ? $value : '-',
			'goals'         => ( $value = $this->modGetter->goals )                         ? $value : '-',
			'content'       => ( $value = $this->modGetter->content )                       ? $value : '-',
			'evaluation'    => ( $value = $this->modGetter->evaluation )                    ? $value : '-',
			'remediation'   => ( $value = $this->modGetter->remediation )                   ? $value : '-',
			'comments'      => ( $value = $this->modGetter->comments )                      ? $value : '-',
			'bibliography'  => ( $value = $this->modGetter->bibliography )                  ? $value : '-'
        );
        
        foreach( $modInfos as $key => $value ) {
            
            $markers[ '###' . strtoupper( $key ) . '_VALUE###' ] = nl2br( $value );
            $markers[ '###' . strtoupper( $key ) . '_LABEL###' ]  = $this->pi_getLL( 'label-' . $key, $key );
        }
        
        // Process the headers
        for( $i = 0; $i < 10; $i++ ) {
            
            // Add the marker
            $markers[ '###LABEL_' . ( $i + 1 ) . '###' ] = $this->pi_getLL( 'label-' . ( $i + 1 ) );
        }
        
        // Returns the module details
        return $this->api->fe_makeStyledContent(
            'div',
            'module-details',
            $this->api->fe_renderTemplate( 
                $markers,
                '###MODULE_DETAILS###'
            )
        );
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/pi1/class.tx_eespwsmodules_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/pi1/class.tx_eespwsmodules_pi1.php']);
}
?>
