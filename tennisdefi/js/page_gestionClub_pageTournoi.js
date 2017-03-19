

//init le rendu des tables
jQuery.extend(jQuery.fn.dataTable.defaults, {
	"iDisplayLength": 4,
	"scrollY":        "200px",
	"scrollCollapse": true,
	//"stripeClasses": [ 'table_palamares_odd', 'table_palamares_even' ],
	"responsive": true,
	//"jQueryUI":       true,
	"sDom" : '<"top"i>rt<"bottom"flp><"clear">', //'Rlfrtip',
	"bPaginate" : true,
	"bLengthChange" : true,
	"bFilter" : true,
	"bSort" : true,
	"bInfo" : true,
	"bAutoWidth" : false,
	"oLanguage" : {
		"sProcessing" : "Traitement en cours...",
		"sSearch" : "Rechercher&nbsp; un joueur:",
		"sLengthMenu" : "Afficher _MENU_ joueurs par page",
		"sInfo" : "Affichage des classements _START_ &agrave; _END_ sur _TOTAL_ joueurs",
		"sInfoEmpty" : "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
		"sInfoFiltered" : "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
		"sInfoPostFix" : "",
		"sLoadingRecords" : "Chargement en cours...",
		"sZeroRecords" : "Aucun &eacute;l&eacute;ment &agrave; afficher",
		"sEmptyTable" : "Aucune donnée disponible dans le tableau",
		"oPaginate" : {
			"sFirst" : " Premier",
			"sPrevious" : " Pr&eacute;c&eacute;dent  ",
			"sNext" : " Suivant ",
			"sLast" : " Dernier"
		},
		"oAria" : {
			"sSortAscending" : ": activer pour trier la colonne par ordre croissant",
			"sSortDescending" : ": activer pour trier la colonne par ordre décroissant"
		}
	}, //fin langage
	// Mise à jour dynamique
	"fnDrawCallback" : function(oSettings) {
		//Gestion des pages suivantes.... 
		if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
			jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
		}else
			jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').show();
	}//fin fnDrawCallback


} );





//chargement en cours / etc...
//**************************************
function display_loading(html_id,OnOff){
	

	if(OnOff){ 
		jQuery('<div class="loading_overlay"><p class="loading_overlay_text" style="text-align:center">Chargement</p> </div>').css({
			    position: "absolute",
			    width: "100%",
			    height: "100%",
			    top: 0,
			    left: 0,
			    background: "#ccc",
			    opacity: 0.9,
			}).appendTo(jQuery(html_id).css("position", "relative"));
		 
		jQuery(html_id+".loading_overlay_text")
	    .css({
		    position: "absolute",
		    top: "50%",
		    left: "50%",
	    });
	 
	}
	else{
		jQuery(html_id+' .loading_overlay').remove();
		//jQuery("#loading_overlay").remove();
	}
	/*table_datatable.destroy();
	//alert(table_datatable_HTML_ID);
	table_datatable = jQuery(html_id).dataTable({"oLanguage" : {"sZeroRecords" : "hello",}});
	//var selector =   table_datatable_HTML_ID+' .dataTables_empty'; 
	//jQuery(selector).html('hello');
	
	//table_datatable.row.add('<td colspan="5"> toto </td>');
	*/
}





//Chargement d'un tournoi
// ***************************************
function display_one_tournoi(id_tournoi, follow_the_link){
	// si follow the link == false alors on ne déplace pas la page vers le tournoi

	
	var id_area = "#div_DiplayTournoi_area"; // permet de masquer/afficher la zone
	var id_area_loading = '#pour_chargement_display_tournoi';
		var id_titre_tournoi = '#titre_tournoi';
	
	//Affichage du chargement 
	display_loading(id_area_loading, true);	
	
		//Affichage de la zone si besoin
		if(!jQuery(id_area).is(":visible")){
			jQuery(id_area).fadeIn();
			//alert('La section était masquée');
		}
		
		
	
	//Recupération du tournoi par AJAx
	jQuery.ajax({
		url : ajaxurl ,
		data : {
			'security':jquery_page_gestionClub_pageTournoi_nonce,
			'action' : 'tennisdefi_gestion_tournoi',
			'fonction' : 'display_tournoi',
			'tournoi_selected' :id_tournoi
		},
		dataType : 'JSON',
		success : function(data) {
			
			// chargement du titre
			jQuery(id_titre_tournoi).html("Détail du tournoi : "+data["titre"]);

			//Mise à jour table
			table_tournoi.fnClearTable();
			if(data["aaData"].length)
				table_tournoi.fnAddData(data["aaData"]);
			// Allons vers le div avec les info
			if(follow_the_link!=false){
				var aTag = jQuery("a[name='detail_du_tournoi']");
				jQuery('html,body').animate({scrollTop: aTag.offset().top},'slow');
			 }
		}, // fin success

		error : function(errorThrown) {
			// alerte qu'une erreur est arrivee
			//jQuery("#alert_tournoi").show();
			//jQuery("#alert_tournoi").html("Impossible de charger les données du tournoi");
		}
	}); // fin jQuery.ajax
	
	
}//fin display_one_tournoi




//chargement de tous les tournois
//**************************************
function display_all_tournois(){
	
	//jQuery("#tennisdefi_tournois_alerte").html('Rafraichissement des données en cours...');
	//jQuery("#tennisdefi_tournois_alerte").fadeIn();

	var loading_div_id = '#pour_chargement_display';
	
	// Afficchage loading
		display_loading(loading_div_id, true);
	
	
	//Recupération du tournoi par AJAx
	jQuery.ajax({
		url : ajaxurl ,
		data : {
			'security':jquery_page_gestionClub_pageTournoi_nonce,
			'action' : 'tennisdefi_gestion_tournoi',
			'fonction' : 'display_Alltournois',
			'club_user':  club_user
		},
		dataType : 'JSON',
		success : function(data) {

				jQuery("#div_tournoi_selector").html(data['Selector']);

				table_tournois.fnClearTable();
				if(data['Data'].length){ 
					table_tournois.fnAddData(data['Data']);
					//table_tournois.fnAdjustColumnSizing();
					}
			
		}, //fin ajax.success
		error : function(errorThrown) {
			// alerte qu'une erreur est arrivee
			//jQuery('#tennisdefi_tournois_alerte').html('<div class="info-box notice"> Une erreur s\'est produite pendant l\'actualisation</div>');
			//jQuery("#tennisdefi_tournois_alerte").html('Une erreur s\'est produite pendant l\'actualisation');
			// Afficchage loading
			display_loading(loading_div_id, false);
		}
	}); // fin jQuery.ajax
	
	
} // fin fct display_all_tournois


function  update_tournoi_information(){
	//modifier la description d'un tournoi tournois 
	var ajaxurl_ = ajaxurl + '?action=tennisdefi_gestion_tournoi'
							+'&fonction=update_tournoi_resume'
							+'&field=description'
							+'&security='+jquery_page_gestionClub_pageTournoi_nonce;
	
	jQuery('.div_DiplayTournoiSummary_description').editable(ajaxurl_,{
		type      : 'textarea',
		indicator : 'Enregistrement...',
	    //tooltip   : 'Cliquer pour éditer...',
	    cancel    : 'Annuler',
	    submit    : 'modifier',
	    callback : function(value, settings) {
	    	jQuery(this).html(value.slice(1,-1));}//gestion de quote en trop
	});		
	
	
	//modifier la visibilite d'un tournoi tournois 
	var ajaxurl_ = ajaxurl + '?action=tennisdefi_gestion_tournoi'
							+'&fonction=update_tournoi_resume'
							+'&field=visibilite'
							+'&security='+jquery_page_gestionClub_pageTournoi_nonce;
	
	jQuery('.div_DiplayTournoiSummary_visibilite').editable(ajaxurl_,{
		//type      : 'textarea',
		type   : 'select',
		data   : " {'1':'visible','0':'non visible'}",
	    indicator : 'Enregistrement...',
	    cancel    : 'Annuler',
	    submit    : 'OK',
	    callback : function(value, settings) {jQuery(this).html(value.slice(1,-1));}//gestion de quote en trop
	});		
	
	
	//modifier la actif/inactif d'un tournoi tournois 
	var ajaxurl_ = ajaxurl + '?action=tennisdefi_gestion_tournoi'
							+'&fonction=update_tournoi_resume'
							+'&field=actif'
							+'&security='+jquery_page_gestionClub_pageTournoi_nonce;
	
	jQuery('.div_DiplayTournoiSummary_actif').editable(ajaxurl_,{
		//type      : 'textarea',
		type   : 'select',
		data   : " {'1':'actif','0':'inactif'}",indicator : 'Enregistrement...',
	    cancel    : 'Annuler',
	    submit    : 'OK',
	    callback : function(value, settings) {jQuery(this).html(value.slice(1,-1));}//gestion de quote en trop
	    });		
	
	//modifier la open d'un tournoi tournois 
	var ajaxurl_ = ajaxurl + '?action=tennisdefi_gestion_tournoi'
							+'&fonction=update_tournoi_resume'
							+'&field=open'
							+'&security='+jquery_page_gestionClub_pageTournoi_nonce;
	
	jQuery('.div_DiplayTournoiSummary_open').editable(ajaxurl_,{
		//type      : 'textarea',
		type   : 'select',
		data   : " {'1':'inscription libre','0':'inscription restreinte'}",indicator : 'Enregistrement...',
	    cancel    : 'Annuler',
	    submit    : 'OK',
	    callback : function(value, settings) {jQuery(this).html(value.slice(1,-1));}//gestion de quote en trop
	    });	
	//modifier la open d'un tournoi tournois 
	var ajaxurl_ = ajaxurl + '?action=tennisdefi_gestion_tournoi'
							+'&fonction=update_tournoi_resume'
							+'&field=nom'
							+'&security='+jquery_page_gestionClub_pageTournoi_nonce;
	
	jQuery('.div_DiplayTournoiSummary_nom').editable(ajaxurl_,{
		 //tooltip   : 'Cliquer pour éditer...',
		cancel    : 'Annuler',
	    submit    : 'modifier',
	    callback : function(value, settings) {jQuery(this).html(value.slice(1,-1));}//gestion de quote en trop
	    });	

}//fin  update_tournoi_information()



// retirer un joueur d'un tournoi
function AddRemove_Users_from_tournoi(tournoi_id_crypted, action_nom, data_to_send){
	
	//alert("Tounoi : "+tournoi_id_crypted + "  - Action :" + action_nom);
	//console.log( "DATA :  ");
	//console.log(data_to_send);
	//alert("Réalisation de l'action en cours");
	
	jQuery.ajax({

		url : ajaxurl ,
		data : {
			'security':jquery_page_gestionClub_pageTournoi_nonce,
			'action' : 'tennisdefi_gestion_tournoi',
			'fonction' : 'update_tournoi',
			'users_selected_data' : {data_to_send:data_to_send},
			'tournoi_encrypted' : tournoi_id_crypted,
			'tournoi_action' : action_nom,
		},
		dataType : 'JSON',
		success : function(data) {
			// Que fait on ?
			//alert(data);
			//affichage du tournoi modifié
			display_one_tournoi(tournoi_id_crypted, false);// on ne fera pas defiler la page au niveau du tournoi
			
		},

		error : function(errorThrown) {
			// alerte qu'une erreur est arrivee
			alert("une erreur est arrivée. Prévenez l'administrateur du site." );
		}
	}); // fin jQuery.ajax
	
}// fin fct AddRemove_Users_from_tournoi

// Ajouter un tournoi
function addTournoi(dialog, tips, tournoi_nom, tournoi_description, tournoi_visibilite, tournoi_open) {
	var valid = true;
	
	tips.text("Création en cours");
	//alert("création du tournoi "+name.val()+"Validité "+visibilite.filter(':checked').val());

	//AJAX
	//Creation du tournoi par AJAx
	jQuery.ajax({

		url : ajaxurl ,
		data : {
			'security':jquery_page_gestionClub_pageTournoi_nonce,
			'action' : 'tennisdefi_gestion_tournoi',
			'fonction' : 'create_tournoi',
			'tournoi_nom' : tournoi_nom,
			'tournoi_visibilite' : tournoi_visibilite,
			'tournoi_open' : tournoi_open,
			'tournoi_description' : tournoi_description,
		}, //fin data
		dataType : 'JSON',
		success : function(data) {
			// Que fait on ?
			if(data=='OK'){
				
				// Mise à jour du selector avec les tournoi
				tips.text( "Création du tournoi : OK" );
				dialog.dialog( "close" );
				
				//-> mise à jour liste des tournois  et du selecteur
				display_all_tournois();
			}
			else
				tips.text( "Une erreur est survenue, rechargez la page et contactez le site pour signaler une erreur." );
			console.log( data);
		},

		error : function(errorThrown) {
			// alerte qu'une erreur est arrivee
			tips.text( "Une erreur est survenue" );
		}
	}); // fin jQuery.ajax


	//dialog.dialog( "close" );

	return valid;
}

function deleteTournoi(id_tournoi){
	//Suppression du tournoi par AJAx
	jQuery.ajax({
		url : ajaxurl ,
		data : {
			'security':jquery_page_gestionClub_pageTournoi_nonce,
			'action' : 'tennisdefi_gestion_tournoi',
			'fonction' : 'delete_tournoi',
			'tournoi_selected' :id_tournoi
		},
		dataType : 'JSON',
		success : function(data) {
			//Mise à jour
			display_all_tournois();
	
		}, // fin success
	
		error : function(errorThrown) {

			//Mise à jour
			}
		}); // fin jQuery.ajax

}

// **************************************
// **************************************
jQuery(document)
.ready(  function() {

	
	// Init du tableau/ Gestion du datatable // SUPPRESSION DU CHARGEMENT
	table_tournois = jQuery('#table_DiplayTournoiSummary').dataTable({
		"fnInitComplete": function() {
            this.fnAdjustColumnSizing(true);
        },
			"drawCallback":  function( settings ) {
				//Un supprime le chargement
				display_loading('#pour_chargement_display', false);// Retrait  loading après chargement 
				// rechargement des editions
				update_tournoi_information();
								},//fin function
			} //fin datatable
	);
			
	table_palmares = jQuery('#table_toutnoi_palmares').DataTable({
		"iDisplayLength": -1,
		"columnDefs": [
		               {
		                   "targets": [ 3 ],
		                   "visible": false,
		                   "searchable": false
		               }]
	});
	
	table_tournoi = jQuery('#table_tournoi').dataTable({
		 "iDisplayLength": -1,	
		 "drawCallback":  function( settings ) {  
			 //on retire le chargement
				display_loading('#pour_chargement_display_tournoi', false);// Retrait  loading après chargement 
								
			//
							
		 },//fin function					
								
			} //fin datatable
	);



	// chargement de tous les tournois
	display_all_tournois();
	
	//TABS : Affichage en tABS
	jQuery( "#tabs" ).tabs({
		 activate: function(event ,ui){
            //console.log(event);
            //alert(  ui.newTab.index());
			//alert( ui.newTab.attr('li',"innerHTML")[0].getElementsByTagName("a")[0].innerHTML);
			//table_tournois.fnAdjustColumnSizing(true);
			 // On rafraichit :  
			jQuery.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
				//alert( this.text);
        }
	      //collapsible: true
	    });
	
	// Tooltip
	jQuery( document ).tooltip();
	//mise à jour des On/Off
	


	
	
	//options = { /* see below */ };
	//jQuery("input#great").switchButton(options);
	



	// suppression des alertes
	jQuery("#alert_tournoi").hide();

	// Gestion Visibilité du tournoi
	//jQuery( "#checkbox" ).buttonset();

	// ==============================================
	//gestion fonction ajax (sur la classe des bouton)
	// ==============================================
	// Dialog pour la Demande de confirmation de suppression de tournoi
	jQuery( "#dialog-confirm_deleteTournoi" ).dialog({
		  autoOpen: false,
	      show: {
	        effect: "blind",
	        duration: 1000
	      },
	      hide: {
	        effect: "explode",
	        duration: 1000
	      },
	      resizable: false,
	      height:200,
	      modal: true,
	      buttons: {
	        "Supprimer": function() {
	        	deleteTournoi(id_tournoi_to_delete);
	        	jQuery( this ).dialog( "close" );
	        },//supprimer
	        "annuler": function() {
	        	
	        	jQuery( this ).dialog( "close" );
	        }//annuler
	      }//fin bouttons
	    }); // fi demande dialogue 
	
	// ==============================================
	// Summary Tornois
	// ==============================================
	// Supprimer un tournoi
	jQuery('#table_DiplayTournoiSummary').on('click','.boutontennisdefi_deleteTournoi',function(event){	
		 id_tournoi_to_delete = jQuery(this).attr('id');
		//alert('ID tournoi = '+id_tournoi)
		jQuery( "#dialog-confirm_deleteTournoi" ).dialog("open");

	}); // fin clic sur bouton supprimer  le tournoi
	//
	
	
	//affciher un   les torunois 
	jQuery('#table_DiplayTournoiSummary').on('click','.boutontennisdefi_displayTournoi',function(event){
													 
			event.preventDefault();

					var id_tournoi = jQuery(this).attr('id');
					var follow_the_link = true;
					display_one_tournoi(id_tournoi, follow_the_link)
			});//fin du click sur le bouton
	
	// ==============================================
	//Affichage d'un tournoi : gestion suppression d'un joueur
	// ==============================================
	jQuery('#table_tournoi tbody').on( 'click', '.boutontennisdefi_removeuser', function () {

			
		var tournoi_id_crypted = jQuery("input[name=id_tournoi_displayed]").val();
		var action_nom  = 'retirer'; // doit etre coherent avec le selecteur dans la section palmares
		var data_to_send = [jQuery(this).attr('id')];
		

		AddRemove_Users_from_tournoi(tournoi_id_crypted, action_nom, data_to_send)
		
		
		
	} );

	// ==============================================
	// GEstion table PAlmares et des click
	// ==============================================
	jQuery('#table_toutnoi_palmares tbody').on( 'click', 'tr', function () {
		jQuery(this).toggleClass('row_selected');

		
		//jQuery('#menu-item-24308').parent().css("visibility", "visible");
		/*jQuery('#menu-item-24308').parent().css("display", "block");
		jQuery('#menu-item-24308').css("visibility", "visible");
		//jQuery('#menu-item-24308').parent().css("background", 100);
		//jQuery('#menu-item-24308').parent().css("position", "relative");
		jQuery('#menu-item-24308').css("position", "relative");
		*/
		//jQuery('#menu-item-24308').parent().addClass('hover') ;//sub-menu
		//jQuery('#menu-item-24308').addClass('hover') ;//sub-menu
		//jQuery('#menu-item-9613').addClass('hover') ;//sub-menu

		//Mise à jour du comptage
		var NB = table_palmares.rows('.row_selected').data().length;
		jQuery("#div_id_nb_selectedrows").html(NB+" joueurs sélectionné(s)");
		// console.log( table_palmares.row( this ).data() );	
	} );
	




	//déselection des lignes 
	jQuery('#linkResetSelection').click( function () {
		jQuery('#table_toutnoi_palmares tr').removeClass("row_selected");

	} );

	// ==============================================
	// Creationd'un tournoi
	// ==============================================

	var dialog, form,

	// From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
	emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;

	//gestion de la fenetre de formulaire
	dialog = jQuery( "#dialog-form" ).dialog({
		autoOpen: false,
		height: 400,
		//width: 350,
		modal: true,
		buttons: {
			"Crée ce tournoi": function(e){
				
				var tournoi_nom  = jQuery( "#name" ).val();
				//visibilite = jQuery('input[name=visibilite]');
				var tournoi_visibilite = jQuery( "input[name=tournoi_visibilite]" ).filter(':checked').val();
				var tournoi_open 		= jQuery( "input[name=tournoi_open]" ).filter(':checked').val();
				var tournoi_description = jQuery( "textarea[name=tournoi_description]" ).val();
				var tips = jQuery( ".validateTips" );
				
				console.log(tournoi_nom);
				console.log(tournoi_description);
				console.log(tournoi_visibilite);
				console.log(tournoi_open);
				
				return addTournoi(dialog, tips, tournoi_nom, tournoi_description, tournoi_visibilite,tournoi_open);	
			
			}, //fonction à appeler
			Cancel: function() {
				dialog.dialog( "close" );
			}
		},
		close: function() {
			form[ 0 ].reset();
			// allFields.removeClass( "ui-state-error" );
		}
	});


	form = dialog.find( "form" ).on( "submit", function( event ) {
		event.preventDefault();
		//addUser();
	});

	jQuery( "#create-tournoi" ).button().on( "click", function() {

		//tips.text( "Création en cours" )
		dialog.dialog( "open" );
	});



	// ==============================================
	// GEstion des actions
	// ==============================================
	// Ajout des tooltip sur la page
	jQuery(".tooltips_action_dans_tournoi").tooltip({
		content: function() {
			return  "<small><ul>" +
			"<li>Sélectionnez des joueurs dans le tableau ci-dessous</li>" +
			"<li>Séléctionnez une action (Ajouter/retirer/etc.) dans le menu</li>" +
			"<li>Séléctionnez un tournoi dans le menu pour lequel realiser l'action</li>" +
			"<li>Valider votre action en cliquant sur le bouton</li>" +
			"</ul></small>";
		}});

	// Realiser l'action sur un tournoi
	jQuery('#do_action').click( function () {

		var tournoi_id_crypted = jQuery("#tournoi_selected_for_action").val();
		var action_nom  = jQuery("#tournoi_action").val();
		var data_selected = table_palmares.rows('.row_selected').data();
		console.log(data_selected);
		
		var data_to_send = [];
		for (var i = 0; i < data_selected.length; i++) { 
			
			console.log("détails de temp: ");	
			var temp = data_selected[i];
				console.log(temp);
				id_crypted = temp[3];
				console.log(id_crypted);
				console.log("on ajoute au tableau : "+id_crypted);
		data_to_send[i] = id_crypted; // On ne garde que l'ID cryptee (dans la premiere colonne)
				
		}

		AddRemove_Users_from_tournoi(tournoi_id_crypted, action_nom, data_to_send)
		 
	} );


});

