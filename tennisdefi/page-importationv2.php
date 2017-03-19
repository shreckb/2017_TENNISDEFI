<?php
/*
Template Name: Importation
*/


/* 1/ importer le club
    importer tous ses joueurs joueurs
    
    importer les résultats
// 2/ 

*/

          // Sinon, il faut etre loggué, redirection vers la page de log
          if( ! current_user_can('administrator') ){
              wp_redirect( home_url().'?redirect=1'); 
            exit();
          }
    

$Erreur_synthese = array();

?>
<?php
// Ajouter Jquery pour cette page
    wp_enqueue_script( 'doImportation', get_stylesheet_directory_uri() . '/js/doImportation3.js', array( 'jquery' ) );
    

//wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('jquery-ui-accordion');
wp_enqueue_script( 'defi_accordion', get_stylesheet_directory_uri().'/js/defi_accordion.js', array('jquery','jquery-ui-accordion'),null,true);  
wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
 ?>  
<?php    get_header(); ?>

<div id="content" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">

    
    Bonjour , vous devez etre un admin....
    Importation de 30 par 30 clubs
    
    <textarea id="detail_importation" rows="10" cols="50"></textarea>
   
    </div><!-- end of #content -->
               


        
        
        
<?php get_sidebar(); ?>
<?php get_footer(); ?>