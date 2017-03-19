<?php

/*
 * ! \file
 * \brief Contient les fonctions Gérants l'affiliation à un club , la désinscription...
 *
 * Details.
 */

// =========================================
/* SESSION !!!!
 * http://christianelagace.com/wordpress/utiliser-des-variables-de-session-dans-wordpress/
 * http://konrness.com/php5/how-to-prevent-blocking-php-requests/
 * 
 */

add_action('init', 'tennisdefi_session_start', 1);
function tennisdefi_session_start() {
	if ( ! session_id() ) {
		@session_start();
	}
}


add_action('wp_logout', 'tennisdefi_detruire_toutes_variables_session');

function tennisdefi_detruire_toutes_variables_session() {
	if ( isset( $_COOKIE[session_name()] ) ) {
		session_unset();   // détruit les variables de session
		session_destroy();
	}
}




// ========================================
/* ! \brief Statististiques du club  (A COMPLETER)*/
// =========================================
function get_userCLubs_stat($id_club) {
	$argsPalmares = array (
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $id_club
					),
			),
			'post_type' => 'palmares'
	);
	$postPalmares = get_posts ( $argsPalmares );
	$nb_partenaires = 0;
	$nb_victoires   = 0;
	$nb_defaites    = 0;
	$nb_match       = 0;
	$nb_user_compta = 0;
	
	
	foreach ($postPalmares as $post){
		$nb_match_temp = get_post_meta($post->ID, tennisdefi_nbMatch, true);
		if($nb_match_temp>0){
			$nb_match  += $nb_match_temp;
			$nb_user_compta ++;
		}
	}
	
	
}




// ========================================
/* ! \brief Retourne l'ID de la ligne palmares pour un user dans son club */
// =========================================
function getUserPalmaresID($id_user, $id_club) {

$argsPalmares = array (
		'meta_query' => array (
				array (
						'key' => TENNISDEIF_XPROFILE_idClub,
						'value' => $id_club
				),
				array (
						'key' => TENNISDEFI_XPROFILE_idjoueur,
						'value' => $id_user
				)
		),
		'post_type' => 'palmares'
);
$postPalmares = get_posts ( $argsPalmares );

if(count($postPalmares) >0)
	return $postPalmares[0]->ID;
	else 
		return -1;
}

// ========================================
/* ! \brief Retourne si le joueur estADMIN  dans son club */
// =========================================
function isUserAdminInClub($id_user, $id_club) {
	$ID_palmares = getUserPalmaresID($id_user, $id_club);
	$isAdminInClub = (int)get_post_meta ( $ID_palmares, TENNISDEFI_XPROFILE_idAdminInClub, true );

	return 	$isAdminInClub;	
}

// ========================================
/* ! \brief Retourne le rang du joueur dans son club */
// =========================================
function getUserRang($id_user, $id_club) {
	$args = array (
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $id_club 
					),
					array (
							'key' => TENNISDEFI_XPROFILE_idjoueur,
							'value' => $id_user 
					) 
			),
			'post_type' => 'palmares' 
	);
	$postPalmares = get_posts ( $args );
	
	$rang = get_post_meta ( $postPalmares [0]->ID, TENNISDEIF_XPROFILE_rang, true );
	return $rang;
}

// ========================================
/* ! \brief Retourne le rang du joueur dans son club */
// =========================================
function getCurrentUserRang() {
	global $current_user;
	
	$current_club = get_user_meta ( $current_user->ID, TENNISDEIF_XPROFILE_idClub, true );
	$args = array (
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $current_club 
					),
					array (
							'key' => TENNISDEFI_XPROFILE_idjoueur,
							'value' => $current_user->ID 
					) 
			),
			'post_type' => 'palmares' 
	);
	$postPalmares = get_posts ( $args );
	
	$rang = get_post_meta ( $postPalmares [0]->ID, TENNISDEIF_XPROFILE_rang, true );
	return $rang;
}

// ========================================
/* ! \brief Attache un club à un joueur */
// =========================================
function addUserToClub($id_joueur, $id_club_select) {
	$retun_resulat = array (
			"erreur" => false,
			"txt" => "Une erreur est survenue. Merci de contacter l'administrateur du site" 
	);
	
	// delete_user_meta($id_joueur,TENNISDEIF_XPROFILE_idclubs );
	
	$resultat = true;
	
	// Club courrant = club selectionne
	$current_club = get_user_meta ( $id_joueur, TENNISDEIF_XPROFILE_idClub, true );
	if ($id_club_select != $current_club) {
		// echo "club selectionne devient le club courant<br>";
		if (! update_user_meta ( $id_joueur, TENNISDEIF_XPROFILE_idClub, $id_club_select )) {
			$retun_resulat ['erreur'] = true;
			// echo "erreur pour la selection du club courant";
		}
	}  // fin $id_club_select != $current_club
else {
		$retun_resulat ['erreur'] = true;
		$retun_resulat ['txt'] = "Le club etait deja selectionné (normale?)<br>";
		// echo "Le club etait deja selectionné (normale?)<br>"; //Ceci n'est pas vu par l'opératuer
	}
	// echo "current_club = $current_club et id_club_select=$id_club_select<br>";
	
	// echo "ID jouer = $id_joueur et ID club = $id_club_select<br> ";
	// Ajout du club à la liste des clubs du joueur
	$user_clubs = get_user_meta ( $id_joueur, TENNISDEIF_XPROFILE_idclubs, true );
	if (empty ( $user_clubs )) { // Pas encore de club
	                        // echo "Init , on ajoute le club<br>";
		$user_clubs = array (
				$id_club_select 
		);
		if (! update_user_meta ( $id_joueur, TENNISDEIF_XPROFILE_idclubs, $user_clubs ))
			$retun_resulat ['erreur'] = true;
	} else if (in_array ( $id_club_select, $user_clubs )) {
		// echo "le club est déja ataché<br>";
		$retun_resulat ['erreur'] = true;
		$retun_resulat ['txt'] = "Vous appartenez déja à ce club.";
	} else {
		// echo "on ajoute le club à la liste<br>";
		array_push ( $user_clubs, $id_club_select );
		if (! update_user_meta ( $id_joueur, TENNISDEIF_XPROFILE_idclubs, $user_clubs ))
			$retun_resulat ['erreur'] = true;
	}
	
	if (! $retun_resulat ['erreur']) {
		
		// Ajout Palmares
		$post = array (
				'post_title' => 'palmares',
				'post_status' => 'publish', // Choose: publish, preview, future, etc.
				'post_type' => 'palmares' 
		);
		$post_ID = wp_insert_post ( $post ); // Pass the value of $post to WordPress the insert function
		if ($post_ID) {
			// echo "creation post plamresOK<br>";
			// Club + Joueur + Rang +Stat
			update_post_meta ( $post_ID, TENNISDEIF_XPROFILE_idClub, $id_club_select );
			update_post_meta ( $post_ID, TENNISDEFI_XPROFILE_idjoueur, $id_joueur );
			
			$nb_joueur = ( int ) get_post_meta ( $id_club_select, TENNISDEIF_XPROFILE_nbJoueursClub, true ) + 1;
			update_post_meta ( $post_ID, TENNISDEIF_XPROFILE_rang, $nb_joueur );
			
			// mise à jour stat
			update_post_meta ( $post_ID, TENNISDEIF_XPROFILE_nbdefilance, 0 );
			update_post_meta ( $post_ID, TENNISDEIF_XPROFILE_nbpartenaires, 0 );
			update_post_meta ( $post_ID, TENNISDEIF_XPROFILE_nbvictoires, 0 );
			update_post_meta ( $post_ID, TENNISDEIF_XPROFILE_nbdefaites, 0 );
			
			// ajout d'un utilisateur au club
			update_post_meta ( $id_club_select, TENNISDEIF_XPROFILE_nbJoueursClub, $nb_joueur );
		
		
			//Informer les administrateur de club 
			tennisdefi_alerterAdminCLub_newUser($id_joueur, $id_club_select);
			
			
		
		} // fin post palmares
	} // fin si rattachement club OK
	return $retun_resulat;
} // fin fonction
  
// ========================================
/* ! \brief Supprime un club à un joueur */
// =========================================
function removeUserToClub($id_joueur, $id_club_select) {
	$retun_resulat = array (
			"erreur" => false,
			"txt" => "Une erreur est survenue. Merci de contacter l'administrateur du site" 
	);
	
	// echo "id=$id_joueur et id_club = $id_club_select<br>";
	$user_clubs = get_user_meta ( $id_joueur, TENNISDEIF_XPROFILE_idclubs, true );
	$current_club = get_user_meta ( $id_joueur, TENNISDEIF_XPROFILE_idClub, true );
	
	// Le club à supp doit etre dans la liste des clubs du joueur
	if (! in_array ( $id_club_select, $user_clubs )) {
		echo "le club n'etait pas ataché<br>";
		$retun_resulat ['erreur'] = true;
		$retun_resulat ['txt'] = "Erreur: Vous ne pouvez pas retirer ce club de votre liste";
		return $retun_resulat;
	}
	
	// Le joueur ne peut supprimer son club principale
	/*
	 * if(count($user_clubs)<=1){
	 * $retun_resulat['erreur'] = true;
	 * $retun_resulat['txt'] = "Vous ne pouvez pas retirer votre club principal";
	 *
	 * return $retun_resulat;
	 * }
	 */
	 
	 
	 write_log("SUPPRESSION du Joueur : $id_joueur");
	 write_log("==================================");
	//Suppression des matchs associé
	// ------------------------------
	// Defaites
	$args = array ('posts_per_page' => -1,
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $id_club_select
					),
					array (
							'key' => TENNISDEIF_XPROFILE_idPerdant,
							'value' => $id_joueur,
					)
			),
			'post_type' => 'resultats'
	);
	$resultats = get_posts ( $args );
	write_log("SUPPRESSION de ".count($resultats)." defaites");
	foreach ( $resultats as $match ) {
		wp_delete_post( $match->ID, true );
		}
	
	// Victoire
		$args = array ('posts_per_page' => -1,
				'meta_query' => array (
						array (
								'key' => TENNISDEIF_XPROFILE_idClub,
								'value' => $id_club_select
						),
						array (
								'key' => TENNISDEIF_XPROFILE_idVainqueur,
								'value' => $id_joueur,
						)
				),
				'post_type' => 'resultats'
		);
		$resultats = get_posts ( $args );
		write_log("SUPPRESSION de ".count($resultats)." victories");
		foreach ( $resultats as $match ) {
			wp_delete_post( $match->ID, true );
		}
	
	// Mise à jour du palmares
	//------------------------------
	
	// echo "mise à jour rang<br>";
	$rang = getUserRang ( $id_joueur, $id_club_select );
	
	$args = array ('posts_per_page' => -1,
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $id_club_select 
					),
					array (
							'key' => TENNISDEIF_XPROFILE_rang,
							'value' => $rang,
							'compare' => '>=',
							'type' => 'NUMERIC'
					) 
			),
			'post_type' => 'palmares' 
	);
	$palmares = get_posts ( $args );
	write_log("Update de ".count($palmares)." lignes du palmares");
	foreach ( $palmares as $ligne_palmares ) {
		$previous_rang = get_post_meta ( $ligne_palmares->ID, TENNISDEIF_XPROFILE_rang, true );
		update_post_meta ( $ligne_palmares->ID, TENNISDEIF_XPROFILE_rang, $previous_rang - 1 );
		write_log("rang $previous_rang =>  ".($previous_rang-1));
	
	}
	
	// Suppression lignes du palamres
	// echo "suppression ligne palmares<br>";
	$args = array ('posts_per_page' => -1,
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $id_club_select 
					),
					array (
							'key' => TENNISDEFI_XPROFILE_idjoueur,
							'value' => $id_joueur 
					) 
			),
			'post_type' => 'palmares' 
	);
	$postPalmares = get_posts ( $args );
	write_log("Suppression de la ligne du palmares du club pour ce joueur (".count($postPalmares)." ligne(s)) ");
	wp_delete_post ( $postPalmares [0]->ID, true );
	
	// Diminution du nombre de joueur dans le club par 1
	$nb_joueur = ( int ) get_post_meta ( $id_club_select, TENNISDEIF_XPROFILE_nbJoueursClub, true ) - 1;
	update_post_meta ( $id_club_select, TENNISDEIF_XPROFILE_nbJoueursClub, $nb_joueur );
	write_log("Diminution du nombre de joueur dans le club par 1");
	
	// Suppresion du club dans la liste
	// echo"<pre>"; print_r($user_clubs); echo"</pre>";
	$pos = array_search ( $id_club_select, $user_clubs );
	// echo 'CLub found at: ' . $pos;
	unset ( $user_clubs [$pos] );
	update_user_meta ( $id_joueur, TENNISDEIF_XPROFILE_idclubs, $user_clubs );
	write_log("Suppresion du club dans la liste des clubs du joueurs");
	// echo"<pre>"; print_r($user_clubs); echo"</pre>";
	
	// retrait du club courrant ou de tout club si il n'y en a plus
	if(count($user_clubs)){
		write_log("Il reste au joueur : ".count($user_clubs)."Club(s)");
		
		if ($current_club == $id_club_select) {
			update_user_meta ( $id_joueur, TENNISDEIF_XPROFILE_idClub, $user_clubs [0] );
		}
	}
	else{
		write_log("Le joueur n'a plus de club");
		delete_user_meta( $id_joueur, TENNISDEIF_XPROFILE_idClub);
	}	
	
	// Prevenir les responsable du club
	tennisdefi_alerterAdminCLub_newUser($id_joueur, $id_club_select,false);
		
	
	return $retun_resulat;
}//fin fonction



// ========================================
/* ! \brief Suppression d'un joueur depuis Wprdpress= REtire le joueurs de tous ses clubs , supprime ses stat , puis suppression pure et simple du joueur*/
// =========================================
// ====================
function my_delete_user( $user_id ) {
	global $wpdb;
	
	$user_IDclubs = get_user_meta ( $user_id, TENNISDEIF_XPROFILE_idclubs, true );

	// suppression du joueurs dans chacun de ces club
	foreach($user_IDclubs as $id_club_select){
		removeUserToClub($user_id, $id_club_select);
	}

}
add_action( 'delete_user', 'my_delete_user' );



// ========================================
/* ! \brief PErmet d'associer les match d'un joueur à un autre */
// =========================================

function TennisDefi_MatchIDreplace($oldID, $newId, $clubId) {


	// Defaites
	$args = array ('posts_per_page' => -1,
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $clubId
					),
					array (
							'key' => TENNISDEIF_XPROFILE_idPerdant,
							'value' => $oldID,
					)
			),
			'post_type' => 'resultats'
	);
	$resultats = get_posts ( $args );
	write_log("UPdate de ".count($resultats)." defaites");
	foreach ( $resultats as $match ) {
		update_post_meta($match->ID, TENNISDEIF_XPROFILE_idPerdant, $newId);
	}

	// Victoire
	$args = array ('posts_per_page' => -1,
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $clubId
					),
					array (
							'key' => TENNISDEIF_XPROFILE_idVainqueur,
							'value' => $oldID,
					)
			),
			'post_type' => 'resultats'
	);
	$resultats = get_posts ( $args );
	write_log("UPdate  de ".count($resultats)." victories");
	foreach ( $resultats as $match ) {
		update_post_meta($match->ID, TENNISDEIF_XPROFILE_idVainqueur, $newId);

	}

	// Mise à jour des STAT Nouveau
	$palmaresID =  getUserPalmaresID($newId, $clubId);
	$statistiques = getjoueur_STATS_byClub($clubId, $newId);
	update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbpartenaires	, $statistiques['NBpartenaires']);
	update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbvictoires 	, $statistiques['NBvictoires']);
	update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbdefaites 	, $statistiques['nbdefaites']);
	update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbmatcheNuls 	, $statistiques['NBpartenaires']);
	update_post_meta($palmaresID, TENNISDEFI_XPROFILE_nbMacth 		, $statistiques['NBmatch']);
		
	// Mise à jour des STAT Ancien joueur
	$palmaresID =  getUserPalmaresID($oldID, $clubId);
	$statistiques = getjoueur_STATS_byClub($clubId, $oldID);
	update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbpartenaires	, $statistiques['NBpartenaires']);
	update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbvictoires 	, $statistiques['NBvictoires']);
	update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbdefaites 	, $statistiques['nbdefaites']);
	update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbmatcheNuls 	, $statistiques['NBpartenaires']);
	update_post_meta($palmaresID, TENNISDEFI_XPROFILE_nbMacth 		, $statistiques['NBmatch']);
	

}



