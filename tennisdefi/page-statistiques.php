<?php
/*
 * Template Name: statistiques
 */
?>
<?php

	// datatable
    enqueue_script_Lib_DataTable();

// Lib pour afficher des cercles
enqueue_script_Lib_Knob();

// Script de la page utilisatnt les lib ci dessus
wp_enqueue_script ( 'script_page_stat', get_stylesheet_directory_uri () . '/js/page_statistiques.js', array (
		'jquery' 
) );


?>
 
 <?php    get_header(); ?>

<div id="content-full" class="grid col-940">
 
<?php

// TITRE + Changement de club
addTitleAndSelectBox();


// ************************************************
// *********RECUPERATION DATA DU JOEURS **********
// ************************************************
		$current_user = wp_get_current_user ();
		
		$user_idclub = get_the_author_meta ( 'tennisdefi_idClub', $current_user->ID );
		
		
		
		if (isset ( $_GET ["IDstat"] )) {
			
			$is_other_user = true;
			$user_ID = encrypt_decrypt ( 'decrypt', $_GET ["IDstat"] );
			$user_exist_ID = get_userdata ( $user_ID );
			// Securisation
			if (! $user_exist_ID) {
				$is_other_user = false;
				$user_ID = get_current_user_id ();
				echo '<div class="info-box notice">Le joueurs recherché n\'existe pas. Voici vos statistiques</div>';
			} // uer existe
		} else {
			$is_other_user = false;
			$user_ID = get_current_user_id ();
		}
		
		$user_info = get_userdata ( $user_ID );
		$username = $user_info->user_login;
		$first_name = $user_info->first_name;
		$last_name = $user_info->last_name;
		$rang = getUserRang($user_ID, $user_idclub);
		
		
		
		// NB victoire
		$args = array (
				'meta_query' => array (
						'relation' => 'AND',
						array (
								'key' => TENNISDEIF_XPROFILE_idVainqueur,
								'value' => $user_ID 
						),
						array (
								'key' => TENNISDEFI_XPROFILE_matchNul,
								'value' => 0 
						),
						array (
								'key' => TENNISDEIF_XPROFILE_idClub,
								'value' => $user_idclub 
						) 
				),
				'orderby' => 'date',
				'post_type' => 'resultats',
				'posts_per_page' => 999999 
		);
		$victoires_posts = get_posts ( $args );
		$victoires = count ( $victoires_posts );
		
		// NB defaite
		$args = array (
				'meta_query' => array (
						'relation' => 'AND',
						array (
								'key' => TENNISDEIF_XPROFILE_idPerdant,
								'value' => $user_ID 
						),
						array (
								'key' => TENNISDEFI_XPROFILE_matchNul,
								'value' => 0 
						),
						array (
								'key' => TENNISDEIF_XPROFILE_idClub,
								'value' => $user_idclub 
						) 
				),
				'orderby' => 'date',
				'post_type' => 'resultats',
				'posts_per_page' => 999999 
		);
		$defaites_posts = get_posts ( $args );
		$defaites = count ( $defaites_posts );
		
		// NB de match nuls
		$args = array (
				'meta_query' => array (
						'relation' => 'AND',
						array (
								'key' => TENNISDEIF_XPROFILE_idVainqueur,
								'value' => $user_ID 
						),
						array (
								'key' => TENNISDEFI_XPROFILE_matchNul,
								'value' => 1 
						),
						array (
								'key' => TENNISDEIF_XPROFILE_idClub,
								'value' => $user_idclub 
						) 
				),
				'post_type' => 'resultats',
				'posts_per_page' => 999999 
		);
		$matchnul_posts1 = get_posts ( $args );
		$nb_matchnuls = count ( $matchnul_posts1 );
		
		$args = array (
				'meta_query' => array (
						'relation' => 'AND',
						array (
								'key' => TENNISDEIF_XPROFILE_idPerdant,
								'value' => $user_ID 
						),
						array (
								'key' => TENNISDEFI_XPROFILE_matchNul,
								'value' => 1 
						),
						array (
								'key' => TENNISDEIF_XPROFILE_idClub,
								'value' => $user_idclub 
						) 
				),
				'post_type' => 'resultats',
				'posts_per_page' => 999999 
		);
		$matchnul_posts2 = get_posts ( $args );
		$nb_matchnuls += count ( $matchnul_posts2 );
		
		// Analyse fine des stat
		
		$posts_victoire = array ();
		foreach ( $victoires_posts as $post ) :
			$post_match = array ();
			// $post_match['idVainqueur'] =get_post_meta($post->ID , TENNISDEIF_XPROFILE_idVainqueur, true);
			$post_match ['idAdversaire'] = get_post_meta ( $post->ID, TENNISDEIF_XPROFILE_idPerdant, true );
			$post_match ['date'] = get_post_time ( 'd/m/Y', false, $post->ID );
			$posts_victoire [] = $post_match;
			// echo "post(".get_the_ID().") ID Gagnant :".$post_match['idVainqueur']." vs ID perdant : ".$post_match['idPerdant'] ."<br>";
		endforeach
		;
		
		$posts_defaite = array ();
		foreach ( $defaites_posts as $post ) :
			$post_match = array ();
			$post_match ['idAdversaire'] = get_post_meta ( $post->ID, TENNISDEIF_XPROFILE_idVainqueur, true );
			// $post_match['idPerdant'] =get_post_meta($post->ID , TENNISDEIF_XPROFILE_idPerdant, true);
			$post_match ['date'] = get_post_time ( 'd/m/Y', false, $post->ID );
			$posts_defaite [] = $post_match;
			// echo "post(".get_the_ID().") ID Gagnant :".$post_match['idVainqueur']." vs ID perdant : ".$post_match['idPerdant'] ."<br>";
		endforeach
		;
		
		$posts_matchNul = array ();
		foreach ( $matchnul_posts1 as $post ) :
			$post_match = array ();
			$post_match ['idAdversaire'] = get_post_meta ( $post->ID, TENNISDEIF_XPROFILE_idPerdant, true );
			$post_match ['date'] = get_post_time ( 'd/m/Y', false, $post->ID );
			$posts_matchNul [] = $post_match;
		endforeach
		;
		foreach ( $matchnul_posts2 as $post ) :
			$post_match = array ();
			$post_match ['idAdversaire'] = get_post_meta ( $post->ID, TENNISDEIF_XPROFILE_idVainqueur, true );
			$post_match ['date'] = get_post_time ( 'd/m/Y', false, $post->ID );
			$posts_matchNul [] = $post_match;
		endforeach
		;
		
		// liste joueurs avec nb match, victoires defaites...
		$joueurs = array ();
		
		$joueurs = getjoueur_stats ( $posts_defaite, $joueurs, 'defaites' );
		$joueurs = getjoueur_stats ( $posts_victoire, $joueurs, 'victoires' );
		$joueurs = getjoueur_stats ( $posts_matchNul, $joueurs, 'nuls' );
		
		// tri par nombre de match
		foreach ( $joueurs as $key => $row ) {
			$nbmatch [$key] = $row ['nbmatch'];
		}
		array_multisort ( $nbmatch, SORT_DESC, $joueurs );
		
		
		// Nombre de match total
		    $nb_match_total = $victoires + $defaites +  $nb_matchnuls;
		
		    

    
    
// ************************************************
// ********* FIN RECUPERATION DATA DU JOEURS ******
// ************************************************
?>

		    <div class="grid col-940">

<?php
echo "<h2>" . get_post ( $user_idclub )->post_title . "</h2>";


// *************************************************************
// ********* LIGNE DE TITRE (Rang/ VICTORIE/ DEFAITES ... ******
// *************************************************************



//Rang 
echo do_shortcode('[one_third]
		[box title="" border_width="1" border_color="#fda55d" border_style="solid" align="center" text_color="#fda55d"]
		<h3 style="color: #fda55d;"><img src="'.get_bloginfo('stylesheet_directory').'/images/rang.png" width="25" height="25" />&nbsp;&nbsp;Rang</h3>
		<br>
		[counter number="'.$rang.'" color="#fda55d" size="medium" animation="2000"]
		[/box][/one_third]');


//Nombre de Partenaires occasionnels 
$NB_partenaires_diff = count($joueurs);
echo do_shortcode('[one_third]
		[box title="" border_width="1" border_style="solid" text_color="#28704D" border_color="#115534" align="center"]
		<h3 style="color: #115534;">Partenaires<br>occasionnels</h3>
		[counter number="'.$NB_partenaires_diff.'" color="#115534" size="medium" animation="2000"]
		[/box][/one_third]');

//Nombre de Partenaires
$NB_partenaires_friends = friends_get_total_friend_count($user_ID);

echo do_shortcode('[one_third_last]
		[box title="" border_width="1" border_style="solid" text_color="#28704D" border_color="#115534" align="center"]
		<h3 style="color: #115534;">Partenaires<br>habituels*</h3>
		[counter number="'.$NB_partenaires_friends.'" color="#115534" size="medium" animation="2000"]
		[/box][/one_third_last]');
echo "<div><small> *personnes en contact sur Tennis Défi</small></div>";
// Victoires
echo do_shortcode('[one_third][box title="" border_width="1" border_color="#448968" border_style="solid" align="center" text_color="#448968"]
<h3 style="color: #448968;"><img src="'.get_bloginfo('stylesheet_directory').'/images/victoire.png" alt="victoire" width="25" height="25" />&nbsp;&nbsp;Victoires</h3>
[counter number="'.$victoires.'" color="#448968" size="medium" animation="2000"]
[/box][/one_third]');


//Defaites

echo do_shortcode('[one_third][box title="" border_width="1" border_color="#be441b" border_style="solid" align="center" text_color="#be441b"]
<h3 style="color: #be441b;"><img src="'.get_bloginfo('stylesheet_directory').'/images/defaite.png" alt="defaite" width="25" height="25" />&nbsp;&nbsp;Défaites</h3>
[counter number="'.$defaites.'" color="#be441b" size="medium" animation="2000"]
[/box][/one_third]');

// Match Nuls
echo do_shortcode('[one_third_last][box title="" border_width="1" border_color="#175579" border_style="solid" align="center" text_color="#175579"]
<h3 style="color: #175579;font-size:1.4em"><img src="'.get_bloginfo('stylesheet_directory').'/images/match-nul.png" alt="match-nul" width="25" height="25" />
&nbsp;&nbsp;Matchs nuls</h3>
[counter number="'.$nb_matchnuls.'" color="#175579" size="medium" animation="2000"]
[/box][/one_third_last]');

echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');


// *************************************************************
// ********* BILAN DES MATCH / PARTENAIRES               ******
// *************************************************************
?>


    <h3>Bilan des matchs</h3>

	<div class="grid col-220" >
			
      <?php 
      // ***********************
      /// PARRTENAIRES
      // ***********************
      //echo do_shortcode('[wp_charts title="mypie" type="pie" margin="5px 20px"  data="'.$defaites.','.$victoires.','.$nb_matchnuls.'" colors="#448968,#be441b,#175579"]');
     		//echo	do_shortcode('[wp_charts title="mypie" type="pie" align="alignright" margin="5px 20px" data="10,32,50,25,5"]');

      
      echo do_shortcode('[wp_charts title="wp_charts_Bilan" type="doughnut" margin="0" data="'.$victoires.','.$defaites.','.$nb_matchnuls.'" colors="#448968,#be441b,#175579" width="90%" height="90%"]');
		//if ($is_other_user) {
			echo "statistiques $first_name $last_name";
		//}
			
		/* //LEGENDE 						
       <img
				src="<?php echo get_bloginfo('stylesheet_directory').'/images/stats/carre_448968.png'; ?>"
				width="10">
        Victoires : <?php $prc = 100*$victoires/$nb_match_total;  echo number_format($prc,0).'%'; ?>
        <br> <img
				src="<?php echo get_bloginfo('stylesheet_directory').'/images/stats/carre_be441b.png'; ?>"
				width="10">
      Défaites : <?php $prc = 100*$defaites/$nb_match_total;echo number_format($prc,0).'%';?>
        <br> <img
				src="<?php echo get_bloginfo('stylesheet_directory').'/images/stats/carre_175579.png'; ?>"
				width="10">
       Matchs nuls : <?php $prc = 100*$nb_matchnuls/$nb_match_total; echo number_format($prc,0).'%';?>
    */
    ?>
    </div>
		
		
 	<div class="grid col-700 fit">

			<table id="Partenaires" class="table_tennisdefi">
				<thead>
					<tr>
						<th>Partenaire</th>
						<th>Victoires</th>
						<th>Défaites</th>
						<th>Matchs nuls</th>
						<th>Nombre de match</th>
					</tr>
				</thead>
				<tbody> 
        
        <?php
								foreach ( $joueurs as $joueur ) :
									echo "<tr><td>" . $joueur ['user_firstname'] . " " . $joueur ['user_lastname'] . "</td><td>" . $joueur ['victoires'] . "</td><td>" . $joueur ['defaites'] . "</td><td>" . $joueur ['nuls'] . "</td><td>" . $joueur ['nbmatch'] . "</td></tr>";
								endforeach
								;
								?>
        </tbody>
			</table>

		</div>
		</div>
		<!-- FIN #table partenaires -->



<?php 
// *************************************************************
// ********* BILAN VICTOIRES                              ******
// *************************************************************

?>
		<!-- DEBUT #table victoire -->
		
		<div class="grid col-940" id="div_Stat_Victoires">
         <?php echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');?>

					<h3>Victoires</h3>

       <div class="grid col-220">
    	<?php 
    	
    	$nb1  = $victoires;
    	$nb2  = $nb_match_total - $victoires;
 			echo do_shortcode('[wp_charts title="wp_charts_Victoires" type="doughnut" margin="0" data="'.$nb1.','.$nb2.'" colors="#448968,#FDA55D" width="90%" height="90%"]');
			echo "statistiques $first_name $last_name";
    	?>
    	</div> 
    	
            <div class="grid col-700  fit">
			<table id="Victoires" class="table_tennisdefi">
				<thead>
					<tr>
						<th>Victoire contre</th>
						<th class="datatable_date">Date</th>
						<th class="datatable_nosort">Ratio victoires/matchs</th>
					</tr>
				</thead>
				<tbody> 
        
        <?php
								foreach ( $posts_victoire as $match ) :
									$id = $match ['idAdversaire'];
									$key_joueur = 'key_' . $id;
									$ratio = $joueurs [$key_joueur] ['victoires'] . "/" . $joueurs [$key_joueur] ['nbmatch'];
									echo "<tr><td>" . $joueurs [$key_joueur] ['user_firstname'] . " " . $joueurs [$key_joueur] ['user_lastname'] . "</td><td>" . $match ['date'] . "</td><td>" . $ratio . "</td></tr>";
								endforeach
								;
								?>
        </tbody>
			</table>
        </div>    
 
            

		</div> 
		<!-- Fin #table victoire -->
<?php 
// *************************************************************
// ********* BILAN DEFAITES                               ******
// *************************************************************
?>

		<!-- #table defaites -->
		<div class="grid col-940" >
        				<?php echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');?>
			<h3>Défaites</h3>

			 
             <div class="grid col-220" id="div_Stat_Defaites">
			 
			 <?php 
			 
		$nb1  = $defaites;
    	$nb2  = $nb_match_total - $nb1;
    	echo do_shortcode(' [wp_charts title="wp_charts_Défaites" type="doughnut" margin="0" data="'.$nb1.','.$nb2.'" colors="#be441b,#FDA55D" width="90%" height="90%"]');
		echo "statistiques $first_name $last_name";
			?>
		</div> 
			
			 <div class="grid col-700 fit">
			<table id="Defaites" class="table_tennisdefi">
				<thead>
					<tr>
						<th>Défaite contre</th>
						<th class="datatable_date">Date</th>
						<th class="datatable_nosort">Ratio défaites/matchs</th>
					</tr>
				</thead>
				<tbody> 
        
        <?php
								foreach ( $posts_defaite as $match ) :
									$id = $match ['idAdversaire'];
									$key_joueur = 'key_' . $id;
									$ratio = $joueurs [$key_joueur] ['defaites'] . "/" . $joueurs [$key_joueur] ['nbmatch'];
									echo "<tr><td>" . $joueurs [$key_joueur] ['user_firstname'] . " " . $joueurs [$key_joueur] ['user_lastname'] . "</td><td>" . $match ['date'] . "</td><td>" . $ratio . "</td></tr>";
								endforeach
								;
								?>
        </tbody>
			</table>
			</div>
		</div>
		<!-- #table defaites -->
		
<?php 
// *************************************************************
// ********* BILAN MATCH NULS                             ******
// *************************************************************
?>

		<!-- #table matchs nuls -->
		 <?php $prc = number_format(100*$nb_matchnuls/$nb_match_total, 0); ?>
		
		<div class="grid col-940" id="div_Stat_MatchNuls">
        								<?php echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');?>
<h3>Matchs nuls</h3>


		<div class="grid col-220 ">
				 <?php 			 
				$nb1  = $nb_matchnuls;
		    	$nb2  = $nb_match_total - $nb1;
		    	//echo do_shortcode('[wp_charts title="wp_charts_Défaites" type="doughnut" margin="0" data="'.$nb1.','.$nb2.'" colors="#be441b,#FDA55D" width="90%" height="90%"]');
		    	echo do_shortcode('[wp_charts title="Nuls" type="doughnut" margin="0" data="'.$nb1.','.$nb2.'" colors="#175579,#FDA55D" width="90%" height="90%"]');
				echo "statistiques $first_name $last_name";
		    	?>
		</div> 
		
			<div class="grid col-700 fit" >

			<table id="Matchs_Nuls" class="table_tennisdefi">
				<thead>
					<tr>
						<th>Match nul avec</th>
						<th class="datatable_date">Date</th>
						<th class="datatable_nosort">Ratio nuls/matchs</th>
					</tr>
				</thead>
				<tbody> 
        
        <?php
								
								foreach ( $posts_matchNul as $match ) :
									$id = $match ['idAdversaire'];
									$key_joueur = 'key_' . $id;
									$ratio = $joueurs [$key_joueur] ['nuls'] . "/" . $joueurs [$key_joueur] ['nbmatch'];
									echo "<tr><td>" . $joueurs [$key_joueur] ['user_firstname'] . " " . $joueurs [$key_joueur] ['user_lastname'] . "</td><td>" . $match ['date'] . "</td><td>" . $ratio . "</td></tr>";
								endforeach
								;
								
								?>
        </tbody>
			</table>
			</div>

		</div>
		<!-- #table defaites -->




	</div>
	<!-- #content -->



<?php //get_sidebar(); ?>
<?php get_footer(); ?>