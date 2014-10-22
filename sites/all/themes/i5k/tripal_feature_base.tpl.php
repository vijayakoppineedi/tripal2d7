<?php
$feature  = $variables['node']->feature; 
//echo "<pre>"; print_r($feature);echo "</pre>";

// Location featureloc sequences
$featureloc_sequences = custom_i5k_feature_alignments($variables);

// gene_var variable is used to differentiate the gene and mRNA pages
$gene_var = 'gene';

 ?>

<div class="tripal_feature-data-block-desc tripal-data-block-desc"></div> <?php
 
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
// Description row
 $rows[] = array(
    array(
      'data' => 'Description',
      'header' => TRUE
    ),
    ''
  );

// Synonyms row
 $rows[] = array(
    array(
      'data' => 'Synonyms',
      'header' => TRUE
    ),
    ''
  );
	
// Location row
  if(count($featureloc_sequences) > 0){
    foreach($featureloc_sequences as $src => $attrs){
 
    $rows[] = array(
      array(
        'data' => 'Location',
        'header' => TRUE
      ),
     $attrs['location']
    );    
    }
  }

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
