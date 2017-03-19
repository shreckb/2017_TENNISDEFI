<?php
/*
Template Name: palmares_V5
*/

//Debug (telechargement de csv)
//getCSV_customPalmaresForLogin($idClub);
//die();


?>
<?php 
 
//Tooltip pour les texte souris
 wp_enqueue_script( 'jquery-ui-tooltip' );
 enqueue_style_smoothness ();
 

 // Script + Css pour le tableau facon Responstable 2.0 by jordyvanraaij
 	//enqueue_script_Lib_RESPONSTABLE();

	// datatable
	enqueue_script_Lib_DataTable();
	
	// Colorbox (boite de dialog de detail
	enqueue_script_Lib_COLORBOX();
	
 // script pour le detail du joueur
 	wp_enqueue_script( 'palmares_displayInfoUser', get_stylesheet_directory_uri().'/js/palmares_displayInfoUser.js',  array('jquery', 'jquery-ui-tooltip'));
 
 	//wp_enqueue_style("cuperino_Style", "http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/cupertino/jquery-ui.min.css");
 	
 	// ADMINISTRATEUR DU CLUB ? => accès à certaines focntions
 	wp_enqueue_script( 'jquery_jeditable_lib', get_stylesheet_directory_uri().'/js/jquery_jeditable/jquery.jeditable.js');

 	
 	
 	// DATa du joueur
 	//*********************************
 	// obtention du club du joeur
 	$current_user = wp_get_current_user();
 	$current_club = get_user_meta($current_user->ID, TENNISDEIF_XPROFILE_idClub, true);
 	$ID_currentuse_palmares = getUserPalmaresID($current_user->ID, $current_club);
 	
 	// GEstio des user Admin dans leur club
 	$isUserAdminInCLub = isUserAdminInClub($current_user->ID, $current_club);
 	$NB_joueur_club = get_post_meta($current_club, TENNISDEIF_XPROFILE_nbJoueursClub, true);
 	
 	
 	
 	
 	//On récupère les valuer des select box recu pour les setter au chargement de la page.
 	//*********************************
 	// GEstion des différents cas de palmares et Initialisation
 	//CAT : Homme/Femme/Mixte
 	
 	if(isset($_REQUEST['CATEGORIE'])){
 		$palmares_cat =$_REQUEST['CATEGORIE'];
 		//enregistrement en base de la demande utilisateur
 		update_post_meta($ID_currentuse_palmares, TENNISDEFI_PALMARES_XPROFILE_categorie, $palmares_cat);
 	}else{
 		$palmares_cat = get_post_meta($ID_currentuse_palmares, TENNISDEFI_PALMARES_XPROFILE_categorie, true);
 		if(!$palmares_cat)
 			$palmares_cat = TENNISDEFI_PALMARES_CAT_MIXTE;
 	}
 		
 		//Palamres
 		
 		if(isset($_REQUEST['palmares_type'])){
 			$palmares_type =$_REQUEST['palmares_type'];
 			update_post_meta($ID_currentuse_palmares, TENNISDEFI_PALMARES_XPROFILE_type, $palmares_type);
 		}else {
 			$palmares_type = get_post_meta($ID_currentuse_palmares, TENNISDEFI_PALMARES_XPROFILE_type, true);
 			if(!$palmares_type)
 				$palmares_type = TENNISDEFI_PALMARES_TYPE_all;
 			
 		}
 	// passage au scriopt 	
 	wp_localize_script( 'palmares_displayInfoUser', 'palmares_type', $palmares_type ); 
 	wp_localize_script( 'palmares_displayInfoUser', 'palmares_categorie', $palmares_cat );
 	
 	
 	// Lien imprimer
 	$link_palmares_print = get_page_link(get_IDpage_palmares_imprimable())."?CATEGORIE=$palmares_cat&palmares_type=$palmares_type";
 	
 	
 	
 	
 	?>
<?php get_header(); ?>


<div id="content-full" class="grid col-940">
<?php


	// TITRE + Changement de clun
	addTitleAndSelectBox();
	
	// Affichage liste des palamres et filtres
	// ===========================================

	$txt_filtre = '<form  method="POST">';
	// palamares
	$txt_filtre .= palamares_get_listOf($current_club, $current_user->ID);
	// filtres
	$txt_filtre .= '<SELECT id="palmares_filtres" name="CATEGORIE">
 			<option value="'.TENNISDEFI_PALMARES_CAT_MIXTE.'">Mixte</option>
 			<option value="'.TENNISDEFI_PALMARES_CAT_HOMME.'">Homme</option>
 			<option value="'.TENNISDEFI_PALMARES_CAT_FEMME.'">Femme</option>
	</SELECT>';
	// valider
	$txt_filtre .= '<input type="submit" name="boutontennisdefi_palamres_submit"
										class="Classe_boutontennisdefi_changeclub" value="Voir"/>';
		
	$txt_filtre .= "</form>";

	

	
	 //Affichage
	echo '<div class="clearfix">';
	echo do_shortcode("[one_half]  $txt_filtre  [/one_half]
 				   [one_half_last] 
					[button href=\"$link_palmares_print\" target=\"_blank\" size=\"small\" color=\"#175579\" hovercolor=\"#032e49\" textcolor=\"#ffffff\" icon=\"print\" tooltip=\"Téléchargez le palmarès tennis-défi et affichez-le !\"]Télécharger[/button]
 					 [/one_half_last]");
	
	echo '</div>';
			
	//Affichage Classique 		
	if($palmares_type == TENNISDEFI_PALMARES_TYPE_all)
		palmares_displayAll($current_user->ID, $current_club, $isUserAdminInCLub,$palmares_cat, false);
	elseif($palmares_type == TENNISDEFI_PALMARES_TYPE_actif)
		palmares_displayAll($current_user->ID, $current_club, $isUserAdminInCLub,$palmares_cat, true);
	
	elseif($palmares_type == TENNISDEFI_PALMARES_TYPE_friends)
		palmares_displayFriendAndMe($current_club, $current_user->ID,$isUserAdminInCLub, $palmares_cat);
	
	else {
		// On a alors l'id d'un tournois
		$id_tournoi = encrypt_decrypt('decrypt', $palmares_type);
		if( get_post_type( $id_tournoi  )  != 'tournoi' ){
			echo "erreur"; wp_die();
		}
		
		palmares_displayTournoi($current_user->ID, $current_club, $id_tournoi);
	}
    
    

    
    
  
        ?>
      </div>
<!-- end of div 940 -->
<!-- div930 -->
<div class="grid col-940">
	<h2>Légende</h2>
         <?php
     // **********************************
     //        ***** Legende ******
      // **********************************
        echo '<div>';
echo do_shortcode('[one_sixth][box title="" border_width="1" border_color="#448968" border_style="solid" bg_color="9dc6b2" align="center"]<img class="aligncenter size-small wp-image-24304" src="'.get_bloginfo('stylesheet_directory') .'/images/icon-recherche-partenaires.png" alt="recherche partenaires"  />Recherche des partenaires[/box][/one_sixth]');
 
  echo do_shortcode('[one_sixth][box title="" border_width="1" border_color="#448968" border_style="solid" bg_color="9dc6b2" align="center"]<img class="aligncenter size-small wp-image-24310" src="'.get_bloginfo('stylesheet_directory') .'/images/icon-infos-joueur.png" alt="informations joueur"  />Informations sur le joueur[/box][/one_sixth]');
 
  echo do_shortcode('[one_sixth][box title="" border_width="1" border_color="#448968" border_style="solid" bg_color="9dc6b2" align="center"]<img class="aligncenter size-small wp-image-24311" src="'.get_bloginfo('stylesheet_directory') .'/images/icon-joueur-ajouter.png" alt="ajouter comme partenaire"  />
 Ajouter comme partenaire[/box][/one_sixth]');
 
  echo do_shortcode('[one_sixth][box title="" border_width="1" border_color="#448968" border_style="solid" bg_color="9dc6b2" align="center"]<img class="aligncenter size-small wp-image-24312" src="'.get_bloginfo('stylesheet_directory') .'/images/icon-joueur-supprimer.png" alt="retirer de mes partenaires"  />
 Retirer de mes partenaires[/box][/one_sixth]');
 
  echo do_shortcode('[one_sixth][box title="" border_width="1" border_color="#448968" border_style="solid" bg_color="9dc6b2" align="center"]<img class="aligncenter size-small wp-image-24313" src="'.get_bloginfo('stylesheet_directory') .'/images/icon-statistiques.png" alt="statistiques du joueur"  />
 Voir les statistiques du joueur[/box][/one_sixth]');
 
  echo do_shortcode('[one_sixth_last][box title="" border_width="1" border_color="#448968" border_style="solid" bg_color="9dc6b2" align="center"]<img class="aligncenter size-small wp-image-24314" src="'.get_bloginfo('stylesheet_directory') .'/images/icon-balle-rouge.png"  />
 Nombre de partenaires du joueur[/box][/one_sixth_last]');
  echo '</div>';
         

    ?>
    </div>


</div>
<!-- end of #content -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>