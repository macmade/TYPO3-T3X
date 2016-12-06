<?php

/*******************************************************************************
 * Copyright (c) 2009, Jean-David Gadina <macmade@eosgarden.com>
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 
 *  -   Redistributions of source code must retain the above copyright notice,
 *      this list of conditions and the following disclaimer.
 *  -   Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *  -   Neither the name of 'Jean-David Gadina' nor the names of its
 *      contributors may be used to endorse or promote products derived from
 *      this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 ******************************************************************************/

/* $Id: Dictionary.class.php 2 2010-06-21 07:43:04Z macmade $ */

class Property_List_Dictionary extends Property_List_Element implements Countable, Iterator, ArrayAccess
{
    protected $_items    = array();
    protected $_iterator = 0;
    
    public function __construct( array $items = array() )
    {
        foreach( $items as $key => $value )
        {
            $this->$key = $value;
        }
    }
    
    public function __toString()
    {
        $str = '<dict>' . chr( 10 );
        
        foreach( $this->_items as $key => $value )
        {
            $str .= '<key>' . $key . '</key>' . chr( 10 );
            $str .= ( string )$value . chr( 10 );
        }
        
        $str .= '</dict>';
        
        return $str;
    }
    
    public function __get( $property )
    {
        return $this->_items[ $property ];
    }
    
    public function __set( $property, $value )
    {
        if( is_numeric( $value ) )
        {
            $this->_items[ $property ] = new Property_List_Number( $value );
        }
        elseif( is_string( $value ) )
        {
            $this->_items[ $property ] = new Property_List_String( $value ) ;
        }
        elseif( is_array( $value ) )
        {
            $this->_items[ $property ] = new Property_List_Dictionary( $value );
        }
        elseif( is_bool( $value ) )
        {
            $this->_items[ $property ] = new Property_List_Boolean( $value );
        }
        elseif( $value instanceof DateTime )
        {
            $this->_items[ $property ] = new Property_List_Date( $value );
        }
        elseif( $value instanceof Property_List_Element )
        {
            $this->_items[ $property ] = $value;
        }
        else
        {
            throw new Exception();
        }
    }
    
    public function __isset( $property )
    {
        return isset( $this->_items[ $property ] );
    }
    
    public function __unset( $property )
    {
        unset( $this->_items[ $property ] );
    }
    
    public function count()
    {
        return count( $this->_items );
    }
    
    public function current()
    {
        return current( $this->_items );
    }
    
    public function next()
    {
        next( $this->_items );
        
        $this->_iterator++;
    }
    
    public function key()
    {
        return key( $this->_items );
    }
    
    public function valid()
    {
        return $this->_iterator < count( $this->_items );
    }
    
    public function rewind()
    {
        reset( $this->_items );
        
        $this->_iterator = 0;
    }
    
    public function offsetGet( $offset )
    {
        return $this->$offset;
    }
    
    public function offsetSet( $offset, $value )
    {
        $this->$offset = $value;
    }
    
    public function offsetExists( $offset )
    {
        return isset( $this->$offset );
    }
    
    public function offsetUnset( $offset )
    {
        unset( $this->$offset );
    }
}
