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

// Includes the wizard icon base class
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'classes/class.tx_adlercontest_wizardicon.php' );

/** 
 * Class that adds the wizard icon.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */
class tx_adlercontest_pi1_wizicon extends tx_adlercontest_wizardIcon
{
    /**
     * The wizard class file
     */
    protected $_wizardClassFile = __FILE__;
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi1/class.tx_adlercontest_pi1_wizicon.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi1/class.tx_adlercontest_pi1_wizicon.php']);
}
