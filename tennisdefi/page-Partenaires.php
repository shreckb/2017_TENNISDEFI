<?php
/*
Template Name: partenaires_Generalités
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
	//titre
	echo '<h1 class="entry-title post-title">'.get_the_title().'</h1>';
?>
<br>
Ajoutez vos partenaires occasionnels comme partenaires habituels et offrez vous une nouvelle approche du tennis.
<br>
<?php 

//$url_recherchePArt = get_page_link( get_IDpage_DefiRecherchePartenaires());
//$url_palmares      = get_page_link( get_IDpage_DefiRecherchePartenaires());

echo "<div>";

$url_pageDeclarerResultat = get_page_link(get_page_DeclarerResultat());
$url_pageRecherPartenaire = get_page_link(get_IDpage_DefiRecherchePartenaires());
$url_pageActivite		  = get_page_activity_url();

echo do_shortcode ('
		<a href="'.$url_pageDeclarerResultat.'" style="text-decorations:none; color:inherit;">
		[one_fourth]
			[box title="" border_width="1" border_style="solid"  icon="check" icon_style="border" icon_shape="circle" align="center"]Déclarez un résultat plus rapidement encore.[/box]
		[/one_fourth]
		</a>
		
		<a href="'.$url_pageRecherPartenaire.'" style="text-decorations:none; color:inherit;">
		[one_fourth]
			[box title="" border_width="1" border_style="solid" icon="search" icon_style="border" icon_shape="circle" align="center"]Lancez une "recherche de joueur" parmi vos partenaires directement.[/box]
		[/one_fourth]
		</a>
		
		<a href="'.$url_pageActivite.'" style="text-decorations:none; color:inherit;">
		[one_fourth]
			[box title="" border_width="1" border_style="solid" icon="bar-chart-o" icon_style="border" icon_shape="circle" align="center"]Suivez vos activités respectives (résultats de match).[/box]
		[/one_fourth]
		</a>
		
		[one_fourth_last]
			[box title="" border_width="1" border_style="solid" icon="envelope-o" icon_style="border" icon_shape="circle" align="center"]Echangez des messages et des commentaires entre vous.[/box]
		[/one_fourth_last]
');
echo "</div>";
?>

	Pour ajouter un joueur comme partenaire, c'est simple, depuis le palmarès cliquez sur l'icone <img  src="<?php echo get_bloginfo('stylesheet_directory') .'/images/icon-joueur-ajouter.png'; ?>" width="20" alt="ajouter comme partenaire">

	<?php 
	//echo do_shortcode ('[divider style="icon-center" icon="angle-double-down" border="medium"]'); ?>
	
	<?php 
	// Liste des user en cours
	
	?>
	

	
		

 
    </div>
<!-- end of #content -->

<?php get_footer(); ?>