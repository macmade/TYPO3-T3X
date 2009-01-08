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
 * Shopping cart class for the 'cjf' extension.
 * 
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

class tx_cjf_shoppingCart
{
    // Session key identifier
    var $sesKey = 'tx_cjf_shoppingcart';
    
    // FE user object
    var $user   = NULL;
    
    // Session data
    var $data   = array();
    
    /**
     * 
     */
    function tx_cjf_shoppingCart()
    {
        // Reference to the FE user object
        $this->user =& $GLOBALS[ 'TSFE' ]->fe_user;
        
        // Get session data
        $data = $this->user->getKey( 'ses', $this->sesKey );
        
        // Check for a valid array
        if( is_array( $data ) ) {
            
            // Store session data in class
            $this->data = $data;
        }
    }
    
    /**
     * 
     */
    function updateSessionData()
    {
        // Update session
        $this->user->setKey( 'ses', $this->sesKey, $this->data );
        return true;
    }
    
    /**
     * 
     */
    function addItem( $table, $id, $price, $quantity = 1 )
    {
        // Check if item is already in the cart
        if( isset( $this->data[ $id ] ) ) {
            
            // Increase quantity
            $this->data[ $id ][ 'quantity' ] += $quantity;
            
        } else {
            
            // Add data
            $this->data[ $id ] = array(
                'table' => $table,
                'price' => $price,
                'quantity' => $quantity,
            );
        }
        
        // Store new data
        $this->updateSessionData();
        
        // Return element session ID
        return true;
    }
    
    /**
     * 
     */
    function updateItemQuantity( $id, $quantity )
    {
        // Check if item exists
        if( isset( $this->data[ $id ] ) && is_numeric( $quantity ) ) {
            
            if( $quantity > 0 ) {
                
                // Update quantity
                $this->data[ $id ][ 'quantity' ] = ( int )$quantity;
                
            } else {
                
                $this->removeItem( $id );
            }
            
            // Store new data
            $this->updateSessionData();
        }
    }
    
    /**
     * 
     */
    function getItem( $id )
    {
        if( isset( $this->data[ $id ] ) ) {
            
            // Return item
            return $this->data[ $id ];
            
        } else {
            
            // Item not found
            return false;
        }
    }
    
    /**
     * 
     */
    function removeItem( $id )
    {
        // Remove item
        unset( $this->data[ $id ] );
        
        // Store new data
        $this->updateSessionData();
    }
    
    /**
     * 
     */
    function countItems()
    {
        // Number of items
        $count = 0;
        
        foreach( $this->data as $item ) {
            
            // Add price
            $count += $item[ 'quantity' ];
        }
        
        // Return total
        return $count;
    }
    
    /**
     * 
     */
    function getTotal()
    {
        // Price
        $total = 0;
        
        foreach( $this->data as $item ) {
            
            // Add price
            $total += $item[ 'price' ] * $item[ 'quantity' ];
        }
        
        // Return total
        return $total;
    }
    
    /**
     * 
     */
    function emptyCart()
    {
        // Empty data array
        $this->data = array();
        
        // Store in session
        $this->updateSessionData();
    }
}

 // XCLASS inclusion
if ( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/cjf/class.tx_cjf_shoppingcart.php' ] ) {
    include_once( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/cjf/class.tx_cjf_shoppingcart.php' ] );
}
