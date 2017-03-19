<?php

//Gestion de buddypress
//************************************************************************
  

/*! \file
     \brief Contient les fonctions autour de buddyPress
    
    Details.
*/

/// \todo :  si besoin dans el futur : http://buddydev.com/plugins/bp-private-message-rate-limiter/
  
// ASTUCE : http://phpdoc.ftwr.co.uk/buddypress/BuddyPress/_bp-friends---bp-friends-templatetags.php.html

// Aide à comprendre l'inscription : Page après inscription
add_filter( 'bp_after_registration_confirmed', 'tennisdefi_inscriptionStepDescription' );
function tennisdefi_inscriptionStepDescription(){
	echo "<h3>Vos prochaines étapes</h3>";
	echo do_shortcode('
			[one_third][box border_width="2" border_style="solid" border_color="#175579" text_color="#0A4164" icon="bullseye" icon_style="bg" icon_shape="circle" align="center" height="150px"]
					Etape 1/3 : Selectionnez un club
			[/box][/one_third]
			[one_third][box border_width="2" border_style="solid" text_color="#28704D" border_color="#115534" icon="group" icon_style="bg" icon_shape="circle" align="center" height="150px"]
					Etape 2/3 : Rendez vous dans le palmares du club, trouvez vos partenaires potentiels[/box][/one_third]
			[one_third_last][box  text_color="#9C2D07" border_width="2" border_style="solid" border_color="#731D00" icon="thumbs-up" icon_style="bg" icon_shape="circle" align="center" height="150px"]
					Etape 3/3 : Jouez, déclarez vos résultats
			[/box][/one_third_last]' );
}

// Ajout du sender quand c'est un message entre user : Ca marche  ?
add_filter( 'wp_mail', 'tennisdefi_buddypress_private_message_filter' );
function tennisdefi_buddypress_private_message_filter( $args ) {

	$new_wp_mail = array(
			'to'          => $args['to'],
			'subject'     => $args['subject'],
			'message'     => $args['message'],
			'headers'     => $args['headers'],
			'attachments' => $args['attachments'],
	);

	write_log("*********tennisdefi_buddypress_private_message_filter***********");
	write_log($args);
	return $args;
}


//===============FACEBOOK TWEETER.....=========================
/*! \brief Encode en php la fonction encodeURIComponent de javascript
        source : http://buddydev.com/buddypress/add-send-private-message-button-in-members-directory-on-a-buddypress-network/
*/
//https://developers.facebook.com/docs/sharing/reference/feed-dialog/v2.2?locale=fr_FR
//http://www.hyperarts.com/blog/tutorial-how-to-add-facebook-share-button-to-your-web-site-pages/
function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}

//Suppression du "depuis X min" pour affichage dans reseaux sociaux
function buddypress_activity_sharing_getTextOnly($txt){
    
    //Suppression du "depuis X min"
           $activite =  ereg_replace('<span class="time-since">(.*)</span>',
'',$txt);
            //suppression des paramètres HTML, non pris en compte par FaceBook
            $activite =  strip_tags($activite);
    return $activite;
    
};

// FaceBook
//-----------------
/*! \brief retourne le code pour un bouton "Facebook share" (utilise la fonction feed de facebook)
*/
function buddypress_facebook_share_activity_button(){
    $tennisdefi_facebook_appID = 849136158464672; // ID de l'application, définit sur le compte de Gbezot sur FaceBook
    $picture = encodeURIComponent(get_bloginfo('stylesheet_directory') .'/images/default-logo-orange_200x200.png');/// \todo: A ecrire en code propre et non en dur (utilisé pour le dev en local)

    $caption = encodeURIComponent('Tennis Défi (www.tennis-defi.com)');
    $description = encodeURIComponent('Premier tournoi interactif');
    $redirect_uri = encodeURIComponent(get_bloginfo('url'));  /// \todo: encodeURIComponent('http://tennis-defi.com');   
    
    //Texte 
    $activite = bp_get_activity_action();
    $activite = buddypress_activity_sharing_getTextOnly($activite);
    
            
            // Methode directLink
            $link = encodeURIComponent( bp_get_activity_thread_permalink() );
            $name = encodeURIComponent($activite);
            
            $url_facebook_feed = "https://www.facebook.com/dialog/feed?app_id=$tennisdefi_facebook_appID&link=$link&picture=$picture&name=$name&caption=$caption&description=$description&redirect_uri=$redirect_uri&display=popup";
           $output =  ' <a href="'.$url_facebook_feed.'" target="_blank" ><img src="'.get_bloginfo('stylesheet_directory') .'/images/facebook.png" alt="affichez cette activité sur votre facebook" width="60"></a> ';
    

    return $output;
}

// Twitter
//-----------------
/*! \brief retourne le code pour un bouton "Twitter "
        source : https://dev.twitter.com/web/tweet-button
*/
function buddypress_twitter_activity_button(){
    //Texte 
    $activite = bp_get_activity_action();
    $activite = buddypress_activity_sharing_getTextOnly($activite);
    $output = '<span class="twitter-share-button"><a class="twitter-share-button" name="social-share_facebook"
  href="https://twitter.com/share"
  data-url="http://tennis-defi.com"
  data-via="TennisDéfi"
  data-lang ="fr"
  data-text="'.$activite.'" 
  data-count="none"/>
tweetter
</a></span>';
    
    return $output;
}
//========================================
/*! \brief Utilisatio de la fonction mail entre user
        source : http://buddydev.com/buddypress/add-send-private-message-button-in-members-directory-on-a-buddypress-network/
*/
function buddypress_get_send_private_message_url($user_id) {
    //$user_id: the user id to whom we are sending the message
    if( !$user_id || $user_id == bp_loggedin_user_id() )
     return;

     if ( bp_is_my_profile() || !is_user_logged_in() )
     return false;

    return apply_filters( 'hibuddy_get_send_private_message_url', wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $user_id ) ) );
}




//========================================
/*! \brief Permet de forcer le dsiplay name à prenom Nom
    \todo Gérer l'importation de joueur, à la première connexion, le displayname n'est pas à jour. A la seconde ?
    \todo : VErifier si la suppression de cette fontion resoud nos problème : le plugin gère le display name depuis buddypress
    
*/
// =======================================
/*
function force_pretty_displaynames($user_login, $user) {

    $outcome = get_Xprofilefield_Prenom($user->ID) . " " . get_Xprofilefield_Nom($user->ID);
    if (!empty($outcome) && ($user->data->display_name!=$outcome)) {
        wp_update_user( array ('ID' => $user->ID, 'display_name' => $outcome));    
    }
}
add_action('wp_login','force_pretty_displaynames',10,2); 
*/


//========================================
/*! \brief Inscription : ajoute un champ de confirmation pour l'email 
*/
// =======================================
function registration_add_email_confirm(){ ?>
    <?php do_action( 'bp_signup_email_first_errors' ); ?>
<input type="text" name="signup_email_first" id="signup_email_first"
	value="<?php
    echo empty($_POST['signup_email_first'])?'':$_POST['signup_email_first']; ?>" />
<label>Confirmez l'adresse e-mail <?php _e( '(required)', 'buddypress' ); ?></label>
<?php do_action( 'bp_signup_email_second_errors' ); ?>
<?php }
add_action('bp_signup_email_errors', 'registration_add_email_confirm',20);
 

//========================================
/*! \brief Inscription ; Valide que l'email est bien entré correctemetn 2 fois
*/
// =======================================
function registration_check_email_confirm(){
    global $bp;
 
    //buddypress check error in signup_email that is the second field, so we unset that error if any and check both email fields
    unset($bp->signup->errors['signup_email']);

    //check if email address is correct and set an error message for the first field if any
    $account_details = bp_core_validate_user_signup( $_POST['signup_username'], $_POST['signup_email_first'] );

    if ( !empty( $account_details['errors']->errors['user_email'] ) )
        $bp->signup->errors['signup_email_first'] = $account_details['errors']->errors['user_email'][0];

 
    //if first email field is not empty we check the second one
    if (!empty( $_POST['signup_email_first'] ) ){
        //first field not empty and second field empty
        if(empty( $_POST['signup_email'] ))
            $bp->signup->errors['signup_email_second'] ='Assurez-vous d\'entrer correctement votre email 2 fois'; // 'Please make sure you enter your email twice';
        //both fields not empty but differents
        elseif($_POST['signup_email'] != $_POST['signup_email_first'] )
            $bp->signup->errors['signup_email_second'] = 'Les deux adresses email ne correspondent pas';
        //'The emails you entered do not match.';
    }
    

    
}
add_action('bp_signup_validate', 'registration_check_email_confirm',20);

//========================================
/*! \brief Inscription :Pas de verification que l'email est déja utilisé = Bypass validation email_exist()
*/
// =======================================
function skip_email_exist($result){

    
    write_log("=========On Passe par skip_email_exist ===============");
    if(isset($result['errors']->errors['user_email']) && ($key = array_search(__('Sorry, that email address is already used!', 'buddypress'), $result['errors']->errors['user_email'])) !== false) {
        
        
        
        //unset($result['errors']->errors['user_email'][$key]);
            $temp = $result['errors']->errors;
                unset( $temp['user_email'][$key] );
            $result['errors']->errors= $temp;
        
        
        if (empty($result['errors']->errors['user_email'])) {
            //unset($result['errors']->errors['user_email']);
            $temp = $result['errors']->errors;
                unset( $temp['user_email'] );
             $result['errors']->errors= $temp;  
        }
    }
    define( 'WP_IMPORTING', 'SKIP_EMAIL_EXIST' );
    
    return $result;
}
add_filter('wpmu_validate_user_signup', 'skip_email_exist');




//========================================
/*! \brief Retourne l'icone avec le lien pour envoyer un message privé
*/
// =======================================
function buddypress_get_send_private_message_button($user_id) {
         //$user_id: the user id to whom we are sending the message

    
    // image pour message:
       $link_text = '<img src="'.get_bloginfo('stylesheet_directory') .'/images/message_image.png" alt="message" height="20" width="20">';
    
    
          //don't show the button if the user id is not present or the user id is same as logged in user id
         if( !$user_id || $user_id == bp_loggedin_user_id() )
         return;
        $defaults = array(
         'id' => 'private_message-'.$user_id,
         'component' => 'messages',
         'must_be_logged_in' => true,
         'block_self' => true,
         'wrapper_id' => 'send-private-message-'.$user_id,
         'wrapper_class' =>'send-private-message',
         'link_href' => buddypress_get_send_private_message_url($user_id),
         'link_title' => __( 'Send a private message to this user.', 'buddypress' ),
         'link_text' => $link_text,
         'link_class' => 'send-message',
         );
     $btn = bp_get_button( $defaults );
 
 return apply_filters( 'hibuddy_get_send_private_message_button', $btn );
}



//========================================
/*! \brief buddypress, bouton add/remove friend
*/
// =======================================
function mb_bp_friend_button($button) {
	
	if ( is_array($button) && isset($button['id'])  ) {
		if ($button['id'] == 'is_friend'){
			$button['link_text'] = '<img src="'.get_bloginfo('stylesheet_directory') .'/images/icon-joueur-supprimer.png" width="20" alt="retirer de vos partenaires">';
		}	
                elseif ($button['id'] == 'not_friends'){
			$button['link_text'] = '<img src="'.get_bloginfo('stylesheet_directory') .'/images/icon-joueur-ajouter.png" width="20" alt="ajouter comme partenaire">';
		}
	}
	return $button;
}
add_filter( 'bp_get_add_friend_button','mb_bp_friend_button');








//========================================
/*! \brief  on ne peut visualiser que les membres/activité  de ses amis et dans son club !!
*/
// =======================================    
function filtering_buddypress_default( $query=false, $object = false ) {
	//return $query.'include=763,762&type=blalb';
    if($object == 'activity'){
    	
    	//voir ici :https://codex.buddypress.org/plugindev/add-custom-filters-to-loops-and-enjoy-them-within-your-plugin/
    	//On récupère les valeurs par défauts passées 
    	$args = wp_parse_args( $query, array(
    			'action'  => false,
    			'type'    => false,
    			'user_id' => false,
    			'page'    => 1
    	) );
    	
    	//On ne veut que soit et les amis
    	$friends = friends_get_friend_user_ids( bp_loggedin_user_id() );
    	$friends[] = bp_loggedin_user_id();
    	$args['user_id'] = $friends;
    	
    	// On ne peut voir que les activité de son club
    	$user_idclub        =  get_the_author_meta(TENNISDEIF_XPROFILE_idClub, bp_loggedin_user_id(), true) ;
    	
    	 
    	$args['meta_query'] = array(
    			array(
    					/* this is the meta_key you want to filter on */
    					'key'     => TENNISDEIF_XPROFILE_idClub,
    					'value'   => $user_idclub,
    					'type'    => 'numeric',
    					'compare' => '='
    			),
    	);
    	
    	write_log("*************ACTIVITE Filtree **********************");
    	write_log($args);
    	
    	/*
    	 //Methode Précédente
    	 //===============
          if ( empty( $query ) && empty( $_POST ) ) {
            //$query = 'action=activity_update';
          }
          $friends = friends_get_friend_user_ids( bp_loggedin_user_id() );
          $friends[] = bp_loggedin_user_id();
          $friends_and_me = implode( ',', (array) $friends );
          $friends_and_me =  '&user_id=' . $friends_and_me;
          $query .=$friends_and_me;
         // write_log($query);
          */
         
    	$query = empty( $args ) ? $query : $args;
    }
    elseif($object == 'members'){
		//Seuls les amis
    	$query  .='&object=friends';
    }
    
   
          
  return $query;
}
add_filter( 'bp_ajax_querystring', 'filtering_buddypress_default', 999 , 2);


// =====================================
// ====================================

function tennisdefi_ActivityAddClubID( $Activity_instance ) {
	// this is a silly example : just to illustrate !!
	//$activity_count = get_user_meta( $user_id, 'silly_activity_counter' );

	//$activity_count = empty( $activity_count ) ? 1 : intval( $activity_count ) + 1 ;

	//update_user_meta( $user_id, 'silly_activity_counter', $activity_count );
		write_log("****************** tennisdefi_ActivityAddClubID ****************");
		
		global $current_user;
		$user_idclub        =  get_the_author_meta(TENNISDEIF_XPROFILE_idClub, $current_user->ID, true) ;
		
		bp_activity_update_meta( $Activity_instance->id, TENNISDEIF_XPROFILE_idClub, (int)$user_idclub );

		write_log("Id club = $user_idclub, IdActivity = ".$Activity_instance->id);
		write_log("****************** tennisdefi_ActivityAddClubID ****************");
	
}

add_action( 'bp_activity_after_save', 'tennisdefi_ActivityAddClubID', 10, 1 );



//========================================
/*! \brief  Ajout de nouveaux types d'activité TennisDefi dans Buddypress
*/
// =======================================  
/// Ajout
function init_budypressActivity_Actions(){
	// On véririfie que le plugin est activé sinon, le site est KO
	if ( function_exists('bp_is_active')){
    // résultat
    $bool = bp_activity_set_action( get_ActivityTennisDefi_Component(), get_ActivityResultat_Type(), "déclaration de résultats");
    // défi
    $bool = bp_activity_set_action( get_ActivityTennisDefi_Component(), get_ActivityDefi_Type(), "défi Tennis Défi");
    // recherche de partenaire
    $bool = bp_activity_set_action( get_ActivityTennisDefi_Component(), get_ActivityRecherchePartenaire_Type(), "recherche d'un partenaire");
    // remplace de partenaire
    $bool = bp_activity_set_action( get_ActivityTennisDefi_Component(), get_ActivityRemplacePartenaire_Type(), "recherche d'un remplaçant");
    
	}
}
add_action('init', 'init_budypressActivity_Actions');



//========================================
/*! \brief  Gestion  filtres de recherche sur pages activité
*/
// =======================================  
function display_activity_actions() {
    $bp_activity_actions = buddypress()->activity->actions;
    // Recupération des listes d'actions
    $bp_plugin_actions = array();
    if( !empty( $bp_activity_actions->{get_ActivityTennisDefi_Component()} ) )
        $bp_plugin_actions = array_values( (array) $bp_activity_actions->{get_ActivityTennisDefi_Component()} );

    if( empty( $bp_plugin_actions ) )
        return;
 
    foreach( $bp_plugin_actions as $type ):?>
<option value="<?php echo esc_attr( $type['key'] );?>"><?php echo esc_attr( $type['value'] ); ?></option>
<?php endforeach;
}

add_action( 'bp_activity_filter_options',         'display_activity_actions'  );
add_action( 'bp_member_activity_filter_options',  'display_activity_actions'  );






// ==========================
// Page Activité : Ajout avant bp_before_activity_loop de stat 
//============================

// define the bp_before_activity_loop callback
function tennisdefi_bp_before_activity_loop(  ) 
{
	global $current_user;
	
	//  Ajout du choix du club
	addTitleAndSelectBox(false); 
	
	//write_log("*************tennisdefi_bp_before_activity_loop **************");
	//Init
	$ACTIVITY_synthese = array(
			get_ActivityRecherchePartenaire_Type () => 0,
			get_ActivityRemplacePartenaire_Type () => 0,
			get_ActivityDefi_Type () => 0,
			get_ActivityResultat_Type () => 0 
	);

	//Club de l'utilisateur
	$current_user_idclub        =  get_the_author_meta(TENNISDEIF_XPROFILE_idClub, $current_user->ID, true) ;
	
	
	// Analyse
	if (bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) : 
	  	 while ( bp_activities() ) : 
				bp_the_activity();
			// On traite par club
				$activity_clubID = bp_activity_get_meta(bp_get_activity_id(), TENNISDEIF_XPROFILE_idClub, true);
	
				if($current_user_idclub == $activity_clubID){
						$type = bp_get_activity_type();
						$ACTIVITY_synthese[$type] ++;
						//write_log("\t".$type);
				}
	     endwhile;
	 endif; 
		
	 //echo "<pre>"; print_r($ACTIVITY_synthese ); echo "</pre>";
	
	 
	 $NB_resultat  = $ACTIVITY_synthese[get_ActivityResultat_Type()];
	 $NB_recherche =  $ACTIVITY_synthese[get_ActivityRecherchePartenaire_Type()] 
	 				+ $ACTIVITY_synthese[get_ActivityRemplacePartenaire_Type()] 
	 				+ $ACTIVITY_synthese[get_ActivityDefi_Type()]  ;

	
	 //write_log("NB resultat : ".$NB_resultat);
	 //write_log("NB recherche : ".$NB_recherche);
	 
	
	  if($NB_resultat or $NB_recherche){
		echo "<div>";
		// Resultats
		echo do_shortcode ( '[one_half]
		[box title="" border_width="1" border_style="solid" text_color="#175579" border_color="#175579" align="center"]
		<h3 style="color: #175579;"><span class="dashicons dashicons-clipboard"></span>&nbsp;&nbsp;Déclarations de résultats</h3>
		[counter number="' . $NB_resultat . '" color="#175579" size="medium" animation="2000"]
		[/box][/one_half]' );
		
		// REcherche /défi joueur
		echo do_shortcode ( '[one_half_last][box title="" border_width="1" border_color="#175579" border_style="solid" align="center" text_color="#448968"]
<h3 style="color: #175579;"><span class="dashicons dashicons-universal-access"></span>&nbsp;&nbsp;Défis et recherches de joueurs</h3>
[counter number="' . $NB_recherche . '" color="#175579" size="medium" animation="2000"]
[/box][/one_half_last]' );
		
		echo "</div>";
	}
	 
	 
    // make action magic happen here...
};
        
// add the action
add_action( 'bp_before_activity_loop', 'tennisdefi_bp_before_activity_loop', 10, 0 );


// ==========================
// Page Activité : Ajout pour chaque activité un lien fgacebook et tweeter
//============================

// define the bp_before_activity_loop callback
function tennisdefi_bp_activity_entry_meta(  )
{
       // Tennis DEFI : Ajout du bouton facebook
               echo buddypress_facebook_share_activity_button();
               echo buddypress_twitter_activity_button();
};

// add the action
add_action( 'bp_activity_entry_meta', 'tennisdefi_bp_activity_entry_meta');





//************************************************************************
//Getter / Setter
//************************************************************************
//========================================
/*! \brief  Séeries de fonction pour : get_ActivityTennisDefi_Component(),get_ActivityResultat_Type,get_ActivityDefi_Type,get_ActivityRecherchePartenaire_Type
*/
// =======================================  
function get_ActivityTennisDefi_Component(){
    return "Tennisdefi";
}


function get_ActivityResultat_Type(){
    return "new_resultat";
}
function get_ActivityDefi_Type(){
    return "new_defi";
}
function get_ActivityRecherchePartenaire_Type(){
    return "new_recherchePartenaire";
}
function get_ActivityRemplacePartenaire_Type(){
	return "new_remplacePartenaire";
}

// Gestion des champs xProfiles
function get_Xprofilefield_recherchePartenaires($user_Id){
    return xprofile_get_field_data( TENNISDEIF_XPROFILE_rechjoueur, $user_Id ); /// \todoVerif  La valeur du champ doit etre setté en fonction du site 
}
// Gestion des champs xProfiles
function get_Xprofilefield_Nom($user_Id){
    //return xprofile_get_field_data( '1', $user_Id ); //// \todoVerif  La valeur du champ doit etre setté en fonction du site 
}
// Gestion des champs xProfiles
function get_Xprofilefield_Prenom($user_Id){
    //return xprofile_get_field_data( '2', $user_Id ); /// \todoVerif  La valeur du champ doit etre setté en fonction du site 
}
   

    