<?php

/*! \file functions.php

     \brief Contient et appelle toutes les fonctions php du theme

    

    Details.

*/


// Modifications de wp-login.php

//-----------------------------------------

/* function custom_login_logo() {
    echo '<style type="text/css">
        h1 a { background-image:url('. get_bloginfo( 'template_directory' ) .'/images/logo_tennis_defi_blanc_80x80.png) !important; }
    </style>';
}

add_action('login_head', 'custom_login_logo');

function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'tennis-défi.com, premier tournoi interactif';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );*/


// Fonctions modifiant le theme
//-----------------------------------------
include('fonctions/functions_theme.php');


// TEnnis-DEFI : Le pannel coté ADMIN
//----------------------------------------
include('fonctions/functions_adminPanel.php');
include('fonctions/functions_adminPanel_stats.php');
include('fonctions/functions_adminPanel_updateTCL.php');
include('fonctions/functions_adminPanel_gestionUsers.php');

// Fonctions de sécurité
//-----------------------------------------
include('fonctions/functions_securite.php');



//Fonction d'inclusion des librairies javascript
//-----------------------------------------
include('fonctions/function_lib.php');


// Gestions des posts
//------------------------------------------
include('fonctions/functions_posts.php');

// Gestions des users coté admin
//------------------------------------------
include('fonctions/functions_utilisateurs.php');


// Gestion de BUDDY PRESS
// -----------------------------
include('fonctions/functions_buddypress.php');



//fonction GestionsUSER
//------------------------------------------
include('fonctions/functions_gestionUsers.php');
include('fonctions/functions_gestionUsers2.php');



//fonction moteur de Tennis Defi: Declarer resultat
//------------------------------------------
include('fonctions/functions_declarerResultat.php');


// GEstion Tournois
// -----------------------------------------
include('fonctions/functions_tournois.php');


//fonction Ajax
//------------------------------------------
include('fonctions/functions_ajaxCLubs.php');
include('fonctions/functions_Palmares.php');



//fonction Mails
//------------------------------------------
include('fonctions/functions_mails.php');



// Fonction d'importation
//------------------------------------------
include('fonctions/functions_importation.php');

// Foonctions Gestions affichage BackEnd POSTS
include('fonctions/functions_DisplayPost.php');

// Fonction gestion adminitration de club par les joueurs Admin
include('fonctions/functions_gestionCLub_ByAdmin.php');





//========================================

/*! \brief Permet de crypter/decrypter des données: notamment l'ID du joueur

      source : http://naveensnayak.wordpress.com/2013/03/12/simple-php-encrypt-and-decrypt/

*/

//=========================================

function encrypt_decrypt($action, $string) {

    $output = false;



    $encrypt_method = "AES-256-CBC";

    $secret_key = 'clé secrète TennisDefi de cryptage';

    $secret_iv  = 'clé secrète TennisDefi de décryptage';



    // hash

    $key = hash('sha256', $secret_key);

    

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning

    $iv = substr(hash('sha256', $secret_iv), 0, 16);



    if( $action == 'encrypt' ) {

        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);

        $output = base64_encode($output);

    }

    else if( $action == 'decrypt' ){

        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

    }

    

    

    return $output;

}









//========================================

/*! \brief Permet d'ecrire dans el ficheir Debug.log(dossier wp_content)

*/

//=========================================

if (!function_exists('write_log')) {

    function write_log ( $log )  {

        if ( true === WP_DEBUG ) {

            if ( is_array( $log ) || is_object( $log ) ) {

                error_log( print_r( $log, true ) );

            } else {

                error_log( $log );

            }

        }

    }

}

//========================================
/*! \brief Permet d'afficher à l'écran le contenu d'un tableau
*/
//=========================================

if (!function_exists('frontEnd_display_array')) {

	function frontEnd_display_array ( $tab )  {

		echo "<pre>"; print_r($tab); echo"</pre>";
	}

}
