<?php

# $Id: ext_localconf.php 194 2010-01-27 08:55:55Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Adds the frontend plugins
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 1 );
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 2 );
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 3 );

// Registers the classes directory
tx_oop_Typo3_ClassManager::getInstance()->registerClassDir(
    'tx_marvincatalog_',
    t3lib_extMgm::extPath( $_EXTKEY ) . 'classes' . DIRECTORY_SEPARATOR,
    true
);

$TYPO3_CONF_VARS[ 'FE' ][ 'eID_include' ][ $_EXTKEY . '_categories' ] = 'EXT:' . $_EXTKEY . '/scripts/categories.inc.php';
$TYPO3_CONF_VARS[ 'FE' ][ 'eID_include' ][ $_EXTKEY . '_catalog' ]    = 'EXT:' . $_EXTKEY . '/scripts/catalog.inc.php';
$TYPO3_CONF_VARS[ 'FE' ][ 'eID_include' ][ $_EXTKEY . '_moods' ]    = 'EXT:' . $_EXTKEY . '/scripts/moods.inc.php';

$TYPO3_CONF_VARS[ 'SC_OPTIONS' ][ 't3lib/class.t3lib_tcemain.php' ][ 'clearCachePostProc' ][] = 'tx_marvincatalog_Hook_Cache->clearCache';

if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] ) ) {
    
    $EXT_CONF = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] );
    
    if( isset( $EXT_CONF[ 'magento_dir' ] ) && $EXT_CONF[ 'magento_dir' ] ) {
        
        if( isset( $TYPO3_CONF_VARS[ 'FE' ][ 'addAllowedPaths' ] ) && $TYPO3_CONF_VARS[ 'FE' ][ 'addAllowedPaths' ] ) {
            
            $TYPO3_CONF_VARS[ 'FE' ][ 'addAllowedPaths' ] .= ',' . $EXT_CONF[ 'magento_dir' ];
            
        } else {
            
            $TYPO3_CONF_VARS[ 'FE' ][ 'addAllowedPaths' ] = $EXT_CONF[ 'magento_dir' ];
        }
    }
    
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
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'collection' ] = array(
                array(
                    'GETvar' => 'tx_marvincatalog_pi1[collection]'
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'show' ] = array(
                array(
                    'GETvar'   => 'tx_marvincatalog_pi1[action]',
                    'valueMap' => array(
                        'list'       => 'catalog-category',
                        'details'    => 'catalog-watch',
                        'send-email' => 'catalog-sendEmail'
                    )
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'category' ] = array(
                array(
                    'GETvar' => 'tx_marvincatalog_pi1[category]'
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'watch' ] = array(
                array(
                    'GETvar' => 'tx_marvincatalog_pi1[watch]'
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'mobile-show' ] = array(
                array(
                    'GETvar'   => 'tx_marvincatalog_pi3[action]',
                    'valueMap' => array(
                        'list'       => 'mobileCatalog-category',
                        'details'    => 'mobileCatalog-watch'
                    )
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'mobile-category' ] = array(
                array(
                    'GETvar' => 'tx_marvincatalog_pi3[category]'
                )
            );
            
            $REALURL[ '_DEFAULT' ][ 'postVarSets' ][ '_DEFAULT' ][ 'mobile-watch' ] = array(
                array(
                    'GETvar' => 'tx_marvincatalog_pi3[watch]'
                )
            );
            
            unset( $REALURL );
        }
    }
    
    unset( $EXT_CONF );
}

?>
