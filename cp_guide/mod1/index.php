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

# $Id: index.php 2 2010-06-21 07:43:04Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

unset( $MCONF );

require('conf.php' );
require( $BACK_PATH . 'init.php' );
require( $BACK_PATH . 'template.php' );

$LANG->includeLLFile( 'EXT:cp_guide/mod1/locallang.xml' );

require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
require_once( t3lib_extMgm::extPath( 'cp_guide' ) . 'lib/plist/init.inc.php' );

$BE_USER->modAccess( $MCONF, 1 );

class tx_cpguide_module1 extends t3lib_SCbase
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
                $this->_export();
                exit();
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
                       . $LANG->getLL( 'export' )
                       . '</a>'
                       . '</div>';
    }
    
    public function getContent()
    {
        $this->content .= $this->doc->endPage();
        
        return $this->content;
    }
    
    protected function _export()
    {
        $categories = t3lib_BEfunc::getRecordsByField
        (
            'pages',
            'pid',
            $this->id,
            '',
            '',
            'sorting'
        );
        
        $plist             = new Property_List();
        $plist->categories = new Property_List_Array();
        
        foreach( $categories as $category )
        {
            $cat = array
            (
                'title' => $category[ 'title' ],
                'sub'   => new Property_List_Array()
            );
            
            $subCategories = t3lib_BEfunc::getRecordsByField
            (
                'pages',
                'pid',
                $category[ 'uid' ],
                '',
                '',
                'sorting'
            );
            
            foreach( $subCategories as $subCategory )
            {
                $subCat = array
                (
                    'title'    => $subCategory[ 'title' ],
                    'articles' => new Property_List_Array()
                );
            
                $articles = t3lib_BEfunc::getRecordsByField
                (
                    'tx_cpguide_domain_model_article',
                    'pid',
                    $subCategory[ 'uid' ],
                    'AND sys_language_uid = 0',
                    '',
                    'sorting'
                );
            
                foreach( $articles as $article )
                {
                    $subCat[ 'articles' ][] = array
                    (
                        'title'   => $article[ 'title' ],
                        'content' => new Property_List_Data( $article[ 'content' ] )
                    );
                }
                
                $cat[ 'sub' ][] = $subCat;
            }
            
            $plist->categories[] = $cat;
        }
        
        header( 'Content-type: application/xml' );
        header( 'Content-disposition: attachment; filename=cp_guide.plist' );
        
        print $plist;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cp_guide/mod1/index.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cp_guide/mod1/index.php']);
}

$SOBE = t3lib_div::makeInstance( 'tx_cpguide_module1' );

$SOBE->init();

foreach( $SOBE->include_once as $INC_FILE )
{
    include_once( $INC_FILE );
}

$SOBE->main();

print $SOBE->getContent();
