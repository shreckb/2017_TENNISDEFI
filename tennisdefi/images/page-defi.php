<?php
/*
Template Name: Defi
*/

?>
<?php
// Ajouter Jquery pour cette page
//wp_enqueue_script('jquery-ui-datepicker');
//wp_enqueue_script('jquery-ui-accordion');
//wp_enqueue_script( 'defi_accordion', get_stylesheet_directory_uri().'/js_accordion/defi_accordion.js', array('jquery','jquery-ui-accordion'),null,true);  
enqueue_style_smoothness ();
wp_enqueue_script( 'date_picker', get_stylesheet_directory_uri().'/js/date_picker.js', array('jquery','jquery-ui-datepicker'),null,true);  
wp_enqueue_script( 'date_picker2', get_stylesheet_directory_uri().'/js/jquery.ui.datepicker-fr.js', array('jquery','jquery-ui-datepicker'),null,true);  

?>  
<?php get_header(); ?>

<div id="content-full" class="grid col-940">
    

<?php


// TITRE + Changement de clun
addTitleAndSelectBox();



    $current_user = wp_get_current_user();
    $user_idclub =  get_the_author_meta( 'tennisdefi_idClub',$current_user->ID) ;
    $user_rang = getCurrentUserRang();
?>

    <!--  =========== Sommaire ===================  --> 
    <div class="grid col-940">
    <?php
    	// echo do_shortcode('[button href="#lancer-defi" size="xlarge" title="Lancer un défi"  color="#448968" hovercolor="#6ba589" textcolor="#f4fcff"][/button]');
		//echo do_shortcode('[button href="#recherche-partenaire" size="xlarge" title="Rechercher un partenaire"  color="#28704d" hovercolor="#6ba589" textcolor="#ffffff"][/button]');
		//echo do_shortcode('[button href="#recherche-replacant" size="xlarge" title="Trouver un remplaçant"  color="#115534" hovercolor="#6ba589" textcolor="#ffffff"][/button]');
 	?>
     </div>  
    

      <div class="grid col-940">

    
    <?php
   	//  =========== Défi - partenaire ===================
     echo do_shortcode('[toggle title="Lancer un défi"][TENNISDEFI_DEFI][/toggle]');
     echo do_shortcode('[toggle title="Rechercher un partenaire"][TENNISDEFI_RECHERCHE][/toggle]');
     echo do_shortcode('[toggle title="Trouver un remplaçant"][TENNISDEFI_RECHERCHE_REMPLACANT][/toggle]');
   ?>
   
   





</div><!-- #content -->







<?php //get_sidebar(); ?>
<?php get_footer(); ?>