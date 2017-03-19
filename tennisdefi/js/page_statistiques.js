jQuery(document)
		.ready(
				function() {
                        /*
					// GEstions des chiffres
					jQuery(".knob_stat_Partenaires").knob({
						readOnly : true,
						displayInput : true,

					});
					jQuery(".knob_stat_Victoires").knob({
						readOnly : true,
						displayInput : true,
						'draw' : function() {
							jQuery(this.i).val(this.cv + '%')
						}
					});
					jQuery(".knob_stat_Defaites").knob({
						readOnly : true,
						displayInput : true,
						'draw' : function() {
							jQuery(this.i).val(this.cv + '%')
						}
					});
					jQuery(".knob_stat_Nuls").knob({
						readOnly : true,
						displayInput : true,
						'draw' : function() {
							jQuery(this.i).val(this.cv + '%')
						}
					});
                    */
					
					
					// Animation NB Partenaires (http://rapiddg.com/blog/animated-charts-jquery-knob-and-jqueryanimate)
					var val_knob = jQuery('.knob_stat_Partenaires').attr('value'); 

					jQuery({animatedVal: 0}).animate({animatedVal: val_knob}, {
				       duration: 2000,
				       easing: "swing", 
				       step: function() { 
				    	   jQuery(".knob_stat_Partenaires").val(Math.ceil(this.animatedVal)).trigger("change"); 
				       }
				    }); 
			
					  
					// GEstions des tables
					var length_data = 4;

					var traduction = {
						"sProcessing" : "Traitement en cours...",
						"sSearch" : "Avec un partenaire à préciser",
						"sLengthMenu" : "Afficher _MENU_ &eacute;l&eacute;ments",
						"sInfo" : "Affichage des résultats _START_ &agrave; _END_ sur _TOTAL_",
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
					};

					// ----------------table_statisitque---------
					// Page statisitque:

					jQuery('#Partenaires')
							.dataTable(
									{
										"aaSorting" : [],
										"responsive": true,
										"iDisplayLength" : length_data,
										"sDom" : '<"top"i>rt<"bottom"flp><"clear">',
										"bPaginate" : true,
										"bLengthChange" : true,
										"bFilter" : true,
										"bSort" : true,
										"bInfo" : true,
										"bAutoWidth" : false,
										"fnDrawCallback": function(oSettings) {
										        if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
										            jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
										        }else
                                                  jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').show();
										    },
										"oLanguage" : {
											"sProcessing" : "Traitement en cours...",
											"sSearch" : "Avec un partenaire à préciser",
											"sLengthMenu" : "Afficher _MENU_ &eacute;l&eacute;ments",
											"sInfo" : "Affichage des partenaires _START_ &agrave; _END_ sur _TOTAL_",
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
										}
									});

					

					jQuery('#Victoires').dataTable({
						"aaSorting" : [],
						"responsive": true,
						"iDisplayLength" : length_data,
						"sDom" : '<"top"i>rt<"bottom"flp><"clear">',
						"bPaginate" : true,
						"bLengthChange" : true,
						"bFilter" : true,
						"bSort" : true,
						"bInfo" : true,
						"bAutoWidth" : false,
						"oLanguage" : traduction,
                        "fnDrawCallback": function(oSettings) {
										        if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
										            jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
										        }else
                                                  jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').show();
										    },
						columnDefs: [ { type: 'date-eu', targets: ['datatable_date']},
									  {sortable: false,targets: ['datatable_nosort']} // ne pas trier la colonne avec class nosort 
									 ]
					});

					jQuery('#Defaites').dataTable({
						"aaSorting" : [],
						"responsive": true,
						"iDisplayLength" : length_data,
						"sDom" : '<"top"i>rt<"bottom"flp><"clear">',
						"bPaginate" : true,
						"bLengthChange" : true,
						"bFilter" : true,
						"bSort" : true,
						"bInfo" : true,
						"bAutoWidth" : false,
						"oLanguage" : traduction,
                        "fnDrawCallback": function(oSettings) {
										        if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
										            jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
										        }else
                                                  jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').show();
										    },
						columnDefs: [ { type: 'date-eu', targets: ['datatable_date']},
									  {sortable: false,targets: ['datatable_nosort']} // ne pas trier la colonne avec class nosort 
									 ]
					});

					jQuery('#Matchs_Nuls').dataTable({
						"aaSorting" : [],
						"responsive": true,
						"iDisplayLength" : length_data,
						"sDom" : '<"top"i>rt<"bottom"flp><"clear">',
						"bPaginate" : true,
						"bLengthChange" : true,
						"bFilter" : true,
						"bSort" : true,
						"bInfo" : true,
						"bAutoWidth" : false,
						"oLanguage" : traduction,
                        "fnDrawCallback": function(oSettings) {
										        if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
										            jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
										        }else
                                                  jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').show();
										    },
						columnDefs: [ { type: 'date-eu', targets: ['datatable_date']},
									  {sortable: false,targets: ['datatable_nosort']} // ne pas trier la colonne avec class nosort 
									 ]
					});

					

				});