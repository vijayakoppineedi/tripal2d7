/**
 * @file
 * jQuery code.
 * 
 */

// Setting up popup.
// 0 means disabled; 1 means enabled.
var popupStatus = 0;

/**
 * Loading popup with jQuery.
 */
function popup_message_load_popup() {
  // Loads popup only if it is disabled.
  if (popupStatus == 0) {
    jQuery("#popup-message-background").css( {
      "opacity": "0.7"
    });
    jQuery("#popup-message-background").fadeIn("slow");
    jQuery("#popup-message-window").fadeIn("slow");
    popupStatus = 1;
  }
}

/**
 * Disabling popup with jQuery.
 */
function popup_message_disable_popup() {
  // Disables popup only if it is enabled.
  if (popupStatus == 1) {
    jQuery("#popup-message-background").fadeOut("slow");
    jQuery("#popup-message-window").fadeOut("slow");
    popupStatus = 0;
  }
}

/**
 * Centering popup.
 */
function popup_message_center_popup(width, height) {
  // Request data for centering.
  var windowWidth = document.documentElement.clientWidth;
  var windowHeight = document.documentElement.clientHeight;

  var popupWidth = 0
  if (typeof width == "undefined") {
    popupWidth = jQuery("#popup-message-window").width();
  }
  else {
    popupWidth = width;
  }
  var popupHeight = 0
  if (typeof width == "undefined") {
    popupHeight = jQuery("#popup-message-window").height();
  }
  else {
    popupHeight = height;
  }

  // Centering.
  jQuery("#popup-message-window").css( {
    "position": "absolute",
    "width" : popupWidth + "px",
    "height" : popupHeight + "px",
    "top": windowHeight / 2 - popupHeight / 2,
    "left": windowWidth / 2 - popupWidth / 2
  });

  // Only need force for IE6.
  jQuery("#popup-message-background").css( {
    "height": windowHeight
  });
}

/**
 * Display popup message.
 */
function popup_message_display_popup(popup_message_title, popup_message_body, width, height) {
  //Here popup_message_title is nothing but feature_id will change to feature_id once my functionality works perfe
  //alert("hello "+popup_message_title);

  /*jQuery('#popup-window').append('<div id="popup-message-window"><div>hello test </div><a id="copy-button" href="#" class="">Copy..</a><a id="popup-message-close">X</a>\n\
   <h1 class="popup-message-title">' + popup_message_title + '</h1><div id="popup-message-content">' + popup_message_body
    + '</div></div><div id="popup-message-background"></div>');*/
  var fid = popup_message_title;
  var type = popup_message_body;	
  var popup_title = fid+"-"+type;	
	
  jQuery.ajax({
    type: 'POST',
    url: '/zclip/'+popup_title,        
    data: '',		
    success: function(data) { 	 
      jQuery('#popup-window').append(data);	
      // Loading popup.
	  
      popup_message_center_popup(width, height);
      popup_message_load_popup();
  
      // Closing popup.
      // Click the x event!
      jQuery("#popup-message-close").click(function() {
        jQuery('#popup-window').text('');
        popup_message_disable_popup();
	    jQuery("#navigation a:visited").css({"background":"none"}); 
	    jQuery("#navigation").css({"display":"block"});
      });
      // Click out event!
      jQuery("#popup-message-background").click(function() {
        jQuery('#popup-window').text('');
        popup_message_disable_popup();
	    jQuery("#navigation a:visited").css({"background":"none"});
	    jQuery("#navigation").css({"display":"block"});
      });
      // Press Escape event!
      jQuery(document).keypress(function(e) {
      if (e.keyCode == 27 && popupStatus == 1) {
	    jQuery('#popup-window').text('');
        popup_message_disable_popup();	  
	    jQuery("#navigation a:visited").css({"background":"none"});
        jQuery("#navigation").css({"display":"block"});
	  }
    });
  
   //jQuery('#detail_transcripts-tripal-data-pane').html(data).show();			  
    },
    error: function(){ alert("ERROR"); },            
    cache:false
  });
}
