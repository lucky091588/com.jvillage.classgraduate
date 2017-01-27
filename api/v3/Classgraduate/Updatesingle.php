<?php

/**
 * Classgraduate.Update API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_classgraduate_Updatesingle_spec(&$spec) {
  $spec['id'] = array(
    'api.required' => 1,
    'name' => 'id',
    'title' => 'Contact ID',
  );
  $spec['graduating_class'] = array(
    'name' => 'graduating_class',
    'title' => 'Graduating Class',
    'type' => 1,
   );
}

/**
 * Classgraduate.Updatesingle API
 *
 * Update Current Grade field for a single contact.
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_classgraduate_Updatesingle($params) {
  $current_grade_custom_field_id = _classgraduate_var_get('classgraduate_current_grade_custom_field_id');
  if (!isset($params['graduating_class'])) {
    $params['graduating_class'] = _civicrm_api3_classgraduate_get_class($params['id']);
  }
  $api_params = array(
    'id' => $params['id'],
    "custom_{$current_grade_custom_field_id}" => _classgraduate_calculate_grade($params['graduating_class']),
  );
  $results = civicrm_api3('Contact', 'create', $api_params);
  return civicrm_api3_create_success(1, $params, 'Classgraduate', 'Updatesingle');
}

function _civicrm_api3_classgraduate_get_class($id) {
  $graduating_class_custom_field_id = _classgraduate_var_get('classgraduate_graduating_class_custom_field_id');
  $api_params = array(
    'id' => $id,
    'return' => array("custom_{$graduating_class_custom_field_id}"),
  );

  try {
    $results = civicrm_api3('Contact', 'getsingle', $api_params);
  } catch (Exception $e) {
    throw new API_Exception("No contact found for id='{$id}'.");
  }
  return $results["custom_{$graduating_class_custom_field_id}"];
}

