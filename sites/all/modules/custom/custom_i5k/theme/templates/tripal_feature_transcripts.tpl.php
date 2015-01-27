<?php
//echo "<pre> tripalfeature: "; print_r($node); echo "</pre>";
$feature = $node->feature;
$_SESSION['node'] = $node;

$all_relationships = $feature->all_relationships;
//echo "<pre>"; print_r($all_relationships); echo "</pre>";

$object_rels = $all_relationships['object'];
$subject_rels = $all_relationships['subject'];

if (count($object_rels) > 0 or count($subject_rels) > 0) { ?>
  <div class="tripal_feature-data-block-desc tripal-data-block-desc"></div> <?php
  
  // first add in the subject relationships.  
  foreach ($subject_rels as $rel_type => $rels){
    foreach ($rels as $obj_type => $objects){ ?>

      <p>This <?php print $feature->type_id->name;?> is <?php print $rel_type ?> the following <b><?php print $obj_type ?></b> feature(s): <?php
       
      // the $headers array is an array of fields to use as the colum headers.
      // additional documentation can be found here
      // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
      $headers = array('Name', 'Type', 'Transcript Length', 'Protein Length', 'Detailed View');
      
      // the $rows array contains an array of rows where each row is an array
      // of values for each column of the table in that row.  Additional documentation
      // can be found here:
      // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
      $rows = array();
  
      foreach ($objects as $object){	
  
		// Below tripal function is used to get seqlen from featureloc relationship
		$pep_values = array('object_id' => $object->record->object_id->feature_id);
        $pep_options = array('return_array' => 1);		
		
        $pep_feature = chado_generate_var('feature_relationship', $pep_values, $pep_options);
		
	    $pep_seqlen = "NA";	
        foreach($pep_feature as $key => $pep_subject) {
          if($pep_subject->subject_id->type_id->cvterm_id == PEP_TYPE_ID) {
	        $pep_seqlen = $pep_subject->subject_id->seqlen;
	      }
        }				 
		  
		// Transcript length is the cdna length coz cdna residues showing for mRNA
		$cdna_seq_values = array('uniquename' => $object->record->object_id->uniquename, 'type_id' => CDNA_TYPE_ID);	
		$cdna_feature_seqlen = chado_generate_var('feature', $cdna_seq_values);		
	    $seqlength = 'NA';		
	    if(!empty($cdna_feature_seqlen->seqlen))
		  $seqlength = $cdna_feature_seqlen->seqlen;
		  
		$link = '';  
		if(isset($object->record->object_id->feature_id))
		  $link = "<div class='tripal_toc_list_item'><a id='detail_transcript_view' class='tripal_toc_list_item_link detail_transcripts_link-".$object->record->object_id->feature_id."' href='#'>link</a></div>";
		  
        $rows[] = array(          
          array('data' => $object->record->object_id->uniquename, 'width' => '30%'),          
          array('data' => $object->record->object_id->type_id->name, 'width' => '10%'),
		  $seqlength,
		  $pep_seqlen,
		  $link
        ); 
       } 
       // the $table array contains the headers and rows array as well as other
       // options for controlling the display of the table.  Additional
       // documentation can be found here:
       // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
       $table = array(
         'header' => $headers,
         'rows' => $rows,
         'attributes' => array(
           'id' => 'tripal_feature-table-relationship-object',
           'class' => 'tripal-data-table'
         ),
         'sticky' => FALSE,
         'caption' => '',
         'colgroups' => array(),
         'empty' => '',
       );
       
       // once we have our table array structure defined, we call Drupal's theme_table()
       // function to generate the table.
       print theme_table($table); ?>
       </p>
       <br><?php
     }
  }
  
  // second add in the object relationships.  
  foreach ($object_rels as $rel_type => $rels){
    foreach ($rels as $subject_type => $subjects){?>
      <p>The following features are <?php print $rel_type ?> this <?php print $feature->type_id->name;?>: <?php 
      // the $headers array is an array of fields to use as the colum headers.
      // additional documentation can be found here
      // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
      $headers = array('Name', 'Type', 'Transcript Length', 'Protein Length', 'Detailed View');
      
      // the $rows array contains an array of rows where each row is an array
      // of values for each column of the table in that row.  Additional documentation
      // can be found here:
      // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
      $rows = array();

      foreach ($subjects as $subject){	   
	    $nid = $subject->record->object_id->nid;
		// Below tripal function is used to get seqlen from featureloc relationship
		$pep_values = array('object_id' => $subject->record->subject_id->feature_id);
        $pep_options = array('return_array' => 1);		
		
        $pep_feature = chado_generate_var('feature_relationship', $pep_values, $pep_options);
		
		//echo "poepe ".PEP_TYPE_ID;
	    $pep_seqlen = "NA";	
        foreach($pep_feature as $key => $pep_subject) {
		  if($pep_subject->subject_id->type_id->cvterm_id == PEP_TYPE_ID) {
	        $pep_seqlen = $pep_subject->subject_id->seqlen;
	      }
        }				
		// Transcript length is the cdna length coz cdna residues showing for mRNA
		$cdna_seq_values = array('uniquename' => $subject->record->subject_id->uniquename, 'type_id' => CDNA_TYPE_ID);	
		$cdna_feature_seqlen = chado_generate_var('feature', $cdna_seq_values);		
	    $seqlength = 'NA';		
	    if(!empty($cdna_feature_seqlen->seqlen))
		  $seqlength = $cdna_feature_seqlen->seqlen;
		
		$link = "<div class='tripal_toc_list_item'><a id='detail_transcript_view' class='tripal_toc_list_item_link detail_transcripts_link-".$subject->record->subject_id->feature_id."-".$nid."' href='#'>link</a></div>";
		  
        $rows[] = array(
          array('data' =>$subject->record->subject_id->uniquename, 'width' => '30%'),          
          array('data' =>$subject->record->subject_id->type_id->name, 'width' => '10%'),
		  array('data' =>$seqlength, 'width' => '10%'),  
          array('data' => $pep_seqlen, 'width' => '10%'),
          $link		  
        ); 
       } 
       // the $table array contains the headers and rows array as well as other
       // options for controlling the display of the table.  Additional
       // documentation can be found here:
       // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
       $table = array(
         'header' => $headers,
         'rows' => $rows,
         'attributes' => array(
           'id' => 'tripal_feature-table-relationship-subject',
           'class' => 'tripal-data-table'
         ),
         'sticky' => FALSE,
         'caption' => '',
         'colgroups' => array(),
         'empty' => '',
       );
       
       // once we have our table array structure defined, we call Drupal's theme_table()
       // function to generate the table.
       print theme_table($table); ?>
       </p>
       <br><?php
     }
  }
}


