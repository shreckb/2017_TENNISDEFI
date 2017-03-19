<?php
/*
Template Name: SelectCLUB_byUser
*/

/*! \page TENNISDEFI_templates_Page
  

  \section sec2 section 2

*/

?>

<?php 

/*wp_enqueue_script('jquery');
/*    wp_enqueue_style ('autocomple_css',get_stylesheet_directory_uri().'/js/css/page_selectclub_autocomplete.css');
    wp_enqueue_style ('autocomple_css2',get_stylesheet_directory_uri().'/js/css/smoothness/jquery-ui-1.10.4.custom.css');
    wp_enqueue_script('autocomple',    get_stylesheet_directory_uri().'/js/page_selectclub_autocomplete.js', array('jquery', 'jquery-ui-autocomplete'));*/

// datatable
    enqueue_script_Lib_DataTable();


// Source ;:http://ivaynberg.github.io/select2/
/*
wp_enqueue_style ('selectClub_autocomple_css',get_stylesheet_directory_uri().'/js/select2-3.5.1/select2.css');
wp_enqueue_script('selectClub_autocomple_script',    get_stylesheet_directory_uri().'/js/select2-3.5.1/select2.js', array('jquery', 'jquery-ui-autocomplete')); 
wp_enqueue_script('selectClub_autocomple_script_lang',    get_stylesheet_directory_uri().'/js/select2-3.5.1/select2_locale_fr.js'); 
*/
enqueue_script_Lib_Select2();
wp_enqueue_script('selectClub_autocomple_script2',    get_stylesheet_directory_uri().'/js/page_selectclub_autocomplete.js', array('jquery', 'jquery-ui-autocomplete')); 

?>

<?php    get_header(); ?>

 

<div id="content" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">
    <h1 class="entry-title post-title"><?php echo get_the_title(); ?></h1>
    <?php  if(isset($_GET['redirect'])):?>
    <div class="info-box notice">Vous devez avoir sélectionné votre club pour visualiser cette page</div>
    <?php endif; ?>

    <?php 



	global $current_user;
// ------------------------------
// PARTIE Ttraitement
//-------------------------------
	//Suppression d'un club
	//======================	
					if (isset($_POST['submit_deleteclub']) &&  wp_verify_nonce( $_POST['deleteclub_nonce_field'], 'deleteclub_action' ) ) {
						$id_crypted = $_POST['ID_deleteClub'];
					$id_club_select = encrypt_decrypt('decrypt', $id_crypted);
							
						
						//	echo "$id_crypted => = $id_club_select <br>";		
							
							$resultat = removeUserToClub($current_user->ID, $id_club_select);
								
							if($resultat['erreur']){ 
										echo '<div class="info-box alert">'.$resultat['txt'].'  </div>	';
									}
							else{
								$rang =getCurrentUserRang(); 
									echo '<div class="info-box success">Ce club a bien été déttaché de votre identifiant</div>';
							}
				
				}// fin traitement formulaire

	
							// Ajout d'un club
							//=======================
							if (isset($_POST['submit_addclub']) &&  wp_verify_nonce( $_POST['addclub_nonce_field'], 'addclub_action' ) ) {
										$url_palmares = get_page_link(get_IDpage_Palmares());
										$id_club_select = (int)$_POST['ID_club'];
										$resultat =  addUserToClub($current_user->ID, $id_club_select); 			
										
										if($resultat['erreur']){ 
													echo '<div class="info-box alert">'.$resultat['txt'].'  </div>	';
												}
										else{
											$rang =getCurrentUserRang(); 
											echo '<div class="info-box success">Votre club a bien été attaché à votre identifiant<br> Vous allez être redirigé vers la page du PALMARES de votre nouveau club<br>
												</div>';
                                            // REnvoie vers la pagedu palmares
                                             echo '<script language="javascript">
                                                            window.setTimeout(function () {
                                                                location.href = "'.$url_palmares.'";
                                                            }, 1000);
                                                    </script>';

										}
							
							}// fin traitement suppression club
							


// ------------------------------
// PARTIE le joueur a déja un club
//-------------------------------

// Javascript pour le formulaire
	?>
    <script language="javascript">
        function validation_delete_club_form(theForm){
            return confirm("La suppression d'un club entraine la suppresion de vos statistiques etc. \r\nCette action est irréversible.\r\nVoulez-vous continuer?");
                 
        }
    </script>
   <?php
		function locale_create_deleteClub_form($id_club){
			
			$id_encrypted = encrypt_decrypt('encrypt', $id_club);
 									echo '<form method="post" action="'.get_page_link( get_page_selection_club_id()).'" onsubmit="return validation_delete_club_form(this)">
    												<input type="hidden" id="ID_deleteClub" name="ID_deleteClub" value="'.$id_encrypted.'"/>';
            // Securité
            wp_nonce_field('deleteclub_action','deleteclub_nonce_field');
            echo '<input type="submit" name="submit_deleteclub"  id="submit_delete_club" class="Classe_boutontennisdefivert" value="retirer ce club" />';
            echo "</form>";   
}
//Affichage des club
$id_club=esc_attr( get_the_author_meta( TENNISDEIF_XPROFILE_idClub, $current_user->ID ) );

if(!empty($id_club)){
		echo '<h3>Liste de vos clubs</h3>';
				
	echo '<table id="table_clubs_user" class="table_tennisdefi">
                    <thead><tr>
                    <th>Club</th>
                    <th>Rang</th>
                    <th>Nombre de joueur dans ce club</th>
                    <th></th>
                    </tr>
                    </thead>
                    <tbody> ';
                    
  		
        $id_clubs =  get_the_author_meta(TENNISDEIF_XPROFILE_idclubs, $current_user->ID);	
	    		
        foreach ( $id_clubs as $id_club ) { 
									$nom_club = get_the_title($id_club);
									$nb_joueur = get_post_meta($id_club, TENNISDEIF_XPROFILE_nbJoueursClub, true);
									$rang = getUserRang($current_user->ID, $id_club);
									echo "<tr>";
									echo "<td>$nom_club</td> <td>$rang</td>  <td>$nb_joueur</td>";
									echo '<td>';
									locale_create_deleteClub_form($id_club);
									echo '</td>';
									echo "</tr>";
	
								}
	
	echo'</tbody></table>'; 	
}


?>

<?php
// On Affiche les etapes d'inscription si le joueur n'a pas de club
if(empty($id_club)){
	echo "<div>";
	//echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
	tennisdefi_inscriptionStepDescription(); // voir function_buddypress.php
	echo "</div>";
}
?>


<?php
// PARTIE FORMULAIRE : ajouter un club
// ------------------

$permalink_page_clubs = get_permalink( get_IDpage_MapClubs()); 
$url_PageConditions =  esc_url( get_permalink( get_page_by_title( 'Conditions d\'utilisation' ) ) );
    ?>
    <?php 
    	echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
     ?>
				<h3>Ajouter un club </h3>
    <p>Sélectionnez votre club dans la liste ci-dessous. <br>
        Vous pouvez vous aider de la <a href="<?php echo $permalink_page_clubs;?>">page clubs</a> pour rechercher plus précisement celui-ci.<br>
   </p>

    <script language="javascript">
        function validation_form(theForm){
            var bResult = true;
            if (theForm.ID_club.value == ''){
                document.getElementById('div_alert').innerHTML = 'Vous devez selectionner votre club dans la liste de choix.';
                bResult = false;
            } 
            return bResult;    
        }
    </script>
    <form method="post" action="<?php echo  get_page_link( get_page_selection_club_id()) ?>" onsubmit="return validation_form(this)">
        <div class="ui-widget">
            <div>
                <label for="clubs_selections">Recherchez votre club : </label>
               <?php 
               //<input type="hidden" id="ID_club" name="ID_club"/>
               ?> 
                <select id="ID_club" name ="ID_club">
 					 <option value="" selected="selected"></option>
				</select>
            </div>
            <div id= "div_alert" class="error_validation_form"></div>
            
            <?php wp_nonce_field('addclub_action','addclub_nonce_field'); ?>
            <p><input type="submit" name="submit_addclub" class="Classe_boutontennisdefi" id="submit_Choix_club" value="Valider mon choix" /></p>
        </div>
        </form>
        

     
        
        
        
        <div>
        <?php  $url_creer_club = get_permalink( get_IDpage_Contact()); 
        		echo do_shortcode('[divider style="icon-center" icon="angle-double-down" border="medium"]');
     		?>
        <h3>Vous ne trouvez pas votre club dans la liste ?</h3>
Créez un nouveau club <a href="<?php echo $url_creer_club;?>">ici</a>.<br />
L'administrateur du site validera vos données, nous vous informerons par email de la disponibilité de votre nouveau club.
	</div>
        
</div><!-- #content -->




    <?php get_sidebar(); ?>
    <?php get_footer(); ?>