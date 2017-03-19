jQuery(document)
		.ready(
				function() {
					
					
					
					
					
					jQuery( document ).tooltip();
					
					// Annimaion
				/*	jQuery("#Legende1").click(function() {
						jQuery( "#table_palmares" ).slideDown( 1000);
						//
					});
					*/
					
		//Personnalisation Selec palamres et mise de la valeur en surbrillance
			    
		jQuery("#palmares_lisOf").val(palmares_type);
		jQuery("#palmares_filtres").val(palmares_categorie);
			
		//----------------table_palmares---------
		jQuery('#table_palmares')
							.dataTable(
									{
									"iDisplayLength": 100,
									"stripeClasses": [ 'table_palamares_odd', 'table_palamares_even' ],
										"responsive": true,
										//"jQueryUI":       true,
										"sDom" : 'Rlfrtip',//'<"top"i>rt<"bottom"flp><"clear">', //'Rlfrtip',
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
										    
                                            
                                            
											//jQuery(".ajax").colorbox();
											jQuery(".cboxElement_palmares")
													.colorbox(
															{

																rel : "groupe1",
																close : "fermer",
																xhrError : "impossible de charger le contenu",
																//next : "suiv.",
																//previous : "préc.",
																current : "joueur {current} sur {total}",
																initialWidth : 300,
																initialHeight : 200,
																title : function() {
																	return ''
																},//suppression du titre
																opacity : 0.8,
																preloading : true,
																//slideshow:true,
																
															}); //fin colorbox

										}//fin fnDrawCallback

										
									}); // fin datatbale
		
		//-------------------------------------------------
		// Gestion Adminsitrateur qui peut modifier email
		//-------------------------------------------------
		var ajaxurl_ = ajaxurl + '?action=Palamares_AdminsitrationEmail';
	     jQuery('.edit').editable(ajaxurl_,{
	         indicator : 'Enregistrement...',
	         tooltip   : 'Cliquer pour éditer...',
	         cancel    : 'Annuler',
	         submit    : 'OK',
	     });
		
		
		
				});
