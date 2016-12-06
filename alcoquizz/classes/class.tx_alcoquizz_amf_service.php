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

# $Id: class.tx_alcoquizz_amf_service.php 25 2010-06-21 08:05:58Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * AMF service for the AlcooQuizz Flex application
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  alcoquizz
 */
class tx_alcoquizz_Amf_Service
{
    /**
     * Storage for the received data
     */
    protected $_data = array();
    
    /**
     * ESS tables data
     */
    protected $_ess  = array
    (
        /**
         * Drinks per week
         */
        array
        (
            /* Women */
            array
            (
                /* 16-17 */
                array( 45.0, 15.6, 24.8, 10.0, 2.4, 0.4, 1.8 ),
                /* 18-19 */
                array( 41.6, 13.1, 35.5, 5.2, 2.5, 0.0, 2.1 ),
                /* 20-24 */
                array( 37.5, 20.5, 30.5, 6.6, 2.2, 0.7, 2.0 ),
                /* 25-29 */
                array( 40.1, 21.6, 27.3, 7.9, 1.9, 0.7, 0.5 ),
                /* 30-34 */
                array( 46.6, 22.0, 21.8, 6.1, 2.2, 0.4, 0.9 ),
                /* 35-44 */
                array( 42.1, 26.3, 21.2, 7.4, 1.7, 0.4, 0.9 ),
                /* 45-54 */
                array( 38.1, 25.1, 21.3, 11.8, 2.6, 0.4, 0.6 ),
                /* 55-64 */
                array( 37.2, 23.7, 19.8, 15.2, 3.3, 0.2, 0.6 ),
                /* 65+ */
                array( 48.5, 19.5, 12.1, 14.9, 4.1, 0.3, 0.6 )
            ),
            /* Men */
            array
            (
                /* 16-17 */
                array( 39.3, 10.7, 20.7, 15.3, 4.3, 4.4, 5.3 ),
                /* 18-19 */
                array( 20.6, 8.4, 26.6, 23.7, 11.3, 1.1, 8.3 ),
                /* 20-24 */
                array( 19.4, 8.8, 28.1, 24.3, 10.7, 3.5, 5.3 ),
                /* 25-29 */
                array( 22.6, 8.0, 32.1, 18.6, 10.0, 3.3, 5.4 ),
                /* 30-34 */
                array( 19.7, 10.8, 40.3, 17.4, 6.1, 2.9, 2.8 ),
                /* 35-44 */
                array( 18.3, 11.9, 35.1, 21.9, 6.3, 2.2, 4.4 ),
                /* 45-54 */
                array( 19.3, 11.3, 30.6, 21.5, 9.6, 3.1, 4.6 ),
                /* 55-64 */
                array( 17.8, 9.6, 27.1, 23.3, 10.4, 4.4, 7.4 ),
                /* 65+ */
                array( 16.9, 9.3, 23.0, 27.8, 13.0, 3.6, 6.3 )
            )
        ),
        
        /**
         * Binge 4/5 drinks
         */
        array
        (
            /* Women */
            array
            (
                array( 82.7, 8.6, 7.0, 1.7, 0.0 ),
                array( 70.6, 17.0, 10.9, 1.4, 0.0 ),
                array( 67.6, 17.5, 10.6, 4.3, 0.0 ),
                array( 69.5, 19.2, 8.3, 3.0, 0.0 ),
                array( 75.8, 16.3, 6.6, 1.3, 0.0 ),
                array( 79.1, 15.3, 4.1, 1.2, 0.3 ),
                array( 75.2, 18.2, 5.4, 1.0, 0.2 ),
                array( 84.5, 11.9, 2.7, 0.8, 0.1 ),
                array( 94.2, 4.4, 1.0, 0.2, 0.1 )
            ),
            /* Men */
            array
            (
                array( 62.1, 15.1, 15.4, 7.3, 0.0 ),
                array( 40.9, 23.1, 23.5, 11.9, 0.5 ),
                array( 45.6, 20.1, 23.0, 11.1, 0.3 ),
                array( 47.4, 25.6, 21.7, 4.9, 0.4 ),
                array( 51.7, 30.6, 13.1, 4.6, 0.0 ),
                array( 57.1, 30.7, 10.1, 1.8, 0.2 ),
                array( 61.5, 27.6, 7.7, 3.1, 0.2 ),
                array( 63.7, 23.2, 9.0, 3.4, 0.8 ),
                array( 80.8, 13.3, 4.0, 1.1, 0.7 )
            )
        )
    );
    
    /**
     * ESS ages columns
     */
    protected $_essAges   = array
    (
        array( 16, 17 ),
        array( 18, 19 ),
        array( 20, 24 ),
        array( 25, 29 ),
        array( 30, 34 ),
        array( 35, 44 ),
        array( 45, 54 ),
        array( 55, 64 ),
        array( 65 )
    );
    
    /**
     * ESS rows (drinks per week)
     */
    protected $_essDrinks = array
    (
        'Moins d\'un verre par semaine',
        '1 - <3',
        '3 - <8',
        '8 - <15',
        '15 - <22',
        '22 - <28',
        '28+'
    );
    
    
    /**
     * ESS rows (binge 4/5 drinks)
     */
    protected $_essDrinksAlt = array
    (
        'Jamais',
        'Moins d\'une fois par mois',
        'Chaque mois',
        'Chaque semaine',
        'Tous les jours ou presque'
    );
    
    /**
     * Computes the AUDIT score
     * 
     * @return  int     The AUDIT score
     */
    protected function _getAudit()
    {
        // Units per week (question II.1)
        $units = ( int )$this->_data[ 'II1' ];
        
        // Score for question II.5.2 (not on the form)
        if( $units < 3 )
        {
            $II52 = 0;
        }
        elseif( $units < 5 )
        {
            $II52 = 1;
        }
        elseif( $units < 7 )
        {
            $II52 = 2;
        }
        elseif( $units < 10 )
        {
            $II52 = 3;
        }
        else
        {
            $II52 = 4;
        }
        
        // Score for question II.5.1 (not on the form)
        // In the flex form:
        //      
        //      - Never                 - 1
        //      - One day per month     - 2
        //      - 2-3 days per month    - 3
        //      - One day per week      - 4
        //      - Two days per week     - 5
        //      - Three days per week   - 6
        //      - Four days per week    - 7
        //      - Five days per week    - 8
        //      - Six days per week     - 9
        //      - Seven days per week   - 10
        if( $this->_data[ 'II2' ] == 1 )
        {
            $II51 = 0;
        }
        elseif( $this->_data[ 'II2' ] == 2 )
        {
            $II51 = 1;
        }
        elseif( $this->_data[ 'II2' ] == 3 )
        {
            $II51 = 2;
        }
        elseif( $this->_data[ 'II2' ] == 4 )
        {
            $II51 = 2;
        }
        elseif( $this->_data[ 'II2' ] == 5 || $this->_data[ 'II2' ] == 6 )
        {
            $II51 = 3;
        }
        else
        {
            $II51 = 4;
        }
        
        // Data for the form questions (II.5.3 - II.5.10)
        $data = array
        (
            $this->_data[ 'II53' ],
            $this->_data[ 'II54' ],
            $this->_data[ 'II55' ],
            $this->_data[ 'II56' ],
            $this->_data[ 'II57' ],
            $this->_data[ 'II58' ],
            $this->_data[ 'II59' ],
            $this->_data[ 'II510' ]
        );
        
        // Process the data for the form questions (II.5.3 - II.5.10)
        //      
        //      A - Never
        //      B - Less than one time per month
        //      C - One time per month
        //      D - One time per week
        //      E - Each day
        foreach( $data as $key => $value )
        {
            if( $value == 'A' )
            {
                $data[ $key ] = 0;
            }
            elseif( $value == 'B' )
            {
                $data[ $key ] = 1;
            }
            elseif( $value == 'C' )
            {
                $data[ $key ] = 2;
            }
            elseif( $value == 'D' )
            {
                $data[ $key ] = 3;
            }
            elseif( $value == 'E' )
            {
                $data[ $key ] = 4;
            }
            else
            {
                $data[ $key ] = 0;
            }
        }
        
        // Adds all the scores
        $audit = $II51 + $II52 + $data[ 0 ] + $data[ 1 ] + $data[ 2 ] + $data[ 3 ] + $data[ 4 ] + $data[ 5 ] + $data[ 6 ] + $data[ 7 ];
        
        // Returns the final AUDIT score
        return $audit;
    }
    
    /**
     * Gets the risk
     * 
     * @return  int     0 for no risk, 1 for episodic risk, 2 for monthly risk, 3 for both risks
     */
    protected function _getRisk()
    {
        // Whether we have a man or a woman (question I.1)
        $isMan = ( $this->_data[ 'I1' ] == 1 );
        
        // Person's age (question I.2)
        $age   = ( int )$this->_data[ 'I2' ];
        
        // Unites per week (question II.1)
        $units = $this->_getConsumption();
        
        // Init
        $risk  = 0;
        
        // Checks the age and sex
        if( $isMan && $age < 65 && $units >= 15 )
        {
            // Monthly risk
            $risk += 2;
        }
        elseif( $units >= 8 )
        {
            // Monthly risk
            $risk += 2;
        }
        
        // Checks question II.3 and II.5.3
        if( $this->_data[ 'II3' ] && $this->_data[ 'II3' ] != 'Jamais' )
        {
            // Episodic risk
            $risk += 1;
        }
        elseif( $this->_data[ 'II53' ] && $this->_data[ 'II53' ] != 'A' )
        {
            // Episodic risk
            $risk += 1;
        }
        
        // Returns the total risk
        return $risk;
    }
    
    /**
     * Gets the category
     * 
     * @return  int     0 for green, 1 for orange, 2 for red
     */
    protected function _getCategory()
    {
        // Gets the risk
        $risk  = $this->_getRisk();
        
        // Gets the AUDIT score
        $audit = $this->_getAudit();
        
        // Checks the value
        if( $risk == 0 && $audit < 8 )
        {
            // No risk and low AUDIT score - Green
            $category = 0;
        }
        elseif( $risk != 0 && $audit < 13 )
        {
            // Risk and low AUDIT score - Orange
            $category = 1;
        }
        else
        {
            // Risk and high AUDIT score - Red
            $category = 2;
        }
        
        // Returns the category
        return $category;
    }
    
    /**
     * Gets the blood alcohol content
     * 
     * @return  float   The BAC value
     */
    protected function _getAlcohol()
    {
        // Whether we have a man or a woman (question I.1)
        $isMan  = ( $this->_data[ 'I1' ] == 1 );
        
        // Person weight (question I.3)
        $weight = ( int )$this->_data[ 'I3' ];
        
        // Parts of the II.4 questions
        $infos  = explode( '--', $this->_data[ 'II4' ] );
        
        // Max number of units
        $units  = ( int )array_shift( $infos );
        
        // Number of hours for the units
        $hours  = ( int )array_pop( $infos );
        
        // Checks the sex
        if( $isMan )
        {
            // Man formula
            $alcohol = ( ( $units * 10 ) / ( $weight * 0.7 ) ) - ( 0.16 * $hours );
        }
        else
        {
            // Woman formula
            $alcohol = ( ( $units * 10 ) / ( $weight * 0.6 ) ) - ( 0.16 * $hours );
        }
        
        // Maximum value is 4
        if( $alcohol > 4 )
        {
            $alcohol = 4;
        }
        
        // Minimum value is 0
        if( $alcohol < 0 )
        {
            $alcohol = 0;
        }
        
        // Returns the BAC
        return $alcohol;
    }
    
    /**
     * Gets the alcohol risk percentage (lethal accident)
     * 
     * @param   float   The blood alcohol content
     * @return  flaot   The risk percentage
     */
    protected function _getAlcoholRisk( $rate )
    {
        // Age of the person
        $age  = ( int )$this->_data[ 'I2' ];
        
        // Init
        $risk = 0;
        
        // Checks the age
        if( $age < 35 )
        {
            if( $rate >= 0.2 && $rate < 0.5 )
            {
                $risk = 2.44;
            }
            elseif( $rate >= 0.5 && $rate < 0.8 )
            {
                $risk = 5.26;
            }
            elseif( $rate >= 0.8 && $rate < 1 )
            {
                $risk = 9.95;
            }
            elseif( $rate >= 1 && $rate < 1.5 )
            {
                $risk = 24.31;
            }
            elseif( $rate >= 1.5 )
            {
                $risk = 174.13;
            }
        }
        else
        {
            if( $rate >= 0.2 && $rate < 0.5 )
            {
                $risk = 2.26;
            }
            elseif( $rate >= 0.5 && $rate < 0.8 )
            {
                $risk = 4.56;
            }
            elseif( $rate >= 0.8 && $rate < 1 )
            {
                $risk = 8.18;
            }
            elseif( $rate >= 1 && $rate < 1.5 )
            {
                $risk = 18.51;
            }
            elseif( $rate >= 1.5 )
            {
                $risk = 274.87;
            }
        }
        
        // Returns the alcohol risk percentage
        return $risk;
    }
    
    /**
     * Gets the consumption
     * 
     * @return  int     The user's consumption
     */
    protected function _getConsumption()
    {
        // Data for the questions II.1 and II.2
        $units = ( int )$this->_data[ 'II1' ];
        $freq  = ( int )$this->_data[ 'II2' ];
        
        // Checks the frequency
        if( $freq < 4 )
        {
            // Less than once a week - Returns the number of units
            return $units;
        }
        
        // More than once a week - Returns the number of units multiplied by the number of days
        //      
        //      1 day per week  - 4
        //      2 day per week  - 5
        //      3 day per week  - 6
        //      4 day per week  - 7
        //      5 day per week  - 8
        //      6 day per week  - 9
        //      7 day per week  - 10
        return $units * ( $freq - 3 );
    }
    
    /**
     * Gets the number of hamburgers
     * 
     * @param   int     The user's consumption
     * @return  int     The number of hamburgers
     */
    protected function _getHamburgers( $consumption )
    {
        // Units per day (based on the weekly consumption)
        $units         = $consumption / 7;
        
        // Units per month
        $unitsPerMonth = $units * 30;
        
        // Number of calories
        $kcal          = ( $unitsPerMonth * 3 ) * 100;
        
        // A hamburger is 250 calories
        return $kcal / 250;
    }
    
    /**
     * Gets the number of chocolate bars
     * 
     * @param   int     The user's consumption
     * @return  int     The number of chocolate bars
     */
    protected function _getChocolates( $consumption )
    {
        // Units per day (based on the weekly consumption)
        $units         = $consumption / 7;
        
        // Units per month
        $unitsPerMonth = $units * 30;
        
        // Number of calories
        $kcal          = ( $unitsPerMonth * 3 ) * 100;
        
        // A chocolate bar is 180 calories
        return $kcal / 180;
    }
    
    /**
     * Gets the number of calories
     * 
     * @param   int     The user's consumption
     * @return  int     The number of calories
     */
    protected function _getKCal( $consumption )
    {
        // Units per day (based on the weekly consumption)
        $units         = $consumption / 7;
        
        // Units per month
        $unitsPerMonth = $units * 30;
        
        // Number of calories
        $kcal          = ( $unitsPerMonth * 3 ) * 100;
        
        // Return the number of calories
        return $kcal;
    }
    
    /**
     * Gets the key in the ESS for the gender
     * 
     * @return  int     The key corresponding to the correct gender in the ESS array
     */
    protected function _getEssGenderKey()
    {
        // 0 for woman, 1 for man
        return ( int )$this->_data[ 'I1' ];
    }
    
    /**
     * Gets the key in the ESS for the age
     * 
     * @return  int     The key corresponding to the correct age in the ESS array
     */
    protected function _getEssAgeKey()
    {
        // Person's age (question II.2)
        $age = ( int )$this->_data[ 'I2' ];
        
        // Checks the age, and returns the correct key in the ESS array
        if( $age < 18 )
        {
            return 0;
        }
        elseif( $age < 20 )
        {
            return 1;
        }
        elseif( $age < 25 )
        {
            return 2;
        }
        elseif( $age < 30 )
        {
            return 3;
        }
        elseif( $age < 35 )
        {
            return 4;
        }
        elseif( $age < 45 )
        {
            return 5;
        }
        elseif( $age < 55 )
        {
            return 6;
        }
        elseif( $age < 65 )
        {
            return 7;
        }
        else
        {
            return 8;
        }
    }
    
    /**
     * Gets the key in the ESS for the drinks per week
     * 
     * @param   int     The number of drinks
     * @return  int     The key corresponding to the drinks per week in the ESS array
     */
    protected function _getEssDrinksKey( $drinks )
    {
        if( $drinks < 1 )
        {
            return 0;
        }
        elseif( $drinks < 3 )
        {
            return 1;
        }
        elseif( $drinks < 8 )
        {
            return 2;
        }
        elseif( $drinks < 15 )
        {
            return 3;
        }
        elseif( $drinks < 22 )
        {
            return 4;
        }
        elseif( $drinks < 28 )
        {
            return 5;
        }
        else
        {
            return 6;
        }
    }
    
    /**
     * Gets the key in the ESS for the binge 4/5 drinks
     * 
     * @param   int     The number of times
     * @return  int     The key corresponding to the binge 4/5 drinks in the ESS array
     */
    protected function _getEssDrinksAltKey( $times )
    {
        return array_search( $times, $this->_essDrinksAlt );
    }
    
    /**
     * Gets the consumption data (drinks per week) - All data for the correct
     * gender and age
     * 
     * @return  array   The consumption data
     */
    protected function _getConsumptionData()
    {
        // Init
        $data = array();
        $i    = 0;
        
        // Gets the necessary keys in the ESS table
        $key1 = $this->_getEssGenderKey();
        $key2 = $this->_getEssAgeKey();
        
        // Process each value in the ESS table matching the correct gender and age
        foreach( $this->_ess[ 0 ][ $key1 ][ $key2 ] as $value )
        {
            // Storage
            $object        = new stdClass();
            
            // Stores the values
            $object->key   = $this->_essDrinks[ $i ];
            $object->value = $value;
            
            // Adds the object
            $data[] = $object;
            
            $i++;
        }
        
        // Returns the data
        return $data;
    }
    
    /**
     * Gets the consumption data (binge 4/5 drinks) - All data for the correct
     * gender and age
     * 
     * @return  array   The consumption data
     */
    protected function _getConsumptionAltData()
    {
        // Init
        $data = array();
        $i    = 0;
        
        // Gets the necessary keys in the ESS table
        $key1 = $this->_getEssGenderKey();
        $key2 = $this->_getEssAgeKey();
        
        // Process each value in the ESS table matching the correct gender and age
        foreach( $this->_ess[ 1 ][ $key1 ][ $key2 ] as $value )
        {
            // Storage
            $object        = new stdClass();
            
            // Stores the values
            $object->key   = $this->_essDrinksAlt[ $i ];
            $object->value = $value;
            
            // Adds the object
            $data[] = $object;
            
            $i++;
        }
        
        // Returns the data
        return $data;
    }
    
    /**
     * Gets the key for the user's comsumption (drinks per week) in the
     * consumption data
     * 
     * @return  int     The array key
     */
    protected function _getConsumptionRate( $drinks )
    {
        return $this->_getEssDrinksKey( $drinks );
    }
    
    
    /**
     * Gets the key for the user's comsumption (binge 4/5 drinks) in the
     * consumption data
     * 
     * @return  int     The array key
     */
    protected function _getConsumptionAltRate( $times )
    {
        return $this->_getEssDrinksAltKey( $times );
    }
    
    /**
     * Gets the impact
     * 
     * @return  array   An array with the question numbers (III11 - III112, III21, III22)
     *                  to which the user answered something else than 'never'.
     */
    protected function _getImpact()
    {
        $impact = array();
        
        if( $this->_data[ 'III11' ] && $this->_data[ 'III11' ] != 'A' )
        {
            $impact[] = 'III11';
        }
        
        if( $this->_data[ 'III12' ] && $this->_data[ 'III12' ] != 'A' )
        {
            $impact[] = 'III12';
        }
        
        if( $this->_data[ 'III13' ] && $this->_data[ 'III13' ] != 'A' )
        {
            $impact[] = 'III13';
        }
        
        if( $this->_data[ 'III16' ] && $this->_data[ 'III16' ] != 'A' )
        {
            $impact[] = 'III16';
        }
        
        if( $this->_data[ 'III17' ] && $this->_data[ 'III17' ] != 'A' )
        {
            $impact[] = 'III17';
        }
        
        if( $this->_data[ 'III18' ] && $this->_data[ 'III18' ] != 'A' )
        {
            $impact[] = 'III18';
        }
        
        if( $this->_data[ 'III19' ] && $this->_data[ 'III19' ] != 'A' )
        {
            $impact[] = 'III19';
        }
        
        if( $this->_data[ 'III110' ] && $this->_data[ 'III110' ] != 'A' )
        {
            $impact[] = 'III110';
        }
        
        if( $this->_data[ 'III111' ] && $this->_data[ 'III111' ] != 'A' )
        {
            $impact[] = 'III111';
        }
        
        if( $this->_data[ 'III112' ] && $this->_data[ 'III112' ] != 'A' )
        {
            $impact[] = 'III112';
        }
        
        if( $this->_data[ 'III21' ] && $this->_data[ 'III21' ] != 'A' )
        {
            $impact[] = 'III21';
        }
        
        if( $this->_data[ 'III22' ] && $this->_data[ 'III22' ] != 'A' )
        {
            $impact[] = 'III22';
        }
        
        return $impact;
    }
    
    /**
     * Gets the computed data for a specific result set
     * 
     * @param   stdClass    $params
     * @return  array
     */
    public function getData( $params )
    {
        // Result ID
        $id  = ( int )$params->id;
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery
        (
            '*',
            'tx_alcoquizz_result',
            'uid=' . ( int )$id
        );
        
        // Storage
        $data = array();
        
        // Gets the result row
        if( $res && $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) )
        {
            // Stores the user data
            $this->_data                    = $row;
            
            // Distinct values for II.4
            $last                           =  explode( '--', $this->_data[ 'II4' ] );
            
            // Fills the computed values
            $data[ 'category' ]             = $this->_getCategory();
            $data[ 'risk' ]                 = $this->_getRisk();
            $data[ 'audit' ]                = $this->_getAudit();
            $data[ 'alcohol' ]              = $this->_getAlcohol();
            $data[ 'alcohol_risk' ]         = $this->_getAlcoholRisk( $data[ 'alcohol' ] );
            $data[ 'impact' ]               = $this->_getImpact();
            $data[ 'consumption' ]          = $this->_getConsumption();
            $data[ 'consumption_rate' ]     = $this->_getConsumptionRate( $data[ 'consumption' ] );
            $data[ 'consumption_data' ]     = $this->_getConsumptionData();
            $data[ 'consumption_alt_rate' ] = $this->_getConsumptionAltRate( $this->_data[ 'II3' ] );
            $data[ 'consumption_alt_data' ] = $this->_getConsumptionAltData();
            $data[ 'consumption_age' ]      = $this->_essAges[ $this->_getEssAgeKey() ];
            $data[ 'last_drinks' ]          = array_shift( $last );
            $data[ 'last_hours' ]           = array_pop( $last );
            $data[ 'hamburgers' ]           = $this->_getHamburgers( $data[ 'consumption' ] );
            $data[ 'chocolates' ]           = $this->_getChocolates( $data[ 'consumption' ] );
            $data[ 'kcal' ]                 = $this->_getKCal( $data[ 'consumption' ] );
        }
        
        // Returns the data to the flex application
        return $data;
    }
    
    /**
     * Stores the data from the flex application in the TYPO3 database
     * 
     * @param   stdClass    $data
     * @return  int
     */
    public function setData( $data )
    {
        // Checks for valid data
        if( !is_object( $data ) || !isset( $data->externalizedData ) || !is_array( $data->externalizedData ) )
        {
            return 0;
        }
        
        // Initialization
        $step   = 0;
        $id     = 0;
        $fields = array();
        
        // Process each received data
        foreach( $data->externalizedData as $field )
        {
            // Checks for a valid field
            if( !is_object( $field ) || !isset( $field->id ) || !isset( $field->value ) )
            {
                continue;
            }
            
            // Checks the field name
            if( $field->id == 'step' )
            {
                // Current step number
                $step = ( int )$field->value;
            }
            elseif( $field->id == 'id' )
            {
                // Current result ID
                $id = ( int )$field->value;
            }
            else
            {
                // Data field - Stores it
                $fields[ $field->id ] = $field->value;
            }
        }
        
        // Checks for the last step
        if( $step === 3 )
        {
            // Sets the complete flag
            $fields[ 'complete' ] = 1;
        }
        
        // Checks if we already have an ID
        if( $id > 0 )
        {
            // Modification time
            $fields[ 'tstamp' ] = time();
            
            // Updates the data
            $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( 'tx_alcoquizz_result', 'uid=' . $id, $fields );
            
            return $id;
        }
        
        // Creation and modification time
        $fields[ 'crdate' ] = time();
        $fields[ 'tstamp' ] = $fields[ 'crdate' ];
        
        // Inserts a new record
        $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery( 'tx_alcoquizz_result', $fields );
        
        return $GLOBALS[ 'TYPO3_DB' ]->sql_insert_id();
    }
    
    /**
     * Stores the survey data from the flex application in the TYPO3 database
     * 
     * @param   stdClass    $data
     * @return  int
     */
    public function setSurveyData( $data )
    {
        // Checks for valid data
        if( !is_object( $data ) || !isset( $data->externalizedData ) || !is_array( $data->externalizedData ) )
        {
            return 0;
        }
        
        $id     = 0;
        $fields = array();
        
        // Process each received data
        foreach( $data->externalizedData as $field )
        {
            // Checks for a valid field
            if( !is_object( $field ) || !isset( $field->id ) || !isset( $field->value ) )
            {
                continue;
            }
            
            // Checks the field name
            if( $field->id == 'id' )
            {
                // Current result ID
                $fields[ 'id_result' ] = $field->value;
            }
            else
            {
                // Data field - Stores it
                $fields[ $field->id ] = $field->value;
            }
        }
        
        // Creation and modification time
        $fields[ 'crdate' ] = time();
        $fields[ 'tstamp' ] = $fields[ 'crdate' ];
        
        // Inserts a new record
        $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery( 'tx_alcoquizz_survey', $fields );
        
        return $GLOBALS[ 'TYPO3_DB' ]->sql_insert_id();
    }
}
