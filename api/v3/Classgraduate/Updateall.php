<?php

/**
 * Classgraduate.Updateall API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_classgraduate_Updateall_spec(&$spec) {
}

/**
 * Classgraduate.Updateall API
 *
 * Update Current Grade field for all contacts.
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_classgraduate_Updateall($params) {
  // Get custom field IDs
  $graduating_class_custom_field_id = _classgraduate_var_get('classgraduate_graduating_class_custom_field_id');
  $current_grade_custom_field_id = _classgraduate_var_get('classgraduate_current_grade_custom_field_id');

  $now_year = date('Y');
  $updated_count = 0;

  // Update contacts with a current or future year in $graduating_class_custom_field_id.
  // Limit API result counts to a fixed maximum, and loop through repeated api
  // calls until all are processed.
  $api_get_limit = 200;
  $continue = TRUE;
  while ($continue) {
    $api_params = array(
      'return' => array("id"),
      "custom_{$graduating_class_custom_field_id}" => array('>=' => $now_year),
      'options' => array('limit' => $api_get_limit),
    );
    $result = civicrm_api3('Contact', 'get', $api_params);
    foreach($result['values'] as $value) {
      // Call Classgraduate.updatesingle for each found contact.
      civicrm_api3('Classgraduate', 'Updatesingle', array(
        'id' => $value['id'],
        'graduating_class' => $value["custom_{$graduating_class_custom_field_id}"],
      ));
      $updated_count++;
    }
    if ($result['count'] < $api_get_limit) {
      $continue = FALSE;
    }
  }

  // Update contacts with a past year in $graduating_class_custom_field_id and
  // any value in $current_grade_custom_field_id.
  $continue = TRUE;
  while ($continue) {
    $api_params = array(
      'return' => array("id"),
      "custom_{$graduating_class_custom_field_id}" => array('<' => $now_year),
      'options' => array('limit' => $api_get_limit),
    );
    $result = civicrm_api3('Contact', 'get', $api_params);
    foreach($result['values'] as $value) {
      civicrm_api3('Classgraduate', 'Updatesingle', array(
        'id' => $value['id'],
        'graduating_class' => $value["custom_{$graduating_class_custom_field_id}"],
      ));
      $updated_count++;
    }
    if ($result['count'] < $api_get_limit) {
      $continue = FALSE;
    }
  }
  

  // Update contacts with a value in $current_grade_custom_field_id and no value
  // in $graduating_class_custom_field_id.
  $continue = TRUE;
  while ($continue) {
    $api_params = array(
      'return' => array("id"),
      "custom_{$current_grade_custom_field_id}" => array('IS NOT NULL' => 1),
      "custom_{$graduating_class_custom_field_id}" => array('IS NULL' => 1),
      'options' => array('limit' => $api_get_limit),
    );
    $result = civicrm_api3('Contact', 'get', $api_params);
    foreach($result['values'] as $value) {
      civicrm_api3('Classgraduate', 'Updatesingle', array(
        'id' => $value['id'],
        'graduating_class' => $value["custom_{$graduating_class_custom_field_id}"],
      ));
      $updated_count++;
    }
    if ($result['count'] < $api_get_limit) {
      $continue = FALSE;
    }
  }

  return civicrm_api3_create_success("Updated records: $updated_count", $params, 'Classgraduate', 'Updateall');
}
