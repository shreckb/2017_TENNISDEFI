<?php
/*
Template Name: Importation_resultats
*/


/* 1/ importer le club
    importer tous ses joueurs joueurs
    
    importer les résultats
// 2/ 

*/

$file_debug = "importation_match.csv";

?>
<?php
// Ajouter Jquery pour cette page
//wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('jquery-ui-accordion');
wp_enqueue_script( 'defi_accordion', get_stylesheet_directory_uri().'/js/defi_accordion.js', array('jquery','jquery-ui-accordion'),null,true);  
wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
 ?>  
<?php    get_header(); ?>

<div id="content" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">




<?php  
//***********************************************
//
//************************************************
$saveMatch = 1; 
$nb_page = 20;  
echo "version 2.8<br>";



// SERVEUR
//$user ='root';
//$passwd ='GI1fHA9AFWdF';
//$host ='localhost';
//$bdd ='old_tennisdefi';

// LOCAL
$user ='root';
$passwd ='root';
$host ='localhost';
$bdd ='TennisDefi_20130622';





$mysqli = new mysqli($host, $user, $passwd , $bdd );
if ($mysqli->connect_errno) {
    echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$mysqli->query("SET NAMES 'utf8'");
//echo $mysqli->host_info . "\n";




// 398

// *******************
// Gestion du temps d'execution: Appel de cette page N fois 
// *******************

$permalien = get_permalink();
    if (!isset($_POST["pageNumber"])){
        $page = 0; 
        //$Erreur_synthese = [];
        file_put_contents ( $file_debug  , "Importation du : ".date('Y-m-d H:i:s')."\n" );
        
        // Recuperation du nombre de resultats
        $query = "SELECT * FROM matchs WHERE idclub = 17690001";
                $results_matchs = $mysqli->query($query);
                $NB_match_all = $results_matchs->num_rows;
                
        
                //Suppression des resutats
                /*global $wpdb;
                $querystr = "SELECT ID FROM  $wpdb->posts WHERE post_type = 'resultats' ";

                $results = $wpdb->get_results($querystr, OBJECT);

                echo "results SELECT.... = ".count($results)."<br>";
                //echo "<pre>"; print_r($results); echo "</pre>";

                //$posts_return = get_posts($args); // on prend tous les clubs
                //echo "Résultats à suppriemerr :  ".count($posts_return)."<br>";
                //echo "<pre>"; print_r($posts_return); echo "</pre>";
                foreach($results as $post){
                    //echo "<pre>"; print_r($ID); echo "</pre>";

                    //echo"$ID<br>";
                    //wp_delete_post($post->ID, false);
                    update_post_meta($post->ID, "tennisdefi_idClub",  3339);
                   }
                   */
     
                
    }
    else{
        $page = $_POST["pageNumber"];
        $NB_match_all = $_POST["matchNumber"];
    }
        
        // Info pour la page
        $page_next = $page+1;
        $match_per_page = (int)($NB_match_all/$nb_page)  + 1;
        $start = $page*$match_per_page; 
        $stop  = ($page+1)*$match_per_page -1;

    echo" Page : $page/$nb_page; <br>Nb match par page : $match_per_page<br>start: $start<br>stop:$stop";
    // creation du formulaire pour passer la page
    echo '<form method="post" action="" id="formPage">  
                <input type="hidden" id="pageNumber" name="pageNumber" value="'.$page_next.'" />
                <input type="hidden" id="matchNumber" name="matchNumber" value="'.$NB_match_all.'" />
                NB_match_all
        </form>';

    
    echo "<script type=\"text/javascript\">
         jQuery(document).ready(function($){
               //alert(\"Chargement fini: $page/$nb_page\");
                //if($page_next<1){
                if($page_next<$nb_page){
                    document.forms[\"formPage\"].submit();
                }
                else
                    alert(\"We are DONE :)\");
         });
      </script>";
               
                 
            // *******************************************
            // Récupération Statistiques des match
            // *******************************************
            
            $data_match_table = array(
                1  ,'datematch',
                2	,'idjoueur1',
                3	,'idjoueur2',
                4	,'resultat',// 3 =victoire, 1=defaite, 2=nul
                5	,'j1s1',
                6	,'topvalid',
                7	,'handicapj1',
                8	,'j1pos1',      
                9	,'j1pos2',    
                10	,'j2pos1',  
                11	,'j2pos2',	        
                12	,'idclub',	        
                13	,'j1s2',	       
                14	,'j1s3',	        
                15	,'j2s1',	        
                16	,'j2s2',	        
                17	,'j2s3',	        
                18	,'j1t1',	        
                19	,'j1t2',	        
                20,     'j1t3',	        
                21	,'j2t1',	        
                22	,'j2t2',	        
                23	,'j2t3',	        
                24	,'handicapj2',	
                25	,'idmatch');
                
                
                
                
            // table match 
                $query = "SELECT * FROM matchs WHERE idclub = 17690001 limit  $start,$match_per_page";
                $results_matchs = $mysqli->query($query);
                    $num_rows_matchs = $results_matchs->num_rows;

                echo "<H1>NB resultats $num_rows_matchs <br></H1>";
                echo '<div id="defi_accordion">';

                for ($row_match_no = 0; $row_match_no < $results_matchs->num_rows ; $row_match_no++) {
                                $results_matchs->data_seek($row_match_no);
                                $row_match = $results_matchs->fetch_assoc();
                                
                                
                                $old_idclub = $row_match['idclub'];
                                
                                
                            echo  "<h3> Match N° $row_match_no , ID = ".$row_match['idmatch']."(old club $old_idclub)</h3> <div>";
                            // ****************************************************
                            // recherche de l'idclub dans la nouvelle base WOrdpress
                            // ****************************************************
                                $args = array('post_type' => 'club','meta_key' => 'old_idClub', 'meta_value' => $old_idclub);
                                    $posts_return = get_posts($args); // on prend tous les clubs
                    
                                    if(count($posts_return) !=1)
                                        file_put_contents ( $file_debug  , $row_match['idmatch']."; $old_idclub; 100; Plusieurs club porte ce numéro dans la nouvelle base de donnee\n", FILE_APPEND);
                                   else
                                   {
                                        $idclub_new = $posts_return[0]->ID;
                                    
                                    // recherche IUser 1
                                        $args = array(   'meta_query' => array('relation' => 'AND',
                                                     0 => array('key'     => 'tennisdefi_idClub','value'   => $idclub_new,'compare' => '='),
                                                    1 => array('key'     => 'old_tennisdefiID','value'   => $row_match['idjoueur1'],'compare' => '=')
                                                ), 'fields' => 'ID');
                                        $user1 = new WP_User_Query( $args );
                                     // recherche IUser 2   
                                        $args = array(   'meta_query' => array('relation' => 'AND',
                                                    0 => array('key'     => 'tennisdefi_idClub','value'   => $idclub_new,'compare' => '='),
                                                    1 => array('key'     => 'old_tennisdefiID','value'   => $row_match['idjoueur2'],'compare' => '=')
                                                ), 'fields' => 'ID');
                                        $user2 = new WP_User_Query( $args );
                                        
                                        // *************************
                                        // Les utilisateurs Existent ?
                                        // **************************
                                       if($count($user1) !=1 || $count($user2) !=1){
                                           file_put_contents ( $file_debug  , $row_match['idmatch']."; $old_idclub; 101; Erreur un des 2 joueurs n existe pas dans la  nouvelles Bdd.(IDj1 = ".$row_match['idjoueur1']." et IDj2 =".$row_match['idjoueur2'].")\n", FILE_APPEND );
                                           //echo "Erreur : count(J1) = ".$user_query1->total_users ." count(J2) = ".$user_query2->total_users."<br>";
                                           }
                                           else
                                           {
                                                        //echo"<pre>" ; print_r($user_query1); echo "</pre>";
                                                        $idJ1  = $user_query1->results[0];
                                                        $idJ2  = $user_query2->results[0];
                                                        echo "ID : $idJ1 vs $idJ2<br>";
                                                        
                                                        
                                                        if($saveMatch){
                                                                            $post = array(
                                                                            'post_title'	=> 'resultat importé',
                                                                            'post_status'	=> 'publish',			// Choose: publish, preview, future, etc.
                                                                            'post_type'	=> 'resultats',
                                                                            'post_date'     => $row_match['datematch']
                                                                            );
                                                                            $post_ID_match = wp_insert_post($post);  // Pass  the value of $post to WordPress the insert function
                                                                    
                                                                    if( is_wp_error( $post_ID_match ) ) {
                                                                        file_put_contents ( $file_debug  , $row_match['idmatch']."; $old_idclub; 102; erreur à la creation du post resultats:   ".$post_ID_match.get_error_message() .'\n', FILE_APPEND);
                                                                        }
                                                                        else{
                                                                            
                                                                                    // *************************
                                                                                    // Deatil du match
                                                                                    // ************************
                                                                                   update_post_meta($post_ID_match, "tennisdefi_idClub",  $idclub_new);
                                                                                             
                                                                                    
                                                                                    if($row_match['resultat'] == 1) {// defaite
                                                                                            update_post_meta($post_ID_match, "tennisdefi_idVainqueur",  $idJ2);
                                                                                            update_post_meta($post_ID_match, "tennisdefi_idPerdant",  $idJ1);
                                                                                            // inversion du score
                                                                                            update_post_meta($post_ID_match, "j1s1",  $row_match['j1s1']);
                                                                                            update_post_meta($post_ID_match, "j1s2",  $row_match['j1s2']);
                                                                                            update_post_meta($post_ID_match, "j1s3",  $row_match['j1s3']);
                                                                                            update_post_meta($post_ID_match, "j2s1",  $row_match['j2s1']);
                                                                                            update_post_meta($post_ID_match, "j2s2",  $row_match['j2s2']);
                                                                                            update_post_meta($post_ID_match, "j2s3",  $row_match['j2s3']);
                                                                                            update_post_meta($post_ID_match, "match_nul", 0);
                                                                                     } if($row_match['resultat'] == 3) {
                                                                                            update_post_meta($post_ID_match, "tennisdefi_idVainqueur",  $idJ1);
                                                                                            update_post_meta($post_ID_match, "tennisdefi_idPerdant",  $idJ2);
                                                                                            
                                                                                            update_post_meta($post_ID_match, "j2s1",  $row_match['j1s1']);
                                                                                            update_post_meta($post_ID_match, "j2s2",  $row_match['j1s2']);
                                                                                            update_post_meta($post_ID_match, "j2s3",  $row_match['j1s3']);
                                                                                            update_post_meta($post_ID_match, "j1s1",  $row_match['j2s1']);
                                                                                            update_post_meta($post_ID_match, "j1s2",  $row_match['j2s2']);
                                                                                            update_post_meta($post_ID_match, "j1s3",  $row_match['j2s3']);
                                                                                            update_post_meta($post_ID_match, "match_nul", 0);
                                                                                    } else{ // match nul
                                                                                            update_post_meta($post_ID_match, "tennisdefi_idVainqueur",  $idJ1);
                                                                                            update_post_meta($post_ID_match, "tennisdefi_idPerdant",  $idJ2);
                                                                                            
                                                                                            update_post_meta($post_ID_match, "j1s1",  $row_match['j1s1']);
                                                                                            update_post_meta($post_ID_match, "j1s2",  $row_match['j1s2']);
                                                                                            update_post_meta($post_ID_match, "j1s3",  $row_match['j1s3']);
                                                                                            update_post_meta($post_ID_match, "j2s1",  $row_match['j2s1']);
                                                                                            update_post_meta($post_ID_match, "j2s2",  $row_match['j2s2']);
                                                                                            update_post_meta($post_ID_match, "j2s3",  $row_match['j2s3']);
                                                                                            update_post_meta($post_ID_match, "match_nul", 1);
                                                                                    
                                                                                    }
                                                                            }// le post est cree
                                                                            }// On doit enregistrer le match saveMatch=1
                                    
                                                }// les 2 joueurs existe dans la nouvelle Bdd
         
                                    }//le club est unique et existe
                                    
                                    
                            
                                 /*
                                 
                                */
                                 echo '</div>';
 
               }// fin boucle match
            

    // Deconnexion de la base de donnees
    $mysqli->close();
    


?>
            
   </div>
   
    </div><!-- end of #content -->
               


        
        
        
<?php get_sidebar(); ?>
<?php get_footer(); ?>