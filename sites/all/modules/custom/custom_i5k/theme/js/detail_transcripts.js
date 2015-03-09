(function(jQuery) {
  Drupal.behaviors.custom_i5k_transcript = {
    attach: function (context, settings) {
	 
	
	//jQuery('.generic_loading_icon').hide();
	  
	  var $table = jQuery('table.tripal-chado_feature-contents-table');
	  //var $wrap = jQuery('<div/>').attr('id', 'loading_icon');
	  /*$table.append(
       jQuery('td.tripal-contents-table-td-data').append($wrap), jQuery('div#loading_icon').html('<img src="/sites/all/themes/i5k/images/loading.gif">')
     );*/
	 var $wrap = jQuery('<div/>').attr('id', 'popup-window');
	  $table.append(
       jQuery('td.tripal-contents-table-td-data').append($wrap)
	  ); 
	  
	 // jQuery('div#loading_icon').hide();
	  
    /*  jQuery('a#detail_transcript_view').on('click', function() {
	  //alert("hellooo test ");		
		var classNames = jQuery(this).attr('class').toString().split(' ');		  
		
        var feature_id = classNames[1].split('-');		
        feature_id = feature_id[1];		   
		
		if(!isNaN(feature_id)) {
	      jQuery.ajax({
            type: 'POST',
            url: '/detail_transcript/'+feature_id,            
            data: '',
			
            success: function(data) { 
			  //alert("in success "+data);
			  //jQuery('div#loading_icon').hide();
			  jQuery('.content1').html(data).show();			  
  		    },
            error: function(){ alert("ERROR"); },            
            cache:false
          });
		}
      });*/
	  
	  
	var parentDivs = jQuery('#multiAccordion div'),
    childDivs = jQuery('#multiAccordion h3').siblings('div');	
    jQuery("#multiAccordion h2").first().removeClass().addClass('accordionOpen');
    jQuery("#multiAccordion div").first().show();
    //$("#multiAccordion div div").first().show();
		
    jQuery('#multiAccordion h2').click(function () {
      parentDivs.slideUp();
      if (jQuery(this).next().is(':hidden')) {	   
	    jQuery("#multiAccordion h2").removeClass().addClass('accordionClose');
	    jQuery(this).removeClass().addClass('accordionOpen');
        jQuery(this).next().slideDown();
      } else {	
	    jQuery(this).removeClass().addClass('accordionClose');
        jQuery(this).next().slideUp();	  
      }
    });  
    jQuery('#multiAccordion h3').click(function () {
      childDivs.slideUp();
      if (jQuery(this).next().is(':hidden')) {
	    jQuery("#multiAccordion h3").removeClass().addClass('accordionClose');
	    jQuery(this).removeClass().addClass('accordionOpen');
        jQuery(this).next().slideDown();
      } else {
	    jQuery(this).removeClass().addClass('accordionClose');
        jQuery(this).next().slideUp();
      }
    });	

 
	
  }
};
})(jQuery);