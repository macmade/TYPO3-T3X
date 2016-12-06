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

# $Id: class.tx_netanalytics_tracker.php 38 2010-06-14 12:26:50Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Google Analytics tracker code
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  netanalytics
 */
class tx_netanalytics_tracker
{
    /**
     * The instance of tslib_fe
     */
    protected $_tsfe = NULL;
    
    /**
     * The TS configuration array
     */
    protected $_conf = array();
    
    /**
     * The current page row
     */
    protected $_page = array();
    
    /**
     * Adds the Google Analytics tracker code
     * 
     * @param   array   The parameters passed to the hook (tslib/class.tslib_fe.php - contentPostProc-output)
     * @return  NULL
     */
    public function addTrackerCode( array $params )
    {
        $this->_tsfe = $GLOBALS[ 'TSFE' ];
        $this->_conf = $this->_tsfe->tmpl->setup[ 'plugin.' ][ 'tx_netanalytics_pi1.' ];
        $this->_page = $this->_tsfe->page;
        
        $trackerCode = <<<HTML
<script type="text/javascript">
    /* <![CDATA[ */
    var _gaq = _gaq || [];
    
    _gaq.push( [ '_setAccount', '%s' ] );
    _gaq.push( [ '_trackPageview' ] );
    
    (
        function()
        {
            var ga   = document.createElement( 'script' );
            ga.type  = 'text/javascript';
            ga.async = true;
            ga.src   = ( ( document.location.protocol == 'https' ) ? 'https://ssl' : 'http://www' ) + '.google-analytics.com/ga.js';
            var s    = document.getElementsByTagName( 'script' )[ 0 ];
            
            s.parentNode.insertBefore( ga, s );
        }
    )();
    /* ]]> */
</script>
HTML;
        
        $params[ 'footerData' ][] = sprintf( $trackerCode, $this->_conf[ 'trackerId' ] );
    }
}

// Crappy X-Class declaration, to avoid that stupid warning with the TYPO3 extension manager...
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/netanalytics/Classes/class.tx_netanalytics_tracker.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/netanalytics/Classes/class.tx_netanalytics_tracker.php']);
}
