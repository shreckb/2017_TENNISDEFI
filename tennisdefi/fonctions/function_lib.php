<?php

/*
 * ! \file
 * \brief Contient les fonctions pourt inclure les librairy javascript
 * permet de vérifier si il y a des mise à jour, un ficheir à modifier et ca met à joiur tout le site....
 *
 * Details.
 */

// ========================================
/*
 * ! \brief Datatable et DataTableCSS
 * version 1.10.4 (update le 18 janv 2015)
 */
// ========================================
function enqueue_script_Lib_DataTable() {
	//wp_enqueue_script ( 'jquery' );
	
	
	//Version 1.10.7
	/*
	wp_enqueue_style ( 'dataTables_css', 'http://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css' );
	wp_enqueue_script ( 'dataTables_script', 'http://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js', array (
			'jquery' 
	) );
	
	// Ajout du module de tri de date
	//wp_enqueue_script ( 'dataTables_script_date_sorting', 'http://cdn.datatables.net/plug-ins/3cfcc339e89/sorting/date-eu.js' );
	wp_enqueue_script ( 'dataTables_script_date_sorting', 'http://cdn.datatables.net/plug-ins/1.10.7/sorting/date-eu.js');
	//Ajout de l'option responsive
	wp_enqueue_style ( 'dataTables_responsive_css', 'http://cdn.datatables.net/responsive/1.0.3/css/dataTables.responsive.css');
	wp_enqueue_script ( 'dataTables_responsive', 'http://cdn.datatables.net/responsive/1.0.3/js/dataTables.responsive.js');

	*/
		//VErsion 1.10.10
	wp_enqueue_style ( 'dataTables_css', 'http://cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css' );
	wp_enqueue_script ( 'dataTables_script', 'http://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js', array (
			'jquery'
	) );
	//ajoit plugin pour l'affichage d'un chartgement
	//wp_enqueue_script ( 'dataTables_processingIndicator',    'http://cdn.datatables.net/plug-ins/1.10.10/api/fnProcessingIndicator.js');
	
	//Ajout du plugin pour tri des dates
	wp_enqueue_script ( 'dataTables_script_date_sorting', 'http://cdn.datatables.net/plug-ins/1.10.10/sorting/date-eu.js');
	//ajout du plugin pour l'option resposive
	wp_enqueue_style ( 'dataTables_responsive_css', 'http://cdn.datatables.net/responsive/2.0.0/css/responsive.dataTables.min.css');
	wp_enqueue_script ( 'dataTables_responsive',    'http://cdn.datatables.net/responsive/2.0.0/js/dataTables.responsive.min.js');
	

}

// ========================================
/*
 * ! \brief Script + Css pour le tableau facon Responstable 2.0 by jordyvanraaij
 * 			Respond en version v1.4.2
 */
// ========================================
function enqueue_script_Lib_RESPONSTABLE() {

 // Script + Css pour le tableau facon Responstable 2.0 by jordyvanraaij
 wp_enqueue_script( 'lib_RESPOND', get_stylesheet_directory_uri().'/js/Respond/dest/respond.min.js');
 wp_enqueue_style( 'palmares_responstable_style2', get_stylesheet_directory_uri().'/js/responstable/css/style.css');
 
}


// ========================================
/*
 * ! \brief // Colorbox (boite de dialog de detail du palmares par exemple)
 */
// ========================================
function enqueue_script_Lib_COLORBOX() {

// Colorbox (boite de dialog de detail du palmares par exemple)
wp_enqueue_style ( 'palmares_colobox_css', get_stylesheet_directory_uri().'/js/colorbox/colorbox.css');
wp_enqueue_script( 'palmares_colobox', get_stylesheet_directory_uri().'/js/colorbox/jquery.colorbox-min.js',  array('jquery'));
}


// ========================================
/*
 * ! \brief Knob : Permet de faire de cercles et chiffres
 * version Fix decimal value #107
 */
// ========================================
function enqueue_script_Lib_Knob() {
	wp_enqueue_script ( 'jquery' );
	wp_enqueue_script ( 'knob_script', get_stylesheet_directory_uri () . '/js/jQuery-Knob/jquery.knob.min.js', array (
			'jquery' 
	) );
}

// ========================================
/*
 * ! \brief Select2: Permet de faire des menus deroulants
 * version 3.5.2
 */
// ========================================
function enqueue_script_Lib_Select2() {
	
	
	// Version 4
	wp_enqueue_style  ('select2_css'    , 		'http:////cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css');
	wp_enqueue_script ( 'select2_script', 		'http:////cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js' , array('jquery'));
	//wp_enqueue_script ( 'select2_script_lang',  'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/fr.js' );
	
	
	// Version 3.5
	/*
	wp_enqueue_script ( 'jquery' );
	wp_enqueue_style  ('select2_css'    , 		'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css');
	wp_enqueue_script ( 'select2_script', 		'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js' );
	wp_enqueue_script ( 'select2_script_lang',  'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2_locale_fr.js' );
	*/
	
	
	// Applique La Lib Select2 à tous les champ <select>
	wp_enqueue_script('autocomple_script',    get_stylesheet_directory_uri().'/js/applySelect2_SelectTag.js', array('jquery'));
	
	
	enqueue_style_smoothness ();
}

// ========================================
/*
 * ! \brief Gestion css utilisé par certaine mise en page 
 * version 3.5.2
 */
// ========================================
function enqueue_style_smoothness (){
	wp_enqueue_style ( 'smoothness_style', 'https://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css');
}

		
	