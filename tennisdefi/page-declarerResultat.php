<?php
/*
Template Name: DeclarerResultat
*/
?>
<?php

// ****************************************************************************
// CSS et Javascripts
// ****************************************************************************

// Ajout calendrier  + validaton date
wp_enqueue_script('jquery-ui-datepicker');
enqueue_style_smoothness ();
wp_enqueue_script( 'datevalidation', get_stylesheet_directory_uri().'/js/validationDates.js'); 
wp_enqueue_script( 'date_picker', get_stylesheet_directory_uri().'/js/date_picker.js', array('jquery','jquery-ui-datepicker'),null,true);  
wp_enqueue_script( 'date_picker2', get_stylesheet_directory_uri().'/js/jquery.ui.datepicker-fr.js', array('jquery','jquery-ui-datepicker'),null,true); 



// Ajout selection simple menu deroulant (Source ;:http://ivaynberg.github.io/select2)
enqueue_script_Lib_Select2();

//	Affichage des resultats
	enqueue_script_Lib_DataTable();



//wp_enqueue_style ('resultat_autocomple_css',get_stylesheet_directory_uri().'/js/select2-3.5.1/select2.css');

//wp_enqueue_script('resultat_autocomple_script',    get_stylesheet_directory_uri().'/js/select2-3.5.1/select2.js', array('jquery', 'jquery-ui-autocomplete')); 
//wp_enqueue_script('resultat_autocomple_script_lang',    get_stylesheet_directory_uri().'/js/select2-3.5.1/select2_locale_fr.js'); 


//wp_enqueue_style('boutonTennisdefi', get_stylesheet_directory_uri().'/js/css/test_bouton.css');
?>  

<?php  
// ****************************************************************************
// Entetes et tite de page
// ****************************************************************************
?>

<?php get_header(); ?>

<div id="content-full" class="grid col-940">
    
  <?php   
    // TITRE + Changement de clun
		addTitleAndSelectBox();

	// User Declarant
		$current_user       = wp_get_current_user();
		$user_idclub        =  get_the_author_meta(TENNISDEIF_XPROFILE_idClub, $current_user->ID, true) ;
		$current_user_rang  =  getCurrentUserRang();
		$current_user_isAdminClub = isUserAdminInClub($current_user->ID, $user_idclub);

// ****************************************************************************
// PARTIE Ttraitement
// ****************************************************************************

if (isset($_POST['submit']) && wp_verify_nonce( $_POST['nonce_field_declarerResultat'], 'declarer un resultat' ) ) {

	if($current_user_isAdminClub && isset($_POST['ADMINISTRATEUR_envoiePourAutre'])){
			//echo "Vous etes ADMIn et la box es tchekee<br>";
			$user_decalrantID = (int)$_POST['User_declarant'];
			$user_decalrantRang = getUserRang($user_decalrantID, $user_idclub);
			
		}else{
			
			//echo "Vous n'etes pas ADMIn ET/OU la box n'est pas cochee<br>";
			$user_decalrantID = $current_user->ID;
			$user_decalrantRang = getUserRang($user_decalrantID, $user_idclub);
		}

	
	
	//$current_userID =  ; // joueur ou responsable de club 
    // recupération des valeur du formulaire
    		$adversaireID       = (int)$_POST['AdversaireID'];
			$Math_statut = (int)$_POST['resultat'];  // 1: victoire 2: Defaite, 3: match nul
    		 $adversaire_rang = getUserRang($adversaireID, $user_idclub);

    // ----------------------------
    // Mise à jour du rang des joueurs
    // ----------------------------  
    if($Math_statut < 3){ // victoire  ou defaite ou matchg nul sinon erruer 
    		if($Math_statut==1)
    			tennis_defi_gestionResultat((int)$user_idclub, (int)$user_decalrantRang, (int)$adversaire_rang, 1,  $user_decalrantID , $adversaireID);
    			else
    				tennis_defi_gestionResultat((int)$user_idclub, (int)$user_decalrantRang, (int)$adversaire_rang, 0, $user_decalrantID , $adversaireID);
    }		

    				

    // ----------------------------
    // Mise en BdD du POST
    // ----------------------------	

    // conversion de la date
    $date = str_replace('/', '-', $_POST['DateMatch']);
    
    $post = array(
        'post_title'	=> 'resultats',
        'post_status'	=> 'publish',			// Choose: publish, preview, future, etc.
        'post_type'	    => 'resultats',
        'post_date'     => date("Y-m-d", strtotime($date)),
    );
    $post_ID = wp_insert_post($post);  // Pass  the value of $post to WordPress the insert function
    if($post_ID){
        // Do the wp_insert_post action to insert it
        //do_action('wp_insert_post', 'wp_insert_post'); 
        update_post_meta($post_ID, TENNISDEIF_XPROFILE_idClub,  (int)$user_idclub) ;
        update_post_meta($post_ID, TENNISDEIF_XPROFILE_matchDeclarePar,  (int)$user_decalrantID) ;
        if($Math_statut == 3)
        		update_post_meta($post_ID, TENNISDEFI_XPROFILE_matchNul,  1) ;
        else
        		update_post_meta($post_ID, TENNISDEFI_XPROFILE_matchNul,  0);
        		
        		       
         // Score (donné pour le vainqueur)
        update_post_meta($post_ID, 'j1s1',  (int)$_POST['j1s1']) ;
        update_post_meta($post_ID, 'j1s2',  (int)$_POST['j1s2']) ;
        update_post_meta($post_ID, 'j1s3',  (int)$_POST['j1s3']) ;

        update_post_meta($post_ID, 'j2s1',  (int)$_POST['j2s1']) ;
        update_post_meta($post_ID, 'j2s2',  (int)$_POST['j2s2']) ;
        update_post_meta($post_ID, 'j2s3',  (int)$_POST['j2s3']) ;


        if($Math_statut == 1 || $Math_statut == 3){ 
        				// victoire ou nul
            update_post_meta($post_ID, TENNISDEIF_XPROFILE_idVainqueur,  (int)$user_decalrantID) ;
            update_post_meta($post_ID, TENNISDEIF_XPROFILE_idPerdant,  (int)$adversaireID) ;
        }else{
        	//Defaite
            update_post_meta($post_ID, TENNISDEIF_XPROFILE_idPerdant,  (int)$user_decalrantID) ;
            update_post_meta($post_ID, TENNISDEIF_XPROFILE_idVainqueur,  (int)$adversaireID) ;
        } // victorie ou defaite
					  
			// ----------------------------
			// Envoie EMAIL à l'autre joueur
			 // ----------------------------
        $user_declarantInfo  = get_userdata($user_decalrantID);
        $user_adversaireInfo = get_userdata($adversaireID);
        $user_declarantInfo_displayname  = ucfirst(strtolower($user_declarantInfo ->user_firstname))." ".strtoupper($user_declarantInfo ->user_lastname);
        $user_adversaireInfo_displayname = ucfirst(strtolower($user_adversaireInfo->user_firstname))." ".strtoupper($user_adversaireInfo->user_lastname);
        $nouveau_rang_adversaire = getUserRang($adversaireID, $user_idclub); 
 
        $url_activite = get_page_activity_url();
        
        $sujet  = "Tennis Défi : nouveau résultat";
        $headers[] = "Reply-To:$user_declarantInfo_displayname<".$current_user->user_email.">\r\n";
        
        $message = "Bonjour $user_adversaireInfo_displayname, \n\n";
        $message.=  "$user_declarantInfo_displayname vient de déclarer le résultat de votre rencontre du $date.\n\n";

        $message .= "\n Retrouvez, partagez et commentez cette activités sur votre page <a href=\"$url_activite\">$url_activite</a> \n\n ";
        $message .=  "Votre login : " .$user_adversaireInfo->user_login. "\n" ;
        $message .=  "Vous êtes maintenant n°: " . $nouveau_rang_adversaire ."\n";
        $message .=  "Votre club : " . get_post($user_idclub)->post_title ."\n\n";
        
        $message .=  "L'équipe TENNIS DEFI\n" ;
        $message .=  "<a href=\"http://tennis-defi.com\">tennis-defi.com</a>" ;
		
		//envoie
		/*wpMandrill::mail ( $user_adversaireInfo->user_email, $sujet, nl2br ( $message ), 
									$headers, $attachments = array (), 
									$tags = array ('tennisdefi_Defi') 
						);*/
		
		wp_mail($user_adversaireInfo->user_email, $sujet,   $message , 
									$headers, $attachments = array ());
				
			write_log("*****************************")	;
			write_log($message);
			write_log("*****************************")	;
				
        
        // ----------------------------
        // Mise à jour des stats des joueurs
        // ----------------------------
		//Declarant
        $palmaresID =  getUserPalmaresID($user_decalrantID, $user_idclub);
        $statistiques = getjoueur_STATS_byClub($user_idclub, $user_decalrantID);
	        update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbpartenaires	, $statistiques['NBpartenaires']);
	        update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbvictoires 	, $statistiques['NBvictoires']);
	        update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbdefaites 	, $statistiques['nbdefaites']);
	        update_post_meta($palmaresID, TENNISDEFI_XPROFILE_matchNul 	, $statistiques['NBpartenaires']);
	        update_post_meta($palmaresID, TENNISDEFI_XPROFILE_nbMacth 		, $statistiques['NBmatch']);
	        update_post_meta($palmaresID, TENNISDEFI_XPROFILE_lastdeclaration, date('Y-m-d H:i:s'));
	     //Autre
	        $palmaresID =  getUserPalmaresID($adversaireID, $user_idclub);
	        $statistiques = getjoueur_STATS_byClub($user_idclub, $adversaireID);
	        update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbpartenaires	, $statistiques['NBpartenaires']);
	        update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbvictoires 	, $statistiques['NBvictoires']);
	        update_post_meta($palmaresID, TENNISDEIF_XPROFILE_nbdefaites 	, $statistiques['nbdefaites']);
	        update_post_meta($palmaresID, TENNISDEFI_XPROFILE_matchNul 	, $statistiques['NBpartenaires']);
	        update_post_meta($palmaresID, TENNISDEFI_XPROFILE_nbMacth 		, $statistiques['NBmatch']);
	        update_post_meta($palmaresID, TENNISDEFI_XPROFILE_lastdeclaration, date('Y-m-d H:i:s'));
	        
	        update_post_meta($user_idclub, TENNISDEFI_XPROFILE_lastdeclaration, date('Y-m-d H:i:s'));
	        	
        // 
        
        //echo "<br>date du post :".get_the_date( $post_ID)."<br><br>";

        // -----------------------------------
        // GEstion Buddy Press : Activity
        // ----------------------------------
        $JoueurCourant_lien = bp_core_get_userlink($user_decalrantID, $no_anchor = false, $just_link = false );
        $JoueurAdversaire_lien  = bp_core_get_userlink($adversaireID, $no_anchor = false, $just_link = false );

        if($Math_statut == 1){ //Victoire
            $Activity_txt_UserCourant     = $JoueurCourant_lien.' a battu '.$JoueurAdversaire_lien;
            $Activity_txt_UserAdversaire  = $JoueurAdversaire_lien.' a perdu contre '.$JoueurCourant_lien;
        }
        else if($Math_statut == 2){ //Defaite
            $Activity_txt_UserAdversaire  = $JoueurAdversaire_lien.' a battu '.$JoueurCourant_lien;
            $Activity_txt_UserCourant     = $JoueurCourant_lien.' a perdu contre '.$JoueurAdversaire_lien;
        }
        else{ //Match Null
            $Activity_txt_UserAdversaire  = $JoueurAdversaire_lien.' a fait match nul avec '.$JoueurCourant_lien;
            $Activity_txt_UserCourant     = $JoueurCourant_lien.' a fait match nul avec '.$JoueurAdversaire_lien;
        } 

        // Activyté du joueur Courant
        $args = array('component' => get_ActivityTennisDefi_Component(), 
                      'type'      => get_ActivityResultat_Type(),
                      'user_id'   => $user_decalrantID, 
                      'action'    => $Activity_txt_UserCourant);
        $activity_id_1 = bp_activity_add( $args );  

        // Activyté de l'adversaire
        $args = array('component' => get_ActivityTennisDefi_Component(), 
                      'type'      => get_ActivityResultat_Type(),
                      'user_id'   => $adversaireID, 
                      'action'    => $Activity_txt_UserAdversaire,
                      'hide_sitewide' => false); //Evite d'afficher les 2 meme resultats
        $activity_id_2 = bp_activity_add( $args ); 

       
        // mise à jour du club pour l'activité (permet de retrouver les activités du club)
        //bp_activity_update_meta( $activity_id_1, TENNISDEIF_XPROFILE_idClub, (int)$user_idclub );
        //bp_activity_update_meta( $activity_id_2, TENNISDEIF_XPROFILE_idClub, (int)$user_idclub );
		// Géré dans al function tennisdefi_ActivityAddClubID()
        // -----------------------------
        // Ajout Notification pour celui qui ne déclare pas
        // ------------------------------
        if ( bp_is_active( 'notifications' ) ) {
                             
            $notification = bp_notifications_add_notification( array(
                'user_id'           => $adversaireID,
                'item_id'           => $activity_id_2,
                'secondary_item_id' => $current_user->ID,
                'component_name'    => get_ActivityTennisDefi_Component(),
                'component_action'  => get_ActivityResultat_Type(),
                'date_notified'     => bp_core_current_time(),
                'is_new'            => 1,
            ) );

        }


        // -----------------------------------
        // Affichage retour
        // ----------------------------------
        if($current_user_isAdminClub && isset($_POST['ADMINISTRATEUR_envoiePourAutre'])){
			$nouveau_rang = getUserRang($user_decalrantID, $user_idclub);
			echo '<div class="info-box success grid col-940">';
			echo "<h3>Le match a bien été enregistré</h3>";
			echo "Le joueur occupe à présent le rang N° : $nouveau_rang<br>";
			
		}
		else{
			
		        $nouveau_rang = getCurrentUserRang();
		        echo '<div class="info-box success grid col-940">';
		        echo "<h3>Votre match a bien été enregistré</h3>";
		        echo "Vous occupez à présent le rang N° : $nouveau_rang<br>";
		        //affichage de l'activite
		        echo "Une activité a été créée:<br>";
		        if ( bp_has_activities( bp_ajax_querystring( 'activity' ).'&include='.$activity_id_1 ) ){
		        	while ( bp_activities() ) {
		        		bp_the_activity();
		       			 bp_activity_action(); // affiche l'activité
				        // Tennis DEFI : Ajout du bouton facebook
				        echo"<div>";
		       			 echo "Partagez celle-ci : ";
				        echo buddypress_facebook_share_activity_button();
				        echo buddypress_twitter_activity_button();
				        echo"</div>";
				        
		        		}
		        }
		        //echo get_page_activity_url();
		        echo '<a href="'.get_page_activity_url().'"> retrouvez toutes vos activités ici</a>';
        
		}
        
        echo '</div>';

    }// fin si wp_insertPost OK
 
    

} // fin tratement formulaire


// ------------fin traitement formulaire ----------

// ****************************************************************************
// PARTIE FORMULAIRE
// ****************************************************************************


    ?>

    
    <script language="javascript">
        function validation_form(theForm){
            var bResult = true;

            if (theForm.AdversaireID.value == <? echo $current_user->ID;?>){
                document.getElementById('div_alert_joueur').innerHTML = 'Vous ne pouvez pas déclarer un match avec vous-même.';
                window.location.hash = '#div_alert_joueur';
                bResult = false;
            }
            else{
                document.getElementById('div_alert_joueur').innerHTML = '';
            }
        
            if (!isValidDate(theForm.DateMatch.value)){
                    document.getElementById('div_alert_date').innerHTML = "La date entrée n'est pas valide";
            
                    window.location.hash = '#div_alert_date';
                    //document.getElementById('div_alert_date').focus();
                    bResult = false;
            } 
            else{
                document.getElementById('div_alert_date').innerHTML = "";
            }

            return bResult;   
        }

     // fonction Checkbox
     
 		function fct_ADMINISTRATEUR_envoiePourAutre() {
 	 		//alert("Hellop" + document.getElementById("ADMINISTRATEUR_envoiePourAutre"));
			if(document.getElementById("ADMINISTRATEUR_envoiePourAutre").checked){
 				document.getElementById("User_declarant").disabled = false;
 				}
			else{
        		document.getElementById("User_declarant").disabled = true;
				}
			}



        
    </script>

	<!-- Formulaire -->
	<form method="post" action="">

    
    <?php 
    
    
    // Administrateur de club
    if($current_user_isAdminClub){
    	echo do_shortcode('[one_third_last][box title="Administrateur" border_width="2" border_style="solid" text_color="#28704D" border_color="#115534" icon="male" icon_style="bg" icon_shape="circle" align="center" height="200px"]
 		<input NAME="ADMINISTRATEUR_envoiePourAutre" ID="ADMINISTRATEUR_envoiePourAutre" value ="false" type="checkbox" onchange="fct_ADMINISTRATEUR_envoiePourAutre()" />  Vous souhaitez déclarer au nom de quelqu\'un ?     
        </br>'
    		.combobox_joueurs_v2('User_declarant', $user_idclub, false, true).
    		'[/box][/one_third_last]');
    	
    	// DIVIDER
    	echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
    	 
    }

    
    
    // ENTETE
    echo do_shortcode('[one_third][box title="Votre club" text_color="#9C2D07" border_width="2" border_style="solid" border_color="#731D00" icon="compass" icon_style="bg" icon_shape="circle" align="center" height="200px"]<span style="font-size:2em; font-weight:bold; line-height:1.5em">'.get_post($user_idclub)->post_title.'</span>[/box][/one_third]');  
    echo do_shortcode('[one_third][box title="Votre rang" border_width="2" border_style="solid" border_color="#175579" text_color="#0A4164" icon="bullseye" icon_style="bg" icon_shape="circle" align="center" height="200px"][counter number="'.$current_user_rang.'" color="#0A4164" size="large" animation="3000"][/box][/one_third]');
    echo do_shortcode('[one_third_last][box title="Votre partenaire" border_width="2" border_style="solid" text_color="#28704D" border_color="#115534" icon="male" icon_style="bg" icon_shape="circle" align="center" height="200px"]Sélectionnez votre partenaire</br></br>'
				    		.combobox_joueurs_v2('AdversaireID', $user_idclub).
				    		'<div id= "div_alert_joueur" class="error_validation_form"></div>
				    		[/box][/one_third_last]');
    ?>    
    
    <?php 
    // DIVIDER
    echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]'); 
	?>
  
    <?php 
    // DATE / GAGNE-PERDU / SCORE
    echo do_shortcode('[one_third][box title="Date du match" text_color="#9C2D07" border_width="2" border_style="dotted" border_color="#731D00" icon="calendar" icon_style="border" icon_shape="circle" align="center"]
    					<input type="text" id="DateMatch" name="DateMatch" value="'.date("d/m/Y").'"/></br></br>(jour/mois/année)
    					<div id= "div_alert_date" class="error_validation_form"></div>
    		[/box][/one_third]');  
    echo do_shortcode('[one_third][box title="Gagné ou perdu ?" border_width="2" border_style="dotted" border_color="#175579" text_color="#0A4164" icon="thumbs-up" icon_style="border" icon_shape="circle" align="center"]Avez-vous gagné ou perdu ce match ?</br></br>
    							<div align="left">
    							<input type="radio" name="resultat" value="1" checked> Victoire <br>
						        <input type="radio" name="resultat" value="2"> D&eacute;faite<br>
							 	<input type="radio" name="resultat" value="3"> Match nul
    							</div>
    						[/box][/one_third]');
    echo do_shortcode('[one_third_last][box title="Score du vainqueur" border_width="2" border_style="dotted" text_color="#28704D" border_color="#115534" icon="cog" icon_style="border" icon_shape="circle" align="center"]Entrez le score du vainqueur</br>(ex 6/1 6/3 ou 6/1 4/6 6/4)</br>
    		 1<sup>&nbsper</sup> set&#8239;&#8239;&#8239;&#8239;&#8239;&#8239;&#8239;<INPUT TYPE="number" NAME="j1s1" size="1" maxlength="2" value="0" min="0" max="30"> /
              <INPUT TYPE="number" NAME="j2s1" size="2" maxlength="2" value="0" min="0" max="30">
                         <br>
               2<sup>ème</sup>  set&nbsp;&#8239;<INPUT TYPE="number" NAME="j1s2" size="2" maxlength="2" value="0" min="0" max="30"> /
               <INPUT TYPE="number" NAME="j2s2" size="2" maxlength="2" value="0" min="0" max="30">
                        <br>
               3<sup>ème</sup> set&nbsp;<INPUT TYPE="number" NAME="j1s3" size="2" maxlength="2" value="0" min="0" max="30"> /
               <INPUT TYPE="number" NAME="j2s3" size="2" maxlength="2" value="0" min="0" max="30">
    		[/box][/one_third_last]');
    ?>  
    
    <div style="text-align: center; margin-top: 40px">
			<input id="boutontennisdefi" type="submit" name="submit"
				value="Déclarer le résulat du match" />
		</div>
    
        <?php wp_nonce_field('declarer un resultat','nonce_field_declarerResultat'); ?>
		</form>
 
 
 
 <?php // ***************************************************************************
      // 				Affichaage Resultats
      // ***************************************************************************    	

         echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
          echo "<h4> Vos derniers matchs déclarés</h4>";
         // Vicoires / Match Nuls
         $args = array (
         		//'fields' => 'ids',
         		'meta_query' => array (
         				'relation' => 'AND',
         				array ('key' => TENNISDEIF_XPROFILE_idVainqueur,	'value' => $current_user->ID),
         				array ('key' => TENNISDEIF_XPROFILE_idClub,			'value' => $user_idclub
         				)
         		),
         		'orderby' => 'ID',
         		'post_type' => 'resultats',
         		'posts_per_page' => 10
         );
         $resultats_posts = get_posts ( $args );
         
         // Défaites / Match Nuls
         $args = array (
         		//'fields' => 'ids',
         		'meta_query' => array (
         				'relation' => 'AND',
         				array ('key' => TENNISDEIF_XPROFILE_idPerdant,	'value' => $current_user->ID),
         				array ('key' => TENNISDEIF_XPROFILE_idClub,		'value' => $user_idclub
         				)
         		),
         		'orderby' => 'ID',
         		'post_type' => 'resultats',
         		'posts_per_page' => 10
         );
         $resultats_posts = array_merge($resultats_posts, get_posts ( $args ));
         
		
 		/*        
         // On ne garde que les 10 derniers match
         function date_compare($a, $b)
         {
         	//write_log($a->post_date);
         	$t1 = strtotime($a->post_date);
         	$t2 = strtotime($b->post_date);
         	return $t2 - $t1;
         }
         usort($resultats_posts, 'date_compare');
         */
         
      
          // On ne garde que les 10 derniers match
          function temp_ID_compare($a, $b)
          {
          //write_log($a->post_date);
          $id1  = $a->ID;
          $id2  = $b->ID;
          return $id2 - $id1;
          }
          usort($resultats_posts, 'temp_ID_compare');
          
         
         
        // echo "resultats : ".count($resultats_postsID)."<br>";
		
         //print_r($resultats_postsID);
         
         ?>
		
        <div class="grid col-700 fit">

		<table id="Match" class="table_tennisdefi">
			<thead>
				<tr>
					<th>Date</th>
					<th>Gagnant</th>
					<th>Perdant</th>
					
				</tr>
			</thead>
			<tbody> 
        
        <?php
        
								for($k=0; $k<min(array(10, count($resultats_posts))); $k++){
									$resultat = $resultats_posts[$k];
									$IDresultat = $resultat->ID;
									// foreach ( $resultats_posts as $resultat ) :
									$IDgagnant = get_post_meta ( $resultat->ID, TENNISDEIF_XPROFILE_idVainqueur, true );
									$IDperdant = get_post_meta ( $resultat->ID, TENNISDEIF_XPROFILE_idPerdant,   true );
									$user_gagant = get_userdata ( $IDgagnant );
									$user_perdant = get_userdata ( $IDperdant );
									
									
									//$date	   = $resultat->post_date; ///date('d/m/yyyy', $resultat->post_date);
									$date = date('d-m-Y', strtotime($resultat->post_date));
									
									echo "<tr><td>" . $date ."</td><td>" . $user_gagant->user_firstname .' '.$user_gagant->user_lastname . "</td><td>" . $user_perdant->user_firstname .' '.$user_perdant->user_lastname . "</td></tr>";
								}
		?>
        </tbody>
		</table>

	</div>

</div>
<!-- end of #content -->

<?php //get_sidebar(); ?>
                        <?php get_footer(); ?>