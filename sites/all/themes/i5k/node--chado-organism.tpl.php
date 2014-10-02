<?php

if($teaser) {
  print render($content);
}
else {
  $node_type = $node->type; ?>
  
  <script type="text/javascript">
    // We do not use Drupal Behaviors because we do not want this
    // code to be executed on AJAX callbacks. This code only needs to 
    // be executed once the page is ready.
    jQuery(document).ready(function($){	    	  
	  //VIJAYA - Hiding the cck field tabs from left side bar
	  //VIJAYA - Adding base_url to the links Annotation and Assembly Methods, Outside sources
      if (!window.location.origin)
         window.location.origin = window.location.protocol+"//"+window.location.host;
	 
	  $(".tripal_toc_list_item").each(function(i,v){       	  
		var item_title = $(v).text();		
	    /*if(item_title == 'Scaffold N50' || item_title == 'Organism Image' || item_title == 'Feature Browser' || item_title == 'Feature Summary' || item_title == 'Comments' || item_title == 'GC Content' || item_title == 'Contig N50' || item_title == 'Number Of Genes') { 		
		  $(v).hide();		  
		} */		        
		if(item_title == 'Outside Sources' || item_title == 'Annotation Methods' || item_title == 'Assembly Methods' || item_title == 'Annotations') {
		  //REg Exp: <a href="//node/1243232" target=_blank>title</a> 
		  // First spliting '=' then in [1] we got "//node/1243232" target
		  // split with space then in [0] we got "//node/1243232"
		  // replace double quotes with '' then we got //node/1243232
		  // finally replace // with '' 
		  //append with base url means window.location.origin
		  var href = window.location.origin+"/"+v.innerHTML.split("=")[1].split(" ")[0].replace(/"/g, '').replace(/\/\//g,'');
		  //alert("href "+$('a', this).attr('href', href)); 
		  $('a', this).attr('href', href);	  
		}		
  	  });
	  
      // Hide all but the first data pane 
      $(".tripal-data-pane").hide().filter(":nth-child(1)").show();
  
      // When a title in the table of contents is clicked, then 
      // show the corresponding item in the details box 
      $(".tripal_toc_list_item_link").click(function(){
        var tab = $(this).attr('id');
        var id = tab + "-tripal-data-pane";	
		
	    //VIJAYA - Added Assembly, Statistics and Annotation  details below the organism overview. Those details show only on overview page, hide on other tabs.
		if(tab == 'base') {		  
		  $("#tripal_organism-assembly-annotation-details").show();
		} else {
		  $("#tripal_organism-assembly-annotation-details").hide();
		}	
		
        $(".tripal-data-pane").hide().filter("#"+ id).fadeIn('fast');
        return false;
      });
  
      // If a ?pane= is specified in the URL then we want to show the
      // requested content pane. For previous version of Tripal,
      // ?block=, was used.  We support it here for backwards
      // compatibility
      var pane;
      pane = window.location.href.match(/[\?|\&]pane=(.+?)[\&|\#]/)
      if (pane == null) {
        pane = window.location.href.match(/[\?|\&]pane=(.+)/)
      }
      // if we don't have a pane then try the old style ?block=
      if (pane == null) {
        pane = window.location.href.match(/[\?|\&]block=(.+?)[\&|\#]/)
        if (pane == null) {
          pane = window.location.href.match(/[\?|\&]block=(.+)/)
        }
      }
      if(pane != null){
        $(".tripal-data-pane").hide().filter("#" + pane[1] + "-tripal-data-pane").show();
      }
      // Remove the 'active' class from the links section, as it doesn't
      // make sense for this layout
      $("a.active").removeClass('active');
    });
  </script>
  
  <div id="tripal_<?php print $node_type?>_contents" class="tripal-contents">
    <table id ="tripal-<?php print $node_type?>-contents-table" class="tripal-contents-table">
      <tr class="tripal-contents-table-tr">
        <td nowrap class="tripal-contents-table-td tripal-contents-table-td-toc"  align="left"><?php
        
          // print the table of contents. It's found in the content array 
          if (array_key_exists('tripal_toc', $content)) {
            print $content['tripal_toc']['#markup'];
          
            // we may want to add the links portion of the contents to the sidebar
            //print render($content['links']);
            
            // remove the table of contents and links so thye doent show up in the 
            // data section when the rest of the $content array is rendered
            unset($content['tripal_toc']);
            unset($content['links']); 
          } ?>

        </td>
        <td class="tripal-contents-table-td-data" align="left" width="100%"> <?php
         
          // print the rendered content 
          print render($content); ?>
        </td>
      </tr>
    </table>
  </div>
    <div id="tripal_organism-assembly-annotation-details"> 
  <!-- tripal assembly annotation details Theme -->
   <?php print theme('tripal_organism_assembly_annotation_details', array('node' => $node)); ?>   
</div>
  <?php 
}


