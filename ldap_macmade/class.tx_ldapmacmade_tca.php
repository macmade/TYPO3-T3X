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
     * TCA helper class for extension 'ldap_macmade'.
     *
     * @author          Jean-David Gadina (info@macmade.net)
     * @version         1.0
     */
    
    /**
     * [CLASS/FUNCTION INDEX OF SCRIPT]
     * 
     *      43:         class tx_ldapmacmade_tca
     *      66:         function showTableFields( &$params,&$pObj )
     *     106:         function beAuth( $params,$pObj )
     *     176:         function feAuth( $params,$pObj  )
     * 
     *                  TOTAL FUNCTIONS: 3
     */
    
    class tx_ldapmacmade_tca
    {
        
        /**
         * List table fields
         * 
         * This function adds all the available fields to selector
         * box items arrays.
         *
         * @param       array       $params         Array of items passed by reference
         * @param       object      $pObj           The parent object (t3lib_TCEforms / t3lib_transferData depending on context)
         * @return      null
         */
        function showTableFields( &$params,&$pObj )
        {
            global $TCA, $LANG;
            
            // Check parameters
            if ( isset( $params[ 'row' ][ 'mapping_external' ] ) ) {
                
                // Table to use
                $table = $params[ 'row' ][ 'mapping_external' ];
                
                // Load TCA for current table
                t3lib_div::loadTCA( $table );
                
                // Traverse TCA array
                foreach( $TCA[ $table ][ 'columns' ] as $key => $value ) {
                    
                    // Get field label
                    $label = $LANG->sL( $value[ 'label' ] );
                    
                    // Create select label
                    $select = ( $label ) ? $label . ' (' . $key . ')' : $key;
                    
                    // Add select item
                    $params[ 'items' ][] = array( $select, $key );
                }
            }
        }
        
        /**
         * Renders be_auth field
         * 
         * This function renders the field be_auth of table 'tx_ldapmacmade_server',
         * depending of the extension configuration. If the OpenLDAP authentification
         * features are enabled, it renders two radio buttons to set if the server can be
         * used for authentification. Otherwise, no fields are rendered.
         * 
         * @param       array       $params         Array of items passed by reference
         * @param       object      $pObj           The parent object (t3lib_TCEforms / t3lib_transferData depending on context)
         * @return      string      The field, depending of the extension configuration
         */
        function beAuth( &$params, &$pObj )
        {
            global $LANG;
            
            // Include locallang file
            $LANG->includeLLFile( 'EXT:ldap_macmade/locallang_db.php' );
            
            // Check extension configuration
            if ( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'ldap_macmade' ] ) ) {
                
                // Extension configuration array
                $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'ldap_macmade' ] );
                
                // Check backend authentification
                if ( isset( $extConf[ 'be_auth' ] ) ) {
                    
                    // Get element value
                    $elValue = $params[ 'itemFormElValue' ];
                    
                    // Get element name
                    $elName = $params[ 'itemFormElName' ];
                    
                    // Storage
                    $field = array();
                    
                    // Labels
                    $labelOn = $LANG->getLL( 'tx_ldapmacmade_server.be_auth.on' );
                    $labelOff = $LANG->getLL( 'tx_ldapmacmade_server.be_auth.off' );
                    
                    // Checked states
                    $checkedOn = ( $elValue ) ? ' checked' : '';
                    $checkedOff = ( !$elValue ) ? ' checked' : '';
                    
                    // On
                    $field[] = '<input type="radio" value="1" name="' . $elName . '"' . $checkedOn . '>';
                    $field[] = '&nbsp;' . $labelOn;
                    
                    // Break
                    $field[] = '<br>';
                    
                    // Off
                    $field[] = '<input type="radio" value="0" name="' . $elName . '"' . $checkedOff . '>';
                    $field[] = '&nbsp;' . $labelOff;
                    
                    // Return field
                    return implode( chr( 10 ), $field );
                    
                } else {
                    
                    // Error label
                    $error = $LANG->getLL( 'tx_ldapmacmade_server.be_auth.disabled' );
                    
                    // Not enabled
                    return '<span class="typo3-red">' . $error . '</span>';
                }
            }
        }
        
        /**
         * Rendres fe_auth field
         * 
         * This function renders the field fe_auth of table 'tx_ldapmacmade_server',
         * depending of the extension configuration. If the OpenLDAP authentification
         * features are enabled, it renders two radio buttons to set if the server can be
         * used for authentification. Otherwise, no fields are rendered.
         * 
         * @param       array       $params         Array of items passed by reference
         * @param       object      $pObj           The parent object (t3lib_TCEforms / t3lib_transferData depending on context)
         * @return      string      The field, depending of the extension configuration
         */
        function feAuth( &$params, &$pObj )
        {
            global $LANG;
            
            // Include locallang file
            $LANG->includeLLFile( 'EXT:ldap_macmade/locallang_db.php' );
            
            // Check extension configuration
            if ( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'ldap_macmade' ] ) ) {
                
                // Extension configuration array
                $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'ldap_macmade' ] );
                
                // Check backend authentification
                if ( isset( $extConf[ 'fe_auth' ] ) ) {
                    
                    // Get element value
                    $elValue = $params[ 'itemFormElValue' ];
                    
                    // Get element name
                    $elName = $params[ 'itemFormElName' ];
                    
                    // Storage
                    $field = array();
                    
                    // Labels
                    $labelOn = $LANG->getLL( 'tx_ldapmacmade_server.fe_auth.on' );
                    $labelOff = $LANG->getLL( 'tx_ldapmacmade_server.fe_auth.off' );
                    
                    // Checked states
                    $checkedOn = ( $elValue ) ? ' checked' : '';
                    $checkedOff = ( !$elValue ) ? ' checked' : '';
                    
                    // On
                    $field[] = '<input type="radio" value="1" name="' . $elName . '"' . $checkedOn . '>';
                    $field[] = '&nbsp;' . $labelOn;
                    
                    // Break
                    $field[] = '<br>';
                    
                    // Off
                    $field[] = '<input type="radio" value="0" name="' . $elName . '"' . $checkedOff . '>';
                    $field[] = '&nbsp;' . $labelOff;
                    
                    // Return field
                    return implode( chr( 10 ), $field );
                    
                } else {
                    
                    // Error label
                    $error = $LANG->getLL( 'tx_ldapmacmade_server.fe_auth.disabled' );
                    
                    // Not enabled
                    return '<span class="typo3-red">' . $error . '</span>';
                }
            }
        }
    }
    
    // XCLASS inclusion
    if ( defined( 'TYPO3_MODE' ) && isset( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/ldap_macmade/class.tx_ldapmacmade_tca.php' ] ) ) {
        include_once( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/ldap_macmade/class.tx_ldapmacmade_tca.php' ] );
    }
