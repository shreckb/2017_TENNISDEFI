<?php 
// Affihage AJAX 
//==============================================
// La recherche de latitude long doit etre mis en pause

// A supprimer ultérieurement
error_reporting(E_ALL ^ E_DEPRECATED);
    
add_action('wp_ajax_nopriv_do_ajax_import', 'notre_fonction_ajax_importation');
add_action('wp_ajax_do_ajax_import', 'notre_fonction_ajax_importation');
function notre_fonction_ajax_importation(){

	set_time_limit(0);// Permet d''eviter les probleme de time out.
	
    $output = "pas d'info";
    
    error_log('Dct Appellee: '.$_REQUEST['fn']);

   // ce switch lancera les fonctions selon la valeur qu'aura notre variable 'fn'
     switch($_REQUEST['fn']){

          case 'initIMPORT':
            $output = initIMPORT();
            break;
         
         case 'getClubsNumber':
            $output = getClubNum();
            break;
         
         
         case 'get_clubs':
            $output = IMPORT_create_clubs($_REQUEST['start'], $_REQUEST['step']);
            break;
         case 'update_clubs_LatLng':
            	$output = IMPORT_update_clubs_LatLng($_REQUEST['step'], $_REQUEST['max_step']);
            	break;
         
         case 'get_users':
            $output = IMPORT_create_users($_REQUEST['start'], $_REQUEST['step']);
            break;
         
        case 'update_users':
            $output = IMPORT_update_users($_REQUEST['start'], $_REQUEST['step']);
            break;
            
        case 'IMPORT_user_match_multiclub': 
        	
        	$output = IMPORT_user_match_multiclub($_REQUEST['start'], $_REQUEST['step']);
        	break;
        	
        case 'IMPORT_user_matchTCL':
        		$output = IMPORT_user_match_TCL( $_REQUEST['start_match']);
        		break;
        	

         
     case 'update_usersTEMP':
            $output = IMPORT_update_usersTEMP($_REQUEST['start'], $_REQUEST['step']);
            break;
         
          default:
            $output = 'No function specified, check your jQuery.ajax() call';
            break;
     }
     
  
    
   // Maintenant nous allons transformer notre résultat en JSON et l'afficher
     $output=json_encode($output);
     
    //error_log( $output);
    //error_log("****** $output*********");
    //return $output;
    
    if(is_array($output)){
        print_r($output);
     }
     else{
        echo $output;
     }
    
    //error_log("************** Fin gestion appel AJAX******************");
     die;
    
    

}



// ========================
/*! \brief Connexion à l'ancienne base de données */
// =========================
function connectBD_oldTENNISDEFI()
{
    // LOCAL
    $user ='root';
    $passwd ='root';
    $host ='localhost';
    $bdd ='TennisDefi_20130622';
    
    
   
    
    
    $mysqli = new mysqli($host, $user, $passwd , $bdd );
        if ($mysqli->connect_errno) {
            error_log("****** Connexion à la BDD KO*********");
            echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            die;
        }
        $mysqli->query("SET NAMES 'utf8'");
        
    
        return $mysqli;
    
}

// DéConnexion à l'ancienne base de données
function disconnectBD_oldTENNISDEFI($mysqli)
{
    $mysqli->close();   
}


function write_logImport($data , $ajouter_fin = 1){
    if($ajouter_fin)
        file_put_contents ( "Importation_GBEZOT.csv", $data , FILE_APPEND );
    else
        file_put_contents ( "Importation_GBEZOT.csv", $data  );
}



// ========================
/*! \brief Compter le nombre de clubs */
// =========================
function getClubNum(){
    
    // combien de club
    $mysqli = connectBD_oldTENNISDEFI();
        // CLUB
        $query = "SELECT * FROM clubs ORDER BY nbjoueurs DESC ";
        $results = $mysqli->query($query);
        $num_rows = $results->num_rows;
        $output = $num_rows;
    
        disconnectBD_oldTENNISDEFI($mysqli);
    return $output;
    
}

// ******************************************* 
// Init importation
// ******************************************* 
function initIMPORT(){
    
    
    // Init Log file
    write_logImport ("Importation du : ".date('Y-m-d H:i:s')."\n" , false );
    
    // combien de club
    $mysqli = connectBD_oldTENNISDEFI();
        // CLUB
        $query = "SELECT * FROM clubs ORDER BY nbjoueurs DESC ";
        $results = $mysqli->query($query);
        $num_rows = $results->num_rows;
        $output = "Initfile : importation_GBEZOT.txt   .......OK\r\nNB club: $num_rows";
    
        disconnectBD_oldTENNISDEFI($mysqli);
    return $output;
}
   



// ========================
/*! \brief  Recuperation des clubs par Club */
// =========================
function IMPORT_create_clubs($start, $step){
    
    $saveCLUB = true; // permet la mise en base ou non
    
        // CLUB
        $query = "SELECT * FROM clubs ORDER BY nbjoueurs DESC LIMIT $start,$step; ";
        $mysqli = connectBD_oldTENNISDEFI();
        $results = $mysqli->query($query);
    
    
    
   $data_club = array( 0 =>	'nomclub',
            1 =>	'nbjoueurs',
            2 =>	'nbmatchs',
            3=>         'nbpartenaires',
            4=>	        'nbpoints',
            5=>	        'sigle',
            6=>	        'dpt',
            7=>	        'idclub',
            8=>	        'ligue',
            9=>	        'correspondant',
            10=>	'dateinscri',
            11=>	'datetournoi',
            12=>	'datetournoifin',
            13=>	'president',
            14=>	'tresorier',
            15=>	'directeur',
            16=>	'enseignant',
            17=>	'secretaire',
            18=>	'topactif',
            19=>	'adresse',
            20=>	'adresse2',
            21=>	'cp',
            22=>	'ville',
            23=>	'tel',
            24=>	'siteweb',
            25=>	'reseau'
            );



    $mysqli = connectBD_oldTENNISDEFI();
    
    $clubs =[]; // ID des clubs
    
    for ($row_no = 0; $row_no < $results->num_rows ; $row_no++) {
                $results->data_seek($row_no);
                $row_club = $results->fetch_assoc();
                    
                // Creer compte et info  à faire pour ce club
                $Nom_club = Importation_gestionEncodageUTF8($row_club['nomclub']);
                $nbjoueur = $row_club['nbjoueurs'];
                $idclub   = $row_club['idclub'];
                $adresse = ($row_club['adresse']);
                $adresse2 = Importation_gestionEncodageUTF8($row_club['adresse2']);
                $cp = $row_club['cp'];
                $ville = Importation_gestionEncodageUTF8($row_club['ville']);
                $dpt = $row_club['dpt'];
        
                // Correction nom club
                
                
        if($idclub>50){
            //clubs avec ID <50 ce sont des clubs type dentaires....
        
        // creation du post club
         $post = array('post_type'	=> 'club',  'post_title'=> $Nom_club, 'post_status'	=> 'publish');
               if($saveCLUB){
                      $post_IDclub = wp_insert_post($post);  // Pass  the value of $post to WordPress the insert function
                                    
                      if( is_wp_error( $post_IDclub ) ) {
                          // Sortie disque à faire
                          write_logImport("Importation Club ; $idclub; $Nom_club; 99; erreur à la creation du club :  wp_insert_post retrourne: ".$post_IDclub.get_error_message() ."\n");
                        }
                       else{
                            // mise à jour data (pas le nom), pas l'idclub, pas le top actif .
                            for ($k=5; $k<count($data_club); $k++){
                                  if( ($k != 18) && ($k !=7))
                                     update_post_meta($post_IDclub, $data_club[$k],  $row_club[$data_club[$k]]) ;
                                    }
                            update_post_meta($post_IDclub, "old_idClub",  $idclub) ;

                           //CP
                           if($cp == null){
                             write_logImport("Importation Club ; $idclub; $Nom_club; 100; Le club n'a pas de Code postal\n");
                              }
                           
                           // Nombre de joueru à 0
                           update_post_meta($post_IDclub, TENNISDEIF_XPROFILE_nbJoueursClub,  0) ;
                           
                           
                           write_logImport("Importation Clubs ;$idclub;$Nom_club; 0; Pas d'erreur; Nb joueur: $nbjoueur; Code Postal: $cp; WordpressID: $post_IDclub\n");
                           

                       } // fin si error à la creatipon du post club

               }// fin si save club
            else{
                write_logImport("Importation Clubs ;$idclub;$Nom_club; $nbjoueur;$cp\n");
                }
            }//fin id club >50
    }//fin foreach lignes clubs
        
    
    disconnectBD_oldTENNISDEFI($mysqli);
    return "OK";
}
    


// ========================
/*! \brief  Recuperation des JOUEURS (resulats.) */
// =========================
function IMPORT_create_users($start, $step){
    
    // Permet de sauter la vérifiaction d'email
     define( 'WP_IMPORTING', 'SKIP_EMAIL_EXIST' );
    
     //connexion BDD
     $mysqli = connectBD_oldTENNISDEFI();
     
     
// Les clubs ont déja ete importés
    $saveJoueur = true;
    $saveCLUB = $saveJoueur;

        global $post;
        $args = array( 'posts_per_page' => $step, 'offset'=> $start, 'post_type' => 'club', 'orderby' => 'ID','order'            => 'ASC', 'exclude'=>'26' );
    
    // On ne prends pas en compte le club(OD=26) le club de test
        $data_clubs = get_posts( $args );
    

    error_log("************** Début Lot De traitement ******************");
    error_log("Clubs : " .count($data_clubs));
    error_log("Importation Joueurs , club: offset: $start, step: $step");
    write_logImport("Importation Joueurs ; -10 ; ----------Traitement par lot (club: offset: $start, step: $step;) \n");
	
     $write_logImport_TXT ='';
    
    if(count($data_clubs)>0){
   for($k_club=0; $k_club<count($data_clubs); $k_club++){
       
       
        $data_club = $data_clubs[$k_club];
        $idclub = $data_club->ID;
        $idclub_wordpress = $idclub;
        $idclub_old  = get_post_meta( $idclub_wordpress, "old_idClub", true );
        $Nom_club = $data_club->post_title;
            
            
    
    
        
    
        // validation: Nb joueur tables : joueurs/ palmares / club
        $query = "SELECT * FROM joueurs WHERE  idclub = $idclub_old" ;  
        $result_joueurs = $mysqli->query($query); 
        $num_rows_joueurs = $result_joueurs->num_rows;        

    
        // Recherche par rang/palmares croissant
        $query = "SELECT * FROM palmares WHERE  idclub = $idclub_old ORDER BY npalmares" ;
        $result_rangs = $mysqli->query($query); 
        $num_rows_rangs = $result_rangs->num_rows;
        
       
       error_log("Club : $Nom_club ($num_rows_rangs jrs)\t Old ID : $idclub_old \t=>\t new :$idclub_wordpress");  
       $write_logImport_TXT.="Importation Joueurs ;$idclub_wordpress; $Nom_club; -100 ;--------------------Init CLUB  $Nom_club -------------------------\n";
       
        //write_logImport("<br>Id club=$idclub, NB joueur table club = $nbjoueur,NB joueur table palmares = $num_rows_rangs,NB joueur table joueurs = $num_rows_joueurs <br><br>\n");
    
        //Validation NB joueur vs NB palmares vs NB joueur Club est cohérent
        if( ($num_rows_rangs != $num_rows_joueurs) ){
            //write_logImport( "Nb joueur table palmares($num_rows_rangs) DIFF Nb joueur table joueur($num_rows_joueurs)<br>";
            $write_logImport_TXT.="Importation Joueurs ; $idclub ; $Nom_club ; 0 ; Nb joueur table palmares($num_rows_rangs) DIFF Nb joueur table joueur($num_rows_joueurs)\n";
                }
        else{
             // write_logImport("$idclub;$Nom_club;  10; Nb joueur table palmares($num_rows_rangs) EGAL Nb joueur table joueur($num_rows_joueurs)\n");
            }
        
        
        // reinit compteurs par club
        $rang_precedent = 0; // permet de reclasser les joueurs si il y a un probleme
        $delta_rang_prec = 1; // par defaut à 1. si il y a un trou, permet de ne detecer que les nouvelle erreur
        $nb_joueur_comptes = 0;
        $tableau_joueur_affichage = ""; // permet d'afficher un tableau de control du palmares
        

        //foreach ( $result_rangs as $row_rang ){
        for ($row_no_palmares = 0; $row_no_palmares < $num_rows_rangs ; $row_no_palmares++) {
                       $result_rangs->data_seek($row_no_palmares);
                       $row_rang = $result_rangs->fetch_assoc();
                    
                      $id_joueur = $row_rang['idjoueur'];
                      $rang_joueur = $row_rang['npalmares'];
                  
                        // Table
                        //0	idclub
                        //1	idjoueur
                        //2	npalmares
                        //3	meilleurpalmares
                        //4	meilleurjbattu
                        //5	dpt
                    


        
                     // *******************************************
                     // DATA sur le joueur: table joueurs
                     // *******************************************
                    $query = "SELECT * FROM joueurs WHERE idjoueur = $id_joueur";
                    $result_joueur = $mysqli->query($query);
                    //echo $query.'<br>';
                    $num_rows_joueur = $result_joueur->num_rows;
                                // Info sur le joueur (1ere ligne retournee)        
                                $result_joueur->data_seek(0); 
                                $row_joueur = $result_joueur->fetch_assoc();
            
                            
            
                    //LValidation: Le joueur Existe
                    if($num_rows_joueur ==0){
                         $write_logImport_TXT.="Importation Joueurs ;$idclub;$Nom_club;  2a;IDjoueur=$id_joueur n'existe pas dans la table joueurs\n";
                         continue; // on passe au tour suivant
                    }
                    
                    // Valiation l'id dujoueur est unique
                    if($num_rows_joueur >1){
                    	$write_logImport_TXT.="Importation Joueurs ;$idclub;$Nom_club; 2b; IDjoueur=$id_joueur est utilisé plusieurs fois dans la table joueurs; on prend le premier\n";
                    	continue; // on passe au tour suivant
                    }
                    
                    
        // VALIDATION DU NOM ET PRENOM
			$nom       = Importation_gestionEncodageUTF8($row_joueur['nom']);
			$prenom    = Importation_gestionEncodageUTF8($row_joueur['prenom']);
			$row_joueur['nom'] = $nom;
			$row_joueur['prenom'] = $prenom;
			
			if(!Importation_is_valid_users_names($nom) || !Importation_is_valid_users_names($prenom) ){
                    	$write_logImport_TXT.="Importation Joueurs ;$idclub;$Nom_club;  2c ;IDjoueur=$id_joueur n'a pas de noms valides ($nom $prenom)\n";
                         continue; // on passe au tour suivant
                    }
			

                    
                    //Validation: email
                    $email = $row_joueur['email'];
                    $email2 = $row_joueur['email2'];
                
                    if ( !is_email( $email ) ) {
                    	if(!is_email( $email2 )){
                    	$write_logImport_TXT.="Importation Joueurs ;$idclub;$Nom_club;  2b ;IDjoueur=$id_joueur n'a pas d'email valide ($nom-  $prenom- $email)\n";
                    	continue; // on passe au tour suivant
                    	}else{
                    		//email2 est valide mais pas email1
                    		$row_joueur['email'] = $email2;
                    	}
                    }
                    
                          
                                  
                $data_joueur_table = array(
                            0   =>'idjoueur',
                            1	=>'idclub',
                            2	=>'prenom',
                            3	=>'nom',
                            4	=>'dateinscri',
                            5	=>'nbpoints',
                            6	=>'nbmatchs',
                            7	=>'nbfilleuls',
                            8	=>'topnouveau',
                            9	=>'rechjoueur',
                            10	=>'classement',
                            11	=>'exclassement',
                            12	=>'nbdefaites',
                            13	=>'nbvictoires',
                            14	=>'nbpartenaires',
                            15	=>'nbdefilance',
                            16	=>'nbdefiaccepte',
                            17	=>'nbdefirefuse',
                            18	=>'ville',
                            19	=>'cp',
                            20	=>'rue',
                            21	=>'nrue',
                            22	=>'email',
                            23	=>'email2',
                            24	=>'tel',
                            25	=>'tel2',
                            26	=>'datenaissance',
                            27	=>'mdp',
                            28	=>'profession',
                            29	=>'autresport',
                            30	=>'toptournoi',
                            31	=>'topcorrespondant',
                            32	=>'topinfo',
                            33	=>'dateinscriclub',
                            34	=>'categorie',
                            35	=>'dptclub',
                            36	=>'topjoueur',
                            37	=>'nbnuls',
                            38	=>'meilleurcbattu',
                            39	=>'idparrain',
                            40	=>'ballesdepensees',
                            41	=>'ballesencheres',
                            42	=>'telpo',
                            43	=>'hobbies',
                            44	=>'pub',
                            45	=>'ligue',
                            46	=>'autresport2',
                            47	=>'autresport3',
                            48	=>'fonction',
                            49	=>'pointssession1',
                            50	=>'rencontre',
                            51	=>'etablissement',
                            52	=>'metier',
                            53	=>'domaine',
                            54	=>'resfonction',
                            55	=>'resspecialite',
                            56	=>'siteinternet',
                            57	=>'descriptif',
                            58	=>'responsabilite',
                            59	=>'adressesoc');
                                    
                                
    
                             $write_logImport_TXT.="Importation Joueurs ;$idclub;$Nom_club; 900; Init traitemet du user  $prenom $nom\n"; 
                                    
                            // Si le joueur existait déja
                               $args = array('meta_key'     => 'old_tennisdefiID',
                                                'meta_value'   => $row_joueur['idjoueur']);
                                $data_users = get_users( $args ); 
                                 if(count($data_users)>0){
                                    for($k_user=0; $k_user<count($data_users); $k_user++){
                                        $data_user = $data_users[$k_user];
                                        $post_IDjoueur = $data_user->ID; 
                                    }
                                 }
                                 else{
                                     list($post_IDjoueur, $txt) = import_create_user_row($row_joueur,$idclub,$Nom_club,$id_joueur) ;
                                     $write_logImport_TXT.= $txt;
                                 }
     
                                    
                                
                                // Enregistrement du joeur
                                // ---------------------------
                                if($saveJoueur && $post_IDjoueur){
                                    $result_addUserToClub = addUserToClub($post_IDjoueur, $idclub_wordpress);
                                    if($result_addUserToClub['erreur'])
								        $write_logImport_TXT.="Importation Joueurs ;$idclub;$Nom_club; 36;  user($post_IDjoueur, old = ".$row_joueur['idjoueur'].",   $prenom $nom Erreur mise à jour club:  ;".$result_addUserToClub['txt']."\n"; 
								}   //if($saveJoueur && $post_IDjoueur){
                               
            } //boucle palmares
                        
   
    
   }// Fin boucle club
    }// fin if(count($data_clubs))>0 
   
    	write_logImport($write_logImport_TXT);
        //fermeture lien avec Bdd
        disconnectBD_oldTENNISDEFI($mysqli);
       
       error_log("************** FIN Lot De traitement ******************");
        return "done";
        
        
    
 }


// *******************************************                            
// Mise à jour Stat du Joueur User NB partenaire, Nb match etc....
// *******************************************
function IMPORT_update_users($start, $step){

    // Les clubs sont importés et le joueurs Aussi
        global $post;
        $args = array('orderby' => 'ID' , 'fields' => array( 'ID' ));

        $data_users = get_users( $args );
        $data_users = array_slice ($data_users , $start , $step );
    
       // $data_users = array_slice ($data_users , $start , $step );
    
    error_log("************** Début Lot De traitement ******************");
        
    //error_log("Clubs : " .count($data_clubs));
    write_logImport("Mise à jour des  Joueurs ; -10 ; ----------Traitement par lot (offset: $start, step: $step;) \n");
        
    if(count($data_users)>0){
        for($k_user=0; $k_user<count($data_users); $k_user++){
            $data_user = $data_users[$k_user];
            $id_joueur = $data_user->ID;
            
            $id_clubs = get_user_meta($id_joueur, TENNISDEIF_XPROFILE_idclubs, true);
            //write_log($id_clubs);
            for($iter_club=0; $iter_club< count($id_clubs); $iter_club++){
            	$id_club = $id_clubs[$iter_club];
            	update_statistique_Joueur($id_joueur,$id_club);
            }
            
            
            //write_logImport("Mise à jour des  Joueurs ; 1 ; ID = $id_joueur; Nb_partenaires ; $Nb_partenaires ; Nb_victoire ; $Nb_victoire;Nb_defaite ; $Nb_defaite \n");
     
            

            
        } // Fin boucle sur les joueurs
        
    } // fin if il y a des joueurs
    
    // On doit retourner le nombre de user total pour que le Javascript s'arrete au bon moment
        $nb_users_data=count_users();
        $nb_users = $nb_users_data['total_users'];
        return $nb_users;
}



// ========================
/*! \brief  Recuperation des match par clubs . FOnctionne pour les clubs avec quelques Resultats
 * */
// =========================
function IMPORT_user_match_multiclub($start_club, $step_club){
	
	$mysqli = connectBD_oldTENNISDEFI ();
	
	global $post;
	$args = array (
			'posts_per_page' => $step_club,
			'offset' => $start_club,
			'post_type' => 'club',
			'orderby' => 'ID',
			'order' => 'ASC',
			'exclude' => '26' 
	);
	// On ne prends pas en compte le club(OD=26) le club de test
	$data_clubs = get_posts ( $args );
	
	error_log ( "************** Début Lot De traitement ******************" );
	error_log ( "Clubs : " . count ( $data_clubs ) );
	error_log ( "Importation Match  , club[offset: $start_club, step: $step_club]" );
	write_logImport ( "Importation Match Multi Clubs ; -10 ; ----------Traitement par lot (club: offset: $start_club, step: $step_club;) \n" );
	
	$data_match_table = array (
			1,
			'datematch',
			2,
			'idjoueur1',
			3,
			'idjoueur2',
			4,
			'resultat', // 3 =victoire, 1=defaite, 2=nul
			5,
			'j1s1',
			6,
			'topvalid',
			7,
			'handicapj1',
			8,
			'j1pos1',
			9,
			'j1pos2',
			10,
			'j2pos1',
			11,
			'j2pos2',
			12,
			'idclub',
			13,
			'j1s2',
			14,
			'j1s3',
			15,
			'j2s1',
			16,
			'j2s2',
			17,
			'j2s3',
			18,
			'j1t1',
			19,
			'j1t2',
			20,
			'j1t3',
			21,
			'j2t1',
			22,
			'j2t2',
			23,
			'j2t3',
			24,
			'handicapj2',
			25,
			'idmatch' 
	);
	
	if (count ( $data_clubs ) > 0) {
		for($k_club = 0; $k_club < count ( $data_clubs ); $k_club ++) {
			
			$data_club = $data_clubs [$k_club];
			$idclub = $data_club->ID;
			$idclub_wordpress = $idclub;
			$idclub_old = get_post_meta ( $idclub_wordpress, "old_idClub", true );
			
			if ($idclub_old != 17690001) {
				// connexion BDD
				error_log ( "\t\tClub N°:".($start_club+$k_club)." Old_Id = $idclub_old => new ID wordpress = $idclub_wordpress");
					
				
				// Importation des matchs
				// Recuperation du nombre de resultats
				$query = "SELECT * FROM matchs WHERE idclub = $idclub_old";
				$results_matchs = $mysqli->query ( $query );
				$NB_match_all = $results_matchs->num_rows;
				
				for($row_match_no = 0; $row_match_no < $NB_match_all; $row_match_no ++) {
					$results_matchs->data_seek ( $row_match_no );
					$row_match = $results_matchs->fetch_assoc ();
					
					importation_match_unique ( $idclub_wordpress, $row_match );
				} // fin boucle match
			}
		} // Fin boucle club
	} // fin if(count($data_clubs))>0
	  
	// fermeture lien avec Bdd
	disconnectBD_oldTENNISDEFI ( $mysqli );
	
	error_log ( "************** FIN Lot De traitement ******************" );
	return "done";
}


// ========================
/*! \brief  Recuperation des match pour 1 club avec par paquet de 300 matchs . 
 *   FOnctionne pour les clubs avec beaucoup de  Resultats
 * */
// =========================
function IMPORT_user_match_TCL($start_match){
	global $post;
	
	$nb_match_byLap = 300;
	$idclub_old = 17690001;
	
	error_log ( "Importation MAtch TCL , debut match: [$start_match-" . ($nb_match_byLap + $start_match - 1) . "]" );
	
	$args = array (
			'post_type' => 'club',
			'meta_query' => array (
					array (
							'key' => 'old_idClub',
							'value' => '17690001' 
					) 
			) 
	);
	
	$data_clubs = get_posts ( $args );
	if (count ( $data_clubs ) != 1) {
		error_log ( "PAs de club trouvé" );
		write_log ( $data_clubs );
		return "done";
	}
	$data_club = $data_clubs [0];
	$idclub = $data_club->ID;
	$idclub_wordpress = $idclub;
	
	// connexion BDD
	$mysqli = connectBD_oldTENNISDEFI ();
	
	// Importation des matchs
	
	// Recuperation du nombre de resultats
	// $query = "SELECT * FROM matchs WHERE idclub = $idclub_old ";
	$query = "SELECT * FROM matchs WHERE idclub = $idclub_old LIMIT $start_match, $nb_match_byLap";
	$results_matchs = $mysqli->query ( $query );
	$NB_match = $results_matchs->num_rows;
	
	// On doit refaire un tour ?
	$make_a_lap = ($NB_match == $nb_match_byLap); // si pas assez de resultat en base $NB_match < $nb_match_byLap
	
	for($row_match_no = 0; $row_match_no < $NB_match; $row_match_no ++) {
		$results_matchs->data_seek ( $row_match_no );
		$row_match = $results_matchs->fetch_assoc ();
		
		importation_match_unique ( $idclub_wordpress, $row_match );
	} // fin boucle match
	  
	// fermeture lien avec Bdd
	disconnectBD_oldTENNISDEFI ( $mysqli );
	
	error_log ( "************** FIN Lot De traitement ******************" );
	
	if ($make_a_lap) {
		$next_start = $start_match + $nb_match_byLap;
		return $next_start;
	} else
		return "done";
}

// *******************************************                            
// Mise à jour Info User (importation de champ manquant ou update stat)
// *******************************************
function IMPORT_update_clubs_LatLng($step, $max_iter){

    

    // Les clubs sont importés 
        global $post;
    
            $NB_clubs = wp_count_posts( 'club')->publish;

    
            $NB_Club_step = $NB_clubs / ($max_iter);
            $offset = $NB_Club_step * $step;
                
         //$args = array('post_type' => 'club','posts_per_page' => $step, 'offset', $start);
      $args = array( 'posts_per_page' => $NB_Club_step, 'offset'=> $offset, 'post_type' => 'club', 'orderby' => 'ID','order'=> 'ASC');
         $data_clubs = get_posts($args);
    
    
    error_log("************** Début Lot De traitement ******************");
    
    error_log("Clubs ($step/$max_iter) offset =$offset \t NB club: " .count($data_clubs). "/$NB_clubs");
   // error_log("debut  : " .count($data_clubs));
    //write_logImport("Mise à jour des  Joueurs ; -10 ; ----------Traitement par lot (offset: $start, step: $step;) \n");
      $output  = "fin<bR>";
    if(count($data_clubs)>0){
        for($k_club=0; $k_club<count($data_clubs); $k_club++){
            $data_club = $data_clubs[$k_club];
            $id_club = $data_club->ID;
            $nom = get_post_field('post_title', $id_club) ;
            $old_ID =get_post_meta($id_club , "old_idClub", true);
            
            // Recupération Lat et Lng
            $mysqli = connectBD_oldTENNISDEFI();
            $query = "SELECT Lat, Lng FROM clubs WHERE idclub  =   $old_ID";
            //$output .= $query;
         
            
            $results = $mysqli->query($query);
            for ($row_no = 0; $row_no < $results->num_rows ; $row_no++) {
            
                $results->data_seek($row_no);
                $row_club = $results->fetch_assoc();
                
               $Lat = $row_club['Lat'];
                $Lng = $row_club['Lng'];
            
            if($Lat != 0){
                $output .= "=========> $nom (Id = $id_club) => Lat: $Lat, Lng: $Lng<br>";
                update_post_meta($id_club, "latitude", $Lat); 
                update_post_meta($id_club, "longitude", $Lng); 
            }
            else
                $output .= "$nom (Id = $id_club)<br>";
            }

            
        } // Fin boucle sur les clubs
        
    } // fin if il y a des clubs

        return $output;
}



// *******************************************                            
// Mise à jour Info User TEMP : Test xprofile Importation
// *******************************************
function IMPORT_update_usersTEMP($start, $step){

    
    //connexion BDD
        $mysqli = connectBD_oldTENNISDEFI();
    
    
    // Recuperation de tous les users
        global $post;
        $args = array('orderby' => 'ID' , 'fields' => array( 'ID' ));

        $data_users = get_users( $args );
    
        $data_users = array_slice ($data_users , $start , $step );
    
    error_log("************** Début Lot De traitement IMPORT_update_usersTEMP******************");
        
    //error_log("Clubs : " .count($data_clubs));
     error_log("Mise à jour des  Joueurs ; -10 ; ----------Traitement par lot (offset: $start, step: $step;) \n");
    if(count($data_users)>0){
        for($k_user=0; $k_user<count($data_users); $k_user++){
            $data_user = $data_users[$k_user];
            
         $ID_tennisdefi_old   =get_user_meta($data_user->ID , "old_tennisdefiID", true);

        if($ID_tennisdefi_old){ 
                   $query = "SELECT * FROM joueurs WHERE idjoueur = $ID_tennisdefi_old";
                   $result_joueur = $mysqli->query($query);
                    //echo $query.'<br>';
                    $num_rows_joueur = $result_joueur->num_rows;
                    $result_joueur->data_seek(0); 
                     $row_joueur = $result_joueur->fetch_assoc();
            
            $date =  date( 'Y-m-d H:i:s', strtotime($row_joueur['dateinscri'] ) );
            /* error_log("$ID_tennisdefi_old \t\t=>\t\t ".$data_user->ID);
                error_log("date inscrip".$row_joueur['dateinscri']."\t=>$date" );
                error_log($row_joueur['nbdefilance']);
            error_log($row_joueur['rechjoueur']);
            error_log($row_joueur['classement']);
            error_log($row_joueur['exclassement']);
            error_log($row_joueur['rue']);
            error_log($row_joueur['ville']);
             error_log($row_joueur['cp']);
             error_log($row_joueur['tel']);
             error_log($row_joueur['datenaissance']);
             error_log($row_joueur['profession']);
            error_log($row_joueur['metier']);
            */
            
            // Mise à jour du mot de passe
           // wp_set_password( $row_joueur['mdp'], $data_user->ID );
            
            //Mise à jour email
            
            $user_id = wp_update_user( array( 'ID' => $data_user->ID, 'user_email' => $row_joueur['email']) );
            if ( is_wp_error( $user_id ) ) {
            	write_log("Erreur MaJ email :".$data_user->ID."\t email = [".$row_joueur['email']."] ");
            }
            
              //A Dé-commenter
              //====================
              /*
            // DATA WORDPRESS (non modifiable par le user)
            if($row_joueur['dateinscri'] != '0000-00-00'){
                $date =  date( 'Y-m-d H:i:s', strtotime($row_joueur['dateinscri'] ) );
                $b = update_user_meta($data_user->ID, TENNISDEIF_XPROFILE_dateinscri   , $date) ;             
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : dateinscri b=$b\n");

            }
            
            //
             $a = intval($row_joueur['nbdefilance']);
            $b = update_user_meta($data_user->ID, "nbdefilance" , $a); /// \todo ajouter le comptage des défi lancé
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : nbdefilance b=$b (Nb defi = ".intval($row_joueur['nbdefilance'])."\n");
   
            // DATA buddypress
            if($row_joueur['rechjoueur'])
                    $b = xprofile_set_field_data(TENNISDEIF_XPROFILE_rechjoueur   , $data_user->ID, 'oui');
            else
                    $b = xprofile_set_field_data(TENNISDEIF_XPROFILE_rechjoueur   , $data_user->ID, 'non');
        
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : rechjoueur\n");
            $b = xprofile_set_field_data(TENNISDEIF_XPROFILE_classement   , $data_user->ID, $row_joueur['classement']); 
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : classement\n");      
            $b = xprofile_set_field_data(TENNISDEIF_XPROFILE_exclassement , $data_user->ID, $row_joueur['exclassement']);
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : exclassement\n");
        
            //adresse
            $adresse = $row_joueur['nrue'].' '.$row_joueur['rue'];           
           $b = xprofile_set_field_data( TENNISDEIF_XPROFILE_adresse    , $data_user->ID, $adresse);
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : adresse\n");      
           $b = xprofile_set_field_data( TENNISDEIF_XPROFILE_ville      , $data_user->ID, $row_joueur['ville']);
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : ville\n");
           $b = xprofile_set_field_data( TENNISDEIF_XPROFILE_codepostal , $data_user->ID, $row_joueur['cp']);
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : cp\n");
            
            // tel
            if($row_joueur['tel'] != '')
                    $tel = $row_joueur['tel'];
            else
                   $tel = $row_joueur['tel2'];       
            $b = xprofile_set_field_data(  TENNISDEIF_XPROFILE_telephone ,$data_user->ID, $tel);  
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : tel\n");          
           
            //date de naissance
            if($row_joueur['datenaissance'] != '0000-00-00'){
            $date =  date( 'Y-m-d H:i:s', strtotime($row_joueur['datenaissance'] ) );
            
            error_log(" date lu : ".$row_joueur['datenaissance']."\tconvertie en ".strtotime($row_joueur['datenaissance'] )."\tqui donne : $date");
            $b = xprofile_set_field_data(  TENNISDEIF_XPROFILE_datenaissance , $data_user->ID,$date);
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : datenaissance\n");
            }
            //Metier /prof 
            $b = xprofile_set_field_data( TENNISDEIF_XPROFILE_metier, $data_user->ID,$row_joueur['metier']);
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : metier\n");
            
            
                        
            
$Professions  = array(
                            "Chef d'entreprise (PDG,DG,GÈrant)",
                            "Directeur, chef de service",
                            "Ingénieur, cadre, chef de projet",
                            "Profession libérale ",
                            "Enseignant",
                            "Etudiant",
                            "Lycéens",
                            "CollÈgiens",
                            "Artisans",
                            "Commercant",
                            "Technicien, Agent de maitrise, Employé",
                            "Retraité",
                            "Autre ou non renseigné");
            
            $b = xprofile_set_field_data( TENNISDEIF_XPROFILE_emploi, $data_user->ID, $Professions[intval($row_joueur['profession'])-1]);
                if(!$b) write_logImport("userWP_ID = ".$data_user->ID.",erreur maj user_meta , champ : profession\n");
           
 			*/
                
                
        }//fin if user à un $ID_tennisdefi_old
            

        } // Fin boucle sur les joueurs
        
    } // fin if il y a des joueurs
    
    // On doit retourner le nombre de user total pour que le Javascript s'arrete au bon moment
        $nb_users_data=count_users();
        $nb_users = $nb_users_data['total_users'];
        return $nb_users;
}

// *************************************
// TOOLS
// ****************************************


// find_username($nom,$prenom)
// =======================================
// Creation User Name = 1 lettre prenom + nom + Digit si meme
function import_find_username($nom,$prenom,$idclub,$Nom_club, $id_joueur){
        //$username = substr ( $prenom , 0 ,1 ).$nom;
        
        $username = strtolower( $prenom.'.'.$nom);
        if(username_exists( $username )){
                $indice_username = 2;
                while(username_exists( $username )){
                    $username = strtolower($prenom.'.'.$nom.$indice_username);
                    $indice_username ++;
                }
        	write_logImport("Importation Joueurs ;$idclub;$Nom_club; 300; Username already exist :  ".substr ( $prenom , 0 ,1 ).$nom." => $username ; IDjoueur=$id_joueur ($nom $prenom)\n");
        }
        
        return $username;
}


// GEstion des Problème d'encodage UTF8
function Importation_gestionEncodageUTF8($txt){
	//$nom = utf8_encode($nom);
	$txt = ereg_replace('Â©', 'é', $txt);
	$txt = ereg_replace('ã©', 'é', $txt);
	$txt = ereg_replace('Ã¨', 'è', $txt);
	$txt = ereg_replace('Ã¯', 'ï', $txt);
	$txt = ereg_replace('Ã¹', 'ù', $txt);
	$txt = ereg_replace('Ã§', 'ç', $txt);
	$txt = ereg_replace('Ã ', 'à', $txt);
	

	return $txt;
}

// User au TCL?
function Importation_users_isTCL($mysqli, $oldIDUser){
	// Recherche par rang/palmares croissant
	$query = "SELECT * FROM palmares WHERE  idclub = 17690001 AND idjoueur = $oldIDUser" ;
	$result_rangs = $mysqli->query($query);
	$num_rows_rangs = $result_rangs->num_rows;
	error_log($query);
	if($num_rows_rangs > 0)
		return true;
	else 
		return false;
}
// Update TCL Emails
function Importation_users_TCL_updateEMAIL($nom, $prenom, $noms, $prenoms, $emails){
			$key_nom    = array_keys($noms, strtoupper($nom));
			$key_prenom = array_keys($prenoms, ucfirst($prenom));
			//echo "$nom $prenom:<br> ";
			//echo "<pre>"; print_r($key_nom); echo "</pre>";
			//echo "<pre>"; print_r($key_prenom); echo "</pre>";
			
			if(!empty($key_nom) && !empty($key_prenom)){
				//echo "$nom $prenom:<br> ";
				//echo "<pre>"; print_r($key_nom); echo "</pre>";
				//echo "<pre>"; print_r($key_prenom); echo "</pre>";
				
				$array_key = array_intersect_key(   array_flip($key_nom), array_flip($key_prenom)  ) ;
				//var_dump	($array_key);
				if(count($array_key)>0){
					reset($array_key); 
					$first_key = key($array_key);
					return $emails[$first_key];
				}
				
		return false;	
				
			}
}

// Validation du nom ou prenom (Pas de chiffre, au moins 2 caractère valide)
function Importation_is_valid_users_names($nom){
	//source: http://www.phpjabbers.com/php-validation-and-verification-php27.html
	// Full Name must contain letters, dashes and spaces only and must start with letter.
	if(preg_match("/^[a-zA-ZéèÉ][çéèÉiïëîôâa-zA-Z -_]+$/", $nom) === 0)
		return false;
		
		return true;
}



// =======================================
// Creation User Name = 1 lettre prenom + nom + Digit si meme
function import_create_user_row($row_joueur,$idclub,$Nom_club,$id_joueur){
        $nom       = $row_joueur['nom'];
        $prenom    = $row_joueur['prenom'];
    
        $write_logImport_TXT ='';
        $output = array(false,$write_logImport_TXT); // On retourne l'ID du joueuer creer ou false et la phrase de debug/error 
    // Creation du user
    //-------------------
    // Creation User Name = 1 lettre prenom + nom + Digit si meme
      $username = import_find_username($nom,$prenom,$idclub,$Nom_club, $id_joueur);
    
  //$post_IDjoueur = wp_create_user( $username, $row_joueur['mdp'], $row_joueur['email'] );
    $post_IDjoueur = wp_create_user( $username, $row_joueur['mdp'], "shreckb@gmail.com" );
    
    if( is_wp_error( $post_IDjoueur ) ) {
                // Problème à la creation du la creation du joueur à reussi
                $error_string = $post_IDjoueur->get_error_message();
                $write_logImport_TXT.= "Importation Joueurs ;$idclub;$Nom_club; 301; erreur à la creation du joueur ID=$id_joueur $prenom $nom: wp_create_user retrourne: $error_string\n";
        
        return array(false, $write_logImport_TXT);
    } 
    
    // la creation du joueur à reussi


            //Metadata 
         if(!update_user_meta( $post_IDjoueur,  'old_tennisdefiID', $row_joueur['idjoueur']))
            $write_logImport_TXT.="Importation Joueurs ;$idclub; $Nom_club; 304, erreur update_user_meta 'old_tennisdefiID' de  $prenom $nom (newID = $post_IDjoueur) \n";

            //NOm et Prenom
            $update_user_return = wp_update_user( array( 'ID' => $post_IDjoueur, 
                                                'last_name' => $nom, 
                                                 'first_name' => $prenom ) );

            if ( is_wp_error( $update_user_return ) ) {
                    // There was an error, probably that user doesn't exist.
                    $write_logImport_TXT.="Importation Joueurs ;$idclub;$Nom_club; 350; Impossible de mettre le nom et prenom à jour l'utilisateur : $prenom $nom (IDnew=$id_joueur)\n";
                    return array($post_IDjoueur, $write_logImport_TXT);
            }
                                            
                                            
          //Mise à jpur de Buddy press
         // Copier collé du pluginbp_xprofile_wp_user_sync
        // =================================================
            $last_name = $nom;
            $first_name = $prenom;
        // update first_name field
                xprofile_set_field_data( 
                    'First Name', 
                    $post_IDjoueur, 
                    $first_name
                );

                // update last_name field
                xprofile_set_field_data( 
                    'Last Name',
                    $post_IDjoueur,
                    $last_name
                );

                // When XProfiles are updated, BuddyPress sets user nickname and display name 
                // so we should too...

                // construct full name
                $full_name = $first_name.' '.$last_name;

                // set user nickname
                bp_update_user_meta( $post_IDjoueur, 'nickname', $full_name );

                // access db
                global $wpdb;

                // set user display name - see xprofile_sync_wp_profile()
                $wpdb->query( 
                    $wpdb->prepare( 
                        "UPDATE {$wpdb->users} SET display_name = %s WHERE ID = %d", 
                        $full_name, 
                        $post_IDjoueur 
                    )
                );

                // see notes above regarding when BuddyPress updates the "Name" field

                // update BuddyPress "Name" field directly
                xprofile_set_field_data( 
                    bp_xprofile_fullname_field_name(), 
                            $post_IDjoueur,				
                            $full_name 
                );   
            // Fin update BuddyPress 
    
    
    return array($post_IDjoueur, $write_logImport_TXT);
 
}




// find_username($nom,$prenom)
// =======================================
// Importation d'un resultat
function importation_match_unique($idclub_new, $row_match) {
	
	
	//return true;
	// recherche User 1
	$args = array (
			'meta_query' => array (
					array (
							'key' => 'old_tennisdefiID',
							'value' => $row_match ['idjoueur1'],
							'compare' => '=' 
					) 
			),
			'fields' => 'ID' 
	);
	$users1 = get_users ( $args );
	
	// echo "<pre>"; print_r($users1); echo "</pre>";
	// recherche IUser 2
	$args = array (
			'meta_query' => array (
					array (
							'key' => 'old_tennisdefiID',
							'value' => $row_match ['idjoueur2'],
							'compare' => '=' 
					) 
			),
			'fields' => 'ID' 
	);
	$users2 = get_users ( $args );
	
	// *************************
	// Les utilisateurs Existent ?
	// **************************
	if (count ( $users1 ) != 1 || count ( $users2 ) != 1) {
		// file_put_contents ( $file_debug, $row_match ['idmatch'] . "; $old_idclub; 101; Erreur un des 2 joueurs n existe pas dans la nouvelles Bdd.(IDj1 = " . $row_match ['idjoueur1'] . " et IDj2 =" . $row_match ['idjoueur2'] . ")\n", FILE_APPEND );
		// echo "CLub $idclub_new Erreur : count(J1=".$row_match ['idjoueur1'].") = ".count( $user1 ) ." ET count(J2=".$row_match ['idjoueur2'].") = ".count( $user2 )."<br>";
		
	} else {
		// Les joueurs existent
		// **********************
		// echo"<pre>" ; print_r($user_query1); echo "</pre>";
		$idJ1 = $users1 [0];
		$idJ2 = $users2 [0];
		
		// Création du post
		$post = array (
				'post_title' => 'resultat importé',
				'post_status' => 'publish', // Choose: publish, preview, future, etc.
				'post_type' => 'resultats',
				'post_date' => $row_match ['datematch'] 
		);
		$post_ID_match = wp_insert_post ( $post ); // Pass the value of $post to WordPress the insert function
		
		if (is_wp_error ( $post_ID_match )) {
			file_put_contents ( $file_debug, $row_match ['idmatch'] . "; $old_idclub; 102; erreur à la creation du post resultats:   " . $post_ID_match . get_error_message () . '\n', FILE_APPEND );
		} else {
			
			// *************************
			// Deatil du match
			// ************************
			update_post_meta ( $post_ID_match, "tennisdefi_idClub", $idclub_new );
			
			if ($row_match ['resultat'] == 1) { // defaite
				update_post_meta ( $post_ID_match, "tennisdefi_idVainqueur", $idJ2 );
				update_post_meta ( $post_ID_match, "tennisdefi_idPerdant", $idJ1 );
				// inversion du score
				update_post_meta ( $post_ID_match, "j1s1", $row_match ['j1s1'] );
				update_post_meta ( $post_ID_match, "j1s2", $row_match ['j1s2'] );
				update_post_meta ( $post_ID_match, "j1s3", $row_match ['j1s3'] );
				update_post_meta ( $post_ID_match, "j2s1", $row_match ['j2s1'] );
				update_post_meta ( $post_ID_match, "j2s2", $row_match ['j2s2'] );
				update_post_meta ( $post_ID_match, "j2s3", $row_match ['j2s3'] );
				update_post_meta ( $post_ID_match, "match_nul", 0 );
			}
			if ($row_match ['resultat'] == 3) {// Victorie
				update_post_meta ( $post_ID_match, "tennisdefi_idVainqueur", $idJ1 );
				update_post_meta ( $post_ID_match, "tennisdefi_idPerdant", $idJ2 );
				
				update_post_meta ( $post_ID_match, "j2s1", $row_match ['j1s1'] );
				update_post_meta ( $post_ID_match, "j2s2", $row_match ['j1s2'] );
				update_post_meta ( $post_ID_match, "j2s3", $row_match ['j1s3'] );
				update_post_meta ( $post_ID_match, "j1s1", $row_match ['j2s1'] );
				update_post_meta ( $post_ID_match, "j1s2", $row_match ['j2s2'] );
				update_post_meta ( $post_ID_match, "j1s3", $row_match ['j2s3'] );
				update_post_meta ( $post_ID_match, "match_nul", 0 );
			} else { // match nul
				update_post_meta ( $post_ID_match, "tennisdefi_idVainqueur", $idJ1 );
				update_post_meta ( $post_ID_match, "tennisdefi_idPerdant", $idJ2 );
				
				update_post_meta ( $post_ID_match, "j1s1", $row_match ['j1s1'] );
				update_post_meta ( $post_ID_match, "j1s2", $row_match ['j1s2'] );
				update_post_meta ( $post_ID_match, "j1s3", $row_match ['j1s3'] );
				update_post_meta ( $post_ID_match, "j2s1", $row_match ['j2s1'] );
				update_post_meta ( $post_ID_match, "j2s2", $row_match ['j2s2'] );
				update_post_meta ( $post_ID_match, "j2s3", $row_match ['j2s3'] );
				update_post_meta ( $post_ID_match, "match_nul", 1 );
			}
		} // le post est cree
			  
		// echo "ID : $idJ1 vs $idJ2<br>";
	} // les 2 joueurs existe dans la nouvelle Bdd

}

// Mise à jour des statistiques
function update_statistique_Joueur($user_ID,$user_idclub){
	
	$user_info = get_userdata ( $user_ID );

	
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
					) ,
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
	
	//Mise à jour Dans le palmares
	$args = array (
			'meta_query' => array (
					array (
							'key' => TENNISDEIF_XPROFILE_idClub,
							'value' => $user_idclub
					),
					array (
							'key' => TENNISDEFI_XPROFILE_idjoueur,
							'value' => $user_ID
					)
			),
			'post_type' => 'palmares'
	);
	$postPalmares = get_posts ( $args );
	$PostPalmaresID = $postPalmares [0]->ID;
	
	update_post_meta($PostPalmaresID, TENNISDEIF_XPROFILE_nbpartenaires , max(0, count($joueurs)-1) ) ;
	update_post_meta($PostPalmaresID, TENNISDEIF_XPROFILE_nbvictoires, $victoires) ;
	update_post_meta($PostPalmaresID, TENNISDEIF_XPROFILE_nbdefaites, $defaites) ;
	update_post_meta($PostPalmaresID, TENNISDEFI_XPROFILE_nbMacth, ($victoires+$defaites+$nb_matchnuls)) ;
	
	delete_user_meta( $user_id, TENNISDEIF_XPROFILE_nbpartenaires );
	delete_user_meta( $user_id, TENNISDEIF_XPROFILE_nbvictoires );
	delete_user_meta( $user_id, TENNISDEIF_XPROFILE_nbdefaites );
	delete_user_meta( $user_id, TENNISDEFI_XPROFILE_nbMacth );
	
	error_log("ID= $user_ID ; CLub_ID =  => $victoires/$defaites/$nb_matchnuls => ".($victoires+$defaites+$nb_matchnuls));
	
	
}


        