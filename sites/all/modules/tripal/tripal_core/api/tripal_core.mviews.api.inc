<?php
/**
 * @file
 * Provides an application programming interface (API) to manage materialized views in Chado.
 */

/**
 * @defgroup tripal_mviews_api Tripal Materalized Views API
 * @ingroup tripal_core_api
 * @{
 * Provides an application programming interface (API) to manage materialized views in Chado.
 * The Perl-based chado comes with an interface for managing materialzed views.  This
 * API provides an alternative Drupal-based method.
 * @}
 */

/**
 * Add a materialized view to the chado database to help speed data access. This
 * function supports the older style where postgres column specifications
 * are provided using the $mv_table, $mv_specs and $indexed variables. It also
 * supports the newer preferred method where the materialized view is described
 * using the Drupal Schema API array.
 *
 * @param $name
 *   The name of the materialized view.
 * @param $modulename
 *   The name of the module submitting the materialized view (e.g. 'tripal_library')
 * @param $mv_table
 *   The name of the table to add to chado. This is the table that can be queried.
 * @param $mv_specs
 *   The table definition
 * @param $indexed
 *   The columns that are to be indexed
 * @param $query
 *   The SQL query that loads the materialized view with data
 * @param $special_index
 *   currently not used
 * @param $comment
 *   A string containing a description of the materialized view
 *
 * @ingroup tripal_mviews_api
 *
function tripal_add_legacy_mview($name, $modulename, $mv_table, $mv_specs, $indexed,
  $query, $special_index, $comment = NULL) {

  // Create a new record
  $record = new stdClass();
  $record->name = $name;
  $record->modulename = $modulename;
  $record->mv_table = $mv_table;
  $record->mv_specs = $mv_specs;
  $record->indexed = $indexed;
  $record->query = $query;
  $record->special_index = $special_index;
  $record->comment = $comment;

  // add the record to the tripal_mviews table and if successful
  // create the new materialized view in the chado schema
  if (drupal_write_record('tripal_mviews', $record)) {

    // drop the table from chado if it exists
    if (chado_table_exists($mv_table)) {
      $sql = "DROP TABLE {$mv_table}";
      chado_query($sql);
    }

    // now construct the indexes
    $index = '';
    if ($indexed) {
      // add to the array of values
      $vals = preg_split("/[\n,]+/", $indexed);
      $index = '';
      foreach ($vals as $field) {
        $field = trim($field);
        $index .= "CREATE INDEX idx_${mv_table}_${field} ON $mv_table ($field);";
      }
    }
  }

  // add the table to the database
  $sql = "CREATE TABLE {$mv_table} ($mv_specs); $index";
  $previous_db = chado_set_active('chado');  // use chado database
  $results = db_query($sql);
  chado_set_active($previous_db);  // now use drupal database
  if ($results) {
    drupal_set_message(t("Materialized view '%name' created", array('%name' => $name)));
  }
  else {
    drupal_set_message(t("Failed to create the materialized view table: '%mv_table'", array('%mv_table' => $mv_table)), 'error');
  }
}
*/

/**
 * Add a materialized view to the chado database to help speed data access. This
 * function supports the older style where postgres column specifications
 * are provided using the $mv_table, $mv_specs and $indexed variables. It also
 * supports the newer preferred method where the materialized view is described
 * using the Drupal Schema API array.
 *
 * @param $name
 *   The name of the materialized view.
 * @param $modulename
 *   The name of the module submitting the materialized view (e.g. 'tripal_library')
 * @param $mv_schema
 *   If using the newer Schema API array to define the materialized view then
 *   this variable should contain the array or a string representation of the
 *   array.
 * @param $query
 *   The SQL query that loads the materialized view with data
 * @param $comment
 *   A string containing a description of the materialized view
 *
 * @ingroup tripal_mviews_api
 */
function tripal_add_mview($name, $modulename, $mv_schema, $query, $comment = NULL) {

  if (!array_key_exists('table', $mv_schema)) {
     tripal_report_error('tripal_core', TRIPAL_ERROR,
       'Must have a table name when creating an mview.', array());
     return NULL;
  }
  
  $mv_table = $mv_schema['table'];

  // see if the mv_table name already exsists
  $mview_id = db_query(
    'SELECT mview_id FROM {tripal_mviews} WHERE name = :name',
    array(':name' => $name))->fetchField();

  if(!$mview_id) {
    $transaction = db_transaction();
    try {
      // Create a new record
      $record = new stdClass();
      $record->name = $name;
      $record->modulename = $modulename;
      $record->mv_table = $mv_table;
      $record->query = $query;
      $record->comment = $comment;
      
      // convert the schema into a string format
      $str_schema = var_export($mv_schema, TRUE);
      $str_schema = preg_replace('/=>\s+\n\s+array/', '=> array', $str_schema);
      $record->mv_schema = $str_schema;
  
      // add the record to the tripal_mviews table and if successful
      // create the new materialized view in the chado schema
      if (drupal_write_record('tripal_mviews', $record)) {
  
        // drop the table from chado if it exists
        if (chado_table_exists($mv_table)) {
          $sql = 'DROP TABLE {' . $mv_table . '}';
          chado_query($sql);
        }
        // create the table
        chado_create_custom_table($mv_table, $mv_schema, 0, $record->mview_id);
      }
    }
    catch (Exception $e) {
      $transaction->rollback();
      watchdog_exception('tripal_core', $e);
      $error = _drupal_decode_exception($e);
      drupal_set_message(t("Could not create the materialized view %table_name: %message.",
      array('%table_name' => $name, '%message' => $error['!message'])), 'error');
      return FALSE;
    }
    drupal_set_message(t("Materialized view '%name' created", array('%name' => $name)));
    return TRUE;
  }
  else {
    tripal_report_error('tripal_cv', TRIPAL_WARNING,
      "Materialized view, %vname, already exists. Cannot create.",
      array('%vname' => $name));
    drupal_set_message(t("Materialized view, $name, already exists. Cannot create.", array('%name' => $name)));
    return FALSE;
  }
}

/**
 * Edits a materialized view to the chado database to help speed data access. This
 * function supports the older style where postgres column specifications
 * are provided using the $mv_table, $mv_specs and $indexed variables. It also
 * supports the newer preferred method where the materialized view is described
 * using the Drupal Schema API array.
 *
 * @param $mview_id
 *   The mview_id of the materialized view to edit
 * @param $name
 *   The name of the materialized view.
 * @param $modulename
 *   The name of the module submitting the materialized view (e.g. 'tripal_library')
 * @param $mv_table
 *   The name of the table to add to chado. This is the table that can be queried.
 * @param $mv_specs
 *   The table definition
 * @param $indexed
 *   The columns that are to be indexed
 * @param $query
 *   The SQL query that loads the materialized view with data
 * @param $special_index
 *   currently not used
 * @param $comment
 *   A string containing a description of the materialized view
 * @param $mv_schema
 *   If using the newer Schema API array to define the materialized view then
 *   this variable should contain the array.
 *
 * @ingroup tripal_mviews_api
 */
function tripal_edit_mview($mview_id, $name, $modulename, $mv_table, $mv_specs,
  $indexed, $query, $special_index, $comment = NULL, $mv_schema = NULL) {

  $transaction = db_transaction();
  try {
    // get the table name from the schema array
    $schema_arr = array();
    if ($mv_schema) {
      // get the schema from the mv_specs and use it to add the custom table
      eval("\$schema_arr = $mv_schema;");
      $mv_table = $schema_arr['table'];
    }
  
    $record = new stdClass();
    $record->mview_id    = $mview_id;
    $record->name        = $name;
    $record->modulename  = $modulename;
    $record->query       = $query;
    $record->last_update = 0;
    $record->status      = '';
    $record->comment     = $comment;
    $record->mv_schema   = $mv_schema;
    $record->mv_table    = $mv_table;
    
    // update the record to the tripal_mviews table
    drupal_write_record('tripal_mviews', $record, 'mview_id');
  
    // get the view before we update and check to see if the table structure has
    // changed. If so, then we want to drop and recreate the table. If not, then
    // just save the updated SQL.
    $create_table = 1;
    $sql = "SELECT * FROM {tripal_mviews} WHERE mview_id = :mview_id";
    $results = db_query($sql, array(':mview_id' => $mview_id));
    $mview = $results->fetchObject();
    if ($mview->mv_schema == $mv_schema and $mview->mv_table == $mv_table) {
      chado_create_custom_table($mv_table, $schema_arr, 0, $record->mview_id);
        drupal_set_message(t("Materialized view '%name' created", array('%name' => $name)));
    }
    else {
      $message = "View '%name' updated.  All records remain. ";
      if ($query != $mview->query) {
        $message .= "Please repopulate the view to use the updated query.";
      }
      drupal_set_message(t($message, array('%name' => $name)));
    }
      
    // construct the indexes SQL if needed
    $index = '';
    if ($indexed) {
      // add to the array of values
      $vals = preg_split("/[\n,]+/", $indexed);
      $index = '';
      foreach ($vals as $field) {
        $field = trim($field);
        $index .= "CREATE INDEX idx_${mv_table}_${field} ON $mv_table ($field);";
      }
    }

  }
  catch (Exception $e) {
    $transaction->rollback();
    watchdog_exception('tripal_core', $e);
    $error = _drupal_decode_exception($e);
    drupal_set_message(t("Could not update materialized view '%table_name': %message.",
    array('%table_name' => $mv_table, '%message' => $error['!message'])), 'error');
    return FALSE;
  }
}

/**
 * Retrieve the materialized view_id given the name
 *
 * @param $view_name
 *   The name of the materialized view
 *
 * @return
 *   The unique identifier for the given view
 *
 * @ingroup tripal_mviews_api
 */
function tripal_get_mview_id($view_name) {
  if (db_table_exists('tripal_mviews')) {
    $sql = "SELECT * FROM {tripal_mviews} WHERE name = :name";
    $results = db_query($sql, array(':name' => $view_name));
    $mview = $results->fetchObject();
    if ($mview) {
      return $mview->mview_id;
    }
  }

  return FALSE;
}

/**
 * Populates the specified Materialized View
 *
 * @param $mview_id
 *   The unique ID of the materialized view for the action to be performed on
 *
 * @ingroup tripal_mviews_api
 */
function tripal_refresh_mview($mview_id) {
  global $user;

  if (!$mview_id) {
    return '';
  }

  // get this mview details
  $sql = "SELECT * FROM {tripal_mviews} WHERE mview_id = :mview_id";
  $results = db_query($sql, array(':mview_id' => $mview_id));
  $mview = $results->fetchObject();

  // add a job to populate the mview
  $args = array("$mview_id");
  tripal_add_job("Populate materialized view '$mview->name'", 'tripal_core',
     'tripal_populate_mview', $args, $user->uid);

}

/**
 * Does the specified action for the specified Materialized View
 *
 * @param $op
 *   The action to be taken. One of update or delete
 * @param $mview_id
 *   The unique ID of the materialized view for the action to be performed on
 *
 * @ingroup tripal_mviews_api
 */
function tripal_delete_mview($mview_id) {
  global $user;

  if (!$mview_id) {
    return '';
  }

  // get this mview details
  $sql = "SELECT * FROM {tripal_mviews} WHERE mview_id = :mview_id";
  $results = db_query($sql, array(':mview_id' => $mview_id));
  $mview = $results->fetchObject();

  // if op is to delete then do so
  // remove the mview from the tripal_mviews table
  $sql = "DELETE FROM {tripal_mviews} WHERE mview_id = $mview_id";
  db_query($sql);

  // does the table already exist?
  $mview_exists = db_table_exists('chado.' . $mview->mv_table);

  // drop the table from chado if it exists
  if ($mview_exists) {
    $sql = "DROP TABLE {" . $mview->mv_table . "}";
    $success = chado_query($sql);
    if ($success) {
      drupal_set_message(t("Materialized view, %name, deleted.", array('%name' => $mview->name)));
    }
    else {
      drupal_set_message(t("Problem deleting materialized view, %name.", array('%name' => $mview->name)), 'error');
    }
  }
}

/**
 * Update a Materialized View
 *
 * @param $mview_id
 *   The unique identifier for the materialized view to be updated
 *
 * @return
 *   True if successful, FALSE otherwise
 *
 * @ingroup tripal_mviews_api
 */
function tripal_populate_mview($mview_id) {
  $sql = "SELECT * FROM {tripal_mviews} WHERE mview_id = :mview_id ";
  $results = db_query($sql, array(':mview_id' => $mview_id));
  $mview = $results->fetchObject();
  if ($mview) {
    // execute the query inside a transaction so that it doesn't destroy existing data
    // that may leave parts of the site unfunctional
    $transaction = db_transaction();
    try {
      $previous_db = chado_set_active('chado');  // use chado database
      $success = db_query("DELETE FROM {" . $mview->mv_table . "}");
      $success = db_query("INSERT INTO {" . $mview->mv_table . "} ($mview->query)");
      chado_set_active($previous_db);  // now use drupal database
      // if success get the number of results and update the table record
      if ($success) {
        $sql = "SELECT count(*) as cnt FROM {" . $mview->mv_table . "}";
        $results = chado_query($sql);
        $count = $results->fetchObject();
        $record = new stdClass();
        $record->mview_id = $mview_id;
        $record->last_update = REQUEST_TIME;
        $record->status = "Populated with " . number_format($count->cnt) . " rows";
        drupal_write_record('tripal_mviews', $record, 'mview_id');
      }
      // if not success then throw an error
      else {
        throw new Exception("ERROR populating the materialized view ". $mview->mv_table . ". See Drupal's recent log entries for details.");
      }
    }
    catch (Exception $e) {
      $transaction->rollback();
      // print and save the error message
      $record = new stdClass();
      $record->mview_id = $mview_id;
      $record->status = "ERROR populating $mview->mv_table. See Drupal's recent log entries for details.\n";
      drupal_write_record('tripal_mviews', $record, 'mview_id');
      watchdog_exception('tripal_mviews', $e);
      return FALSE;
    }
    print "Done.\n";
    return TRUE;
  }
}
