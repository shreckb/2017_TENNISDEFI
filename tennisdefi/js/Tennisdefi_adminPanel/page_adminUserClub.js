jQuery(document).ready(function(){

	jQuery( "#user_list_id" ).select2();
	
	jQuery( "#user_list_id" ).on('change', function() {
		  //alert( this.value ); // or $(this).val()
		  var id_joueur = this.value;
		  //reset
		  jQuery("#select_displayUserLcubs").html('<OPTION> ...</OPTION>');
	      
		  
		  jQuery.ajax({
	          url:  ajaxurl, 
	          data:{
	               'action':'adminPage_getUserCLubsID',
	               'id_user':id_joueur
	               
	               },
	          dataType: 'JSON',
	          success:function(data){
	        	  var options = [];
	        	 
	        	      for (var i = 0; i < data.length; i++) {
	        	          options.push('<option value="',
	        	        		  data[i].id_palamres, '">',
	        	        		  data[i].nom_club, '(N°',
	        	        		  data[i].rang, ')</option>');
	        	      }
	        	      jQuery("#select_displayUserLcubs").html(options.join(''));
	        	      
	              //alert("DATA recue");
	              console.log(data);
	              console.log(data[0].nom_club);
	            }, // Fin success(data)

	          error: function(errorThrown){
	        	  jQuery("#select_displayUserLcubs").html('<OPTION> ------</OPTION>');
        	      
	             console.log(errorThrown);
	          	}
  
		});

	
	});//fin du onchange
	
	
	
	// ********************************************
	// Section Transfert Resultat d'un joueur à l'autre
	// *********************************************
	//alert("Hello world");
	jQuery( "#transfertResultats_JoueurSource" ).select2();
	jQuery( "#transfertResultats_JoueurDestination" ).select2();

		// Etape 1 : Choix du jouer => Update Liste des clubs
	// =====================================================
	jQuery( "#transfertResultats_JoueurSource" ).on('change', function() {
		  //alert( "Ca click !"); // or $(this).val()
		  var id_joueur = this.value;
		  //reset
		  jQuery("#transfertResultats_JoueurClub").html('<OPTION> recherche en cours </OPTION>');
	      
		  
		  jQuery.ajax({
	          url:  ajaxurl, 
	          data:{		 
	               'action':'adminPage_getUserCLubsID',
	               'id_user':id_joueur
	               
	               },
	          dataType: 'JSON',
	          success:function(data){
	        	  console.log(data);
	        	  var options = [];
	        	  options.push('<option value="">selectionner le club</option>');
	        	 
	        	      for (var i = 0; i < data.length; i++) {
	        	          options.push('<option value="',
	        	        		  data[i].id_club, '">',
	        	        		  data[i].nom_club, '',
	        	        		  '(N°', 		data[i].rang,		 	' | ',
	        	        		  'NB_match:', 	data[i].nb_match, ' | ',
	        	        		  'V:', data[i].victoires, 		 ' | ',
	        	        		  'D:', data[i].defaites, 		 ' )',
	        	        		  ')</option>');
	        	      }
	        	      jQuery("#transfertResultats_JoueurClub").html(options.join(''));
	        	      
	              //alert("DATA recue");
	              
	            }, // Fin success(data)

	          error: function(errorThrown){
	        	  jQuery("#transfertResultats_JoueurClub").html('<OPTION> ------</OPTION>');
        	      
	             console.log(errorThrown);
	          	}
  
		});

	});//fin du onchange sur le joueur

	// Etape 2 : Choix du club => mise à jour des listes des joueur du club
	// =====================================================
	jQuery( "#transfertResultats_JoueurClub" ).on('change', function() {
		  //alert( this.value ); // or $(this).val()
		//alert( "Ca click !"); // or $(this).val()
		  var id_club = this.value;
		  //reset
		  jQuery("#transfertResultats_JoueurDestination").html('<OPTION> recherche en cours </OPTION>');
		  jQuery("#transfertResultats_JoueurDestination").select2();
		  
		  jQuery.ajax({
	          url:  ajaxurl, 
	          data:{
	               'action':'adminPage_getUsersPalmaresIDfromClub',
	               'id_club':id_club
	               
	               },
	          dataType: 'JSON',
	          success:function(data){
	        	  var options = [];
	        	  options.push('<option value="">selectionner le joueur</option>');
	        	 
	        	      for (var i = 0; i < data.length; i++) {
	        	          options.push('<option value="',
	        	        		  data[i].id_user, '">',
	        	        		  				data[i].displayName, '(',
	        	        		  'N°', 		data[i].rang,		 	' | ',
	        	        		  'NB_match°', 	data[i].nb_match, ' | ',
	        	        		  'V', data[i].victoires, 		 ' | ',
	        	        		  'D', data[i].defaites, 		 ' )',
	        	        		  ')</option>');
	        	      }
	        	      jQuery("#transfertResultats_JoueurDestination").html(options.join(''));
	        	      jQuery("#transfertResultats_JoueurDestination").select2();
	        	      
	              //alert("DATA recue");
	              console.log(data);
	            
	            }, // Fin success(data)

	          error: function(errorThrown){
	        	  jQuery("#transfertResultats_JoueurDestination").html('<OPTION> ------</OPTION>');
	        	  jQuery("#transfertResultats_JoueurDestination").select2();
      	      
	             console.log(errorThrown);
	          	}

		});

	});//fin du onchange sur le club
	
	
	
	// Etape 3 : effectuer le changement
	// =====================================================
	jQuery( "#transfertResultats_JoueurDestination" ).on('change', function() {
		//alert('Click ()');
		var txt_club 			= jQuery( "#transfertResultats_JoueurClub" ).find('option:selected').text();
		var id_club 			= jQuery( "#transfertResultats_JoueurClub" ).find('option:selected').val();
		var txt_joueur_source 	= jQuery( "#transfertResultats_JoueurSource" ).find('option:selected').text();
		var id_joueur_source 	= jQuery( "#transfertResultats_JoueurSource" ).find('option:selected').val();
		var txt_joueur_dest 	= jQuery( "#transfertResultats_JoueurDestination" ).find('option:selected').text();
		var id_joueur_dest 		= jQuery( "#transfertResultats_JoueurDestination" ).find('option:selected').val();
		
		var dialog_txt = 'Voulez vous transferer les résultat de : \n\n' 
						+ txt_joueur_source + '(id = '+ id_joueur_source + ')'
						+ '\n\nvers\n\n' 
						+ txt_joueur_dest + '(id = '+ id_joueur_dest 
						+ '\n\n dans le club : ' +txt_club+ '(id = '+ id_club + ')'
						+ ') \n\n Attention cette action est irréversible ';
						
			var r = confirm(dialog_txt);
			
			if (r == false){
				//alert("REfus pris en comte");
				return;
				}

		jQuery("#AJAX_statut").html("<font size=\"3\" color=\"red\">Debut du traitement</font>");
		  
		  jQuery.ajax({
	          url:  ajaxurl, 
	          data:{
	               'action':'adminPage_changeREsultatFromJoueurAtoB',
	               'id_club':id_club,
	               'id_joueurSource': id_joueur_source,
	               'id_joueurDest':id_joueur_dest,
	               
	               },
	          dataType: 'JSON',
	          success:function(data){
	        	  jQuery("#AJAX_statut").html("<font size=\"3\" color=\"green\">"+data+"</font>");
	        	  
	              console.log(data);
	            
	            }, // Fin success(data)

	          error: function(errorThrown){
	        	
	        	  jQuery("#AJAX_statut").html("<font size=\"3\" color=\"red\">erreur voir la console</font>");
	             console.log(errorThrown);
	          	}

		});//fin du ajax
		  
	
		});//fin du onchange final

});