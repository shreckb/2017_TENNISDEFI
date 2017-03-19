<?php

/*! \file
     \brief Contient les fonctions de gestions des mails avec Mandrill
    Details.
*/



// ========================================
/* ! \brief Ajoute un lien dans les emails pour détecter des envoie frauduleux de mails  */
// =========================================
function get_mailFraude_Link($id_club, $id_user_sender, $id_user_receiver,$tag_email="non défini") {
	//$tag: permettra de savoir par quelle fonction le mail est e,voyé (defi, recherche partenaire,recherche remplaçant,  autre)
	$url_base = get_page_link( get_page_DeclareMailFraude());
	$id_user_sender_crypted   = encrypt_decrypt('encrypt', $id_user_sender);
	$id_user_receiver_crypted = encrypt_decrypt('encrypt',  $id_user_receiver);
	$tag_encrypt = encrypt_decrypt('encrypt',  $tag_email);
	$date_encrypt =  encrypt_decrypt('encrypt',  time());
	//var_dump(time());
	// From USER : celui qui declarera la fraude donc celui qui a recu l'email
	// ToUSER : celui qui fraude, donc celui qui envoie le mail 
	// TAG : d'ou vien tle mail (defi/recherche partenaire)
	// ID :  date d'envoi mail , pour eviter que quelqu'un declare plusieurs fois une fraude 
	
	$url  = $url_base."?FromUSER=$id_user_receiver_crypted&ToUSER=$id_user_sender_crypted&ID=$date_encrypt&Tag=$tag_encrypt";
	
	$txt = "Déclarez ce mail <a href=\"$url\">comme frauduleux en cliquant ici</a>";
	
	
	return $txt;
	
}



// Remarque: Le template est directement setté dans l'interface admin de wordpress (regalge mandrill) !

/*
//ajout du template
function filterMail($message)
{
    $message['template']['name'] = 'template-mailmandrill';
    write_log("============filterMail(message)=============");
    write_log($message);
    return $message;
}

add_filter('mandrill_payload', 'filterMail');
*/
/*
function SendTemplate() {
	// Create a template called MyCoolTemplate and use this code:
	$template_code = '
	Hello *|FNAME|*,

	<p>Your personal coupon code is: *|COUPON|*</p>

	<p>Event Date: *|DATE|*</p>
	<p>Address: *|ADDRESS|*</p>  

	<div mc:edit="body"></div>
	<div mc:edit="sidebar"></div>
	';

	// Sending an email using a template and merge vars
	$to = 'gbezot@gmail.com';
	$globalmv = array(
					array('name' => 'date', 'content' => 'Tomorrow morning!'),
					array('name' => 'address', 'content' => 'Our office')
				);
	$mv	= array(
				array(
					'rcpt' => 'gbezot@gmail.com',
					'vars' => array(
									array('name' => 'fname', 'content' => 'Number One'),
									array('name' => 'coupon', 'content' => '123456'),
								)
				),
				array(
					'rcpt' => 'gbezot@gmail.com',
					'vars' => array(
									array('name' => 'fname', 'content' => 'Number Two'),
									array('name' => 'coupon', 'content' => '654321'),
								)
				),
			);
	$message = array(
		'subject' => 'Email Subject',
		'from_name' => 'Freedie',
		'from_email' => 'your@email.com',
		'to' => $to,
		'merge' => true,
		'global_merge_vars' => $globalmv,
		'merge_vars' => $mv,
		'html' => array(
						array('name' => 'body', 'content' => 'This is the body!'),
						array('name' => 'sidebar', 'content' => 'This is the sidebar!'),
					)
	);
	wpMandrill::sendEmail(
		$message,
		$tags = array('Tag 1', 'Tag 2'),
		$template_name = 'MyCoolTemplate'
	);
}*/