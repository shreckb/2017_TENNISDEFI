<?php
/*
Template Name: gestionClub
Brief : Page reservee aux Administrateurs de club
*/
?>

<?php 
	// datatable
	enqueue_script_Lib_DataTable();
	wp_enqueue_script( 'jquery_page_gestionClub', get_stylesheet_directory_uri().'/js/page_gestionClub.js');
	

   $ajax_nonce = wp_create_nonce( "tennisdefi_ajax_security_pageGestionClub_Main" );
   wp_localize_script( 'jquery_page_gestionClub', 'jquery_page_gestionClub_pageMain_nonce', $ajax_nonce );  

	
	?>
	
	
<?php get_header(); ?>

                
<div id="content" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">

<?php   
	addTitleAndSelectBox();

  
    // Verification d'un club
      $current_user = wp_get_current_user();
      $current_club = get_the_author_meta ( 'tennisdefi_idClub', $current_user->ID,true );
      $isAdminCLub = isUserAdminInClub($current_user->ID, $current_club);
      $current_club_encrypted  = encrypt_decrypt ( 'encrypt', $current_club );
      


      
       
      
      
      // Acquisiiton Donnees sur le nombre d'nscriptions
      
      
      
      
      if(!$isAdminCLub):
      	echo '<div class="info-box notice grid col-940"> Pour visualiser cette page vous devez être administrateur du club<br> Contactez l\'administrateur du site pour le devenir <br></div> ';
      else:


      // ===========================================
      //Statistiques du club
      // ===========================================
      echo "<div> <h4> Synthèse </h4>";
     
      $NB_user = get_post_meta($current_club, TENNISDEIF_XPROFILE_nbJoueursClub,true);
      
      // MAtch declare
      $args = array(
      		'fields' => 'ids',
      		'meta_query' => array(
      				array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_club)
      		),
      		'post_type' => 'resultats',
      		'numberposts' =>-1,
			);
      $NB_match  = count(get_posts($args));

      	//NB joueurs dans le club
      echo do_shortcode('[one_half][box title="" border_width="1" border_color="#448968" border_style="solid" align="center" text_color="#448968"]
<h3 style="color: #448968;"><img src="'.get_bloginfo('stylesheet_directory').'/images/victoire.png" alt="victoire" width="25" height="25" />&nbsp;&nbsp;Nombre d\'Inscrits</h3>
[counter number="'.$NB_user.'" color="#448968" size="medium" animation="2000"]
[/box][/one_half]');
      
      
      // NB match déclaré
      echo do_shortcode('[one_half_last][box title="" border_width="1" border_color="#175579" border_style="solid" align="center" text_color="#175579"]
<h3 style="color: #175579;font-size:1.4em"><img src="'.get_bloginfo('stylesheet_directory').'/images/match-nul.png" alt="match-nul" width="25" height="25" />
&nbsp;&nbsp;Nombre de Matchs déclarés</h3>
[counter number="'.$NB_match.'" color="#175579" size="medium" animation="2000"]
[/box][/one_half_last]');
      
      
      echo "</div>";
      echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
      
      // ===========================================
      // Affichage  Actions
      // ===========================================
      $url_contact = get_permalink( get_IDpage_Contact());
      ?>
     <div>
     vous souhaitez enrichir vos possibilités d'action, n'hésitez pas à proposer de nouvelles fonctionnalités (<a href="<?php echo $url_contact;?>"> page contact</a>) 
      </div>
      
      <?

      echo '<div>';
      echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
      
      // ===========================================
      // Affichage Evolution du nombre de d'inscription dans le club
      // ===========================================
      
      $inscriptions = get_InscriptionClubBetween($current_club);
      $options = '';
      if($inscriptions[2]) {
      	// pas d'evolution sur les 6 dernier mois, il faut adpater l'ordonnees
      	$scalestepwidth = 10;
      	$scalesteps = 5;
      	$scalestartvalue = $inscriptions[2]- $scalesteps*$scalestepwidth/2;
      	$options = " scaleoverride='true' scalesteps='$scalesteps' scalestepwidth='$scalestepwidth' scalestartvalue='$scalestartvalue' ";
      
      }

      echo do_shortcode ('
      
      [one_half][animation effect="flip-y" delay="200"]
   		<div>	<h4>Nombre d\'inscrit dans votre club</h4><br>
      		[wp_charts title="linechart_Inscrits" type="line" width="100%" margin="5px 20px" datasets="'.$inscriptions[1].'" labels="'.$inscriptions[0].' '.$options.'"]
		</div>
      		
      	[/animation][/one_half]');
      
      // ===========================================
     	 // Affichage Evolution du nombre de resultats dans le club
   		// ===========================================
      $resultats = get_MAtchDeclaresClubBetween($current_club);
      $options = '';
      if($resultats[2]) {
      	// pas d'evolution sur les 6 dernier mois, il faut adpater l'ordonnees
      	$scalestepwidth = 10;
      	$scalesteps = 5;
      	$scalestartvalue = $resultats[2]- $scalesteps*$scalestepwidth/2;
      	$options = " scaleoverride='true' scalesteps='$scalesteps' scalestepwidth='$scalestepwidth' scalestartvalue='$scalestartvalue' ";
      		
      }
      	
      echo do_shortcode ('
      	[one_half_last][animation effect="flip-y" delay="600"]
				<h4>Nombre de match déclarés</h4>
				[wp_charts title="linechart_match" type="line" width="100%"  margin="5px 20px" datasets="'.$resultats[1].'" labels="'.$resultats[0].'" '.$options.']
		[/animation][/one_half_last]
		');
      
      
      echo "</div>";
      

      // ===========================================
      // //Ajout Joueur 
      // ===========================================
         echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
      
        $url_image_loading = get_stylesheet_directory_uri ().'/images/loading.gif';
            
         ?>
         <div> <h4> Gestion des Effectifs </h4>
          Vous pouvez inscrire directement un joueur dans votre club. Pour cela remplissez le formulaire ci-dessous. Un email lui sera automatiquement envoyé avec ses identifiants. 
            <form action="" id="form_create_user" method="post">
                 <div id="nom-group" class="form-group">
                     <label for="nom">Nom</label>
                     <input type="text" class="form-control" name="nom" placeholder="Nom">
                     <!-- errors will go here -->
                 </div>
                 <div id="prenom-group" class="form-group">
                     <label for="prenom">Prénom</label>
                     <input type="text" class="form-control" name="prenom" placeholder="Prénom">
                     <!-- errors will go here -->
                 </div>
                 <div id="email-group" class="form-group">
                     <label for="email">Email</label>
                     <input type="text" class="form-control" name="email" placeholder="user@email.com">
                     <!-- errors will go here -->
                 </div>
                 
                 <input type="hidden" name="idclub" value="<? echo $current_club_encrypted; ?>">
  
               <div class="small"><button type="submit" id="adduserbutton" class="Classe_boutontennisdefi">inscrire ce joueur</button></div>

               <!-- Gestion changement -->
              <span id="tennisdefi_form_createUser_LoadingImage" style="display: none"> 
                <img  src="<? echo $url_image_loading; ?>" width="20" />
                </span>
            </form>
            </div>
         <?

      


      // ===========================================
      // Changer le  rang d'un joueur
      // ===========================================
      echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
      echo '<div id="changerRang"><h4>Changer le rang d\'un joueur</h4>';
		
	if (isset($_POST['rang_future']) && wp_verify_nonce( $_POST['nonce_field_changerRang'], 'changer de rang' ) ) {
		//echo '<div class="info-box notice grid col-940"> Changement effectuer. Voir le palmares <br></div> ';
		$rang_futur = (int)$_POST['rang_future'];
		$joueurSelectionne = (int)$_POST['JoueurID'];
		
		echo "joueur ID = $joueurSelectionne (club ID =  $current_club) et rang futur = $rang_futur<br>";
		tennisdefi_changerRangJoueur($joueurSelectionne, $current_club, $rang_futur);
		// focus sur le formulaire
		?>
		<script language="javascript">
			document.getElementById("changerRang").focus();
		</script>
		<?php
	}
	
	// nb joueur dans le club.
	$rang_max = get_post_meta($current_club, TENNISDEIF_XPROFILE_nbJoueursClub, true);
	
    echo "<div>";
    echo  '<form method="post" action="" onsubmit="return validation_form(this)">';

    echo "<div>Selectionnez un joueur : ". combobox_joueurs_v2('JoueurID', $current_club) ."</div>";
    echo " <div> Nouveau rang :";
      echo '<INPUT TYPE="number" NAME="rang_future" size="2" maxlength="2" value="1" min="1" max="'.$rang_max.'">';
      echo "(entre 1 et $rang_max)";
    echo "</div>";

		wp_nonce_field('changer de rang','nonce_field_changerRang');
       echo '<input id="boutontennisdefi" type="submit" name="submit"
       		value="changer le rang du joueur" />';
       
      echo "</form>";
  echo "</div>";
      // ===========================================
      // Convivialité
      // ===========================================
      echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
      
      echo "<div><h4>Convivialité</h4>";
      
      $args = array(
      		'fields' => 'ids',
      		'meta_query' => array(
      		array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_club)
      ),
      		'post_type' => 'palmares',
      		'numberposts' =>-1,
      		'meta_key' => TENNISDEIF_XPROFILE_rang,
      		'orderby' => 'meta_value_num',
      		'order' => 'ASC',);
      
      $postPalmares = get_posts($args);
      
      echo '<table id="table_convivialite" class="dt-responsive no-wrap stripe table_tennisdefi">
                    <thead><tr>
                    <th>Nom </th>
                    <th>Prénom</th>
      				<th>Nombre de Match</th>
      				<th>Nombre de Victoires</th>
      				<th>Nombre de Defaites</th>
      				<th>Nombre de Match nuls</th>
     				<th>Nombre de Partenaires occasionnels</th>
      				<th>Nombre de Partenaires habituels</th>
                    <th>Date d\'inscription</th>
      				</tr></thead>';
      echo   '<tbody>';
      
      foreach ( $postPalmares as $lignePalmares ) {
      	 
      	//$rang_currentuser   = get_post_meta($lignePalmares->ID , TENNISDEIF_XPROFILE_rang , true);
      	$id_joueur = get_post_meta($lignePalmares , TENNISDEFI_XPROFILE_idjoueur , true);
      	$user = get_userdata($id_joueur);
      	$user = get_userdata($id_joueur);

      	
      	$NBpartenaires 	= get_post_meta($lignePalmares, TENNISDEIF_XPROFILE_nbpartenaires 	, 	true);
      	$NBvictoires 	= get_post_meta($lignePalmares, TENNISDEIF_XPROFILE_nbvictoires 	, 	true);
      	$nbdefaites 	= get_post_meta($lignePalmares, TENNISDEIF_XPROFILE_nbdefaites 		, 	true);
      	$NBmatchNuls 	= get_post_meta($lignePalmares, TENNISDEIF_XPROFILE_nbmatcheNuls 	, 	true);
      	$NBmatch 		= get_post_meta($lignePalmares, TENNISDEFI_XPROFILE_nbMacth 		, 	true);
      	$NB_partenaires_friends = friends_get_total_friend_count($id_joueur);
      	
      	
      	
      	
      	echo '<tr>';
		echo '<td>'.strtoupper($user->user_lastname).'</td>';
      	echo '<td>'.$user->user_firstname.'</td>';
      	echo '<td>'.$NBmatch.'</td>';
      	echo '<td>'.$NBvictoires.'</td>';
      	echo '<td>'.$nbdefaites.'</td>';
      	echo '<td>'.$NBmatchNuls.'</td>';
      	echo '<td>'.$NBpartenaires.'</td>';
      	echo '<td>'.$NB_partenaires_friends.'</td>';
      	echo '<td>'.$user->user_registered.'</td>';
      	echo'</tr>';
      	
      	}
      	
      	 
      	echo'</tbody></table>';
      
      	
      
      echo "</div>";
      echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
      
      // ===========================================
      // Matchs déclarés
      // ===========================================
      echo "<div><h4>Derniers matchs déclarés</h4>";
      
      $args = array(
      		//'fields' => 'ids',
      		'orderby' => 'ID',
      		'order' => 'DESC',
      		'meta_query' => array(
      				array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $current_club)
      		),
      		'post_type' => 'resultats',
      		'posts_per_page' =>400,
      		);
      
      $posts = get_posts($args);
      
      echo '<table id="table_resultats" class="dt-responsive no-wrap stripe table_tennisdefi">
                    <thead><tr>
                    <th class="datatable_date">Date </th>
                    <th>Gagnant</th>
      				<th>Perdant</th>
      				<th>ID</th>
      				</tr></thead>';
      echo   '<tbody>';
      
      foreach ( $posts as $post ) {
      	$post_ID = $post->ID;
      	$date_match = date('d-m-Y', strtotime($post->post_date));
      	
      	$id_perdant = get_post_meta($post_ID , TENNISDEIF_XPROFILE_idPerdant , true);
      	$id_gagnant = get_post_meta($post_ID , TENNISDEIF_XPROFILE_idVainqueur , true);
      	 
		
      	$user_perdant = get_userdata($id_perdant);
      	$user_gagnant = get_userdata($id_gagnant);

      	 
      	echo '<tr>';
      	echo "<td>$date_match</td>";
      	echo '<td>'.strtoupper($user_gagnant->user_lastname).' '.$user_gagnant->user_firstname.'</td>';
      	echo '<td>'.strtoupper($user_perdant->user_lastname).' '.$user_perdant->user_firstname.'</td>';
      	echo "<td>$post_ID</td>";
      	echo'</tr>';
      	 
      }
       
      
      echo'</tbody></table>';
      
       
      
      echo "</div";
      
      
      
      
      // GEstion des Formulaires
      // ===========================
      //à venir si besoin
      
      
      
      
      

   		 endif; // fin si $isAdminCLub
    
    ?>
                </div><!-- .entry-content -->


        
<?php get_sidebar(); ?>
<?php get_footer(); ?>