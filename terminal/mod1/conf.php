<?php

// TYPO3 module path
define( 'TYPO3_MOD_PATH', '../typo3conf/ext/terminal/mod1/' );

// Back path to the TYPO3 directory
$BACK_PATH = '../../../../typo3/';

// Module infos
$MCONF['name']                              = 'tools_txterminalM1';
$MCONF['access']                            = 'admin';
$MCONF['script']                            = 'index.php';
$MLANG['default'][ 'tabs_images' ][ 'tab' ] = 'moduleicon.gif';
$MLANG['default'][ 'll_ref' ]               = 'LLL:EXT:terminal/mod1/locallang_mod.xml';
