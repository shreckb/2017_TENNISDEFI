<?php

/*! \file
     \brief Contient les fonctions modifiant le theme
    
    Details.
*/


// ===============
// FrontEnd : Ajout d'une page administrateur de club + Gestion tournoi
// =================
add_filter('wp_nav_menu_items','addMenu_AdminClub_PageLink');
 function addMenu_AdminClub_PageLink ($nav){
 	
 	global $current_user;
 	$current_club = get_the_author_meta ( 'tennisdefi_idClub', $current_user->ID,true );
 	$isAdminCLub = isUserAdminInClub($current_user->ID, $current_club);
 	
 	
 	if($isAdminCLub){
       // write_log("USER IS ADLIN");
 		$url_1 = get_page_link( get_IDpage_gestion_ClubByAdmin() );
 		$urL_2 = get_page_link( get_IDpage_gestion_ClubByAdmin_Tournois() );
 		$url_3 = get_page_link( get_IDpage_palmares_custom() );

 		$menu_admin = "<li ><a href=\"$url_1\">Administration</a>";
 		$menu_admin  .= "<ul>";
					$menu_admin .=	"<li><a href=\"$url_1\">Gestion du Club</a></li>";
					$menu_admin .=	"<li><a href=\"$urL_2\">Tournois</a></li>";
					//$menu_admin .=	"<li><a href=\"$url_3\" target=\"_blank\">Palmarès Spécifiques</a></li>";
		$menu_admin .=	"</ul>
			</li>";
 		
 		$menu = $nav.$menu_admin;
 		
  	/*$menu = $nav."<li>
  				<a href='".$url."'>Gestion du Club</a></li>";*/
  	

 	}
 	else
 		$menu = $nav;

 	return $menu;
}

// ========================
/*! \brief En plus du titre de la page, permet d'ajouter le changement de club
*/
// =========================
function addTitleAndSelectBox($show_title = true) {
	// SI plusieurs clubs : on affiche le changement de club
	global $current_user;
	get_currentuserinfo ();
	$current_club = get_the_author_meta ( 'tennisdefi_idClub', $current_user->ID );
	$clubs = get_user_meta ( $current_user->ID, TENNISDEIF_XPROFILE_idclubs, true );


	if (count ( $clubs ) > 1) {
		//Le joueur à pluisuers clubs
		?>
<div class="grid col-940">
	<div class="grid col-700"
		style="width: 50%; margin-bottom: 0; padding-bottom: 0">
		<?php 
		if($show_title)
			echo '<h1 class="entry-title post-title">'.get_the_title().'</h1>';
		?>
	</div>
	<div class="grid col-220 fit" align="center"
		style="width: 45%; text-align: right; float: right; margin-bottom: 0; padding-bottom: 0">
		<form id="tennisdefi_sidebar_data_form" method="POST">
			<SELECT name="CLUB_selected" id="CLUB_selected" size="1">
					<?php
							foreach ( $clubs as $club_ID ) {
									$club_titre = get_the_title ( $club_ID );
									$id_club_crypted = encrypt_decrypt ( 'encrypt', $club_ID );
									if ($club_ID == $current_club)
										echo '<OPTION VALUE="' . $id_club_crypted . '" selected>' . $club_titre;
									else
										echo '<OPTION VALUE="' . $id_club_crypted . '" >' . $club_titre;
							}
							?>
					</SELECT>
							<?php 
							//Securité du formulaire
							wp_nonce_field( 'tennisdefi_ChangerClub_data_securite', 'tennisdefi_ChangerClub_data_nonce');
							//bouton
							$url_gestion = get_page_link(get_page_selection_club_id()); 
							echo '<input type="submit" name="boutontennisdefi_changeClub"
										id="boutontennisdefi_changeClub" class="Classe_boutontennisdefi_changeclub" value="Changer de club" />';
							//echo '<a href="'.$url_gestion.'">gérer</a>';
								
							// Gestion changement
							$url_image = get_stylesheet_directory_uri ().'/images/loading.gif';
						
							echo '<span id="tennisdefi_sidebar_data_LoadingImage" style="display: none">
							  	 <img  src="'.$url_image.'" width="20" /></span>';
							  	 
					?>

					</form>

		<script type="text/javascript">
				jQuery("#boutontennisdefi_changeClub").click(
				function(event) {

					// Prevent Default Action
					event.preventDefault();
						
					// Icone Chargement + disable du bouton 
					jQuery("#tennisdefi_sidebar_data_LoadingImage").show();
					// Desactivation du buton
					jQuery("#tennisdefi_sidebar_data_submit").attr("disabled",
					"disabled");
				
					// AJAX
					 jQuery.ajax({
						 	url:  ajaxurl, 
						 	data:{
					               action 			: 'tennisdefi_sidebar_data',
								   nonce 			: jQuery('#tennisdefi_ChangerClub_data_nonce').val(),
								  'club_selected' 	: jQuery('#CLUB_selected').val()
					             },
				          dataType: 'JSON',
				          success:function(){
				          		jQuery("#tennisdefi_sidebar_data_LoadingImage").hide();
								jQuery("#boutontennisdefi_changeClub").removeAttr(
										"disabled");
								//recharge la page (sans les donnees postee)
								window.location = window.location.href;


				          		//alert("OK !");
				 
				            }, // Fin success(data)

				          error: function(errorThrown){
				          		jQuery("#tennisdefi_sidebar_data_LoadingImage").hide();
						  		jQuery("#boutontennisdefi_changeClub").removeAttr(
										"disabled");
				              //alert("KO !");           
				                         } // Fin error(data)
 
					}); // FIN AJAX
				

				
				});
						</script>
	</div>
</div>
<?php 				
									
	}//fin je joueur à des club
	else{
		// Le joueur à 1 club
		if($show_title)
			echo '<h1 class="entry-title post-title">'.get_the_title().'</h1>';
	}
	
	
}


// ========================================
/* ! \brief Permet de gerer la requete ajax de changement de club associé avec la fonction addTitleAndSelectBox()*/
// =========================================
function process_ajaxChangerClub() {
		
		global $current_user;   
		//write_log("hello plugin sidebar...process_ajax()");
		header('Content-Type: application/json');

		// Check the nonce field  
		if (!check_ajax_referer( 'tennisdefi_ChangerClub_data_securite', 'nonce', false ) && !isset($_REQUEST['club_selected'])) {				
			$return_args = array(
				"result" => "Error",
				"message" => "403 Forbidden",
				);
			$response = json_encode( $return_args );
			echo $response;

			die;
			}
		
		// PArtie traitement 
			$user_clubs = get_user_meta($current_user->ID,  TENNISDEIF_XPROFILE_idclubs, true); // ts les clubs du joeuurs 
 		
			$ID_club_crypted =$_REQUEST['club_selected'];
			$Id_club_selected = encrypt_decrypt('decrypt', $ID_club_crypted); 

		// Le club doit etre dans les clubs du joueur		
		 if(!in_array($Id_club_selected, $user_clubs)){	
		 			$return_args = array(
				"result" => "Error",
				"message" => "403 Forbidden",
				);
			$response = json_encode( $return_args );
			echo $response;
			die;
			}
		 
		 // Mise à jour du club courrant du joueur
		update_user_meta( $current_user->ID, TENNISDEIF_XPROFILE_idClub, $Id_club_selected);
		$message ="Club = $Id_club_selected ($ID_club_crypted)";
		$return_args = array(
			'message' => $message,
			);
			
		$response = json_encode( $return_args );
		echo $response;

		die;

	} // end process_ajaxChangerClub      

   // Ajax
   add_action( 'wp_ajax_nopriv_tennisdefi_sidebar_data', 'process_ajaxChangerClub' );               
   add_action( 'wp_ajax_tennisdefi_sidebar_data', 'process_ajaxChangerClub' );   



// ========================
/*!MOFIFICATIONS WP-LOGIN.PHP */
// =========================
/*login stylesheet */
function custom_login_css()  {
    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/style-login.css" />';
}
add_action('login_head', 'custom_login_css');


/* changement URL logo */
function custom_url_login()  {
    return home_url();
    //return get_bloginfo( 'url' ); // On retourne l'index du site
}
add_filter('login_headerurl', 'custom_url_login');

/* changement attibut title du logo */
function custom_title_login($message) {
     return 'tennis-défi.com, premier tournoi interactif';
    //return get_bloginfo('description'); // On retourne la description du site
}
add_filter('login_headertitle', 'custom_title_login');


//========================================
/*! \brief REmplacement du gravatar par défautpar un du site
*/
//=========================================
//http://www.lejournaldublogueur.fr/comment-installer-gravatar-sur-wordpress/
function newgravatar ($avatar_defaults) {
    $myavatar = get_bloginfo('stylesheet_directory') .'/images/own-gravatar.jpg';
    write_log("========newgravatar() URL=>".$myavatar);
    $avatar_defaults[$myavatar] = "Tennis Défi";
    return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'newgravatar', 1 );




//========================================
/*! \brief masquage du sous menu Gerer son club (Sera utilisée pour la page club)
*/
//========================================
//http://justintadlock.com/archives/2011/06/13/removing-menu-pages-from-the-wordpress-admin
add_action( 'admin_menu', 'gestion_sous_menu_gererClub', 999 );
function gestion_sous_menu_gererClub() {
    //remove_menu_page( 'gerer-son-club' );
	//remove_submenu_page( 'gerer-son-club.php', 'theme-editor.php' );

}



//========================================
/*! \brief  taille de vignette pour les clubs, utilisé ?
*/
//========================================
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 50, 50);
add_image_size( $name = 'club_thumbnails', $width = 50, $height = 50, $crop = false );


// =======================
/*! \brief Activation / Désactivtion de lien dans l'Admin Bar
*/
// =======================
function wps_admin_bar() {

    global $wp_admin_bar;

    if( ! current_user_can('administrator') ){
	$wp_admin_bar->remove_menu('wp-logo'); // Cette ligne désactive le logo WP et le menu associé

    //$wp_admin_bar->remove_menu('about'); // Cette ligne désactive le menu d'acces " A propos de WordPress " 
    $wp_admin_bar->remove_menu('wporg'); // Cette ligne désactive le menu d'acces a WordPress.org
    $wp_admin_bar->remove_menu('documentation'); // Cette ligne désactive le menu d'acces a la documentation de WordPress 
    $wp_admin_bar->remove_menu('support-forums'); // Cette ligne désactive le menu d'acces au forum de WordPress
    $wp_admin_bar->remove_menu('feedback'); // Cette ligne désactive le menu d'acces au Remarque
    $wp_admin_bar->remove_menu('view-site'); // Cette ligne désactive le lien vers le tableau de bord de WordPress
    $wp_admin_bar->remove_menu('site-name'); // Cette ligne désactive le menu d'acces au tableau de bord
	$wp_admin_bar->remove_menu('dashboard'); // Cette ligne désactive le lien associé au nom du blog vers le tableau de bord de WordPress
	$wp_admin_bar->remove_menu('themes'); // Cette ligne désactive le lien vers les options du thème.
    $wp_admin_bar->remove_menu('search'); // Cette ligne désactive la fonction rechercher
    /*
	$wp_admin_bar->remove_menu('widgets'); // Cette ligne désactive le lien vers les options des widgets
	$wp_admin_bar->remove_menu('menus'); // Cette ligne désactive le lien vers l'option menus
	$wp_admin_bar->remove_menu('menus'); // Cette ligne désactive le lien vers l'option menus

	$wp_admin_bar->remove_menu('updates'); // Cette ligne désactive l'icon des mise à jours

	$wp_admin_bar->remove_menu('comments'); // Cette ligne désactive l'icon des commentaires

	// $wp_admin_bar->remove_menu('new-content'); // Cette ligne désactive l'icon et le menu nouveau

	$wp_admin_bar->remove_menu('new-post'); // Cette ligne désactive le lien ajouter un nouvelle article

	$wp_admin_bar->remove_menu('new-media'); // Cette ligne désactive le lien vers la bibliothèque multimédia

	$wp_admin_bar->remove_menu('new-link'); // Cette ligne désactive le lien ajouter un nouveau lien

	$wp_admin_bar->remove_menu('new-page'); // Cette ligne désactive le lien ajouter une page

	$wp_admin_bar->remove_menu('new-user'); // Cette ligne désactive le lien ajouter une page

	$wp_admin_bar->remove_menu('edit'); // Cette ligne désactive le lien modifier la page

	

	// $wp_admin_bar->remove_menu('my-account'); // Cette ligne désactive le menu Utilisateur

	$wp_admin_bar->remove_menu('user-info'); // Cette ligne désactive les informations de utilisateur

	$wp_admin_bar->remove_menu('edit-profile'); // Cette ligne désactive le menu d'acces a l'éditeur du profile utilisateur

	$wp_admin_bar->remove_menu('logout'); // Cette ligne désactive le deconnecter pour les utilisateur
    */
    }

}
add_action( 'wp_before_admin_bar_render', 'wps_admin_bar' );





// ========================
/*! \brief Ajoute le javascript à toute les pages du site : Favicon du site, Ajout FaceBokk/Google+/Twitter ""PArtage""
*/
// =========================
function hook_javascript_header()
{

	// Logo du site
	//$output = '<link rel="shortcut icon" href="'.get_stylesheet_directory_uri().'/images/favicon.ico" />';
	$output = '<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/android-chrome-manifest.json">
	<meta name="msapplication-TileColor" content="#be441b">
	<meta name="msapplication-TileImage" content="/mstile-144x144.png">
	<meta name="theme-color" content="#ffffff">';
	
	
        // Ajout des polices pour IE
    $output = '<script type="text/javascript">
  WebFontConfig = {
    google: { families: [ \'Raleway::latin\', \'Open+Sans::latin\' ] }
  };
  (function() {
    var wf = document.createElement(\'script\');
    wf.src = (\'https:\' == document.location.protocol ? \'https\' : \'http\') +
      \'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js\';
    wf.type = \'text/javascript\';
    wf.async = \'true\';
    var s = document.getElementsByTagName(\'script\')[0];
    s.parentNode.insertBefore(wf, s);
  })(); </script>';
    
	/*
	//Google+ (une partie est dasn le header voir ci-dessous)
	$output = '<script src="https://apis.google.com/js/plusone.js"></script>';
	//<!-- Ajoutez les trois balises suivantes dans l'en-tête. -->
$output .= '<meta itemprop="name" content="Tennis Defi">';
$output .= '<meta itemprop="description" content="Gregoire BEZOT à Battu Francis Bezot 6/4 7/6">';
$output .= '<meta itemprop="image" content="'.get_bloginfo('stylesheet_directory') .'/images/default-logo-orange.png">';
    */
    
    // Twitter
$output .='<meta name="twitter:card" content="summary">';
$output .='<meta name="twitter:site" content="@Tennisdefi">';
$output .='<meta name="twitter:title" content="Tennis Défi">';
$output .='<meta name="twitter:description" content="Premier tournoi interactif. 
Pour le joueur: Des partenaires, à son niveau, immédiatement.
Pour le Club  :  Un tournoi interactif, autogéré, des joueurs attirés et fidélisés ">';
$output .='<meta name="twitter:image" content="'.get_bloginfo('stylesheet_directory') .'/images/default-logo-orange_format_4-3.png">';
	


/*
$output .= '<meta name="twitter:card" content="summary_large_image">';
$output .= '<meta name="twitter:site" content="@Tennisdefi">';
//$output .= '<meta name="twitter:creator" content="@SarahMaslinNir">';
$output .= '<meta name="twitter:title" content="Tennis Défi">';
$output .= '<meta name="twitter:description" content="Premier tournoi interactif. 
Pour le joueur: Des partenaires, à son niveau, immédiatement.
Pour le Club  :  Un tournoi interactif, autogéré, des joueurs attirés et fidélisés ">';
$output .= '<meta name="twitter:image:src" content="'.get_bloginfo('stylesheet_directory') .'/images/default-logo-orange.png">';
*/

	// demonstration avec INTRO.JS
	$output.='<link href="'.get_stylesheet_directory_uri().'/js/Intro_2_0_minified/introjs.min.css" rel="stylesheet">';
	$output.='<script type="text/javascript" src="'.get_stylesheet_directory_uri().'/js/Intro_2_0_minified/intro.min.js"></script>';
	$output .='<script type="text/javascript" src="'.get_stylesheet_directory_uri().'/js/demonstration.js"></script>';

	//$output="<script> alert('Page is loading...'); </script>";
	echo $output;
}
add_action('wp_head','hook_javascript_header');

// ========================
/*! \brief Ajoute le javascript à toute les pages du site : Ajout Google Analytics , FaceBokk/Google+/Twitter ""PArtage""
*/
// =========================
function hook_javascript_footer()
{
	
	
	//Twitter
    $output = '<script type="text/javascript">
window.twttr=(function(d,s,id){var t,js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id)){return}js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);return window.twttr||(t={_e:[],ready:function(f){t._e.push(f)}})}(document,"script","twitter-wjs"));
</script>';

    // Google Analytics => par le plugin  : Google Analytics by Yoast
    /*
    $output .= "<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-48734347-1', 'auto');
  ga('send', 'pageview');
			</script>";
			*/
    
	echo $output;
}
add_action('wp_head','hook_javascript_footer');


// Javascript Google dans le header
// ----------------------------------
function add_google_HTML_Att($language_attributes) 
{
	
	$output = $language_attributes;
	$output .= ' itemscope itemtype="http://schema.org/Product"';
    return $output;
}

add_filter('language_attributes', 'add_google_HTML_Att');

//==========================
//Envoie les mail en html
//=========================
function tennisdefi_set_html_mail_content_type() {
    			return 'text/html';
}
//GEstion du Name
// Function to change sender name
function tennisdefi_sender_name( $original_email_from ) {
	write_log("********tennisdefi_sender_name*********");
	write_log("\toriginal_email_from = ".$original_email_from);
	
   	if($original_email_from == "Wordpress" )
    	return 'Tennis-Défi';
    else
    	return $original_email_from;
}
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );

//exemple
//---------
/*
add_filter( 'wp_mail_content_type', 'tennisdefi_set_html_mail_content_type' );
 
$to      = 'sendto@example.com';
$subject = 'The subject';
$body    = 'The email body content';
 
wp_mail( $to, $subject, $body );
 
// Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
*/
//------------


