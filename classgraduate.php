<?php

require_once 'classgraduate.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function classgraduate_civicrm_config(&$config) {
  _classgraduate_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function classgraduate_civicrm_xmlMenu(&$files) {
  _classgraduate_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function classgraduate_civicrm_install() {
  _classgraduate_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function classgraduate_civicrm_postInstall() {
  _classgraduate_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function classgraduate_civicrm_uninstall() {
  _classgraduate_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function classgraduate_civicrm_enable() {
  _classgraduate_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function classgraduate_civicrm_disable() {
  _classgraduate_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function classgraduate_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _classgraduate_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function classgraduate_civicrm_managed(&$entities) {
  _classgraduate_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function classgraduate_civicrm_caseTypes(&$caseTypes) {
  _classgraduate_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function classgraduate_civicrm_angularModules(&$angularModules) {
  _classgraduate_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function classgraduate_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _classgraduate_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function classgraduate_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function classgraduate_civicrm_navigationMenu(&$menu) {
  _classgraduate_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'com.jvillage.classgraduate')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _classgraduate_civix_navigationMenu($menu);
} // */

/**
 * Get value for a given variable specific to this extension.
 */
function _classgraduate_var_get($name) {
  // TODO: If/when budget allows, we can refactor this to support settings
  // configurable in the UI. For now, this function uses some simple logic
  // to determine the right values itself.
  static $vars = array();
  if (!isset($vars[$name])) {
    switch ($name) {
      case 'classgraduate_graduating_class_custom_field_id':
        $result = civicrm_api3('CustomField', 'getsingle', array(
          'custom_group_id' => "Grade_Class",
          'name' => "Graduating_Class",
        ));
        $vars[$name] = $result['id'];
        break;

      case 'classgraduate_current_grade_custom_field_id':
        $result = civicrm_api3('CustomField', 'getsingle', array(
          'custom_group_id' => "Grade_Class",
          'name' => "Current_Grade",
        ));
        $vars[$name] = $result['id'];
        break;

      case 'classgraduate_gradeclass_custom_group_id':
        $result = civicrm_api3('CustomGroup', 'getsingle', array(
          'name' => "Grade_Class",
        ));
        $vars[$name] = $result['id'];
        break;

      case 'classgraduate_graduation_cutoff_date':
        $vars[$name] = '06-15';
        break;

    }
  }
  return $vars[$name];
}

function classgraduate_civicrm_custom($op, $groupID, $entityID, &$params) {
  if (
    ($op == 'edit' || $op == 'create')
    && $groupID == _classgraduate_var_get('classgraduate_gradeclass_custom_group_id')
  ) {
    foreach ($params as $param) {
      if ($param['custom_field_id'] == _classgraduate_var_get('classgraduate_graduating_class_custom_field_id')) {
        civicrm_api3('Classgraduate', 'Updatesingle', array(
          'id' => $entityID,
          'graduating_class' => $param['value'],
        ));
      }
    }
  }
}

function _classgraduate_calculate_grade($graduating_class) {
  if (empty($graduating_class)) {
    return '';
  }
  static $grades_per_graduating_class = array();
  if (!isset($grades_per_graduating_class[$graduating_class])) {
    $graduation_cutoff_date = _classgraduate_var_get('classgraduate_graduation_cutoff_date');

    // Math examples are noted in comments.
    // e.g., $graduating_class = 2020;
    $now_year = date('Y'); // e.g., 2018
    $year_difference = ($graduating_class - $now_year); // e.g., 2
    if (date('z') >= date('z', strtotime("{$now_year}-{$graduation_cutoff_date}"))) {
      // If today's day-of-year is greater than the day-of-year of the cutoff date.
      $grade = (12 - $year_difference + 1); // e.g., 11
    }
    else {
      $grade = (12 - $year_difference); // e.g., 10
    }
    if ($grade > 12) {
      // If grade is over 12, they've graduated already and should have no grade.
      $grade = '';
    }
    elseif ($grade == 0) {
      $grade = 'Kindergarten';
    }
    elseif ($grade == -1) {
      $grade = 'Pre-K';
    }
    elseif ($grade < -1) {
      $grade = '';
    }
    $grades_per_graduating_class[$graduating_class] = $grade;
  }
  return $grades_per_graduating_class[$graduating_class];
}
