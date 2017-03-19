<?php
/*
Template Name: acceuil
*/

//wp_enqueue_script ( 'jquery' );
// Lib pour afficher des cercles
//wp_enqueue_script ( 'dataTables', 'http://cdn.crunchify.com/wp-content/uploads/code/knob.js');


wp_enqueue_script( 'jquery-ui-accordion' );
enqueue_style_smoothness ();

?>

<?php get_header(); ?>

<div id="content-full" class="grid col-940">

<?php 

$url_inscription =  esc_url( get_permalink( get_page_by_title( 'S\'inscrire' ) ) );
$url_connexion   =  wp_login_url();


echo do_shortcode ('
[layerslider id="1"]
<div style="text-align: center; margin-top: 50px;">
		[button href="'.$url_inscription.'" size="large" color="#28704d" hovercolor="#115534" textcolor="#ffffff" icon="pencil"]Inscription[/button]
		[button href="'.$url_connexion.'" size="large" color="#175579" hovercolor="#032e49" textcolor="#ffffff" icon="heart-o"]Connexion[/button]</div>
[divider style="icon-center" icon="angle-double-down" border="medium"]

[animation effect="fade-ltr" delay="100"][one_third]
<h4>Tennis-plaisir avant tout</h4>
[dropcap size="300%" color="#be441b" shape="circle"][icon icon="heart-o"][/dropcap] Tennis-défi est un tournoi permanent qui vous permet de trouver des partenaires à votre niveau, partout et à tout moment.[/one_third][/animation]

[animation effect="fade-ltr" delay="200"][one_third]
<h4>Trouvez des partenaires</h4>
[dropcap size="300%" color="#be441b" shape="circle"][icon icon="male"][/dropcap]Vous avez du temps pour jouer mais pas de partenaire ? Votre partenaire vient de se désister au dernier moment ? Vous êtes nouveau dans un club ? En quelques clics, trouvez avec qui jouer ![/one_third][/animation]

[animation effect="fade-ltr" delay="300"][one_third_last]
<h4>Totalement gratuit</h4>
[dropcap size="300%" color="#be441b" shape="circle"][icon icon="eur"][/dropcap]Dans tennis-défi, tout est gratuit. Seuls votre nom, email et club vous seront demandés pour vous aider à trouver des partenaires.[/one_third_last][/animation]

[animation effect="fade-ltr" delay="400"][one_third]
<h4>Classement convivialité</h4>
[dropcap size="300%" color="#be441b" shape="circle"][icon icon="thumbs-up"][/dropcap]Chez tennis-défi, nous pensons qu\'il n\'y a pas que la performance qui compte. Nous avons créé un classement de convivialité pour récompenser les joueurs qui ont le plus de partenaires et qui acceptent le plus de défis.[/one_third][/animation]

[one_third][animation effect="fade-ltr" delay="500"]
<h4>Classement performance</h4>
[dropcap size="300%" color="#be441b" shape="circle"][icon icon="trophy"][/dropcap]Lorsque vous gagnez contre un adversaire, vous prenez sa place dans le palmarès du club. Un moyen pratique pour évaluer votre niveau, même si vous ne participez pas aux tournois officiels.[/animation][/one_third]

[animation effect="fade-ltr" delay="600"][one_third_last]
<h4>Simple et pratique</h4>
[dropcap size="300%" color="#be441b" shape="circle"][icon icon="leaf"][/dropcap]Avec tennis-défi, pas de prise de tête ! Le site est simple et fonctionne aussi sur tablettes et smartphones.[/one_third_last][/animation]

[divider style="icon-center" icon="angle-double-down" border="medium"]

[one_third][animation effect="flip-y" delay="200"] 
<img class="size-full" style="border: 1px solid #999999;" src="'.get_bloginfo('stylesheet_directory') .'/images/1.inscription.jpg" alt="tennis-défi : inscrivez-vous" width="auto" height="auto" />
<h4><br />
1) Inscrivez-vous</h4>
Entrez votre nom, prénom, email et choisissez votre club. C\'est tout ![/animation][/one_third]
 
[one_third][animation effect="flip-y" delay="400"] 
<img class="alignnone wp-image-2690 size-full" style="border: 1px solid #999999;" src="'.get_bloginfo('stylesheet_directory') .'/images/2.defi.jpg" alt="tennis-défi : défiez et jouez" width="auto" height="auto" />
<h4>2) Défiez et jouez !</h4>
Rien de plus simple ! Défiez le joueur de votre choix, il recevra un email lui indiquant que vous souhaitez jouer avec lui.[/animation][/one_third]

[one_third_last][animation effect="flip-y" delay="600"] 
<img class="alignnone wp-image-2691 size-full" style="border: 1px solid #999999;" src="'.get_bloginfo('stylesheet_directory') .'/images/3.statistiques.jpg" alt="tennis-défi : Visualisez vos progrès" width="auto" height="auto" />
<h4>3) Visualisez vos progrès</h4>
Entrez le résultat du match et regardez votre évolution dans le palmarès du club. De nombreuses statistiques vous attendent !
[/animation][/one_third_last]
');


?>
	

		
<?php 			
// Afaire à la creation du site, puis coller les defines de debug.log

// echo "==================== Creation XPROFILE====================<br>"; 
            //add_custom_xprofile_Tennisdefi_fields();
//echo "==================== fin Creation XPROFILE ====================<br>";

?>

<?php 



// ===========================TEST DE  GREG=========
// =================================================
// Ne pas supprimer

	//global $current_user;
	//$current_club = get_user_meta ( $current_user->ID, TENNISDEIF_XPROFILE_idClub, true );

	
	
		// Fin Test pour Admin dans son club
		// ---------------------------------
		
		
		// TEST des problèmes UTF8
		// =========================
		/*
		$args = array(
				'fields'       => array('ID'),
		);
		$users = get_users($args);
		
		//echo '<pre>'; print_r($users); echo '</pre>';
	
		foreach($users as $user){
			$user = get_userdata($user->ID);
		
			$str_f = "ã©";
			$str_rep = "é";
			if( strpos($user->user_firstname, $str_f) ){
				
				echo $user->user_lastname.' '.$user->user_firstname ;
				echo ' =====> ';
				echo $user->user_lastname;
				echo ' ';
				echo str_replace($str_f,$str_rep, $user->user_firstname);
				echo'<br>';
			}
		}
	
		foreach($users as $user){
			$user = get_userdata($user->ID);
		
			$str_f = "ã©";
			$str_rep = "é";
			if(strpos($user->user_lastname, $str_f) ){
		
				echo $user->user_lastname.' '.$user->user_firstname ;
				echo ' =====> ';
				echo str_replace($str_f,$str_rep, $user->user_lastname);
				echo ' ';
				echo  $user->user_firstname;
				echo'<br>';
			}
		}	
		*/
			
		
		
		
		
		// ============================================
		

		
		
		// Test Page fraude (dans les envois de mail
		// ====================	
	/*
		global $current_user;
		$current_club = get_user_meta ( $current_user->ID, TENNISDEIF_XPROFILE_idClub, true );
		$fraddeur_id = 4;
		$url_fraude = get_mailFraude_Link($current_club, $current_user->ID, $fraddeur_id);
		echo " <a href=\"$url_fraude\"> fraude!!  (à implémenter dans les mails quand HTML :)</a><br>";
	*/
		?>
		

 
    </div>
<!-- end of #content -->

<?php get_footer(); ?>