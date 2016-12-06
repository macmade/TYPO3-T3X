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

/* $Id: List.class.php 2 2010-06-21 07:43:04Z macmade $ */

class Property_List implements Countable, Iterator, ArrayAccess
{
    protected $_dict             = NULL;
    protected $_version          = '1.0';
    protected $_dtdRegistration  = '-//Apple Computer//DTD PLIST 1.0//EN';
    protected $_dtdRUrl          = 'http://www.apple.com/DTDs/PropertyList-1.0.dtd';
    
    public function __construct()
    {
        $this->_dict = new Property_List_Dictionary();
    }
    
    public function __toString()
    {
        $plist = '<?xml version="1.0" encoding="UTF-8"?>' . chr( 10 )
               . '<!DOCTYPE plist PUBLIC "' . $this->_dtdRegistration . '" "' . $this->_dtdRUrl . '">' . chr( 10 )
               . '<plist version="' . $this->_version . '">' . chr( 10 )
               . ( string )$this->_dict . chr( 10 )
               . '</plist>';
        
        return $plist;
    }
    
    public function __get( $property )
    {
        return $this->_dict->$property;
    }
    
    public function __set( $property, $value )
    {
        $this->_dict->$property = $value;
    }
    
    public function __isset( $property )
    {
        return isset( $this->_dict->$property );
    }
    
    public function __unset( $property )
    {
        unset( $this->_dict->$property );
    }
    
    public function count()
    {
        return count( $this->_dict );
    }
    
    public function current()
    {
        return $this->_dict->current();
    }
    
    public function next()
    {
        $this->_dict->next();
    }
    
    public function key()
    {
        return $this->_dict->key();
    }
    
    public function valid()
    {
        return $this->_dict->valid();
    }
    
    public function rewind()
    {
        $this->_dict->rewind();
    }
    
    public function offsetGet( $offset )
    {
        return $this->_dict->$offset;
    }
    
    public function offsetSet( $offset, $value )
    {
        $this->_dict->$offset = $value;
    }
    
    public function offsetExists( $offset )
    {
        return isset( $this->_dict->$offset );
    }
    
    public function offsetUnset( $offset )
    {
        unset( $this->_dict->$offset );
    }
}
