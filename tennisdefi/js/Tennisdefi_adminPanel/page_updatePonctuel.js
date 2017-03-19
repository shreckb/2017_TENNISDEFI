jQuery(document).ready(function(){
	//alert("page_updatePonctuels.js");
	
	
	
	//requette ajax et progression : Fonctions génériques 
	function my_ajax_runtime(action, fn, div_data, bvar_complete){
		console.log("my_ajax_runtime(fn="+fn+")");
		jQuery.ajax({
	         url:  ajaxurl, 
	         async:true,
	         data:{'action':action, 'fn' : fn},
	         dataType: 'JSON',
	         success:function(data){
	        	 //alert('donnee recue : '+ data);
	        	 jQuery(div_data).html(data);
	        	 bvar_complete.prop = true;
	        	 
	         }, // Fin success(data)
	         error: function(errorThrown){ 
	        	 bvar_complete.prop = true;
	        	 jQuery(div_data).html(ajaxurl);
	         }
	         
	    });
		
	}
	
	function my_ajax_runtime_progress(action, fn, div_info, bvar_complete) {

		if(bvar_complete.prop)
			console.log("      fini");
		else{

	    	jQuery(div_info).show();
	    	jQuery.ajax({
	    		async:true,
	            url:  ajaxurl, 
	            data:{
	                 'action':action ,
	                 'fn' :fn
	                 },
	            dataType: 'JSON',
	            success:function(data){
	            	jQuery(div_info).html(data);
	            	/*jQuery(div_info).progressbar({
	            		value: data
	            	});*/
	            	//console.log(data);
	            	//console.log(bvar_complete.prop);
	            	if (!bvar_complete.prop){
	            		//console.log("  pas    fini");
	            		setTimeout(my_ajax_runtime_progress(action, fn, div_info, bvar_complete), 100);
	            	}
	            	else{
	            		jQuery(div_info).html(data);
	            		jQuery(div_info).hide();
	            		//console.log("      fini");
	           		 }
	           		
	            },
	            error: function(errorThrown){ 
	 
	           	 jQuery(div_info).html('Erreur');
	           	if (!bvar_complete.prop){
	                setTimeout(my_ajax_runtime_progress(action, fn, div_info, bvar_complete), 500);
	              }
	            }// fin error
	            
	       }); ///fin ajax
    }//fin if 
	
	}
	
	// TEST 1 : Action et progression
	// ==============================================
	var action ="menu_page_UpdatePonctuel_ajaxRequest";
	var fn = "update_palmares";
	var div_data = "#test1_dataReturn";
	var ajax_display_update_palmares_completed = {prop: false};
	my_ajax_runtime(action, fn, div_data,ajax_display_update_palmares_completed);
	
	var fn = "update_palmares_progress";
	var div_info = "#test1_progression";
	my_ajax_runtime_progress(action, fn, div_info, ajax_display_update_palmares_completed);
	
});