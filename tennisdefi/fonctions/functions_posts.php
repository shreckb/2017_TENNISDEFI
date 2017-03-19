<?php

/*! \file
     \brief Contient les fonctions de créations des custums post TennisDEFI
    
    Details.

*/



/*
function custumWprofileField()
{
// construct data to save
		$data = compact( 
			array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete' ) 
		);
		
		// use bp function to get new field ID
		$field_id = xprofile_insert_field( $data );
}

add_action('init', 'custumWprofileField');
*/
// ========================
/*! \brief Gestion des Posts : Clubs,resultats, classements,  tournoi interne club
*/
// =========================
function my_custom_init()
{
    // CLub
    register_post_type('club', array(
        'label' => __('clubs'),
        'singular_label' => __('club'),
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'register_meta_box_cb', 'add_events_metaboxesCLUB'
    ));

    // resultat
    register_post_type('resultats', array(
        'label' => __('resultats'),
        'singular_label' => __('resultat'),
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'has_archive' => false,
        'supports' => array('custom-fields'),
    ));

    // palmares
    register_post_type('palmares', array(
        'label' => __('palmares'),
        'singular_label' => __('palmares'),
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'has_archive' => false,
        'supports' => array('custom-fields'),
    ));

    // tournois
    register_post_type('tournoi', array(
    		'label' => __('tournois'),
    		'singular_label' => __('tournoi'),
    		'public' => false,
    		'show_ui' => true,
    		'capability_type' => 'post',
    		'hierarchical' => false,
    		'has_archive' => false,
    		'supports' => array('custom-fields'),
    		'menu_icon'           => 'dashicons-networking',
    
    ));

}
add_action('init', 'my_custom_init');



// Gestion des Posts Clubs 
// =============================
add_action( 'add_meta_boxes', 'add_events_metaboxes' ); // declré à la creatino du post
// Add the Events Meta Boxes
function add_events_metaboxes() {
	$prefix = 'club_';
	$post_type = 'club';
	add_meta_box($prefix.'TennisDefi_metabox',
	 'Metadata du Club', 'display_club_metabox', $post_type, 'normal', 'high');

}
//Affichage
function display_club_metabox() {
	global $post;

	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="clubmeta_noncename" id="clubmeta_noncename" value="' .
			wp_create_nonce( 'page_edit_club_meta' ) . '" />';

	// Get the location data if its already been entered
	$nbJoueur = get_post_meta($post->ID, TENNISDEIF_XPROFILE_nbJoueursClub, true);
	$adresse  = get_post_meta($post->ID, TENNISDEIF_XPROFILE_club_adresse, true);
	$adresse2= get_post_meta($post->ID, TENNISDEIF_XPROFILE_club_adresse2, true);
	$cp= get_post_meta($post->ID, TENNISDEIF_XPROFILE_club_cp, true);
	$ville= get_post_meta($post->ID, TENNISDEIF_XPROFILE_club_ville, true);
	$dpt= get_post_meta($post->ID, TENNISDEIF_XPROFILE_club_dpt, true);
	$latitude= get_post_meta($post->ID, TENNISDEIF_XPROFILE_club_latitude, true);
	$longitude= get_post_meta($post->ID, TENNISDEIF_XPROFILE_club_longitude, true);
	// Echo out the field
	echo 'Nb joueurs:  <input type="text" name="'.TENNISDEIF_XPROFILE_nbJoueursClub.'" value="' . $nbJoueur  . '" /> <br>';

	echo 'Nb adresse:  <input type="text" name="'.TENNISDEIF_XPROFILE_club_adresse.'" value="' . $adresse  . '" /> <br>';
	echo 'adresse2:  <input type="text" name="'.TENNISDEIF_XPROFILE_club_adresse2.'" value="' . $adresse2  . '" /> <br>';
	echo 'cp:  <input type="text" name="'.TENNISDEIF_XPROFILE_club_cp.'" value="' . $cp  . '" /> <br>';
	echo 'ville:  <input type="text" name="'.TENNISDEIF_XPROFILE_club_ville.'" value="' . $ville  . '" /> <br>';
	echo 'dpt:  <input type="text" name="'.TENNISDEIF_XPROFILE_club_dpt.'" value="' . $dpt  . '" /> <br>';
	echo 'latitude:  <input type="text" name="'.TENNISDEIF_XPROFILE_club_latitude.'" value="' . $latitude  . '" /> <br>';
	echo 'longitude:  <input type="text" name="'.TENNISDEIF_XPROFILE_club_longitude.'" value="' . $longitude  . '" /> <br>';
	
}

// Save the Metabox Data

function wpt_save_club_meta($post_id, $post) {
	
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if(!isset($_POST['clubmeta_noncename']))
		return;	
	
	if ( !wp_verify_nonce( $_POST['clubmeta_noncename'], 'page_edit_club_meta' )) {
		return $post->ID;
	}

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID )){
		return $post->ID;
	}

	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.

	$page_meta_list[TENNISDEIF_XPROFILE_nbJoueursClub] 	= $_POST[TENNISDEIF_XPROFILE_nbJoueursClub];
	$page_meta_list[TENNISDEIF_XPROFILE_club_adresse] 	= $_POST[TENNISDEIF_XPROFILE_club_adresse];
	$page_meta_list[TENNISDEIF_XPROFILE_club_adresse2] 	= $_POST[TENNISDEIF_XPROFILE_club_adresse2];
	$page_meta_list[TENNISDEIF_XPROFILE_club_cp] 		= $_POST[TENNISDEIF_XPROFILE_club_cp];
	$page_meta_list[TENNISDEIF_XPROFILE_club_ville] 	= $_POST[TENNISDEIF_XPROFILE_club_ville];
	$page_meta_list[TENNISDEIF_XPROFILE_club_dpt] 		= $_POST[TENNISDEIF_XPROFILE_club_dpt];
	$page_meta_list[TENNISDEIF_XPROFILE_club_latitude] 	= $_POST[TENNISDEIF_XPROFILE_club_latitude];
	$page_meta_list[TENNISDEIF_XPROFILE_club_longitude] = $_POST[TENNISDEIF_XPROFILE_club_longitude];


	// Add values of $events_meta as custom fields

	foreach ($page_meta_list as $key => $value) { // Cycle through the metadataList array!
		write_log("On trite la clé : $key avec la valeur $value");
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice
		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
			add_post_meta($post->ID, $key, $value);
		}

	}
}

add_action('save_post', 'wpt_save_club_meta', 1, 2); // save the custom fields


// ========================
/*! \brief Gestion des Metadata Xprofile Buddypress : Profession, telephone, REcherche de partenaire
    Liste de define creé automatiquement par appel unique de add_custom_xprofile_Tennisdefi_fields()
    \todoVerif  : Appeler cette focntion une fois (page acceui par exemple puis supprimer l'appel et copier coller les DEFINE crées et affichés dans Debug.log
*/
// =========================


// Partie fixe (ne pas modifier)
// User_metadata
define("TENNISDEIF_XPROFILE_dateinscri",    "dateinscri");
define("TENNISDEIF_XPROFILE_nbdefilance",   "nbdefilance");
define("TENNISDEIF_XPROFILE_idClub",        "tennisdefi_idClub"); // club actif du joueurs parmi les clubs
define("TENNISDEIF_XPROFILE_idclubs",       "tennisdefi_clubs");
define("TENNISDEIF_XPROFILE_fraudeList",       "tennisdefi_emailsFraudeList");

// Post Palmares (donnees par club)
define("TENNISDEIF_XPROFILE_nbpartenaires",  "Nb_partenaires");
define("TENNISDEIF_XPROFILE_nbvictoires",   "Nb_victoires");
define("TENNISDEIF_XPROFILE_nbdefaites",    "Nb_defaites");
define("TENNISDEFI_XPROFILE_nbMacth", "tennisdefi_nbMatch");        
define("TENNISDEIF_XPROFILE_rang",          "tennisdefi_rang"); // clubs choisis par le joueur
define("TENNISDEFI_XPROFILE_idjoueur", "tennisdefi_idjoueur");
define("TENNISDEFI_XPROFILE_idAdminInClub", "tennisdefi_isAdminInClub");
define("TENNISDEFI_XPROFILE_lastdeclaration", "tennisdefi_lastdeclaration");
define("TENNISDEFI_TOURNOI_userTournoi", "tennisdefi_tournoi_userTournoi"); // liste destournois auxquels le joueur est inscrits

// GEstion des palmares Mixte, Homme, Femme
define("TENNISDEFI_PALMARES_TYPE_all",    		"tennisdefi_palmares_display_all"); // affiche tous les joueurs du club
define("TENNISDEFI_PALMARES_TYPE_friends",    	"tennisdefi_palmares_display_friends"); // affiche les amis du joueurs
define("TENNISDEFI_PALMARES_TYPE_actif",    		"tennisdefi_palmares_display_actif"); // affiche tous les joueurs du club


define("TENNISDEFI_PALMARES_CAT_MIXTE",    0);
define("TENNISDEFI_PALMARES_CAT_HOMME",    1);
define("TENNISDEFI_PALMARES_CAT_FEMME",    2);
 // Sauvegarde de l'option d'afichage du palamres pour chaque joueur
define("TENNISDEFI_PALMARES_XPROFILE_type",    		"tennisdefi_palmares_xprofile_type"); // affiche tous les joueurs du club
define("TENNISDEFI_PALMARES_XPROFILE_categorie",    	"tennisdefi_palmares_xprofile_categorie"); // affiche les amis du joueurs
//define("TENNISDEFI_PALMARES_XPROFILE_nbligne",    	"tennisdefi_palmares_xprofile_nbligne"); // affiche les amis du joueurs

// TENNISDEIF_XPROFILE_idClub  : aussi utilisé

// Post Club

define("TENNISDEIF_XPROFILE_nbJoueursClub", "tennisdefi_nbJoueurs"); // post club: nombre de joueurs dans le club
define("TENNISDEIF_XPROFILE_club_adresse"     ,'adresse');
define("TENNISDEIF_XPROFILE_club_adresse2",  'adresse2');		
define("TENNISDEIF_XPROFILE_club_cp",  		 'cp');
define("TENNISDEIF_XPROFILE_club_ville",     'ville');
define("TENNISDEIF_XPROFILE_club_dpt",  		'dpt');
define("TENNISDEIF_XPROFILE_club_latitude",  'latitude');
define("TENNISDEIF_XPROFILE_club_longitude",  'longitude');
// deja defini
	//define("TENNISDEFI_XPROFILE_lastdeclaration", "tennisdefi_lastdeclaration");

           
//Post Resutat
define("TENNISDEIF_XPROFILE_matchDeclarePar", "postPar"); // dans le post resultat on sait qui a déclaré le match

define("TENNISDEIF_XPROFILE_idVainqueur", 'tennisdefi_idVainqueur');
define("TENNISDEIF_XPROFILE_idPerdant"  , 'tennisdefi_idPerdant');
define("TENNISDEFI_XPROFILE_matchNul", "match_nul");
// TENNISDEFI_XPROFILE_idjoueur : aussi utilisé

//Post TOURNOI
define("TENNISDEIF_TOURNOI_nomTournoi", "tennisdefi_tournoiNom"); //  nom du tournoi
define("TENNISDEIF_TOURNOI_isOpen", "tennisdefi_tournoi_isOpen"); //  tous les joueurs peuvent s'y inscrire?
define("TENNISDEIF_TOURNOI_description", "tennisdefi_tournoi_description"); //  description


define("TENNISDEIF_TOURNOI_isActif", "tennisdefi_tournoi_isActif"); //  array  des jouuers dans le club
define("TENNISDEIF_TOURNOI_isVisible", "tennisdefi_tournoi_isVisible"); //  array  des jouuers dans le club
define("TENNISDEIF_TOURNOI_usersArray", "tennisdefi_usersArray"); //  array  des jouuers dans le club
define("TENNISDEIF_TOURNOI_usersArrayClassement", "tennisdefi_usersArrayClassement"); // // Classement des joueur à la creation {'key_idJoueur" => classement}  
define("TENNISDEIF_TOURNOI_usersArrayLastClassement", "tennisdefi_usersArrayLastClassement");// Dernière actualisation du Classement des joueur  {'key_idJoueur" => classement}  



// Partie Generee automatiquement
// ===============================
define("TENNISDEIF_XPROFILE_telephone", 4); /// odoVerif Doit etre verifié a chaque création de site.
define("TENNISDEIF_XPROFILE_adresse", 5); /// odoVerif Doit etre verifié a chaque création de site.
define("TENNISDEIF_XPROFILE_codepostal", 6); /// odoVerif Doit etre verifié a chaque création de site.
define("TENNISDEIF_XPROFILE_ville", 7); /// odoVerif Doit etre verifié a chaque création de site.
define("TENNISDEIF_XPROFILE_datenaissance", 8); /// odoVerif Doit etre verifié a chaque création de site.
define("TENNISDEIF_XPROFILE_metier", 9); /// odoVerif Doit etre verifié a chaque création de site.
define("TENNISDEIF_XPROFILE_emploi", 10); /// odoVerif Doit etre verifié a chaque création de site.
define("TENNISDEIF_XPROFILE_classement", 24); /// odoVerif Doit etre verifié a chaque création de site.
define("TENNISDEIF_XPROFILE_exclassement", 25); /// odoVerif Doit etre verifié a chaque création de site.
define("TENNISDEIF_XPROFILE_rechjoueur", 74); /// odoVerif Doit etre verifié a chaque création de site.

// Update au 2 Mai 2015 : jout du sexe 
define("TENNISDEIF_XPROFILE_sexe", 176); /// odoVerif Doit etre verifié a chaque création de site.



//==================================
/*! \brief Gestion des Metadata Xprofile Buddypress : Profession, telephone, REcherche de partenaire
    \attention Cette fonction doit etre appelée qu'une seule fois par création de site. Pas de do_action utilisé.
    \todoVerif  : Appeler cette focntion une fois (page acceui par exemple puis supprimer l'appel et copier coller les DEFINE crées et afficher dans Debug.log
*/
// =================================
function add_custom_xprofile_Tennisdefi_fields(){

    
    
    echo('*******************************************************');
    echo ('====== DEFINE à coller dans fucntion_post par exemple.');
    
    
    // sutpression d'ancien groupe
    for($k=3;$k<200;$k++){
        $statut = xprofile_delete_field_group( $k );
        //echo " suppression ID= $k : $statut <br>";
    }

    
    
    // Ajout du Groupe
$args = array(
		'name'           => 'Informations personnelles',
		'description'    => '',
		'can_delete'     => true
	);

$ID_groupe = xprofile_insert_field_group( $args) ;

     // type de champs : //'datebox';
                        //'datebox'       
                        //'multiselectbox'
                        //'number'        
                        //'url'           
                        //'radio'         
                        //'selectbox'     
                        //'textarea'      
                        //'textbox'      
        
        
  if(    $ID_groupe){  
        $field_group_id = $ID_groupe;
        $can_delete = 0;
		$parent_id = 0;		
   	    $description = '';
        $is_required = 0;
/*// Champs WP
            'nbpartenaires', \todo verifeir que c'est pris en compte
            dateinscri', \todo verifeir que c'est pris en compte
            nbmatchs',   \todo verifeir que c'est pris en compte
            nbdefaites', \todo verifeir que c'est pris en compte
            nbvictoires',/todo verifeir que c'est pris en compte
             nbnuls',
            nbpartenaires',\todo verifeir que c'est pris en compte
            nbdefilance',\todo verifeir que c'est pris en compte
            nbdefiaccepte', \todo verifeir que c'est pris en compte
            nbdefirefuse',\todo verifeir que c'est pris en compte
            topjoueur',
  */        
  // Champ BuddyPress 
  
    
    // Téléphone
    // ================
        $type = 'number';  /// \todo : gérer à l'importztion les 2 tel possibles.
        $name = "Téléphone";
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));
		$field_id = xprofile_insert_field( $data );
   if( $field_id)
       
        echo("define(\"TENNISDEIF_XPROFILE_telephone\", $field_id); /// \todoVerif Doit etre verifié a chaque création de site.");
    else
        echo(" Erreur creation field $name");
            
     
    
    //  adresse
    // ================
        $type = 'textbox';
        $name = "Adresse";
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));
      
		$field_id = xprofile_insert_field( $data );
   if( $field_id) 
    echo("define(\"TENNISDEIF_XPROFILE_adresse\", $field_id); /// \todoVerif Doit etre verifié a chaque création de site.");
       else
        echo(" Erreur creation field $name");
    
    // CP 
        $type = 'textbox';
  		$name = 'Code postal';
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));       
		$field_id = xprofile_insert_field( $data );
    if( $field_id)
    echo("define(\"TENNISDEIF_XPROFILE_codepostal\", $field_id); /// \todoVerif Doit etre verifié a chaque création de site.");
       else
        echo(" Erreur creation field $name");
    
        // ville
        $type = 'textbox';
  		$name = 'Ville';
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));       
		$field_id = xprofile_insert_field( $data );
    if( $field_id)
    	echo("define(\"TENNISDEIF_XPROFILE_ville\", $field_id); /// \todoVerif Doit etre verifié a chaque création de site.");
       else
        echo(" Erreur creation field $name");
    
    // Date de naissance
    //======================
         $type = 'datebox';
        $name = 'Date de naissance';
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));       
		$field_id = xprofile_insert_field( $data );
    if( $field_id)
    echo("define(\"TENNISDEIF_XPROFILE_datenaissance\", $field_id); /// \todoVerif Doit etre verifié a chaque création de site.");
       else
        echo(" Erreur creation field $name");
    
    // Métier
    // ===============
        $type = 'textbox';
  		$name = 'Métier';
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));       
		$field_id = xprofile_insert_field( $data );
                  
    if( $field_id)
    	echo("define(\"TENNISDEIF_XPROFILE_metier\", $field_id); /// \todoVerif Doit etre verifié a chaque création de site.");
       else
        echo(" Erreur creation field $name");
    // Choix multiple
        // profession => choix multiple
        // ===============================
        $name = 'selectionnez votre emploi';
        $type = 'selectbox';
            
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));
      
        $field_id = xprofile_insert_field( $data );
    if( $field_id)
    echo("define(\"TENNISDEIF_XPROFILE_emploi\", $field_id); /// \todoVerif Doit etre verifié a chaque création de site.");
       else
        echo(" Erreur creation field $name");
    
    
        if($field_id) {
                    // Ajout des choix (-1 par rapport 
                    $Professions  = array(
                            "Chef d'entreprise (PDG,DG,GÈrant)",
                            "Directeur, chef de service",
                            "Ingénieur, cadre, chef de projet",
                            "Profession libérale ",
                            "Enseignant",
                            "Etudiant",
                            "Lycéens",
                            "CollÈgiens",
                            "Artisans",
                            "Commercant",
                            "Technicien, Agent de maitrise, Employé",
                            "Retraité",
                            "Autre ou non renseigné");

                        foreach (  $Professions as $frof ) {

                            xprofile_insert_field( array(
                                'field_group_id'	=> $field_group_id,
                                'parent_id'		=> $field_id,
                                'type'			=> 'option',
                                'name'			=> $frof,
                            ));

                        }
        }// fin  creation champ
    
    
    
    // Classements (Actuel et Meilleur)
        $name = 'classement actuel';
        $type = 'selectbox';
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));
      
        $field_id_1= xprofile_insert_field( $data );
    if( $field_id_1)              
    echo("define(\"TENNISDEIF_XPROFILE_classement\", $field_id_1); /// \todoVerif Doit etre verifié a chaque création de site.");
       else
        echo(" Erreur creation field $name");
        $name = 'meilleurs classement';
        $type = 'selectbox';
            
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));
      
        $field_id_2= xprofile_insert_field( $data );
    if( $field_id)              
    echo("define(\"TENNISDEIF_XPROFILE_exclassement\", $field_id_2); /// \todoVerif Doit etre verifié a chaque création de site.");
       else
        echo(" Erreur creation field $name");
    
        if($field_id_1) {
                    // Ajout des choix (-1 par rapport à la base de tennisdefi
                    $Professions  = array('NC','40','30/5','30/4','30/3','30/2','30/1','30','15/5','15/4','15/3','15/2','15/1','15',	'5/6',	'4/6',	'3/6',	'2/6',	'1/6','0','-2/6','-4/6','-15','-30');

                        foreach (  $Professions as $frof ) {

                            xprofile_insert_field( array(
                                'field_group_id'	=> $field_group_id,
                                'parent_id'		=> $field_id_1,
                                'type'			=> 'option',
                                'name'			=> $frof,
                            ));
                            
                             xprofile_insert_field( array(
                                'field_group_id'	=> $field_group_id,
                                'parent_id'		=> $field_id_2,
                                'type'			=> 'option',
                                'name'			=> $frof,
                            ));

                        }//boucle options
            
            
            //recherche partenaire
            //-----------------------
        $type =  'radio'; 
        $name = "recherche plus partenaires ?";
        $description = "";
		$data = compact(array( 'field_group_id', 'parent_id', 'type', 'name', 'description', 'is_required', 'can_delete'));
		$field_id = xprofile_insert_field( $data );
   if( $field_id){
   	echo("define(\"TENNISDEIF_XPROFILE_rechjoueur\", $field_id); /// \todoVerif Doit etre verifié a chaque création de site.");
       
       xprofile_insert_field( array(
                                'field_group_id'	=> $field_group_id,
                                'parent_id'		=> $field_id,
                                'type'			=> 'option',
                                'name'			=> 'oui',
                            ));
                            
                             xprofile_insert_field( array(
                                'field_group_id'	=> $field_group_id,
                                'parent_id'		=> $field_id,
                                'type'			=> 'option',
                                'name'			=> 'non',
                            ));
   }
    else
    {
        echo(" Erreur creation field $name");
    }// fin creation recherche partenaire
            
            
        }// fin  creation champ classement et exclmseemnt 
    
      
  }//fin $ID_groupe
echo('*******************************************************');
    
}