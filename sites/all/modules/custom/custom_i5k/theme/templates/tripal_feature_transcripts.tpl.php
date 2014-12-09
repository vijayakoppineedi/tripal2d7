<?php
//echo "<pre>"; print_r($node); echo "</pre>";
$feature = $node->feature;

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
      $headers = array('Name', 'Type', 'Transcript Length', 'Transcript Protein', 'Detail View');
      
      // the $rows array contains an array of rows where each row is an array
      // of values for each column of the table in that row.  Additional documentation
      // can be found here:
      // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
      $rows = array();
      
      foreach ($objects as $object){	  
	    $seqlength = '-';
	    if(!empty($object->record->object_id->seqlen))
		  $seqlength = $object->record->object_id->seqlen;
		$link = '';  
		if(isset($subject->record->subject_id->feature_id))
		  $link = "<div class='tripal_toc_list_item'><a id='detail_transcript_view' class='tripal_toc_list_item_link detail_transcripts_link-".$subject->record->subject_id->feature_id."' href='#'>link</a></div>";
		  
        $rows[] = array(          
          array('data' => $object->record->object_id->uniquename, 'width' => '30%'),          
          array('data' => $object->record->object_id->type_id->name, 'width' => '10%'),
		  $seqlength,
		  'Need more info',
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
      $headers = array('Name', 'Type', 'Transcript Length', 'Transcript Protein', 'Detail View');
      
      // the $rows array contains an array of rows where each row is an array
      // of values for each column of the table in that row.  Additional documentation
      // can be found here:
      // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
      $rows = array();
      
      foreach ($subjects as $subject){ 	  
	    $nid = $subject->record->object_id->nid;
		
	    $seqlength = '-';
	    if(!empty($subject->record->subject_id->seqlen))
		  $seqlength = $subject->record->subject_id->seqlen;
		  //?pane=detail_transcripts&fid=".$subject->record->subject_id->feature_id."
		  $link = "<div class='tripal_toc_list_item'><a id='detail_transcript_view' class='tripal_toc_list_item_link detail_transcripts_link-".$subject->record->subject_id->feature_id."-".$nid."' href='#'>link</a></div>";
		  
        $rows[] = array(
          array('data' =>$subject->record->subject_id->uniquename, 'width' => '30%'),          
          array('data' =>$subject->record->subject_id->type_id->name, 'width' => '10%'),
		  array('data' =>$seqlength, 'width' => '10%'),  
          'Need more info',
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
