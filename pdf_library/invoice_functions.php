<?php
require('fpdf.php');

class PDF_Invoice extends FPDF
{
// private variables
var $colonnes;
var $format;

function sizeOfText( $texte, $largeur )
{
    $index    = 0;
    $nb_lines = 0;
    $loop     = TRUE;
    while ( $loop )
    {
        $pos = strpos($texte, "\n");
        if (!$pos)
        {
            $loop  = FALSE;
            $ligne = $texte;
        }
        else
        {
            $ligne  = substr( $texte, $index, $pos);
            $texte = substr( $texte, $pos+1 );
        }
        $length = floor( $this->GetStringWidth( $ligne ) );
        $res = 1 + floor( $length / $largeur) ;
        $nb_lines += $res;
    }
    return $nb_lines;
}

function addCompanyAddress( $address )
{
    $this->SetFont("Arial", "", 12);
    $this->SetXY(7, 248);
    $this->MultiCell( 200, 4, $address);
}

function addClientAddress( $address )
{
    $this->SetFont("Arial", "", 12);
    $this->SetXY(7, 98);
    $this->MultiCell(160, 4, $address);
}

function right_blocks($x1, $y1, $f, $content)
{
    $this->SetFont( "Arial", "", $f);
    $this->SetXY( $x1, $y1);
    $this->Cell(40,4, $content , '', '', "L");
}


function right_blocks_bold($x1, $y1, $f, $content)
{
    $this->SetFont( "Arial", "B", $f);
    $this->SetXY( $x1, $y1);
    $this->Cell(40,4, $content , '', '', "L");
}

function addCols( $tab )
{
    global $colonnes;
    $this->SetFont( "Arial", "", 12);
    $r1  = 10;
    $r2  = $this->w - ($r1 * 2) ;
    $y1  = 153;
    $this->SetXY( $r1, $y1 );
    $this->Line( $r1, $y1+6, $r1+$r2, $y1+6);
    $colX = $r1;
    $colonnes = $tab;
    while ( list( $lib, $pos ) = each ($tab) )
    {
        $this->SetXY( $colX, $y1+2 );
        $this->Cell( $pos, 1, $lib, 0, 0, "C");
        $colX += $pos;
    }
}

function addLineFormat( $tab )
{
    global $format, $colonnes;
    
    while ( list( $lib, $pos ) = each ($colonnes) )
    {
        if ( isset( $tab["$lib"] ) )
            $format[ $lib ] = $tab["$lib"];
    }
}

function addLine( $ligne, $tab )
{
    global $colonnes, $format;

    $ordonnee     = 10;
    $maxSize      = $ligne;

    reset( $colonnes );
    while ( list( $lib, $pos ) = each ($colonnes) )
    {
        $longCell  = $pos -2;
        $texte     = $tab[ $lib ];
        $length    = $this->GetStringWidth( $texte );
        $tailleTexte = $this->sizeOfText( $texte, $length );
        $formText  = $format[ $lib ];
        $this->SetXY( $ordonnee, $ligne-1);
        $this->MultiCell( $longCell, 4 , $texte, 0, $formText);
        if ( $maxSize < ($this->GetY()  ) )
            $maxSize = $this->GetY() ;
        $ordonnee += $pos;
    }
    return ( $maxSize - $ligne );
}

}
?>
