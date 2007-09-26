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
 * Flexform helper class
 *
 * This class is part of the Developer API (api_macmade) extension, and is
 * only available for PHP5.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - PHP methods
 *              public function __construct( &$xmlData )
 *              public function __get( $fieldName )
 * 
 * SECTION:     2 - SPL Iterator methods
 *              public function rewind
 *              public function current
 *              public function key
 *              public function next
 *              public function valid
 * 
 * SECTION:     3 - Private or protected class methods
 *              protected function _relinkFieldsShortcut
 * 
 * SECTION:     4 - Public class methods
 *              public function getFieldValue( $fieldName, $sheet = 'sDEF', $lang = 'lDEF', $value = 'vDEF' )
 *              public function setDefaultSheet( $name )
 *              public function setDefaultLang( $name )
 *              public function setDefaultValue( $name )
 * 
 *              TOTAL FUNCTIONS: 12
 */

class tx_apimacmade_flexform implements Iterator
{
    // SimpleXMLElement with the data from the flexform
    protected $_flexData      = NULL;
    
    // Shortcut for the flexform fields
    protected $_fields        = NULL;
    
    // Default IDs for the flexform section, for the __get method
    protected $_sheet         = 'sDEF';
    protected $_lang          = 'lDEF';
    protected $_value         = 'vDEF';
        
    // Current position for the iterator methods
    protected $_iteratorIndex = 0;
    
    /***************************************************************
     * SECTION 1:
     * 
     * PHP methods
     ***************************************************************/
    
    /**
     * Class constructor
     * 
     * @param   string  $xmlData    The flexform XML data
     * @return  NULL
     */
    public function __construct( &$xmlData )
    {
        // Checks if the SimpleXMLElement class is available
        if( !class_exists( SimpleXMLElement ) ) {
            
            // SimpleXMLElement not available
            throw new Exception( 'The SimpleXMLElement class is not available in your PHP installation.' );
        }
        
        // Creates a SImpleXMLElement with the flexform data
        try {
            
            // Create the object
            $this->_flexData = new SimpleXMLElement( $xmlData );
            
        } catch( Exception $e ) {
            
            // Cannot parse the flexform
            throw $e;
        }
    }
    
    /**
     * PHP getter method
     * 
     * This methods gets the value of a flexform field, as if it was a property
     * of this class.
     * 
     * @param   string  $fieldName  The name of the flexform field
     * @return  string  The field value
     */
    public function __get( $fieldName )
    {
        return $this->getFieldValue(
            $fieldName,
            $this->_sheet,
            $this->_lang,
            $this->_value
        );
    }
    
    /***************************************************************
     * SECTION 2:
     * 
     * SPL Iterator methods
     ***************************************************************/
    
    /**
     * Move position to the first element (Iterator method)
     * 
     * @return  NULL
     */
    public function rewind()
    {
        $this->_iteratorIndex = 0;
    }
    
    /**
     * Get current element (Iterator method)
     * 
     * @return  SimpleXML   The municipality object
     */
    public function current()
    {
        // Tries to get the value
        if( $value = $this->_fields->field[ $this->_iteratorIndex ]->xpath( 'value[@index="' . $this->_value . '"]' ) ) {
            
            // Returns the value
            return ( string )array_shift( $value );
        }
        
        // No such value
        return false;
    }
    
    /**
     * Get current element's key (Iterator method)
     * 
     * @return  int     The municipality ID
     */
    public function key()
    {
        $currentField = $this->_fields->field[ $this->_iteratorIndex ];
        return ( string )$currentField[ 'index' ];
    }
    
    /**
     * Move position to the next element (Iterator method)
     * 
     * @return  NULL
     */
    public function next()
    {
        $this->_iteratorIndex++;
    }
    
    /**
     * Checks current element (Iterator method)
     * 
     * @return  boolean
     */
    public function valid()
    {
        return is_object( $this->_fields ) && isset( $this->_fields->field[ $this->_iteratorIndex ] );
    }
    
    /***************************************************************
     * SECTION 3:
     * 
     * Private or protected class methods
     ***************************************************************/
    
    /**
     * Gets a shorctut to the fields object
     * 
     * @return  boolean
     */
    protected function _relinkFieldsShortcut()
    {
        // Creates the XPath expression
        $xPath = '/T3FlexForms/data/sheet[@index="'
               . $this->_sheet
               . '"]/language[@index="'
               . $this->_lang
               . '"]';
        
        // Tries to get fields
        if( $fields = $this->_flexData->xpath( $xPath ) ) {
            
            // Stores the fields
            $this->_fields = array_shift( $fields );
            return true;
        }
        
        // No fields
        $this->_fields = NULL;
        return false;
    }
    
    /***************************************************************
     * SECTION 3:
     * 
     * Public class methods
     ***************************************************************/
    
    /**
     * Gets the value of a flexform field
     * 
     * @param   string  $fieldName  The name of the flexform field
     * @param   string  $sheet      The ID of the flexform sheet (default is sDEF) 
     * @param   string  $lang       The ID of the language (default is lDEF)
     * @param   string  $value      The ID of the value (default is vDEF)
     * @return  string  The value of the flexform field
     */
    public function getFieldValue( $fieldName, $sheet = 'sDEF', $lang = 'lDEF', $value = 'vDEF' )
    {
        // Creates the XPath expression
        $xPath = '/T3FlexForms/data/sheet[@index="'
               . $sheet
               . '"]/language[@index="'
               . $lang
               . '"]/field[@index="'
               . $fieldName
               . '"]/value[@index="'
               . $value
               . '"]';
        
        // Tries to get the field value
        if( $value = $this->_flexData->xpath( $xPath ) ) {
            
            // Returns the value
            return ( string )array_shift( $value );
        }
        
        // No such field
        return false;
    }
    
    /**
     * Sets the default flexform sheet ID
     * 
     * @param   string  $id     The ID of the sheet
     * @return  boolean
     */
    public function setDefaultSheet( $id )
    {
        // Sets the sheet ID
        $this->_sheet = ( string )$id;
        
        // Relink the fields shortcut
        $this->_relinkFieldsShortcut();
        return true;
    }
    
    /**
     * Sets the default flexform lang ID
     * 
     * @param   string  $id     The ID of the lang
     * @return  boolean
     */
    public function setDefaultLang( $id )
    {
        // Sets the lang ID
        $this->_lang = ( string )$id;
        
        // Relink the fields shortcut
        $this->_relinkFieldsShortcut();
        return true;
    }
    
    /**
     * Sets the default flexform value ID
     * 
     * @param   string  $id     The ID of the value
     * @return  boolean
     */
    public function setDefaultValue( $id )
    {
        // Sets the value ID
        $this->_value = ( string )$id;
        return true;
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/api_macmade/php5/class.tx_apimacmade_flexform.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/api_macmade/php5/class.tx_apimacmade_flexform.php']);
}
