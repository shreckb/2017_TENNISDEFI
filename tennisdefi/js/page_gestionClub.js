jQuery(document)
.ready(
		function() {
		


		jQuery('#adduserbutton').click(function( event ) {
									 
			event.preventDefault();
			jQuery('.form-group').removeClass('info-box success'); // remove the error class
			jQuery('.form-group').removeClass('info-box alert'); // remove the error class
    		jQuery('.help-block').remove(); // remove the error text
   


			jQuery("#tennisdefi_form_createUser_LoadingImage").show();
			jQuery.ajax({
				url : ajaxurl ,
				data : {
					'security':jquery_page_gestionClub_pageMain_nonce,
					'action' : 'tennisdefi_gestionClubAdmin',
					'fonction' : 'add_user',
					'email' : jQuery('input[name=email]').val(),
					'nom' : jQuery('input[name=nom]').val(),
					'prenom' : jQuery('input[name=prenom]').val(),
					'idclub' :jQuery('input[name=idclub]').val(),
				},
				dataType : 'JSON',
				success : function(data) {
						jQuery("#tennisdefi_form_createUser_LoadingImage").hide();
						// log data to the console so we can see
			        	console.log(data);
			        	// here we will handle errors and validation messages
				        if ( ! data.success) {
				            
				            // handle errors for name ---------------
				            if (data.errors.nom) {
				                jQuery('#nom-group').addClass('info-box alert'); // add the error class to show red input
				                jQuery('#nom-group').append('<div class="help-block">' + data.errors.nom + '</div>'); // add the actual error message under our input
				            }

				            // handle errors for name ---------------
				            if (data.errors.prenom) {
				                jQuery('#prenom-group').addClass('info-box alert'); // add the error class to show red input
				                jQuery('#prennom-group').append('<div class="help-block">' + data.errors.prenom + '</div>'); // add the actual error message under our input
				            }

				            // handle errors for email ---------------
				            if (data.errors.email) {
				                jQuery('#email-group').addClass('info-box alert'); // add the error class to show red input
				                jQuery('#email-group').append('<div class="help-block">' + data.errors.email + '</div>'); // add the actual error message under our input
				            }
				            // handle errors for email ---------------
				            if (data.errors.message) {
				                jQuery('#form_create_user').append('<div class="info-box alert">' + data.errors.message + '</div>');
							}

				        } else {

				            // ALL GOOD! just show the success message!
				            jQuery('#form_create_user').append('<div class="info-box success">' + data.message + '</div>');

				            // usually after form submission, you'll want to redirect
				            // window.location = '/thank-you'; // redirect a user to another page
				            //alert('success'); // for now we'll just alert the user

				        }

					}//fin success
				});

		});

			// ========================================
			//Tables Dans la plage
			// ========================================

			//----------------table_convivialite---------
			jQuery('#table_convivialite')
			.dataTable(
					{
						"iDisplayLength": 5,
						"stripeClasses": [ 'table_palamares_odd', 'table_palamares_even' ],
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
							"sInfo" : "Affichage de _START_ &agrave; _END_ sur _TOTAL_ joueurs",
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
						

					}); // fin datatbale

	


//----------------table_convivialite---------
jQuery('#table_resultats')
.dataTable(
		{
			"iDisplayLength": 5,
			"stripeClasses": [ 'table_palamares_odd', 'table_palamares_even' ],
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
				"sSearch" : "Rechercher&nbsp; un match:",
				"sLengthMenu" : "Afficher _MENU_ match par page",
				"sInfo" : "Affichage des match _START_ &agrave; _END_ sur _TOTAL_ match",
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

			columnDefs: [ { type: 'date-eu', targets: ['datatable_date']},
			              {sortable: false,targets: ['datatable_date']}
						   ]

		}); // fin datatbale

});

