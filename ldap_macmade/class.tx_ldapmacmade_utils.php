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
     * Class 'tx_ldapmacmade_utils' for the 'ldap_macmade' extension.
     *
     * @author          Jean-David Gadina (info@macmade.net)
     * @version         1.0
     */
    
    /**
     * [CLASS/FUNCTION INDEX OF SCRIPT]
     * 
     *        :        class tx_ldapmacmade_utils
     *      75:        function createLDAP( $server )
     *     120:        function cleanEntries( $entries )
     *     177:        function sortEntries( $entries, $sortKey )
     *     219:        function checkLDAPField( $user, $field )
     *     248:        function substituteLDAPValue( $ldapField, $ldapValue )
     *     281:        function ldap2BE( $user, $server, $mode )
     *     378:        function ldap2FE( $user, $server, $mode )
     *     475:        function mapFields( $fields, $xmlds, $user )
     *     529:        function importGroups( $user, $server, $type )
     * 
     *                TOTAL FUNCTIONS: 9
     */
    
    // LDAP class
    require_once ( t3lib_extMgm::extPath( 'ldap_macmade' ) . 'class.tx_ldapmacmade_div.php' );
    
    // Developer API class
    require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );
    
    // Backend class
    require_once ( PATH_t3lib . 'class.t3lib_befunc.php' );
    
    class tx_ldapmacmade_utils
    {
        
        /**
         * Create a LDAP server instance
         * 
         * This function creates an instance of the LDAP helper class.
         * 
         * @param       array       $server         An OpenLDAP server row
         * @return      array       An array with the LDAP results and errors if any
         */
        function createLDAP( $server )
        {
            
            // Configuration array
            $conf = array(
                'host'     => $server[ 'address' ],
                'port'     => $server[ 'port' ],
                'version'  => $server[ 'version' ],
                'user'     => $server[ 'user' ],
                'tls'      => $server[ 'tls' ],
                'password' => $server[ 'password' ],
                'baseDN'   => $server[ 'basedn' ],
                'filter'   => $server[ 'filter' ],
            );
            
            // New LDAP class
            $ldap = t3lib_div::makeInstance( 'tx_ldapmacmade_div' );
            
            // Set LDAP class configuration array
            $ldap->conf = $conf;
            
            // Initialization of the LDAP class
            $ldap->init();
            
            // Results
            $results = array(
                'version' => $ldap->pver,
                'bind'    => $ldap->r,
                'search'  => $ldap->sr,
                'count'   => $ldap->num,
                'entries' => $ldap->info,
            );
            
            // Return results and errors array
            return array( $results, $ldap->errors );
        }
        
        /**
         * Clean LDAP entries
         * 
         * This function cleans an array of LDAP entries, to allow a smooth
         * processing.
         * 
         * @param       array       $entries        An array with LDAP entries
         * @return      array       The cleaned array
         */
        function cleanEntries( $entries )
        {
            
            // Check for an array
            if ( is_array( $entries ) && count( $entries ) ) {
                
                // Check every element
                foreach ( $entries as $key => $val ) {
                    
                    // Check for an array
                    if ( is_array( $val ) ) {
                        
                        // Check for a value
                        if ( isset( $val[ 'count' ] ) && count( $val ) == 2 ) {
                            
                            // Replace array with value
                            $entries[ $key ] = $val[ 0 ];
                            
                        } else {
                            
                            // Process subarray
                            $entries[ $key ] = $this->cleanEntries( $val );
                        }
                    } else if ( is_int( $key ) ) {
                        
                        // Integer key / Check redundant information
                        if ( $val == $lastKey ) {
                            
                            // Unset data
                            unset( $entries[ $key ] );
                        }
                    } else if ( $key == 'count' ) {
                        
                        // LDAP count information
                        unset( $entries[ $key ] );
                    }
                    
                    // Memorize key
                    $lastKey = $key;
                    
                    // Force string
                    settype( $lastKey, 'string' );
                }
                
                // Return clean array
                return $entries;
            }
        }
        
        /**
         * Sort LDAP entries
         * 
         * This function sorts an array of LDAP entries.
         * 
         * @param       array       $entries        An array with LDAP entries
         * @param       string      $sortKey        The LDAP field used for sorting
         * @return      array       The sorted array
         */
        function sortEntries( $entries, $sortKey )
        {
            
            // Storage
            $data = array();
            
            // Process input array
            foreach( $entries as $key => $val ) {
                
                // Check key
                if ( array_key_exists( $sortKey, $val ) ) {
                    
                    // Get sort field
                    $field = $this->checkLDAPField( $val, $sortKey );
                    
                    // Set data
                    $data[ $field ] = $val;
                    
                } else {
                    
                    // Don't change
                    $data[ $key ] = $val;
                }
            }
            
            // Sort array
            ksort($data);
            
            // Return sorted array
            return $data;
        }
        
        /**
         * Check LDAP value
         * 
         * This function is used to check a LDAP value. In LDAP, a field value
         * can be an array with subvalues. In that case, the function returns
         * only the first subvalue.
         * 
         * @param       array       $user           The LDAP user
         * @param       string      $field          The LDAP field
         * @return      string      The final value
         */
        function checkLDAPField( $user, $field )
        {
            
            // Check if the field exists
            if ( is_array( $user ) && isset( $user[ $field ] ) ) {
                
                // Check if field is an array
                if ( is_array( $user[ $field ] ) && count( $user[ $field ] ) ) {
                    
                    // Return first value
                    return array_shift( $user[ $field ] );
                    
                } else {
                    
                    // Return value
                    return $user[ $field ];
                }
            }
        }
        
        /**
         * Substitute LDAP value
         * 
         * This function is used to check if a LDAP value must be substituted
         * by a fixed value.
         * 
         * @param       string      $ldapField      The field to process
         * @param       array       $user           The LDAP user
         * @return      string      The final value
         */
        function substituteLDAPValue( $ldapField, $user )
        {
            
            // Check for a valid string
            if ( is_string( $ldapField ) ) {
                
                // Get parts
                $parts = preg_split( '/\|/', $ldapField );
                
                // Storage
                $import = array();
                
                // Process parts
                foreach( $parts as $part ) {
                    
                    // Checking field type
                    if ( substr( $part, 0, 8 ) == '[STATIC]' ) {
                        
                        // Static data
                        $import[] = substr( $part, 8 );
                        
                    } else if ( substr( $part, 0, 6 ) == '[LDAP:' ) {
                        
                        // Storage
                        $regs = array();
                
                        // Find LDAP field
                        ereg( '\[LDAP:([^]]+)\]', $part, $regs );
                
                        // Try to get LDAP field
                        if ( $field = $this->checkLDAPField( $user, $regs[1] ) ) {
                        
                            // Specific LDAP field
                            $import[] = $field;
                        }
                        
                    } else {
                        
                        // LDAP data
                        $import[] = $this->checkLDAPField( $user, $part );
                    }
                }
                
                // Return processed field
                return implode( '', $import );
            }
        }
        
        /**
         * Map additionnal fields.
         * 
         * This function is used to build an associative array with the optional
         * fields to import or udpate.
         * 
         * @param       array       $fields         An associative array with fields to map
         * @param       string      $xmlds          The flexform data with mapping informations
         * @param       array       $user           The LDAP user
         * @return      array       The import array
         */
        function mapFields($fields,$xmlds,$user)
        {
            
            // Flexform as an array
            $flex = t3lib_div::xml2array($xmlds);
            
            // Storage
            $import = array();
            
            // Check array 
            if ( isset( $flex[ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'fields' ][ 'el' ] ) ) {
                
                // Mapping data
                $data = $flex[ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'fields' ][ 'el' ];
                
                // Check array
                if ( is_array( $data ) && count( $data ) ) {
                    
                    // Process each fields
                    foreach( $data as $map ) {
                        
                        // Field data control
                        $checkType  = isset( $map[ 'field' ][ 'el' ][ 'type' ][ 'vDEF' ] );
                        $checkValue = isset( $map[ 'field' ][ 'el' ][ 'ldap' ][ 'vDEF' ] );
                        
                        // Check field data
                        if ( $checkType && $checkValue ) {
                            
                            // Field type
                            $type = $map[ 'field' ][ 'el' ][ 'type' ][ 'vDEF' ];
                            
                            // Field value
                            $value = $map[ 'field' ][ 'el' ][ 'ldap' ][ 'vDEF' ];
                            
                            // Check if field must be imported
                            if ( array_key_exists( $type, $fields ) ) {
                                
                                // Add field
                                $import[ $fields[ $type ] ] = $this->substituteLDAPValue( $value, $user );
                            }
                        }
                    }
                }
            }
            
            // Return import array
            return $import;
        }
        
        /**
         * Get a backend user.
         * 
         * This function import or udpate a user from the LDAP server to the backend
         * users table.
         * 
         * @param       array       $user           The LDAP user
         * @param       array       $server         The LDAP server
         * @param       string      $mode           The mode (IMPORT or UPDATE)
         * @return      null
         */
        function ldap2BE( $user, $server, $mode )
        {
            
            // Check for valid arrays
            if ( is_array( $user ) && is_array( $server ) ) {
                
                // Storage
                $insertFields = array();
                
                // Current BE user
                $cruser = 0;
                
                // Check for a BE user
                if ( is_object( $GLOBALS[ 'BE_USER' ] ) && is_array( $GLOBALS[ 'BE_USER' ]->user ) ) {
                    
                    // Set current BE user for creation
                    $cruser = $GLOBALS[ 'BE_USER' ]->user[ 'uid' ];
                }
                
                // Check mode
                if ( $mode == 'IMPORT' ) {
                    
                    // Static fields to insert for import
                    $insertFields[ 'pid' ]       = 0;
                    $insertFields[ 'crdate' ]    = time();
                    $insertFields[ 'cruser_id' ] = $cruser;
                }
                
                // Modification date
                $insertFields[ 'tstamp' ] = time();
                
                // Username
                $insertFields[ 'username' ] = $this->checkLDAPField( $user, $server[ 'mapping_username' ] );
                
                // Get password rule
                $pwdRule = $server[ 'be_pwdrule' ];
                
                // Storage
                $regs = array();
                
                // Find LDAP field
                ereg( '\[LDAP:([^]]+)\]', $pwdRule, $regs );
                
                // Try to get LDAP field
                if ( $ldapField = $this->checkLDAPField( $user, $regs[ 1 ] ) ) {
                    
                    // Replace pattern
                    $password = ereg_replace( '\[LDAP:[^]]+\]', $ldapField, $pwdRule );
                }
                
                // Password
                $insertFields[ 'password' ] = md5( $password );
                
                // Lang
                $insertFields[ 'lang' ] = $server[ 'be_lang' ];
                
                // TS Config
                $insertFields[ 'TSconfig' ] = $server[ 'be_tsconf' ];
                
                // Fields to map
                $map = array( 'name' => 'realName', 'email' => 'email' );
                
                // Additionnal fields
                $additionnalFields = $this->mapFields( $map, $server['mapping'], $user );
                
                // Import BE groups from LDAP?
                $ldapGroups = ($server['be_groups_import']) ? $this->importGroups($user,$server,'BE') : array();
                
                // Add fixed BE groups
                $additionnalGroups = ($server['be_groups_fixed']) ? explode(',',$server['be_groups_fixed']) : array();
                
                // Merge groups
                $groups = array_merge($ldapGroups,$additionnalGroups);
                
                // Add groups for user
                $insertFields['usergroup'] = implode(',',$groups);
                
                // Merge arrays
                $insert = array_merge($insertFields,$additionnalFields);
                
                // Check mode
                if ($mode == 'IMPORT') {
                    
                    // MySQL INSERT query
                    if ($GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users',$insert)) {
                        
                        // Store UID
                        $uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
                    }
                    
                } else if ($mode == 'UPDATE') {
                    
                    // Get existing UID
                    $be_users = t3lib_BEfunc::getRecordsByField('be_users','username',$insert['username']);
                    
                    // Get first one (if many)
                    $be_user = array_shift($be_users);
                    
                    // MySQL UPDATE query
                    if ($GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users','uid=' . $be_user['uid'],$insert)) {
                        
                        // Store UID
                        $uid = $be_user['uid'];
                    }
                }
                
                // Check for a UID
                if (isset($uid)) {
                    
                    // Return UID
                    return $uid;
                }
            }
        }
        
        /**
         * Get a frontend user.
         * 
         * This function import or update a user from the LDAP server to the frontend
         * users table.
         * 
         * @param        $user                The LDAP user
         * @param        $server                The LDAP server
         * @param        $mode                The mode (IMPORT or UPDATE)
         * @return        Void
         */
        function ldap2FE($user,$server,$mode) {
            
            // Check for valid arrays
            if (is_array($user) && is_array($server)) {
                
                // Storage
                $insertFields = array();
                
                // Check mode
                if ($mode == 'IMPORT') {
                    
                    // Static fields to insert
                    $insertFields['pid'] = $server['pid'];
                    $insertFields['crdate'] = time();
                    $insertFields['cruser_id'] = (is_array($GLOBALS['BE_USER']->user)) ? $GLOBALS['BE_USER']->user['uid'] : 0;
                }
                
                // Modification date
                $insertFields['tstamp'] = time();
                
                // Username
                $insertFields['username'] = $this->checkLDAPField($user,$server['mapping_username']);
                
                // Get password rule
                $pwdRule = $server['be_pwdrule'];
                
                // Storage
                $regs = array();
                
                // Find LDAP field
                ereg('\[LDAP:([^]]+)\]',$pwdRule,$regs);
                
                // Try to get LDAP field
                if ($ldapField = $this->checkLDAPField($user,$regs[1])) {
                    
                    // Replace pattern
                    $password = ereg_replace('\[LDAP:[^]]+\]',$ldapField,$pwdRule);
                }
                
                // Password
                $insertFields['password'] = $password;
                
                // Lang
                $insertFields['lockToDomain'] = $server['fe_lock'];
                
                // TS Config
                $insertFields['TSconfig'] = $server['be_tsconf'];
                
                // Additionnal fields
                $additionnalFields = $this->mapFields(array('name'=>'name','address'=>'address','phone'=>'telephone','fax'=>'fax','email'=>'email','title'=>'title','zip'=>'zip','city'=>'city','country'=>'country','www'=>'www','company'=>'company'),$server['mapping'],$user);
                
                // Import BE groups from LDAP?
                $ldapGroups = ($server['fe_groups_import']) ? $this->importGroups($user,$server,'FE') : array();
                
                // Add fixed BE groups
                $additionnalGroups = ($server['fe_groups_fixed']) ? explode(',',$server['fe_groups_fixed']) : array();
                
                // Merge groups
                $groups = array_merge($ldapGroups,$additionnalGroups);
                
                // Add groups for user
                $insertFields['usergroup'] = implode(',',$groups);
                
                // Merge arrays
                $insert = array_merge($insertFields,$additionnalFields);
                
                // Check mode
                if ($mode == 'IMPORT') {
                    
                    // MySQL INSERT query
                    if ($GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users',$insert)) {
                        
                        // Store UID
                        $uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
                    }
                    
                } else if ($mode == 'UPDATE') {
                    
                    // Get existing UID
                    $fe_users = t3lib_BEfunc::getRecordsByField('fe_users','username',$insert['username'],' AND pid=' . $server['pid']);
                    
                    // Get first one (if many)
                    $fe_user = array_shift($fe_users);
                    
                    // MySQL UPDATE query
                    if ($GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid=' . $fe_user['uid'],$insert)) {
                        
                        // Store UID
                        $uid = $fe_user['uid'];
                    }
                }
                
                // Check for a UID
                if (isset($uid)) {
                    
                    // Return UID
                    return $uid;
                }
            }
        }
        
        /**
         * Import LDAP groups.
         * 
         * This function import BE & FE groups for a user from the LDAP server.
         * 
         * @param        $user                The LDAP user
         * @param        $server                The LDAP server
         * @param        $type                The import type (BE or FE)
         * @return        Void
         */
        function importGroups($user,$server,$type) {
            
            // User ID (user ID for the posixGroup schema, otherwise the user DN)
            $userId = ($server['group_class'] == 'posixGroup' && $server['group_member'] == 'memberUid') ? $this->checkLDAPField($user,$server['mapping_username']) : $this->checkLDAPField($user,'dn');
            
            // Search filter for groups
            $filter = '(&(objectClass=' . $server['group_class'] . ')(' . $server['group_member'] . '=' . $userId . '))';
            
            // Replace search filter
            $server['filter'] = $filter;
            
            // LDAP
            $ldap = $this->createLDAP($server);
            
            // Storage
            $importGroups = array();
            
            // Check LDAP entries
            if (tx_apimacmade::div_checkArrayKeys($ldap,'0,entries') && is_array($ldap[0]['entries'])) {
                
                // Clean LDAP entries array
                $entries = $this->cleanEntries($ldap[0]['entries']);
                
                // Process each groups
                foreach($entries as $group) {
                    
                    // Get group name
                    $groupName = $this->checkLDAPField($group,'cn');
                    
                    // Check import type (BE or FE)
                    if ($type == 'BE') {
                        
                        // Try to get existing BE group
                        $t3_group = t3lib_BEfunc::getRecordsByField('be_groups','title',$groupName);
                        
                        // Check if group must be imported
                        if (!$t3_group) {
                            
                            // Storage
                            $insertFields = array();
                            
                            // Static fields to insert
                            $insertFields['pid'] = 0;
                            $insertFields['tstamp'] = time();
                            $insertFields['crdate'] = time();
                            $insertFields['cruser_id'] = (is_array($GLOBALS['BE_USER']->user)) ? $GLOBALS['BE_USER']->user['uid'] : 0;
                            
                            // Group name
                            $insertFields['title'] = $groupName;
                            
                            // MySQL INSERT query
                            $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_groups',$insertFields);
                            
                            // Select group
                            $t3_group = t3lib_BEfunc::getRecordsByField('be_groups','title',$groupName);
                        }
                        
                    } else if ($type == 'FE') {
                        
                        // Try to get existing FE group
                        $t3_group = t3lib_BEfunc::getRecordsByField('fe_groups','title',$groupName);
                        
                        // Check if group must be imported
                        if (!$t3_group) {
                            
                            // Storage
                            $insertFields = array();
                            
                            // Static fields to insert
                            $insertFields['pid'] = $server['pid'];
                            $insertFields['tstamp'] = time();
                            
                            // Group name
                            $insertFields['title'] = $groupName;
                            
                            // MySQL INSERT query
                            $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_groups',$insertFields);
                            
                            // Select group
                            $t3_group = t3lib_BEfunc::getRecordsByField('fe_groups','title',$groupName);
                        }
                    }
                    
                    // Check group array
                    if (isset($t3_group) && is_array($t3_group)) {
                        
                        // Process each groups (if many)
                        foreach($t3_group as $key=>$value) {
                            
                            // Add GID
                            $importGroups[] = $value['uid'];
                        }
                    }
                }
                
                // Remove duplicates if any and return GIDs
                return array_unique($importGroups);
            }
        }
    }
    
    // XCLASS inclusion
    if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/class.tx_ldapmacmade_utils.php']) {
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/class.tx_ldapmacmade_utils.php']);
    }
