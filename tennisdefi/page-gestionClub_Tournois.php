<?php
/*
Template Name: gestionClub_Tournois
Brief : Page reservee au Administrateur de club, Gestion des tournois
*/
?>

<?php 
	// datatable
wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_style("wp-jquery-ui-dialog");
enqueue_script_Lib_DataTable();
//Pour la selection et modification
wp_enqueue_script( 'jquery_jeditable_lib', get_stylesheet_directory_uri().'/js/jquery_jeditable/jquery.jeditable.js');
	
	// https://github.com/olance/jQuery-switchButton
	//wp_enqueue_script ( 'jquery' );
	//wp_enqueue_script("jquery-effects-core");
	//wp_enqueue_script('jquery_onoff_button',      get_stylesheet_directory_uri().'/js/jQuery-switchButton/jquery.switchButton.js',array('jquery','jquery-effects-core') );
	//wp_enqueue_style ( 'jquery_onoff_button_css', get_stylesheet_directory_uri().'/js/jQuery-switchButton/main.css'  );
	
	//Scriopt de la page
	// ==================
	wp_enqueue_script( 'jquery_page_gestionClub_pageTournoi', get_stylesheet_directory_uri().'/js/page_gestionClub_pageTournoi.js', array('jquery','jquery-effects-core', 'jquery-ui-tooltip', 'jquery-ui-dialog', 'jquery-ui-tabs'));
	// Avec Sécurisation des requetes ajax, attention au Handle (1er argument = nom du script appele ci-dessus)
	$ajax_nonce = wp_create_nonce( "tennisdefi_ajax_security_pageGestionClub_Tournois" );
	wp_localize_script( 'jquery_page_gestionClub_pageTournoi', 'jquery_page_gestionClub_pageTournoi_nonce', $ajax_nonce );	

	// Gestion des donnees User et passage à javascript si besoins
	$current_user = wp_get_current_user();
	$current_club = get_the_author_meta ( 'tennisdefi_idClub', $current_user->ID,true );
	$isAdminCLub = isUserAdminInClub($current_user->ID, $current_club);
	
	$current_club_encrypted  = encrypt_decrypt ( 'encrypt', $current_club );
	wp_localize_script( 'jquery_page_gestionClub_pageTournoi', 'club_user', $current_club_encrypted );
	
	
	
	
	
	?>
	
	
<?php get_header(); ?>


<div id="content"
	class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">

<?php   
	addTitleAndSelectBox();

  

      
      // Acquisiiton Donnees sur le nombre d'nscriptions
      
      
      
      
      if(!$isAdminCLub):
      	echo '<div class="info-box notice grid col-940"> Pour visualiser cette page vous devez être administrateur du club<br> Contactez l\'administrateur du site pour le devenir <br></div> ';
      else:
      
      // ===========================================
      // Entetes : avant les TABS d'affichae
      // ===========================================
      echo "<div>";
      echo '<button id="create-tournoi">ajouter un tournoi</button>';
      echo '</div>';

      ?>
      <div id="tabs">
		  <ul>
		    <li><a href="#tabs-1">Synthèse de vos Tournois</a></li>
		    <li><a href="#tabs-2">Gestions des tournois</a></li>
		  </ul>
      
      
      <?php
      // ================================================
      //   			Affichage de tous tournois
      // ================================================
      //echo do_shortcode('<div>[divider style="icon-center" icon="angle-double-down" border="medium"]</div>');
      echo '<div id="tabs-1">';
		echo '<div id="pour_chargement_display">';
		echo '<table id="table_DiplayTournoiSummary" class="table_tennisdefi">
                    <thead><tr>
					<th>Création</th>
      				<th>Nom</th>
                    <th>Joueurs</th>
                    <th>Statut </th>
					<th>Actions</th>
					<th>Description</th>
      				</tr>
      				</thead><tbody></tbody></table>';
		echo '</div>';

		//$titre_tournoi, $nb_user,$txt_actif,$txt_visibilite,$txt_open,$txt_affiherDetails.$txt_supprimerTournoi ,$details);
		
	echo '</div>'; // fin tab 1
	
	
	

      
      

      //===========================
      //Affichage du palamres/Actions/Creer TOurnoi....
      //===========================
     
	$args = array('meta_query' => array(
			array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_club)
	),
			'post_type' => 'palmares',
			'numberposts' =>-1,
			'meta_key' => TENNISDEIF_XPROFILE_rang,
			'orderby' => 'meta_value_num',
			'order' => 'ASC',);
	
	$postPalmares = get_posts($args);
     
     // echo do_shortcode('<div><br>[divider style="icon-center" icon="angle-double-down" border="medium"]<br></div>');
      echo '<div id="tabs-2">';
     
    
      echo   "<h2>Palmares de votre club </h2>";
      //Action et selection d'un tournoi
      echo "<div>";
	      echo '<span id="div_tournoi_selector"></span>'; // sera rempli par ajax appeler par la mise à jour des tournois
	      echo get_Tournoi_actions('tournoi_action');
	      echo '<button id="do_action">OK</button>';
	      echo'<a title="" class="tooltips_action_dans_tournoi">
	      <img  src="' . get_bloginfo ( 'stylesheet_directory' ) . '/images/icon-infos-joueur.png" alt="plus d\'information" height="20" width="20">
	      </a>';
      echo "</div>";
      
      echo '<div>
      		<span id="div_id_nb_selectedrows">0 joueur selectionné(s)</span>
      		<span><a id="linkResetSelection">(cliquer pour annuler la selection)</a></span></div>';
           
      echo '<table id="table_toutnoi_palmares" class="dt-responsive no-wrap stripe table_tennisdefi">
                    <thead>
      				<tr>
      				
                    <th>Rang</th>
                    <th>Nom </th>
                    <th>Prénom</th>
					<th>ID_cypted</th>
      				</tr>
      				</thead><tbody>';

      
      
      foreach ( $postPalmares as $lignePalmares ) {
      
	      	$id_joueur = get_post_meta($lignePalmares->ID , TENNISDEFI_XPROFILE_idjoueur , true);
	      	$rang_currentuser   = get_post_meta($lignePalmares->ID , TENNISDEIF_XPROFILE_rang , true);
	      	$user = get_userdata($id_joueur);
	      	
	      	$id_joueur_crypted = encrypt_decrypt ( 'encrypt', $id_joueur );
	      	echo '<tr>';
	      	
	      	echo "<td>$rang_currentuser</td>";
	      	echo '<td>'.strtoupper($user->user_lastname).'</td>';
	      	echo '<td>'.$user->user_firstname.'</td>';
	      	echo "<td>$id_joueur_crypted</td>";
	      	echo'</tr>';
	      	
      }
      echo'</tbody></table>';
      
      
      
      echo '</div>'; // fin tab 2
       echo "</div>"; // Fin TAB
      
      
      // ================================================
      //   			Affichage d'un tournoi
      // ================================================
      echo '<div id="div_DiplayTournoi_area" style="display:none"> ';
      //echo '<div id="div_DiplayTournoi_area"> ';
      	
      echo '<a name="detail_du_tournoi"></A>'; // permet de cibler cette aprtie de la page.
      //echo do_shortcode('<div><br>[divider style="icon-center" icon="angle-double-down" border="medium"]<br></div>');
       
      echo '<div class="info-box notice" id="alert_tournoi"></div>';
      
      echo '<h3 id="titre_tournoi"></h3>';
      
      
      // Initialisation de la table datatable
      echo '<div id="pour_chargement_display_tournoi">';
      echo '<table id="table_tournoi" class="dt-responsive no-wrap stripe table_tennisdefi">
		      <thead><tr>
		      <th>Rang</th>
		      <th>Nom </th>
		      <th>Prénom</th>
		      <th>Rang inital</th>
		      <th>Rang actuel</th>
		      <th>Gain de place</th>
			  <th>Actions</th>
		      </tr></thead><tbody></tbody></table>';
      echo "</div>"; // div chargement
      
      echo "</div>"; // fin div id=div_DiplayTournoi_area
      

      ?>
      
        <div id="dialog-form" title="Création d'un tournoi">
		<p class="validateTips">Saisissez les paramètres du tournoi</p>

		<form>
			<fieldset>
				<label for="name">Nom du tournoi</label> 
				<input type="text"
					name="name" id="name" value="Nouveau tournoi"
					class="text ui-widget-content ui-corner-all"> 
			<label for="open">Autorisez-vous les joueurs du club à s'incrire  d'eux meme à ce tournoi ?</label> 
				<input type="radio" name="tournoi_open"
						id="tournoi_open" value="0"
						class="text ui-widget-content ui-corner-all">
				 Non <input
						type="radio" name="tournoi_open" id=tournoi_open value="1"
						class="text ui-widget-content ui-corner-all" checked> Oui
					
				<label for="tournoi_visibilite">Souhaitez vous rendre visible ce tournoi aux
						joueurs que vous y ajouterez?</label> 
				<input type="radio" name="tournoi_visibilite"
						id="tournoi_visibilite" value="0"
						class="text ui-widget-content ui-corner-all">
				 Non <input
						type="radio" name="tournoi_visibilite" id="tournoi_visibilite" value="1"
						class="text ui-widget-content ui-corner-all" checked> Oui
				
				<textarea name="tournoi_description">entrez une descritpion ici.....</textarea>
				<!-- Allow form submission with keyboard without duplicating the dialog button -->
				<input type="submit" tabindex="-1"
					style="position: absolute; top: -1000px">
			</fieldset>
		</form>
	</div>
      
      
   <div id="dialog-confirm_deleteTournoi" title="Supprimer ce tournoi">
	  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	  Confirmez vous la suppression définitive de ce tournoi ?<br><br><br></p>
	</div>
      
      
      
      
      
      <?php

   		 endif; // fin si $isAdminCLub
    //===========================
    ?>









	</div>
<!-- .entry-content -->



<?php get_sidebar(); ?>
<?php get_footer(); ?>