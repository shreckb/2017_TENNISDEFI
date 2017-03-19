<?php

/*
 * ! \file
 * \brief Contient les fonctions de gestions d'affichage coté admin Panel
 */

// VOir le ficheir function_adminPanel.php pour la declaration du menu

// Ajout du Script
function register_TennisDefi_STAT_JSfile() {
	wp_register_script ( 'custum_tennisdefi_adminpanel_statJSscript', get_stylesheet_directory_uri () . '/js/Tennisdefi_adminPanel/page_stat.js' );
	wp_enqueue_script ( 'select2_script', 		'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js' );
	wp_enqueue_style  ('select2_css'    , 		'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css');
	

}
add_action ( 'admin_init', 'register_TennisDefi_STAT_JSfile' );
function add_TennisDefi_STAT_JSfile($hook) {
	global $TENNISDEFI_hook_to_menu_pageStat; // defini dans le fichier parent(function_adminPanel.php)
	if ($hook != $TENNISDEFI_hook_to_menu_pageStat)
		return;
	
	wp_enqueue_script ( 'custum_tennisdefi_adminpanel_statJSscript', array (
			'jquery' 
	), array (
			'jquery' 
	) );
	wp_enqueue_script ( 'select2_script', array ('jquery'), array ('jquery') );
	
	
	enqueue_script_Lib_DataTable ();
	enqueue_script_Lib_Select2();
}
add_action ( 'admin_enqueue_scripts', 'add_TennisDefi_STAT_JSfile' );



	// =================================
	// Nombre d'inscrit dans le plmares de tous les clubs
	// =================================
	
function TennisDefi_menu_page_STAT_display_users() {
	$value = [];
	$legend = [];
	$delta = [];
	
	for($k = - 5; $k <= 0; $k ++) {
		$txt = "$k months";
		$date = getdate ( strtotime ( $txt ) ); // mois precedents
		$args = array (
				'fields' => 'ids',
				'date_query' => array (
						array (
								'before' => array (
										'year' => $date ['year'],
										'month' => $date ['mon'],
										'day' => 31 
								),
								'inclusive' => true 
						) 
				),
				'post_type' => 'palmares',
				'numberposts' => - 1 
		);
		
		$value [] = count ( get_posts ( $args ) );
		$legend [] = $date ['month'] . ' ' . $date ['year'];
		// delta
		$last = count ( $value ) - 1;
		if ($last == 0)
			$delta [] = 0;
		else
			$delta [] = $value [$last] - $value [$last - 1];
	}
	
	// Affichage
	echo "<table><tr>";
	echo "<tr> <td style=\"text-align:center\">Mois : </td>";
	for($k = 0; $k < count ( $legend ); $k ++)
		echo "<td style=\"text-align:center\">$legend[$k]</td>";
	echo "</tr>";
	echo "<tr> <td style=\"text-align:center\">Nb : </td>";
	for($k = 0; $k < count ( $value ); $k ++)
		echo "<td style=\"text-align:center\">$value[$k]</td>";
	echo "</tr>";
	echo "<tr> <td style=\"text-align:center\">Delta : </td>";
	for($k = 0; $k < count ( $value ); $k ++)
		echo "<td style=\"text-align:center\">$delta[$k]</td>";
	echo "</tr>";
	
	echo "</table>";
}


// =================================
// Nombre de match déclarés
// =================================
function TennisDefi_menu_page_STAT_display_resultats(){
	$value = [ ];
	$legend = [ ];
	$delta = [ ];
	
	for($k = - 5; $k <= 0; $k ++) {
		$txt = "$k months";
		$date = getdate ( strtotime ( $txt ) ); // mois precedents
		$args = array (
				'fields' => 'ids',
				'date_query' => array (
						array (
								'before' => array (
										'year' => $date ['year'],
										'month' => $date ['mon'],
										'day' => 31
								),
								'inclusive' => true
						)
				),
				'post_type' => 'resultats',
				'numberposts' => - 1
		);
	
		$value [] = count ( get_posts ( $args ) );
		$legend [] = $date ['month'] . ' ' . $date ['year'];
		// delta
		$last = count ( $value ) - 1;
		if ($last == 0)
			$delta [] = 0;
		else
			$delta [] = $value [$last] - $value [$last - 1];
	}
	
	// Affichage
	echo "<table id=\"table_resultats\"><tr>";
	echo "<tr> <td style=\"text-align:center\">Mois : </td>";
	for($k = 0; $k < count ( $legend ); $k ++)
		echo "<td style=\"text-align:center\">$legend[$k]</td>";
		echo "</tr>";
		echo "<tr> <td style=\"text-align:center\">Nb : </td>";
		for($k = 0; $k < count ( $value ); $k ++)
			echo "<td style=\"text-align:center\">$value[$k]</td>";
			echo "</tr>";
			echo "<tr> <td style=\"text-align:center\">Delta : </td>";
			for($k = 0; $k < count ( $value ); $k ++)
				echo "<td style=\"text-align:center\">$delta[$k]</td>";
			echo "</tr>";
	
			echo "</table>";
	
}


// =================================
// Nombre de match déclarés
// =================================
function TennisDefi_menu_page_STAT_display_lastUsers($NB_users =100){
	
	$users = get_users ( array (
			'orderby' => 'registered',
			'order' => 'DESC',
			'number' => $NB_users
	) );
	
	// echo "<table id=\"table_inscrits\">";
	echo '<table id="table_inscrits" class="dt-responsive no-wrap stripe table_tennisdefi">
                    <thead><tr>
                    <th>Nom</th>
                    <th>Date inscription</th>
                    <th>CLubs (actif)</th>
     				<th>dernière activité</th>
     				</tr></thead><tbody> ';
	
	foreach ( $users as $user ) {
	
		// print_r($user);
		$userdata = get_userdata ( $user->ID );
		$user_idclub = $club_actif = get_the_title ( get_the_author_meta ( 'tennisdefi_idClub', $user->ID ) );
		$user_clubs = get_user_meta ( $user->ID, TENNISDEIF_XPROFILE_idclubs, true );
	
		$last_update = bp_member_latest_update ();
	
		$txt_club = '';
		if(!empty($user_clubs))
			foreach ( $user_clubs as $club_id ) {
				$club_name = get_the_title ( $club_id );
				$txt_club .= "$club_name<br>";
			}
	
		echo "<tr>
		<td style=\"text-align:center\">$userdata->last_name  $userdata->first_name</td>
		<td style=\"text-align:center\">" . $user->user_registered . "</td>
		<td style=\"text-align:center\">$txt_club ($club_actif)</td>
		<td style=\"text-align:center\">$last_update</td>
			
			
		</tr>";
	}
	echo "</table>";
}



function TennisDefi_menu_page_STAT_display_statbyclub() {
	
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
	echo '<table id="table_clubs_data" class="dt-responsive no-wrap stripe table_tennisdefi">
                    <thead><tr>
                    <th>Club</th>
                    <th>Nombre d\'inscrit</th>
                    <th>Nombre de Resultats m</th>
     				<th>Ratio Resultat/joueur</th>
					<th>Evolution du nombre de joueur / il y 3 mois</th>
					<th>Evolution du nombre de match / il y 3 mois</th>
     				</tr></thead><tbody> ';
	

	
	$clubs_ID = get_posts($args);
	
	$club_rejetes = 0;
	echo count($clubs_ID)." Clubs Trouvés<br>";
	foreach ($clubs_ID as $club_ID)
	{
		
		$club_name = get_the_title ( $club_ID );
		
		// NB REsutlats total
		$args = array (
				'fields' => 'ids',
				'meta_query' => array (
						array (
								'key' => TENNISDEIF_XPROFILE_idClub,
								'value' => $club_ID
						)
				),
				'post_type' => 'resultats',
				'numberposts' => - 1
		);
		$NB_resultat = count(get_posts($args));
		
		// NB de resultat declaré il y a 3 mois (permet de voir l'evolution....
		$date = getdate ( strtotime ( "-3 months" ) ); // mois precedents
		$args = array (
				'fields' => 'ids',
				'meta_query' => array (array (
								'key' => TENNISDEIF_XPROFILE_idClub,
								'value' => $club_ID
						)
				),
				'date_query' => array (array (
								'before' => array (
										'year' => $date ['year'],
										'month' => $date ['mon'],
										'day' => 30
								),
								'inclusive' => true
						)
				),
				'post_type' => 'resultats',
				'numberposts' => - 1
		);
		$NB_resultat_2m = count(get_posts($args));
		
		
		
		// NB joueur
		$args = array (
				'fields' => 'ids',
				'meta_query' => array (
						array (
								'key' => TENNISDEIF_XPROFILE_idClub,
								'value' => $club_ID
						)
				),
				'post_type' => 'palmares',
				'numberposts' => - 1
		);
		$NB_joueur = count(get_posts($args));
		
		
		// NB joueur il y a 2 mois
		$args = array (
				'fields' => 'ids',
				'meta_query' => array (
						array (
								'key' => TENNISDEIF_XPROFILE_idClub,
								'value' => $club_ID
						)
				),
				'date_query' => array (array (
						'before' => array (
								'year' => $date ['year'],
								'month' => $date ['mon'],
								'day' => 30
						),
						'inclusive' => true
				)
				),
				'post_type' => 'palmares',
				'numberposts' => - 1
		);
		$NB_joueur_2m = count(get_posts($args));
		
		
		
		$NB_match_par_joeur = $NB_resultat/$NB_joueur;
		$evol_match = $NB_resultat - $NB_resultat_2m;
		$evol_joueur = $NB_joueur - $NB_joueur_2m;
		
		
		//Affichage
			echo "<tr>";
			echo "<td>$club_name</td>";
			echo "<td>$NB_joueur</td>";
			echo "<td>$NB_resultat</td>";
			echo "<td>$NB_match_par_joeur</td>";
			echo "<td>$evol_joueur</td>";
			echo "<td>$evol_match</td>";
			
			echo "</tr>";

	}

	echo "</table>";
}




// ****************************
// Page 1 --- Principale
// ****************************
function TennisDefi_menu_page_STAT() {
	echo "<h1>STAT</h1>";
	
	

	echo "<h2>Inscrits dans les clubs</h2>";
	TennisDefi_menu_page_STAT_display_users ();
	
	
	//echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
		

	echo "<h2>Résultats déclarés</h2>";
	TennisDefi_menu_page_STAT_display_resultats();
	
	echo "<h2>30 derniers inscrits</h2>";
	TennisDefi_menu_page_STAT_display_lastUsers(30);
	
	
	echo "<h2>Details par club</h2>";
	TennisDefi_menu_page_STAT_display_statbyclub();
	
	
}

