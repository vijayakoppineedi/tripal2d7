<?php
/*
  $db_con = pg_connect("host=localhost dbname=tripal user=tripal password=tri2pal");

  $result = pg_query($db_con, "SELECT * from content_type_chado_organism");
  
  $myfile = fopen("organism.txt", "w") or die("unable to open file");

  $txt_header = "nid vid contig scaffold genenum gccontent titles\n";
  fwrite($myfile, $txt_header);

  while($row = pg_fetch_assoc($result)) {
    $titles_res = pg_fetch_assoc(pg_query($db_con, "select * from content_field_resource_titles where nid=".$row['nid']));
    $title = !empty($titles_res['field_resource_titles_value'])?$titles_res['field_resource_titles_value']:'NULL'; 
    $txt_row = $row['nid']." ".$row['vid']." ".$row['field_contig_value']." ".$row['field_scaffold_value']." ".$row['field_number_of_genes_value']." ".$row['field_gc_content_value']." ".$title."\n";
    echo $txt_row."<br>";

    fwrite($myfile, $txt_row);   
  }  
 fclose($myfile); 
*/
/******************Links DATA ********************************/
/*
 $db_con = pg_connect("host=localhost dbname=tripal user=tripal password=tri2pal");

  $result = pg_query($db_con, "SELECT * from content_type_chado_organism");

  $myfile = fopen("og_links.txt", "w") or die("unable to open file");

  $txt_header = "nid vid delta links\n";
  fwrite($myfile, $txt_header);

  while($row = pg_fetch_assoc($result)) {

    $links =  pg_fetch_all(pg_query($db_con, "select * from content_field_resource_links where nid=".$row['nid']));
    $txt_row = ''; 
    for($i=0; $i<count($links); $i++) {
      $txt_row .=  $links[$i]['nid']."@@".$links[$i]['vid']."@@".$links[$i]['delta']."@@".$links[$i]['field_resource_links_value']."\n";
    }
  
    echo "<br>".$txt_row;

    fwrite($myfile, $txt_row);
  }
 fclose($myfile);
  */
/*****************END Links **********************************/

/********************** Blocks DATA *****************************/
/*
  $db_con = pg_connect("host=localhost dbname=tripal user=tripal password=tri2pal");

  $result = pg_query($db_con, "SELECT * from content_type_chado_organism");

  $myfile = fopen("og_blocks.txt", "w") or die("unable to open file");

  $txt_header = "nid vid delta blocks format\n";
  fwrite($myfile, $txt_header);

  while($row = pg_fetch_assoc($result)) {
    $blocks = pg_fetch_all(pg_query($db_con, "select * from content_field_resource_blocks where nid=".$row['nid']));
     //print_r($blocks);
    $txt_row = ''; $blocks_text = ''; $format = '';
    for($i=0; $i<count($blocks); $i++) {
     // echo "blocks ".$blocks[$i]['field_resource_blocks_value']."<br>";
      $format = (!empty($blocks[$i]['field_resource_blocks_format']) && ($blocks[$i]['field_resource_blocks_format'] != '')) ?$blocks[$i]['field_resource_blocks_format']:'NULL';
      $blocks_text = (!empty($blocks[$i]['field_resource_blocks_value']) && ($blocks[$i]['field_resource_blocks_value'] != ''))? $blocks[$i]['field_resource_blocks_value']:'NULL';

      $blocks_text = str_replace("\n",'', $blocks_text);
      $blocks_text = str_replace("\r",'', $blocks_text);
      $blocks_text = str_replace("\r\n",'', $blocks_text);
      $blocks_text = str_replace("'", "\'", $blocks_text);
      $txt_row .=  $blocks[$i]['nid']."@@".$blocks[$i]['vid']."@@".$blocks[$i]['delta']."@@".$blocks_text."@@".$format."\n";
    }
    echo "<br>".$txt_row;
    fwrite($myfile, $txt_row);
  }
 fclose($myfile);
*/

/*****************END BLOCKS ************************************/

?>
