<?php
/*
Template Name: palmares_V5_printable
*/


/*! \page TENNISDEFI_templates_Page templates des pages tennis defi
  

  \section palmares_V5_printable affiche le palmares du club de facon imprimable

*/


// DATa du joueur
//*********************************
// obtention du club du joeur
$current_user = wp_get_current_user();
$current_club = get_user_meta($current_user->ID, TENNISDEIF_XPROFILE_idClub, true);
$ID_currentuse_palmares = getUserPalmaresID($current_user->ID, $current_club);
$club_nom = get_post($current_club)->post_title;
// GEstion des différents  cat.
	if(isset($_GET['CATEGORIE']))
		$palmares_cat =$_GET['CATEGORIE'];
	else
			$palmares_cat = TENNISDEFI_PALMARES_CAT_MIXTE;

		
	//Palamres
	if(isset($_GET['palmares_type']))
		$palmares_type =$_GET['palmares_type'];
	else 
		$palmares_type = TENNISDEFI_PALMARES_TYPE_all;


		$isUserAdminInCLub = false ; //on force pour eviter l'affichage d'info perso dans un pdf
		$for_printing = true;
		
		//Affichage Classique
		if($palmares_type == TENNISDEFI_PALMARES_TYPE_all)
			$DATA_palmares = palmares_displayAll($current_user->ID, $current_club, $isUserAdminInCLub , $palmares_cat, false, $for_printing);			 
		elseif($palmares_type == TENNISDEFI_PALMARES_TYPE_actif)
			$DATA_palmares = palmares_displayAll($current_user->ID, $current_club, $isUserAdminInCLub , $palmares_cat, true, $for_printing);
		elseif($palmares_type == TENNISDEFI_PALMARES_TYPE_friends)
			$DATA_palmares = palmares_displayFriendAndMe($current_club, $current_user->ID,$isUserAdminInCLub, $palmares_cat, $for_printing);
		else {
			// On a alors l'id d'un tournois
			$id_tournoi = encrypt_decrypt('decrypt', $palmares_type);
			if( get_post_type( $id_tournoi  )  != 'tournoi' ){
				echo "erreur"; wp_die();
			}
			$DATA_palmares = palmares_displayTournoi($current_user->ID, $current_club, $id_tournoi, $for_printing);
			}
			
			
//write_log("palmares_type = $palmares_type et DATA_palmares = $palmares_cat");
//write_log($DATA_palmares);

// **************************
//Generation PDF : 
// http://www.tcpdf.org/examples.php
// **************************
// Include the main TCPDF library (search for installation path).
//write_log(get_stylesheet_directory_uri().'/fonctions/fpdf181/fpdf.php');
//require(STYLESHEETPATH . '/fonctions/fpdf181/fpdf.php');
require_once(STYLESHEETPATH . '/fonctions/tcpdf/tcpdf.php');
//require_once('./fonctions/fpdf181/fpdf.php');


// INIT
class TENNISDEFI_PDF extends TCPDF
{
	// En-tête
	public function Header()
	{
		$logo_header = get_stylesheet_directory_uri().'/images/logo_email.png';
		//$titre = "Tennis Defi Palmarès";

		// Logo
		$this->Image($logo_header,10,6,80);
		// Police helvetica gras 15
		$this->SetFont('helvetica','B',15);
		// Saut de ligne
		//$this->Ln(22);
		// Title
		//$this->Cell(0,15,$titre,0,false,'C');
		//Saut de ligne
		//$this->Ln(5);

	}

	// Pied de page
	public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        $txt = "palmarès généré le ".date('d/m/Y')." sur www.tennis-defi.com";
        $this->Cell(50,10,$txt,0,false, 'L');
        // Page number
        $txt = 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages();
        $this->Cell(50, 10, $txt, 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }


}


// create new PDF document
$pdf = new TENNISDEFI_PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tennis-Défi.com');
$pdf->SetTitle('Palmarès');
$pdf->SetSubject('palmarès du club : '.$club_nom);
$pdf->SetKeywords('tennisdefi.com, premier tournoi en ligne, palmarès');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+5, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



//============================================================+
// END OF INIT
//============================================================+
$pdf->AddPage();

// CLub
$html = '<H2 align="center">Palmarès  du club : '.$club_nom.'</H2>';
$pdf->writeHTML($html, true, false, true, false, '');


// Info
$info = '<br><br>'.$DATA_palmares['info'].'<br><br>';
$pdf->writeHTML($info, true, false, true, false, '');


//corps du tableau
$pdf->writeHTML($DATA_palmares['data'], true, false, true, false, '');

//Legend
$html = '<img height="15" width="15" src="'.get_bloginfo('stylesheet_directory') .'/images/icon-recherche-partenaires.png" /> Ce joueur recherche des partenaires';
$pdf->writeHTML($html, true, false, true, false, '');


$filename = date('Y_m_d')."_palmares.pdf";
$pdf->Output( $filename, "I" );
