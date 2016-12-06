<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 netinfluence                                                        #
# All rights reserved                                                          #
#                                                                              #
# This script is part of the TYPO3 project. The TYPO3 project is free          #
# software. You can redistribute it and/or modify it under the terms of the    #
# GNU General Public License as published by the Free Software Foundation,     #
# either version 2 of the License, or (at your option) any later version.      #
#                                                                              #
# The GNU General Public License can be found at:                              #
# http://www.gnu.org/copyleft/gpl.html.                                        #
#                                                                              #
# This script is distributed in the hope that it will be useful, but WITHOUT   #
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or        #
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for    #
# more details.                                                                #
#                                                                              #
# This copyright notice MUST APPEAR in all copies of the script!               #
################################################################################

# $Id: Indexing.php 7 2010-10-18 13:46:27Z jean $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Indexing scheduler task
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_Scheduler_Indexing extends tx_scheduler_Task
{
    protected $_words   = array();
    protected $_pidList = array();
    protected $_allowed = array();
    protected $_time    = 0;
    public    $pid      = 0;
    
    /**
     * 
     */
    protected function _index()
    {
        $GLOBALS[ 'TYPO3_DB' ]->sql_query( 'TRUNCATE TABLE tx_cpguide_domain_model_word' );
        
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery
        (
            '*',
            'tx_cpguide_domain_model_article',
            'pid IN (' . implode( ',', $this->_pidList ) . ') AND NOT deleted AND NOT hidden'
        );
        
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) )
        {
            $article = ( int )$row[ 'uid' ];
            $content = $row[ 'content' ];
            $content = str_replace( '&nbsp;', ' ', $content );
            $content = str_replace( '<', ' <', $content );
            $content = str_replace( '>', '> ', $content );
            $content = html_entity_decode( htmlspecialchars_decode( strip_tags( $content ) ) );
            $content = preg_replace( '/\s+/', ' ', $content );
            $words   = array();
            $lang    = $row[ 'sys_language_uid' ];
            $phrases = preg_split( '/[.;:!?]/', $content );
            
            foreach( $phrases as $phrase )
            {
                $i      = 0;
                $words  = array();
                $phrase = trim( $phrase );
                
                preg_match_all
                (
                    '/\p{L}[\p{L}\p{Mn}\p{Pd}\'\x{2019}]*/u',
                    $phrase,
                    $words
                );
                
                if( !isset( $words[ 0 ] ) || !count( $words[ 0 ] ) )
                {
                    continue;
                }
                
                foreach( $words[ 0 ] as $word )
                {
                    if( isset( $this->_allowed[ $word ] ) )
                    {
                        if( !isset( $this->_words[ '1:' . $word ] ) )
                        {
                            $this->_words[ '1:' . $word ] = array( $article => $article );
                        }
                        else
                        {
                            $this->_words[ '1:' . $word ][ $article ] = $article;
                        }
                    }
                    elseif( strlen( $word ) > 3 )
                    {
                        $word = strtolower( $word );
                        
                        if( !isset( $this->_words[ '1:' . $word ] ) )
                        {
                            $this->_words[ '1:' . $word ] = array( $article => $article );
                        }
                        elseif( !isset( $this->_words[ $word ][ $article ] ) )
                        {
                            $this->_words[ '1:' . $word ][ $article ] = $article;
                        }
                        
                        if( isset( $words[ 0 ][ $i + 1 ] ) && strlen( $words[ 0 ][ $i + 1 ] ) > 2 )
                        {
                            $word = $words[ 0 ][ $i ] . ' ' . $words[ 0 ][ $i + 1 ];
                            $word = strtolower( $word );
                            
                            if( !isset( $this->_words[ '2:' . $word ] ) )
                            {
                                $this->_words[ '2:' . $word ] = array( $article => $article );
                            }
                            elseif( !isset( $this->_words[ $word ][ $article ] ) )
                            {
                                $this->_words[ '2:' . $word ][ $article ] = $article;
                            }
                        }
                        
                        if( isset( $words[ 0 ][ $i + 2 ] ) && strlen( $words[ 0 ][ $i + 2 ] ) > 2 )
                        {
                            $word = $words[ 0 ][ $i ] . ' ' . $words[ 0 ][ $i + 1 ] . ' ' . $words[ 0 ][ $i + 2 ];
                            $word = strtolower( $word );
                            
                            if( !isset( $this->_words[ '3:' . $word ] ) )
                            {
                                $this->_words[ '3:' . $word ] = array( $article => $article );
                            }
                            elseif( !isset( $this->_words[ $word ][ $article ] ) )
                            {
                                $this->_words[ '3:' . $word ][ $article ] = $article;
                            }
                        }
                    }
                    
                    $i++;
                }
            }
        }
        
        foreach( $this->_words as $word => $articles )
        {
            $this->_insertWord( $word, implode( ',', $articles ) );
        }
    }
    
    /**
     * 
     */
    protected function _insertWord( $word, $articles, $lang = '' )
    {
        $count = substr( $word, 0, 1 );
        $word  = substr( $word, 2 );
        
        $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery
        (
            'tx_cpguide_domain_model_word',
            array(
                'pid'        => $this->pid,
                'crdate'     => $this->_time,
                'tstamp'     => $this->_time,
                'word'       => $word,
                'articles'   => $articles,
                'word_count' => $count
            )
        );
    }
    
    /**
     * 
     */
    public function execute()
    {
        $this->_time = time();
        
        if( $this->pid <= 0 )
        {
            return false;
        }
        
        $pages = array();
        $res   = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery
        (
            'uid,deleted,hidden,pid',
            'pages',
            'pid=' . $this->pid . ' AND NOT deleted AND NOT hidden'
        );
        
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) )
        {
            $pidList[] = $row[ 'uid' ];
        }
        
        $res   = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery
        (
            'uid,deleted,hidden,pid',
            'pages',
            'pid IN (' . implode( ',', $pidList ) . ') AND NOT deleted AND NOT hidden'
        );
        
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) )
        {
            $this->_pidList[] = $row[ 'uid' ];
        }
        
        if( !count( $this->_pidList ) )
        {
            return false;
        }
        
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery
        (
            'uid,deleted,hidden,pid,word',
            'tx_cpguide_domain_model_allowed_word',
            'pid=' . $this->pid . ' AND NOT deleted AND NOT hidden'
        );
        
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) )
        {
            $this->_allowed[ $row[ 'word' ] ] = $row[ 'word' ];
        }
        
        $this->_index();
        
        return true;
    }
    
    /**
     * 
     */
    public function getAdditionalInformation()
    {
        return '';
    }
}
