jQuery(document)
.ready(
		function() {

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

