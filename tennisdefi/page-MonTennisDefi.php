<?php
/*
Template Name: monTennisDefi_Generalités
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

<?php 

//$url_recherchePArt = get_page_link( get_IDpage_DefiRecherchePartenaires());
//$url_palmares      = get_page_link( get_IDpage_DefiRecherchePartenaires());

echo "<div>";

$url_pageSTAT 		= get_page_link(get_page_selection_userStat());
$url_pageProfile 	= bp_loggedin_user_domain();
$url_pagChoixClub	= get_page_link(get_page_selection_club_id());
$url_pageDeclarerResultat = get_page_link(get_page_DeclarerResultat());

echo do_shortcode ('
		<a href="'.$url_pageSTAT.'" style="text-decorations:none; color:inherit;">
		[one_fourth]
		[box title="" border_width="1" border_style="solid" icon="bar-chart-o" icon_style="border" icon_shape="circle" align="center"]Suivez vos résultats, analysez vos statistiques[/box]
		[/one_fourth]
		</a>
		
		<a href="'.$url_pageProfile.'" style="text-decorations:none; color:inherit;">
		[one_fourth]
			[box title="" border_width="1" border_style="solid" icon="user" icon_style="border" icon_shape="circle" align="center"]Gérez vos informations, changez de mot de passe, etc.[/box]
		[/one_fourth]
		</a>
		
		<a href="'.$url_pagChoixClub.'" style="text-decorations:none; color:inherit;">
		[one_fourth]
			[box title="" border_width="1" border_style="solid"  icon="home" icon_style="border" icon_shape="circle" align="center"]Choisissez, ajoutez, retirez un club[/box]
		[/one_fourth]
		</a>
		
		
		<a href="'.$url_pageDeclarerResultat.'" style="text-decorations:none; color:inherit;">
		[one_fourth_last]
			[box title="" border_width="1" border_style="solid" icon="check" icon_style="border" icon_shape="circle" align="center"]Déclarez vos résultats.[/box]
		[/one_fourth_last]
		</a>
');
echo "</div>";
?>

	<?php 
	//echo do_shortcode ('[divider style="icon-center" icon="angle-double-down" border="medium"]'); ?>
	
	<?php 
	// Liste des user en cours
	
	?>
	

	
		

 
    </div>
<!-- end of #content -->

<?php get_footer(); ?>