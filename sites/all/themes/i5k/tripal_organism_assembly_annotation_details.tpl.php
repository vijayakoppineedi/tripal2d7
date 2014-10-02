<?php
$node = $variables['node'];
$organism = $variables['node']->organism;

// Search for the Assembly Methods and Annotation Methods links and get/extract the node ids from the links
foreach($node->field_resource_links['und'] as $key => $links) {
  $link = explode('/', $links['value']);  
  if($link[0] == 'Assembly Methods|') {
    $assembly_nid = $link[2];
	$assembly_node = node_load($assembly_nid);
    $data = $assembly_node->analysis;  
    $assembly_analysis = tripal_core_expand_chado_vars($data,'field','analysis.description');
  } else if($link[0] == 'Annotation Methods|') {
    $annotation_nid = $link[2];
	$annotation_node = node_load($annotation_nid);
    $data = $annotation_node->analysis;  
    $annotation_analysis = tripal_core_expand_chado_vars($data,'field','analysis.description');
	
  }
}
	
?>
<div id="assembly_annotation_details">   
  <div class="assembly_details">
    <h2>Assembly Information</h2>
    <table id="tripal_analysis-table-base" class="tripal_analysis-table tripal-table tripal-table-vert">
      <tr class="tripal_analysis-table-odd-row tripal-table-even-row">
        <th>Analysis Name</th>
        <td><?php print $assembly_analysis->name; ?></td>
      </tr>
      <tr class="tripal_analysis-table-odd-row tripal-table-odd-row">
        <th nowrap>Software</th>
        <td><?php 
          print $assembly_analysis->program; 
          if($assembly_analysis->programversion and $assembly_analysis->programversion != 'n/a'){
             print " (" . $assembly_analysis->programversion . ")"; 
          }
          if($assembly_analysis->algorithm){
             print ". " . $assembly_analysis->algorithm; 
          }
          ?>
        </td>
      </tr>
      <tr class="tripal_analysis-table-odd-row tripal-table-even-row">
        <th nowrap>Source</th>
        <td><?php 
          if($assembly_analysis->sourceuri){
             print "<a href=\"$assembly_analysis->sourceuri\">$assembly_analysis->sourcename</a>"; 
          } else {
             print $assembly_analysis->sourcename; 
          }
          if($assembly_analysis->sourceversion){
             print " (" . $assembly_analysis->sourceversion . ")"; 
          }
          ?>
          </td>
      </tr>
      <tr class="tripal_analysis-table-odd-row tripal-table-odd-row">
        <th nowrap>Date performed</th>
        <td><?php print preg_replace("/^(\d+-\d+-\d+) .*/","$1",$assembly_analysis->timeexecuted); ?></td>
      </tr>
      <tr class="tripal_analysis-table-odd-row tripal-table-even-row">
        <th nowrap>Materials & Methods</th>
        <td><?php print $assembly_analysis->description; ?></td>
      </tr>             	                                
    </table>   
  </div>
  
  <div class="statistics_details">
    <h2>Statistics</h2>
    <table id="tripal_organism-table-base" class="tripal_organism-table tripal-table tripal-table-vert">
    <tr class="tripal_organism-table-even-row tripal-table-even-row">
      <td colspan=2 class="tripal_organism-table-even-row" id="assembly-metrics-table"><b>Assembly Metrics</b></td>      
    </tr>
    <tr class="tripal_organism-table-odd-row tripal-table-odd-row">
      <th>Contig N50</th>
      <td><?php print $node->field_contig_n50['und'][0]['value']; ?></td>
    </tr>
    <tr class="tripal_organism-table-even-row tripal-table-even-row">
      <th>Scaffold N50</th>
      <td><?php print $node->field_scaffold_n50['und'][0]['value']; ?></td>
    </tr>
    <!--<tr class="tripal_organism-table-odd-row tripal-table-odd-row">
      <th>Number of gene predictions</th>
      <td><?php //print $node->field_number_of_genes['und'][0]['value']; ?></td>
    </tr>-->
    <tr class="tripal_organism-table-even-row tripal-table-even-row">
       <th>GC Content</th>
       <td><?php print $node->field_gc_content['und'][0]['value']; ?></td>
    </tr> 
    </table>
  </div> 
</div>


