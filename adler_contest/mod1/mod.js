<!--
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
 * JavaScript code for the module 'Adler / Registration' of the
 * 'adler_contest' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */

// Script state
script_ended = 0;

/**
 * TYPO3 jump to URL function
 * 
 * @param   string  url The new location
 * @return  Void
 */
function jumpToUrl( url )
{
    // Jumps to the requested location
    document.location = url;
}

/**
 * Module class
 * 
 * @return  Void
 */
function tx_adlercontest_module1()
{
    // The GET variables
    var _getVars = new Array();
    
    // Calls the constructor
    __construct();
    
    /**
     * Class constructor
     * 
     * @return  Void
     */
    function __construct()
    {
        // Gets the query string
        var queryParts = window.location.search.substring( 1 ).split( '&' );
        
        // Storage for the loop
        var getVar;
        
        // Process each query part
        for( var i = 0; i < queryParts.length; i++ ) {
            
            // Gets the variable name and it's value
            getVar = queryParts[ i ].split( '=' );
            
            // Stores the value
            _getVars[ getVar[ 0 ] ] = getVar[ 1 ];
        }
    }
    
    /**
     * Gets a variable from the GET variables
     * 
     * @param   string  varName The name of the variable to get
     * @return  string  The value of the requested variable
     */
    this.getVar = function( varName )
    {
        // Returns the requested variable
        return _getVars[ varName ];
    }
}

// Creates an instance of the module class
SOBE = new tx_adlercontest_module1();

//-->
