function tennisdefi_demonstration(){
	//jQuery('#menu-item-9615').show();
	//jQuery('#menu-item-9615').parent().children('.sub-menu:first').show();
	//jQuery('#sub-menu').show();
	
	
	//jQuery('#menu-item-24308').parent().css("visibility", "visible");
	//jQuery('#menu-item-24308').parent().slideDown('fast').show(400); //Drop down the subnav on hover
	
	/*
	jQuery('#menu-item-9615').css("visibility", "visible");
	jQuery('#menu-item-9615').addClass("hover");
	jQuery('#menu-item-24308').css("visibility", "visible");
	*/
	
	//jQuery('.menu .ul').css("visibility","true");
	//jQuery(".current_page_item .sub-menu").show();
	//jQuery("ul.menu li a").trigger('mouseenter');
	
	/*
	jQuery('#menu-item-9613').toggleClass("hover");
	var A = jQuery('#menu-item-9613 a');
	A.show();
	console.log("jQuery('#menu-item-9613 a')");
	console.log(A);
	*/
	/*jQuery('#menu-item-9615 .a').slideToggle(function () {
		
		jQuery('a#responsive_menu_button').addClass('responsive-toggle-open');
		
	});*/
	
	var intro = introJs();
    
 
	//changement
     intro.onbeforechange(function(targetElement) {
    	 console.log("element ID ="+ jQuery(targetElement).attr('id'));
    	 var id = jQuery(targetElement).attr('id');
    	 if(id){
    		 
    		 console.log("visible?");
    		 A =  jQuery(targetElement).parent().find("ul.sub-menu");
    		 cosole.log(A);
    		 jQuery(targetElement).parent().find("ul.sub-menu").css("visibility", "visible"); 
    	 }
    	 
    	});
     
     
     intro.onafterchange(function(targetElement) {

    	 var id = jQuery(targetElement).attr('id');
    	 if(id){
    		 
    		 jQuery(targetElement).css("visibility", "hidden"); 
    	 }
    	 
       	 });
     
     intro.setOptions({
    	skipLabel : "fermer",
    	nextLabel: "suivant",
    	prevLabel: "précédent",
    	
       steps: [
         { 
           intro: "Hello world!"
         },
         {
        	 // 
           element: '#menu-item-9615',
           intro: "Mes stats : Ok, wasn't that fun?",
           position: 'right'
         },
         {
           element: '#menu-item-24308',
           intro: 'Profile : More features, more fun.',
           position: 'right'
         },
         {
           element: '#menu-item-24323',
           intro: "Mon réseau : Another step.",
           position: 'right'
         },
         {
           element: '#step5',
           intro: 'Get it, use it.'
         }
       ]
     });
     intro.start();
    
 }
       
    