<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 eosgarden - Jean-David Gadina (macmade@eosgarden.com)               #
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

# $Id$

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Time related utilities
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_Time_Utils
{
    /**
     * Class constructor
     * 
     * The class constructor is private as all methods from this class are
     * static.
     * 
     * @return  NULL
     */
    private function __construct()
    {}
    
    /**
     * Returns an age.
     * 
     * This method returns an age, calculated from a timestamp. By default,
     * the method takes the current time as reference, but another timestamp
     * can be specified. The method also returns by default the age in days,
     * but it can also returns it in seconds, minutes or hours.
     * 
     * @param   int     The base timestamp
     * @param   int     The time from which to calculate the age (timestamp). Will use current time if none supplied
     * @param   string  The type of age to return (s = seconds, i = minutes, h = hours, or d = days). Default is days.
     * @return  int     An age, as a numeric value
     */
    public static function calcAge( $tstamp, $curTime = false, $ageType = false )
    {
        // Process age types
        switch( $ageType ) {
            
            // Seconds
            case 's':
                $division = 1;
                break;
            
            // Minutes
            case 'i':
                $division = 60;
                break;
            
            // Hours
            case 'h':
                $division = 3600;
                break;
            
            // Default - Days
            default:
                $division = 86400;
                break;
        }
        
        // Gets the current time, if none specified
        if( !$currentTime ) {
            
            $currentTime = time();
        }
        
        // Gets the differences between the two timestamps
        $diff = $currentTime - $tstamp;
        
        // Returns the age
        return ceil( $diff / $division );
    }
    
    /**
     * Converts a week number to a timestamp.
     * 
     * This method returns a timestamp for a given year (XXXX), week number, and
     * day number (0 is sunday, 6 is saturday).
     * 
     * Thanx to Nicolas Miroz for the informations about date computing.
     * 
     * @param   int The day number
     * @param   int The week number
     * @param   int The year
     * @return  int A timestamp
     */
    public static function weekToDate( $day, $week, $year )
    {
        // First january of the year
        $firstDay = mktime( 0, 0, 0, 1, 1, $year );
        
        // Gets the day for the first january
        $firstDayNum = date( 'w', $firstDay );
        
        // Computes the first monday of the year and the week number for that day
        switch( $firstDayNum ) {
        
            // Sunday
            case 0:
                
                // Monday is 02.01 | Week is 1
                $monday = mktime( 0, 0, 0, 01, 02, $year );
                $weekNum = 1;
                break;
            
            // Monday
            case 1:
                
                // Monday is 01.01 | Week is 1
                $monday = mktime( 0, 0, 0, 01, 01, $year );
                $weekNum = 1;
                break;
            
            // Tuesday
            case 2:
                
                // Monday is 07.01 | Week is 2
                $monday = mktime( 0, 0, 0, 01, 07, $year );
                $weekNum = 2;
                break;
            
            // Wednesday
            case 3:
                
                // Monday is 06.01 | Week is 2
                $monday = mktime( 0, 0, 0, 01, 06, $year );
                $weekNum = 2;
                break;
            
            // Thursday
            case 4:
                
                // Monday is 05.01 | Week is 2
                $monday = mktime( 0, 0, 0, 01, 05, $year );
                $weekNum = 2;
                break;
            
            // Friday
            case 5:
                
                // Monday is 04.01 | Week is 1
                $monday = mktime( 0, 0, 0, 01, 04, $year );
                $weekNum = 1;
                break;
            
            // Saturday
            case 6:
                
                // Monday is 03.01 | Week is 1
                $monday = mktime( 0, 0, 0, 01, 03, $year );
                $weekNum = 1;
                break;
        }
        
        // Computes the difference in days from the monday to the requested day
        $dayDiff = ( $day == 0 ) ? 6 : ( $day - 1 );
        
        // Number of day to the requested date
        $numDay = ( ( $week - ( $weekNum - 1 ) - 1 ) * 7 ) + $dayDiff + date( 'd', $monday );
        
        // Creates and returns the timestamp for the requested date
        return mktime( 0, 0, 0, 01, $numDay, $year );
    }
}
