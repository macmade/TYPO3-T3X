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

// Includes the FPDF class
require_once( t3lib_extMgm::extPath( 'fpdf' ) . 'class.tx_fpdf.php' );

// Includes the macmade.net API class
require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

/** 
 * PDF export class for the 'adler_contest' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */
class tx_adlercontest_pdfExport extends PDF
{
    /**
     * Wether the static variables are set or not
     */
    private static $_hasStatic         = false;
    
    /**
     * The extension key
     */
    private static $_extKey            = '';
    
    /**
     * The language object
     */
    protected static $_lang            = NULL;
    
    /**
     * The database object
     */
    protected static $_db              = NULL;
    
    /**
     * The upload directory
     */
    protected static $_uploadDirectory = '';
    
    /**
     * The language file
     */
    protected static $_langFile        = 'LLL:EXT:adler_contest/lang/pdf.xml';
    
    /**
     * The profile row
     */
    protected $_profile                = array();
    
    /**
     * The user row
     */
    protected $_user                   = array();
    
    /**
     * Class constructor
     * 
     * @return  NULL
     * @see     _setStaticVars
     */
    public function __construct( array $profile, array $user )
    {
        // Checks if the static variables are set
        if( !self::$_hasStatic ) {
            
            // Sets the static variables
            self::_setStaticVars();
        }
        
        // Stores the database rows
        $this->_profile = $profile;
        $this->_user    = $user;
        
        // Calls the FPDF constructor
        parent::__construct();
    }
    
    /**
     * Sets the needed static variables
     * 
     * @return  NULL
     */
    private static function _setStaticVars()
    {
        // Gets the path
        $extPathInfo            = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
        
        // Removes the 'classes' directory
        array_pop( $extPathInfo );
        
        // Sets the extension key
        self::$_extKey          = array_pop( $extPathInfo );
        
        // Creates a reference to the lang object
        self::$_lang            =  $GLOBALS[ 'LANG' ];
        
        // Creates a reference to the database object
        self::$_db              =  $GLOBALS[ 'TYPO3_DB' ];
        
        // Sets the upload directory
        self::$_uploadDirectory = t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', self::$_extKey ) . '/' );
        
        // Static variables are set
        self::$_hasStatic     = true;
    }
    
    /**
     * Gets a locallang label
     * 
     * @return  string  The locallang label
     */
    protected function _getLabel( $label )
    {
        return self::$_lang->sL( self::$_langFile . ':' . $label );
    }
    
    /**
     * Creates the header for a specific page
     * 
     * @return  NULL
     */
    protected function _pageHeader( $label )
    {
        // Font configuration
        $this->SetFont( 'Helvetica', 'B', 20 );
        
        // Writes the header
        $this->SetXY( 20, 30 );
        $this->Cell( 0, 0, $this->_getLabel( $label ) );
    }
    
    /**
     * Gets a country name from the 'static_countries' table
     * 
     * @param   int $uid    The ID of the country
     * @return  The country name
     */
    protected function _getCountryName( $uid )
    {
        // Selects the country
        $res = self::$_db->exec_SELECTquery(
            'uid,cn_short_en',
            'static_countries',
            'uid=' . $uid
        );
        
        // Checks the ressource
        if( $res && $row = self::$_db->sql_fetch_assoc( $res ) ) {
            
            // Returns the country name
            return $row[ 'cn_short_en' ];
        }
        
        // Country not found
        return $uid;
    }
    
    /**
     * Adds a picture to the current page
     * 
     * @return  NULL
     */
    protected function _addPicture( $file )
    {
        // Absolute path to the file
        $fileAbsPath = self::$_uploadDirectory . $file;
        
        // Maximum dimensions
        $maxWidth    = 170;
        $maxHeight   = 230;
        
        // Gets the image size
        $size        = getimagesize( $fileAbsPath );
        
        // Original dimensions
        $origWidth   = $size[ 0 ];
        $origHeight  = $size[ 1 ];
        
        // Checks the orientation
        if( $origWidth < $origHeight ) {
            
            // Sets the final height
            $height = $maxHeight;
            
            // Sets the final width
            $width  = ( $origWidth * $height ) / $origHeight;
            
        } else {
            
            // Sets the final width
            $width  = $maxWidth;
            
            // Sets the final height
            $height = ( $origHeight * $width ) / $origWidth;
        }
        
        // Adds the picture
        $this->Image(
            $fileAbsPath,
            20,
            40,
            $width,
            $height
        );
    }
    
    /**
     * Creates the 'data' page
     * 
     * @return  NULL
     * @see     _pageHeader
     * @see     _getCountryName
     */
    protected function _dataPage()
    {
        // Adds a new page
        $this->AddPage();
        
        // Adds the header
        $this->_pageHeader( 'data.header' );
        
        // Font configuration
        $this->SetFont( 'Helvetica', 'B', 10 );
        
        // Starting Y position
        $y      = 50;
        
        // Fields to display
        $fields = array(
            'lastname',
            'firstname',
            'gender',
            'email',
            'address',
            'address2',
            'country',
            'nationality',
            'birthdate',
            'school_name',
            'school_address',
            'school_country'
        );
        
        // Process each field
        foreach( $fields as $field ) {
            
            // Checks if the field is 'email'
            if( $field == 'email' ) {
                
                // Gets the label
                $label = self::$_lang->sL( 'LLL:EXT:lang/locallang_general.php:LGL.email' );
                
            } else {
                
                // Gets the label
                $label = self::$_lang->sL( 'LLL:EXT:adler_contest/lang/tx_adlercontest_users.xml:' . $field ) . ':';
            }
            
            // Writes the label
            $this->SetXY( 20, $y );
            $this->Cell( 0, 0, $label );
            
            // Checks the field
            switch( $field ) {
                
                // Gender
                case 'gender':
                    
                    $value = strtoupper( $this->_profile[ $field ] );
                    break;
                
                // Email
                case 'email':
                    
                    $value = $this->_user[ $field ];
                    break;
                
                // Birth date
                case 'birthdate':
                    
                    $value = date( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'ddmmyy' ], $this->_profile[ $field ] );
                    break;
                
                // Country
                case 'country':
                    
                    $value = $this->_getCountryName( $this->_profile[ $field ] );
                    break;
                
                // Country
                case 'school_country':
                    
                    $value = $this->_getCountryName( $this->_profile[ $field ] );
                    break;
                
                // No processing
                default:
                    
                    $value = $this->_profile[ $field ];
                    break;
            }
            
            // Adds the value
            $this->SetXY( 75, $y );
            $this->Cell( 0, 0, $value );
            
            // Increases the Y position
            $y += 10;
        }
    }
    
    /**
     * Creates the 'project' page
     * 
     * @return  NULL
     * @see     _pageHeader
     * @see     _addPicture
     */
    protected function _projectPage()
    {
        // Adds a new page
        $this->AddPage();
        
        // Adds the header
        $this->_pageHeader( 'project.header' );
        
        // Adds the picture
        $this->_addPicture( $this->_profile[ 'project' ] );
    }
    
    /**
     * Creates the 'age proof' page
     * 
     * @return  NULL
     * @see     _pageHeader
     * @see     _addPicture
     */
    protected function _ageProofPage()
    {
        // Adds a new page
        $this->AddPage();
        
        // Adds the header
        $this->_pageHeader( 'age.header' );
        
        // Adds the picture
        $this->_addPicture( $this->_profile[ 'age_proof' ] );
    }
    
    /**
     * Creates the 'school proof' page
     * 
     * @return  NULL
     * @see     _pageHeader
     * @see     _addPicture
     */
    protected function _schoolProofPage()
    {
        // Adds a new page
        $this->AddPage();
        
        // Adds the header
        $this->_pageHeader( 'school.header' );
        
        // Adds the picture
        $this->_addPicture( $this->_profile[ 'school_proof' ] );
    }
    
    /**
     * Outputs the PDF file
     * 
     * This method will aborts the current script, and forces the browser to
     * download the PDF file.
     * 
     * @return  NULL
     */
    public function outputFile()
    {
        // Creates the PDF
        $pdf = $this->Output();
        
        // Outputs the PDF
        tx_apimacmade::div_output( $pdf, 'application/pdf', $this->_user[ 'username' ] . '.pdf' );
        exit();
    }
    
    /**
     * Creates the PDF pages
     * 
     * @return  NULL
     * @see     _projectPage
     * @see     _dataPage
     * @see     _ageProofPage
     * @see     _schoolProofPage
     */
    public function createPages()
    {
        // Checks for a project
        if( $this->_profile[ 'project' ] ) {
            
            // Creates the project page
            $this->_projectPage();
        }
        
        // Creates the personnal data page
        $this->_dataPage();
        
        // Checks for a project
        if( $this->_profile[ 'age_proof' ] ) {
            
            // Creates the age proof page
            $this->_ageProofPage();
        }
        
        // Checks for a project
        if( $this->_profile[ 'school_proof' ] ) {
            
            // Creates the school proof page
            $this->_schoolProofPage();
        }
    }
    
    /**
     * Creates the PDF header
     * 
     * @return  NULL
     * @see     _getLabel
     */
    public function Header()
    {
        // Font configuration
        $this->SetFont( 'Helvetica', 'B', 10 );
        
        // Left header
        $this->SetXY( 20, 20 );
        $this->Cell( 0, 0, $this->_getLabel( 'header.fileNum' ) . ' ' . $this->_profile[ 'uid' ] );
    }
    
    /**
     * Creates the PDF footer
     * 
     * @return  NULL
     * @see     _getLabel
     */
    public function Footer()
    {
        // Number of pages
        $pages = 1;
        
        // Checks for a project
        if( $this->_profile[ 'project' ] ) {
            
            // Increases the number of pages
            $pages++;
        }
        
        // Checks for a project
        if( $this->_profile[ 'age_proof' ] ) {
            
            // Increases the number of pages
            $pages++;
        }
        
        // Checks for a project
        if( $this->_profile[ 'school_proof' ] ) {
            
            // Increases the number of pages
            $pages++;
        }
        
        // Font configuration
        $this->SetFont( 'Helvetica', 'B', 10 );
        
        // Left header
        $this->SetXY( 20, 280 );
        $this->Cell( 0, 0, sprintf( $this->_getLabel( 'footer.pages' ), $this->page, $pages ) );
    }
}

// XCLASS inclusion
if ( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/adler_contest/class.tx_adlercontest_pdfexport.php' ] ) {
    include_once( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/adler_contest/class.tx_adlercontest_pdfexport.php' ] );
}
