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

# $Id: class.tx_netmailing_pi1_wizicon.php 23 2009-12-18 11:09:55Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Frontend plugin wizard icon class.
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  netmailing
 */
class tx_netmailing_pi1_wizicon
{
    /**
     * Processes the wizard items array
     *
     * @param   array   The wizard items
     * @return  array   The modified wizard items array
     */
    public function proc( array $wizardItems )
    {
        global $LANG;
        
        // Gets the locallang values
        $labels = $this->includeLocalLang();
        
        // Wizard item
        $wizardItems[ 'plugins_tx_netmailing_pi1' ] = array(
            
            // Icon
            'icon'        => t3lib_extMgm::extRelPath( 'netmailing' ) . 'res/img/pi1.gif',
            
            // Title
            'title'       => $LANG->getLLL( 'title', $labels ),
            
            // Description
            'description' => $LANG->getLLL( 'description', $labels ),
            
            // Parameters
            'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=netmailing_pi1'
        );
        
        // Return items
        return $wizardItems;
    }
    
    /**
     * Reads the language filel and returns the $LOCAL_LANG array
     * found in that file.
     *
     * @return  array   The array with language labels
     */
    public function includeLocalLang()
    {
        global $LANG;
        
        // Includes the language file
        $labels = $LANG->includeLLFile( 'EXT:netmailing/lang/wiz-pi1.xml', false );
        
        // Returns the language labels
        return $labels;
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/netmailing/pi1/class.tx_netmailing_pi1_wizicon.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/netmailing/pi1/class.tx_netmailing_pi1_wizicon.php']);
}
