<?php
/*
Template Name: declarerFraudeMAIL
*/
?>

<?php get_header(); ?>

<div id="content-full" class="grid col-940">
	<h1 class="entry-title post-title"><?php echo get_the_title(); ?></h1>
      
      <?php 
      
	  
	  $AFFICHE_DEBUG = false;
      // ========================================
      /* récupération des info générees par la fonction get_mailFraude_Link()  */
      // =========================================
      
		//$tag: permettra de savoir par quelle fonction le mail est e,voyé (defi, recherche partenaire, autre)

      // From USER : celui qui declarera la from donc celui qui a recu l'email
      	$id_user_declarant   = encrypt_decrypt('decrypt', $_GET['FromUSER']);
      // ToUSER : celui qui fraude, donc celui qui envoie le mail
      	$id_user_fraudeur   = encrypt_decrypt('decrypt', $_GET['ToUSER']);
      	 
      // TAG : d'ou vien tle mail (defi/recherche partenaire)
      	$tag   = encrypt_decrypt('decrypt', $_GET['Tag']);
      	 
      // ID :  date d'envoi mail , pour eviter que quelqu'un declare plusieurs fois une fraude
      	$ID_mess =  encrypt_decrypt('decrypt', $_GET['ID']);
      	 
      
      // Celui qui declare la fraude
      	
      //	Celui qui fraude
	  if($AFFICHE_DEBUG){
      	echo "Declarant : $id_user_declarant<br>";
      	echo "fraudeur : $id_user_fraudeur<br>";
      	echo "tag : $tag<br>";
      	echo "DATE : $ID_mess => ".date('Y-m-d', $ID_mess)."<br>";
      	//var_dump($ID_mess);
	  }
		
		
      // MESSAGE
      	$message_succes = '<div class="info-box success">Vous venez de déclarer un abus.<br> Nous vous en remercions</div>';
      	$message_erreur = '<div class="info-box notice"> Action impossible. Le compte du joueur n\'existe peut être plus.</div>';
  		$message_dejafait = '<div class="info-box notice">Vous avez déjà déclaré cet abus.<br> Nous vous en remercions</div>';
      	
      	$newFraude_array = array ('declarant' => $id_user_declarant, 'tag' => $tag, 'date' => $ID_mess);
      	
     // Validation que les joueurs existes
     $Validation= false;
      	if( get_userdata( $id_user_declarant ) && get_userdata($id_user_fraudeur)){
     	 	$Validation = true;
     	 	if($AFFICHE_DEBUG){  echo "les joueurs existent<br>"; }
      	}
      	

    // Valdiation que la date est bonne
     	 	$jour  = date('d', $ID_mess);
			$mois  = date('m', $ID_mess);
     	 	$annee = date('Y', $ID_mess);
     	 	
     	if($Validation && $ID_mess && checkdate($mois, $jour, $annee) ){
     		$Validation = true;
     		if($AFFICHE_DEBUG){ echo "Date OK<br>";}
     	}
     	else{
     		$Validation = false;
     		if($AFFICHE_DEBUG){ echo "Date KO<br>";}
     	}	
     	//Validation Tag 
     	if($Validation && $tag ){
     		$Validation = true;
     		if($AFFICHE_DEBUG){ echo "TAG OK<br>";}
     	}else {
     		$Validation = false;
     		if($AFFICHE_DEBUG){ echo "TAG KO<br>";}
     	}	
      	
     // GEstion de la fraude
     if(!$Validation){
     	// L'un des  ID des joueurs n'exist pas ou la date n'est pas bonne : Abus de la fonction, restons calme
     	if($AFFICHE_DEBUG){ echo $message_erreur;}
     	   }
     else{
      	
      	$user_fraudeList = get_user_meta ( $id_user_fraudeur, TENNISDEIF_XPROFILE_fraudeList, true );
      	if (empty ( $user_fraudeList )) { // Pas encore de fraude
      		echo $message_succes;
      		if($AFFICHE_DEBUG){ echo "PRemiere fraude<br>";}
      		$user_fraudeList = $newFraude_array;
      		update_user_meta ( $id_user_fraudeur, TENNISDEIF_XPROFILE_fraudeList, $user_fraudeList );
      	} else 
      		
   			if (in_array ( $newFraude_array, $user_fraudeList )) {
      			echo $message_dejafait;
			} else {
      			if($AFFICHE_DEBUG){ echo "On ajoute nouvelle fraude<br>";}
				echo $message_succes;
      			array_push ( $user_fraudeList, $newFraude_array );
      			update_user_meta ( $id_user_fraudeur, TENNISDEIF_XPROFILE_fraudeList, $user_fraudeList );
				} // nouvelle fraude
     }// LES USERS existent 

      
      ?>
      
      
      
</div>
<!-- end of #content -->

<?php get_footer(); ?>