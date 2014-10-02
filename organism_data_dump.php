<?php
/*
$db_con = pg_connect("host=localhost dbname=tripal_final user=tripal password=tri2pal");
$handle = fopen("organism.txt", "r");
if ($handle) {
  $i = 0; 
  while (($line = fgets($handle)) !== false) {
    if($i === 0) {
	  $i++;
	  continue;
	}
    // process the line read.
	print $line."<br>\n";	 
	$line_cols = explode(' ', $line);
	print_r($line_cols);
	$nid = $line_cols[0];
	$vid = $line_cols[1];
	$contig = $line_cols[2];
	$scaffold = $line_cols[3];
	$genenum =  $line_cols[4];
	$gc = $line_cols[5];
	$title = $line_cols[6];
    $result = pg_num_rows(pg_query($db_con, "SELECT * FROM node where nid=".$nid));	
	if($result == 1) {
	  pg_query("INSERT INTO field_data_field_contig_n50(entity_type, bundle, deleted, entity_id, revision_id, language, delta, field_contig_n50_value, field_contig_n50_format) values('node', 'chado_organism', 0,".$nid.",".$vid.",'und',0,'".$contig."','NULL')");
	  //pg_query("INSERT INTO field_data_field_gc_content(entity_type, bundle, deleted, entity_id, revision_id, language, delta, field_gc_content_value, field_gc_content_format) values('node', 'chado_organism', 0,".$nid.",".$vid.",'und',0,'".$gc."','NULL')");
	  //pg_query("INSERT INTO field_data_field_number_of_genes(entity_type, bundle, deleted, entity_id, revision_id, language, delta, field_number_of_genes_value, field_number_of_genes_format) values('node', 'chado_organism', 0,".$nid.",".$vid.",'und',0,'".$genenum."','NULL')");
	  //pg_query("INSERT INTO field_data_field_scaffold_n50(entity_type, bundle, deleted, entity_id, revision_id, language, delta, field_scaffold_n50_value, field_scaffold_n50_format) values('node', 'chado_organism', 0,".$nid.",".$vid.",'und',0,'".$scaffold."','NULL')");
	  //pg_query("INSERT INTO field_data_field_resource_titles(entity_type, bundle, deleted, entity_id, revision_id, language, delta, field_resource_titles_value, field_resource_titles_format) values('node', 'chado_organism', 0,".$nid.",".$vid.", 'und',0,'".$title."','NULL')");
	}
	$i++;
  }
  
} else {
    // error opening the file.
} 
fclose($handle);
*/
/******DUMP links table data **********************/
/*
 $db_con = pg_connect("host=localhost dbname=tripal_final user=tripal password=tri2pal");
  $handle = fopen("og_links.txt", "r");
  if ($handle) {
    $i = 0; 
  while (($line = fgets($handle)) !== false) {
    if($i === 0) {
	  $i++;
	  continue;
	}
    print "vijji ".$line."<br>\n";	 
	$line_cols = explode('@@', $line);
	print_r($line_cols);
    $nid = $line_cols[0];
	$vid = $line_cols[1];
	$delta = $line_cols[2];
	$links = $line_cols[3];
	$result = pg_num_rows(pg_query($db_con, "SELECT * FROM node where nid=".$nid));	
	if($result == 1) {
	  pg_query("INSERT INTO field_data_field_resource_links(entity_type, bundle, deleted, entity_id, revision_id, language, delta, field_resource_links_value, field_resource_links_format) VALUES('node', 'chado_organism', 0,".$nid.",".$vid.",'und',".$delta.",'".$links."','NULL')");
	}
	
	$i++;
  }
  
  } else {
    // error opening the file.
  } 
  fclose($handle);
*/
/*******END dumping links table data **************/

/************DUMP blocks table data **************/
 $db_con = pg_connect("host=localhost dbname=tripal_final user=tripal password=tri2pal");
  $handle = fopen("og_blocks.txt", "r");
    if ($handle) {
    $i = 0; 
  while (($line = fgets($handle)) !== false) {
    if($i === 0) {
	  $i++;
	  continue;
	}
    print "<br>LINE: ".$line."<br>\n";	 
	$line_cols = explode('@@', $line);
	print_r($line_cols);
    $nid = $line_cols[0];
	$vid = $line_cols[1];
	$delta = $line_cols[2];
	$links = $line_cols[3];
	$format = $line_cols[4];
	
	$result = pg_num_rows(pg_query($db_con, "SELECT * FROM node where nid=".$nid));	
	if($result == 1) {
	  pg_query("INSERT INTO field_data_field_resource_blocks(entity_type, bundle, deleted, entity_id, revision_id, language, delta, field_resource_blocks_value, field_resource_blocks_format) VALUES('node', 'chado_organism', 0,".$nid.",".$vid.",'und',".$delta.",'".$links."','".$format."')");
	}
	
	$i++;
	
  }	
  } else {
    // error opening the file.
  } 
  fclose($handle); 
/*********END dumping blocks table data **********/


?>