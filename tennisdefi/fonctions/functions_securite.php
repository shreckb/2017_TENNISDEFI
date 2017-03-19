<?php

/*! \file
     \brief Contient les fonctions sur la Securité Accès , redirection...
    
    Details.
*/


//========================================
/*! \brief Defini l'ID de la  page du palmares impirmable
     \todoVerif  La valeur de cette fonction doit etre settée(get_IDpage_palmares_imprimable)
*/
//=========================================
function get_IDpage_gestion_Partenaires(){
	return "24824"; // ID de la page pour les administrateur du club
	/// \attention l'ID retournée doit  existé
}

function get_IDpage_gestion_ClubByAdmin(){
	return "24388"; // ID de la page pour les administrateur du club
	/// \attention l'ID retournée doit  existé
}
function get_IDpage_gestion_ClubByAdmin_Tournois(){
	
	
	//return "25137"; // ID de la page pour les administrateur du club
	return "25175"; //=> Sur le serveur 
	/// \attention l'ID retournée doit  existé
}


function get_IDpage_palmares_imprimable(){
      return "9601"; //"9596"; // ID de la page pour que palameres imprimable
            /// \attention l'ID retournée doit  existé
}

function get_IDpage_palmares_custom(){
      return "24983";  // ID de la page pour que palameres customisé (vu par l'admin du club )
            /// \attention l'ID retournée doit  existé
} 

function get_IDpage_inscription(){
      return "27"; 
            /// \attention l'ID retournée doit  existé
}

function get_IDpage_condition_utilisation(){
      return "24294"; //"9596"; // ID de la page pour que palameres imprimable
            /// \attention l'ID retournée doit  existé
}

function get_IDpage_mustbelog(){ // Page de renvoie quand le user n'est pas logé
      return "24318"; // ID de la page pour que palameres imprimable
            /// \attention l'ID retournée doit  existé
}
function get_IDpage_DefiRecherchePartenaires(){ // Page de renvoie quand pas logé
      return "21"; // ID de la page pour que palameres imprimable
            /// \attention l'ID retournée doit  existé
}

function get_IDpage_Palmares(){ // Page Palamres
      return "17"; // ID de la page pour que palameres imprimable
            /// \attention l'ID retournée doit  existé
}

function get_IDpage_MapClubs(){ // Page Palamres
      return "11"; // ID de la page pour que palameres imprimable
            /// \attention l'ID retournée doit  existé
}
function get_IDpage_Contact(){ // Page Palamres
      return "24338"; // ID de la page pour que palameres imprimable
            /// \attention l'ID retournée doit  existé
}

function get_page_DeclareMailFraude(){ //Defini l'ID de la  page de déclaration de detournement mail
	return "24365"; // ID de la page  
	/// \attention l'ID retournée doit existé
}

function get_page_DeclarerResultat(){ //Defini l'ID de la  page de déclaration de detournement mail
	return "14"; // ID de la page
	/// \attention l'ID retournée doit existé
}

//========================================
/*! \brief Defini l'ID de la  page de selection du club
     \todoVerif  La valeur de cette fonction doit etre settée(get_page_selection_club_id)
*/
//=========================================
function get_page_selection_club_id(){
      return "43"; // ID de la page pour que le user selectionne son club
            /// \attention l'ID retournée doit etre existé
}

//========================================
/*! \brief Defini l'ID de la  page des stats du joueur
 \todoVerif  La valeur de cette fonction doit etre settée(get_page_selection_userStat)
 */
//=========================================
function get_page_selection_userStat(){
	return "19"; 
	/// \attention l'ID retournée doit etre existé
}

//========================================
/*! \brief Defini l'ID de la  page activite
 \todoVerif  La valeur de cette fonction doit etre settée(get_page_selection_userStat)
 */
//=========================================
function get_page_activity_url(){
	$page_activite_id = 9;
	return get_page_link( $page_activite_id);
	/// \attention l'ID retournée doit etre existé
}


//========================================
/*! \brief Defini la restriction des pages et redirection vers la page d'acceuil

    la liste des pages $page_allowed doit etre exaustive et la fonction "get_page_selection_club_id" bien renseigné pour la redirection
        \todoVerif La liste des page "$page_allowed" doit etre correctement remplie   
*/
//=========================================
function redirect_tennisdefi_user()
{
	//return;
	
        $page_allowed = array( 0, 7 ,29, 96, 9598, 24274, 
        				get_IDpage_MapClubs(),
        				get_IDpage_inscription(),
        				get_IDpage_condition_utilisation(),
						get_IDpage_mustbelog(),
						get_IDpage_Contact(),
						get_page_DeclareMailFraude(),
		); // ajout acceuil temp quand sur le serveur + declarerFraude
                // La page zero correspond au fait que quand on active buddypress le fait d'avoir une page avec tennisdefi.com/activatebp/key ne donne pas l'ID 29 mais zero//$page_allowed = array( 10, 5, 27 ,29, 96,24274); // page accessibles par tous (clubs, accueil, inscription, activerBP, importton, declarerFraude
    //$page_allowed = array( 11, 7, 27 ,29, 96,24274); // page accessibles par tous (clubs, accueil, inscription, activerBP, importation,declarerFraude ) 
    /// \attention La liste des pages "$page_allowed" doit etre correctement remplie
       
              
    $page_sans_club = array(get_page_selection_club_id()); // Page où il faut avoir un club associé
    
          // si ce n'est pas une page, on ne fait rien
          if ( ! is_page() ) return;

          // Pages Profile Buddypress => C'est une page mais l'ID vaut 0 comme la page de registration
          // bp_is_my_profile = page de profile meme si pas ma page...
          if( !is_user_logged_in()  && bp_is_user_profile()){
          	wp_redirect( get_page_link( get_IDpage_mustbelog()  ) );
          	exit();
          }
          
         /* 
         write_log("***************redirect_tennisdefi_user*****************");
          write_log("On est sur une page et get_queried_object_id = ".get_queried_object_id());
          if(is_user_logged_in() )
          	write_log(" is_user_logged_in = true");
          else  write_log(" is_user_logged_in = false");
          
          if(bp_is_user_profile() )
          		write_log(" bp_is_user_profile = true");
          else  write_log(" bp_is_user_profile = false");
*/
          
          
          
           // si c'est une page autorisée à tous, on ne fait rien
          if ( in_array( get_queried_object_id(), $page_allowed ) ) 
              return;

          // Sinon, il faut etre loggué, redirection vers la page de log
          if( ! is_user_logged_in() ){
               wp_redirect( get_page_link( get_IDpage_mustbelog()  ) );
            exit();
          }
    
          
          
    		// Si Joueur logué et page Selection club : on ne fait rien 
    		if(get_queried_object_id() == get_page_selection_club_id())
    							return;
    
        // Sinon, il faut etre loggué et avoir un club => Selection du club
            global $current_user;
			$user_idclub = get_user_meta( $current_user->ID, TENNISDEIF_XPROFILE_idClub, true);
          if(empty($user_idclub)){
              wp_redirect( get_page_link( get_page_selection_club_id()).'?redirect=1' ); 
            exit();
          }
}
add_action( 'template_redirect', 'redirect_tennisdefi_user' );

//========================================
/*! \brief Redirection vers le choix du club à la connexion
*/
//========================================
function my_login_redirect( $redirect_to, $request, $user ) {
    $id_page_Selectionclub =  get_page_selection_club_id();  
    
  
    global $user;
    $user_idclub =  get_the_author_meta( TENNISDEIF_XPROFILE_idClub, $user->ID ) ;
    
      if(empty($user_idclub))
        return get_page_link(get_page_selection_club_id());
      else
          return get_page_link(get_page_selection_userStat());
}
add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

//========================================
/*! \brief Interdire l'accès au page admin aux non admin
*/
//========================================
function redirect_non_admin_users() {
       if( !defined('DOING_AJAX') && !current_user_can('manage_options') ){
        wp_redirect( home_url() );
        exit();
        }
}
add_action( 'admin_init', 'redirect_non_admin_users' );