jQuery(document).ready(function(){
    
     
    ajaxURL = ajaxurl;

    
    
    
  // ********************** 
  //affichage de tous les clubs
  // **********************

    //Requete
     jQuery('#recherche_club').keyup(function(){ 
        if(jQuery('#recherche_club').val().length >3)
            //doAjaxRequest(); 
            doAjaxRequest_V2(ajaxURL); 
    });
     
    // **********************
    // MAP
    // **********************
     // Map Init (variable global)
      markers = [];
      marker_content = [];
     makers_index = -1;
     // Centre de la carte
      center_Lat = 45.789758;
      center_Lng = 3.103971;
     
    initMap(center_Lat, center_Lng);
    google.maps.event.addDomListener(window, 'resize', function() {
    	var center = new google.maps.LatLng(center_Lat, center_Lng);
  	    map.panTo(center);
    	});
    
    // Permet de recentrer la carte si resize de la fenetre ou au chargemebt
    
    //Gestion du cghargement
    //http://www.basicslabs.com/Projects/progressBar/
    pb = progressBar();
            map.controls[google.maps.ControlPosition.RIGHT].push(pb.getDiv());
        
    var NB = 4; // Nb d'iteration
    pb.start(NB*25);
      GgtCLubListOnMapp(ajaxURL, 0,NB);


    
     // Gestion des recherches
      // ============================
      //https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete-addressform
      //placeSearch, autocomplete;
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
      };
      
      autocomplete = new google.maps.places.Autocomplete(
    	        /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
    	        {types: ['geocode']});

      // Create the autocomplete object, restricting the search to geographical
      // location types.
      autocomplete = new google.maps.places.Autocomplete(
          /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
          {types: ['geocode']});

      // When the user selects an address from the dropdown, populate the address
      // fields in the form.
      autocomplete.addListener('place_changed', centerMap);

    
    
});



function centerMap() {
	  // Get the place details from the autocomplete object.
	  var place = autocomplete.getPlace();
	  var lat = place.geometry.location.lat();
	  var lng = place.geometry.location.lng();
	  
  		var center = new google.maps.LatLng(lat, lng);
	    map.panTo(center);
	    //map.setViewport(place.geometry.viewport);
	    map.setZoom(14);
	    
	    
	  console.log(place);
	  //console.log(lng);

	  //alert(place);
}

//[START region_geolocation]
//Bias the autocomplete object to the user's geographical location,
//as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
if (navigator.geolocation) {
 navigator.geolocation.getCurrentPosition(function(position) {
   var geolocation = {
     lat: position.coords.latitude,
     lng: position.coords.longitude
   };
   var circle = new google.maps.Circle({
     center: geolocation,
     radius: position.coords.accuracy
   });
   autocomplete.setBounds(circle.getBounds());
 });
}
}



// Creer la carte
function initMap(Lat,Lng) {
        var mapOptions = {
          center: new google.maps.LatLng( Lat, Lng),
        
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          zoom: 5
        };
         map = new google.maps.Map(document.getElementById("js-map-container"),
            mapOptions);
      
     
         
         
      }

	



// Recehrche et ajoute club sur la carte
function GgtCLubListOnMapp(ajaxURL, segment, nb_total){

        
    // Part 1
     jQuery.ajax({
          url:  ajaxURL, //'http://localhost:8888/site03-testImport/wp-admin/admin-ajax.php', //'http://localhost:8888/site02-WordPress/wp-admin/admin-ajax.php',
          data:{
               'action':'do_ajax',
               'fn':'get_clubs_map_part',
               'segment' : segment,
               'nb_total' : nb_total,
               },
          dataType: 'JSON',
          success:function(data){
              
          
                    oInfo = new google.maps.InfoWindow();
                //alert('Carte, NB club :' + data.length + "titre ="+ data[0]['post_title']+"Long = "+ parseFloat(data[
               // 0]['longitude']));     
                for (var i=0;i<data.length;i++){ 
                    addMarker(i, parseFloat(data[i]['latitude']), parseFloat(data[i]['longitude']), data[i]);
                }
                var mcOptions = {gridSize: 50, maxZoom: 15};
                var mc = new MarkerClusterer(map, markers,mcOptions );
                if(   segment == nb_total-1)
                    pb.hide();
                else{
                    pb.updateBar(segment*25);
                    GgtCLubListOnMapp(ajaxURL, segment+1, nb_total);
                }
              
         
            
              
            }, // Fin success(data)

          error: function(errorThrown){
               //alert('Erreur: Impossible de charger les clubs');
               jQuery("#erreur_affichage_mapClubs").show();
               jQuery("#erreur_affichage_mapClubs").append(jQuery('Impossible de charger tous les clubs').fadeIn('slow'));
				
				
               console.log(errorThrown);
              if(   segment == nb_total-1)
                    pb.hide();
            else
                    pb.updateBar(segment*25);
          }
 
     });
             
 
}


//------------------
// Recherche de club par champ de recehrceh et affichage tableau
function doAjaxRequest_V2(ajaxURL){


    jQuery('#table_clubs').dataTable( {
        "destroy" : true,
        "processing": false,
        "serverSide": false,
        "bFilter"  : false,
        "aaSorting" : [],
        "sAjaxSource": ajaxURL + '?action=do_ajax&fn=get_latest_posts&count=10&txtsearch='+jQuery('#recherche_club').val(), //'http://localhost:8888/site03-testImport/wp-admin/admin-ajax.php?action=do_ajax&fn=get_latest_posts&count=10&txtsearch='+jQuery('#recherche_club').val(),
         "aoColumns": [
            { "sTitle": "Club" },
            { "sTitle": "Joueurs <br>Tennis-Defi" },
            { "sTitle": "Adresse" },
                    ],
        "oLanguage": {
                "sProcessing":     "Traitement en cours...",
                "sSearch":         "Rechercher&nbsp; un partenaire:",
                "sLengthMenu":     "Afficher _MENU_ clubs",
                 "sInfo":           "Affichage des clubs _START_ &agrave; _END_ sur _TOTAL_ clubs",
                  "sInfoEmpty":      "Affichage du club 0 &agrave; 0 sur 0 club",
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
 
}


//---------------------------
// ajoute un marqueur sur la carte 
function addMarker(i, lat, lng, data) {
    if (!isNaN(lat) && !isNaN(lng) && lng != null && lat !=0 & lng!=0 && lat !='' && lng !='') {
        
        
            //var myLatlng = new google.maps.LatLng(lat, lng);
            // To add the marker to the map, use the 'map' property
           // var marker = new google.maps.Marker({position: myLatlng, map: map,zIndex: i, title:data['post_title']});
       
        
         // ajout fenetre
         /*
        var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+
      '<h5 id="firstHeading" class="firstHeading">'+data['post_title']+'</h5>'+
      '<div id="bodyContent">'+
      '<li> Nombre de joueurs : '+data['tennisdefi_nbJoueurs']+'</li>'+
      '<li> Adresse : '+data['adresse']+' '+ data['adresse2']+', '+data['ville']+' '+data['cp']+'</li>'+
      '</div>'+
      '</div>';

      var infowindow = new google.maps.InfoWindow({
          content: contentString,
          maxWidth: 200
      });
      
      google.maps.event.addListener(marker, 'click', function() {
        infowindow.open(map,marker);
        });
        
        */
        
         
        myLatLng = new google.maps.LatLng(lat, lng);
        bounds = new google.maps.LatLngBounds();
        
       

            makers_index ++;  
        //alert("Ajout item: "+makers_index+"=>"+data['post_title']+"\tLat ="+lat +"\zIndex:"+makers_index);
        var marker = new google.maps.Marker({ position: myLatLng,  map: map, zIndex: makers_index});
            marker.title = data['post_title'];
        
        //eval('var marker' + i + ' = new google.maps.Marker({ position: myLatLng,  map: map, zIndex: i});');
         

        
       // var marker_obj = marker; //eval('marker' + i);
        //bounds.extend(marker_obj.position);
       // markersArray.push(eval('marker' + i));
       // marker_obj.title = data['post_title'];
        
        
      var content =  '</div>'+
      '<h5 id="firstHeading" class="firstHeading">'+data['post_title']+'</h5>'+
      '<div id="bodyContent">'+
      '<li> Nombre de joueurs : '+data['tennisdefi_nbJoueurs']+'</li>'+
      '<li> Adresse : '+data['adresse']+' '+ data['adresse2']+', '+data['ville']+' '+data['cp']+'</li>'+
      '</div>'+
      '</div>';
      
        
        marker_content.push(content);
        markers.push(marker);
        
       // eval('var infowindow' + i + ' = new google.maps.InfoWindow({ content: content,  maxWidth: 370});');
       // var infowindow_obj = eval('infowindow' + i);
        //var marker_obj = eval('marker' + i);
        google.maps.event.addListener(marker, 'click', function () {
             oInfo.setContent( marker_content[marker.zIndex]);
             oInfo.open( map, marker);
             } )
         //   infowindow_obj.open(map, marker_obj);
        //);}
    

                }// lat  != null
        
        
        
       
  
  
}



