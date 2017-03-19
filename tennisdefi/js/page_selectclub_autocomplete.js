
jQuery(document).ready(function($){


	// Mise en forme table club
var length_data=  4;
    
    var traduction = {
    "sProcessing":     "Traitement en cours...",
    "sSearch":         "Avec un partenaire à préciser",
    "sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
     "sInfo":           "Affichage des résultats _START_ &agrave; _END_ sur _TOTAL_",
      "sInfoEmpty":      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
    "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
    "sInfoPostFix":    "",
    "sLoadingRecords": "Chargement en cours...",
    "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
    "sEmptyTable":     "Aucune donnée disponible dans le tableau",
    "oPaginate": {
        "sFirst":      " Premier",
        "sPrevious":   " Pr&eacute;c&eacute;dent  ",
        "sNext":       " Suivant ",
        "sLast":       " Dernier"
    },
    "oAria": {
        "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
        "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
    }
};
  
  
    
    
    //----------------table_statisitque---------
    //Page statisitque: 
    
    
    jQuery('#table_clubs_user').dataTable( {
                      "iDisplayLength": length_data,
                     "sDom": '<"top"i>rt<"bottom"flp><"clear">',
                  "bPaginate": false,
        "bLengthChange": true,
        "bFilter": false,
        "bSort": false,
        "bInfo": false,
        "bAutoWidth": false,
         "oLanguage": {
    "sProcessing":     "Traitement en cours...",
    "sSearch":         "Avec un partenaire à préciser",
    "sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
     "sInfo":           "Affichage des partenaires _START_ &agrave; _END_ sur _TOTAL_",
      "sInfoEmpty":      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
    "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
    "sInfoPostFix":    "",
    "sLoadingRecords": "Chargement en cours...",
    "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
    "sEmptyTable":     "Aucune donnée disponible dans le tableau",
    "oPaginate": {
        "sFirst":      " Premier",
        "sPrevious":   " Pr&eacute;c&eacute;dent  ",
        "sNext":       " Suivant ",
        "sLast":       " Dernier"
    },
    "oAria": {
        "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
        "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
    }
}
        } );
	
	// Champ de sélection du club
	
    $("#ID_club").select2({
        placeholder: "Choisir un club",
        width: "300px" ,
        minimumInputLength: 3,
        escapeMarkup: function (m) { return m; }, // we do not want to escape markup since we are displaying html in results
        
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            data: function (params) {
                return {
                    'recherche': params.term,
                    'action':'do_ajax',
                    'fn':'get_clubs_search',
                    
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        var value_text = item.post_title;
                                value_text += '<ul><li>Nombre de joueurs: '+ item.tennisdefi_nbJoueurs +'</li>';
                            if(item.cp != ' ' )
                                value_text += '<li>adresse : '+item.adresse + item.adresse2 + item.cp + item.ville +'</li>';
                        value_text += '</ul>';

                        return {                           
                            text: value_text, 
                            slug: 'slug....', //item.slug,
                            id: item.ID
                        }
                    })
                };
            }
        }
    });


});