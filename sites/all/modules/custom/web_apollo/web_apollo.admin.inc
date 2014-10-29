<?php

/**
 * @file
 * Admin page callbacks for the Contact module.
 */
 
 
 /**
 * Form builder; Build Organisms and Recipients to the settings form.
 */
function web_apollo_admin($form, &$form_state) {
  $web_apollo_recipients = variable_get('web_apollo_recipients'); 
  $web_apollo_organism = variable_get('web_apollo_organism');
   
  $form['web_apollo_organism'] = array('#type' => 'textarea',
    '#title' => t('Organism(s)'),
    '#default_value' => $web_apollo_organism,
    '#description' => t("The possible values this field can contain. Enter one value per line, in the format key|label."),
    '#required' => TRUE,
  );
  
  $form['web_apollo_recipients'] = array('#type' => 'textarea',
    '#title' => t('Recipients'),
    '#default_value' => $web_apollo_recipients,
    '#description' => t("Example: 'webmaster@example.com' or 'sales@example.com,support@example.com'. To specify multiple recipients, separate each e-mail address with a comma."),
    '#required' => TRUE,
  );
  
  return system_settings_form($form);
}

/**
 * Validate the web apollo recipients.
 */
function web_apollo_admin_validate($form, &$form_state) {
  if (empty($form_state['values']['web_apollo_recipients'])) {
    form_set_error('recipients', t('You must enter one or more recipients.'));
  }
  else {
    $recipients = explode(',', $form_state['values']['web_apollo_recipients']);
    foreach ($recipients as $recipient) {
      if (!valid_email_address(trim($recipient))) {
        form_set_error('recipients', t('%recipient is an invalid e-mail address.', array('%recipient' => $recipient)));
      }
    }
  }
}


/**
 * List the web apollo registered users with pagination and 
 * with Approve/Reject options
 **/
function webapollo_register_users() {
  $output =  "";
  
  if(isset($_GET['sort']) && isset($_GET['order'])){
    // Sort it Ascending or Descending?
    if($_GET['sort'] == 'asc')
      $sort = 'ASC';
    else
      $sort = 'DESC';

    // Which column will be sorted
    switch($_GET['order']){
      case 'Name':
        $order = 'name';
        break;
      case 'Email':
        $order = 'email';
        break;
      case 'Organism':
        $order = 'organisms';
        break;
      case 'Status':
        $order = 'status';
        break;
      default:
        $order = 'created';
    }
  }
  else{
    // Default sort
    $sort = 'DESC';
    $order = 'created';
  }

  // Select table
  $query = db_select("webapollo_users", "w");
  // Select fields
  $query->fields("w", array("uid", "name", "email", "organisms", "status", "created"));
    // Set order by
  $query->orderBy($order, $sort);
  // For pagination
  $query = $query->extend('TableSort')->extend('PagerDefault')->limit(25);  
  
  // Execute query
  $result = $query->execute();
  
  // Prepare table header
  // Prepare table header
  $header = array(
			array("data" => t('Name'),"field" => "name"),
            array("data" => t('Email'),"field" => "email"),
			array("data" => t('Organism'),"field" => "Organisms"),
			array("data" => t('Status'),"field" => "status"),
			array("data" => t('Created'),"field" => "created", 'sort' => 'desc'),
			array()			
          );
  
    // Now build the form array
  $web_apollo_organism = variable_get('web_apollo_organism');
  $organism_data = get_webapollo_organisms($web_apollo_organism); 
  
  $rows = array();  
  $i = 0;

// Looping for filling the table rows  
  while($data = $result->fetchObject()){
    // Fill the table rows
    $rows[$i][] = $data->name;
	$rows[$i][] = $data->email;
	$rows[$i][] = $organism_data[$data->organisms];
	$status = '';
	if($data->status == 1) $status = 'Approved';
 	else if($data->status == 0) $status = 'Rejected';
    else if($data->status == 2) $status = 'Pending'; 	 
	$rows[$i][] = $status; 	 
	$rows[$i][] = date('M d Y h:i:s A', $data->created);
	$rows[$i][] = l('Edit', 'webapollo/user/'.$data->uid.'/edit');
	$i++;
  }
  	 
  
// Output of table with the paging
  $output = theme_table(
    array(
      "header" => $header,
      "rows" => $rows,
      "attributes" => array(),
      "sticky" => true, // Table header will be sticky
      "caption" => "",
      "colgroups" => array(),
      "empty" => t("Table has no row!") // The message to be displayed if table is empty
    )
  ).theme("pager");

  
  return $output;
}


/**
 * @hook_form()
 * Registered user form with options Approve/Reject  
 */ 
function webapollo_user_approval($form, &$form_state, $uid) { 
 $header = array(); 
 
  // sql here
  $data = db_select('webapollo_users','w')
          ->fields('w',array('uid', 'name', 'pass', 'email', 'organisms', 'status'))
		  ->condition('w.uid',$uid,'=')
          ->execute()->fetchObject();
	

  // Now build the form array
  $form = array();

  $form['web_apollo_table'] = array(
    '#tree' => TRUE, // Need this for easy submit processing
    '#header' => $header, // We trick Drupal to pass our $header to theme here
    '#theme' => 'webapollo_user_approval'
  );
  $form['web_apollo_table']['name'] = array(	   
	'#type' => 'value',
	'#title' => t('Name'),
	'#value' => $data->name,
  );
  $form['web_apollo_table']['email'] = array(	   
	'#type' => 'value',
	'#value' => $data->email,
  );
 
  $web_apollo_organism = variable_get('web_apollo_organism');
  $organism_data = get_webapollo_organisms($web_apollo_organism);         
  $organism_value = $organism_data[$data->organisms];

  $form['web_apollo_table']['organism_key'] = array(	   
	'#type' => 'value',
	'#value' => $data->organisms,
   );
	 
  $form['web_apollo_table']['organism'] = array(	   
	'#type' => 'value',
	'#value' => $organism_value,
  );

  $status = '';

  if($data->status == 1) $status = 'Approved';
  else if($data->status == 0) $status = 'Pending'; 
  else $status = 'Rejected';
	 
  $form['web_apollo_table']['status'] = array(
    '#type' => 'radios',  
    '#default_value' => $data->status,
    '#options' => array(t('Reject'), t('Approve')),
  );
	
  $form['web_apollo_table']['uid'] = array(	   
	'#type' => 'value',
	'#value' => $data->uid,
  );
	
  $form['web_apollo_table']['pass'] = array(	   
	'#type' => 'value',
	'#value' => $data->pass,
   );
	 
  // Submit function
  $form['web_apollo_table']['submit'] = array(
    '#type' => 'submit',
    '#name' => 'submit',
    '#value' => t('Save'),
  );
 
  return $form; 
}

/**
 * @hook_form_submit()
 * Runs the add_user.pl and set_track_permissions.pl to create user accounts and permissions for respective organisms.
 * @sends an email to user regarding login credentails to the web apollo site.
 */ 
function webapollo_user_approval_submit($form, &$form_state) {
  $values = $form_state['values']['web_apollo_table'];
  $status = $form_state['values']['web_apollo_table']['status'];
  $uid = $form_state['values']['web_apollo_table']['uid'];  
  $to = $values['email'];  
  $from = FROM_ADDRESS;
  
  $status_res = db_select('webapollo_users','w')
          ->fields('w',array('status'))
		  ->condition('w.uid',$uid,'=')
          ->execute()->fetchField();
		  

	
  if( ($status_res != $status) && ($status == 1)) {  
    /***************web apollo automated login credentials*******************************/
    $web_apollo_organism = variable_get('web_apollo_organism');
    if(!empty($web_apollo_organism)) {
      $organism_data = get_webapollo_organisms($web_apollo_organism);
    }
	  
    $organism_nameconv = array('anogla', 'athros', 'cimlec'); //Hard coded the values for db_users_admin
    $organism_short_name = searchkey($organism_data, $values['organism_key']); 	
	
    if(in_array($organism_short_name, $organism_nameconv))
      $db_name = $organism_short_name."_users_admin";
    else 
      $db_name = $organism_short_name."_users"; 	
	
    $db_user = "web_apollo_users_admin";
    $db_pass = "web_apollo_users_admin";
 
    $user = $values['email'];  
    $pass = $values['pass'];

    $user_pass_values['organism'] = $organism_data[$values['organism']];
    $user_pass_values['user'] = $user;
    $user_pass_values['pass'] = $pass;
  
   // $add_user = "perl add_user.pl -D ".$db_name." -U ".$db_user." -P ".$db_pass." -u ".$user." -p ".$pass;
   //ON PRODUCTION uncomment below line and comment the very next line for host apollo.nal.usda.gov otherwise it takes localhost
   // $add_user = "python adduser.py -dbuser ".$db_user." -dbname ".$db_name." -user ".$user." -pwd ".$pass." -host apollo.nal.usda.gov";
    $exec_user = exec("python adduser.py -dbuser ".$db_user." -dbname ".$db_name." -user ".$user." -pwd ".$pass);   
    //$exec_user = exec("python adduser.py -dbuser ".$db_user." -dbname ".$db_name." -user ".$user." -pwd ".$pass." -host apollo.nal.usda.gov");   

    //Only on prod we have data for species seqids.txt files, but not on dev. change the seqids.txt file while testing on gmod-dev server 
    /*($organism_short_name == 'diacit')
      $seqids = '/app/data/other_species/'.$organism_short_name.'/seqids/seqids.txt';
    else
      $seqids = '/app/data/BCM_i5k_pilot/'.$organism_short_name.'/seqids/seqids.txt';

    $set_permissions =  shell_exec("perl set_track_permissions.pl -D ".$db_name." -U ".$db_user." -P ".$db_pass." -u ".$user." -t ".$seqids." -rw");
  */

     //DEVELOPMENT 
    // $set_permissions =  shell_exec("perl set_track_permissions.pl -D ".$db_name." -U ".$db_user." -P ".$db_pass." -u ".$user." -t trackseq.txt -rw"); 

    //Send email to the user about the login credentials to the web apollo site
    $user_email_sent = drupal_mail('web_apollo', 'webapollo_user_approve_email', $to, language_default(), $values, $from, TRUE);

    if(!$user_email_sent) {	  
      drupal_set_message(t('Failed to send the email due to some technical problems'));	  
    } else {
	  db_update('webapollo_users') // Table name no longer needs {}
		->fields(array('status' => $status))
		->condition('uid', $uid, '=')
		->execute();
     drupal_set_message(t('Successfully an email was sent to '.$values['email'].' regarding the status approved.'));
    }
	
    //Displaying status message on the site
    $status = ($status == 1)?'Approved': 'Rejected';  
    drupal_set_message(t($form_state['values']['web_apollo_table']['name']."'s status was changed to ".$status));    
  } // Status approved condition ends here
  else if($status == 0) {
       db_update('webapollo_users') // Table name no longer needs {}
		->fields(array('status' => $status))
		->condition('uid', $uid, '=')
		->execute();
	  drupal_set_message(t($values['name'].' has been rejected by  the admin due to some reasons.'));
  }   
  /**********************************************/ 
  
  $form_state['redirect'] = 'admin/structure/webapollo';
  return;
}

/**
 * Search key by value from an array
 **/
 function searchkey($organism_data, $needle)
{
   foreach($organism_data as $key => $data)
   {  
      if(strcmp($key, $needle) == 0) {
	   return $key;
	  }
   }
   return false;
}
