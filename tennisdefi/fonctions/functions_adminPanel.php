<?php

/*! \file
     \brief Contient les fonctions de gestions d'affichage coté admin Panel
*/






// Ajoute un menu et un sous menu
add_action('admin_menu', 'add_TennisDefi_menu');
function add_TennisDefi_menu(){
	global $TENNISDEFI_hook_to_menu_pageStat ;
	global $TENNISDEFI_hook_to_menu_pageUpdatePonctuel ;
	global $TENNISDEFI_hook_to_menu_pageAdminGEstionClub;
	// Menu Top Level (tennisdefi)
	$img_url = 'dashicons-chart-area'; //get_bloginfo('stylesheet_directory') .'/images/default-logo-orange_format_4-3_200x267.png';
	add_menu_page( 'TennisDefi Menu', 'tennisdefi', 'manage_options', 'Tennisdefi_menu_slug', 'TennisDefi_menu_page', $img_url, 6);

	// Sous menu PAGE 2 : gestion admin clubs(voir page : function_adminPanel.php)
	$TENNISDEFI_hook_to_menu_pageAdminGEstionClub = add_submenu_page( 'Tennisdefi_menu_slug' , 'Tennisdefi Première page',
				 'Gestion des Membres (Admin,Resultat)' , 'manage_options' , 'Tennisdefi_smenu_gestionAdminClub_slug' , 'TennisDefi_menu_pageAdminClub');
	
	// Sous menu PAGE Stats (voir fichier functions_adminPanel_stats.php)
	$TENNISDEFI_hook_to_menu_pageStat = add_submenu_page( 'Tennisdefi_menu_slug' , 'Tennisdefi Première page',
				 'Statistiques' , 'manage_options' , 'Tennisdefi_smenu_STAT_slug' , 'TennisDefi_menu_page_STAT');
	
	// Sous menu PAGE 3 (voir ci dessous)
	$TENNISDEFI_hook_to_menu_pageUpdatePonctuel = add_submenu_page( 'Tennisdefi_menu_slug' , 'Tennisdefi Première page',
	'Update ponctuel' , 'manage_options' , 'Tennisdefi_smenu_updatePonctuel_slug' , 'TennisDefi_menu_updatePonctuel');
	
	// Sous menu PAGE 4 (voir ci dessous)
	//$TENNISDEFI_hook_to_menu_pageImportTCLjeune = add_submenu_page( 'Tennisdefi_menu_slug' , 'Tennisdefi upload joueurs TCL jeunes',
		//	'Import TCL jeune' , 'manage_options' , 'Tennisdefi_smenu_importTCLJeune_slug' , 'TennisDefi_menu_importTCLJeune');
	
}

//*****************************************
//===========================================
// Page 1 --- Principale
function TennisDefi_menu_page(){
	
	echo "<h1>Tennis DEFI: Page de garde </h1>";
	//TennisDefi_menu_page_STAT();

//bp_is_user_active()
$args = array(
	'number'       => -1,
	'fields'       => 'ids',
		 );
$users = get_users($args);
echo "il y a : ".count($users)."<br>";


/*
	echo "<h4> liste joueurs TCL porche du rang 490 </h4>";
	$args = array (
			'meta_query' => array (
					array ('key' => TENNISDEIF_XPROFILE_idClub,'value' => 9621),
					array ('key' => TENNISDEIF_XPROFILE_rang,'value' => 490),
								),
			'post_type' => 'palmares'
	);
	$postPalmares = get_posts ( $args );
	
	

	foreach($postPalmares as $palmares_ligne){
		
		$Id_user = (int)get_post_meta ( $palmares_ligne->ID, TENNISDEFI_XPROFILE_idjoueur     , true );
		$rang     = (int)get_post_meta ( $palmares_ligne->ID, TENNISDEFI_XPROFILE_rang        , true );
		$user_info = get_userdata($Id_user);

		$nom    =  $user_info->user_lastname;
		$prenom =  $user_info->user_firstname;
		
	
		echo "$rang   ;   $nom $prenom (Id=$Id_user)";
	}

*/

}


//*******************************************
//*******************************************


function TennisDefi_menu_importTCLJeune(){
	
	
	// set the PHP timelimit to 10 minutes
	set_time_limit(600);

	
	$text = "Ceci est un accent aigu : <é> - grave : <è> - circonflexe : <ê>.";
	
	echo 'Original : ', $text, "<br>";
	echo 'TRANSLIT : ', iconv("UTF-8", "ISO-8859-1//TRANSLIT", $text), "<br>";
	echo 'IGNORE   : ', iconv("UTF-8", "ISO-8859-1//IGNORE", $text), "<br>";
	echo 'Brut     : ', iconv("UTF-8", "ISO-8859-1", $text), "<br>";
	echo "ASCII";
	echo 'TRANSLIT : ', iconv("UTF-8", "ASCII//TRANSLIT", $text), "<br>";
	echo 'IGNORE   : ', iconv("UTF-8", "ASCII//IGNORE", $text), "<br>";
	echo 'Brut     : ', iconv("UTF-8", "ASCII", $text), "<br>";
	
	
	echo 'IMport TCL Jeunes<br>';
	echo "<h2> Update d'anciens et de noveaux joueurs</h2>";
	$id_club_to_import = 25030;
	echo "<h3>importation dans le club : ".get_the_title($id_club_to_import)."</h3>";
	
	$file ='../temp/LISTING TENNIS DEFI.csv';
	$handle = fopen($file, "r");
	
	$handle_out_csv = fopen('../temp/sortie_importTCLJeune.csv', 'w');
	if(!$handle_out_csv)
		{
		echo "Impossible d'ecrire le fichier out : '../temp/sortie_importTCLJeune.csv'";
		return -1;
	}else{
		echo 'fichier de validation ici : <a href="../temp/sortie_importTCLJeune.csv">lien</a><br><br>'; 
		$data_to_write = array('nom', 'prenom', 'login', 'date de naissance', );
		fputcsv($handle_out_csv, $data_to_write);
	}

	if(!$handle){
		echo "Impossible de lire le fichier<br> <a href=\"$file\">lien</a>";
		return -1;
	}
	
		if (($handle = fopen($file, "r")) !== FALSE) {
			define("UPDATETCL_sexe",5);
			define("UPDATETCL_nom", 0);
			define("UPDATETCL_prenom", 1);
			define("UPDATETCL_datenaissance", 2);
			define("UPDATETCL_tel", 3);
			define("UPDATETCL_email", 4);

				
	
			// Premiere ligne de titre
			//-------------------------
			$data = fgetcsv($handle, 0, ";");
			$num = count($data);
			$num = 6; // limitation du nombre de colonne
	
			echo "<b>Il y a $num colonnes</b><br>";
			echo'<table id="table_clubs_data" class="dt-responsive no-wrap stripe table_tennisdefi">';
			echo"<thead><tr>";
			for ($c=0; $c < $num; $c++) {
				echo "<th>$data[$c]</th>";
			} // fin d'une ligne
			echo '<th>Traitement</th>';
				
			echo "</tr></thead><tbody>";
		
			// Parcours du fichier
			// -------------------
			while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {	
					// TRAITEMENT PAR LIGNE DU FICHIER
					// ============================================

					$num = count($data); // nombre de champs
					$num = 6;// limitation du nombre de colonne
					$txt = '';
					echo'<tr>';
					for ($c=0; $c < $num; $c++) {
						echo "<td>$data[$c]</td>";
					} // fin d'une ligne
					
					
					// Colonne traitement
					echo "<td>";
					
					
						// Creation du joeuru
						$password = $data[UPDATETCL_datenaissance];
						$email =$data[UPDATETCL_email];
						$username_plain = strtolower($data[UPDATETCL_prenom]).  '.'  .strtolower($data[UPDATETCL_nom]);
						
						
						//$username = iconv("UTF-8","ASCII//TRANSLIT//IGNORE",$username_plain); //$email;
						$str = htmlentities($username_plain, ENT_NOQUOTES, $charset);
						
						$str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
						$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
						$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
						$username =strtolower($str);
						
						//$username = strtr($username_plain, 'é', 'e');
						//$username = strtr($username, "è", 'e');
						/*
						$username = iconv("UTF-8","ASCII//TRANSLIT",$username_plain); //$email;
						
						
						$username = str_replace("'", '', $username);
						$username = str_replace("`", '', $username);
						$username = str_replace("^", '', $username);
						$username = str_replace("",  '', $username);
						*/
						echo "$username_plain ===>  $username <br>";
						if(!validate_username( $username ) )
							echo "<br><b>username Invalide </b><br>";
						
						//continue;
						
						
						//$username = iconv("utf-8","ascii//TRANSLIT",$username_plain); //$email;
						//echo $username.'<br>';
						if(username_exists( $username )){
							$validite_DONTEXIST = false;
							echo "Erreur : Le username existe déja";						
						}
						else {
							
							//echo "creation du jouer : $username et pass : $password et email =$email<br>";
							//write_log("creation du jouer : $username et pass : $password et email =$email");
							//$new_user_id = wp_create_user($username, $password, $email);
							$new_user_id = wp_create_user($username, $password, $email);
							//echo "wp_create: "; echo "<pre>" ; print_r($new_user_id); echo "</pre><br>";	
							//write_log("\t creation du jouer : $username et pass : $password et email =$email => new ID : $new_user_id");
							//if(!$new_user_id){
								echo  " wp_create_user() :" .$new_user_id." <br>";
							//}
						
								
								if(is_wp_error($new_user_id)){
									echo  "Erreur wp_create_user() :".get_error_messages($new_user_id)." <br>";
								}
							
								$data_to_write = array($data[UPDATETCL_nom], $data[UPDATETCL_prenom], $data[UPDATETCL_datenaissance], $username, $new_user_id);
								fputcsv($handle_out_csv, $data_to_write);
								
								
								//attachament au club
								addUserToClub($new_user_id, $id_club_to_import);
								// Mise à jour Info Joueur
								
								//NOm et Prenom
								$update_user_return = wp_update_user( array( 'ID' => $new_user_id,
										'last_name' => $data[UPDATETCL_nom],
										'first_name' => $data[UPDATETCL_prenom] ) );
							
								if ( is_wp_error( $update_user_return ) ) 
									echo "Erreur Nom Prenom  :Impossible de mettre le nom et prenom à jour l'utilisateur <br>";

							// TEL
								if (! xprofile_set_field_data ( TENNISDEIF_XPROFILE_telephone, $new_user_id, $data [UPDATETCL_tel] ))
										echo  'Erreur : Impossible de mettre à jour le tel<br>';
										
							// date de naissance
									//$sexe = xprofile_get_field_data ( TENNISDEIF_XPROFILE_sexe, $user_id ); // / \todoVerif La valeur du champ doit etre setté en fonction du site
									if (strcmp ( $data [UPDATETCL_sexe], 'f' ) == 0) {
										if (! xprofile_set_field_data ( TENNISDEIF_XPROFILE_sexe, $new_user_id, 'féminin' ))
											echo  'Erreur : Impossible de mettre à jour le sexe féminin<br>';
									} else {
										if (! xprofile_set_field_data ( TENNISDEIF_XPROFILE_sexe, $new_user_id, 'masculin' ))
											echo 'Erreur : Impossible de mettre à jour le sexe masculin<br>';
									}
							
						
							
							
						
						
						}//creation du joueur
						
						
							
						
					
					
					//fin colonne traitement
					echo "</td>";
						
					
					
					
					//write_log("LEcture de la ligne $row pour ".$data[UPDATETCL_nom]." ". $data[UPDATETCL_prenom]);
						echo'</tr>';
			}
		
		}
		fclose($handle_out_csv);
		fclose($handle);
		
}