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
require_once( t3lib_extMgm::extPath( 'eesp_ws_modules' ) . 'classes/class.tx_eespwsmodules_listgetter.php' );
require_once( t3lib_extMgm::extPath( 'eesp_ws_modules' ) . 'classes/class.tx_eespwsmodules_singlegetter.php' );
require_once( t3lib_extMgm::extPath( 'eesp_ws_modules' ) . 'classes/class.tx_eespwsmodules_peoplegetter.php' );

class tx_eespwsmodules_pi1 extends tslib_pibase
{
    // Configuration array
    protected $_conf              = array();
    
    // Instance of the Developer API
    protected $_api               = NULL;
    
    // Instance of the module getter
    protected $_modGetter         = NULL;
    
    // Instance of the people getter
    protected $_peopleGetter      = NULL;
    
    // Storage for the modules
    protected $_modules           = array();
    
    // Storage for the module dates
    protected $_dates             = array();
    
    // Total number of modules
    protected $_modCount          = array();
    
    // Modules informations
    protected $_modInfos          = array();
    
    // New line character
    protected $_NL                = '';
    
    // Collapse picture
    protected $_collapsePicture   = '';
    
    // Classrooms picture
    protected $_classRoomsPicture = '';
    
    // Current URL
    protected $_url               = '';
    
    // Current date
    protected $_currentDate       = '';
    
    // Same as class name
    public $prefixId              = 'tx_eespwsmodules_pi1';
    
    // Path to this script relative to the extension dir
    public $scriptRelPath         = 'pi1/class.tx_eespwsmodules_pi1.php';
    
    // The extension key
    public $extKey                = 'eesp_ws_modules';
    
    // Version of the Developer API required
    public $apimacmade_version    = 4.5;
    
    // Check plugin hash
    public $pi_checkCHash         = true;
    
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
     * @param   string  $content    The content object
     * @param   array   $conf       The TS setup
     * @return  string  The content of the plugin.
     */
    public function main( &$content, array &$conf )
    {
        // DEBUG ONLY - Sets the error reporting to the highest possible level
        #error_reporting( E_ALL | E_STRICT );
        
        // New instance of the macmade.net API
        $this->_api =& tx_apimacmade::newInstance(
            'tx_apimacmade',
            array(
                &$this
            )
        );
        
        // Gets the current URL
        $this->_url     = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        
        // Gets the current date
        $this->_currentDate = time();
        
        // Set default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Load locallang labels
        $this->pi_loadLL();
        
        // Sets the new line character
        $this->_NL = chr( 10 );
        
        // Set class confArray TS from the function
        $this->_conf = $conf;
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Set final configuration (TS or FF)
        $this->_setConfig();
        
        // Checks some values from the configuration
        if( isset( $this->_conf[ 'wsdlUrl' ] )
            && isset( $this->_conf[ 'soapOperationList' ] )
            && isset( $this->_conf[ 'soapOperationSingle' ] )
            && isset( $this->_conf[ 'templateFile' ] )
            && isset( $this->_conf[ 'holidays' ] )
        ) {
            
            // Builds the view (single or list)
            $content = ( isset( $this->piVars[ 'showModule' ] ) ) ? $this->_singleView() : $this->_listView();
            
        } else {
            
            // Displays the error message
            $content = $this->_api->fe_makeStyledContent(
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
     * @return  NULL
     */
    protected function _setConfig()
    {
        // Mapping array for PI flexform
        $flex2conf = array(
            'wsdlUrl'              => 'sWSDL:wsdl_url',
            'soapOperationList'    => 'sWSDL:soap_operation_list',
            'soapOperationSingle'  => 'sWSDL:soap_operation_single',
            'soapOperationPeople'  => 'sWSDL:soap_operation_people',
            'collapseListItems'    => 'sDISPLAY:collapse_list_items',
            'scriptaculous.'       => array(
                'appear' => 'sDISPLAY:appear',
                'fade'   => 'sDISPLAY:fade'
            ),
            'classRooms.'          => array(
                'router'   => 'sCLASSROOMS:router',
                'internal' => 'sCLASSROOMS:internal',
                'external' => 'sCLASSROOMS:external'
            )
        );
        
        // Ovverride TS setup with flexform
        $this->_conf = $this->_api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->_conf,
            $this->piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->_api->debug($this->_conf,'EESP / Modules (WSDL): configuration array');
    }
    
    /**
     * 
     */
    protected function _listView()
    {
        // Initialize the template object
        $this->_api->fe_initTemplate( $this->_conf[ 'templateFile' ] );
        
        // Storage for the template markers
        $markers = array();
        
        // Checks for the collapse option
        if( $this->_conf[ 'collapseListItems' ] ) {
            
            // Include Scriptaculous
            $this->_api->fe_includeScriptaculousJs();
            
            // Builds the appear scripts
            $this->_buildAppearScript();
        }
        
        // Checks the display type
        if( isset( $this->piVars[ 'display' ] ) && $this->piVars[ 'display' ] == 1 ) {
            
            // Display modules by modules
            $listMethod     = '_modulesListByModule';
            
        } else {
            
            // Display modules by dates
            $listMethod     = '_modulesListByDate';
        }
                
        // Do not display the module list at first time
        $modulesList = '';
        $count       = '';
        
        // Checks if the module list must be displayed
        if( isset( $this->piVars[ 'submit' ] ) ) {
            
            try {
                
                if( !isset( $this->piVars[ 'peopleId' ] )) {
                    
                    // Initialize the SOAP operations
                    $this->_soapPeopleRequest();
                    
                    // Gets the result type
                    $result = $this->_peopleGetter->getResultType();
                    
                     // Checks the result type
                    if( $result === 0 ) {
                        
                        $people = $this->_peopleGetter->current();
                        
                        $this->_soapListRequest( $people->number );
                        
                        // Builds the module list
                        $content = $this->$listMethod();
                        $count   = $this->_api->fe_makeStyledContent(
                                        'div',
                                        'count',
                                        sprintf( $this->pi_getLL( 'modules-number' ), count( $this->_modCount ) )
                                   );
                        
                    } elseif( $result === 3 ) {
                        
                        $content = $this->_showPeople();
                        $count   = $this->_api->fe_makeStyledContent(
                                        'div',
                                        'count',
                                        sprintf( $this->pi_getLL( 'people-number' ), $this->_peopleGetter->getNumberOfPeople() )
                                   );
                        
                    } else {
                        
                        $content = $this->_api->fe_makeStyledContent(
                                        'div',
                                        'noResult',
                                        $this->pi_getLL( 'no-people' )
                                   );
                    }
                    
                } else {
                    
                    $this->_soapListRequest( $this->piVars[ 'peopleId' ] );
                    
                    // Builds the module list
                    $content = $this->$listMethod();
                    $count   = $this->_api->fe_makeStyledContent(
                                    'div',
                                    'count',
                                    sprintf( $this->pi_getLL( 'modules-number' ), count( $this->_modCount ) )
                               );
                }
                
            } catch( Exception $e ) {
                
                // Displays the error message
                $content = $this->_api->fe_makeStyledContent(
                    'div',
                    'exception',
                    sprintf(
                        $this->pi_getLL( 'exception' ),
                        $e->getMessage()
                    )
                );
                
                return $content;
            }
            
        }
        
        // No modules were found
        $markers[ '###COUNT###' ]  = $count;
        $markers[ '###RESULT###' ] = $content;
        
        // Builds the option form
        $markers[ '###LIST_OPTIONS###' ] = $this->_optionsForm();
        
        // Return content
        return $this->_api->fe_renderTemplate( $markers, '###LIST###' );
    }
    
    /**
     * 
     */
    protected function _singleView()
    {
         // Initialize the template object
        $this->_api->fe_initTemplate( $this->_conf[ 'templateFile' ] );
        
        // Storage for the template markers
        $markers = array();
        
        // Checks for the collapse option
        if( $this->_conf[ 'collapseListItems' ] ) {
            
            // Include Scriptaculous
            $this->_api->fe_includeScriptaculousJs();
            
            // Builds the appear scripts
            $this->_buildAppearScript();
        }
            
        try {
            
            // Initialize the SOAP operations
            $this->_soapListRequest();
            $this->_soapSingleRequest();
            
        } catch( Exception $e ) {
            
            // Displays the error message
            $content = $this->_api->fe_makeStyledContent(
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
        $markers[ '###SINGLE_OPTIONS###' ] = $this->_optionsForm();
        
        // Shows the details
        $markers[ '###MODULE###' ]         = $this->_showModule();
        
        // Shows the dates
        $markers[ '###DATES###' ]          = $this->_modulesListByDate();
        
        // Return content
        return $this->_api->fe_renderTemplate( $markers, '###SINGLE###' );
    }
    
    protected function _showPeople()
    {
        // Storage
        $people = array();
        
        // Process each people
        foreach( $this->_peopleGetter as $key => $value ) {
            
            // First name
            $firstName = $this->_api->fe_makeStyledContent(
                'span',
                'firstName',
                $value->firstName
            );
            
            // Last name
            $lastName = $this->_api->fe_makeStyledContent(
                'span',
                'lastName',
                $value->lastName
            );
            
            // TypoLink configuration
            $typoLink = array(
                'parameter'        => $GLOBALS[ 'TSFE' ]->id,
                'useCacheHash'     => 1,
                'title'            => $value->number,
                'additionalParams' => $this->_api->fe_typoLinkParams(
                    array(
                        'peopleId' => $value->number
                    ),
                    true
                )
            );
            
            // Adds the link
            $link     = $this->cObj->typoLink(
                $firstName . ' ' . $lastName,
                $typoLink
            );
            
            // Class name
            $className = ( $this->piVars[ 'mode' ] == 2 ) ? 'people-student' : 'people-collaborator';
            
            // Adds the people
            $people[] = $this->_api->fe_makeStyledContent(
                'div',
                $className,
                $link
            );
        }
        
        // Returns the people list
        return $this->_api->fe_makeStyledContent(
            'div',
            'peopleList',
            implode( $this->_NL, $people )
        );
    }
    
    /**
     * 
     */
    protected function _soapPeopleRequest()
    {
        try {
            
            // Gets an instance of the module getter
            $this->_peopleGetter = $this->_api->newInstance(
                'tx_eespwsmodules_peopleGetter',
                array(
                    $this->_conf[ 'wsdlUrl' ],
                    $this->_conf[ 'soapOperationPeople' ]
                )
            );
            
            // Sets the SOAP parameters
            $this->_peopleGetter->setSoapArg( 'FourD_arg1', $this->piVars[ 'lastname' ] );
            $this->_peopleGetter->setSoapArg( 'FourD_arg2', $this->piVars[ 'firstname' ] );
            $this->_peopleGetter->setSoapArg( 'FourD_arg3', '' );
            $this->_peopleGetter->setSoapArg( 'FourD_arg4', $this->piVars[ 'mode' ] );
            
            // Initialization of the SOAP request
            $this->_peopleGetter->soapRequest();
        
            // Removes the modules getter to free some memory, as it's not needed anymore
            unset( $this->_modGetter );
            
        } catch( Exception $e ) {
            
            throw $e;
        }
    }
    
    /**
     * 
     */
    protected function _soapListRequest( $peopleId )
    {
        // Checks the display mode
        if( isset( $this->piVars[ 'display' ] ) && $this->piVars[ 'display' ] == 1 ) {
            
            // Method to use to get the modules
            $getModulesMethod      = '_getModulesByModule';
            
            // Argument to pass to the helper class
            $helperClassDisplayArg = true;
            
        } else {
            
            // Method to use to get the modules
            $getModulesMethod      = '_getModulesByDate';
            
            // Argument to pass to the helper class
            $helperClassDisplayArg = false;
        }
        
        try {
            
            // Gets an instance of the module getter
            $this->_modGetter = $this->_api->newInstance(
                'tx_eespwsmodules_listGetter',
                array(
                    $this->_conf[ 'wsdlUrl' ],
                    $this->_conf[ 'soapOperationList' ],
                    $helperClassDisplayArg
                )
            );
            
            // Only show future modules, or not
            $passed = ( isset( $this->piVars[ 'passed' ] ) ) ? 1 : 0;
            
            // Sets the SOAP parameters
            $this->_modGetter->setSoapArg( 'FourD_arg1', $peopleId );
            $this->_modGetter->setSoapArg( 'FourD_arg2', $this->piVars[ 'mode' ] );
            $this->_modGetter->setSoapArg( 'FourD_arg3', $passed );
            
            // Initialization of the SOAP request
            $this->_modGetter->soapRequest();
            
            // Gets the modules
            $this->$getModulesMethod();
        
            // Removes the modules getter to free some memory, as it's not needed anymore
            unset( $this->_modGetter );
            
        } catch( Exception $e ) {
            
            throw $e;
        }
    }
    
    /**
     * 
     */
    protected function _soapSingleRequest()
    {
        try {
            
            // Gets an instance of the module getter
            $this->_modGetter = $this->_api->newInstance(
                'tx_eespwsmodules_singleGetter',
                array(
                    $this->_conf[ 'wsdlUrl' ],
                    $this->_conf[ 'soapOperationSingle' ]
                )
            );
            
            // Sets the SOAP parameters
            $this->_modGetter->setSoapArg( 'FourD_arg1', $this->piVars[ 'showModule' ] );
            $this->_modGetter->setSoapArg( 'FourD_arg2', '' );
            
            // Initialization of the SOAP request
            $this->_modGetter->soapRequest();
            
        } catch( Exception $e ) {
            
            throw $e;
        }
    }
    
    /**
     * 
     */
    protected function _getModulesByDate()
    {
        // Process each module returned by the module getter
        foreach( $this->_modGetter as $id => $date ) {
            
            // Gets the date informations
            $year     = date( 'Y', $date );
            $month    = date( 'n', $date );
            $week     = date( 'W', $date );
            $day      = date( 'w', $date );
            $meridiem = date( 'a', $date );
            
            // Checks the storage place for the current year
            if( !isset( $this->_dates[ $year ] ) ) {
                
                // Creates the storage place
                $this->_dates[ $year ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Sorts the array
                // This should not be useful, but some modules (holiday for instance) didn't seems to be sorted correctly in 4D!
                ksort( $this->_dates );
            }
            
            // Gets a reference to the entries
            $yearArray =& $this->_dates[ $year ][ 'entries' ];
            
            // Checks the storage place for the current month
            if( !isset( $yearArray[ $month ] ) ) {
                
                // Creates the storage place
                $yearArray[ $month ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Sorts the array
                // This should not be useful, but some modules (holiday for instance) didn't seems to be sorted correctly in 4D!
                ksort( $yearArray );
            }
            
            // Gets a reference to the entries
            $monthArray =& $yearArray[ $month ][ 'entries' ];
            
            // Checks the storage place for the current week
            if( !isset( $monthArray[ $week ] ) ) {
                
                // Creates the storage place
                $monthArray[ $week ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Sorts the array
                // This should not be useful, but some modules (holiday for instance) didn't seems to be sorted correctly in 4D!
                ksort( $monthArray );
            }
            
            // Gets a reference to the entries
            $weekArray =& $monthArray[ $week ][ 'entries' ];
            
            // Checks the storage place for the current day
            if( !isset( $weekArray[ $day ] ) ) {
                
                // Creates the storage place
                $weekArray[ $day ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Sorts the array
                // This should not be useful, but some modules (holiday for instance) didn't seems to be sorted correctly in 4D!
                ksort( $weekArray );
            }
            
            // Gets a reference to the entries
            $dayArray =& $weekArray[ $day ][ 'entries' ];
            
            // Checks the storage place for the current meridiem
            if( !isset( $dayArray[ $meridiem ] ) ) {
                
                // Creates the storage place
                $dayArray[ $meridiem ] = array(
                    'entries' => array(),
                    'count'   => 0
                );
                
                // Sorts the array
                // This should not be useful, but some modules (holiday for instance) didn't seems to be sorted correctly in 4D!
                ksort( $dayArray );
            }
            
            // Gets a reference to the entries
            $meridiemArray =& $dayArray[ $meridiem ][ 'entries' ];
            
            // Checks if the module already exists
            if( !isset( $this->_modules[ $id ] ) ) {
                
                // Stores the module informations
                $this->_modules[ $id ] = array(
                    'number'   => $this->_modGetter->number,
                    'credits'  => $this->_modGetter->credits,
                    'domain'   => $this->_modGetter->domain,
                    'section'  => $this->_modGetter->section,
                    'type'     => $this->_modGetter->type,
                    'incharge' => $this->_modGetter->incharge,
                    'title'    => $this->_modGetter->title,
                    'subcode'  => $this->_modGetter->subcode
                );
            }
            
            // Checks if the module has already been stored
            // This shouldn't be the case, except for the holiday (bug in 4D?)
            if( !isset( $meridiemArray[ $id ] ) ) {
                
                // Stores the date's specific informations
                $meridiemArray[ $id ] =  array(
                    'comments' => $this->_modGetter->comments,
                    'date'     => $date
                );
                
                // Adds a reference to the common module informations
                $meridiemArray[ $id ][ 'common' ] =& $this->_modules[ $id ];
                
                // Increase all counters
                $this->_modCount[ $id ] = true;
                $this->_dates[ $year ][ 'count' ]++;
                $this->_dates[ $year ][ 'entries' ][ $month ][ 'count' ]++;
                $this->_dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'count' ]++;
                $this->_dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ][ 'count' ]++;
                $this->_dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ][ 'entries' ][ $meridiem ][ 'count' ]++;
            }
        }
        
        return true;
    }
    
    protected function _getModulesByModule()
    {
        // Process each module returned by the module getter
        foreach( $this->_modGetter as $id => $date ) {
            
            // Stores the module informations
            $this->_modules[ $id ] = array(
                'common' => array(
                    'number'   => $this->_modGetter->number,
                    'credits'  => $this->_modGetter->credits,
                    'domain'   => $this->_modGetter->domain,
                    'section'  => $this->_modGetter->section,
                    'type'     => $this->_modGetter->type,
                    'incharge' => $this->_modGetter->incharge,
                    'title'    => $this->_modGetter->title
                ),                
                'dates'    => $date,
                'comments' => $this->_modGetter->comments
            );
            
            // Counter for modules
            $this->_modCount[ $id ] = true;
        }
        
        return true;
    }
    
    /**
     * 
     */
    protected function _buildAppearScript()
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
        $script[] = '           Effect.' . $this->_conf[ 'scriptaculous.' ][ 'fade' ] . '( elementId );';
        $script[] = '           ';
        $script[] = '           // Sets the display flag';
        $script[] = '           tx_eexpwsmodules_pi1_infosDivs[ id ] = false;';
        $script[] = '           ';
        $script[] = '       } else {';
        $script[] = '           ';
        $script[] = '           // Appear effect';
        $script[] = '           Effect.' . $this->_conf[ 'scriptaculous.' ][ 'appear' ] . '( elementId );';
        $script[] = '           ';
        $script[] = '           // Sets the display flag';
        $script[] = '           tx_eexpwsmodules_pi1_infosDivs[ id ] = true;';
        $script[] = '       }';
        $script[] = '   }';
        $script[] = '// ]]>';
        $script[] = '</script>';
        
        // Adds the script to the TYPO3 headers
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_eespwsmodules_pi1_appearScript' ] = implode( $this->_NL, $script );
        
        return true;
    }
    
    /**
     * 
     */
    protected function _moduleInfos( $id, array &$module, $byModule = false )
    {
        // Suffix for the template sections
        $tmplSuffix = ( $byModule ) ? '_BYMOD' : '_BYDATE';
        
        // Storage for the template markers
        $markers    = array();
        
        // Checks if the module is an holiday module
        if( ( int )$module[ 'common' ][ 'number' ] === ( int )$this->_conf[ 'holidays' ] ) {
            
            // Sets the markers
            $markers[ '###TITLE_LABEL###' ] = $this->pi_getLL( 'label-title' );
            $markers[ '###TITLE_VALUE###' ] = $this->_api->fe_makeStyledContent(
                'span',
                'module-title',
                $module[ 'common' ][ 'title' ]
            );
            
            // Sets the template section
            $templateSection                = '###LIST_MODINFOS_HOLIDAYS###';
            
        } else {
            
            // Checks if the infos for the current module already exists
            if( !isset( $this->_modInfos[ $id ] ) ) {
                
                // Gets the common markers
                $this->_modInfos[ $id ] = $this->_moduleInfosMarkers( $id, $module );
            }
            
            // Gets a reference to the markers
            $markers = $this->_modInfos[ $id ];
            
            // Specific markers for the view by date
            if( !$byModule ) {
                
                // Checks the value for comments
                if( $module[ 'comments' ] && !is_array( $module[ 'comments' ] ) ) {
                    
                    // Adds the comments for the current date
                    $markers[ '###COMMENTS_VALUE###' ] = $this->_api->fe_makeStyledContent( 'span', 'module-comments', $module[ 'comments' ] );
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
                $markers[ '###DATES_VALUE###' ] = $this->_moduleInfosDateTable( $module[ 'dates' ], $module[ 'comments' ] );
            }
            
            // Checks if the collapse option is set
            if( $this->_conf[ 'collapseListItems' ] ) {
                
                // Checks if the collapse picture needs to be processed
                if( !$this->_collapsePicture ) {
                    
                    // Builds the collapse picture
                    $this->_collapsePicture = $this->cObj->IMAGE( $this->_conf[ 'collapsePicture.' ] );
                }
                
                // ID for the info DIV
                $infosDivId = microtime( true ) * 10000;
                
                // Adds the collapse picture with the link
                $markers[ '###COLLAPSE_PICTURE###' ] = '<a href="javascript:tx_eespwsmodules_pi1_showInfoDiv( '
                                                     . $infosDivId
                                                     . ' );" title="'
                                                     . $this->pi_getLL( 'collapse-title' )
                                                     . '">'
                                                     . $this->_collapsePicture
                                                     . '</a>';
                
                // Checks if we have to add the classrooms infos
                if( $this->_conf[ 'classRooms.' ][ 'router' ]
                    && $this->_conf[ 'classRooms.' ][ 'internal' ]
                    && $this->_conf[ 'classRooms.' ][ 'external' ]
                ) {
                    
                    // Checks if the collapse picture needs to be processed
                    if( !$this->_classRoomsPicture ) {
                        
                        // Builds the collapse picture
                        $this->_classRoomsPicture = $this->cObj->IMAGE( $this->_conf[ 'classRooms.' ][ 'picture.' ] );
                    }
                
                    // Adds the collapse picture with the link
                    $markers[ '###CLASSROOMS_PICTURE###' ] = $this->_classRoomsPicture;
                }
                
                // TypoLink for the module view
                $typoLink = array(
                    'parameter'        => $GLOBALS[ 'TSFE' ]->id,
                    'useCacheHash'     => 1,
                    'title'            => $module[ 'common' ][ 'number' ],
                    'additionalParams' => $this->_api->fe_typoLinkParams(
                        array(
                            'showModule' => $id
                        )
                    )
                );
                
                // Adds the link
                $markers[ '###LINK###' ] = $this->cObj->typoLink(
                    $this->_api->fe_renderTemplate(
                        $markers,
                        '###LIST_MODINFOS_COLLAPSE_LINK' . $tmplSuffix . '###'
                    ),
                    $typoLink
                );
                
                // Adds the collapsed content
                $markers[ '###COLLAPSE_CONTENT###' ] = $this->_api->fe_makeStyledContent(
                    'div',
                    'module-infoDiv',
                    $this->_api->fe_renderTemplate(
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
                    'additionalParams' => $this->_api->fe_typoLinkParams(
                        array(
                            'showModule' => $id
                        )
                    )
                );
                
                // Adds the link
                $markers[ '###LINK###' ] = $this->cObj->typoLink(
                    $this->_api->fe_renderTemplate( $markers, '###LIST_MODINFOS_LINK' . $tmplSuffix . '###' ),
                    $typoLink
                );
                
                // Sets the template section
                $templateSection = '###LIST_MODINFOS' . $tmplSuffix . '###';
            }
        }
        
        // Returns the info div
        return $this->_api->fe_makeStyledContent( 'div', 'module-infos', $this->_api->fe_renderTemplate( $markers, $templateSection ) );
    }
    
    /**
     * 
     */
    protected function _moduleInfosMarkers( $id, array &$module )
    {
        // Storage for the template markers
        $markers = array();
        
        // Sets the values
        $markers[ '###TITLE_VALUE###' ]    = $this->_api->fe_makeStyledContent( 'span', 'module-title',    $module[ 'common' ][ 'title' ] );
        $markers[ '###NUMBER_VALUE###' ]   = $this->_api->fe_makeStyledContent( 'span', 'module-number',   $module[ 'common' ][ 'number' ] );
        $markers[ '###CREDITS_VALUE###' ]  = $this->_api->fe_makeStyledContent( 'span', 'module-credits',  $module[ 'common' ][ 'credits' ] );
        $markers[ '###DOMAIN_VALUE###' ]   = $this->_api->fe_makeStyledContent( 'span', 'module-domain',   $module[ 'common' ][ 'domain' ] );
        $markers[ '###SECTION_VALUE###' ]  = $this->_api->fe_makeStyledContent( 'span', 'module-section',  $module[ 'common' ][ 'section' ] );
        $markers[ '###TYPE_VALUE###' ]     = $this->_api->fe_makeStyledContent( 'span', 'module-type',     $module[ 'common' ][ 'type' ] );
        $markers[ '###SUBCODE_VALUE###' ]  = $this->_api->fe_makeStyledContent( 'span', 'module-subcode',  $module[ 'common' ][ 'subcode' ] );
        $markers[ '###INCHARGE_VALUE###' ] = $this->_api->fe_makeStyledContent( 'span', 'module-incharge', implode( ', ', $module[ 'common' ][ 'incharge' ] ) );
        
        // Sets the labels
        $markers[ '###TITLE_LABEL###' ]    = $this->pi_getLL( 'label-title' );
        $markers[ '###NUMBER_LABEL###' ]   = $this->pi_getLL( 'label-number' );
        $markers[ '###CREDITS_LABEL###' ]  = $this->pi_getLL( 'label-credits' );
        $markers[ '###DOMAIN_LABEL###' ]   = $this->pi_getLL( 'label-domain' );
        $markers[ '###SECTION_LABEL###' ]  = $this->pi_getLL( 'label-section' );
        $markers[ '###TYPE_LABEL###' ]     = $this->pi_getLL( 'label-type' );
        $markers[ '###SUBCODE_LABEL###' ]  = $this->pi_getLL( 'label-subcode' );
        $markers[ '###INCHARGE_LABEL###' ] = $this->pi_getLL( 'label-incharge' );
        
        // Return the markers
        return $markers;
    }
    
    /**
     * 
     */
    protected function _moduleInfosDateTable( &$dates, &$comments )
    {
        // Checks the input arrays
        if( is_array( $dates ) && is_array( $comments ) && count( $dates ) ) {
            
            // Storage
            $htmlCode = array();
            
            // Starts the table
            $htmlCode[] = $this->_api->fe_makeStyledContent(
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
            $htmlCode[] = $this->_api->fe_makeStyledContent( 'th', 'date', $this->pi_getLL( 'label-dates' ) );
            
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
                $comment  = ( isset( $comments[ $ts ] ) && $comments[ $ts ] ) ? $this->_api->fe_makeStyledContent( 'div', 'comment', $comments[ $ts ] ) : '';
                
                // Full date
                $fullDate = strftime( $this->_conf[ 'dateFormatStrftime' ], $ts ) . ' - ' . $this->pi_getLL( date( 'a', $ts ) );
                
                // Current date
                $date = $this->_api->fe_makeStyledContent(
                    'div',
                    'date',
                    $fullDate
                );
                
                // Writes the date
                $htmlCode[] = $this->_api->fe_makeStyledContent(
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
            return $this->_api->fe_makeStyledContent( 'div', 'module-infos-dates', implode( $this->_NL, $htmlCode ) );
        }
    }
    
    protected function _displaySelect()
    {
        // Storage
        $select = array();
        
        // Starts the select tag
        $select[] = '<select name="'
                  . $this->prefixId
                  . '[display]" size="1">';
        
        // Process each display options
        for( $i = 0; $i < 2; $i++ ) {
            
            // Selected state
            $selected = '';
            
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
    
    protected function _modeSelect()
    {
        // Storage
        $select = array();
        
        // Starts the select tag
        $select[] = '<select name="'
                  . $this->prefixId
                  . '[mode]" size="1">';
        
        // Process each display options
        for( $i = 2; $i > 0; $i-- ) {
            
            // Selected state
            $selected = '';
            
            // Checks for incoming value from the plugin variables
            if( isset( $this->piVars[ 'mode' ] ) ) {
                
                // Selected item state
                $selected = ( $i === ( int )$this->piVars[ 'mode' ] ) ? ' selected="selected"' : '';
                
            }
            
            // Adds the option tag
            $select[] = '<option value="'
                      . $i
                      . '"'
                      . $selected
                      . '>'
                      . $this->pi_getLL( 'options-mode-' . $i )
                      . '</option>';
        }
        
        // Ends the select tag
        $select[] = '</select>';
        
        // Returns the full select
        return implode( chr( 10 ), $select );
    }
    
    protected function _createInput( $name )
    {
        // Default value
        $value = ( isset( $this->piVars[ $name ] ) ) ? $this->piVars[ $name ] : '';
        
        // Label
        $label = '<label for="'
               . $this->prefixId
               . '_'
               . $name
               . '">'
               . $this->pi_getLL( 'options-' . $name )
               . '</label>';
        
        // Text input
        $input = '<input name="'
               . $this->prefixId
               . '['
               . $name
               . ']'
               . '" id="'
               . $this->prefixId
               . '_'
               . $name
               . '" type="text" size="'
               . $this->_conf[ 'inputSize' ]
               . '" value="'
               . $value
               . '" />';
        
        // Return the input
        return $label . $input;
    }
    
    protected function _createCheckBox( $name )
    {
        // Default value
        $checked = ( isset( $this->piVars[ $name ] ) ) ? ' checked' : '';
        
        // Label
        $label   = '<label for="'
                 . $this->prefixId
                 . '_'
                 . $name
                 . '">'
                 . $this->pi_getLL( 'options-' . $name )
                 . '</label>';
        
        // Text input
        $input   = '<input name="'
                 . $this->prefixId
                 . '['
                 . $name
                 . ']'
                 . '" id="'
                 . $this->prefixId
                 . '_'
                 . $name
                 . '" type="checkbox" size="'
                 . $this->_conf[ 'inputSize' ]
                 . '"'
                 . $checked
                 . '" />';
        
        // Return the input
        return $label . $input;
    }
    
    /**
     * 
     */
    protected function _optionsForm()
    {
        // Storage
        $htmlCode = array();
        $markers  = array();
        
        // TypoLink configuration for the form action
        $typoLink   = array(
            'parameter'    => $GLOBALS[ 'TSFE' ]->id,
            'useCacheHash' => 1
        );
        
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
        
        // Adds the first name input
        $markers[ '###FIRSTNAME###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'firstName',
            $this->_createInput( 'firstname' )
        );
        
        // Adds the last name input
        $markers[ '###LASTNAME###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'lastName',
            $this->_createInput( 'lastname' )
        );
        
        // Adds the display select
        $markers[ '###DISPLAY###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'display',
            '<label>' . $this->pi_getLL( 'options-display' ) . '</label> ' . $this->_displaySelect()
        );
        
        // Adds the mode select
        $markers[ '###MODE###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'mode',
            '<label>' . $this->pi_getLL( 'options-mode' ) . '</label> ' . $this->_modeSelect()
        );
        
        // Adds the future only checkbox
        $markers[ '###PASSED###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'passed',
            $this->_createCheckBox( 'passed' )
        );
        
        // Adds the form submit input
        $markers[ '###SUBMIT###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'submit',
            '<input name="' . $this->prefixId . '[submit]" type="submit" value="' . $this->pi_getLL( 'options-submit' ) . '" />'
        );
        
        // Adds the form fields
        $htmlCode[] = $this->_api->fe_renderTemplate( $markers, '###OPTIONS###' );
        
        // Close the form tag
        $htmlCode[] = '</form>';
        
        // Returns the form
        return $this->_api->fe_makeStyledContent(
            'div',
            'options',
            implode( $this->_NL, $htmlCode )
        );
    }
    
    protected function _modulesListByDate()
    {
        // Storage
        $htmlCode = array();
        
        // Starts the table
        $htmlCode[] = $this->_api->fe_makeStyledContent(
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
        $htmlCode[] = $this->_api->fe_makeStyledContent( 'th', 'month',    $this->pi_getLL( 'headers-month' ) );
        $htmlCode[] = $this->_api->fe_makeStyledContent( 'th', 'week',     $this->pi_getLL( 'headers-week' ) );
        $htmlCode[] = $this->_api->fe_makeStyledContent( 'th', 'day',      $this->pi_getLL( 'headers-day' ) );
        $htmlCode[] = $this->_api->fe_makeStyledContent( 'th', 'meridiem', $this->pi_getLL( 'headers-meridiem' ) );
        $htmlCode[] = $this->_api->fe_makeStyledContent( 'th', 'modules',  $this->pi_getLL( 'headers-modules' ) );
        
        // Ends the table headers
        $htmlCode[] = '</tr>';
        $htmlCode[] = '</thead>';
        
        // Starts the table body
        $htmlCode[] = '<tbody>';
        
        // Process each year in the module array
        foreach( $this->_dates as $year => &$monthArray ) {
            
            // Adds the year separation
            $htmlCode[] = '<tr>'
                        . $this->_api->fe_makeStyledContent(
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
                                    $htmlCode[] = $this->_api->fe_makeStyledContent(
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
                                    $htmlCode[] = $this->_api->fe_makeStyledContent(
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
                                    $htmlCode[] = $this->_api->fe_makeStyledContent(
                                        'td',
                                        'day',
                                        $anchor . $dayLabel . '<br />' . date( $this->_conf[ 'dateFormat' ], $module[ 'date' ] ),
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
                                    $htmlCode[] = $this->_api->fe_makeStyledContent(
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
                                $class = ( ( string )$module[ 'common' ][ 'domain' ] === ( string )$this->_conf[ 'holiday' ] ) ? 'holiday' : 'domain-' . $module[ 'common' ][ 'domain' ];
                                
                                // Adds the module
                                $htmlCode[] = $this->_api->fe_makeStyledContent(
                                    'td',
                                    $class,
                                    $this->_moduleInfos( $id, $module )
                                );
                                
                                // Ends the row
                                $htmlCode[] = '</tr>';
                                
                                // Removes the module to free some memory
                                unset( $this->_dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ][ 'entries' ][ $meridiem ][ 'entries' ][ $id ] );
                            }
                            
                            // Removes the meridiem to free some memory
                            unset( $this->_dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ][ 'entries' ][ $meridiem ] );
                        }
                        
                        // Removes the day to free some memory
                        unset( $this->_dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ][ 'entries' ][ $day ] );
                    }
                    
                    // Removes the week to free some memory
                    unset( $this->_dates[ $year ][ 'entries' ][ $month ][ 'entries' ][ $week ] );
                }
                
                // Removes the month to free some memory
                unset( $this->_dates[ $year ][ 'entries' ][ $month ] );
            }
            
            // Removes the year to free some memory
            unset( $this->_dates[ $year ] );
        }
        
        // Ends the table
        $htmlCode[] = '</tbody>';
        $htmlCode[] = '</table>';
        
        // Returns the table
        return $this->_api->fe_makeStyledContent( 'div', 'calendar', implode( $this->_NL, $htmlCode ) );
    }
    
    protected function _modulesListByModule()
    {
        // Storage
        $htmlCode = array();
        
        // Process each module
        foreach( $this->_modules as $id => $module ) {
            
            // Adds the module informations
            $htmlCode[] = $this->_moduleInfos( $id, $module, true );
        }
        
        // Returns the list
        return $this->_api->fe_makeStyledContent( 'div', 'modules', implode( $this->_NL, $htmlCode ) );
    }
    
    /**
     * 
     */
    protected function _showModule()
    {
        // Storage for the template markers
        $markers = array();
        
        // Module informations
        $modInfos = array(
            'title'         => ( $value = $this->_modGetter->title )                         ? $value : '-',
            'number'        => ( $value = $this->_modGetter->number )                        ? $value : '-',
            'sections'      => ( $value = implode( ', ', $this->_modGetter->sections ) )     ? $value : '-',
            'incharge'      => ( $value = implode( '<br />', $this->_modGetter->incharge ) ) ? $value : '-',
            'domain'        => ( $value = $this->_modGetter->domain )                        ? $value : '-',
            'type'          => ( $value = $this->_modGetter->type )                          ? $value : '-',
            'credits'       => ( $value = $this->_modGetter->credits )                       ? $value : '-',
            'formation'     => ( $value = $this->_modGetter->formation )                     ? $value : '-',
			'level'         => ( $value = $this->_modGetter->level )                         ? $value : '-',
			'organisation'  => ( $value = $this->_modGetter->organisation )                  ? $value : '-',
			'language'      => ( $value = $this->_modGetter->language )                      ? $value : '-',
			'prerequisites' => ( $value = $this->_modGetter->prerequisites )                 ? $value : '-',
			'goals'         => ( $value = $this->_modGetter->goals )                         ? $value : '-',
			'content'       => ( $value = $this->_modGetter->content )                       ? $value : '-',
			'evaluation'    => ( $value = $this->_modGetter->evaluation )                    ? $value : '-',
			'remediation'   => ( $value = $this->_modGetter->remediation )                   ? $value : '-',
			'comments'      => ( $value = $this->_modGetter->comments )                      ? $value : '-',
			'bibliography'  => ( $value = $this->_modGetter->bibliography )                  ? $value : '-'
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
        return $this->_api->fe_makeStyledContent(
            'div',
            'module-details',
            $this->_api->fe_renderTemplate( 
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
