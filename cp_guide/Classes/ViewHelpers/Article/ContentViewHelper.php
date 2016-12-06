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

# $Id: ContentViewHelper.php 7 2010-10-18 13:46:27Z jean $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * View helper for the article content
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cp_guide
 */
class Tx_CpGuide_ViewHelpers_Article_ContentViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper
{
    protected function _romanToArabic( $roman )
    {
        $out       = 0;
        $translate = array
        (
            'M' => 1000,
            'D' => 500,
            'C' => 100,
            'L' => 50,
            'X' => 10,
            'V' => 5,
            'I' => 1
        );
        
        $j = strlen( $roman );
        
        for( $i = 0; $i < $j; $i++ )
        {
            $out += $translate[ $roman[ $i ] ];
            
            if( $i < $j - 1 && $translate[ $roman[ $i ] ] < $translate[ $roman[ $i + 1 ] ] )
            {
                $out -= 2 * $translate[ $roman[ $i ] ];
            }
        }
        
        return $out;
    }
    
    protected function _createLinks( $content )
    {
        $sections = array( 'IV', 'V', 'III', 'II', 'I' );
        
        foreach( $sections as $section )
        {
            $pattern = '(' . $section . ')-([0-9]+)';
            $content = preg_replace_callback( '|' . $pattern . '|', array( $this, '_internalLinks' ), $content );
        }
        
        $content = preg_replace_callback( '|([MDCLXVI]+) CO|', array( $this, '_coChapterLinks' ), $content );
        $content = preg_replace_callback( '|(CO )([0-9]+[a-z]?)( et )([0-9]+[a-z]?)|', array( $this, '_coArticleLink' ), $content );
        $content = preg_replace_callback( '|CO ([0-9]+[a-z]?)|', array( $this, '_coArticleLink' ), $content );
        $content = preg_replace_callback( '|([0-9]+[a-z]?) CO|', array( $this, '_coArticleLink' ), $content );
        $content = preg_replace_callback( '|ATF ([0-9]+) ([MDCLXVI]+) ([0-9]+)|', array( $this, '_atfLink' ), $content );
        $content = preg_replace_callback( '|LPGA ([0-9]+)/([0-9]+)|', array( $this, '_lpgaLink' ), $content );
        $content = preg_replace_callback( '|(LECCT )([0-9]+[a-z]?)|', array( $this, '_lecctLink' ), $content );
        $content = preg_replace_callback( '|OSE ([0-9]+[a-z]?)|', array( $this, '_oseLink' ), $content );
        
        $content = str_replace( 'LTr', '<a href="http://www.admin.ch/ch/f/rs/822_11/index.html" title="LTr">LTr</a>', $content );
        $content = str_replace( 'LECCT', '<a href="http://www.admin.ch/ch/f/rs/221_215_311/index.html" title="LECCT">LECCT</a>', $content );
        
        return $content;
    }
    
    protected function _internalLinks( array $matches )
    {
        $section    = $matches[ 1 ];
        $subSection = $matches[ 2 ];
        $link       = new Tx_Fluid_Core_ViewHelper_TagBuilder( 'a', '<!-- internal: section -->' . $section . '-<!-- internal: subsection -->' . $subSection );
        $uriBuilder = $this->controllerContext->getUriBuilder();
        $args       = array( 'section' => $section, 'subSection' => $subSection );
        $uri        = $uriBuilder->reset()->uriFor( 'sectionNumber', $args, 'Guide', 'CpGuide', 'Pi1' );
        
        $link->addAttribute( 'href', $uri );
        
        return $link->render();
    }
    
    protected function _coChapterLinks( array $matches )
    {
        $chapter = $this->_romanToArabic( $matches[ 1 ] );
        $part    = ( $chapter < 6 ) ? 1 : 2;
        $link    = 'http://www.admin.ch/ch/f/rs/220/index2.html#id-' . $part . '-' . $chapter;
        
        return '<a href="' . $link . '" title="' . $matches[ 0 ] . '">' . $matches[ 0 ] . '</a>';
    }
    
    protected function _coArticleLink( array $matches )
    {
        if( count( $matches ) === 5 )
        {
            $article = $matches[ 4 ];
            $link    = 'http://www.admin.ch/ch/f/rs/220/a' . $article . '.html';
            
            return $matches[ 1 ] . $matches[ 2 ] . $matches[ 3 ] . '<a href="' . $link . '" title="' . $matches[ 4 ] . '">' . $matches[ 4 ] . '</a>';
        }
        
        $article = $matches[ 1 ];
        $link    = 'http://www.admin.ch/ch/f/rs/220/a' . $article . '.html';
        
        return '<a href="' . $link . '" title="' . $matches[ 0 ] . '">' . $matches[ 0 ] . '</a>';
    }
    
    protected function _atfLink( array $matches )
    {
        $link = 'http://relevancy.bger.ch/php/clir/http/index.php?lang=fr&type=highlight_simple_query&highlight_docid=atf%3A%2F%2F' . $matches[ 1 ] . '-' . $matches[ 2 ] . '-' . $matches[ 3 ] . '%3Afr';
        
        return '<a href="' . $link . '" title="' . $matches[ 0 ] . '">' . $matches[ 0 ] . '</a>';
    }
    
    protected function _lpgaLink( array $matches )
    {
        $link = 'http://www.admin.ch/ch/f/rs/830_1/a' . $matches[ 1 ] . '.html#' . $matches[ 2 ];
        
        return '<a href="' . $link . '" title="' . $matches[ 0 ] . '">' . $matches[ 0 ] . '</a>';
    }
    
    protected function _lecctLink( array $matches )
    {
        $link = 'http://www.admin.ch/ch/f/rs/221_215_311/a' . $matches[ 2 ] . '.html';
        
        return $matches[ 1 ] . '<a href="' . $link . '" title="' . $matches[ 2 ] . '">' . $matches[ 2 ] . '</a>';
    }
    
    protected function _oseLink( array $matches )
    {
        $link = 'http://www.admin.ch/ch/f/rs/823_111/a' . $matches[ 1 ] . '.html';
        
        return '<a href="' . $link . '" title="' . $matches[ 0 ] . '">' . $matches[ 0 ] . '</a>';
    }
    
    public function initializeArguments()
    {
        $this->registerArgument( 'search', 'string' );
    }
    
    public function render()
    {
        $content = $this->renderChildren();
        $search  = urldecode( $this->arguments[ 'search' ] );
        
        if( $search )
        {
            $words  = explode( ' ', $search );
            
            foreach( $words as $word )
            {
                if( strlen( $word ) > 3 )
                {
                    $content = str_replace( $word, '<span class="tx-cpguide-searchWord">' . $word . '</span>', $content );
                }
            }
        }
        
        return $this->_createLinks( $content );
    }
}

