<?php

# $Id: eid.inc.php 25 2010-06-21 08:05:58Z macmade $

// Security check
if( !defined( 'TYPO3_MODE' ) )
{
    // TYPO3 is not running
    trigger_error
    (
        'TYPO3 does not seem to be running. This script can only be used with TYPO3.',
        E_USER_ERROR
    );
}

tslib_eidtools::connectDB();

if( isset( $_GET[ 'pdf' ] ) )
{
    #define( 'FPDF_FONTPATH', t3lib_extMgm::extPath( 'alcoquizz' ) . 'res/' );
    
    setlocale( LC_ALL, 'fr_FR.utf8' );
    
    require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'classes/class.tx_alcoquizz_pdf_generator.php' );
    
    $pdf = new tx_alcoquizz_Pdf_Generator( $_GET[ 'pdf' ] );
    
    $pdf->out();
}
else
{
    set_include_path( t3lib_extMgm::extPath( 'alcoquizz' ) . 'lib/:' . get_include_path() );
    
    require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'lib/Zend/Amf/Server.php' );
    require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'classes/class.tx_alcoquizz_amf_service.php' );
    
    $server  = new Zend_Amf_Server();
    
    $server->setClass( 'tx_alcoquizz_Amf_Service' );
    
    print $server->handle();
}
