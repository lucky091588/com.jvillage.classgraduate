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
  $offset = 0;
  while ($continue) {
    $api_params = array(
      'return' => array("id"),
      "custom_{$graduating_class_custom_field_id}" => array('>=' => $now_year),
      'options' => array(
        'limit' => $api_get_limit,
        'offset' => $offset,
      ),
    );
    $result = civicrm_api3('Contact', 'get', $api_params);
    foreach ($result['values'] as $value) {
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
    $offset += $api_get_limit;
  }

  // Update contacts with a past year in $graduating_class_custom_field_id and
  // any value in $current_grade_custom_field_id.
  $continue = TRUE;
  $offset = 0;
  while ($continue) {
    $api_params = array(
      'return' => array("id"),
      "custom_{$graduating_class_custom_field_id}" => array('<' => $now_year),
      'options' => array(
        'limit' => $api_get_limit,
        'offset' => $offset,
      ),
    );
    $result = civicrm_api3('Contact', 'get', $api_params);
    foreach ($result['values'] as $value) {
      civicrm_api3('Classgraduate', 'Updatesingle', array(
        'id' => $value['id'],
        'graduating_class' => $value["custom_{$graduating_class_custom_field_id}"],
      ));
      $updated_count++;
    }
    if ($result['count'] < $api_get_limit) {
      $continue = FALSE;
    }
    $offset += $api_get_limit;
  }

  // Update contacts with a value in $current_grade_custom_field_id and no value
  // in $graduating_class_custom_field_id.
  // Unfortunately, as of 4.7.16 at least, API v3 doesn't support operators other
  // than '=' on custom fields. So we need to use straight SQL to find these.
  $getFieldResult = civicrm_api3('CustomField', 'get', array(
    'sequential' => 1,
    'custom_group_id' => "Grade_Class",
    'api.CustomGroup.get' => array(),
  ));

  $customGroupTableName = $getFieldResult['values'][0]['api.CustomGroup.get']['values'][0]['table_name'];

  foreach ($getFieldResult['values'] as $value) {
    if ($value['name'] == 'Graduating_Class') {
      $graduatingClassColumnName = $value['column_name'];
    }
    elseif ($value['name'] == 'Current_Grade') {
      $currentGradeColumnName = $value['column_name'];
    }
  }
  $query = "
    SELECT entity_id
    FROM $customGroupTableName
    WHERE
      coalesce($graduatingClassColumnName, '') = ''
      AND coalesce($currentGradeColumnName, '') != ''
  ";
  $dao = CRM_Core_DAO::executeQuery($query);
  while ($dao->fetch()) {
    $contact_id = $dao->entity_id;
    // Now update the grade based on an empty graduation year.
    civicrm_api3('Classgraduate', 'Updatesingle', array(
      'id' => $contact_id,
      'graduating_class' => '',
    ));
    $updated_count++;
  }
  return civicrm_api3_create_success("Updated records: $updated_count", $params, 'Classgraduate', 'Updateall');
}
