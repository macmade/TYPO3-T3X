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

# $Id: class.tx_alcoquizz_pdf.php 25 2010-06-21 08:05:58Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'lib/fpdf16/fpdf.php' );

class tx_alcoquizz_Pdf extends FPDF
{
    public function __construct()
    {
        parent::__construct( 'P', 'mm', 'A4' );
        
        $this->AddFont( 'museo300', '', 'museo300.php' );
        $this->AddFont( 'museo500', '', 'museo500.php' );
        $this->AddFont( 'museo700', '', 'museo700.php' );
    }
    
    public function Header()
    {
        $this->Image( t3lib_extMgm::extPath( 'alcoquizz' ) . 'res/logo-pdf.png', 10, 10, 50 );
        
        $this->SetY( 20 );
        $this->SetFont( 'museo700', '', 17 );
        $this->Cell( 0, 0, strftime( 'Mon bilan du %e %B %Y, %Hh%M' ), 0, 0, 'R' );
        $this->Ln( 12 );
        
        $this->SetFont( 'museo300', '', 8 );
        $this->MultiCell( 0, 3.5, 'Avertissement: ce bilan ne doit pas remplacer les conseils d’un professionnel de la santé. Les informations recueillies par le site «Alcooquizz» sont entièrement anonymes et peuvent être utilisées à des fins de recherche et pour améliorer le site. Le site «Alcooquizz» n’a enregistré aucune donnée personnelle.' );
        
        $this->SetLineWidth( 0.1 );
        $this->SetDrawColor( 178, 178, 178 );
        $this->Line( 10, 30, 200, 30 );
        $this->Line( 10, 44, 200, 44 );
        $this->Ln( 10 );
    }
    
    public function Footer()
    {
        $this->AliasNbPages();
        $this->SetY( -10 );
        $this->SetFont( 'museo300', '', 9 );
        $this->Cell( 0, 0, 'Une initiative du Département universitaire de médecine et santé communautaires du CHUV.' );
        
        $this->SetLineWidth( 0.1 );
        $this->SetDrawColor( 178, 178, 178 );
        $this->Line( 10, 283, 200, 283 );
        
        $this->SetX( 160 );
        $this->Cell( 45, 0, 'Page ' . $this->PageNo() . ' / {nb}', 0, 0, 'R' );
    }
}
