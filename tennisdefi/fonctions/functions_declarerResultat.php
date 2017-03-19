<?php

/*! \file
     \brief Contient les fonctions declarer un resultat....
    
    Details.
*/

//========================================
/*! \brief Prise en compte d'un resultat entre joueurs. Fonctio mère
*/
//========================================
function tennis_defi_gestionResultat($id_club, $rangUser, $rangAdversaire, $boolVictoire){
    
    //echo "<br><br>validation: <br>- rang Adversaire : $rangAdversaire <br>- rang user : $rangUser <br> victoire = $boolVictoire <br><br>";
    
      if($rangAdversaire == $rangUser):
            //echo "meme id user"; Géré au niveau du formulaire
        else:
           // traitement
            if($boolVictoire)
            { //  victoire
                  if($rangAdversaire < $rangUser ){
                        tennis_defi_updaterangs($id_club, $rangAdversaire, $rangUser);
                       // echo "<br> ==> Victoire + changement de rang à faire";
                        //echo "<br> fait";
                        }
                   // else{ echo "<br> => victoire mais rang adversaire plus faible";}
            }
            else  // defaite
            {
                if($rangUser < $rangAdversaire ){
                    tennis_defi_updaterangs($id_club, $rangUser, $rangAdversaire);
                    //echo "<br> ==> Defaite  + changement de rang à faire";
                    //echo "<br> fait";
                    }
                //else echo "<br> => Defaite mais rang user plus faible";
             }           
                  
        
            
      endif;// vrai resultat(rang adversaire != rang-user
    
    
    } // if meme ID
    
//========================================
/*! \brief Mise à jour des rangs des joueursaprès déclaration résultat
*/
//========================================
function tennis_defi_updaterangs($id_club, $rangUser_start, $rangUser_stop){
    // perdant mieux classé
    // gagnant en bas dans le tableau
    
      // le user plus faible remonte dans le tableau (doit etre recherché avant de modifier l'ordre) 	
		  $args_stop = array('meta_query' => array(
		  			array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $id_club),
		  			array('key' => TENNISDEIF_XPROFILE_rang,'value' => $rangUser_stop, 'compare' => '=')
		  		),'post_type' => 'palmares');
			$palmares = get_posts($args_stop);
			$id_palmares_rang_stop = $palmares[0]->ID;
		    
    // ajoute au rang des utilisateurs du club +1 entre rang du gagnant et du perdant
    // y compris le numéro  du perdant.
    // le rnag stop n'est pas modifé
    $args = array('posts_per_page' => -1,
    			'meta_query' => array(
    			'relation' => 'AND',
		  			array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $id_club),
		  			array('key' 			=> TENNISDEIF_XPROFILE_rang,
		  							'value' 		=> array( $rangUser_start, $rangUser_stop), 
		  							'type'  		=> 'numeric',
										'compare' => 'BETWEEN'
									)
		  		),
		  		'post_type' => 'palmares');
    
			$palmares = get_posts($args);
			foreach($palmares as $ligne_palmares){
				$previous_rang = get_post_meta($ligne_palmares->ID, TENNISDEIF_XPROFILE_rang,true);
				update_post_meta($ligne_palmares->ID, TENNISDEIF_XPROFILE_rang, $previous_rang+1);
					}
		
        // nouvelle place du gagnant
        update_post_meta($id_palmares_rang_stop, TENNISDEIF_XPROFILE_rang, $rangUser_start);
			
               //echo "<br>Modif2 userID(". $id_user_rang_stop."): ".$rangUser_stop."=>".$rangUser_start."<br>";
                           
}