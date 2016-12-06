<?php

$XML = new SimpleXMLElement( '<?xml version="1.0" encoding="utf-8"?><select id="tx-ymregister-pi1-field-city" name="tx_ymregister_pi1[user][city]"></select>' );

if( $STATE = t3lib_div::_GET( 'stateId' ) ) {
    
    $CITIES = tx_oop_Database_Layer::getInstance()->getRecordsByFields(
        'tx_netdata_cities',
        array(
            'id_tx_netdata_states' => ( int )$STATE
        ),
        'fullname'
    );
    
    $XML->addChild( 'option' );
    
    foreach( $CITIES as $CITY ) {
        
        $CHILD            = $XML->addChild( 'option', htmlentities( $CITY->fullname ) );
        $CHILD[ 'value' ] = $CITY->uid;
    }
}

header( 'Content-type: text/xml' );
print $XML->asXML();
exit();
