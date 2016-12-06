<?php

# $Id: ext_localconf.php 183 2010-01-04 12:36:28Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Adds the frontend plugins
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 1 );

// Registers the classes directory
tx_oop_Typo3_ClassManager::getInstance()->registerClassDir(
    'tx_marvincare_',
    t3lib_extMgm::extPath( $_EXTKEY ) . 'classes' . DIRECTORY_SEPARATOR,
    true
);

// Adds the save & new buttons
tx_oop_Typo3_Utils::addSaveAndNewButton( $_EXTKEY, 'manuals' );
tx_oop_Typo3_Utils::addSaveAndNewButton( $_EXTKEY, 'movements' );

if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] ) ) {
    
    $EXT_CONF = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] );
    
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
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'display' ] = array(
                array(
                    'GETvar'   => 'tx_marvincare_pi1[action]',
                    'valueMap' => array(
                        'files' => 'manual-files'
                    )
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'movement' ] = array(
                array(
                    'GETvar'      => 'tx_marvincare_pi1[movement]',
                    'lookUpTable' => array(
                        'table'               => 'tx_marvincare_movements',
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
            
            unset( $REALURL );
        }
    }
    
    unset( $EXT_CONF );
}

?>
