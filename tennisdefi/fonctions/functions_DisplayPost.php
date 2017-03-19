<?php

/*! \file
     \brief Contient les fonctions d'affichage des metadata des post dans le backend
    Details http://www.smashingmagazine.com/2011/10/04/create-custom-post-meta-boxes-wordpress/
            http://justintadlock.com/archives/2009/09/10/adding-and-using-custom-user-profile-fields
*/

// ==============================
// GEstion du profile des utilisateurs
// ===============================
/*
add_action( 'show_user_profile', 'tennisdefi_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'tennisdefi_show_extra_profile_fields' );

function tennisdefi_show_extra_profile_fields( $user ) { 
	
		 $array_of_fields = array("dateinscri", "tennisdefi_idClub","old_tennisdefiID");
				

		echo '<h3>Info cachées sur le user</h3>
				<table class="form-table">';
	
		for($k=0;$k<count($array_of_fields); $k++){
					$txt = $array_of_fields[$k];
					$val = esc_attr( get_the_author_meta($txt, $user->ID ) );
					echo '<tr>
									<th><label for="'.$txt.'">'.$txt.'</label></th>
									<td>
									<input type="text" name="'.$txt.'" id="'.$txt.'" value="'.$val.'" class="regular-text" /><br />		
										</td>
								</tr>';
		}
		// Array
		$txt = "tennisdefi_clubs";
		$val = implode(',', get_the_author_meta($txt, $user->ID) );	
	echo '<tr>
			<th><label for="'.$txt.'">'.$txt.'</label></th>
			<td>
				<input type="text" name="'.$txt.'" id="'.$txt.'" value="'.$val.'" class="regular-text" /><br />
			</td>
		</tr>';
	echo '</table>';
}


// Enregistrement
add_action( 'personal_options_update', 'tennisdefi_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'tennisdefi_save_extra_profile_fields' );

function tennisdefi_save_extra_profile_fields( $user_id ) {

	 $array_of_fields = array("dateinscri", "tennisdefi_idClub","old_tennisdefiID");
				
				
	if ( !current_user_can( 'edit_user', $user_id ) && !isset($_POST['tennisdefi_idClub']) && !isset($_POST['tennisdefi_clubs']) )
		return false;

for($k=0;$k<count($array_of_fields); $k++){
					$txt_field = $array_of_fields[$k];
					update_usermeta( $user_id, $txt_field, $_POST[$txt_field] );
		}
		
		// Array
		$txt_field	= "tennisdefi_clubs";
		$data 				= explode(',', $_POST[$txt_field]);
		update_usermeta( $user_id, $txt_field , $data );
}
*/