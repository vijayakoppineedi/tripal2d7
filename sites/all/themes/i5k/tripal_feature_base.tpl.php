<?php
$feature  = $variables['node']->feature; 
//echo "<pre>"; print_r($feature);echo "</pre>";

//VIJAYA - To retrieve synonym  data
$options = array('return_array' => 1);
$synonym = chado_expand_var($feature, 'table', 'feature_synonym', $options);

/* Building feature Synonym  */
$feature_synonym = "";
if(isset($synonym->feature_synonym) && !empty($synonym->feature_synonym)) {
  foreach($synonym->feature_synonym as $synonym_obj) {  
    $feature_synonym .= $synonym_obj->synonym_id->name."<br>"; 
  }
}

// Location featureloc sequences
$featureloc_sequences = custom_i5k_feature_alignments($variables);
$location = '';
if(count($featureloc_sequences) > 0){
  foreach($featureloc_sequences as $src => $attrs){
    $location = $attrs['location'];
  }
}  
//VIJAYA - To display the comments (Note) of all mRNA's and transcripts
$relationship = tripal_feature_get_feature_relationships($feature);
$transcript_count = '';
if(isset($relationship['object']['part of']['mRNA'])) {
  $transcript_count = "This gene has ".count($relationship['object']['part of']['mRNA']);
  $transcript_count .= (count($relationship['object']['part of']['mRNA']) > 1)?" mRNA transcripts":" mRNA transcript";
}

$comment = "";
if(isset($relationship['object']['part of']['mRNA']) && !empty($relationship['object']['part of']['mRNA'])) {
  foreach($relationship['object']['part of']['mRNA'] as $key => $child_featurelocs) {
    $featurelocs_feature_id = $child_featurelocs->child_featurelocs[0]->feature_id;  
	
    $select = array('feature_id' => $featurelocs_feature_id, 'type_id' => 85);
    $columns = array('value', 'feature_id');
    $featureprop = chado_select_record('featureprop', $columns, $select);
    if(!empty($featureprop[0]->value))
      $comment .= $child_featurelocs->record->subject_id->name." - Note: ".$featureprop[0]->value."<br>";
  }
}

//VIJAYA gene_var variable is used to differentiate the gene and mRNA pages
$gene_var = 'gene';

 ?>


<div class="tripal_feature-data-block-desc tripal-data-block-desc" style='color:red;'></div>
 <?php
 
// the $headers array is an array of fields to use as the colum headers. 
// additional documentation can be found here 
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
// This table for the analysis has a vertical header (down the first column)
// so we do not provide headers here, but specify them in the $rows array below.
$headers = array();

// the $rows array contains an array of rows where each row is an array
// of values for each column of the table in that row.  Additional documentation
// can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7 
$rows = array();

// Name row
$rows[] = array(
  array(
    'data' => 'Gene ID',
    'header' => TRUE,
    'width' => '20%',
  ),
  $feature->name
);
// Unique Name row
$rows[] = array(
  array(
    'data' => 'Gene Name',
    'header' => TRUE
  ),
  $feature->uniquename
);
if($feature->type_id->name == $gene_var) {
// Synonyms row
 $rows[] = array(
    array(
      'data' => 'Synonyms',
      'header' => TRUE
    ),
    $feature_synonym
  );
	
// Location row
  $rows[] = array(
    array(
      'data' => 'Location',
      'header' => TRUE
    ),
   $location
  );    

// Transcript row
$rows[] = array(
   array(
     'data' => 'Transcripts',
     'header' => TRUE
   ),
   $transcript_count
);
  
//Comment row
$rows[] = array(
   array(
     'data' => 'User Comments',
     'header' => TRUE
   ),
   $comment
);
  
}  //END of if condition for gene page
else { 
// Type row
$rows[] = array(
  array(
    'data' => 'Type',
    'header' => TRUE
  ),
  $feature->type_id->name
);

 // Organism row
 $organism = $feature->organism_id->genus ." " . $feature->organism_id->species ." (" . $feature->organism_id->common_name .")";
 if (property_exists($feature->organism_id, 'nid')) {
   $organism = l("<i>" . $feature->organism_id->genus . " " . $feature->organism_id->species . "</i> (" . $feature->organism_id->common_name .")", "node/".$feature->organism_id->nid, array('html' => TRUE));
 } 
 $rows[] = array(
   array(
     'data' => 'Organism',
     'header' => TRUE,
   ),
   $organism
 );
} // gene if condition ends

// Seqlen row
if($feature->seqlen > 0) {
  $rows[] = array(
    array(
      'data' => 'Sequence length',
      'header' => TRUE,
    ),
    $feature->seqlen
  );
}
// allow site admins to see the feature ID
/* VIJAYA - As per the new design 2014 commenting Feature ID row
if (user_access('view ids')) { 
  // Feature ID
  $rows[] = array(
    array(
      'data' => 'Feature ID**',
      'header' => TRUE,
      'class' => 'tripal-site-admin-only-table-row',
    ),
    array(
      'data' => $feature->feature_id,
      'class' => 'tripal-site-admin-only-table-row',
    ),
  );
}
*/
// Is Obsolete Row
if($feature->is_obsolete == TRUE){
  $rows[] = array(
    array(
      'data' => '<div class="tripal_feature-obsolete">This feature is obsolete</div>',
      'colspan' => 2
    ),
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
    'id' => 'tripal_feature-table-base',
    'class' => 'tripal-data-table'
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);

// once we have our table array structure defined, we call Drupal's theme_table()
// function to generate the table.
print theme_table($table); 

 $iframe_src = "https://apollo.nal.usda.gov/"; //"http://gmod-dev.nal.usda.gov:8080/";
     
  // nal abbreviation = first 3 letters of genus + first 3 letters of species
  $nal_abbreviation = strtolower(substr($feature->organism_id->genus, 0, 3) .  substr($feature->organism_id->species, 0, 3));
     
  $current_model = $nal_abbreviation."_current_models";

  $iframe_location = !empty($location)?$location:'';	 

  $iframe_src = $iframe_src . $nal_abbreviation . "/jbrowse/?loc=" . $iframe_location . "&tracks=DNA%2CAnnotations%2C" . $current_model."&hightlight";
  
?>    
 


