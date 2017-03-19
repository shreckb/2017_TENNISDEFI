<?php

/*! \file
     \brief Contient les fonctions Gérants l'affichage des User en Base (MetaBox....)
    
    Details.
*/
//========================================
/*! \brief Creer le formulaire pour afficher sous ladmin le profile de l'utilisateur
*/
//=========================================
function tennisdefi_show_extra_profile_fields( $user ) { ?>
                <h3>Autres informations</h3>
                <table class="form-table">
                <tbody>
                <tr>
                <th><label for="">Téléphone</label></th>
                <td>
                                                <?php
                                                        $tel=esc_attr( get_the_author_meta( 'tennisdefi_telephone', $user->ID ));
                                                ?>
                <input id="telephone" class="regular-text" name="tennisdefi_telephone" type="text" value="<?php echo $tel; ?>"></td>
                </tr>
                <tr>
                <th><label for="fax">Fax</label></th>
                <td>
                                                <?php
                                                        $fax=esc_attr( get_the_author_meta( 'tennisdefi_fax', $user->ID ) );
                                                ?>
                <input id="fax" class="regular-text" name="tennisdefi_fax" type="text" value="<?php echo $fax; ?>"></td>
                </tr>
                <tr>
                <th><label for="adresse">Adresse</label></th>
                <td>
                                                <?php
                                                        $adresse=esc_attr( get_the_author_meta( 'tennisdefi_adresse', $user->ID ) );
                                                ?>
                                                <textarea id="adresse" name="tennisdefi_adresse"><?php echo $adresse; ?></textarea></td>
                </tr>

                <tr>
                <th><label for="club">ID du club</label></th>
                <td>
                                                <?php
                                                        $id_club=esc_attr( get_the_author_meta( 'tennisdefi_idClub', $user->ID ) );
                                                ?>
                                                <input id="idClub" class="regular-text" name="tennisdefi_idClub" type="number" value="<?php echo $id_club; ?>">(Ne pas modifier)</td>
                </tr>
                <tr>
                <th><label for="clubrang">Rang dans le club</label></th>
                <td>
                                                <?php
                                                        $user_rang=esc_attr( get_the_author_meta( 'tennisdefi_rang', $user->ID ) );
                                                ?>
                                                <input id="rang" class="regular-text" name="tennisdefi_rang" type="number" value="<?php echo $user_rang; ?>">(Ne pas modifier)</td>
                </tr>

                </tbody>
                </table>
<?php }



/// todo: verifierl'utilité'
function tennisdefi_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	//une ligne par info supplémentaire
	update_user_meta( $user_id, 'tennisdefi_telephone', $_POST['tennisdefi_telephone'] );
	update_user_meta( $user_id, 'tennisdefi_fax', $_POST['tennisdefi_fax'] );
	update_user_meta( $user_id, 'tennisdefi_adresse', $_POST['tennisdefi_adresse'] );
        update_user_meta( $user_id, 'tennisdefi_idClub', $_POST['tennisdefi_idClub'] );
        update_user_meta( $user_id, 'tennisdefi_rang', $_POST['tennisdefi_rang'] );
}


//add_action( 'show_user_profile', 'tennisdefi_show_extra_profile_fields' );
//add_action( 'edit_user_profile', 'tennisdefi_show_extra_profile_fields' );
//add_action( 'personal_options_update', 'tennisdefi_save_extra_profile_fields' );
//add_action( 'edit_user_profile_update', 'tennisdefi_save_extra_profile_fields' );