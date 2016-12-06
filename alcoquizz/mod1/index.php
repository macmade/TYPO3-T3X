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

# $Id: index.php 25 2010-06-21 08:05:58Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

unset( $MCONF );

require('conf.php' );
require( $BACK_PATH . 'init.php' );
require( $BACK_PATH . 'template.php' );

$LANG->includeLLFile( 'EXT:alcoquizz/mod1/locallang.xml' );

require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'lib/php-excel/php-excel.class.php' );

$BE_USER->modAccess( $MCONF, 1 );

class tx_alcoquizz_module1 extends t3lib_SCbase
{
    public $pageinfo;
    
    public function init()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
        
        parent::init();
    }
    
    public function main()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

        $this->pageinfo = t3lib_BEfunc::readPageAccess( $this->id, $this->perms_clause );
        $access         = is_array( $this->pageinfo ) ? 1 : 0;
        
        if( ( $this->id && $access ) || ( $BE_USER->user[ 'admin' ] && !$this->id ) )
        {
            $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath = $BACK_PATH;
            $this->doc->form     = '<form action="" method="POST">';
            
            $this->doc->JScode = <<<JS
<script type="text/javascript">
    // <![CDATA[
    
    script_ended = 0;
    
    function jumpToUrl( URL )
    {
        document.location = URL;
    }
    
    // ]]>
</script>
JS;
            
            $this->doc->postCode = <<<JS
<script type="text/javascript">
    // <![CDATA[
    
    script_ended = 1;
    
    if( top.fsMod )
    {
        top.fsMod.recentIds[ 'web' ] = $this->id;
    }
    
    // ]]>
</script>
JS;
            
            $headerSection = $this->doc->getHeader
            (
                'pages',
                $this->pageinfo,
                $this->pageinfo[ '_thePath' ]
            )
            . '<br>'
            . $LANG->sL( 'LLL:EXT:lang/locallang_core.php:labels.path' )
            . ': '
            . t3lib_div::fixed_lgd_pre( $this->pageinfo[ '_thePath' ], 50 );
            
            $this->content .= $this->doc->startPage( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->header( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->spacer( 5 );
            $this->content .= $this->doc->section( '', $this->doc->funcMenu( $headerSection, t3lib_BEfunc::getFuncMenu( $this->id, 'SET[function]', $this->MOD_SETTINGS[ 'function' ], $this->MOD_MENU[ 'function' ] ) ) );
            $this->content .= $this->doc->divider( 5 );
            
            $data = t3lib_div::_GP( __CLASS__ );
            
            if( is_array( $data ) && isset( $data[ 'export' ] ) )
            {
                switch( ( int )$data[ 'export' ] )
                {
                    case 2:
                        
                        $rows = $this->_exportSurvey();
                        break;
                        
                    default:
                        
                        $rows = $this->_exportQuizz();
                        break;
                }
                
                if( count( $rows ) )
                {
                    $this->_exportCsv( $rows );
                    exit();
                }
                else
                {
                    $this->content .= $this->doc->spacer( 20 ) . $LANG->getLL( 'noData' );
                }
            }
            
            $this->moduleContent();
            
            if( $BE_USER->mayMakeShortcut() )
            {
                $this->content .= $this->doc->spacer( 20 )
                               .  $this->doc->section( '', $this->doc->makeShortcutIcon( 'id', implode( ',', array_keys( $this->MOD_MENU ) ), $this->MCONF[ 'name' ] ) );
            }
            
            $this->content .= $this->doc->spacer(10);
        }
        else
        {
            $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath = $BACK_PATH;
            
            $this->content .= $this->doc->startPage( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->header( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->spacer( 5 );
            $this->content .= $this->doc->spacer( 10 );
        }
    }
    
    public function moduleContent()
    {
        global $LANG;
        
        $this->content .= $this->doc->spacer( 20 )
                       . '<div>'
                       .  '<a href="' . t3lib_div::linkThisScript( array( __CLASS__ => array( 'export' => 1 ) ) ) . '">'
                       . $LANG->getLL( 'exportQuizz' )
                       . '</a>'
                       . '</div>'
                       . '<div>'
                       .  '<a href="' . t3lib_div::linkThisScript( array( __CLASS__ => array( 'export' => 2 ) ) ) . '">'
                       .  $LANG->getLL( 'exportSurvey' )
                       . '</a>'
                       .  '</div>';
    }
    
    public function getContent()
    {
        $this->content .= $this->doc->endPage();
        
        return $this->content;
    }
    
    protected function _exportSurvey()
    {
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery
        (
            '*',
            'tx_alcoquizz_survey'
        );
        
        $data = array();
        
        if( !$res )
        {
            return $data;
        }
        
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) )
        {
            $data[] = $row;
        }
        
        return $data;
    }
    
    protected function _exportQuizz()
    {
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery
        (
            '*',
            'tx_alcoquizz_result',
            'complete=1'
        );
        
        $data = array();
        
        if( !$res )
        {
            return $data;
        }
        
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) )
        {
            $data[] = $row;
        }
        
        return $data;
    }
    
    protected function _exportCsv( array $data )
    {
        global $LANG;
        
        $fields = array_keys( $data[ 0 ] );
        $labels = array();
        
        foreach( $fields as $fieldName )
        {
            $label = $LANG->getLL( 'label-' . $fieldName );
            
            if( $label )
            {
                $labels[ $fieldName ] = $label;
            }
        }
        
        $xls   = array();
        $xls[] = $labels;
        $xls[] = array();
        
        foreach( $data as $row )
        {
            $fields = array();
            
            foreach( $row as $key => $value )
            {
                if( isset( $labels[ $key ] ) )
                {
                    if( $key === 'crdate' || $key === 'tstamp' )
                    {
                        $fields[] = date( 'd.m.Y / H:i', $value );
                    }
                    else
                    {
                        $fields[] = $value;
                    }
                }
            }
            
            $xls[] = $fields;
        }
        
        $file = new Excel_XML();
        
        $file->addArray( $xls );
        $file->generateXML( 'alcooquizz-' . date( 'd.m.Y-H.i', time() ) );
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/alcoquizz/mod1/index.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/alcoquizz/mod1/index.php']);
}

$SOBE = t3lib_div::makeInstance( 'tx_alcoquizz_module1' );

$SOBE->init();

foreach( $SOBE->include_once as $INC_FILE )
{
    include_once( $INC_FILE );
}

$SOBE->main();

print $SOBE->getContent();
