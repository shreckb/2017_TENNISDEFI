<?php

/*
 * ! \file
 * \brief Contient les fonctions de gestions d'affichage coté admin Panel
 */

// PAGE 2 ---- GEstion des USER, admin de Cub, REcopie de match d'un joueur à l'autre 


// Ajout du Script
function register_TennisDefi_pageAdminClub_JSfile() {
	wp_register_script ( 'custum_tennisdefi_adminpanel_JSscript', get_stylesheet_directory_uri () . '/js/Tennisdefi_adminPanel/page_adminUserClub.js'  );
	wp_enqueue_script ( 'select2_script', 		'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js' );
	wp_enqueue_style  ('select2_css'    , 		'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css');
	
}
add_action ( 'admin_init', 'register_TennisDefi_pageAdminClub_JSfile' );

function add_TennisDefi_pageAdminClub_JSfile($hook) {
	//write_log("****************");
	//wp_enqueue_script ( 'custum_tennisdefi_adminpanel_JSscript', get_stylesheet_directory_uri () . '/js/Tennisdefi_adminPanel/page_adminUserClub.js' );

	global $TENNISDEFI_hook_to_menu_pageAdminGEstionClub; // defini dans plus haut
	if ($hook != $TENNISDEFI_hook_to_menu_pageAdminGEstionClub)
		return;
	wp_enqueue_script ( 'custum_tennisdefi_adminpanel_JSscript', array (
			'jquery'
	), array (
			'jquery'
	) );
	
	wp_enqueue_script ( 'select2_script', array ('jquery'), array ('jquery') );

	
	
	enqueue_script_Lib_DataTable ();
}
add_action ( 'admin_enqueue_scripts', 'add_TennisDefi_pageAdminClub_JSfile' );



function TennisDefi_menu_pageAdminClub(){



	echo "<h4> liste des admin de club</h4>";
	$args = array (
			'meta_query' => array (
					array ('key' => TENNISDEFI_XPROFILE_idAdminInClub,'value' => 1),
			),
			'post_type' => 'palmares'
	);
	$postPalmares = get_posts ( $args );


	echo "<table>";
	echo "<tr><td>Club </td><td>User</td></tr>";
	foreach($postPalmares as $palmares_ligne){
		$Id_club = (int)get_post_meta ( $palmares_ligne->ID, TENNISDEIF_XPROFILE_idClub, true );
		$Id_user = (int)get_post_meta ( $palmares_ligne->ID, TENNISDEFI_XPROFILE_idjoueur     , true );

		$user_info = get_userdata($Id_user);
		$nom_club = get_post($Id_club)->post_title;

		$nom    =  $user_info->user_lastname;
		$prenom =  $user_info->user_firstname;


		echo "<tr><td>$nom_club(ID=$Id_club)</td><td>$nom $prenom (Id=$Id_user)</td></tr>";
	}

	echo "</table>";


	echo "<h4> voir cette page pour ajouter des admin(function_adminPanel_gestionUser.php)</h4>";
	echo "obtenir ID joueur et ID club<br>";
	// Test pour Admin dans son club
	//==================================
	$ID_joueur = 4425;//Diego +Garcia //324;  //Mel : ID = 324 Francis: 4
	$ID_club   = 11074;  // TCL Jeune //9621;  //club de test :24272    tcl =9621

	$ID_palmares = getUserPalmaresID($ID_joueur, $ID_club);
	if($ID_palmares>0)
		update_post_meta($ID_palmares, TENNISDEFI_XPROFILE_idAdminInClub , 1);
	//$isAdminInClub = get_post_meta ( $ID_palmares, TENNISDEFI_XPROFILE_idAdminInClub, true );
	else
		echo "Erreur , pas de palmares trouvé<br>";

	$isAdminInCLub = isUserAdminInClub($ID_joueur, $ID_club);
	$user_info = get_userdata($ID_joueur);
	$nom_club = get_post($ID_club)->post_title;


	echo "User(ID=$ID_joueur, IDpalamre=$ID_palmares)".$user_info->user_lastname." ".$user_info->user_firstname." (club : $nom_club)<br>";
	echo "L'utilisateur est admin dasn le club ?<br>";
	if($isAdminInCLub==1)
		echo " IsAdmin : OUI <br>";
	else
		echo "isAdmin: Non<br>";

	//==============================
	// SELECTION un joueur, un club etc....
	echo "<h2> Attribution des droits d'Admin ou retrait(dev en cours)</h2>";

	$args = array(
			'number'       => 0,
			'fields'       => array('display_name','ID' ),
	);
	$users =get_users($args);
	//echo "<pre>";print_r($users); echo"</pre>";
	echo ' <select id="user_list_id">';
	echo "<option value=''>selectionner un joueur </option>";
	foreach($users as $user)
		echo "<option value=\"$user->ID\">$user->display_name </option>";
	echo"</select>";
	 
	echo ' <select id="select_displayUserLcubs">';

	echo "<option value=''>------- </option>";

	echo"</select>";
	 
	 
	 
	 
	//==============================
	// Transfert de résultat
	//==============================
	echo "<h2> Attribution des Resultats d'une personne vers une autre(du meme club)</h2>";
	echo "<b>Ancien Joueur(source)<b> : ";

	$args = array(
			'number'       => 0,
			'fields'       => array('display_name','ID' ),
	);
	$users =get_users($args);
	//echo "<pre>";print_r($users); echo"</pre>";
	echo ' <select id="transfertResultats_JoueurSource" style="width: 300px">';
	echo "<option value=''>selectionner un joueur </option>";
	foreach($users as $user)
		echo "<option value=\"$user->ID\">$user->display_name </option>";

	echo"</select>";
	//Club
	echo "<b>Club</b>";
	echo ' <select id="transfertResultats_JoueurClub" data-placeholder="-------" style="width: 300px">';

	echo"</select>";
	//Joueur Destination
	echo "<br><b>Nouveau Joueur(destination)</b>";
	echo ' <select id="transfertResultats_JoueurDestination" data-placeholder="-------" style="width: 300px">';

	echo"</select>";
	echo"<div id='AJAX_statut'></div>";
	 

}

//***********************************
// AJAX pour la page 2 (GEstion users/admin....)
//***********************************
// REcherche club joueur
//========================
//Actions


// REcherche club joueur
//========================
add_action('wp_ajax_adminPage_getUserCLubsID', 'admin_pannel_getuserCLubsAndPalamresID');


// Recherche des infos jouuer Clubs, Palmares, REsultats...) depuis l'id du joueur
function admin_pannel_getuserCLubsAndPalamresID(){

	//echo "hello from admin_pannel_getuserCLubID";
	$id_user = $_REQUEST['id_user'];
	$clubs = get_user_meta ( $id_user, TENNISDEIF_XPROFILE_idclubs, true );

	//write_log("********* admin_pannel_getuserCLubsAndPalamresID ************ id_user=$id_user\n ");
	$repone_array = [];
	foreach ( $clubs as $club_ID ) {
		$club_titre = get_the_title ( $club_ID );
		$rang = getUserRang($id_user, $club_ID);
		$id_palmares = getUserPalmaresID($id_user, $club_ID);
		$V  = get_post_meta($id_palmares , TENNISDEIF_XPROFILE_nbvictoires 		, true);
		$D  = get_post_meta($id_palmares , TENNISDEIF_XPROFILE_nbdefaites 		, true);
		$MN = get_post_meta($id_palmares , TENNISDEIF_XPROFILE_nbmatcheNuls 	, true);
		$NB = get_post_meta($id_palmares , TENNISDEFI_XPROFILE_nbMacth 			, true);



		$repone_array[] = array('id_club'=>$club_ID,
				'nom_club'=>$club_titre,
				'rang' =>$rang,
				'id_palamres' =>$id_palmares,
				'victoires' 		=>	$V,
				'defaites' 			=>	$D,
				'match_nul' 		=>	$MN,
				'nb_match' 			=>	$NB,
		);
	}

	$response = json_encode( $repone_array );
	echo $response;

	die;

}

// Recherche des infos joueuer Clubs, Palmares, REsultats...) dans un club
add_action('wp_ajax_adminPage_getUsersPalmaresIDfromClub', 'admin_pannel_getusersPalamresID_FromClub');
function admin_pannel_getusersPalamresID_FromClub(){

	//echo "hello from admin_pannel_getuserCLubID"; die;

	$id_club = $_REQUEST['id_club'];
	//write_log("********* admin_pannel_getusersPalamresID_FromClub ************ id_club=$id_club\n ");

	$args = array (
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $id_club
					)
			),
			'post_type' => 'palmares',
			'numberposts' => - 1,
			'meta_key' => TENNISDEIF_XPROFILE_rang,
			'orderby' => 'meta_value_num',
			'order' => 'ASC'
	);
	$postPalmares = get_posts ( $args );


	$repone_array = [];
	foreach ( $postPalmares as $lignePalmares ) {
		$id_joueur = get_post_meta ( $lignePalmares->ID, TENNISDEFI_XPROFILE_idjoueur, true );
		$user = get_userdata ( $id_joueur );
		$rang_currentuser = get_post_meta($lignePalmares->ID , TENNISDEIF_XPROFILE_rang , true);
			

		//get_post_meta($lignePalmares->ID , TENNISDEIF_XPROFILE_nbpartenaires  	,true);
		$V  = get_post_meta($lignePalmares->ID , TENNISDEIF_XPROFILE_nbvictoires 		, true);
		$D  = get_post_meta($lignePalmares->ID , TENNISDEIF_XPROFILE_nbdefaites 		, true);
		$MN = get_post_meta($lignePalmares->ID , TENNISDEIF_XPROFILE_nbmatcheNuls 	, true);
		$NB = get_post_meta($lignePalmares->ID , TENNISDEFI_XPROFILE_nbMacth 			, true);



		$palmares_id = get_the_title ( $club_ID );
		$rang = getUserRang($id_user, $club_ID);
		$id_palmares = getUserPalmaresID($id_user, $club_ID);
		$repone_array[] = array(
				'id_user'		=> 	$id_joueur,
				'displayName'		=>  $user->display_name,
				'rang' 				=>	$rang_currentuser,
				'id_palamres' 		=>	$id_palmares,
				'victoires' 		=>	$V,
				'defaites' 			=>	$D,
				'match_nul' 		=>	$MN,
				'nb_match' 			=>	$NB,
		);
	}

	$response = json_encode( $repone_array );
	echo $response;
	//write_log($repone_array);
	die;

}



// Recherche des infos joueuer Clubs, Palmares, REsultats...) dans un club
add_action('wp_ajax_adminPage_changeREsultatFromJoueurAtoB', 'adminPage_changeREsultatFromJoueurAtoB');
function adminPage_changeREsultatFromJoueurAtoB(){

	//echo "hello from admin_pannel_getuserCLubID"; die;

	$id_club = $_REQUEST['id_club'];
	$id_joueurSource = $_REQUEST['id_joueurSource'];
	$id_joueurDest = $_REQUEST['id_joueurDest'];
	
	// Verification que les joueur existe et qu'il sont différents(à faire)
	if($id_joueurSource == $id_joueurDest){
		echo "NOK : les joeuurs ont le meme ID";
		die;
	}
		
	
	write_log("********* adminPage_changeREsultatFromJoueurAtoB ************ ");
	write_log("id_club=$id_club\n ");
	write_log("id_joueurSource=$id_joueurSource\n ");
	write_log("id_joueurDest=$id_joueurDest\n ");
	
	//TRANSFERT fait ICI
	TennisDefi_MatchIDreplace($id_joueurSource, $id_joueurDest, $id_club);
	
	
	$data_out =  "OK : tout s'est déroulé correctement, rafraichir la page pour voir les changements";
	$response = json_encode( $data_out );
	echo $response;
	die;

}

