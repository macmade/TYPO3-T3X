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

# $Id: class.tx_marvincatalog_pi2.php 142 2009-12-08 15:58:12Z julien $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Frontend plugin PI2 for the 'marvin_catalog' extension.
 *
 * @author      Julien Dunand <julien@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_pi2 extends tx_oop_Plugin_Base
{
    /**
     * 
     */
    protected function _getPluginContent( tx_oop_Xhtml_Tag $content )
    {
        $swf = t3lib_extMgm::siteRelPath( $this->extKey ) . 'res/swf/marvin.swf';
        
        $height = 560;
        $width = 970;
        
        $html = '
            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab" height="'.$height.'" width="'.$width.'">' . PHP_EOL
         . '    <param name="movie" value="'.$swf.'">' . PHP_EOL
         . '    <param name="quality" value="high">' . PHP_EOL
         . '    <param name="wmode" value="transparent">' . PHP_EOL
         . '    <param name="allowScriptAccess" value="sameDomain">' . PHP_EOL
         . '    <embed src="'.$swf.'" name="promoplayer" 
                       play="true" loop="false" quality="high" 
                       allowscriptaccess="sameDomain" 
                       wmode="transparent"
                       type="application/x-shockwave-flash" 
                       pluginspage="http://www.adobe.com/go/getflashplayer" 
                       height="'.$height.'" width="'.$width.'" align="middle">' . PHP_EOL
          . '</object>' . PHP_EOL
          . '<br /><br />' . PHP_EOL;
        
        $content->addTextData( $html );
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/pi2/class.tx_marvincatalog_pi2.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/pi2/class.tx_marvincatalog_pi2.php']);
}
