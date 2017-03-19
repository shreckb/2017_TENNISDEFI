<?php
/*
Template Name: connectez-vous
*/
?>

<?php get_header(); ?>

<?php 
	$url_inscription =  esc_url( get_permalink( get_page_by_title( 'S\'inscrire' ) ) );
	$url_connexion   =  wp_login_url();
?>

<div id="content-full" class="grid col-940">


Pour accéder à ces pages, vous devez <a href="<?php echo $url_connexion;?>">vous connecter</a><br />
Vous n'êtes pas encore inscrit ? <a href="<?php echo $url_inscription;?>">Inscrivez-vous à tennis-défi ! </a>

<?php 
echo do_shortcode ('[divider style="icon-center" icon="angle-double-down" border="medium"]');

echo do_shortcode ('<div style="text-align: center; margin-top: 50px;">
		[button href="'.$url_inscription.'" size="large" color="#28704d" hovercolor="#115534" textcolor="#ffffff" icon="pencil"]Inscription[/button]
		[button href="'.$url_connexion.'" size="large" color="#175579" hovercolor="#032e49" textcolor="#ffffff" icon="heart-o"]Connexion[/button]</div>');
		
?>
 
    </div>
<!-- end of #content -->

<?php get_footer(); ?>