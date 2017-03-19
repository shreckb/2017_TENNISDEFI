<?php 

/*! \file
     \brief Contient les fonctions pour le palmares
     		Affichage Nombre de Partenaires du joueurs
     		AJAX: Passerelle entre Page callback et fonction
    
    Details.
*/


//========================================
/*! \brief  affichage login pour Francis :)
 * 
 */
//========================================
// Permet de definir les seuils de partenaires
function getCSV_customPalmaresForLogin($idClub){
	
	$current_user = wp_get_current_user();
	$current_club = get_user_meta($current_user->ID, TENNISDEIF_XPROFILE_idClub, true);
	
	$args = array (
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $current_club
					)
			),
			'post_type' => 'palmares',
			'numberposts' => - 1,
			'meta_key' => TENNISDEIF_XPROFILE_rang,
			'orderby' => 'meta_value_num',
			'order' => 'ASC'
	);
	
	$postPalmares = get_posts($args);
	
	
	
	
	/// output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=data.csv');
	
	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');
	
	// output the column headings
	fputcsv($output, array('login', 'Nom', 'Prenom', 'email'));
	
	// fetch the data
	foreach ( $postPalmares as $lignePalmares ) {
	
		$id_joueur = get_post_meta ( $lignePalmares->ID, TENNISDEFI_XPROFILE_idjoueur, true );
		$user = get_userdata ( $id_joueur );
		write_log($user);
		$row = array($user->user_login, $user->user_lastname, $user->user_firstname, $user->user_email);
		fputcsv($output, $row);
	
	
	
	}
	
	 
}

//========================================
/*! \brief  Parametre d'affichage du nombre de partenaire: Seuils
 */
//========================================
// Permet de definir les seuils de partenaires 
function get_Palmares_NBPartenaire_Seuils_arra(){
	$Palmares_NBPartenaire_Seuils_array =array(4, 10, 20); 

	return $Palmares_NBPartenaire_Seuils_array;
}


// permet d'afficher le texte sur l'image des balles dans la palmares
function  palmares_showNbpartenaire_linkTxt($Nb, $Palmares_NBPartenaire_Seuils_array){
	$output = '';
	if($Nb< $Palmares_NBPartenaire_Seuils_array[0])
		$output.="Le joueur a moins de $Palmares_NBPartenaire_Seuils_array[0] partenaires";
	else if( $Nb< $Palmares_NBPartenaire_Seuils_array[1])
		$output.= "Le joueur a entre $Palmares_NBPartenaire_Seuils_array[0] et $Palmares_NBPartenaire_Seuils_array[1] partenaires";
	else if( $Nb< $Palmares_NBPartenaire_Seuils_array[2])
		$output.="Le joueur a entre $Palmares_NBPartenaire_Seuils_array[0] et $Palmares_NBPartenaire_Seuils_array[1] partenaires";
	else
		$output.="Le joueur a plus de  $Palmares_NBPartenaire_Seuils_array[2] partenaires";
	
	return $output;
}


function palmares_showNbpartenaire_legende_BW(){
	
	$Palmares_NBPartenaire_Seuils_array = get_Palmares_NBPartenaire_Seuils_arra();
	//echo palmares_showNbpartenaire_BW($Palmares_NBPartenaire_Seuils_array[0]-1,$Palmares_NBPartenaire_Seuils_array).": Le joueur a moins de $Palmares_NBPartenaire_Seuils_array[0] partenaires<br>";
	$txt =  palmares_showNbpartenaire_BW($Palmares_NBPartenaire_Seuils_array[0]+1,$Palmares_NBPartenaire_Seuils_array).": Le joueur a entre $Palmares_NBPartenaire_Seuils_array[0] et $Palmares_NBPartenaire_Seuils_array[1] partenaires<br>";
	$txt .= palmares_showNbpartenaire_BW($Palmares_NBPartenaire_Seuils_array[1]+1,$Palmares_NBPartenaire_Seuils_array).": Le joueur a entre $Palmares_NBPartenaire_Seuils_array[1] et $Palmares_NBPartenaire_Seuils_array[2] partenaires<br>";
	$txt .=palmares_showNbpartenaire_BW($Palmares_NBPartenaire_Seuils_array[2]+1,$Palmares_NBPartenaire_Seuils_array).": Le joueur a plus de  $Palmares_NBPartenaire_Seuils_array[2] partenaires<br>";

	return $txt;
}

//========================================
/*! \brief  Affichage de l'icone du nombre de partenaire
 */
//========================================

function palmares_showNbpartenaire($Nb,$Palmares_NBPartenaire_Seuils_array){
	// Parametre d'affichage du nobre de partenaire
	//$seuil_partenaire_bas  = 4;
	//$seuil_partenaire_haut = 10;
	//$seuil_partenaire_tresHaut = 20;
	$img_couleur = '<img src="'.get_bloginfo('stylesheet_directory') .'/images/icon-balle-rouge.png" alt="" height="20" width="20">';
	$img_nb = '<img src="'.get_bloginfo('stylesheet_directory') .'/images/icon-balle-gris.png" alt="" height="20" width="20">';
	 
	$output = '';
	if($Nb< $Palmares_NBPartenaire_Seuils_array[0]){
		$txt = palmares_showNbpartenaire_linkTxt($Nb, $Palmares_NBPartenaire_Seuils_array);
		$output.='<a title="'.$txt.'">'.$img_nb.$img_nb.$img_nb.'</a>';
	}
	else if( $Nb< $Palmares_NBPartenaire_Seuils_array[1]){
		$txt = palmares_showNbpartenaire_linkTxt($Nb, $Palmares_NBPartenaire_Seuils_array);
		$output.='<a title="'.$txt.'">'.$img_couleur.$img_nb.$img_nb.'</a>';
	}
	else if( $Nb< $Palmares_NBPartenaire_Seuils_array[2]){
		$txt = palmares_showNbpartenaire_linkTxt($Nb, $Palmares_NBPartenaire_Seuils_array);
		$output.='<a title="'.$txt.'">'.$img_couleur.$img_couleur.$img_nb.'</a>';
	}
	else{
		$txt = palmares_showNbpartenaire_linkTxt($Nb, $Palmares_NBPartenaire_Seuils_array);
		$output.='<a title="'.$txt.'">'.$img_couleur.$img_couleur.$img_couleur.'</a>';
		}

	return $output;
}

//========================================
/*! \brief  Affichage de l'icone du nombre de partenaire Noir et blanc
 */
//========================================

function palmares_showNbpartenaire_BW($Nb,$Palmares_NBPartenaire_Seuils_array){
	// Parametre d'affichage du nobre de partenaire
	//$seuil_partenaire_bas  = 4;
	//$seuil_partenaire_haut = 10;
	//$seuil_partenaire_tresHaut = 20;
	

	$img_couleur = '<img src="'.get_bloginfo('stylesheet_directory') .'/images/icon-balle-rouge.png" alt="" height="20" width="20">';
	//$img_nb = '<img src="'.get_bloginfo('stylesheet_directory') .'/images/image_balle_NB.png" alt="" height="20" width="20">';

	$output = '';
	if($Nb< $Palmares_NBPartenaire_Seuils_array[0])
		$output.='<a alt="Niveau du joueur"></a>';
	else if( $Nb< $Palmares_NBPartenaire_Seuils_array[1])
		$output.='<a alt="Niveau du joueur">'.$img_couleur.'</a>';
	else if( $Nb< $Palmares_NBPartenaire_Seuils_array[2])
		$output.='<a alt="Niveau du joueur">'.$img_couleur.$img_couleur.'</a>';
	else
		$output.='<a alt="Niveau du joueur">'.$img_couleur.$img_couleur.$img_couleur.'</a>';


	return $output;
}


// ===========================================
// Affihage AJAX 
//==============================================

// **********************
// Affichage info sur le joueur
//  *******************
add_action('wp_ajax_nopriv_Palamares_showUserInfo', 'Palamares_showUserInfo');
add_action('wp_ajax_Palamares_showUserInfo'       , 'Palamares_showUserInfo');
//========================================
/*! \brief  Met à jour l'email du jouer
*/
//========================================
function Palamares_showUserInfo(){
	// Current user club ? (pa celui du joueur vu)
	$current_user = wp_get_current_user();
	$user_idclub =  get_the_author_meta( TENNISDEIF_XPROFILE_idClub, $current_user->ID ) ;
	
   
    $id_joueur_str = encrypt_decrypt('decrypt', $_REQUEST['id']);
    if(!$id_joueur_str){
        echo "Ce joueur n'existe pas";
        die;
    }
    else
    $id_joueur = (int)    $id_joueur_str;  
    
    $user_info = get_userdata($id_joueur);
   
    
    $avatar_link = get_avatar( $id_joueur, 120);
    $nom    =  $user_info->first_name;
    $prenom =  $user_info->last_name;
    $url_profilePage = bp_core_get_user_domain( $id_joueur ).'/profile';
    //$Nb_partenaires = get_user_meta($id_joueur , TENNISDEIF_XPROFILE_nbpartenaires,true);
    $ID_currentuse_palmares = getUserPalmaresID($id_joueur, $user_idclub);
    $Nb_partenaires = get_post_meta ( $ID_currentuse_palmares, TENNISDEIF_XPROFILE_nbpartenaires, true );
    
    
	
    
    
    //$update = get_usermeta( wp_get_current_user()->ID, 'bp_latest_update' );
    $date_activite = bp_get_user_last_activity ($id_joueur);

  //picking up wordpress date time format
   $date_format = get_option('date_format');
 
   //converting the login time to wordpress format
    $last_update = mysql2date($date_format, $date_activite, true);

    ?>
<div style="width:400px; height:400px;padding:20px">
	<div align="left" style="margin:5px;"> <?php echo $avatar_link; ?>  </div>
	<div align="left" style="margin:10px 0 10px 0"> <h3><?php echo "<b> ".$nom." ".$prenom.""; ?></h3></div>
	<div align="left">
	<?php
			$url_image = get_bloginfo ( 'stylesheet_directory' ) . '/images/icon-balle-rouge.png';
		?>
	  	<img src="<?php echo $url_image?>" height="20" width="20">
	   Nombre de partenaires : <?php echo $Nb_partenaires; ?>
	 </div>

	<div align="left">
		<?php
			$url_image = get_bloginfo ( 'stylesheet_directory' ) . '/images/icon-recherche-partenaires.png';
		?>
	  	<img src="<?php echo $url_image?>" height="20" width="20">
				
	   <?php
			if (get_Xprofilefield_recherchePartenaires ( $id_joueur ) == 'oui')
				echo "Recherche plus de partenaires : oui";
			else
				echo "Recherche plus de partenaires : non";
		?>
	</div>
	
	<div align="left">
	  	<?php
			$url_stat = get_bloginfo ( 'url' );
			$url_stat .= "/mon-tennis-defi/mes-statistiques?IDstat=" . encrypt_decrypt ( 'encrypt', $id_joueur );
			$url_image = get_bloginfo ( 'stylesheet_directory' ) . '/images/icon-statistiques.png';
		?>
	  	<a href="<?php echo $url_stat?>" title="Statistiques"> 
	  		<img src="<?php echo $url_image?>" alt="statistiques" height="20"
				 width="20"> Voir les statistiques du joueur
		</a>
	</div>
	
		<div align="left">	
			<a href="<?php echo $url_profilePage?>"  title="Informations sur le joueur">
	            <img src="<?php echo get_bloginfo('stylesheet_directory') .'/images/icon-infos-joueur.png'; ?>"  height="20" width="20">
	            Informations et contact
            </a>
		
		</div>
		
	
	

    
    <div align="left">
	    <?php // Ajout bouton nevoyer un message
	    if($id_joueur != $current_user->ID){
		
			echo  buddypress_get_send_private_message_button($id_joueur);
		echo '<a href="'.buddypress_get_send_private_message_url($id_joueur).'">Envoyer un message à ce joueur </a>';
		}
	     ?>
</div>
    
    	<div align="left"><p>
	<?php
	if ($last_update)
		echo  "Dernière activité sur le site : " . $last_update ;
	else
		echo  "Pas de connexion récente sur le site";
	?>
    </p></div>
</div>    
<?php

  die;

}


add_action( 'wp_ajax_Palamares_AdminsitrationEmail'   , 'Palamares_updateUserEmail');
add_action('wp_ajax_nopriv_Palamares_showUserInfo2', 'Palamares_updateUserEmail');

//========================================
/*! \brief  Mise à jour de l'email du joueur par AJAX depuis la palamres
 */
//========================================

function Palamares_updateUserEmail(){
	
	$id_joueur = (int) encrypt_decrypt('decrypt', $_REQUEST['id']);

	$email = $_REQUEST['value'];
	$aux = get_userdata( $id_joueur );
	
	write_log("ID = $id_joueur");
	
	if(!$aux){
		echo "Ce joueur n'existe pas";
		die;
	}
	else{

		//if(update_user_meta( $id_joueur, 'user_email', $email)){
			//echo $email.'-OK';
		//}
		
		if(wp_update_user( array( 'ID' => $id_joueur, 'user_email' => $email ))){
			echo $email;
		}
		else
			echo "échec";
	
	write_log("EMAIL RECU");
	die;
	}//le joueur existe
}

//========================================
/*! \brief  Affichage de différents palamares
 */
//========================================

//Affiche le selecteur avec la listes des palamres disponible pour le joueurs
function palamares_get_listOf($id_club, $id_user){


	$txt = '<SELECT id="palmares_lisOf" name="palmares_type">';
	
	
	// Palamres du club
	$txt .= "<optgroup label ='Palmarès du club'>";
		$txt .= "<option value='".TENNISDEFI_PALMARES_TYPE_all."'>Tous les joueurs </option>";
		$txt .= "<option value='".TENNISDEFI_PALMARES_TYPE_actif."'>Tous les joueurs Actifs</option>";
	$txt .= "</optgroup>";
	// Partenaires
	$txt .= "<optgroup label ='Palmarès Personnalisés'>";
		$txt .= "<option value='".TENNISDEFI_PALMARES_TYPE_friends."'>Vos partenaires</option>";
	$txt .= "</optgroup>";
	//TOurnois
	
		// LISTRE DES TOURNOIS DU JOUEURS
			$userTournois_id = tennisdefi_tournois_getTournoiID_ofUser($id_user, $id_club);
			if(is_array($userTournois_id)){
				$txt .= "<optgroup label ='Tournois'>";
				//echo "Le joueu à : ".count($userTournois_id)." tournois";
				foreach($userTournois_id as $id_tournoi){
					if (get_post_type ( $id_tournoi ) != 'tournoi')
						continue;
					$titre_tournoi = get_post_meta ( $id_tournoi, TENNISDEIF_TOURNOI_nomTournoi, true );
					$ID_encrypted = encrypt_decrypt ( 'encrypt', $id_tournoi );
					$txt .= "<option value='$ID_encrypted'>$titre_tournoi</option>";	
				}
				
				$txt .= "</optgroup>";
			}// il y a des tournois
			
	
	
	$txt .=  '</SELECT>';
	
	return $txt;
}

function palamares_get_TableHeader($isUserAdminInCLub, $DisplayTournoi,$for_printing=false){

	//Pour le printing du palamres (pdf...)
	if($for_printing){
		//pas d'affichage d'email ?  
		$array_header = '<table border="1" ><thead><tr align="center" bgcolor="#2d678c">
					<th>Rang</th>
					<th>Nom </th>
					<th>Prénom</th>
					<th>Classement</th>
				</tr></thead><tbody>';
		
		return $array_header;
	}
		// ***** TABLE ******
		// ligne de titre
		echo '<table id="table_palmares" class="dt-responsive no-wrap stripe table_tennisdefi">
                    <thead><tr>
                    <th>Rang</th>
                    <th>Nom </th>
                    <th>Prénom</th>
     				<th>Classement</th>
     				<th>Partenaires</th>
                    <th></th>';
	
		if($isUserAdminInCLub)
			echo '<th>Email</th>';
	
		
			echo '</tr></thead><tbody> ';
			

}


function palmares_get_TableFooter($for_printing=false){
	$footer = '</tbody></table>'; 
	
	if($for_printing)
		return $footer;
	else
		echo $footer;
}

// 1 ligne du joueur : Colonne commune à tous les palmares : display Info, classement, Actions demande amis etc...
function palmares_get_row_corps_palamres($id_joueur, $id_ignePalmares , $rang_currentuser, $iscurrentUser, $isUserAdminInCLub,$for_printing=false){


	//$iscurrentUser : true/false :  permet une action  particulière sur le joueur
	
	
	
	
	// Info sur le joueur
	$user = get_userdata ( $id_joueur );
	// $ID_currentuse_palmares = getUserPalmaresID($id_joueur, $current_club);
	$nbpartenaire_currentuser = get_post_meta ( $id_ignePalmares, TENNISDEIF_XPROFILE_nbpartenaires, true );
		
	$user_classement = xprofile_get_field_data ( TENNISDEIF_XPROFILE_classement, $id_joueur );
	$user_classement_top = xprofile_get_field_data ( TENNISDEIF_XPROFILE_exclassement, $id_joueur );
	$nbmatch_declare = get_post_meta ($id_ignePalmares , TENNISDEFI_XPROFILE_nbMacth, true );
	
	//Classement
	if(strcmp($user_classement_top,'') != 0)
		$classement_dispalyed ="$user_classement($user_classement_top)";
	else
		$classement_dispalyed = $user_classement;
		
			
			
	// Recherche de Partenaires
	$recherche_partenaire = '';
	if(get_Xprofilefield_recherchePartenaires($id_joueur)=='oui'){
			$recherche_partenaire =  '<a title="Ce joueur recherche des partenaires" style="color:#E2001A"><img src="'.get_bloginfo('stylesheet_directory') .'/images/icon-recherche-partenaires.png" height="15" width="15"></a>';
	
	}
		//Affichage
		if($for_printing){
			if($rang_currentuser %2)
				$array_data = "<tr>";
			else 
				$array_data = '<tr bgcolor="#eaf3f3">';
			
			$array_data .= "<td> $rang_currentuser$recherche_partenaire</td>";
			$array_data .= "<td> ".strtoupper($user->user_lastname)."</td>";
			$array_data .= "<td> $user->user_firstname</td>";
			$array_data .= "<td> $classement_dispalyed</td>";
			//$array_data .= "<td>".palmares_showNbpartenaire($nbpartenaire_currentuser,get_Palmares_NBPartenaire_Seuils_arra())."</td>";
			$array_data .= "</tr>";
			return $array_data;
		} // fin retour pour impression
		else{
			echo '<tr>';
			// Colonne: Rang
			echo "<td>$rang_currentuser$recherche_partenaire</td>";
			
			// Colonne : Nom Prenom
			if($iscurrentUser)
			{
				echo '<td><b>'.strtoupper($user->user_lastname).'</b></td>';
				echo '<td><b>'.$user->user_firstname.'</b></td>';
			}
			else{
				echo '<td>'.strtoupper($user->user_lastname).'</td>';
				echo '<td>'.$user->user_firstname.'</td>';
			}
			
			//Colonne: Classement
			echo "<td>$classement_dispalyed</td>";

					//Colonne : Nombre de partenaires
					echo '<td>';
					echo palmares_showNbpartenaire($nbpartenaire_currentuser,get_Palmares_NBPartenaire_Seuils_arra());
					echo '</td>';
			
					// Colonnes toutes icones
					echo '<td>';
			
					// Amis
					if(function_exists( 'bp_add_friend_button' ))
						echo  bp_get_add_friend_button($user->ID);
			
			
						$url_ajaxfile = admin_url( 'admin-ajax.php' );
						$url_ajaxfile.= "?action=Palamares_showUserInfo&id=".encrypt_decrypt('encrypt', $user->ID);
			
						$url_stat =  get_bloginfo('url');
						$url_stat.= "/mon-tennis-defi/mes-statistiques?IDstat=".encrypt_decrypt('encrypt', $user->ID);
			
						// Plus d'info
						echo '<a class="cboxElement_palmares" href="'.$url_ajaxfile.'" title="Informations sur le joueur">
	                  <img src="'.get_bloginfo('stylesheet_directory') .'/images/icon-infos-joueur.png"  height="20" width="20"></a>';
			
			
						// Statistiques
						echo '<a class="cboxElement_stats" href="'.$url_stat.'" title="Voir les statistiques du joueur">
	             <img src="'.get_bloginfo('stylesheet_directory') .'/images/icon-statistiques.png" alt="statistiques" height="20" width="20"> </a> ';
			
						//message prive
						echo  buddypress_get_send_private_message_button($id_joueur);
						echo ' ';
			
						echo'</td>';  // fin case plus d'info etc...
			
						// Si Admin on affiche les emails
						if($isUserAdminInCLub){
							$id_encrypted = encrypt_decrypt('encrypt', $id_joueur);
							echo '<td>';
							echo '<div class="edit" id="'.$id_encrypted.'">'.$user->user_email.'</div>';
							echo '</td>';
						}
			
						echo'</tr>';
			
			
		}//fin affichage HTML , pas d'impression
		
		
	
	
}


function palmares_displayAll($current_user_ID, $id_club, $isUserAdmin,$palmares_categorie, $onlyActif,$for_printing=false){
	
	
	// si $forPrinting = True , on retourne les info sous forme de tableau
	// sinon on les affiche directment
	
	

	// $cat = homme /femme / Mixe => foir functions_post pour la def
	if($onlyActif==false)
		$Info ="S'affiche ici tous les joueurs inscrits dans votre club";
	else
		$Info ="S'affiche ici tous les joueurs inscrits dans votre club ayant au moins un match déclaré ces 12 derniers mois";
	
	//Recherche des joueurs : Avec activité ou pas
	if($onlyActif==false)
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
	else 
		$args = array (
				'meta_query' => array (
						'relation' => 'AND',
						array (
								'key' => TENNISDEFI_XPROFILE_lastdeclaration,
								'value' => date("Y-m-d",strtotime("-12 months")),
								'compare' => '>=',
								'type' => 'date'),
							
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
	
		
		
		// Info
		if($for_printing)
			$array_palmares['info'] = $Info;
		else
			echo $Info;

			
		//Entete de la table
		$array_palmares['data'] = palamares_get_TableHeader($isUserAdmin, $DisplayTournoi=false,$for_printing);
		
		
		
	// Corps de la table
	$postPalmares = get_posts ( $args );
	$rang_fictif = 0;
	foreach ( $postPalmares as $lignePalmares ) {
	
		$id_joueur = get_post_meta ( $lignePalmares->ID, TENNISDEFI_XPROFILE_idjoueur, true );

		// GEstion du sexes
		if ($palmares_categorie != TENNISDEFI_PALMARES_CAT_MIXTE) {
			$sexe = xprofile_get_field_data ( TENNISDEIF_XPROFILE_sexe, $id_joueur );
			if (($palmares_categorie == TENNISDEFI_PALMARES_CAT_HOMME) && ($sexe != 'masculin') )
				continue; // on passe tout de suite au joueur suivant
			if (($palmares_categorie == TENNISDEFI_PALMARES_CAT_FEMME) && ($sexe != 'féminin') )
				continue; // on passe tout de suite au joueur suivant
		}
			
		// GEstion du rang
		$rang_fictif ++;
		
			
		
		if($id_joueur == $current_user_ID)
			$iscurrentUser = true;
		else 
			$iscurrentUser = false;
		
			
		// colonnes	
		$array_palmares['data'] .= palmares_get_row_corps_palamres($id_joueur, $lignePalmares->ID , $rang_fictif, $iscurrentUser, $isUserAdmin,$for_printing);

	
		
	
	}
	
	
	//Pieds de table
		$array_palmares['data'] .=palmares_get_TableFooter($for_printing);
	
	
	return $array_palmares;
}

// Affiche le palmares de mes amis seulement
function palmares_displayFriendAndMe($id_club, $current_user_ID,$isUserAdmin, $palmares_categorie, $for_printing=false){
	
	$Info = "S'affiche ici le palmarès de vos partenaires";
	
	
	// ***************
	//Listes des amis (/!\dans tous les clubs)
	$friends_id = friends_get_friend_user_ids($current_user_ID );
	$friends_id[] = $current_user_ID;
	
	// obtention rang et
	$friend_id_keeped=array();
	$friend_rang_keeped=array();
	$friend_palamresID_keeped=array();
	
	// organisation des donnes pour tri selon  le rang
	foreach($friends_id as $friend_id){
		//Le joueur dans le palmares du club ?
		$ID_currentuse_palmares = getUserPalmaresID($friend_id, $id_club);
		if(!$ID_currentuse_palmares)
			continue; // on saute à l'ID suivant si l'ami n'est pas dans ce club
	
			// organisation des donnes pour tri selon  le rang
			$friend_id_keeped[]= $friend_id;
			$friend_palamresID_keeped[]= $ID_currentuse_palmares;
			$friend_rang_keeped[]= get_post_meta ( $ID_currentuse_palmares, TENNISDEIF_XPROFILE_rang, true );
	}
	//  tri selon le rang
	array_multisort($friend_rang_keeped,$friend_id_keeped,  $friend_palamresID_keeped);
	
	
	
	//****************
	// Info
		if($for_printing)
			$array_palmares['info'] = $Info;
		else
			echo $Info;
	
	//Entete de la table
	$array_palmares['data'] = palamares_get_TableHeader($isUserAdmin, $DisplayTournoi=false, $for_printing);
	
	$rang_fictif = 0;
	foreach ( $friend_palamresID_keeped as $Palmares_ID ) {
	
		$id_joueur = $friend_id_keeped[$rang_fictif];
		$id_ignePalmares = $friend_palamresID_keeped[$rang_fictif];
	
		// GEstion du sexes
		if ($palmares_categorie != TENNISDEFI_PALMARES_CAT_MIXTE) {
			$sexe = xprofile_get_field_data ( TENNISDEIF_XPROFILE_sexe, $id_joueur );
			if (($palmares_categorie == TENNISDEFI_PALMARES_CAT_HOMME) && ($sexe != 'masculin') )
				continue; // on passe tout de suite au joueur suivant
				if (($palmares_categorie == TENNISDEFI_PALMARES_CAT_FEMME) && ($sexe != 'féminin') )
					continue; // on passe tout de suite au joueur suivant
		}
			
		// GEstion du rang
		$rang_fictif ++;
		
		// Affichage colonne du palmares
		if($id_joueur == $current_user_ID)
			$iscurrentUser = true;
		else
			$iscurrentUser = false;
		
		$array_palmares['data'] .= palmares_get_row_corps_palamres($id_joueur, $id_ignePalmares , $rang_fictif, $iscurrentUser, $isUserAdmin,$for_printing);
	
	}
	
	//Pieds de table
	$array_palmares['data'] .= palmares_get_TableFooter($for_printing);
	
	return $array_palmares;
	
}



function palmares_displayTournoi($current_user_ID, $current_club, $id_tournoi, $for_printing=false){
	
	//<br> Pour gérer vos tournois, rendez vous ICI";
	
	$id_club_tournoi = get_post_meta ( $id_tournoi, TENNISDEIF_XPROFILE_idClub, true );
	if($id_club_tournoi != $current_club)
		return;
	
	//Recupération des infos du tournoi
	$tournoi =  tennisdefi_tournois_getTournoi($id_tournoi);
		
	
	//$Info = "S'affiche ici le palmarès du tournoi.<br>";
		$Info ="<b><u>Tournoi :</u></b>".$tournoi['titre']." <br>";
		$Info .= "<b><u>description</u></b> : ".$tournoi['description'];
		

	//
	// Entete
	if($for_printing)
		$array_palmares['info'] = $Info;
	else	
	echo $Info;
	
	//Entete de la table
	$array_palmares['data'] = palamares_get_TableHeader($isUserAdmin=false, $DisplayTournoi=true,$for_printing);
	
	
	
	for ($k=0; $k<$tournoi["NBjoueur"] ; $k++){
		$rang_fictif = $k+1;
		$tournoi_id_user  =  $tournoi["userID"][$k];
		$ID_currentuse_palmares = getUserPalmaresID($tournoi_id_user, $current_club);
		
		// Affichage colonne du palmares
		if($tournoi_id_user == $current_user_ID)
			$iscurrentUser = true;
			else
				$iscurrentUser = false;
		
		$array_palmares['data'] .= palmares_get_row_corps_palamres($tournoi_id_user, $ID_currentuse_palmares , $rang_fictif, $iscurrentUser, $isUserAdmin,$for_printing);
		
	}// fin boucle sur le users du tounroi
	
	//Pieds de table
	$array_palmares['data'] .= palmares_get_TableFooter($for_printing);
	
	
	return $array_palmares;
	
}