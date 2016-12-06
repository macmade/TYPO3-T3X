<?php

# $Id: ext_localconf.php 165 2009-12-15 13:44:10Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Adds the frontend plugins
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 1 );

// Registers the classes directory
tx_oop_Typo3_ClassManager::getInstance()->registerClassDir(
    'tx_marvinaddresses_',
    t3lib_extMgm::extPath( $_EXTKEY ) . 'classes' . DIRECTORY_SEPARATOR,
    true
);

// Adds the save & new buttons
tx_oop_Typo3_Utils::addSaveAndNewButton( $_EXTKEY, 'addresses' );

if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_addresses' ] ) ) {
    
    $EXT_CONF = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_addresses' ] );
   
    if( isset( $EXT_CONF[ 'realurl_autoconf' ] ) && $EXT_CONF[ 'realurl_autoconf' ] ) {
        
        if(    t3lib_extMgm::isLoaded( 'realurl' )
            && isset( $TYPO3_CONF_VARS[ 'EXTCONF' ][ 'realurl' ] )
            && is_array( $TYPO3_CONF_VARS[ 'EXTCONF' ][ 'realurl' ] )
        ) {
            
            $REALURL =& $TYPO3_CONF_VARS[ 'EXTCONF' ][ 'realurl' ];
            
            if( !isset( $REALURL[ '_DEFAULT' ] ) ) {
                
                $REALURL[ '_DEFAULT' ] = array();
            }
            
            if( !isset( $REALURL[ '_DEFAULT' ][ 'postVarSets' ] ) ) {
                
                $REALURL[ '_DEFAULT' ][ 'postVarSets' ] = array();
            }
            
            if( !isset( $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ] ) ) {
                
                $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ] = array();
            }
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'show-addresses' ] = array(
                array(
                    'GETvar'   => 'tx_marvinaddresses_pi1[action]',
                    'valueMap' => array(
                        'list-cities'    => 'address-country',
                        'list-addresses' => 'address-city'
                    )
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'country' ] = array(
                array(
                    'GETvar'      => 'tx_marvinaddresses_pi1[country]',
                    'lookUpTable' => array(
                        'table'               => 'tx_marvindata_countries',
                        'id_field'            => 'uid',
                        'alias_field'         => 'fullname',
                        'addWhereClause'      => ' AND NOT deleted',
                        'useUniqueCache'      => 1,
                        'useUniqueCache_conf' => array(
                            'strtolower'     => 1,
                            'spaceCharacter' => '-'
                        )
                    )
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'city' ] = array(
                array(
                    'GETvar'      => 'tx_marvinaddresses_pi1[city]',
                    'lookUpTable' => array(
                        'table'               => 'tx_marvindata_cities',
                        'id_field'            => 'uid',
                        'alias_field'         => 'fullname',
                        'addWhereClause'      => ' AND NOT deleted',
                        'useUniqueCache'      => 1,
                        'useUniqueCache_conf' => array(
                            'strtolower'     => 1,
                            'spaceCharacter' => '-'
                        )
                    )
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'show-page' ] = array(
                array(
                    'GETvar' => 'tx_marvinaddresses_pi1[page]'
                )
            );
            
            unset( $REALURL );
        }
    }
    
    unset( $EXT_CONF );
}

?>
