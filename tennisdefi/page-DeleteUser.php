<?php
/*
Template Name: SupprimerUser
*/
?>
<?php get_header(); ?>

<div id="content" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">
<h1 class="entry-title post-title"><?php echo get_the_title(); ?></h1>

                   

<?php 
$current_user = wp_get_current_user();
$id_club=esc_attr( get_the_author_meta( 'tennisdefi_idClub', $current_user->ID ) );


if (isset($_POST['submit'])) :
// ------------------------------
// PARTIE Ttraitement
//-------------------------------
        
   if( deleteUser((int)$_POST['delete_IDuser'], $id_club) ){
            echo "<h3> Le joueur a bien été supprimé <h3>";
    }
    else
    { echo "<h3>Erreur : impossible de supprimer ce joueur</h3>";}

else :
    // PARTIE FORMULAIRE
    //-----------------------------
    if(empty($id_club)):
        tennis_defi_gotoselectclub();

    else:
        
?>


    
    <H3>Sélectionnez le joueur à supprimer : 
    
    <form method="post" action="<?php echo get_permalink(); ?>">
        <?php 
            // ajout formilaire
            echo combobox_joueurs('delete_IDuser', $id_club);
            ?>
	<p><input type="submit" name="submit" value="supprimer" /></p>
    </form>
     </H3>
     
        
<?php endif;endif; // si pas de formulaire recu et si pas de club attaché.  ?>

	</div><!-- #content -->

    

        
<?php get_sidebar(); ?>
<?php get_footer(); ?>