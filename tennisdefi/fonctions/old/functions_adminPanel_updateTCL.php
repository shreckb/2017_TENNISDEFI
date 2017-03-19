<?php

/*
 * ! \file
 * \brief Contient les fonctions de gestions d'affichage coté admin Panel
 */

// VOir le ficheir function_adminPanel.php pour la declaration du menu
// réutilisation des scripts js panel_stat.php pour utiliser datatables 
// Ajout du Script deja fait dans les stat 
/*
function register_TennisDefi_STAT_JSfile() {
	wp_register_script ( 'custum_tennisdefi_adminpanel_statJSscript', get_stylesheet_directory_uri () . '/js/Tennisdefi_adminPanel/page_stat.js' );
}
add_action ( 'admin_init', 'register_TennisDefi_STAT_JSfile' );
*/



// Problme pares importation
	//LEcture de la ligne 19 pour DU GARDIN Jean-Yves
	//LEcture de la ligne 31 pour BETOULE Régis
	//LEcture de la ligne 100 pour FORT François
	
//************************************************
// add_TennisDefi_STAT_JSfile_forUpdtaeTCL()
//************************************************
function add_TennisDefi_STAT_JSfile_forUpdtaeTCL($hook) {
	global $TENNISDEFI_hook_to_menu_pageUpdateTCL; // defini dans le fichier parent(function_adminPanel.php)
	if ($hook != $TENNISDEFI_hook_to_menu_pageUpdateTCL)
		return;
	
	wp_enqueue_script ( 'custum_tennisdefi_adminpanel_statJSscript', array (
			'jquery' 
	), array (
			'jquery' 
	) );
	enqueue_script_Lib_DataTable ();
}
add_action ( 'admin_enqueue_scripts', 'add_TennisDefi_STAT_JSfile_forUpdtaeTCL' );



define("UPDATETCL_sexe", 0);
define("UPDATETCL_nom", 1);
define("UPDATETCL_prenom", 2);
define("UPDATETCL_datenaissance", 4);
define("UPDATETCL_tel", 6);
define("UPDATETCL_email", 7);
define("UPDATETCL_classement", 8);
define("UPDATETCL_top_classement", 9);
define("UPDATETCL_dejaTennisDefi", 10);

//************************************************
// find_user_byNameAndClub()
// Le problème , quand on cherche par tennisclub actif = TCL......
//************************************************
function find_user_byNameAndClub($nom, $prenom, $TCL_clubID){
	return;
	
	$args = array(
			'meta_query' => array(
					//array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $TCL_clubID),
					array('key' => 'last_name','value' => $nom),
					array('key' => 'first_name','value' => $prenom),
			),
	
			'numberposts' =>-1,
	);
	$users  = get_users( $args );
	
	
	write_log("--------------AVANT-------------------");
	write_log($users);
	// recherche si club présent
	for ($k=0; $k<count($users) ; $k++){
		$id_clubs = get_user_meta($users[$k]->ID, TENNISDEIF_XPROFILE_idclubs, true);
		if(!in_array($TCL_clubID, $id_clubs))
			$users[$k]=[];

	}
	write_log("--------------APRES-------------------");
	write_log($users);
		
	
	return $users;
}



//************************************************
// TennisDefi_updateTCL_userData()
//************************************************
function TennisDefi_updateTCL_userData($data, $user_id, $user_info , $TCL_clubID, $rang) {
	$txt = '';
	// Mettre à jour data
	
	/*
	// Classement
	$classement = xprofile_get_field_data ( TENNISDEIF_XPROFILE_classement, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	$txt .= "classement avant = $classement<br>";
	if (! xprofile_set_field_data ( TENNISDEIF_XPROFILE_classement, $user_id, $data [UPDATETCL_classement] ))
		$txt .= 'Erreur : Impossible de mettre à jour le classement actuel<br>';
	
	$classement = xprofile_get_field_data ( TENNISDEIF_XPROFILE_classement, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	$txt .= "classement après = $classement<br>";
	
	if (strcmp ( $classement, $data [UPDATETCL_classement] ) != 0) {
		$txt .= "Erreur : classement pas egaux<br>'";
	}
	// Ex Classement
	$classement = xprofile_get_field_data ( TENNISDEIF_XPROFILE_exclassement, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	$txt .= "top classement avant = $classement (new lu = " . $data [UPDATETCL_top_classement] . ")<br>";
	if (! xprofile_set_field_data ( TENNISDEIF_XPROFILE_exclassement, $user_id, $data [UPDATETCL_top_classement] ))
		$txt .= 'Erreur : Impossible de mettre à jour le classementy actuel<br>';
	$classement = xprofile_get_field_data ( TENNISDEIF_XPROFILE_exclassement, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	$txt .= "top classement après = $classement<br>";
	if (strcmp ( $classement, $data [UPDATETCL_top_classement] ) != 0) {
		$txt .= "Erreur : top classement pas egaux<br>";
	}
	
	// Telephone
	$tel = xprofile_get_field_data ( TENNISDEIF_XPROFILE_telephone, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	$txt .= "tel avant = $tel (new lu = " . $data [UPDATETCL_tel] . ")<br>";
	if (! xprofile_set_field_data ( TENNISDEIF_XPROFILE_telephone, $user_id, $data [UPDATETCL_tel] ))
		$txt .= 'Erreur : Impossible de mettre à jour le tel<br>';
	$tel = xprofile_get_field_data ( TENNISDEIF_XPROFILE_telephone, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	$txt .= "tel  après = $tel<br>";
	if (strcmp ( $tel, $data [UPDATETCL_tel] ) != 0) {
		$txt .= "Erreur : tel pas egaux<br>";
	}
	
	// email
	$txt .= "email  avant = $user_info->user_email (lu = " . $data [UPDATETCL_email] . ")<br>";
	wp_update_user ( array (
			'ID' => $user_id,
			'user_email' => $data [UPDATETCL_email] 
	) );
	$user_info = get_userdata ( $user_id );
	$txt .= "email  après = $user_info->user_email<br>";
	if (strcmp ( $user_info->user_email, $data [UPDATETCL_email] ) != 0) {
		$txt .= "Erreur : email pas egaux<br>";
	}
	
	// date de naissance
	$date_rpl = str_replace ( '/', '-', $data [UPDATETCL_datenaissance] );
	// date_format($date, 'Y-m-d');
	// write_log($date_rpl);
	$date = date ( 'Y-m-d H:i:s', strtotime ( $date_rpl ) );
	$txt .= "<br> date lu : " . $data [UPDATETCL_datenaissance] . "\tconvertie en " . strtotime ( $date_rpl ) . "\tqui donne : $date<br>";
	$naissance = xprofile_get_field_data ( TENNISDEIF_XPROFILE_datenaissance, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	
	$txt .= "date de naissance avant = $naissance (new lu = " . $data [UPDATETCL_datenaissance] . ")<br>";
	if (! xprofile_set_field_data ( TENNISDEIF_XPROFILE_datenaissance, $user_id, $date ))
		$txt .= 'Erreur : Impossible de mettre à jour la date de naissance<br>';
	$naissance = xprofile_get_field_data ( TENNISDEIF_XPROFILE_datenaissance, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	$txt .= "date de naissance après = $naissance<br>";
	*/
	// sexe
	$sexe = xprofile_get_field_data ( TENNISDEIF_XPROFILE_sexe, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	$txt .= "sexe avant = $sexe (new lu = " . $data [UPDATETCL_sexe] . ")<br>";
	if (strcmp ( $data [UPDATETCL_sexe], 'F' ) == 0) {
		if (! xprofile_set_field_data ( TENNISDEIF_XPROFILE_sexe, $user_id, 'féminin' ))
			$txt .= 'Erreur : Impossible de mettre à jour le sexe féminin<br>';
	} else {
		if (! xprofile_set_field_data ( TENNISDEIF_XPROFILE_sexe, $user_id, 'masculin' ))
			$txt .= 'Erreur : Impossible de mettre à jour le sexe masculin<br>';
	}
	
	$sexe = xprofile_get_field_data ( TENNISDEIF_XPROFILE_sexe, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
	$txt .= "sexe  après = $sexe<br>";
	
	/*
	// mettre à jour son rang
	$rang_prec = getUserRang($user_id, $TCL_clubID);
	$txt .= "rang lu :   $rang (ancien rang = $rang_prec)<br>";
	
	$ID_palmares = getUserPalmaresID ( $user_id, $TCL_clubID );
	if (! $ID_palmares) {
		$txt .= 'Erreur : Impossible de trouver l\'ID du plamares<br>' ;
	}
	else
		update_post_meta($ID_palmares, TENNISDEIF_XPROFILE_rang, $rang);
	$rang = getUserRang($user_id, $TCL_clubID);
	$txt .= "rang nouveau  :   $rang";
	
	$txt = "NOTICE : desctivation des affichage dans TennisDefi_updateTCL_userData()";
*/
	return $txt;
}

// ****************************
// Page 1 --- Principale
// ****************************
function TennisDefi_menu_updateTCL() {
	
	// LEcture  du fichier
	$myfile = fopen("../00_tempDB/compteur.txt", "r") or die("Unable to open file!");
	$data = fgetcsv ( $myfile, 0, ";" );
	$run = $data[0];
	fclose($myfile);
	
	//$run = 0; // 0 -> 10 ; si 100 alors on fait la mise à jour de tout
	$run =100;
	
	echo "<h4>RUN lu  =  $run </h4>";
	
	
	// Incrément pour le tour d'après 
	$next_run = $run+1;
		$myfile = fopen("../00_tempDB/compteur.txt", "w") or die("Unable to open file!");
		fwrite($myfile, $next_run);
		fclose($myfile);
		

	echo "<h1>Update TCL</h1>";
	
	$TCL_clubID = 9621;
	

	
	
	
	$max_ligne_traitement_init =5;
	$max_ligne_traitement = 10; // permet de traiter uniquement des paquet de X lignes du fichier (update unqiuement)
	
	if($run ==0){
		$bSuppressionUser = true; // supprimer les joueurs de puis fichier , à faire ? 
		$Ligne_start = 0; // commence à zero , uniquement pour l'update de data	
		$max_ligne_traitement = $max_ligne_traitement_init; // permet de traiter uniquement des paquet de X lignes du fichier (update unqiuement)

	}
	elseif($run ==1){
		$bSuppressionUser = false; // supprimer les joueurs de puis fichier , à faire ?
		$Ligne_start = $max_ligne_traitement_init; // commence à zero , uniquement pour l'update de data
		}
	else{
		$bSuppressionUser = false; // supprimer les joueurs de puis fichier , à faire ?
		$Ligne_start = ($run-1)*$max_ligne_traitement + $max_ligne_traitement_init; // commence à zero , uniquement pour l'update de data
		}
		
	if($run ==100){
		// Update une fois l'import fait
		$bSuppressionUser = false; // supprimer les joueurs de puis fichier , à faire ?
		$Ligne_start = 0; // commence à zero , uniquement pour l'update de data
		$max_ligne_traitement = 2000; // permet de traiter uniquement des paquet de X lignes du fichier (update unqiuement)

	}

	
	//************************************************
	// Suppression de tout ceux au TCL PRO du  TCL (n'avait pas de résultat de toute facon)
	//************************************************
	$args = array(
			'meta_query' => array(
					array('key' => TENNISDEIF_XPROFILE_idClub,'value' => 10978),
			),
			'numberposts' =>-1,
	);
	$users  = get_users( $args );
	for($k = 0; $k<count($users); $k++){
		wp_delete_user($users[$k]->ID);
	}
	
	
	
	//**********************************
	// TEST 
	// *********************************
	/*
	echo "<h2> TEST liste users trouvés ( nom et prenom des joueurs)</h2>";
	$file ='../00_tempDB/liste 3 mai 2015_utf8.csv';
	$handle = fopen($file, "r");
	
	if(!$handle)
		echo "Impossible de lire le fichier<br> <a href=\"$file\">lien</a";
	
		if (($handle = fopen($file, "r")) !== FALSE) {
	
		// Premiere ligne de titre
		$data = fgetcsv($handle, 0, ";");
		$row = 0;
		echo "<table><tr><td>Row</td><td>Nom</td><td>Prenom</td><td>ID</td></tr>";
		while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
			$row++;
			$args = array(
					'meta_query' => array(
							//array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $TCL_clubID),
							array('key' => 'last_name','value' => $data[UPDATETCL_nom]),
							array('key' => 'first_name','value' => $data[UPDATETCL_prenom]),
					),
			
					'numberposts' =>-1,
			);
			$users = get_users ( $args );
			foreach ( $users as $user ) {
				$clubs = get_user_meta ( $user->ID, TENNISDEIF_XPROFILE_idclubs,true );
				if (!in_array ( $TCL_clubID, $clubs )) {
					echo "<tr>";
					echo "<td>$row</td><td>".$data[UPDATETCL_nom]."</td><td>".$data[UPDATETCL_prenom]."</td><td>".$user->ID."</td>";
					echo "</tr>";
				}//fin pas du TCL
			}// fin foreach
		}//fin while fichier
		echo "</table>";
				
		
		}
	*/

	
	

	
	
	//setlocale(LC_ALL, 'fr_FR');
	//echo setlocale(LC_ALL, 0);
	
		
	
	//************************************************
	//Personnes à traiter à la main:
	//************************************************
	
	
	//BEZOT	Gregoire (se retirer du club)
		// => Afaire
	//ISABELLE IMBARD  ID=50 = TCL!!  ID = 474 à supprimer
		wp_delete_user(474);
		addUserToClub(50, 9630); // Ajout de maman au panorama
		// Le prog mettra tout a jour
		
	// Ranvier Jean-louis
		wp_delete_user(74);
	// BEZOT Grégoire
		removeUserToClub(483, 9621);
		


	//Briede dace (non present dans les joeuurs à supp mais non présent  dans la liste à mettre à jour)
		removeUserToClub(3365, $TCL_clubID);
	// IDEM pour Florent Pigot
		removeUserToClub(3356, $TCL_clubID);
	//Saint-hilaire	Pascal (plusieurs clubs : Id = 94)
		removeUserToClub(94, $TCL_clubID);
	// philippe.bouvier ID 696
		//removeUserToClub(696, $TCL_clubID);
		//louis-joseph.bouvier 
		removeUserToClub(71, $TCL_clubID);
		
		
		
		
	/*	 A NE PLUS FAIRE
			// M xavier.walet => existe mais pas inscrit au TCL
				addUserToClub(3355, $TCL_clubID);
				
			// M Regis Betoule => existe mais pas inscrit au TCL
				addUserToClub(782, $TCL_clubID);	
			// M Sarah et Symvain FATTON => existe mais pas inscrit au TCL
				addUserToClub(3602, $TCL_clubID);
				addUserToClub(27, $TCL_clubID);
			// M gilles.carlot => existe mais pas inscrit au TCL	
				addUserToClub(865, $TCL_clubID);
			// FENOGLIO	Bernard	=> existe mais pas inscrit au TCL	
				addUserToClub(3511, $TCL_clubID);
			// 		GINDRE	Patrick	=> existe mais pas inscrit au TCL	
				addUserToClub(2525, $TCL_clubID);
			// 		imbard-bezot	Isabelle	=> existe mais pas inscrit au TCL	
				addUserToClub(474, $TCL_clubID);
				
			//	DU GARDIN	Jean-Yves	
				addUserToClub(852, $TCL_clubID);
				
			// Loic.WAWRZYNIAK
				addUserToClub(3675, $TCL_clubID);
	*/
		
		
		
	
	
	//************************************************
	// Suppression des joueurs actuels à supprimer
	//************************************************
	 
	if($bSuppressionUser){
			echo "<h2> Suppressions d'anciens joueurs</h2>";
			$file ='../00_tempDB/A supprimer de TD_v2.csv';
			$handle = fopen($file, "r");
		
				if(!$handle)
					echo "Impossible de lire le fichier<br> <a href=\"$file\">lien</a";
				
				if (($handle = fopen($file, "r")) !== FALSE) {
					// Premiere ligne de titre
					//-------------------------
					$data = fgetcsv ( $handle, 0, ";" );
					$num = count ( $data );
					// $num = 6; // limitation du nombre de colonne
					
					echo "<b>Il y a $num colonnes</b><br>";
					echo '<table id="table_inscrits" class="dt-responsive no-wrap stripe table_tennisdefi">';
							echo "<thead><tr>";
							for($c = 0; $c < $num; $c ++) {
								echo "<th>$data[$c]</th>";
							} // fin d'une ligne
							echo '<th>Traitement</th>';
					echo "</tr></thead><tbody>";
					
					$row = 0;
					
					// Parcours du fichier
					// -------------------
				while ( ($data = fgetcsv ( $handle, 0, ";" )) !== FALSE ) {
					
					// TRAITEMENT PAR LIGNE DU FICHIER
					// ============================================
					
					$row ++;
					$num = count ( $data ); // nombre de champs
					                     // $num = 6;// limitation du nombre de colonne
					$txt = '';
						
					if($data[2] == 1){
							$txt .='User à supprimer';
							// Trouver son ID
							$args = array(
									'meta_query' => array(
											//array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $TCL_clubID),
											array('key' => 'last_name','value' => $data[0]),
											array('key' => 'first_name','value' => $data[1]),
									),
							
									'numberposts' =>-1,
							);
							$users  = get_users( $args );
							//write_log($users);
							$valid =true;
							if(count($users)>1){
								$txt.='<br>Erreur: '.count($users).' joueurs trouvés pour cette ligne<br>'.$data[0]. ' '.$data[1].'<br>';
								$valid = false;
							}
							if(count($users)<1){
								$txt.='<br>Erreur: aucun joueur trouvé pour cette ligne<br>';
								$valid = false;
							}
							
							if($valid){
								
								$user_id = $users[0]->ID;
								$user_info = get_userdata($user_id);
								$clubs = get_user_meta($user_id, TENNISDEIF_XPROFILE_idclubs,true );
								if(in_array($TCL_clubID, $clubs)){
									wp_delete_user($user_id);
								}else 
								{
									$txt.="Erreur : Le joueur ,à supprimer, et trouvé ne fait pas parti du TCL";
								}
							}//joueur valide, ID trouvé
										
					}
					echo "<tr>";
					for($c = 0; $c < $num; $c ++) {
						echo "<td>$data[$c]</td>";
					} // fin d'une ligne
					
					echo "<td>$txt</td>";
					echo '</tr>';
				} // fin du ficheir
				echo "</tbody></table>";
			} // le handle existe
			fclose ( $handle );
	}// fin $bSuppressionUser = true			

	
	
	//************************************************
	// Modification ET creation
	//************************************************
	
	echo "<h2> Update d'anciens et de noveaux joueurs</h2>";
	$file ='../00_tempDB/liste 3 mai 2015_corr.csv';
	$handle = fopen($file, "r");

	if(!$handle)
		echo "Impossible de lire le fichier<br> <a href=\"$file\">lien</a";

	
	
	if (($handle = fopen($file, "r")) !== FALSE) {
	
		
		// Premiere ligne de titre
		//-------------------------
		$data = fgetcsv($handle, 0, ";");
		$num = count($data);
		$num = 6; // limitation du nombre de colonne
		
		echo "<b>Il y a $num colonnes</b><br>";
		echo'<table id="table_clubs_data" class="dt-responsive no-wrap stripe table_tennisdefi">';
		echo"<thead><tr><th>ligne</th>";
			for ($c=0; $c < $num; $c++) {
				echo "<th>$data[$c]</th>";
			} // fin d'une ligne
			echo '<th>Traitement</th>';	
			
		echo "</tr></thead><tbody>";
		
		$row = 0;
		
		
		for($k=0; $k<$Ligne_start; $k++){
			$data = fgetcsv($handle, 0, ";");
			$row++;
		}
		
		// Parcours du fichier 
		// -------------------
		while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
			
			//Securité
			if($row > ($Ligne_start +$max_ligne_traitement))
				break;
			
			// TRAITEMENT PAR LIGNE DU FICHIER
			// ============================================
			
			$row ++;
			$num = count($data); // nombre de champs
			$num = 6;// limitation du nombre de colonne
			$txt = '';
			
			write_log("LEcture de la ligne $row pour ".$data[UPDATETCL_nom]." ". $data[UPDATETCL_prenom]);
			// Le jouer est à creer
			// ---------------------------
			if( $data[UPDATETCL_dejaTennisDefi] ==0 ){
				$validite_DONTEXIST = true;
				
				$txt.="le joueur n'existe pas<br>";

				// Validation qu'on la pas déja creer
				$args = array(
						'meta_query' => array(
								//array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $TCL_clubID),
								array('key' => 'last_name','value' => $data[UPDATETCL_nom]),
								array('key' => 'first_name','value' => $data[UPDATETCL_prenom]),
						),
				
						'numberposts' =>-1,
				);
				$users  = get_users( $args );
				$txt.= "on a trouvé ".count($users)." USERS pour ".$data[UPDATETCL_nom]." ". $data[UPDATETCL_prenom]."<br>";
				if(count($users)==1){
					
					// 1 seul joueur trouvé  , fait déja parti du TCL? 
					$clubs = get_user_meta($users[0]->ID, TENNISDEIF_XPROFILE_idclubs,true );
					if(in_array($TCL_clubID, $clubs)){
						$new_user_id = $users[0]->ID;
						$txt.=" NOTICE : le joueur existait deja au TCL (ID = $new_user_id)<br>";
					} 
					else{
						// Le joueur existe, mais Validation qu'on vient bien de le creer
						$dateY =  date ( 'Y', strtotime ( $users[0]->user_registered ) );
						$dateM =  date ( 'm', strtotime ( $users[0]->user_registered ) );
						write_log("ligne : $row \t dateY = $dateY et date M = $dateM ");
						if($dateY ==2015 && $dateM ==5){
							//write_log("PAS DE PROBLEME , CREEATION  en MAI du users");
							$new_user_id = $users[0]->ID;
							$txt.=" NOTICE : le joueur n'existait pas au TCL mais, creation récente (ID = $new_user_id)<br>";
							addUserToClub($new_user_id, $TCL_clubID);				    	
						}
				   		else{
				   					$new_user_id = $users[0]->ID;
				   		 			$txt.= "Erreur : Le joueur est à creer mais il existe déja et avant MAi 2015";
				   		 			$txt.= "=> Verifier que c'est la bonne personne => Update FAIT";
				   		 			addUserToClub($new_user_id, $TCL_clubID);
				   		 			// $validite_DONTEXIST = true;
				   		} // Fin validation que le jouuer existant, pas au tcl, est creer récemment
					}// Fin  du else, le jouer existe et pas au TCL
					
				} // le jouueur est à creer mais existe déja et plusieurs resultats: KO
				elseif(count($users)>1){
					$validite_DONTEXIST = false;
					$txt.= "Erreur : Le joueur est à creer mais il existe déja plusieurs user avec ce nom et prenom";	 
				}
				else {
					// On peut creer le user
					$username = str_replace(' ', '', $data[UPDATETCL_prenom].'.'.$data[UPDATETCL_nom]);
					$password = $data[UPDATETCL_datenaissance];
					$email =$data[UPDATETCL_email];
					
					if(username_exists( $username )){
						$validite_DONTEXIST = false;
						$txt.= "Erreur : Le joueur est à creer mais le username existe déja";
				
					}
					else{
					
					$txt .=  "creation du jouer : $username et pass : $password et email =$email<br>";
					//write_log("creation du jouer : $username et pass : $password et email =$email");
					$new_user_id = wp_create_user($username, $password, $email);
					
					//write_log("\t creation du jouer : $username et pass : $password et email =$email => new ID : $new_user_id");
					if(!$new_user_id){
						
						write_log("Erreur à la creation du joueurs ");
						$txt .=  "Erreur wp_create_user()  <br>";
						$validite_DONTEXIST = false;
					}
					else {		
						
						// Mise à jour Nom et Prenom
						//NOm et Prenom
						$update_user_return = wp_update_user( array( 'ID' => $new_user_id,
								'last_name' => $data[UPDATETCL_nom],
								'first_name' => $data[UPDATETCL_prenom] ) );
						
						if ( is_wp_error( $update_user_return ) ) {
							// There was an error, probably that user doesn't exist.
							
							$txt.="Erreur Nom Prenom  :Impossible de mettre le nom et prenom à jour l'utilisateur <br>";
	
						}
						// Ajout au TCL
						addUserToClub($new_user_id, $TCL_clubID);
					}
					}// c'est bon le username n'existe pas on creer le user et nom, prenom
				} // on creer le joueur à cree	
				
				// Mise  à jour du Gars
				if($validite_DONTEXIST){
					$user_info = get_userdata($new_user_id);
					$txt .= TennisDefi_updateTCL_userData($data, $new_user_id, $user_info,$TCL_clubID, $row);
				}
			} // fin le jouer est à creer
			
			// Joueur Existant
			// --------------------
			if( $data[UPDATETCL_dejaTennisDefi] ==1 ){
				$txt.="le joueur existe deja<br>";
				// Trouver son ID
				$args = array(
						'meta_query' => array(
								//array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $TCL_clubID),
								array('key' => 'last_name','value' => $data[UPDATETCL_nom]),
								array('key' => 'first_name','value' => $data[UPDATETCL_prenom]),
						),
						
						'numberposts' =>-1,
				);
				$users  = get_users( $args );
				//write_log($users);
				$valid =true; 
				if(count($users)>1){
					// CAS PARTICULIOER FENIGLIO :(
					if(strcmp($data[UPDATETCL_nom], 'FENOGLIO')==0){
							$args = array(
								'meta_query' => array(
										array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $TCL_clubID),
										array('key' => 'last_name','value' => 'FENOGLIO'),
									),
								'numberposts' =>-1,
						);
						$users  = get_users( $args );
						if(count($users)!=1){
							$txt.='<br>Erreur avec FENOGLIO :'.count($users) .'Fenoglio trouvés';
							$valid = false;
						}
						else{
							$txt.='<br>FENOGLIO : problème contourné ;)';
						}
					
					}//Fin traitement Fenoglio
					else{
						$txt.='<br>Erreur: '.count($users).' joueurs trouvés pour cette ligne<br>'.$data[UPDATETCL_prenom].' '.$data[UPDATETCL_nom];
						$valid = false;
					}
					
				}
				if(count($users)<1){
					$txt.='<br>Erreur: aucun joueur trouvé pour cette ligne<br>';
					$valid = false;
				}
				
				
				if($valid){
					
					$user_id = $users[0]->ID;
					$user_info = get_userdata($user_id);
					
					$clubs = get_user_meta($user_id, TENNISDEIF_XPROFILE_idclubs,true );
					if(in_array($TCL_clubID, $clubs)){				
						$txt .= 'User Trouvé (' . $user_id . ')' . $user_info->last_name . ' ' . $user_info->first_name . '<br>';
						$txt .= TennisDefi_updateTCL_userData($data, $user_id, $user_info,$TCL_clubID, $row);
					}
					else
					{
						$txt .= 'Erreur : Le joueur treouvé '. $user_info->last_name . ' ' . $user_info->first_name . ' n\'est pas AU TCL<br>';
					}
					
				}//joueur valide, ID trouvé
					
			}// fin le joueur existe
				
			
			
				
			// affichage
			//---------------------
			echo"<tr><td>$row</td>";
			
				for ($c=0; $c < $num; $c++) {
					echo "<td>$data[$c]</td>";
				} // fin d'une ligne
				echo "<td>$txt</td>";
			echo '</tr>';
			
			// FINT RAITEMENT PAR LIGNE DU FICHIER
			// ============================================
			
			}// fin lecture de chaque ligne
			fclose($handle);
			}
			

			echo"</tbody></table>";
			
		// On recharge la page pour le tour d'après	
		if($next_run < 50)	
			echo "<script> window.location.reload(); </script>";
	
	
}

