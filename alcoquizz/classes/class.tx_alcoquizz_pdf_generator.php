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

# $Id: class.tx_alcoquizz_pdf_generator.php 25 2010-06-21 08:05:58Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

define( 'TTF_DIR', PATH_site . 't3lib/fonts/' );

require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'classes/class.tx_alcoquizz_pdf.php' );
require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'classes/class.tx_alcoquizz_amf_service.php' );
require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'lib/jpgraph/jpgraph.php' );
require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'lib/jpgraph/jpgraph_pie.php' );
require_once( t3lib_extMgm::extPath( 'alcoquizz' ) . 'lib/jpgraph/jpgraph_pie3d.php' );

class tx_alcoquizz_Pdf_Generator
{
    protected $_id   = 0;
    protected $_pdf  = NULL;
    protected $_data = NULL;
    protected $_row  = array();
    
    protected $_labels = array
    (
        array( 'A: %.1f%%', 'B: %.1f%%', 'C: %.1f%%', 'D: %.1f%%', 'E: %.1f%%', 'F: %.1f%%', 'G: %.1f%%' ),
        array( 'A: %.1f%%', 'B: %.1f%%', 'C: %.1f%%', 'D: %.1f%%', 'E: %.1f%%' )
    );
    
    protected $_colors = array
    (
        array
        (
            array( '#E2007A', '#E83395', '#ED5CAA', '#F17DBB', '#E2007A', '#E83395', '#ED5CAA' ),
            array( '#E2007A', '#ED5CAA', '#F17DBB', '#E2007A', '#ED5CAA' )
        ),
        array
        (
            array( '#009EE0', '#33B0E6', '#5CBFEB', '#7DCCEF', '#009EE0', '#33B0E6', '#5CBFEB' ),
            array( '#009EE0', '#5CBFEB', '#7DCCEF', '#009EE0', '#5CBFEB' )
        )
    );
    
    protected $_legends = array
    (
        array( 'A: moins de 1 verre', 'B: 1-3 verres', 'C: 3-8 verres', 'D: 8-15 verres', 'E: 15-22 verres', 'F: 22-28 verres', 'G: plus de 28 verres' ),
        array( 'A: jamais', 'B: moins de 1 fois par moins', 'C: chaque mois', 'D: chaque semaine', 'E: tous les jours ou presque' )
    );
    
    protected $_impact = array
    (
        'III11'  => 'Vous avez eu la gueule de bois',
        'III12'  => 'Vous avez manqué le travail ou les cours',
        'III13'  => 'Vous avez accumulé du retard dans votre travail',
        'III14'  => 'Vous avez fait quelquechose que vous avez regretté par la suite',
        'III15'  => 'Vous avez oublié où vous étiez ou ce que vous avez fait',
        'III16'  => 'Vous avez eu des disputes avec des amis',
        'III17'  => 'Vous avez eu une relation sexuelle qui n\'était pas prévue',
        'III18'  => 'Vous avez eu une relation sexuelle non protégée',
        'III19'  => 'Vous avez causé des dommages matériels',
        'III110' => 'Vous avez eu des problèmes avec la police',
        'III111' => 'Vous avez été accidenté ou blessé ou blessé quelqu\'un',
        'III112' => 'Vous avez eu recours à un traitement médical suite à une \'cuite\'',
        'III21'  => 'Votre santé physique en a souffert',
        'III22'  => 'Votre moral en a souffert'
    );
    
    public function __construct( $id )
    {
        $this->_id = ( int )$id;
        $res       = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( '*', 'tx_alcoquizz_result', 'uid=' . $this->_id );
        
        if( $res && $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) )
        {
            $this->_row = $row;
            $this->_pdf = new tx_alcoquizz_Pdf( 'P', 'mm', 'A4' );
            
            $service     = new tx_alcoquizz_Amf_Service();
            $params      = new stdClass();
            $params->id  = $this->_id;
            $this->_data = $service->getData( $params );
            
            $this->_buildPdf();
        }
    }
    
    public function out()
    {
        if( is_object( $this->_pdf ) )
        {
            $this->_pdf->Output();
        }
        else
        {
            throw new Exception( 'PDF file was not generated' );
        }
    }
    
    protected function _image( $file )
    {
        $sex   = ( $this->_row[ 'I1' ] == 1 ) ? 'man' : 'woman';
        $image = t3lib_extMgm::extPath( 'alcoquizz' ) . 'res/' . $sex . '-' . $file;
        
        return $image;
    }
    
    protected function _colorText()
    {
        if( $this->_row[ 'I1' ] == 1 )
        {
            $this->_pdf->SetTextColor( 0, 158, 231 );
        }
        else
        {
            $this->_pdf->SetTextColor( 225, 39, 121 );
        }
    }
    
    protected function _dashedLine( $y, $start, $end )
    {
        while( $start < $end )
        {
            $this->_pdf->Line( $start, $y, $start + 1, $y );
            
            $start += 2;
        }
    }
    
    protected function _buildPdf()
    {
        $this->_pdf->SetFont( 'museo300', '', 11 );
        #$this->_pdf->AddPage();
        #$this->_pageConsumption();
        #$this->_pdf->AddPage();
        #$this->_pageImpact();
        #$this->_pdf->AddPage();
        #$this->_pageAlcohol();
        #$this->_pdf->AddPage();
        #$this->_pageCalories();
        $this->_pdf->AddPage();
        $this->_pageRecommendations();
    }
    
    protected function _pageImpact()
    {
        $this->_pdf->SetY( 50 );
        $this->_pdf->SetFont( 'museo700', '', 17 );
        $this->_pdf->MultiCell( 200, 7, 'Dans quelle mesure l\'alcool à un impact sur moi', 0, 'L' );
        
        $this->_pdf->SetXY( 75, 65 );
        $this->_pdf->Image( $this->_image( 'impact.png' ), 75, 65, 60, 60 );
        
        $this->_pdf->SetFont( 'museo700', '', 11 );
        $this->_pdf->SetXY( 75, 130 );
        
        if( count( $this->_data[ 'impact' ] ) )
        {
            $this->_pdf->MultiCell( 60, 4, 'Votre consommation d’acool a eu un impact négatif sur votre santé physique.', 0, 'C' );
        }
        else
        {
            $this->_pdf->MultiCell( 60, 5, 'Au cours de la dernière année, vous n\'avez pas indiqué de conséquences négatives de votre consommation d\'alcool', 0, 'C' );
        }
        
        $i = 0;
        $y = 65;
        
        foreach( $this->_data[ 'impact' ] as $key => $value )
        {
            $x = ( $i % 2 ) ? 140 : 10;
            
            $this->_pdf->SetFillColor( 229 );
            $this->_pdf->Rect( $x, $y, 60, 20, 'F' );
            
            if( $i % 2 )
            {
                $this->_pdf->SetLeftMargin( 142 );
                $this->_pdf->SetRightMargin( 12 );
            }
            else
            {
                $this->_pdf->SetLeftMargin( 12 );
                $this->_pdf->SetRightMargin( 142 );
            }
            
            $this->_pdf->SetY( $y + 2 );
            
            $this->_pdf->SetFont( 'museo300', '', 11 );
            $this->_pdf->write( 5, $this->_impact[ $value ] );
            
            if( $i % 2 )
            {
                $y += 22;
            }
            
            $i++;
        }
        
        $this->_pdf->SetLeftMargin( 10 );
        $this->_pdf->SetRightMargin( 10 );
    }
    
    protected function _pageAlcohol()
    {
        $this->_pdf->SetY( 50 );
        $this->_pdf->SetFont( 'museo700', '', 17 );
        $this->_pdf->MultiCell( 200, 7, 'Mon alcoolémie  la plus élevée', 0, 'L' );
        
        $this->_colorText();
        $this->_pdf->SetFont( 'museo700', '', 40 );
        $this->_pdf->SetXY( 75, 75 );
        $this->_pdf->MultiCell( 0, 0, round( $this->_data[ 'alcohol' ], 2 ) . '‰' );
        $this->_pdf->SetTextColor( 0 );
        
        
        $this->_pdf->SetFont( 'museo300', '', 11 );
        $this->_pdf->SetLeftMargin( 75 );
        $this->_pdf->SetRightMargin( 72 );
        $this->_pdf->SetY( 85 );
        
        $this->_pdf->write
        (
            5,
            sprintf
            (
                'est le taux d\'alcoolémie correspondant à votre consommation de %s boissons alcoolisées standards en %s heure(s).',
                $this->_data[ 'last_drinks' ],
                $this->_data[ 'last_hours' ]
            )
        );
        
        $this->_pdf->SetFillColor( 229 );
        $this->_pdf->Rect( 10, 65, 60, 202, 'F' );
        
        $this->_pdf->SetLeftMargin( 12 );
        $this->_pdf->SetRightMargin( 142 );
        $this->_pdf->SetY( 68 );
        $this->_pdf->SetFont( 'museo700', '', 11 );
        $this->_pdf->write( 5, 'Alcoolémie (pour mille)' );
        
        $this->_dashedLine( 74, 12, 68 );
        $this->_pdf->SetFont( 'museo700', '', 9 );
        $this->_pdf->SetY( 77 );
        $this->_pdf->SetLeftMargin( 12 );
        
        $this->_pdf->write( 5, '0.2' );
        $this->_pdf->Ln( 20 );
        $this->_pdf->write( 5, '0.5' );
        $this->_pdf->Ln( 40 );
        $this->_pdf->write( 5, '0.6' );
        $this->_pdf->Ln( 35 );
        $this->_pdf->write( 5, '0.8' );
        $this->_pdf->Ln( 15 );
        $this->_pdf->write( 5, '1' );
        $this->_pdf->Ln( 20 );
        $this->_pdf->write( 5, '1.2' );
        $this->_pdf->Ln( 10 );
        $this->_pdf->write( 5, '2' );
        $this->_pdf->Ln( 10 );
        $this->_pdf->write( 5, '3' );
        $this->_pdf->Ln( 20 );
        $this->_pdf->write( 5, '4' );
        
        $this->_dashedLine( 74, 12, 68 );
        $this->_pdf->SetFont( 'museo300', '', 9 );
        $this->_pdf->SetY( 77 );
        $this->_pdf->SetLeftMargin( 22 );
        $this->_pdf->write( 5, 'Légère altération de la vision et de l’audition.Sensation de bien-être, plaisir' );
        $this->_pdf->Ln( 10 );
        $this->_dashedLine( 95, 12, 68 );
        $this->_pdf->write( 5, 'Limite légale pour la conduite d’un véhicule en Suisse et dans la plupart des pays de l’Union Européenne. Diminution de l’attention, de la concentration et des réflexes' );
        $this->_pdf->Ln( 10 );
        $this->_dashedLine( 135, 12, 68 );
        $this->_pdf->write( 5, 'Risque d’accident 2 fois plus important à 0.6 qu’à 0.5 pour milleBaisse de la vision nocturne, légers troubles de l’équilibre, augmentation du temps de réaction' );
        $this->_pdf->Ln( 10 );
        $this->_dashedLine( 170, 12, 68 );
        $this->_pdf->write( 5, 'Troubles de la concentration, désinhibition' );
        $this->_pdf->Ln( 10 );
        $this->_dashedLine( 184, 12, 68 );
        $this->_pdf->write( 5, 'Forts troubles de l’équilibre, problèmes d’élocution, confusion, jugement altéré' );
        $this->_pdf->Ln( 10 );
        $this->_dashedLine( 204, 12, 68 );
        $this->_pdf->write( 5, 'Vomissements' );
        $this->_pdf->Ln( 10 );
        $this->_dashedLine( 214, 12, 68 );
        $this->_pdf->write( 5, 'Black-out (oublis)' );
        $this->_pdf->Ln( 10 );
        $this->_dashedLine( 224, 12, 68 );
        $this->_pdf->write( 5, 'Coma - Etat d’anesthésie, troubles de la mémoire et de la conscience' );
        $this->_pdf->Ln( 10 );
        $this->_dashedLine( 244, 12, 68 );
        $this->_pdf->write( 5, 'Risque vital (risque d’arrêt respiratoire), troubles respiratoires, hypothermie' );
        $this->_pdf->Ln( 10 );
        
        $this->_pdf->SetLeftMargin( 10 );
        $this->_pdf->SetRightMargin( 10 );
        
        $this->_pdf->SetFillColor( 229 );
        $this->_pdf->Rect( 140, 65, 60, 50, 'F' );
        
        $this->_pdf->SetLeftMargin( 142 );
        $this->_pdf->SetRightMargin( 12 );
        $this->_pdf->SetY( 68 );
        
        $this->_colorText();
        $this->_pdf->SetFont( 'museo700', '', 11 );
        $this->_pdf->write( 5, 'Risques encourrus:' );
        $this->_pdf->Ln( 10 );
        $this->_pdf->SetFont( 'museo300', '', 11 );
        $this->_pdf->SetTextColor( 0 );
        $this->_pdf->write
        (
            5,
            sprintf
            (
                'Avec une alcoolémie de %s, vous avez %s fois plus de risque d\'être impliqué dans un accident de la route mortel que quelqu\'un avec une alcoolémie à 0.',
                round( $this->_data[ 'alcohol' ], 2 ),
                round( $this->_data[ 'alcohol_risk' ] )
            )
        );
        
        $this->_pdf->SetLeftMargin( 75 );
        $this->_pdf->SetRightMargin( 10 );
        $this->_pdf->SetY( 120 );
        
        $this->_pdf->write( 5, '0.1‰ par heure. Il faut donc 10 heures pour éliminer une alcoolémie de 1‰. L\'élimination de l\'alcool est constant. Il n\'existe pas de moyen d\'accélérer cette élimination.' );
        $this->_pdf->Ln( 10 );
        $this->_pdf->write( 5, 'L\'acloolémie indique la quantité d\'alcool présente dans le sang. Le taux d\'alcoolémie se mesure en grammes d\'alcool par litre de sang (ou g pour mille, indiqué avec ce sigle : ‰)' );
        
        $this->_pdf->SetLeftMargin( 10 );
        $this->_pdf->SetRightMargin( 10 );
    }
    
    protected function _pageCalories()
    {
        $this->_pdf->SetY( 50 );
        $this->_pdf->SetFont( 'museo700', '', 17 );
        $this->_pdf->MultiCell( 200, 7, 'Equivalent calorique de ma consommation d’alcool.', 0, 'L' );
        
        $this->_pdf->SetXY( 90, 65 );
        $this->_pdf->Image( $this->_image( 'kcal.png' ), 90, 65, 22 );
        
        $this->_colorText();
        
        $this->_pdf->SetFont( 'museo700', '', 40 );
        $this->_pdf->SetXY( 115, 75 );
        $this->_pdf->MultiCell( 0, 0, round( $this->_data[ 'hamburgers' ] ) );
        
        $this->_pdf->SetFont( 'museo700', '', 11 );
        $this->_pdf->SetXY( 115, 88 );
        $this->_pdf->MultiCell( 0, 0, 'ou' );
        
        $this->_pdf->SetFont( 'museo700', '', 40 );
        $this->_pdf->SetXY( 115, 102 );
        $this->_pdf->MultiCell( 0, 0, round( $this->_data[ 'chocolates' ] ) );
        
        $this->_pdf->SetFont( 'museo700', '', 20 );
        $this->_pdf->SetXY( 90, 130 );
        $this->_pdf->MultiCell( 0, 0, sprintf( 'soit %s kcal', round( $this->_data[ 'kcal' ] ) ) );
        
        $this->_dashedLine( 120, 85, 150 );
        $this->_pdf->SetTextColor( 0 );
        
        $this->_pdf->SetFillColor( 229 );
        $this->_pdf->Rect( 10, 65, 60, 42, 'F' );
        
        $this->_pdf->SetXY( 12, 68 );
        $this->_pdf->SetFont( 'museo300', '', 11 );
        $this->_pdf->MultiCell
        (
            56,
            6,
            sprintf
            (
                'Au cours des 3 derniers mois, vous avez ingéré %s kcal en boissons alcoolisées, soit l\'équivalent de %s hamburgers ou de %s barres de chocolat.',
                round( $this->_data[ 'kcal' ] ),
                round( $this->_data[ 'hamburgers' ] ),
                round( $this->_data[ 'chocolates' ] )
            ),
            0,
            'L'
        );
        
        $this->_pdf->SetY( 140 );
        $this->_pdf->SetFont( 'museo700', '', 11 );
        $this->_pdf->write( 5, 'Note:' );
        $this->_pdf->Ln( 5 );
        $this->_pdf->SetFont( 'museo300', '', 11 );
        $this->_pdf->write( 5, 'Ces calories sont contenues dans l\'éthanol (ou alcool "pur"). Si l\'alcool est mélangé avec une autre boisson, le contenu en calories peut alors être plus élevé. Par exemple, voici le nombre de kcal contenues dans un whisky coca (4cl de whisky à 40° et 3 dl de coca) :' );
        $this->_pdf->Ln( 10 );
        $this->_pdf->write( 5, '- 4cl de whisky = 13g d\'éthanol = 91kcal' );
        $this->_pdf->Ln( 5 );
        $this->_pdf->write( 5, '- 3dl coca = 135 kcal' );
        $this->_pdf->Ln( 5 );
        $this->_pdf->write( 5, '- Total = 226 kcal' );
    }
    
    protected function _pageRecommendations()
    {
        $this->_pdf->SetY( 50 );
        $this->_pdf->SetFont( 'museo700', '', 17 );
        $this->_pdf->MultiCell( 120, 7, 'Recommandations suite à mon bilan' );
        
        $this->_pdf->SetY( 65 );
        $this->_pdf->Image( $this->_image( 'cat-' . $this->_data[ 'category' ] . '.png' ), 10, 65, 45 );
        
        switch( $this->_data[ 'category' ] )
        {
            case 0:
                
                $header = 'Il ressort de vos réponses que votre profil de consommation d’alcool ne présente globalement pas ou peu de risque pour votre santé.';
                break;
                
            case 1:
                
                $header = 'Il ressort de vos réponses que votre profil de consommation d’alcool pourrait mettre votre santé en danger.';
                break;
                
            case 2:
                
                $header = 'Il ressort de vos réponses que votre profil de consommation d’alcool pourrait mettre votre santé en danger.';
                break;
        }
        
        $this->_pdf->SetY( 60 );
        $this->_pdf->SetFont( 'museo700', '', 11 );
        $this->_pdf->SetFillColor( 229 );
        $this->_pdf->SetLeftMargin( 60 );
        $this->_pdf->write( 5, $header );
        
        $this->_pdf->SetFont( 'museo300', '', 10 );
        
        if( $this->_data[ 'category' ] == 0 )
        {
            $this->_pdf->Ln( 10 );
            
            if( count( $this->_data[ 'impact' ] ) == 0 )
            {
                $this->_pdf->write( 5, 'Il n’est pas nécessaire de modifier vos habitudes de consommation d’alcool.' );
            }
            else
            {
                $this->_pdf->write( 5, 'Vous avez toutefois indiqué les conséquences suivantes de votre consommation:' );
                $this->_pdf->Ln( 10 );
                
                foreach( $this->_data[ 'impact' ] as $key => $value )
                {
                    $this->_pdf->write( 5, ' - ' . $this->_impact[ $value ] );
                    $this->_pdf->Ln( 5 );
                }
            }
        }
        
        if( $this->_data[ 'category' ] == 1 || $this->_data[ 'category' ] == 2 )
        {
            $this->_pdf->Ln( 10 );
            $this->_pdf->SetFont( 'museo700', '', 11 );
            $this->_pdf->write( 5, 'Ce danger est lié à:' );
            $this->_pdf->Ln( 10 );
            $this->_pdf->SetFont( 'museo300', '', 11 );
            $this->_pdf->write( 5, '- une consommation d’alcool aiguë qui augmente principalement le risque d’accident (risque d’accident mortel multiplié par 10 pour une alcoolémie de 0.9 pour mille). La consommation de 6 verres ou plus lors d’une même occasion augmente aussi les comportements sexuels à risque et le fait d’être violent ou d’être victime de violence.' );
            $this->_pdf->Ln( 10 );
            $this->_pdf->write( 5, '- une consommation régulière de quantités d’alcool qui peuvent provoquer des problèmes de santé à moyen/long terme (par exemple: l’alcool joue un rôle dans l’apparition de cancers , de problèmes psychiques, d’hypertension artérielle)' );
        }
        
        if( $this->_data[ 'category' ] == 2 )
        {
            if( $this->_data[ 'risk' ] == 2 || $this->_data[ 'risk' ] == 3 )
            {
                $this->_pdf->Ln( 10 );
                
                if( $this->_row[ 'I1' ] == 1 )
                {
                    $this->_pdf->write( 5, 'Un homme buvant 4 boissons alcoolisées standards par jour a 3 fois plus de chance d’avoir un cancer du foie et 2 fois plus de chance d’avoir de l’hypertension artérielle qu’un homme ne buvant pas d’alcool.' );
                }
                else
                {
                    $this->_pdf->write( 5, 'Une femme buvant 2 boissons alcoolisées standards par jour a 2 fois plus de chance d’avoir de l’hypertension artérielle et 1,4 fois plus de chance d’avoir un cancer du sein qu’une femme ne buvant pas d’alcool.' );
                }
            }
            
            $this->_pdf->Ln( 10 );
            $this->_pdf->write( 5, 'Vos réponses indiquent aussi une probabilité élevée (80%) de problèmes sérieux liés à la consommation d’alcool (comme la dépendance). Nous vous encourageons à aborder la question de votre consommation d’alcool avec un professionnel de la santé.' );
        }
        
        $this->_pdf->Ln( 10 );
        $this->_pdf->SetFont( 'museo700', '', 11 );
        $this->_pdf->write( 5, 'Ce qu\'il faut savoir' );
        $this->_pdf->Ln( 10 );
        $this->_pdf->SetFont( 'museo300', '', 11 );
        $this->_pdf->write( 5, 'Les problèmes d\'alcool et la dépendance peuvent toucher tout le monde, les hommes comme les femmes. Certaines personnes ont des facteurs de vulnérabilité, c\'est à dire qu\'elles ont plus de risque de devenir dépendantes si elles consomment régulièrement de l\'alcool. Les personnes qui ont des membres de leur famille souffrant de problèmes d\'alcool ont un risque augmenté: 15% des personnes qui ont un parent dépendant de l\'alcool vont devenir dépendants par rapport à 5% dans la population générale (risque trois fois plus élevé). De la même manière, les personnes qui tolèrent bien l\'alcool (bien « tenir » l\'alcool, pouvoir boire plus que les autres et ne ressentir que très peu les effets de l\'alcool) ont un risque plus élevé de devenir dépendantes (50% des personnes hautement tolérantes vont devenir dépendantes).' );
        $this->_pdf->Ln( 10 );
        $this->_pdf->write( 5, 'Note: les recherches scientifiques menées jusqu\'à ce jour ne permettent pas d\'indiquer quelle est la quantité d\'alcool que peut consommer une femme enceinte sans risque de faire une fausse couche ou de provoquer des problèmes de croissance ou de développement chez son enfant à naître. Par principe de précaution et en l\'absence de données prouvées, les recommandations actuelles sont de limiter au maximum la consommation d\'alcool, ou de l\'arrêter, durant la grossesse.' );
        $this->_pdf->Ln( 10 );
        $this->_pdf->SetFont( 'museo700', '', 11 );
        $this->_pdf->write( 5, 'Remarque' );
        $this->_pdf->Ln( 10 );
        $this->_pdf->SetFont( 'museo300', '', 11 );
        $this->_pdf->write( 5, 'Boire de l\'alcool est souvent synonyme de plaisir et de divertissement. Lors de fêtes, d\'événements familiaux et sociaux, les boissons alcoolisées ont souvent leur place et la plupart des personnes en font une consommation modérée, ne présentant pas de risque pour eux-mêmes ni pour les autres.' );
        
        $this->_pdf->SetLeftMargin( 10 );
    }
    
    protected function _pageConsumption()
    {
        $consumptionData     = array();
        $consumptionAltData  = array();
        
        foreach( $this->_data[ 'consumption_data' ] as $object )
        {
            $consumptionData[] = $object->value;
        }
        
        foreach( $this->_data[ 'consumption_alt_data' ] as $object )
        {
            $consumptionAltData[] = $object->value;
        }
        
        $pie1 = $this->_pieChart( $consumptionData, $this->_data[ 'consumption_rate' ], 0 );
        $pie2 = $this->_pieChart( $consumptionAltData, $this->_data[ 'consumption_alt_rate' ], 1 );
        
        $this->_pdf->Image( $pie1, 60, 60, 100, 100 );
        $this->_pdf->Image( $pie2, 60, 175, 100, 100 );
        
        $this->_chartLegends( 0, 120 );
        $this->_chartLegends( 1, 240 );
        
        $this->_pdf->SetY( 50 );
        $this->_pdf->SetFont( 'museo700', '', 17 );
        $this->_pdf->MultiCell( 120, 7, 'Où je me situe par rapport aux autres' );
        
        $this->_pdf->SetY( 60 );
        $this->_pdf->SetFont( 'museo700', '', 15 );
        $this->_pdf->MultiCell( 120, 7, 'Consommation hebdomadaire moyenne' );
        
        $this->_pdf->SetY( 80 );
        $this->_pdf->SetFont( 'museo300', '', 11 );
        $this->_pdf->MultiCell
        (
            60,
            5,
            sprintf
            (
                'Vous avez dit boire %s boissons alcoolisées standards par semaine. Voici comment se situe votre consommation d\'alcool par rapport aux autres %s de %s ans.',
                $this->_data[ 'consumption' ],
                ( $this->_row[ 'I1' ] == 1 ) ? 'hommes' : 'femmes',
                implode( ' - ', $this->_data[ 'consumption_age' ] )
            ),
            0,
            'L'
        );
        
        $maxNum = ( $this->_row[ 'I1' ] == 1 ) ? 5 : 4;
        
        $this->_pdf->SetY( 170 );
        $this->_pdf->SetFont( 'museo700', '', 15 );
        $this->_pdf->MultiCell( 120, 7, 'Fréquence des épisodes de consommation avec ' . $maxNum . ' boissons alcoolisées ou plus' );
        
        $this->_pdf->SetY( 195 );
        $this->_pdf->SetFont( 'museo300', '', 11 );
        
        if( $this->_data[ 'last_drinks' ] >= $maxNum )
        {
            $this->_pdf->MultiCell
            (
                60,
                5,
                sprintf
                (
                    'Vous avez dit boire %s %s boissons alcoolisées (ou plus) lors d’une même occasion. Voici comment se situe votre consommation d\'alcool par rapport aux autres %s de %s ans',
                    strtolower( $this->_data[ 'consumption_alt_data' ][ $this->_data[ 'consumption_alt_rate' ] ]->key ),
                    $maxNum,
                    ( $this->_row[ 'I1' ] == 1 ) ? 'hommes' : 'femmes',
                    implode( ' - ', $this->_data[ 'consumption_age' ] )
                ),
                0,
                'L'
            );
        }
        else
        {
            $this->_pdf->MultiCell( 60, 5, 'Vous avez dit ne jamais boire ' . $maxNum . ' boissons alcoolisées (ou plus) lors d’une même occasion.' );
        }
        
        $this->_pdf->SetFillColor( 229 );
        $this->_pdf->Rect( 155, 80, 45, 26, 'F' );
        
        $this->_pdf->SetLeftMargin( 157 );
        $this->_pdf->SetRightMargin( 12 );
        $this->_pdf->SetY( 82 );
        
        $this->_pdf->SetFont( 'museo700', '', 9 );
        $this->_pdf->write( 4, 'En moyenne par semaine:' );
        $this->_pdf->Ln( 6 );
        $this->_pdf->SetFont( 'museo300', '', 9 );
        $this->_pdf->write
        (
            4,
            sprintf(
                '%s%% des %s de %s ans boivent de l\'alcool comme vous',
                round( $this->_data[ 'consumption_data' ][ $this->_data[ 'consumption_rate' ] ]->value, 1 ),
                ( $this->_row[ 'I1' ] == 1 ) ? 'hommes' : 'femmes',
                implode( ' - ', $this->_data[ 'consumption_age' ] )
            )
        );
        
        $less = 0;
        
        for( $i = 0; $i < $this->_data[ 'consumption_rate' ]; $i++ )
        {
            $less += $this->_data[ 'consumption_data' ][ $i ]->value;
        }
        
        if( $less > 0 )
        {
            $this->_pdf->SetFillColor( 229 );
            $this->_pdf->Rect( 155, 106, 45, 16, 'F' );
            
            
            
            $this->_pdf->Ln( 8 );
            $this->_pdf->write
            (
                4,
                sprintf(
                    '%s%% des %s de %s ans boivent moins que vous',
                    round( $less, 1 ),
                    ( $this->_row[ 'I1' ] == 1 ) ? 'hommes' : 'femmes',
                    implode( ' - ', $this->_data[ 'consumption_age' ] )
                )
            );
        }
        
        $this->_pdf->SetFillColor( 229 );
        $this->_pdf->Rect( 155, 195, 45, 31, 'F' );
        
        $this->_pdf->SetLeftMargin( 157 );
        $this->_pdf->SetRightMargin( 12 );
        $this->_pdf->SetY( 197 );
        
        $this->_pdf->SetFont( 'museo700', '', 9 );
        $this->_pdf->write( 4, 'Par occasion:' );
        $this->_pdf->Ln( 6 );
        $this->_pdf->SetFont( 'museo300', '', 9 );
        $this->_pdf->write
        (
            4,
            sprintf(
                '%s%% boivent aussi fréquemment que vous 5 boissons alcoolisées ou plus lors d\'une même occasion',
                round( $this->_data[ 'consumption_alt_data' ][ $this->_data[ 'consumption_alt_rate' ] ]->value, 1 )
            )
        );
        
        $less = 0;
        
        for( $i = 0; $i < $this->_data[ 'consumption_alt_rate' ]; $i++ )
        {
            $less += $this->_data[ 'consumption_alt_data' ][ $i ]->value;
        }
        
        if( $less > 0 )
        {
            $this->_pdf->SetFillColor( 229 );
            $this->_pdf->Rect( 155, 226, 45, 20, 'F' );
            
            $this->_pdf->Ln( 8 );
            $this->_pdf->write
            (
                4,
                sprintf(
                    '%s%% boivent moins fréquemment 5 boissons alcoolisées ou plus lors d\'une même occasion',
                    round( $less, 1 )
                )
            );
        }
        
        $this->_pdf->SetLeftMargin( 10 );
        $this->_pdf->SetRightMargin( 10 );
    }
    
    protected function _chartLegends( $type, $y )
    {
        $this->_pdf->SetFont( 'museo300', '', 9 );
        
        $sex = ( int )$this->_row[ 'I1' ];
        
        foreach( $this->_legends[ $type ] as $key => $legend )
        {
            $color = $this->_colors[ $sex ][ $type ][ $key ];
            $r     = hexdec( substr( $color, 1, 2 ) );
            $g     = hexdec( substr( $color, 3, 2 ) );
            $b     = hexdec( substr( $color, 5, 2 ) );
            
            $this->_pdf->SetFillColor( $r, $g, $b );
            $this->_pdf->Rect( 11, $y + 1, 3, 3, 'FD' );
            
            $this->_pdf->SetY( $y );
            $this->_pdf->SetLeftMargin( 15 );
            $this->_pdf->write( 5, $legend );
            
            $y += 5;
        }
        
        $this->_pdf->SetLeftMargin( 10 );
        $this->_pdf->SetFont( 'museo300', '', 11 );
    }
    
    protected function _pieChart( array $data, $index, $type )
    {
        $image  = 'alcoquizz_' . $this->_row[ 'uid' ] . '_' . $type . '.png';
        $source = PATH_site . 'typo3temp/' . $image;
        $sex    = ( int )$this->_row[ 'I1' ];
        
        # DEBUG ONLY
        t3lib_div::unlink_tempfile( $source );
        
        if( file_exists( $source ) )
        {
            return $source;
        }
        
        $graph = new PieGraph( 1000, 1000, '', 0, false );
        $pie   = new PiePlot( $data );
        
        $graph->SetUserFont('vera.ttf');
        $graph->img->SetQuality( 100 );
        $graph->SetAntiAliasing( true );
        $graph->SetColor( 'white' );
        $graph->SetFrame( false, 'black', 1 );
        
        $pie->value->SetFont( FF_USERFONT, FS_NORMAL, 20 );
        
        $pie->SetCenter( 0.475, 0.5 );
        $pie->SetLabels( $this->_labels[ $type ] );
        $pie->SetSliceColors( $this->_colors[ $sex ][ $type ] );
        $pie->ExplodeSlice( $index );
        
        $graph->Add( $pie );
        $graph->Stroke( $source );
        
        return $source;
    }
}
