jQuery(document).ready(function() {

	var textareaConsole = jQuery('#detail_importation');
	textareaConsole.val();

	// INIT
	// ************************
	var init_ok = true;
	jQuery.ajax({
		timeout: 100000000000000,
		url : ajaxurl,
		data : {
			'action' : 'do_ajax_import',
			'fn' : 'initIMPORT'
		},
		dataType : 'JSON',
		success : function(data) {
			textareaConsole.append('Initialisation....OK\r\n');
			// textareaConsole.append('OK');
			textareaConsole.append('\r\n' + data + '\r\n');

			// Do the job
			// ****************
			doTheJob(textareaConsole);
		},
		error : function(errorThrown) {
			textareaConsole.append('Initialisation....KO\r\n');
			init_ok = false;
		}
	}); // fin jQuery.ajax

}); // fin funtion

// ************************
// do the Job
// ************************
function doTheJob(textareaConsole) {
	var clubs_number;
	jQuery.ajax({
		url : ajaxurl,
		data : {
			'action' : 'do_ajax_import',
			'fn' : 'getClubsNumber'
		},
		dataType : 'JSON',
		success : function(data) {
			textareaConsole.append('Comptage du nombre de club....OK\r\n');
			clubs_number = data;
			textareaConsole.append('\r\nNb club ' + clubs_number + '\r\n');

			// Creation des clubs 
			
			//if (confirm("Importer les clubs? Etape 1"))
				//create_clubs(textareaConsole, 0, clubs_number);

			// Msie à jour Lat/lng des clubs
			//if (confirm("mise à jour Lat Lng des clubs?Etape 2"))
				//Update_clubs(textareaConsole, 0);

			// Creation joueurs(club deja Deja Fait)
			//if (confirm("importation des joueurs ?Etape 3"))
			//  create_joueurs(textareaConsole, 0, clubs_number,0);

			// Importer les matchs: TCL
			//if (confirm("importation des Match TCL ?Etape 4"))
				//create_match_TCL(textareaConsole, 0);
			
			// Importer les matchs: NON TCL
			//if (confirm("importation des Match NON TCL ? etape 5"))
			  //create_match(textareaConsole, 0, clubs_number, 0);

			// Mise à jour ds joueurs (les resultats doivent etre importes
			// =>stats...)
			//alert("hello18x2");
			//if (confirm("mise à jour des stat ?"))
			  // update_joueurs(textareaConsole, 0,0);

			//Mise à jour joueurs(xprofile)....
			if (confirm("mise à jour des info joueurs ?"))
			  update_joueurs_temp(textareaConsole, 0,0);

		},
		error : function(errorThrown) {
			textareaConsole.append('Comptage du nombre de club....Ko\r\n');
			textareaConsole.append('KO');
		}
	}); // fin jQuery.ajax
}

// ************************
// Create clubs
// ************************
function create_clubs(textareaConsole, index_lap, nb_club) {
	var step = 100;
	var start = index_lap * step;
	var max_lap = nb_club / step + 1;

	// Creations des clubs
	// ************************

	// Part Recupération des clubs
	textareaConsole.append('\r\nCréation des clubs....');
	textareaConsole.scrollTop(textareaConsole[0].scrollHeight
			- textareaConsole.height());
	// textareaConsole.append('\r\n Récupération clubs ID....');

	jQuery.ajax({
		timeout: 100000000000000,
		url : ajaxurl,
		data : {
			'action' : 'do_ajax_import',
			'fn' : 'get_clubs',
			'start' : start,
			'step' : step
		},
		dataType : 'JSON',
		success : function(data) {

			var prc = index_lap / max_lap * 100
			// textareaConsole.append('OK');
			textareaConsole.append(prc.toFixed(3) + '%');

			// textareaConsole.append('\r\n\t' + data);
			// textareaConsole.append('\r\nCréation des joueurs....');
			if (index_lap + 1 < max_lap)
				create_clubs(textareaConsole, index_lap + 1, nb_club);
		},
		error : function(errorThrown) {
			textareaConsole.append('KO');
		}
	}); // fin jQuery.ajax

}

// ******************************************
// create_users par club, de facon réursive
// ******************************************
function create_joueurs(textareaConsole, index_lap, nb_club, start) {
	var step = 50;

	if (index_lap < 6)
		step = 10;

	// var start = index_lap*step;
	var max_lap = nb_club / step + 1;

	// Creations des joueurs
	// ************************
	textareaConsole.append('\r\nImportation Joueurs....');
	textareaConsole.scrollTop(textareaConsole[0].scrollHeight
			- textareaConsole.height());

	jQuery.ajax({
		timeout: 100000000000000,
		url : ajaxurl,
		data : {
			'action' : 'do_ajax_import',
			'fn' : 'get_users',
			'start' : start,
			'step' : step
		},
		dataType : 'JSON',
		success : function(data) {
			var prc = (index_lap + 1) / max_lap * 100
			textareaConsole.append(prc.toFixed(3) + '%\r\n');
			if (index_lap + 1 < max_lap)
				create_joueurs(textareaConsole, index_lap + 1, nb_club, start
						+ step);
		},
		error : function(errorThrown) {
			alert(errorThrown.name);
			alert(errorThrown.message);
			alert(errorThrown.stack);
			textareaConsole.append('KO');
		}
	}); // fin jQuery.ajax

}

// ******************************************
// Update_users, de facon réursive
// ******************************************
function update_joueurs(textareaConsole, index_lap, nb_joueur) {
	var step = 1000;
	var start = index_lap * step;
	var max_lap;
	if (nb_joueur != 0)
		max_lap = nb_joueur / step + 1;
	else
		max_lap = 3; // au premier tour dans le php, on calculera le nombre
						// de joueurs

	// Update des joueurs
	// ************************
	textareaConsole.append('\r\Update Joueurs....');
	textareaConsole.scrollTop(textareaConsole[0].scrollHeight
			- textareaConsole.height());

	jQuery.ajax({
		timeout: 100000000000000,
		url : ajaxurl,
		data : {
			'action' : 'do_ajax_import',
			'fn' : 'update_users',
			'start' : start,
			'step' : step
		},
		dataType : 'JSON',
		success : function(data) {
			nb_joueur = data;
			// alert("Nb joueur recu = "+ data);
			var prc = index_lap / max_lap * 100
			textareaConsole.append(prc.toFixed(3) + '%\r\n');
			if (index_lap + 1 < max_lap)
				update_joueurs(textareaConsole, index_lap + 1, nb_joueur);
		},
		error : function(errorThrown) {
			textareaConsole.append('KO');
		}
	}); // fin jQuery.ajax

}

// ******************************************
// Update_club, de facon réursive
// ******************************************
function Update_clubs(textareaConsole, index_lap) {

	var max_lap = 50;

	// Update des club
	// ************************
	textareaConsole.append('\r\Update Clubs  ' + index_lap + '....');
	textareaConsole.scrollTop(textareaConsole[0].scrollHeight
			- textareaConsole.height());

	jQuery.ajax({
		timeout: 100000000000000,
		url : ajaxurl,
		data : {
			'action' : 'do_ajax_import',
			'fn' : 'update_clubs_LatLng',
			'step' : index_lap,
			'max_step' : max_lap
		},
		dataType : 'JSON',
		success : function(data) {
			nb_joueur = data;
			if (index_lap + 1 < max_lap)
				Update_clubs(textareaConsole, index_lap + 1);
		},
		error : function(errorThrown) {
			textareaConsole.append('KO');
		}
	}); // fin jQuery.ajax

}

// ******************************************
// Update_users TEMP, de facon réursive : importation valeurs
// ******************************************
function update_joueurs_temp(textareaConsole, index_lap, nb_joueur) {
	var step = 1000;
	var start = index_lap * step;
	var max_lap;
	if (nb_joueur != 0)
		max_lap = nb_joueur / step + 1;
	else
		max_lap = 3; // au premier tour dans le php, on calculera le nombre
						// de joueurs
	// max_lap = 1;

	// Update des joueurs
	// ************************
	textareaConsole.append('\r\Update Joueurs....');
	textareaConsole.scrollTop(textareaConsole[0].scrollHeight
			- textareaConsole.height());

	jQuery.ajax({
		timeout: 100000000000000,
		url : ajaxurl,
		data : {
			'action' : 'do_ajax_import',
			'fn' : 'update_usersTEMP',
			'start' : start,
			'step' : step
		},
		dataType : 'JSON',
		success : function(data) {
			nb_joueur = data;
			// alert("Nb joueur recu = "+ data);
			var prc = index_lap / max_lap * 100
			textareaConsole.append(prc.toFixed(3) + '%\r\n');
			if (index_lap + 1 < max_lap)
				update_joueurs_temp(textareaConsole, index_lap + 1, nb_joueur);
		},
		error : function(errorThrown) {
			textareaConsole.append('KO');
		}
	}); // fin jQuery.ajax

}

// ******************************************
// create_match par club, de facon réursive
// ******************************************
function create_match(textareaConsole, index_lap, nb_club, start_club) {
	var step = 100;

	
	if (index_lap < 1)
		step = 1;

	// var start = index_lap*step;
	var max_lap = nb_club / step + 1;

	// Creations des joueurs
	// ************************
	textareaConsole.append('\r\nImportation des Matchs....');
	textareaConsole.scrollTop(textareaConsole[0].scrollHeight
			- textareaConsole.height());

		jQuery.ajax({
			timeout: 100000000000000,
			url : ajaxurl,
			data : {
				'action' : 'do_ajax_import',
				'fn' : 'IMPORT_user_match_multiclub',
				'start' : start_club,
				'step' : step
			},
			dataType : 'JSON',
			success : function(data) {
				var prc = (index_lap + 1) / max_lap * 100;
				textareaConsole.append('Done CLub['+start_club+'- ?] '+ prc.toFixed(3) + '%\r\n');
				
				textareaConsole.append(prc.toFixed(3) + '%\r\n');
				if (index_lap + 1 < max_lap)
					create_match(textareaConsole, index_lap + 1, nb_club, start_club
							+ step);
				else
					textareaConsole.append('IMportation FINISH');
				
			},
			error : function(errorThrown) {
				//alert(errorThrown.name);
				//alert(errorThrown.message);
				//alert(errorThrown.stack);
				textareaConsole.append('KO');
			}
		}); // fin jQuery.ajax


}



function create_match_TCL(textareaConsole,start_match) {


	// Creations des joueurs
	// ************************
	textareaConsole.append('\r\nImportation des Matchs TCL....');
	textareaConsole.scrollTop(textareaConsole[0].scrollHeight
			- textareaConsole.height());

		//alert('Impotztino Club TCL debut match = '+start_match);
		jQuery.ajax({
			timeout: 100000000000000,
			url : ajaxurl,
			data : {
				'action' : 'do_ajax_import',
				'fn' : 'IMPORT_user_matchTCL',

				'start_match' : start_match

			},
			dataType : 'JSON',
			success : function(data) {
				// L'importation du club est partielle
				//alert('LE traitement TCL a retourné : ' + data);
				
				if(data != 'done'){
					//alert('On Reste sur le club');
					
					textareaConsole.append('Done : importation match ['+start_match+'-' + (data-1)+' ]\r\n');
					create_match_TCL(textareaConsole, data);
				}
				else{
					textareaConsole.append('IMportation TCL FINISH\r\n');
					}
			},
			error : function(errorThrown) {
				//alert(errorThrown.name);
				//alert(errorThrown.message);
				//alert(errorThrown.stack);
				textareaConsole.append('KO');
			}
		}); // fin jQuery.ajax 


}

/*
 * function create_joueurs(textareaConsole, index_lap, nb_club){
 * 
 * var step = 100; var start = index_lap*step; var max_lap = nb_club/step +1;
 * 
 * var step = 120;
 * 
 * var bError = false; var initText = textareaConsole.val(); var start =
 * index_lap*step; var stop = Math.min(dataClubs.length, start+step-1); // step
 *  // alert("lap "+index_lap +"/"+last_index+"Clubs["+start+" - " +stop +"]");
 * var dataClubs_sliced = dataClubs.slice(start, stop+1);
 * //alert("dataClubs.length:" + dataClubs.length +" / dataClubs_sliced.length : " +
 * dataClubs_sliced.length); var prc = index_lap/ (dataClubs.length/step +1) *
 * 100; textareaConsole.val(txt_init + prc.toFixed(3) + '%');
 * 
 * jQuery.ajax({ url: ajaxurl, data:{ 'action':'do_ajax_import',
 * 'fn':'get_users', 'dataclub' : JSON.stringify(dataClubs_sliced), //
 * JSON.stringify(dataClubs[index_club]), }, dataType: 'JSON',
 * success:function(data){ if(index_lap+1<last_index)
 * create_users(textareaConsole, txt_init, dataClubs, index_lap+1, last_index ); },
 * error: function(errorThrown){ bError = true; } }); // fin jQuery.ajax
 * 
 * 
 * //Affichage //if(bError) //textareaConsole.val(initText + 'KO'); //else //
 * textareaConsole.val(initText + 'OK');
 * 
 * 
 *  }
 * 
 */

