<?php
/*
Template Name: Display_clubs_v3
*/

?>
<?php 


      //  wp_localize_script( 'function', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
       // wp_localize_script( 'ajax-example', 'AjaxExample', array(
	//	'ajaxurl' => admin_url( 'admin-ajax.php' ),
	//	'nonce' => wp_create_nonce( 'ajax-example-nonce' )
	//) );

    // datatable
    enqueue_script_Lib_DataTable();
    
    //google map
     wp_register_script('googlemaps_progressbar', get_stylesheet_directory_uri() . '/js/progressBar.js', array( 'jquery' ) );
     wp_enqueue_script('googlemaps_progressbar');
    
    
    wp_register_script('googlemaps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAO0iN_HwmHLEYdtoMK7LocbcCurHARvI0&libraries=places', false, '3');
    wp_enqueue_script('googlemaps');
    
     wp_register_script('googlemaps_markermanage', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js', false, '3');
    wp_enqueue_script('googlemaps_markermanage');
    
    
     // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
    wp_enqueue_script( 'doClubRequest', get_stylesheet_directory_uri() . '/js/doClubsRequest.js', array( 'jquery' ) );
    
    wp_register_style( 'csspourlapageClub', get_stylesheet_directory_uri() . 'doClubsDisplay.css');
    
    get_header();?>

                
<div id="content-full" class="grid col-940">
<h1 class="entry-title post-title"><?php echo get_the_title(); ?></h1>
	  
	  Sur la carte, seuls les clubs avec au moins un joueur s'affichent. Pour une recherche exhaustive utilisez le champ de recherche sous la carte. 
	  <div id="locationField">
      <input id="autocomplete" placeholder="Entrez un lieu"
             onFocus="geolocate()" type="text"></input>
      </div>
 		<div class="info-box notice" id="erreur_affichage_mapClubs" style="display: none"></div>
   
        <div class="grid col-940" id="js-map-container" style="height: 500px;">Google Map<br>...</div>
       
       
        <div><label>Rechercher exhaustive d'un club : </label>
        <input id="recherche_club" type="text" value="entrez au moins 3 caractères"></div> 
        <table id="table_clubs" class="stripe"></table>         
        <div>
        
        <?php  $url_creer_club = get_permalink( get_IDpage_Contact());  ?>
       <h3>Vous ne trouvez pas votre club dans la liste ?</h3>
Créez un nouveau club <a href="<?php echo $url_creer_club;?>">ici</a>.<br />
L'administrateur du site validera vos données, nous vous informerons par email de la disponibilité de votre nouveau club.
</div>
       
  </div><!-- end of #content -->


        
<?php //get_sidebar(); ?>
<?php get_footer(); ?>