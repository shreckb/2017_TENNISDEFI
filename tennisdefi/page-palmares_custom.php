<?php
/*
Template Name: palmares_custom
*/


?>
<?php //get_header(); ?>


<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Header Template
 *
 *
 * @file           header.php
 * @package        Responsive
 * @author         Emil Uzelac
 * @copyright      2003 - 2014 CyberChimps
 * @license        license.txt
 * @version        Release: 1.3
 * @filesource     wp-content/themes/responsive/header.php
 * @link           http://codex.wordpress.org/Theme_Development#Document_Head_.28header.php.29
 * @since          available since Release 1.0
 */
?>
	<!doctype html>
	<!--[if !IE]>
	<html class="no-js non-ie" <?php language_attributes(); ?>> <![endif]-->
	<!--[if IE 7 ]>
	<html class="no-js ie7" <?php language_attributes(); ?>> <![endif]-->
	<!--[if IE 8 ]>
	<html class="no-js ie8" <?php language_attributes(); ?>> <![endif]-->
	<!--[if IE 9 ]>
	<html class="no-js ie9" <?php language_attributes(); ?>> <![endif]-->
	<!--[if gt IE 9]><!-->
<html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title><?php wp_title( '&#124;', true, 'right' ); ?></title>

		<link rel="profile" href="http://gmpg.org/xfn/11"/>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>

		<?php wp_head(); ?>
	</head>

<body <?php body_class(); ?>>

<?php responsive_container(); // before container hook ?>
<div id="container" class="hfeed">

<?php responsive_header(); // before header hook ?>
	<div id="header">

		<?php responsive_header_top(); // before header content hook ?>

		

		<?php responsive_in_header(); // header hook ?>

		<?php if( get_header_image() != '' ) : ?>

			<div id="logo">
				<a href="<?php echo home_url( '/' ); ?>"><img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="<?php bloginfo( 'name' ); ?>"/></a>
			</div><!-- end of #logo -->

		<?php endif; // header image was removed ?>

		<?php if( !get_header_image() ) : ?>

			<div id="logo">
				<span class="site-name"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span>
				<span class="site-description"><?php bloginfo( 'description' ); ?></span>
			</div><!-- end of #logo -->

		<?php endif; // header image was removed (again) ?>




		<?php responsive_header_bottom(); // after header content hook ?>

	</div><!-- end of #header -->
<?php responsive_header_end(); // after header container hook ?>

<?php responsive_wrapper(); // before wrapper container hook ?>
	<div id="wrapper" class="clearfix">
<?php responsive_wrapper_top(); // before wrapper content hook ?>
<?php responsive_in_wrapper(); // wrapper hook ?>
        
    <?php //================================ 

      // Verification d'un club
      $current_user = wp_get_current_user();
      $user_idclub =  get_the_author_meta( 'tennisdefi_idClub', $current_user->ID ) ;
      if(empty($user_idclub)):
        tennis_defi_gotoSelectClub();
      else:


        echo '<div class="grid col-940">';
      		// TITRE 
      			echo '<div class="grid col-940">';
		      	echo '<div class="grid col-220">';
		     	 	echo '<img src="'. get_stylesheet_directory_uri().'/images/default-logo-inverse.png" alt="Logo Tennisdefi">';
		     	 echo "</div>";
		     	 	
		     	 echo '<div class="grid col-720 fit" align="center">';
			        echo "<h3>Tournoi Permanent connecté*</h3>";
			        echo "<b>Palmares des joueurs du club </b><br>";
			 		echo "<b>Ayant déclaré une victoire au cours des 3 derniers mois</b><br>";
			 	echo "</div>";
			echo "</div>";
	
 		echo  "<div>";
 		echo "*Vous comptez les points pendant votre heure de réservation?,<br>";
		echo "Il vous suffit de déclarer le résultat sur tennis-défi.com pour participer <br><br>";
		echo  "</div>";
		// FIN TITRE 
		
		
 		//echo "<H2>Tennis Défi, Palmarès  du club : ".get_post($user_idclub)->post_title."</H2>";
        palmares_showNbpartenaire_legende_BW($Palmares_NBPartenaire_Seuils_array);
        
        
        
        
    
    // The Query Palmares    
  		$args = array('meta_query' => array( 
		                  array('key' => TENNISDEIF_XPROFILE_idClub,'value' => $user_idclub)
		  				                    ),
		  				        'post_type' => 'palmares',
                                'numberposts' =>-1,
		  				        'meta_key' => TENNISDEIF_XPROFILE_rang,
            					'orderby' => 'meta_value_num',
            					'order' => 'ASC',);		
		  $postPalmares = get_posts($args); 

 
            // ligne de titre
            echo '<table id="table_palmares">
                    <thead><tr>
                    <th>Rang</th>
                    <th>Nom </th>
                    <th>Prénom</th>
					<th>Classement</th>
					<th>Partenaires</th>
                    </thead>
                    <tbody> ';
                    
                    
            
            // Creation des dates
            $today = getdate();
            $today_3m = getdate(strtotime ("-3 months"));//mois precedent
            //$today_1m = getdate(strtotime ("-1 month"));//mois precedent
            //$today_2m = getdate(strtotime ("-2 months"));//mois precedent
          
            
            
            $rang_virtuel = 0;
            foreach ( $postPalmares as $lignePalmares ) {
            	 
            	
            	$id_joueur = get_post_meta($lignePalmares->ID , TENNISDEFI_XPROFILE_idjoueur , true);
            	
            	
            	$NB_match = count_matchDeclare_at_month_withUser($user_idclub, $id_joueur, $today_3m);
            	if($NB_match>0) :
            			$rang_virtuel ++;
		            	$rang_currentuser       = get_post_meta($lignePalmares->ID , TENNISDEIF_XPROFILE_rang , true);
		            	
		            	$user = get_userdata($id_joueur);
		            	$nbpartenaire_currentuser = get_post_meta ($lignePalmares->ID , TENNISDEIF_XPROFILE_nbpartenaires, true );
		
		            	$user_classement = xprofile_get_field_data( TENNISDEIF_XPROFILE_classement, $id_joueur );
		            	$user_classement_top = xprofile_get_field_data( TENNISDEIF_XPROFILE_exclassement, $id_joueur );
		            	
		            
		            	echo '<tr>';
		            	echo "<td>";
		            		echo "$rang_virtuel";
			            	if(get_Xprofilefield_recherchePartenaires($id_joueur)=='oui')
			            		echo '<a title="Ce joueur recherche des partenaires"> <img src="'.get_bloginfo('stylesheet_directory') .'/images/icon-recherche-partenaires.png" alt="" height="20" width="20"> </a>';
			            	
			            	
		            	echo "</td>";
							
		            	// Nom Prenom
						echo '<td>'.$user->user_lastname.'</td>';
		            	echo '<td>'.$user->user_firstname.'</td>';
		            	
		            	//Classement
		            	if(strcmp($user_classement_top,'') != 0)
		            		echo "<td>$user_classement($user_classement_top)</td>";
		            	else 
		            		echo "<td>$user_classement</td>";
		
		            	
		            	// Nombre de partenaires
		            	echo '<td>';
		            	echo palmares_showNbpartenaire_BW($nbpartenaire_currentuser,$Palmares_NBPartenaire_Seuils_array);
		            	echo '</td>'; 
		            	
		            	echo'</tr>';
            	
            	endif; // fin d'affichage si le joueur à des match de ouis X temps
            
            }
             
             echo'</tbody></table>'; 

       	echo"</div>";
    
		endif; // fin si club vide ?>
                </div><!-- end of #content -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
