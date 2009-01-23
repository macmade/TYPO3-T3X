<?php

// TYPO3 module path
define( 'TYPO3_MOD_PATH', '../typo3conf/ext/phpmyadmin_macmade/mod1/' );

// Back path to the TYPO3 directory
$BACK_PATH = '../../../../typo3/';

// Module infos
$MCONF['name']                              = 'tools_txphpmyadminmacmadeM1';
$MCONF['access']                            = 'admin';
$MCONF['script']                            = 'index.php';
$MLANG['default'][ 'tabs_images' ][ 'tab' ] = 'moduleicon.png';
$MLANG['default'][ 'll_ref' ]               = 'LLL:EXT:phpmyadmin_macmade/lang/mod1.xml';
