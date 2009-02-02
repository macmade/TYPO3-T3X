<?php

// Checks the permissions
$BE_USER->modAccess( $MCONF, 1 );

// Includes the backend module class
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class.tx_ooptest_module1.php' );

// Creates a new instance of the backend module
$SOBE = t3lib_div::makeInstance( 'tx_ooptest_module1' );

// Creates the module page
$SOBE->createModulePage();

// Writes the module content
print $SOBE;
