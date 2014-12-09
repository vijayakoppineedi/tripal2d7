(function(jQuery) {
  Drupal.behaviors.custom_i5k_transcript = {
    attach: function (context, settings) {
	//jQuery('.generic_loading_icon').hide();
	  
	  var $table = jQuery('table.tripal-chado_feature-contents-table');
	  var $wrap = jQuery('<div/>').attr('id', 'loading_icon');
	  $table.append(
       jQuery('td.tripal-contents-table-td-data').append($wrap), jQuery('div#loading_icon').html('<img src="/sites/all/themes/i5k/images/loading1.gif">')
     );
	  jQuery('div#loading_icon').hide();
	  
      jQuery('a#detail_transcript_view').on('click', function() {
	  //alert("hellooo test ");		
		var classNames = jQuery(this).attr('class').toString().split(' ');		  
		
        var feature_id = classNames[1].split('-');		
        feature_id = feature_id[1];		   
		
		if(!isNaN(feature_id)) {
	      jQuery.ajax({
            type: 'POST',
            url: '/detail_transcript/'+feature_id,            
            data: '',
			beforeSend : function(){
			  //add div to tripal-contents-table-td-data  and show the data
              jQuery('div#loading_icon').show();
            },
            success: function(data) { 
			  //alert("in success "+data);
			  jQuery('div#loading_icon').hide();
			  jQuery('#detail_transcripts-tripal-data-pane').html(data).show();			  
  		    },
            error: function(){ alert("ERROR"); },            
            cache:false
          });
		}
      });
	  
    }
  };

})(jQuery);