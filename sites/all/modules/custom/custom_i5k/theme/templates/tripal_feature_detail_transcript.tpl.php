<h1>Detailed Transcript View</h1>

<?php
//echo "<pre> variables::: "; print_r($features[0]); echo "</pre>";

//$feature = $variables['node']->feature;

//echo "<pre>"; print_r($features[0]); echo "</pre>";
if(!empty($features[0])) {
  $feature = $features[0];
  $options = array('return_array' => 1);
  $feature = chado_expand_var($feature, 'table', 'analysisfeature', $options);
  $analyses = $feature->subject_id->analysisfeature;
  
  // Dbxref, expand the feature object to include the records from the feature_dbxref table
  $options = array('return_array' => 1);
  $dx_feature = chado_expand_var($feature, 'table', 'feature_dbxref', $options);  
  $feature_dbxrefs = $dx_feature->subject_id->feature_dbxref;  
  //echo "<prE>"; print_r($feature_dbxrefs); echo "</pre>";
  $references = '';	
  if (count($feature_dbxrefs) > 0 ) {    
    foreach ($feature_dbxrefs as $feature_dbxref) {    
      if(!empty($feature_dbxref->dbxref_id->db_id->name) && ($feature_dbxref->dbxref_id->db_id->name != 'GFF_source')){
        // check to see if the reference 'GFF_source' is there.  This reference is
        // used to if the Chado Perl GFF loader was use
        $references .= $feature_dbxref->dbxref_id->db_id->name.":".$feature_dbxref->dbxref_id->accession."<Br>";
      }
    }
  }    

  // Analyses 
  if(count($analyses) == 2) {  
    $analysis_name = '';
      foreach($analyses as $analysis) {  
        $a_name = $analysis->analysis_id->name;
        if (property_exists($analysis->analysis_id, 'nid')) {
	      //$options['target'] = '_blank';
          $analysis_name .= l($a_name, "node/" . $analysis->analysis_id->nid)."<bR>";  
        }   
      }   
  } else {
    $a_name = $analyses->analysis_id->name;
    if (property_exists($analyses->analysis_id, 'nid')) {
	  //$options['target'] = '_blank';
      $analysis_name .= l($a_name, "node/" . $analyses->analysis_id->nid)."<bR>";  
    }   

  }

  //User comment
  $featureprop_id = $feature->subject_id->feature_id;  
  $select = array('feature_id' => $featureprop_id, 'type_id' => 85);
  $columns = array('value', 'feature_id');
  $featureprop = chado_select_record('featureprop', $columns, $select);
  $user_comment = '';
  if(!empty($featureprop[0]->value))
    $user_comment = 'Note: '.$featureprop[0]->value;

  // the $headers array is an array of fields to use as the colum headers.
  $headers = array();

  // the $rows array contains an array of rows where each row is an array
  // of values for each column of the table in that row.  Additional documentation
  // can be found here:
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7 
  $rows = array();
  
  if(isset($features[0])) {
    $feature = $features[0];  
 
    // Name row
    $rows[] = array(
      array(
        'data' => 'Name',
        'header' => TRUE,
        'width' => '20%',
      ),
      $feature->subject_id->name
    );
    // Unique Name row
    $rows[] = array(
      array(
        'data' => 'ID',
        'header' => TRUE
      ),
      $feature->subject_id->uniquename
    );

    // Type row
    $rows[] = array(
      array(
        'data' => 'Type',
        'header' => TRUE
      ),
      $feature->subject_id->type_id->name
    );

    // Dbxref row
    $rows[] = array(
      array(
        'data' => 'Dbxref',
        'header' => TRUE
      ),
      $references
    );

    // Source row
    $rows[] = array(
      array(
        'data' => 'Source',
        'header' => TRUE
      ),
      'Need more info'
    );

    // Analysis row
    $rows[] = array(
      array(
        'data' => 'Analysis',
        'header' => TRUE
      ),
      $analysis_name
    );
  
    // User Submitted info row
    $rows[] = array(
      array(
        'data' => 'User Submitted info',
        'header' => TRUE
      ),
      $user_comment
    );
   
  } // end if condition

  $table = array(
    'header' => $headers,
    'rows' => $rows,
    'attributes' => array(
      'id' => 'tripal_feature-detail-transcript',
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
}

//Bottom table
if(isset($data)) {
  $headers = array('Name', 'ID', 'Type', 'Reference Sequence ID', 'start', 'End', 'Strand', 'View sequence');
  $rows = array();
  //Gene and mRNA data
  if(isset($features['data'])) {
    foreach($features['data'] as $key => $parent_data) {
      $rows[] = array(
        array('data' =>$parent_data['name'], 'width' => '15%'),          
        array('data' =>$parent_data['uniquename'], 'width' => '25%'),
		array('data' =>$parent_data['type'], 'width' => '10%'),  
		array('data' =>$parent_data['refsequence_id'], 'width' => '10%'),  
		array('data' =>$parent_data['fmin'], 'width' => '10%'),  
		array('data' =>$parent_data['fmax'], 'width' => '10%'),  
		array('data' =>$parent_data['strand'], 'width' => '10%'),  
		array('data' =>$parent_data['view_sequence'], 'width' => '10%')                      
      ); 
	} 
  }
  
  foreach($data as $key => $data) {
 	
	
       $strand = '';  
       if($data['strand'] == 1)
	     $strand = '[+]';
	   else if($data['strand'] == -1)	 
	     $strand = '[-]';
	$seq_id = $sub_features[$key][0]->srcfeature_id->name;
		 
       $rows[] = array(
          array('data' =>$data['name'], 'width' => '15%'),          
          array('data' =>$data['uniquename'], 'width' => '25%'),
		  array('data' =>$data['typename'], 'width' => '10%'),  
		  array('data' =>$seq_id, 'width' => '10%'),  
		  array('data' =>$data['fmin'], 'width' => '10%'),  
		  array('data' =>$data['fmax'], 'width' => '10%'),  
		  array('data' =>$strand, 'width' => '10%'),  
		  array('data' =>$data['view_sequence'], 'width' => '10%')                      
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
      'id' => 'tripal_feature-detail-transcript',
      'class' => 'tripal-data-table'
    ),
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
 ?>
<div id='popup-window'> 
</div>
<?php 
  // once we have our table array structure defined, we call Drupal's theme_table()
  // function to generate the table.
  print theme_table($table);
}	   
?>
