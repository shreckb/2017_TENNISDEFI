<?php

/*
 * ! \file
 * \brief Contient les fonctions pour l'adminsitration de club 
 *
 * Details.
 */



// ========================================
/* ! \brief Administrateur du club
 */
// =========================================
function tennisdefi_getIdAdminCLub($id_club){
	$args = array('meta_query' => array(
			array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $id_club),
			array('key' => TENNISDEFI_XPROFILE_idAdminInClub,'value' => 1),
	),
			'post_type' => 'palmares',
			'numberposts' =>-1,
			);
	
	$postPalmaresAdmin = get_posts($args);
	
	$Id_Admins = array();
	foreach ($postPalmaresAdmin as $lignePalmares){
		$id_joueur = get_post_meta($lignePalmares->ID , TENNISDEFI_XPROFILE_idjoueur , true);
		$Id_Admins[] = $id_joueur;
	}
	return $Id_Admins;
		
}

// ========================================
/* ! \brief Prévention des administrateurs du club 
		qu'un joueur vient d'ajouter le cluyb
 */
// =========================================
function tennisdefi_alerterAdminCLub_newUser($id_user, $id_club, $isInscritption = true){
	// $id_user : le user qui s'inscrit
	// $id_club : id du club
	// $isInscritption : true: inscritpion, false: désinscription
	
	
	// ----------------------------
	// Envoie EMAIL à l'autre joueur
	// ----------------------------
	$newuser_Info  = get_userdata($id_user);
	$newuser_displayname  = ucfirst(strtolower($newuser_Info ->user_firstname))." ".strtoupper($newuser_Info ->user_lastname);
	$newuser_email = 	$newuser_Info->user_email;
		// récupération ID admin du club
	$Id_Admins = tennisdefi_getIdAdminCLub ( $id_club );
	foreach ( $Id_Admins as $Id_admin ) {
		$adminUser_Info = get_userdata ( $Id_admin );
		$adminUser_displayname = ucfirst ( strtolower ( $adminUser_Info->user_firstname ) ) . " " . strtoupper ( $adminUser_Info->user_lastname );
		
		//sujet
		if($isInscritption)
			$sujet = "Tennis Défi : nouveau joueur inscrit dans votre club";
		else
			$sujet = "Tennis Défi : un joueur en moins dans votre club";
		
		
		//header
		$headers [] = "Reply-To:\r\n";
		
		//message
		$message = "Bonjour $adminUser_displayname, \n";
		
		if($isInscritption)
			$message .= "$newuser_displayname ($newuser_email) vient de s'inscrire au palmarès de votre club.\n";
		else 
			$message .= "$newuser_displayname ($newuser_email) vient de se retirer du palmarès de votre club.\n";
			
		
		$message .=  "Votre login : " .$adminUser_Info->user_login. "\n" ;
		$message .=  "Votre club : " . get_post($id_club)->post_title ."\n";
		
		$message .= "L'équipe TENNIS DEFI\n";
		$message .= "<a href=\"http://tennis-defi.com\">tennis-defi.com</a>";
		// envoie
		write_log("******************");
		write_log("$message");
		write_log("******************");

		wp_mail ( $adminUser_Info->user_email, $sujet, nl2br ( $message ), $headers, $attachments = array (), $tags = array (
				'tennisdefi_NewUserInClub') );

		

	}
	
	
}

// ========================================
/* ! \brief Statististiques du club  (A COMPLETER)*/
// =========================================
function tennisdefi_changerRangJoueur($id_user, $id_club, $rang_futur){
	
	 // On borne le déplacement vers une valeur plausible
	$rang_max = get_post_meta($id_club, TENNISDEIF_XPROFILE_nbJoueursClub, true);
	$rang_futur = min($rang_futur, $rang_max);
	$rang_futur = max($rang_futur, 1);
	
	$palamresID_user = getUserPalmaresID($id_user, $id_club);
	$rang_actuel     = get_post_meta($palamresID_user, TENNISDEIF_XPROFILE_rang, true);
	
	
	
	//echo "palamresID_user = $palamresID_user rang actuel = $rang_actuel => rang futur = $rang_futur<br>";
	
	
	
	
	// Si pas de changement : On ne fait rien
	if($rang_actuel == $rang_futur)
		return ;
		
		
	
	// Le joueur monte dans  le classement (ex on passe du N°5 au N°2)
	if($rang_futur < $rang_actuel){
		
		// ajoute au rang des utilisateurs du club +1 entre $rang_actuel et $rang_futur
		// y compris $rang_actuel et futur
		// le rnag stop n'est pas modifé
		$args = array('posts_per_page' => -1,
				'meta_query' => array(
						'relation' => 'AND',
						array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $id_club),
						array('key' 			=> TENNISDEIF_XPROFILE_rang,
								'value' 		=> array( $rang_futur, $rang_actuel),
								'type'  		=> 'numeric',
								'compare' => 'BETWEEN'
						)
				),
				'post_type' => 'palmares');
		
		$palmares = get_posts($args);
		foreach($palmares as $ligne_palmares){
			$previous_rang = get_post_meta($ligne_palmares->ID, TENNISDEIF_XPROFILE_rang,true);
			update_post_meta($ligne_palmares->ID, TENNISDEIF_XPROFILE_rang, $previous_rang+1);
			//echo "rang : $previous_rang => ".($previous_rang+1)."<br>";
		}
		

		
		
	}// le joueur monte
	else{
		
		//echo "rang actuel = $rang_actuel => rang futur = $rang_futur<br>";
		// ajoute au rang des utilisateurs du club +1 entre $rang_actuel et $rang_futur
		// y compris $rang_actuel et futur
		// le rnag stop n'est pas modifé
		$args = array('posts_per_page' => -1,
				'meta_query' => array(
						'relation' => 'AND',
						array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $id_club),
						array('key' 			=> TENNISDEIF_XPROFILE_rang,
								'value' 		=> array( $rang_actuel, $rang_futur),
								'type'  		=> 'numeric',
								'compare' => 'BETWEEN'
						)
				),
				'post_type' => 'palmares');
		
		$palmares = get_posts($args);
		foreach($palmares as $ligne_palmares){
			$previous_rang = get_post_meta($ligne_palmares->ID, TENNISDEIF_XPROFILE_rang,true);
			update_post_meta($ligne_palmares->ID, TENNISDEIF_XPROFILE_rang, $previous_rang-1);
			//echo "rang : $previous_rang => ".($previous_rang-1)."<br>";
		}
		
		
		
		
		
	} // le joueur descend
	
	
	// nouvelle place du jouer
	update_post_meta($palamresID_user, TENNISDEIF_XPROFILE_rang, $rang_futur);
	//update_post_meta($palamresID_user, TENNISDEIF_XPROFILE_rang, 3);
	//echo "Le joueur devient rang $rang_futur<br>";
	

	
}

// ========================================
/* ! \brief Statististiques du club  (A COMPLETER)*/
// =========================================
function get_InscriptionClubBetween($current_club){
	// retourne un tableau  avec legende et valeur contenznt el nombre d'incrits cumulés
	// valeur : Le nombre de post de type palmares sur les 6 derbier mois 
	// legedn les mois
	// les 2 tableaux sont des chaines de texte pour affichage par wp_chart
	
	$legend = '';
	$value = '';
	
	// Creation des dates
	$today = getdate();
	$today_1m = getdate(strtotime ("-1 month"));//mois precedent
	$today_2m = getdate(strtotime ("-2 months"));//mois precedent
	$today_3m = getdate(strtotime ("-3 months"));//mois precedent
	$today_4m = getdate(strtotime ("-4 months"));//mois precedent
	$today_5m = getdate(strtotime ("-5 months"));//mois precedent
	

	
	// PALMARES
	
	//  Creation avant 5 mois
	$args = array(
			'meta_query' => array(
					array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_club)
									),
			'date_query' => array (
							array (
								'before' => array (
									'year' => $today_5m['year'],
									'month' => $today_5m['month'],
									'day', 1),
							'inclusive' => true
							)),
			'post_type' => 'palmares',
			'numberposts' =>-1,
			);
	$posts_init  = get_posts( $args );
	$NB = count($posts_init);
	
	$legend .= 'avant, ';
	$value  .= $NB.', ';

	//echo "<br>avant  : ".$today_5m['month']." ".$today_5m['year'].": ".count($posts_init)."<br>";

				
		//  Creation avant 3 mois
			$NB += count_palmares_at_month($current_club, $today_5m);
			$legend .= $today_5m['month'].', ';
			$value  .= $NB.', ';
			
			$NB += count_palmares_at_month($current_club, $today_4m);
			$legend .= $today_4m['month'].', ';
			$value  .= $NB.', ';
			
			
			$NB += count_palmares_at_month($current_club, $today_3m);
			$legend .= $today_3m['month'].', ';
			$value  .= $NB.', ';
			
			
			$NB += count_palmares_at_month($current_club, $today_2m);
			$legend .= $today_2m['month'].', ';
			$value  .= $NB.', ';
		
			$NB += count_palmares_at_month($current_club, $today_1m);
			$legend .= $today_1m['month'].', ';
			$value  .= $NB.', ';
		
			$NB += count_palmares_at_month($current_club, $today);
			$legend .= $today['month'];
			$value  .= $NB;
		
	//echo "$legend <bR>";
	//echo "$value <bR>";
	
	
	
	// Si toutes les valeurs sont égales:
	$DATA_pasEvolution = 0;
	if(count($posts_init) == $NB){
		$DATA_pasEvolution = $NB;
		// meme nombre de post surt tous les mois
	}
	 
	$result = array($legend,$value,$DATA_pasEvolution );
	

	return $result;
	
		
}

function count_palmares_at_month($current_club, $month){
	$args = array(
			'meta_query' => array(
					array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_club)
			),
			'date_query' => array (
					array (
							'after' => array (
									'year' => $month['year'],
									'month' => $month['mon'],
									'day' => 1),
							'before' => array (
									'year' => $month['year'],
									'month' => $month['mon'],
									'day', 31),
							'inclusive' => true
					)),
			'post_type' => 'palmares',
			'numberposts' =>-1,
	);
	$posts  = get_posts( $args );
	
	
	//echo "<br>mois : ".$month['month']." ".$month['year'].": ".count($posts)."<br>";
	//print_r($posts); echo "</pre><br>";
	return count($posts);
	
}


function get_MAtchDeclaresClubBetween($current_club){
	// retourne un tableau  avec legende et valeur contenent el nombre de match declares
	// return[0] = legende :  le nom des mois
	// return[1] = valeur : Le nombre de post de type palmares sur les 6 derniers mois
	// return[2]  = si tous les mois sont egaux alors retourne la valeur sinon 0  . Permet d'juster l'axe Y du graph sous WP chart 
	// les 2 tableaux sont des chaines de texte pour affichage par wp_chart

	$legend = '';
	$value = '';

	// Creation des dates
	$today = getdate();
	$today_1m = getdate(strtotime ("-1 month"));//mois precedent
	$today_2m = getdate(strtotime ("-2 months"));//mois precedent
	$today_3m = getdate(strtotime ("-3 months"));//mois precedent
	$today_4m = getdate(strtotime ("-4 months"));//mois precedent
	$today_5m = getdate(strtotime ("-5 months"));//mois precedent



	// PALMARES

	//  Creation avant 5 mois
	$args = array(
			'meta_query' => array(
					array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_club)
			),
			'date_query' => array (
					array (
							'before' => array (
									'year' => $today_5m['year'],
									'month' => $today_5m['month'],
									'day', 1),
							'inclusive' => true
					)),
			'post_type' => 'resultats',
			'numberposts' =>-1,
	);
	$posts_init  = get_posts( $args );
	$NB = count($posts_init);
		
	
	
	

	$legend .= 'avant, ';
	$value  .= $NB.', ';

	//echo "<br>avant  : ".$today_5m['month']." ".$today_5m['year'].": ".count($posts_init)."<br>";


	//  Creation avant 3 mois
	$NB += count_matchDeclare_at_month($current_club, $today_5m);
	$legend .= $today_5m['month'].', ';
	$value  .= $NB.', ';
		
	$NB += count_matchDeclare_at_month($current_club, $today_4m);
	$legend .= $today_4m['month'].', ';
	$value  .= $NB.', ';
		
		
	$NB += count_matchDeclare_at_month($current_club, $today_3m);
	$legend .= $today_3m['month'].', ';
	$value  .= $NB.', ';
		
		
	$NB += count_matchDeclare_at_month($current_club, $today_2m);
	$legend .= $today_2m['month'].', ';
	$value  .= $NB.', ';

	$NB += count_matchDeclare_at_month($current_club, $today_1m);
	$legend .= $today_1m['month'].', ';
	$value  .= $NB.', ';

	$NB += count_matchDeclare_at_month($current_club, $today);
	$legend .= $today['month'];
	$value  .= $NB;

	//echo "$legend <bR>";
	//echo "$value <bR>";
	
	
	// Si toutes les valeurs sont égales:
	$DATA_pasEvolution = 0;
	 if(count($posts_init) == $NB){
	 		$DATA_pasEvolution = $NB;
	 	// meme nombre de post surt tous les mois
	 }
	 	

	$result = array($legend,$value,$DATA_pasEvolution );
	
	
	
	
	return $result;


}

function count_matchDeclare_at_month($current_club, $month){
	$args = array(
			'meta_query' => array(
					array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_club)
			),
			'date_query' => array (
					array (
							'after' => array (
									'year' => $month['year'],
									'month' => $month['mon'],
									'day' => 1),
							'before' => array (
									'year' => $month['year'],
									'month' => $month['mon'],
									'day', 31),
							'inclusive' => true
					)),
			'post_type' => 'resultats',
			'numberposts' =>-1,
	);
	$posts  = get_posts( $args );


	//echo "<br>mois : ".$month['month']." ".$month['year'].": ".count($posts)."<br>";
	//print_r($posts); echo "</pre><br>";
	return count($posts);

}


// ========================================
/* ! \brief Permet de gerer les requetes de la page gestion club by user  */
// =========================================
// Ajax

add_action( 'wp_ajax_tennisdefi_gestionClubAdmin' , 'ajaxGestionClubAdmin');
add_action( 'wp_ajax_nopriv_tennisdefi_gestionClubAdmin' , 'ajaxGestionClubAdmin');

function ajaxGestionClubAdmin() {
	check_ajax_referer( 'tennisdefi_ajax_security_pageGestionClub_Main', 'security' );
	


	global $current_user;
	write_log("**********process_ajaxGestionClubAdmin");
	header('Content-Type: application/json');


	 switch($_REQUEST['fonction']){

          case 'add_user':
               $output = tennisdefi_pageGestionClub_AddUser($_REQUEST['idclub'], $_REQUEST['nom'],$_REQUEST['prenom'],$_REQUEST['email']);
                break;
    }


	// return all our data to an AJAX call
    echo json_encode($output);
	wp_die();

}

function tennisdefi_pageGestionClub_sendUserMailInscription($club, $login, $email, $mtp){

	$sujet = "Tennis-Defi : Vous venez d'être inscrit";
	$message = "Bonjour,<br>
			Vous venez d'être inscrit à Tennis-Defi.com par le responsable de votre club sur notre site.
			Voici vos identifiants:<br>
			- votre club : $club<br>
			 - votre login :  $login<br>
			 - votre mot de passe :  $mtp<br>

			 Sportivement<br>
			 L'équipe Tennis-Defi<br>
			<a href = \"www.tennis-defi.com\">www.tennis-defi.com</a>";

	//passage en HTML
	add_filter( 'wp_mail_content_type', 'tennisdefi_set_html_mail_content_type' );
		wp_mail($email, $sujet,   $message );	
	// Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
	remove_filter( 'wp_mail_content_type', 'tennisdefi_set_html_mail_content_type' );	
 	 


}

function tennisdefi_pageGestionClub_AddUser($id_club_crypted, $nom, $prenom, $user_email){

	$errors         = array();      // array to hold validation errors
	$data           = array();      // array to pass back data

	//decrypt ID club
	$id_club = encrypt_decrypt('decrypt', $id_club_crypted);

	$user_name = $user_email;
	$user_id = username_exists( $user_name );
	$nom_club = get_post($id_club)->post_title;
	$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );

	


	if(empty($prenom))
		$errors['prenom'] = "Le prénom ne peut être vide";	
	 if(empty($nom))
		$errors['nom'] = "Le nom ne peut être vide";	
	if(empty($user_email))
		$errors['email'] = "L'email ne peut être vide";

	else if(email_exists($user_email))
		$errors['email'] = "Cet email est déjà utilisé";
	else if( !is_email( $user_email ))
      $errors['email'] = "Cet email n'est pas valide";
	else if($user_id)
		$errors['user'] = "Impossible d'inscrire ce joueur car il existe déja avec cet email";
	else if ( FALSE === get_post_status( $id_club ) )
		$errors['club'] = "Ce club n'existe pas";
	


// return a response ===========================================================

    // if there are any errors in our errors array, return a success boolean of false
    if ( empty($errors)) {
    	
    	$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		$user_id = wp_create_user( $user_name, $random_password, $user_email );
    	if($user_id){
    		addUserToClub($user_id, $id_club); 
    		
    		//save nom/prenom
    		wp_update_user( array( 'ID' => $user_id,
  										'last_name' => $nom,
	 									'first_name' => $prenom) );


    	tennisdefi_pageGestionClub_sendUserMailInscription($nom_club, $user_name, $user_email, $random_password);

    	       // if there are items in our errors array, return those errors
        $data['success'] = true;
        $data['message'] = 'Le joueur a bien été créé. Un email lui a été envoyé avec son identifiant et son mot de passe';
        }
        else
        	$data['success'] = false;
        	$errors['message'] = "Une erreur est survenue. Impossible de créer le joueur";
        	$data['errors'] = $errors;

    } else{

    	// if there are items in our errors array, return those errors
        $data['success'] = false;
        $data['errors']  = $errors;
    }
    	
	return $data;
}

