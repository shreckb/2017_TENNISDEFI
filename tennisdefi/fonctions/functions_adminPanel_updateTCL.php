<?php

/*
 * ! \file
 * \brief Contient les fonctions de gestions d'affichage coté admin Panel
 */

// VOir le ficheir function_adminPanel.php pour la declaration du menu
// réutilisation des scripts js panel_stat.php pour utiliser datatables 
// Ajout du Script deja fait dans les stat 




//************************************************
// add_TennisDefi_STAT_JSfile_forUpdtaeTCL()
// Ajoute les script JS
//************************************************
function add_TennisDefi_STAT_JSfile_forUpdtaePonctuel($hook) {
	
	global $TENNISDEFI_hook_to_menu_pageUpdatePonctuel; // defini dans le fichier parent(function_adminPanel.php)
	if ($hook != $TENNISDEFI_hook_to_menu_pageUpdatePonctuel)
		return;
	

	wp_register_script ( 'custum_tennisdefi_adminpanel_updatePonctuelJSscript', get_stylesheet_directory_uri () . '/js/Tennisdefi_adminPanel/page_updatePonctuel.js' );
	wp_enqueue_script ( 'custum_tennisdefi_adminpanel_updatePonctuelJSscript', array (
			'jquery'), array ('jquery') );
	
	enqueue_script_Lib_DataTable ();
	enqueue_script_Lib_Select2();

}
add_action ( 'admin_enqueue_scripts', 'add_TennisDefi_STAT_JSfile_forUpdtaePonctuel' );


// ****************************
// Page  --- Update POonctuel
// ****************************
function TennisDefi_menu_updatePonctuel() {
	echo "Pas d'action codées.";
	
	// boucle sur les clubs
	
	// TEST 1: mise à jour du meta " dernier match declaré" dans la ligne  palmares de chaque  joueur 
	// => permettra d'afficher un palmares avec ceux qui on déclaré depuis les N denier mois
	// utilisation de l'ajax pour traiter tout le monde
	echo "<h2>Test 1:  update \"dernier match déclarés\"</h2>";
	
	echo "<div id='test1_progression'> avancement 0%</div>";
	echo "<div id='test1_dataReturn'></div>";
		
	
 	
	
	
}


add_action('wp_ajax_menu_page_UpdatePonctuel_ajaxRequest', 'TennisDefi_menu_page_UpdatePonctuel_ajaxRequest');
add_action( 'wp_ajax_nopriv_menu_page_UpdatePonctuel_ajaxRequest', 'TennisDefi_menu_page_UpdatePonctuel_ajaxRequest' );

// Recherche des infos jouuer Clubs, Palmares, REsultats...) depuis l'id du joueur
function TennisDefi_menu_page_UpdatePonctuel_ajaxRequest(){
	write_log("=====TennisDefi_menu_page_UpdatePonctuel_ajaxRequest=====");
	switch($_REQUEST['fn']){
		case 'update_palmares' :
			write_log('\t fn=update_palmares');
			$resultat = TennisDefi_menu_page_UpdatePonctuel_update_palmares();
			break;
		case 'update_palmares_progress':
			write_log('\t fn=update_palmares_progress');
			$prc =  $_SESSION['TennisDefi_menu_page_UpdatePonctuel_update_palmares_progress'];
			$resultat = number_format ( $prc,  2).'%';
			write_log($resultat);
			//$resultat = 22;	
			break;
				
		default:
			$resultat = 'erreur : Option inconnue';
			break;
	}



	$response = json_encode( $resultat );
	echo $response;
	die;
}

function TennisDefi_menu_page_UpdatePonctuel_update_palmares(){
	
	// set the PHP timelimit to 10 minutes
	set_time_limit(600);
	
	
	// liste des clubs
	$args = array (
			'fields' => 'ids',
			'post_type' => 'club',
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_nbJoueursClub,
							'value' => 1,
							'compare' => '>'
					)
			),
				
			'numberposts' => -1
	);
	
	// init table
	$resultat = '<table id="table_clubs_data" class="dt-responsive no-wrap stripe table_tennisdefi">
                    <thead><tr>
                    <th>Club</th>
					<th>NB user</th>
					<th>dernier resultat</th>
     				</tr></thead><tbody> ';
	$clubs_ID = get_posts($args);
	
	//$clubs_ID = [9621];
	
	$nb = 0.;
	$NB_clubs = count($clubs_ID);
	$resultat .=  $NB_clubs." Clubs Trouvés<br>";
	write_log($resultat);
	foreach ($clubs_ID as $club_ID)
	{
		$club_name = get_the_title ( $club_ID );
		
		
		
		// JOB 
		//---------------------
		// Palmares
		$args = array ('fields' => 'ids',
						'meta_query' => array (
						array ('key' => TENNISDEIF_XPROFILE_idClub,'value' => $club_ID)),
						'post_type' => 'palmares',
						'numberposts' => - 1
						);
		$palmares_ids = get_posts($args);
		$NB_joueur = count($palmares_ids);
		
		write_log($club_name .'			NB joueurs: '.$NB_joueur);
		$last_date_club = '1990-01-30';//yyyy-mm-dd
		foreach ($palmares_ids as $palmares_id)
		{
			$last_date_user = '1990-01-30';//yyyy-mm-dd
			$user_id = get_post_meta($palmares_id,TENNISDEFI_XPROFILE_idjoueur,true );
				
			// 1/2 : recherche dernier resultat dans les defaites
			$args = array (
					//'fields' => 'ids',
					'meta_query' => array (
							'relation' => 'AND',
							array ('key' => TENNISDEIF_XPROFILE_idPerdant,	'value' => $user_id),
							array ('key' => TENNISDEIF_XPROFILE_idClub,		'value' => $club_ID
							)
					),
					'orderby' => 'date',
					'post_type' => 'resultats',
					'posts_per_page' => 1
			);
			$last_result = get_posts($args);

			//write_log($last_result);
			if(count($last_result)){
				//write_log(" dernier resultat du joueur :$user_id " . $last_result[0]->post_date);
				if($last_result[0]->post_date  > $last_date_user){
					//write_log($last_result[0]->post_date  ."   >    "  .$last_date_user);
					$last_date_user = $last_result[0]->post_date ;
					
				}
			}

			//2/2 :  recherche dernier resultat dans les victoires
			$args = array (
					//'fields' => 'ids',
					'meta_query' => array (
							'relation' => 'AND',
							array ('key' => TENNISDEIF_XPROFILE_idVainqueur,	'value' => $user_id),
							array ('key' => TENNISDEIF_XPROFILE_idClub,		'value' => $club_ID
							)
					),
					'orderby' => 'date',
					'post_type' => 'resultats',
					'posts_per_page' => 1
			);
			$last_result = get_posts($args);
				
				
			//write_log($last_result);
			if(count($last_result)){
				//write_log(" dernier resultat du joueur :$user_id " . $last_result[0]->post_date);
				if($last_result[0]->post_date  > $last_date_user){
					//write_log($last_result[0]->post_date  ."   >    "  .$last_date_user);
					$last_date_user = $last_result[0]->post_date ;
				}
			}
			
			
			// Update post meta de la ligbne Palmares
			update_post_meta($palmares_id, TENNISDEFI_XPROFILE_lastdeclaration, $last_date_user);
			
			
			// par rapport au club
			if($last_date_user > $last_date_club)
				$last_date_club = $last_date_user;
			
			
			
		}// foreach palmares
		
		// Update post meta du club
		update_post_meta($club_ID, TENNISDEFI_XPROFILE_lastdeclaration, $last_date_user);
			
		$resultat .="<tr>
						<td>$club_name(id=$club_ID)</td>
						<td>$NB_joueur</td>
						<td>$last_date_club</td>
					</tr>";
		
	
		
		//Avancement
		//-------------------
		$nb++;
		$pourcentage = 100. *$nb/$NB_clubs;
		session_start();
		$_SESSION['TennisDefi_menu_page_UpdatePonctuel_update_palmares_progress']  = $pourcentage;
		// close the session write
		session_write_close();
		
		
	}//foreach club
	
	$resultat .= '</tbody></table>';

	
	return $resultat;

}