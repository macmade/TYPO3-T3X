<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 macmade.net
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
 * PDF class for the 'cjf' extension.
 * 
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *        :     function main( $content, $conf )
 * 
 *              TOTAL FUNCTIONS: 
 */

// FPDF class
require_once( t3lib_extMgm::extPath( 'fpdf' ) . 'class.tx_fpdf.php' );

class tx_cjf_pdf extends PDF
{
    
    // Header
    var $title       = '';
    
    // Parent object
    var $pObj        = NULL;
    
    // Client informations
    var $client      = array();
    
    // Order ID
    var $orderId     = '';
    
    // Order date
    var $date        = '';
    
    // Orders
    var $orders      = array();
    
    // Events
    var $events      = array();
    
    // Taxes for tickets
    var $ticketTaxes = 0;
    
    // Taxes for postal delivery
    var $postalTaxes = 0;
    
    // Currency
    var $currency    = '';
    
    // Order type
    var $orderType   = 0;
    
    /**
     * 
     */
    function Header()
    {
        global $LANG;
        
        // Font configuration
        $this->SetFont( 'Helvetica', 'B', 10 );
        
        // Left header
        $this->SetX( 20 );
        $this->Cell( 50, 5, $this->title, 0, 1, 'L' );
        $this->SetX( 20 );
        $this->Cell( 50, 5, $this->pObj->pi_getLL( 'pdf-confirmationHeader' ), 0, 1, 'L' );
        $this->Ln(10);
        
        // Order infos
        $this->SetFont( 'Helvetica', '', 10 );
        $this->SetX( 20 );
        $this->Cell( 40, 5, $this->pObj->pi_getLL( 'pdf-orderNum' ), 0, 0, 'L' );
        $this->Cell( 50, 5, $this->orderId, 0, 1, 'L' );
        $this->SetX( 20 );
        $this->Cell( 40, 5, $this->pObj->pi_getLL( 'pdf-date' ), 0, 0, 'L' );
        $this->Cell( 50, 5, $this->date, 0, 0, 'L' );
        $this->Ln( 10 );
        
        // Check order type
        if( $this->orderType == 1 ) {
            
            // Disclaimer
            $this->SetX( 20 );
            $this->SetFont( 'Helvetica', 'B', 8 );
            $this->MultiCell( 70, 3, $this->pObj->pi_getLL( 'pdf-disclaimer' ), 0, 'L' );
            $this->Ln( 5 );
            
            // Note
            $this->SetX( 20 );
            $this->SetTextColor( 255, 0, 0 );
            $this->MultiCell( 70, 3, $this->pObj->pi_getLL( 'pdf-note' ), 0, 'L' );
            $this->SetTextColor( 0, 0, 0 );
            
            // Right header
            $this->SetXY( 130, 10 );
            $this->SetFont( 'Helvetica', 'B', 15 );
            $this->Cell( 0, 6, $this->pObj->pi_getLL( 'pdf-voucher' ), 0, 1, 'L', 0 );
            $this->SetFont( 'Helvetica', '', 10 );
        }
        
        // Client data
        $this->SetXY( 130, 30 );
        $this->SetFillColor( 240, 240, 240 );
        $this->Cell( 0, 6, $this->pObj->pi_getLL( 'pdf-clientData' ), 0, 1, 'L', 0 );
        $this->SetXY( 125, 40 );
        $this->MultiCell( 55, 36, '', '', 'L', 1 );
        $this->SetXY( 130, 40 );
        $this->MultiCell( 50, 4, $this->createClientData(), '', 'L', 0 );
        $this->Ln(20);
    }
    
    /**
     * 
     */
    function createClientData()
    {
        // Storage
        $data = array();
        
        $data[] = '';
        $data[] = $this->client[ 'name_last' ] . ' ' . $this->client[ 'name_first' ];
        $data[] = $this->client[ 'address' ];
        $data[] = $this->client[ 'zip' ] . ' ' . $this->client[ 'city' ];
        $data[] = $this->client[ 'country' ];
        
        // Return data
        return implode( chr( 10 ), $data );
    }
    
    /**
     * 
     */
    function orderInfos()
    {
        // Positions
        $fieldPosY = 120;
        $tablePosY = 130;
        
        // Header
        $this->SetXY( 20, $fieldPosY - 10 );
        $this->SetFont( 'Helvetica', 'B', 12 );
        $this->Cell( 0, 6, $this->pObj->pi_getLL( 'pdf-detailsHeader' ), 0, 0, 'L', 0 );
        
        // Table headers
        $this->SetXY( 20, $fieldPosY );
        $this->SetFont( 'Helvetica', '', 10 );
        $this->SetFillColor( 240, 240, 240 );
        $this->Cell( 10, 6, $this->pObj->pi_getLL( 'pdf-order-id' ), 1, 0, 'C', 1 );
        $this->Cell( 20, 6, $this->pObj->pi_getLL( 'pdf-order-date' ), 1, 0, 'C', 1 );
        $this->Cell( 70, 6, $this->pObj->pi_getLL( 'pdf-order-event' ), 1, 0, 'L', 1 );
        $this->Cell( 20, 6, $this->pObj->pi_getLL( 'pdf-order-quantity' ), 1, 0, 'C', 1 );
        $this->Cell( 20, 6, $this->pObj->pi_getLL( 'pdf-order-price' ), 1, 0, 'R', 1 );
        $this->Cell( 20, 6, $this->pObj->pi_getLL( 'pdf-order-total' ), 1, 0, 'R', 1 );
        $this->Ln();
        
        // Order items
        $this->SetFont( 'Helvetica', '', 8 );
        
        // Storage
        $ids        = array();
        $dates      = array();
        $events     = array();
        $quantities = array();
        $prices     = array();
        $totals     = array();
        
        // Number of tickets
        $itemsNum = 0;
        
        // Order total
        $orderTotal = 0;
        
        // Process each order
        foreach( $this->orders as $key => $value ) {
            
            // Event row
            $eventRow = $this->events[ $value[ 'id_event' ] ];
            
            // Add details
            $ids[]        = $value[ 'id_event' ];
            $dates[]      = date( 'd.m.Y', $eventRow[ 'date' ] );
            $events[]     = $eventRow[ 'title' ];
            $quantities[] = $value[ 'quantity' ];
            $prices[]     = number_format( $value[ 'price' ], 2, '.', '\'' );
            $totals[]     = number_format( $value[ 'total' ], 2, '.', '\'' );
            
            // Increase total
            $orderTotal += $value[ 'price' ] * $value[ 'quantity' ];
            
            // Increase number of tickets
            $itemsNum += $value[ 'quantity' ];
        }
        
        // Blank line
        $ids[]        = ' ';
        $dates[]      = ' ';
        $events[]     = ' ';
        $quantities[] = ' ';
        $prices[]     = ' ';
        $totals[]     = ' ';
        
        // Ticket taxes
        $ticketTaxes = $this->ticketTaxes * $itemsNum;
        
        // ID
        $this->SetY( $tablePosY );
        $this->SetX( 20 );
        $this->MultiCell( 10, 4, implode( chr( 10 ), $ids ), 'B', 'C' );
        
        // Date
        $this->SetY( $tablePosY );
        $this->SetX( 30 );
        $this->MultiCell( 20, 4, implode( chr( 10 ), $dates ), 'B', 'C' );
        
        // Event
        $this->SetY( $tablePosY );
        $this->SetX( 50 );
        $this->MultiCell( 70, 4, implode( chr( 10 ), $events ), 'B', 'L' );
        
        // Quantity
        $this->SetY( $tablePosY );
        $this->SetX( 120 );
        $this->MultiCell( 20, 4, implode( chr( 10 ), $quantities ), 'B', 'C' );
        
        // Price
        $this->SetY( $tablePosY );
        $this->SetX( 140 );
        $this->MultiCell( 20, 4, implode( chr( 10 ), $prices ), 'B', 'R' );
        
        // Total
        $this->SetY( $tablePosY );
        $this->SetX( 160 );
        $this->MultiCell( 20, 4, implode( chr( 10 ), $totals ), 'B', 'R' );
        
        // Order total
        $this->SetFillColor( 240, 240, 240 );
        $this->SetX( 120 );
        $this->Cell( 40, 6, $this->pObj->pi_getLL( 'pdf-subtotal' ) . ' ' . $this->currency, '', 0, 'R' , 0 );
        $this->Cell( 20, 6, number_format( $orderTotal, 2, '.', '\'' ), '', 1, 'R', 0 );
        
        // Pay taxes
        if( $ticketTaxes > 0 ) {
            $this->SetX( 120 );
            $this->Cell( 40, 6, $this->pObj->pi_getLL( 'pdf-paytaxes' ) . ' ' . $this->currency, '', 0, 'R', 0 );
            $this->Cell( 20, 6, number_format( $ticketTaxes, 2, '.', '\''), 'T,B', 1, 'R', 0 );
        }
        
        // Postal taxes
        if( $this->orderType == 0 && $this->postalTaxes > 0 ) {
            $this->SetX( 120 );
            $this->Cell( 40, 6, $this->pObj->pi_getLL( 'pdf-postaltaxes' ) . ' ' . $this->currency, '', 0, 'R', 0 );
            $this->Cell( 20, 6, number_format( $this->postalTaxes, 2, '.', '\''), 'T,B', 1, 'R', 0 );
        }
        
        // Final total
        $finalTotal = ( $this->orderType == 1 ) ? $orderTotal + $ticketTaxes : $orderTotal + $ticketTaxes + $this->postalTaxes;
        
        // Final total
        $this->SetX( 120 );
        $this->SetLineWidth( 0.5 );
        $this->Cell( 40, 6, $this->pObj->pi_getLL( 'pdf-total' ) . ' ' . $this->currency, '', 0, 'R', 0 );
        $this->Cell( 20, 6, number_format( $finalTotal, 2, '.', '\'' ), 'T,B', 1, 'R', 0 );
    }
}

 // XCLASS inclusion
if ( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/cjf/class.tx_cjf_pdf.php' ] ) {
    include_once( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/cjf/class.tx_cjf_pdf.php' ] );
}
