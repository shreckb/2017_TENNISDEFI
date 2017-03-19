<?php
/*! \file
 \brief Contient les fonctions de gestions des tournois

 Details.

 */

/*
 * Le post TOurnois :
 * 				+ id du club pour le tournoi
 * 				+ date de creation du tournoi
 * 				+ date de mise à jour du tournoi
 * 				+ Nom du tournoi
 * 				+ id des joueurs : array
 * 				+ classement initiaux : array ('key_idJoueur' => rang)
 * 				+ dernier classement actualisé : array ('key_idJoueur' => rang)
 * 				+ tournois actif : visibilité par les user ou pas.
 * 
 */ 	


function tennisdefi_tournois_addTournoi_TO_userPalmares($id_user, $id_club, $id_tournoi){
	
	//Obtention ligne Palmares
	$id_palmares = getUserPalmaresID($id_user, $id_club);
	write_log("tennisdefi_tournois_addTournoi_TO_userPalmares : IDPalamees :$id_palmares " );
	if($id_palmares){
		// récupération de la liste des tournoi
		$array_tournois = get_post_meta($id_palmares, TENNISDEFI_TOURNOI_userTournoi,true);
		
		//si pas de tableau 
		if(!is_array ( $array_tournois)){
			$array_tournois = array();
			write_log("init du tableau");
		}
		
		// ajout du torunoi si besoin
		if(!in_array($id_tournoi, $array_tournois)){
			write_log("tournoi pas dans array");
			$array_tournois[] =  $id_tournoi;
			update_post_meta($id_palmares, TENNISDEFI_TOURNOI_userTournoi, $array_tournois );	
		}
		
		
	}
		
	
}

function tennisdefi_tournois_removeTournoi_TO_userPalmares($id_joueur, $id_club, $id_tournoi){
	
	write_log("Debut : tennisdefi_tournois_removeTournoi_TO_userPalmares");
	//Obtention ligne Palmares
	$id_palmares = getUserPalmaresID($id_joueur, $id_club);
	if($id_palmares){
		write_log("tennisdefi_tournois_removeTournoi_TO_userPalmares : id palmares:$id_palmares");
		// récupération de la liste des tournoi
		$array_tournois = get_post_meta($id_palmares, TENNISDEFI_TOURNOI_userTournoi,true);
		
		if(!is_array($array_tournois)){
			$array_tournois = array();
			write_log("Pas de tableau, alors que dans le tournoi? pas possible normalement");
		 
		}
		
		// retrait du tournoi si besoin
		if( !( ($indice = array_search($id_tournoi, $array_tournois)) === false)){
			unset($array_tournois[$indice]);	
			write_log("On a retirer le tpournoi au joueur");
		}
			
			update_post_meta($id_palmares, TENNISDEFI_TOURNOI_userTournoi, $array_tournois );
			
	}// id palamres existe
				
	write_log("FIN : tennisdefi_tournois_removeTournoi_TO_userPalmares");
	
}



function tennisdefi_tournois_create($id_club, $nom,$description, $isVisible, $isActif,$isOpen){
	//write_log("**************tennisdefi_tournois_create**************");
	
	$date_tournois = date("Y_m_d");
	$post_title = "tournoi_".$id_club."_".$date_tournois;
	
	// Ajout du tounois dans la Base
	$post = array (
			'post_title' => $post_title,
			'post_status' => 'publish', // Choose: publish, preview, future, etc.
			'post_type' => 'tournoi'
	);
	$post_ID = wp_insert_post ( $post ); // Pass the value of $post to WordPress the insert function
	
	//Ajout du nom et du club et des stuctures
	if ($post_ID) {
		add_post_meta($post_ID, TENNISDEIF_XPROFILE_idClub, $id_club);
		add_post_meta($post_ID, TENNISDEIF_TOURNOI_nomTournoi, $nom);
		add_post_meta($post_ID, TENNISDEIF_TOURNOI_usersArray, array());
		add_post_meta($post_ID, TENNISDEIF_TOURNOI_usersArrayClassement, array()); // Classement à la creation
		add_post_meta($post_ID, TENNISDEIF_TOURNOI_usersArrayLastClassement,array());
		
		add_post_meta($post_ID, TENNISDEIF_TOURNOI_isActif,$isActif); 	// si on doit mettre à jour les classement
		add_post_meta($post_ID, TENNISDEIF_TOURNOI_isVisible,$isVisible); // si on doit l'afficher à tous les joueurs concernés
		add_post_meta($post_ID, TENNISDEIF_TOURNOI_isOpen,$isOpen); // si on doit l'afficher à tous les joueurs concernés
		
		//Description
		add_post_meta($post_ID, TENNISDEIF_TOURNOI_description,$description); 
		
		
		return $post_ID;
		
	}
	else
		return false;
		
}

function tennisdefi_tournois_detete($id_tournoi){
	wp_delete_post( $id_tournoi, true );
}

function tennisdefi_tournois_addUsers($id_tournoi, $array_id_users){

	
	//write_log("**************tennisdefi_tournois_addUsers**************");
	//validation
	if( get_post_type( $id_tournoi  )  != 'tournoi' )
		return false;

	
	//  Récupération data du club
	$id_club = get_post_meta($id_tournoi, TENNISDEIF_XPROFILE_idClub, true);
	$tournoi_id_users 			= get_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArray, true);
	$tournoi_id_usersClassement = get_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArrayClassement, true);
	$tournoi_id_usersLastClassement = get_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArrayLastClassement, true);
	
	
	
	/*
	write_log("init \n----------------------------");
	write_log("Update du  tournoi : $id_tournoi");
	write_log("array_id_users:");  write_log($array_id_users);
	write_log("tournoi_id_users"); write_log($tournoi_id_users);
	write_log("tournoi_id_usersClassement"); write_log($tournoi_id_usersClassement);
	*/
	$key_prefix = "key_"; // permet de creer une clee pour les tableaux  {'clé' => classement} 
	
	
	// ajout des joueurs
	foreach($array_id_users as $id_joueur){
		$key =  $key_prefix.$id_joueur; // clé  dans le tableu iD / classement
		$rang = getUserRang($id_joueur, $id_club);
		
		// Nouveau joueur: 
		if(!in_array($id_joueur, $tournoi_id_users)){
			$tournoi_id_users[] =  $id_joueur;
			
			tennisdefi_tournois_addTournoi_TO_userPalmares($id_joueur, $id_club, $id_tournoi);	
		}
		
		// mise à jour des classements (ou creation si bsoin)
			$tournoi_id_usersClassement[$key]      = $rang;
			$tournoi_id_usersLastClassement[$key]  = $rang;
	}
	
	/*
	write_log("fin \n----------------------------");
	write_log("tournoi_id_users"); write_log($tournoi_id_users);
	write_log("tournoi_id_usersClassement"); write_log($tournoi_id_usersClassement);
	*/
	update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArray, $tournoi_id_users );
	update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArrayClassement, $tournoi_id_usersClassement);
	update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArrayLastClassement, $tournoi_id_usersLastClassement );
	
	return true;

}


function tennisdefi_tournois_removeUsers($id_tournoi, $array_id_users){

	
	//validation
	if( get_post_type( $id_tournoi  )  != 'tournoi' )
		return false;
	
	$id_club = get_post_meta($id_tournoi, TENNISDEIF_XPROFILE_idClub, true);
	$tournoi_id_users 			= get_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArray, true);
	$tournoi_id_usersClassement = get_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArrayClassement, true);
	//write_log($tournoi_id_users);
	//write_log($tournoi_id_users);
	
	foreach($array_id_users as $id_joueur){
		write_log('array_search($id_joueur, $tournoi_id_users)');
		write_log(array_search($id_joueur, $tournoi_id_users));
		if(! (  ( $indice = array_search($id_joueur, $tournoi_id_users)) === false)) {
			tennisdefi_tournois_removeTournoi_TO_userPalmares($id_joueur, $id_club, $id_tournoi);
			write_log("on retire le user : $id_joueur du tournoi : $id_tournoi");
			
			unset($tournoi_id_users[$indice]);
			$key_prefix = "key_"; // permet de creer une clee
			$key =  $key_prefix.$id_joueur;
			unset($tournoi_id_usersClassement[$key]);
		}// 

	}// fin foreach

	update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArrayClassement, $tournoi_id_usersClassement);
	update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArray, $tournoi_id_users );
	
	//write_log($tournoi_id_users);
	
	return true;
}

/*
 * ============================ 
 * retourne un tableau trié par nouveau classement
 */
function tennisdefi_tournois_getTournoiID_ofUser($id_user, $id_club){
	//Obtention ligne Palmares
	$id_palmares = getUserPalmaresID($id_user, $id_club);
	
	$array_tournois = get_post_meta($id_palmares, TENNISDEFI_TOURNOI_userTournoi,true);
	
	return $array_tournois;
}


function tennisdefi_tournois_getTournoi($id_tournoi) {
	// validation
	if (get_post_type ( $id_tournoi ) != 'tournoi')
		return false;
	
	// Si Il faut mettre à jour les classements des joueur on le fait
	if(get_post_meta ( $id_tournoi, TENNISDEIF_TOURNOI_isActif, true ))
		tennisdefi_tournois_Update($id_tournoi);
		
		
		
	// Récupération data
	$titre_tournoi = get_post_meta ( $id_tournoi, TENNISDEIF_TOURNOI_nomTournoi, true );
	$date_creation = get_post_time ( 'j F Y', false, $id_tournoi, true );
	
	$id_club = get_post_meta ( $id_tournoi, TENNISDEIF_XPROFILE_idClub, true );
	$tournoi_id_users = get_post_meta ( $id_tournoi, TENNISDEIF_TOURNOI_usersArray, true );
	$tournoi_id_usersClassement = get_post_meta ( $id_tournoi, TENNISDEIF_TOURNOI_usersArrayClassement, true );
	$tournoi_id_usersLastClassement = get_post_meta ( $id_tournoi, TENNISDEIF_TOURNOI_usersArrayLastClassement, true );
	$description = get_post_meta ( $id_tournoi, TENNISDEIF_TOURNOI_description, true );
	// Creation de 3 tableaux pour le tri : IDuser, ClassementInitial, dernier Classement actualisé
	// Classement en fonction du nouveau rang
	$Array_ID = array();  // tableau 1D avec ID de chaque joueur
	$Array_lastRang = array();  // tableau 1D avec  dernier Classement actualisé
	$Array_initialRang = array();  // tableau 1D avec  rang initial
	$key_prefix = "key_"; // permet de creer une clee pour les tableaux  {'clé' => classement}
	
	foreach ( $tournoi_id_users as $tournoi_id_user ) {
			$Array_ID [] = $tournoi_id_user;
			$key = $key_prefix . $tournoi_id_user;
			$Array_initialRang[] = $tournoi_id_usersClassement [$key];
			$Array_lastRang []   = $tournoi_id_usersLastClassement [$key];
	} // fin foreach
	  
	// Classement selon le dernier rang
	array_multisort ( $Array_lastRang, $Array_ID, $Array_initialRang );
	
	$tournoi = array (
			"titre" => $titre_tournoi,
			"description" => $description,
			"NBjoueur" => count ( $Array_lastRang ),
			"userID" => $Array_ID,
			"classementInit" => $Array_initialRang,
			"classementActu" => $Array_lastRang 
	);
	
	return $tournoi;
}


/*
 * ============================
 * retourne met à jour le classement des joueur d'un tournoi
 */
function tennisdefi_tournois_Update($id_tournoi){


	//validation
	if( get_post_type( $id_tournoi  )  != 'tournoi' )
		return false;

	// Recupération des infos
	//$titre_tournoi 				= get_post_meta($id_tournoi, TENNISDEIF_TOURNOI_nomTournoi, true );
	$tournoi_id_users 			= get_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArray, true);
	$tournoi_id_usersClassement = get_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArrayClassement, true);
	$tournoi_id_usersLastClassement = get_post_meta ( $id_tournoi, TENNISDEIF_TOURNOI_usersArrayLastClassement, true );
	$tournoi_id_club			= get_post_meta($id_tournoi, TENNISDEIF_XPROFILE_idClub, true);
	$key_prefix = "key_"; // permet de creer une clee
	
	// Remarque : il est possible qu'entre la creation et l'affichage du tournoi des personnes ont été retiré du plamares/club : dans cd cas pas de rang, 
		// REcherche du nouveau classement
	foreach ($tournoi_id_users as $tournoi_id_user){
		$key =  $key_prefix.$tournoi_id_user; // clé  dans le tableu iD / classement
		$rang = getUserRang($tournoi_id_user, $tournoi_id_club);
		if($rang)
			$tournoi_id_usersLastClassement[$key]  = $rang;
		else{
			// le jouer n'existe plus dans ce club'
			unset($tournoi_id_usersLastClassement[$key] );
			unset($tournoi_id_usersClassement[$key] );
			if(!(($key_id = array_search($tournoi_id_user, $tournoi_id_users)) === false) ){
				unset($tournoi_id_users[$key_id]);
			}
		}//else si rang
			
		
	}// fin foreach
	
	//Mise en base
	update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArray, $tournoi_id_users );
	update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArrayClassement, $tournoi_id_usersClassement);
	update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_usersArrayLastClassement, $tournoi_id_usersLastClassement );
	
}





/*
 * retourne un tableau avec les donnees de tous les tournois du club
 * => a afficher avec datatable par exemple
 * retourne un tableau avec : ['Nb' => Nb tournoi]
 * 							  ['Data' => array([data du tournoi1],[data du tournoi2], ...]
 * 							  ['Selector' => html code pour le selecteur de tournoi]
 * ex: http://datatables.net/release-datatables/examples/data_sources/js_array.html
 */
function tennisdefi_tournois_displayAll($current_club){

	$resultat = array('Nb' => 0, 'Data'=> array(), 'Selector' => '');
	
	$args = array('post_type' => 'tournoi',
			'numberposts' =>-1,
			'orderby'    => 'date',
			'meta_query' => array(
					array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_club)
			),
	);
	$tournoisClub =  get_posts($args);
	
	// Mise à jour NB tournoi
	$resultat['Nb'] = count($tournoisClub);
	
	
	$indice = 0;
	
	$txtSelector = '<SELECT id="tournoi_selected_for_action" size="1" style="width=400px">';
	$txtSelector .= "<option value='' disabled selected style='display:none;'>Choisir un tournoi</option>";
	
	//echo '<div id="div_DiplayTournoiSummary_info" class="info-box notice"> Aucun tournoi n\'a encore été créé </div>';
	foreach($tournoisClub as $tournoi){
		
		//write_log($tournoi);
		//recupération d'info
		$ID_encrypted = encrypt_decrypt ( 'encrypt', $tournoi->ID );
		$titre_tournoi = get_post_meta($tournoi->ID, TENNISDEIF_TOURNOI_nomTournoi, true );
		$tournoi_id_users 			= get_post_meta($tournoi->ID, TENNISDEIF_TOURNOI_usersArray, true);
		$nb_user = count($tournoi_id_users);
		
		
		$titre_tournoi = "<span class='div_DiplayTournoiSummary_nom' style='width:200' id='$ID_encrypted'> $titre_tournoi </span>";
		
		
		//visibilité
		$titre = "si visible, ce tournoi est affiché aux joueurs inscrits.";
		if (get_post_meta ( $tournoi->ID, TENNISDEIF_TOURNOI_isVisible, true ) == 1)
			$txt_visibilite = '<span class="" title ="' . $titre . '">visible</span>';
		else
			$txt_visibilite = '<span class="" title ="' . $titre . '">non visible</span>';
		
		// Actif/Inactif	(icon :dashicons dashicons-awards)
		$titre = "si inactif, le classement des joueurs n'est plus mise à jour";
		if (get_post_meta($tournoi->ID, TENNISDEIF_TOURNOI_isActif, true) == 1)
			$txt_actif = '<span class="" title ="' . $titre . '">actif</span>';
		else
			$txt_actif = '<span class="" title ="' . $titre . '">inactif</span>';
		
			// Ouvert aux inscription de tous(icon:dashicons dashicons-unlock)
			$titre = "inscription libre: les joueurs peuvent s'inscrire d'eux-meme à ce tournoi";
			if (get_post_meta($tournoi->ID, TENNISDEIF_TOURNOI_isOpen, true) == 1)
				$txt_open = '<span class="" title ="' . $titre . '">inscription libre</span>';
			else
				$txt_open = '<span class="" title ="' . $titre . '">inscription restreinte</span>';
	
		$tournois_statuts = "<ui>
				<li><span class='div_DiplayTournoiSummary_visibilite' style='width:200' id='$ID_encrypted'> $txt_visibilite </span>
				<li><span class='div_DiplayTournoiSummary_actif' style='width:200' id='$ID_encrypted'> $txt_actif </span>
				<li><span class='div_DiplayTournoiSummary_open' style='width:200' id='$ID_encrypted'>$txt_open</span>
		</ui>";	
		
		$description = "<span class='div_DiplayTournoiSummary_description' id='$ID_encrypted' rows='4' cols='50'> ". get_post_meta($tournoi->ID, TENNISDEIF_TOURNOI_description, true)."</span>"; 
		$tournoi_creation = mysql2date('j/m/Y', $tournoi->post_date);
		
		//actions
		$txt_affiherDetails = '<input type="button"   id="'.$ID_encrypted.'" class="boutontennisdefi_displayTournoi" value="afficher" />';
		$txt_supprimerTournoi = '<input type="button"   id="'.$ID_encrypted.'" class="boutontennisdefi_deleteTournoi" value="supprimer" />';
		//$txt_modifierTournoi = '<input type="button"   id="'.$ID_encrypted.'" class="boutontennisdefi_updateTournoi" value="modifier" />';
		
		$actions = "$txt_affiherDetails<br>$txt_supprimerTournoi";
		
		$txtSelector .= '<OPTION VALUE="' . $ID_encrypted . '" >' . $titre_tournoi;

		$resultat['Data'][] = array($tournoi_creation, $titre_tournoi, $nb_user,$tournois_statuts, $actions ,$description);  


	}
	
	$txtSelector .= '</SELECT>';
	$resultat['Selector'] = $txtSelector;
	
	//write_log("tennisdefi_tournois_displayAll : ");
	//write_log($resultat);
		return $resultat;

}


/*
 * retroune une variable avec l'Affichage du tournoi 
 * Titre, table, etc...
 */
/*
function tennisdefi_tournois_display($id_tournoi, $current_user_id){
		$tournoi = tennisdefi_tournois_getUpdate($id_tournoi);
		if(!$tournoi)
			return;
		$txt = "<br><div>";
      	$txt  .= "<h2 id=\"titre_tournoi\">tournoi : ".$tournoi["titre"]." </h2>";
      	//$txt  .= "il y a : ".$tournoi["NBjoueur"]." joueurs dans ce tournoi";
      	$txt  .= '<table id="table_tournoi" class="dt-responsive no-wrap stripe table_tennisdefi">
                    <thead><tr>
                    <th>Rang</th>
                    <th>Nom </th>
                    <th>Prénom</th>
					<th>Rang inital</th>
     				<th>Rang actuel</th>
      				<th>Gain de place</th>
		 		</tr></thead><tbody> ';
      	
      	
      	for ($k=0; $k<$tournoi["NBjoueur"] ; $k++){
      		$tournoi_id_user  =  $tournoi["userID"][$k];
      		$tournoi_rang_init =  $tournoi["classementInit"][$k];
      		$tournoi_rang_actu =  $tournoi["classementActu"][$k];
      		$gain = $tournoi_rang_init - $tournoi_rang_actu;
      		$rang_virtuel  = $k+1;
      		$user = get_userdata($tournoi_id_user);
      	
      		$txt  .= "<tr>";
      		//rang
      	
      		$txt  .= "<td>$rang_virtuel</td>";
      			
      		// Nom Prenom
      		if( $tournoi_id_user == $current_user_id)
      		{
      			$txt  .= '<td><b>'.strtoupper($user->user_lastname).'</b></td>';
      			$txt  .= '<td><b>'.$user->user_firstname.'</b></td>';
      		}
      		else{
      			$txt  .= '<td>'.strtoupper($user->user_lastname).'</td>';
      			$txt  .= '<td>'.$user->user_firstname.'</td>';
      		}
      	
      		// Classement
      		$txt  .= "<td>$tournoi_rang_init</td><td>$tournoi_rang_actu</td>";
      	
      		// Gain de place
      		$txt  .= "<td>$gain</td>";
      		$txt  .= "</tr>";
      	
      	
      	}// fin boucle sur le users du tounroi
      	
      	$txt  .=' ';
      	$txt .= "</div>";
      	return $txt;
	
}
/*
/*
 * retroune un tableau avec le contenu du tournoi, à afficher aec DATABLE par exemple 
 */
function tennisdefi_tournois_JSON($id_tournoi){
	
	$tournoi = tennisdefi_tournois_getTournoi($id_tournoi);
	

	$output = array(
			'sColumns' => 'rang, Nom, Prenom,class Ini, classs Actuel,  gain',
			'sEcho' => 1, 'iTotalRecords' => $tournoi["NBjoueur"],
			 'iTotalDisplayRecords' =>$tournoi["NBjoueur"],
			'aaData' => array(),
			'titre' =>$tournoi["titre"]
	 );
	 

	//On ajoute un champ caché avecl'id du tournoi
	$ID_tournoi_encrypted = encrypt_decrypt ( 'encrypt', $id_tournoi );
	$champ_id_tournoi = '<input type="hidden" name="id_tournoi_displayed" value='.$ID_tournoi_encrypted.'>';
	
	//$tournoiJSON = array();
	for ($k=0; $k<$tournoi["NBjoueur"] ; $k++){
		
		$tournoi_id_user  =  $tournoi["userID"][$k];
		$user = get_userdata($tournoi_id_user);
		
		$ID_user_encrypted = encrypt_decrypt ( 'encrypt', $tournoi_id_user );
		$bouton_remove_user = '<input type="button"   id="'.$ID_user_encrypted.'" class="boutontennisdefi_removeuser" value="retirer du tournoi" />';
		
		
		$ligne = array();
		
		$ligne[]  = $k+1;
		$ligne[]  = strtoupper($user->user_lastname);
		$ligne[]  = $user->user_firstname;
		$ligne[] =   $tournoi["classementInit"][$k];
		$ligne[]  =  $tournoi["classementActu"][$k];
		$ligne[] = $tournoi["classementActu"][$k] - $tournoi["classementInit"][$k];
		$ligne[] = $champ_id_tournoi.$bouton_remove_user;
		
		
		$output['aaData'][] =$ligne;
		//$tournoiJSON[] = $ligne;
	}// fin boucle sur le users du tounroi
	 

	return $output;

}

// ========================================
/* ! \brief Permet de gerer la requete ajax de changement de club associé avec la fonction addTitleAndSelectBox()*/
// =========================================
// Ajax

add_action( 'wp_ajax_tennisdefi_gestion_tournoi' , 'ajaxGestionTournoi');
add_action( 'wp_ajax_nopriv_tennisdefi_gestion_tournoi' , 'ajaxGestionTournoi');

function ajaxGestionTournoi() {

	check_ajax_referer( 'tennisdefi_ajax_security_pageGestionClub_Tournois', 'security' );
	
	global $current_user;
	write_log("**********process_ajaxGestionTournoi");
	header('Content-Type: application/json');

	//-------------------------------
	if($_REQUEST['fonction'] == 'display_Alltournois'){
		// retourne  le tableau de tous les tournois du clubs
		//	$user_clubs = get_user_meta($current_user->ID,  TENNISDEIF_XPROFILE_idclubs, true); // ts les clubs du joeuurs
		$ID_club_encrypted = $_REQUEST['club_user'];
		$current_club = encrypt_decrypt('decrypt', $ID_club_encrypted);
		echo json_encode(tennisdefi_tournois_displayAll($current_club));
		wp_die();
	}
	

	elseif($_REQUEST['fonction'] == 'display_tournoi'){
		// retourne  le tableau du tounoi
		//	$user_clubs = get_user_meta($current_user->ID,  TENNISDEIF_XPROFILE_idclubs, true); // ts les clubs du joeuurs
			$ID_tournoi_crypted = $_REQUEST['tournoi_selected'];
			$Id_tournoi_selected = encrypt_decrypt('decrypt', $ID_tournoi_crypted);
			//$tournoi_txt = tennisdefi_tournois_display($Id_tournoi_selected, 0);
			$tournoi_txt = tennisdefi_tournois_JSON($Id_tournoi_selected);
			//write_log($tournoi_txt);
			echo json_encode($tournoi_txt);
		
			wp_die();
	}
	elseif($_REQUEST['fonction'] == 'delete_tournoi'){
		// supprime un tournoi
		$ID_tournoi_crypted = $_REQUEST['tournoi_selected'];
		$Id_tournoi_selected = encrypt_decrypt('decrypt', $ID_tournoi_crypted);
	
		$tournoi_txt = tennisdefi_tournois_detete($Id_tournoi_selected);

		echo json_encode("Suppression du tournoi effectuée");
	
		wp_die();
	}
	else if($_REQUEST['fonction'] == 'create_tournoi'){
		$current_user = wp_get_current_user();
		$current_club = get_the_author_meta ( 'tennisdefi_idClub', $current_user->ID,true );
		$tournoi_description = $_REQUEST['tournoi_description'];
		$tournoi_nom = $_REQUEST['tournoi_nom'];
		$isvisible = $_REQUEST['tournoi_visibilite'];
		$isOpen = $_REQUEST['tournoi_open'];
		$isActif   = 1; // PAr defaut un nouveau tournoi est actif (mise à jour des classements)
		
		$ID_tournoi = tennisdefi_tournois_create($current_club, $tournoi_nom, $tournoi_description, $isvisible, $isActif,$isOpen);
		if($ID_tournoi)
			$txt = 'OK';
		else 
			$txt = 'Une erreur est survenue';
		
		echo json_encode($txt);
		wp_die();
		
	}
	// ***********************************
	else if($_REQUEST['fonction'] == 'update_tournoi'){
		$current_user = wp_get_current_user();
		$current_club = get_the_author_meta ( 'tennisdefi_idClub', $current_user->ID,true );
		$data_user = $_REQUEST['users_selected_data'];
		$tournoi_action = $_REQUEST['tournoi_action'];
		$tournoi_encrypted = $_REQUEST['tournoi_encrypted'];
		
		
		// Conversion des ID users
		$ID_users = [];
		foreach($data_user['data_to_send'] as $id_user_encrypted)
		 	$ID_users [] = encrypt_decrypt('decrypt', $id_user_encrypted);

		write_log("ID DES JOUEURS A TRAITEER(ajouter/retirer) : ");
		write_log("action:$tournoi_action" );
		write_log($ID_users);
		
		// Verifion que le tournoi existe
		$ID_tournoi = encrypt_decrypt('decrypt', $tournoi_encrypted);
		if(get_post_type( $ID_tournoi ) != 'tournoi'){
			echo json_encode("Une erreur est survenue<br>");
			//write_log("type de post Tournoi demandé pas OK ");
			wp_die();
		}	
		else{
			// On peut traiter l'action
			if($tournoi_action == 'ajouter'){
				tennisdefi_tournois_addUsers($ID_tournoi, $ID_users);
			}
			if($tournoi_action == 'retirer'){
				tennisdefi_tournois_removeUsers($ID_tournoi, $ID_users);
			}
			echo json_encode("OK ca passe");
			wp_die();
		}

	}
	// ***************** Update Resumé d'un tournoi *******************
	else if($_REQUEST['fonction'] == 'update_tournoi_resume'){
		if(!isset($_REQUEST['id']) || !isset($_REQUEST['value']) || !isset($_REQUEST['field']) ){
			echo json_encode("erreur lors de l'enregistrement");
			wp_die();
		}
			$id_tournoi = encrypt_decrypt('decrypt', $_REQUEST['id']);
			$value = htmlentities($_REQUEST['value']);
			$field = $_REQUEST['field'];
			//write_log("id tournoi =$id_tournoi");
			if($field== 'actif')
				update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_isActif, $value);
			elseif($field== 'open')
				update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_isOpen, $value);
			elseif($field== 'visibilite')
				update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_isVisible, $value);
			elseif($field== 'nom')
				update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_nomTournoi, $value);
			elseif($field== 'description'){
				write_log($value);
				update_post_meta($id_tournoi, TENNISDEIF_TOURNOI_description, $value);
			}
			else {
				echo json_encode('champ non reconnu');
				wp_die();
			}

			//on ne retourne rien :)
			echo json_encode($value);
			
			wp_die();
		
	}
	else {
		
		echo json_encode("Une erreur est survenue<br>");
		wp_die();
	}
	

} // end process_ajaxChangerClub





//REtoure la liste des actions
// =================================
function get_Tournoi_actions($ID_selector){

	

	$txt = '<SELECT id="'.$ID_selector.'" size="1" style="width=400px">';
	$txt .= "<option value='' disabled selected style='display:none;'>Choisir une action</option>";
	$txt .= "<option value='ajouter'> Ajouter</option>";
	$txt .= "<option value='retirer'> Retirer</option>";

	$txt .=  '</SELECT>';

	return $txt;
}





