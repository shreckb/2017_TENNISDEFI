<?php 

/*! \file
     \brief Contient les fonctions utilisaant AJAX: Passerelle entre Page callback et fonction
    
    Details.
*/


// 
//==============================================
// La recherche de latitude long doit etre mis en pause


add_action('wp_ajax_nopriv_do_ajax', 'notre_fonction_ajax');
add_action('wp_ajax_do_ajax', 'notre_fonction_ajax');

//========================================
/*! \brief Passerelle AJAX , page <->PAsserelle<-> fonctions(get_all_clubs,get_clubs_search,get_clubs_map_part)
*/
//========================================
function notre_fonction_ajax(){
   /* error_log( "======================notre_fonction_ajax");
    error_log( "fn=".$_REQUEST['fn']);
    error_log( "rech=".$_REQUEST['recherche']);
    
    error_log( "======================notre_fonction_ajax- fin");
 */
   // ce switch lancera les fonctions selon la valeur qu'aura notre variable 'fn'
     switch($_REQUEST['fn']){

          case 'get_latest_posts':
               $output = ajax_get_latest_posts($_REQUEST['count'], $_REQUEST['txtsearch']);
                break;
          case 'get_all_clubs':
             $output = ajax_get_allClub();
              break;
         case 'get_clubs_search':
             $output = ajax_get_clubs_titre($_REQUEST['recherche']);
              break;
         case 'get_clubs_map_part':
         $output = ajax_get_clubs_part($_REQUEST['segment'], $_REQUEST['nb_total']);
         break;
         
          default:
              $output = 'No function specified, check your jQuery.ajax() call';
            break;
 
     }
     
     
   // Maintenant nous allons transformer notre résultat en JSON et l'afficher
     $output=json_encode($output);
     
     if(is_array($output)){
        print_r($output);
     }
     else{
        echo $output;
     }
     die;

 
}

//========================================
/*! \brief From AJAX : Obtention de tous les club en N fois
\todo : Ajouter un champ nonce
*/
//========================================
function ajax_get_clubs_part($segment, $segment_total){
    
    
    $NBclub = wp_count_posts('club')->publish;
    
    $nb = (int)($NBclub/$segment_total);  // on va en prendre plus car tous les club n'ont pas d'adresse lat/long...
    $offset = $nb*($segment -1);
    
    //$nb = 1;

    //Tous les clubs
    $args = array( 'posts_per_page' => $nb, 'offset'=> $offset, 
                  'post_type' => 'club', 
                  'meta_key'  => 'tennisdefi_nbJoueurs',
                  'orderby'   => 'meta_value_num','order'=> 'DESC',     
                 );
                  
       // Clubs avec au moins 1 adhérents
  $args = array( 'posts_per_page' => $nb, 'offset'=> $offset, 
                  'post_type' => 'club', 
                  'meta_key'  => 'tennisdefi_nbJoueurs',
                  'orderby'   => 'meta_value_num','order'=> 'DESC',
                  'meta_query' => array(
                                array(
                                    'key' => 'tennisdefi_nbJoueurs',
                                    'value' => 0,
                                    'compare' => '>' 
                                ))
                 );
    
    
    $posts_return = get_posts($args); // on prend tous les clubs
    
    //error_log( "================ajax_get_clubs_part==================");
    //error_log("it($segment/$segment_total)\tNb club retourne = $NBclub =>offset = $offset et nbpost à prendre : $nb");
     
 $data_club = array( 
           'tennisdefi_nbJoueurs',
           'adresse',
           'adresse2',
           'cp',
           'ville',
            'dpt',
           'latitude',
           'longitude'
            );
     
    
    
     $posts = array();
     foreach($posts_return as $post)
     {
         
        // $post_info = array();
         $post_info['post_title'] =   $post->post_title;
         $post_info['ID'] =   $post->ID;
         for($k=0; $k<count($data_club);$k++)
            $post_info[$data_club[$k]] = get_post_meta( $post->ID   , $data_club[$k], true );
 
        // error_log( "Nom: ". $post_info['post_title'] ."\t\tLat : ". $post_info['latitude']."\tLng : ". $post_info['longitude']);
         $posts[] = $post_info;
    }
    

    return $posts;

}

//========================================
/*! \brief From AJAX : Obtention des clubs par recherche sur le titre.(et département...)
*/
//========================================
function ajax_get_clubs_titre($terme){
    
    $args = array(
        'numberposts' =>30,
        'post_type' => 'club',
        'meta_key' => 'tennisdefi_nbJoueurs',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        's'=> $terme
    );
    
    
    
    $posts_return = get_posts($args); // on prend tous les clubs

   // echo "Nb club retourne = " .count($posts_return); die();
   // echo " nb lub  : ".count($posts_return); die();
    
          $data_club = array( 
           'tennisdefi_nbJoueurs',
           'adresse',
           'adresse2',
           'cp',
           'ville',
            'dpt',
           'latitude',
           'longitude'
            );
     
     
     
     $posts = array();
     foreach($posts_return as $post)
     {
         
        // $post_info = array();
         $post_info['post_title'] =   $post->post_title;
         $post_info['ID'] =   $post->ID;
         //ajout des info plus précises
         for($k=0; $k<count($data_club);$k++)
            $post_info[$data_club[$k]] = get_post_meta( $post->ID   , $data_club[$k], true );
 
         $posts[] = $post_info;
    }
    
    return $posts;
}

//========================================
/*! \brief From AJAX : Obtention des 10000 premiers post  clubs , utilisé?
*/
//========================================
function ajax_get_allClub(){
    $posts_return = get_posts('numberposts=100&post_type=club'); // on prend tous les clubs
/*      $data_club = array( 
           'tennisdefi_nbJoueurs',
           'sigle',
           'dpt',
           'idclub',
           'ligue',
           'correspondant',
           'dateinscri',
           'datetournoi',
           'datetournoifin',
           'president',
           'tresorier',
           'directeur',
           'enseignant',
           'secretaire',
           'topactif',
           'adresse',
           'adresse2',
           'cp',
           'ville',
           'tel',
           'siteweb',
           'reseau',
           'latitude',
           'longitude'
            );
            */
    
          $data_club = array( 
           'tennisdefi_nbJoueurs',
           'adresse',
           'adresse2',
           'cp',
           'ville',
           'latitude',
           'longitude'
            );
     
     //echo "Nb club retourne = " .count($posts_return);
     
     $posts = array();
     foreach($posts_return as $post)
     {
         
        // $post_info = array();
         $post_info['post_title'] =   $post->post_title;
         for($k=0; $k<count($data_club);$k++)
            $post_info[$data_club[$k]] = get_post_meta( $post->ID   , $data_club[$k], true );
            
            
        // recupération Lat Long.
        // peut etre mis en pause pour ganer du temps
         /*
        if( empty($post_info['latitude']))
        {
        
            $address = $post_info['adresse'] . " " . $post_info['adresse2']. " " . $post_info['cp']." " .$post_info['ville']; // Google HQ
            //$address = $post_info['ville']; // Google HQ
            $prepAddr = str_replace(' ','+',$address);
            //echo "<br>adresse_search : $address => $prepAddr <br>";

            
            $geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
            $output= json_decode($geocode);
            //echo " chaine recu:".$geocode."<br>";
            
            if($output->status == "OK"){
                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;
                
                //echo "long : $longitude <br>Lat : $latitude <br>";    
                $post_info['latitude']  = $latitude;
                $post_info['longitude'] = $longitude;
                
                update_post_meta( $post->ID   , 'latitude',  $latitude );
                update_post_meta( $post->ID   , 'longitude',  $longitude );
                }
                else{
                    //echo "pas de chaine retournee<br>";    
                }
            
               // echo "club : ".$post_info['post_title']."  : lat : ".$post_info['latitude']." <br>";
            
        }// mise à jour Long/lat
        
        */
            
         $posts[] = $post_info;
    }
    
    return $posts;
    }

function ajax_get_latest_posts($count,$txtsearch){

   
        //Output
/*
            $output = array(
                    "sEcho" => 10,
                    "iTotalRecords" => 10,
                    "iTotalDisplayRecords" => 10,
                    "aaData" => array()
            );
            
            $aColumns = array( 'engine', 'browser', 'platform', 'version', 'grade' );
            $aRow = array('engine' => "engine:".$txtsearch, 'browser' => 'browser1', 'platform' => 'platform1', 'version' => 'version1',  'grade' => 'grade1' );
            
            //while ( $aRow = mysql_fetch_array( $rResult ) )
            for($k=0; $k<20;$k++)
            {
                    $row = array();
                    for ( $i=0 ; $i<count($aColumns) ; $i++ )
                    {
                            if ( $aColumns[$i] != ' ' )
                            {
                                    $row[] = $aRow[ $aColumns[$i] ];
                            }
                    }
                    $output['aaData'][] = $row;
            }

    return $output;

*/
 $data_club = array( 
           'tennisdefi_nbJoueurs',
           'sigle',
           'dpt',
           'idclub',
           'ligue',
           'correspondant',
           'dateinscri',
           'datetournoi',
           'datetournoifin',
           'president',
           'tresorier',
           'directeur',
           'enseignant',
           'secretaire',
           'topactif',
           'adresse',
           'adresse2',
           'cp',
           'ville',
           'tel',
           'siteweb',
           'reseau'
            );



    if(strlen($txtsearch)==0){
		$args = array (
				'post_type' => 'club',
				'numberposts' => $count,
				'orderby' => 'meta_value_num',
				'meta_key' => TENNISDEIF_XPROFILE_nbJoueursClub 
		);
         $posts_return = get_posts($args);  
         }
         else{
                 
                 /*
                 // On doit uitliser 2 recherche pour rechercher dans les meta value et le ltitre
                $args = array(
                            'post_type' => 'club',
                            'numberposts'=>999999,
                            's'=> $txtsearch);
                        $posts_return1 = get_posts($args);    
                            
                        $args = array(
                            'post_type' => 'club',
                            'numberposts'=>999999,
                            'meta_query' => array(
                                    'relation' => 'OR',
                                    array(
                                            'key' => 'adresse',
                                            'value' => $txtsearch,
                                            'compare' => 'LIKE'
                                    ),
                                    array(
                                            'key' => 'cp',
                                            'value' => $txtsearch,
                                            'compare' => 'LIKE'
                                    ),
                                    array(
                                            'key' => 'ville',
                                            'value' => $txtsearch,
                                            'compare' => 'LIKE'
                                    ),
                            )
                        );

                       $posts_return2 = get_posts($args);
                        //$posts_return = get_posts('numberposts='.$count.'&post_type=club&s='.$txtsearch);
                         
                         $merged = array_merge( $posts_return1, $posts_return2 );

                            $post_ids = array();
                            foreach( $merged as $item ) {
                            $post_ids[] = $item->ID;
                            }

                        $unique = array_unique($post_ids);

                    $posts_return = get_posts(array(
                                    'post_type' => 'club',
                                    'post__in' => $unique,
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1
                                ));
                    */
                    
                     $args = array(
                            'post_type' => 'club',
                     		'meta_key' => TENNISDEIF_XPROFILE_nbJoueursClub,
                     		'orderby' => 'meta_value_num',
                            'numberposts'=>$count,
                            's'=> $txtsearch,
                     		
                     		
                     );
                        $posts_return = get_posts($args);    
						

              }           
               
     $output = array(
     'sColumns' => 'Club, Nombre de joueurs Tennis-Defi, adresse',
     'sEcho' => 1, 'iTotalRecords' => $count, 'iTotalDisplayRecords' =>$count, 
     'aaData' => array() ); 
    
     
     $posts = array();
     foreach($posts_return as $post)
     {
         
         $row = array();
         $row[] = $post->post_title;
         $row[] = get_post_meta( $post->ID   , 'tennisdefi_nbJoueurs', true );
         if(get_post_meta( $post->ID   , 'adresse', true ) =='' && get_post_meta( $post->ID   , 'adresse2', true ) =='')
            $row[] = get_post_meta( $post->ID   , 'cp', true ).' '.get_post_meta( $post->ID   , 'ville', true );
         else
            $row[] = get_post_meta( $post->ID   , 'adresse', true ).' '.get_post_meta( $post->ID   , 'adresse2', true ).', '.get_post_meta( $post->ID   , 'cp', true ).' '.get_post_meta( $post->ID   , 'ville', true );
         $output['aaData'][] = $row;
    }
    
 return $output;
}