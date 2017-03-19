<?php

/*
 * ! \file
 * \brief Contient les fonctions de gestions utilisateurs (combo joueur, deleteUser, analyse resultat du joueurs...)
 *
 * Details.
 */

// ****************************************
// Gestion de fonctions pour l'ajout -supressions de joueur
// ****************************************
require_once ABSPATH . 'wp-admin/includes/user.php';

// ========================================
/*
 * ! \brief Ne pas UTILISER : Permet de supprimer un utilisateur, MàJ des rang du club
 * \todo Quand suppression du jouer, gérer les résultat etc. (Creer 1 seul Identifiant "Joueurs éliminé")
 */
// =========================================
function deleteUser($userID, $user_idclub) {
	echo " Suppression en cours....";
	
	$user_rang = esc_attr ( get_the_author_meta ( 'tennisdefi_rang', $userID ) );
	
	// ajoute au rang des utilisateurs du club -1 entre rangdu gagnant et du perdant
	$args = array (
			'meta_query' => array (
					array (
							'key' => 'tennisdefi_rang',
							'value' => $user_rang,
							'compare' => '>' 
					),
					array (
							'key' => 'tennisdefi_idClub',
							'value' => $user_idclub 
					) 
			) 
	);
	$user_query = new WP_User_Query ( $args );
	foreach ( $user_query->results as $user ) {
		$rang = get_the_author_meta ( 'tennisdefi_rang', $user->ID );
		echo "<br>Modif userID(" . $user->ID . ") : " . $user->user_lastname . " " . $user->user_firstname . " " . $rang . "=>" . ($rang - 1) . "<br>";
		update_user_meta ( $user->ID, 'tennisdefi_rang', $rang - 1 );
	}
	
	// Suppression de l'identitifiant
	if (wp_delete_user ( $userID ))
		return true;
	else
		return false;
	
	// Gestion des résultats enregistrés ?
}


// ========================================
/* ! \brief Nb de match déclaré depuis X mois:
 * compte le nombre de match déclaré depuis X mois
 * Utilisé dans la page palmares_custom
 * */
// =========================================

function count_matchDeclare_at_month_withUser($current_clubID,$current_userID,  $month){
	
	//Victoire ou match nul
	$args = array(
			'meta_query' => array(
					array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_clubID),
					array('key' => TENNISDEIF_XPROFILE_idVainqueur,'value' => $current_userID)
			),
			'date_query' => array (
					array (
							'after' => array (
									'year' => $month['year'],
									'month' => $month['mon'],
									'day' => 1),
							'inclusive' => true
					)),
			'post_type' => 'resultats',
			'numberposts' =>-1,
	);
	$posts  = get_posts( $args );

	$NB = count($posts);
	
	//Defaite ou match nul
	$args = array(
			'meta_query' => array(
					array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_clubID),
					array('key' => TENNISDEIF_XPROFILE_idPerdant,'value' => $current_userID)
			),
			'date_query' => array (
					array (
							'after' => array (
									'year' => $month['year'],
									'month' => $month['mon'],
									'day' => 1),
							'inclusive' => true
					)),
			'post_type' => 'resultats',
			'numberposts' =>-1,
	);
	//$posts  = get_posts( $args );
	
	//$NB += count($posts);
	
	write_log("**********count_matchDeclare_at_month_withUser**************");
	write_log($month);
	write_log($posts);
	write_log("************************************************************");
	
	
	return $NB;

}


// ========================================
/* ! \brief Nb Partenaires : 
 * compte le nombre de partenaires d'un joueur dans un club
 * Utilisé dans la page Gestionnaire de club et 
 * */
// =========================================
function getjoueur_STATS_byClub($user_idclub, $user_ID) {

	// ----------
	$args = array (
			'fields' => 'ids',
			'meta_query' => array (
					'relation' => 'AND',
					array ('key' => TENNISDEIF_XPROFILE_idVainqueur,'value' => $user_ID),
					array ('key' => TENNISDEFI_XPROFILE_matchNul,    'value' => 0),
					array ('key' => TENNISDEIF_XPROFILE_idClub,'value' => $user_idclub)
			),
			'post_type' => 'resultats',
			'numberposts' => -1);
	$victoires_posts = get_posts ( $args );
	$victoires = count ( $victoires_posts );

	// NB defaite
	$args = array ('fields' => 'ids',
			'meta_query' => array (
					'relation' => 'AND',
					array ('key' => TENNISDEIF_XPROFILE_idPerdant,'value' => $user_ID),
					array ('key' => TENNISDEFI_XPROFILE_matchNul,'value' => 0),
					array ('key' => TENNISDEIF_XPROFILE_idClub,'value' => $user_idclub)
			),
			'orderby' => 'date',
			'post_type' => 'resultats',
			'posts_per_page' => 999999
	);
	$defaites_posts = get_posts ( $args );
	$defaites = count ( $defaites_posts );
	
	// NB de match nuls
	$args = array ('fields' => 'ids',
			'meta_query' => array (
					'relation' => 'AND',
					array ('key' => TENNISDEIF_XPROFILE_idVainqueur,'value' => $user_ID),
					array ('key' => TENNISDEFI_XPROFILE_matchNul,'value' => 1),
					array ('key' => TENNISDEIF_XPROFILE_idClub,'value' => $user_idclub)
			),
			'post_type' => 'resultats',
			'posts_per_page' => 999999
	);
	$matchnul_posts1 = get_posts ( $args );
	$nb_matchnuls = count ( $matchnul_posts1 );
	
	$args = array ('fields' => 'ids',
			'meta_query' => array (
					'relation' => 'AND',
					array ('key' => TENNISDEIF_XPROFILE_idPerdant,'value' => $user_ID),
					array ('key' => TENNISDEFI_XPROFILE_matchNul,'value' => 1),
					array ('key' => TENNISDEIF_XPROFILE_idClub,'value' => $user_idclub)
			),
			'post_type' => 'resultats',
			'posts_per_page' => 999999
	);
	$matchnul_posts2 = get_posts ( $args );
	$nb_matchnuls += count ( $matchnul_posts2 );
	
	// Extraction des ID
	$partenaires_ID= array ();
	foreach ( $victoires_posts as $post ) {
		$partenaires_ID [] = get_post_meta ( $post, TENNISDEIF_XPROFILE_idPerdant, true );
	}
	foreach ( $defaites_posts as $post ) {
			$partenaires_ID [] = get_post_meta ( $post, TENNISDEIF_XPROFILE_idVainqueur, true );
		}
	foreach ( $matchnul_posts1 as $post ) {
		$partenaires_ID [] = get_post_meta ( $post, TENNISDEIF_XPROFILE_idPerdant, true );
	}
	foreach ( $matchnul_posts2 as $post ) {
		$partenaires_ID [] = get_post_meta ( $post, TENNISDEIF_XPROFILE_idVainqueur, true );
	}
	
	//-----------

	$users_data = [];
	$NB_match = 0;
	foreach($partenaires_ID as $id_joueur){
		$key_joueur = 'key_' . $id_joueur; // ne doit pas etre un chiffre
		if (! array_key_exists ( $key_joueur, $users_data )) {
			// nouveau parteaire
			$users_data [$key_joueur] = 0;
		}

		// Mise à jour pour tous maintenant qu'on sait que le joueur existe
		$users_data [$key_joueur] ++;
		$NB_match ++;
	}
	$result = array (
			"NBmatchNuls" => $nb_matchnuls,
			"NBvictoires" => $victoires,
			"nbdefaites" => $defaites,
			"NBpartenaires" => count ( $users_data ),
			"NBmatch" => $NB_match 
	);
	return $result;
} // functiong etjoueur


// ========================================
/* ! \brief STATISITIQUES: compte et détails le nombre de parties gagnées/perdues/nules avec chaque adversaire 
 * Utilisé dans la page statistiques
 * */
// =========================================
function getjoueur_stats($posts_match, $users_data, $cat) {
	// $cat = nuls, defaites, victoires
	for($k = 0; $k < count ( $posts_match ); $k ++) {
		$post_match = $posts_match [$k];
		$id_joueur = $post_match ['idAdversaire'];
		$key_joueur = 'key_' . $id_joueur; // ne doit pas etre un chiffre
		if (! array_key_exists ( $key_joueur, $users_data )) {
			// nouveau parteaire
			// echo "creaton de la clé $key_joueur<br>";
			$user = get_userdata ( $id_joueur );
			$users_data [$key_joueur] = array (
					'user_lastname' => $user->user_lastname,
					'user_firstname' => $user->user_firstname,
					'nbmatch' => 0,
					'defaites' => 0,
					'victoires' => 0,
					'nuls' => 0 
			);
		}
		
		// Mise à jour stat
		$users_data [$key_joueur] [$cat] ++;
		$users_data [$key_joueur] ['nbmatch'] ++;
	} // for
	
	return $users_data;
} // functiong etjoueur
  
// ========================================
/* ! \brief simple fonction de classement des joueurs */
// =========================================
function cmp_rang_ASC($a, $b) {
	if ($a->tennisdefi_rang == $b->tennisdefi_rang) {
		return 0;
	}
	return ($a->tennisdefi_rang < $b->tennisdefi_rang) ? - 1 : 1;
}

// ========================================
/* ! \brief retourne le code HTML pour une combobox avec les joueurs du club class par ordre de classement */
// =========================================
function combobox_joueurs($SELECT_name, $user_idclub) {
	$chaine = '<SELECT id='.$SELECT_name.'   name="'.$SELECT_name.'" size="1" style="max-width:90%;">';
	
	$current_user = wp_get_current_user();
	$current_userID = $current_user->ID;
	
	// Requette
	$args = array (
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $user_idclub 
					) 
			),
			'post_type' => 'palmares',
			'meta_key' => TENNISDEIF_XPROFILE_rang,
			'numberposts' => - 1,
			'orderby' => 'meta_value_num',
			'order' => 'ASC' 
	);
	$palmares = get_posts ( $args );
	$tab_joueurs = array ();
	foreach ( $palmares as $ligne_palmares ) {
		$rang = get_post_meta ( $ligne_palmares->ID, TENNISDEIF_XPROFILE_rang, true );
		$id_joueur = get_post_meta ( $ligne_palmares->ID, TENNISDEFI_XPROFILE_idjoueur, true );
		$user = get_userdata ( $id_joueur );
		
		if($id_joueur == $current_userID)
				$chaine .= '<OPTION selected VALUE="' . $user->ID . '">' . $user->display_name . '(N&#176; ' . $rang . ')';
		else
			$chaine .= '<OPTION  VALUE="' . $user->ID . '">' . $user->display_name . '(N&#176; ' . $rang . ')';
		
	}
	$chaine .= '</SELECT>';
	
	return $chaine;
}

// ========================================
/*
 * ! \brief retourne le code HTML pour une combobox avec les amis du joueurs + tous les joueurs du club, Classement par classemrnt ou alphabétique
 *
 * @param string : nom du du Select (<Select name="$Parametre")
 */

// =========================================
function combobox_joueurs_v2($SELECT_name, $user_idclub, $ordre_rang = 0, $disabled_select = false) {
	// $disabled_select  = true => i lfaut desactiver le selecteur
	//				Par défaut le selecteur n'est pas désactivé'
	//$ordre_rang  : Trie les données par nom et prenom si $ordre_rang=0
		// $chaine = '<SELECT name="'.$SELECT_name.'" size="1">';
		
	
	if($disabled_select){
		$chaine = '<SELECT disabled  ID="' . $SELECT_name . '"  name="' . $SELECT_name . '" data-placeholder="Séléctionnez un joueur">';
	}
	else{

		$chaine = '<SELECT  ID="' . $SELECT_name . '"  name="' . $SELECT_name . '" data-placeholder="Séléctionnez un joueur">';
	}
	// $chaine .= '<option value=""></option>';
	// Partenaires du joueurs
	// ---------------------------
	$chaine .= '<optgroup label="Vos Partenaires">';
	if (bp_has_members ( 'user_id=' . bp_loggedin_user_id () )) {
		
		while ( bp_members () ) {
			bp_the_member ();
			
			$friend_id = bp_get_member_user_id ();
			
			// on ne garde que les amis du club actuel
			$user_clubs = get_user_meta ( $friend_id, TENNISDEIF_XPROFILE_idclubs, true );
			if (in_array ( $user_idclub, $user_clubs )) {
				$user_info = get_userdata ( $friend_id );
				$ligne_palmaresID =  getUserPalmaresID($friend_id, $user_idclub);
				
				$friend_rang = get_post_meta ( $ligne_palmaresID, TENNISDEIF_XPROFILE_rang, true );
				$chaine .= '<OPTION VALUE="' . $friend_id . '">' . $user_info->display_name . '(N&#176; ' . $friend_rang . ')                               </OPTION>';
			} // fin si l'ami faitparti du club actuel
		} // fin boucle sur les amis
	}  // friends dispo
else {
		$chaine .= "<OPTION disabled>Vous n'avez pas de partenaire</OPTION>";
	}
	
	$chaine .= '</optgroup>';
	
	// Tous les membre du club
	// ---------------------------
	$chaine .= '<optgroup label="Tous les joueurs du club">';
	
	// Requette
	$args = array (
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $user_idclub 
					) 
			),
			'post_type' => 'palmares',
			'meta_key' => TENNISDEIF_XPROFILE_rang,
			'numberposts' => - 1,
			'orderby' => 'meta_value_num',
			'order' => 'DESC' 
	);
	$palmares = get_posts ( $args );
	$tab_joueurs = array ();
	foreach ( $palmares as $ligne_palmares ) {
		$rang = get_post_meta ( $ligne_palmares->ID, TENNISDEIF_XPROFILE_rang, true );
		$id_joueur = get_post_meta ( $ligne_palmares->ID, TENNISDEFI_XPROFILE_idjoueur, true );
		$user = get_userdata ( $id_joueur );
		$tab_joueurs [] = array (
				'ID' => $id_joueur,
				'rang' => $rang,
				'display_name' => $user->display_name,
				'nom' => $user->last_name,
				'prenom' => $user->first_name 
		);
	}
	
	// liste de colonnes pour le tri
	foreach ( $tab_joueurs as $key => $row ) {
		// $rang[$key] = $row['rang'];
		$nom [$key] = $row ['nom'];
		$prenom [$key] = $row ['prenom'];
	}
	
	// Trie les données par nom et prenom si $ordre_rang=0
	// Ajoute $tab_joueurs en tant que dernier paramètre, pour trier par la clé commune
	if (! $ordre_rang)
		array_multisort ( $nom, SORT_ASC, $prenom, SORT_ASC, $tab_joueurs );
		
		// traitement de chaque ligne
	foreach ( $tab_joueurs as $user_data ) {
		// get_currentuserinfo();
		$rang = $user_data ['rang'];
		$display_name = $user_data ['display_name'];
		$id_encypted = $user_data ['ID'];
		$chaine .= '<OPTION VALUE="' . $id_encypted . '">' . $display_name . '(N&#176; ' . $rang . ')';
	}
	
	$chaine .= '</optgroup>';
	
	$chaine .= '</SELECT>';
	
	return $chaine;
}





