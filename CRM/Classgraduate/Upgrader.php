<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Classgraduate_Upgrader extends CRM_Classgraduate_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed.
   *
  public function install() {
    $this->executeSqlFile('sql/myinstall.sql');
  }

  /**
   * Example: Work with entities usually not available during the install step.
   *
   * This method can be used for any post-install tasks. For example, if a step
   * of your installation depends on accessing an entity that is itself
   * created during the installation (e.g., a setting or a managed entity), do
   * so here to avoid order of operation problems.
   *
  public function postInstall() {
    $customFieldId = civicrm_api3('CustomField', 'getvalue', array(
      'return' => array("id"),
      'name' => "customFieldCreatedViaManagedHook",
    ));
    civicrm_api3('Setting', 'create', array(
      'myWeirdFieldSetting' => array('id' => $customFieldId, 'weirdness' => 1),
    ));
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled.
   */
  public function onUninstall() {
    $getGroupResult = civicrm_api3('CustomGroup', 'get', array(
      'sequential' => 1,
      'name' => "grade_class",
    ));
    $gid = CRM_Utils_Array::value('id', $getGroupResult);
    if (!$gid) {
      return;
    }

    $getFieldResult = civicrm_api3('CustomField', 'get', array(
      'sequential' => 1,
      'custom_group_id' => "grade_class",
      'options' => array('limit' => 0),
    ));
    $fieldValues = CRM_Utils_Array::value('values', $getFieldResult, array());

    foreach ($fieldValues as $fieldValue) {
      $deleteFieldResult = civicrm_api3('CustomField', 'delete', array(
        'id' => $fieldValue['id'],
      ));
    }

    $deleteGroupResult = civicrm_api3('CustomGroup', 'delete', array(
      'id' => $gid,
    ));

  }

  /**
   * Example: Run a simple query when a module is enabled.
   *
  public function enable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 1 WHERE bar = "whiz"');
  }

  /**
   * Example: Run a simple query when a module is disabled.
   *
  public function disable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
  }

  /**
   * Remove old integer custom field "Current Grade" and create new string
   * custom field "Current Grade".
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_4700() {
    $this->ctx->log->info('Applying update 4700');
    $getCurrentGradeFieldResult = civicrm_api3('CustomField', 'get', array(
      'sequential' => 1,
      'custom_group_id' => "Grade_Class",
      'label' => "Current Grade",
    ));
    $oldFid = CRM_Utils_Array::value('id', $getCurrentGradeFieldResult);
    if (empty($oldFid)) {
      return TRUE;
    }

    // Calculate a weight for the new "current grade" field, greater than
    // "graduating class" field weight.
    $getGraduatingClassFieldResult = civicrm_api3('CustomField', 'get', array(
      'sequential' => 1,
      'custom_group_id' => "Grade_Class",
      'label' => "Graduating Class",
    ));
    $graduatingClassFieldValues = CRM_Utils_Array::value(0, $getGraduatingClassFieldResult['values']);
    $newWeight = (CRM_Utils_Array::value('weight', $graduatingClassFieldValues, 0) + 10);

    // Delete the old field.
    $deleteFieldResult = civicrm_api3('CustomField', 'delete', array(
      'id' => $oldFid,
    ));

    // Define parameters and create the new field.
    $oldParams = CRM_Utils_Array::value(0, $getCurrentGradeFieldResult['values']);
    $paramKeysToCopy = array(
      'custom_group_id',
      'name',
      'label',
      'html_type',
      'is_required',
      'is_searchable',
      'is_search_range',
      'help_post',
      'is_active',
      'is_view',
      'text_length',
      'note_columns',
      'note_rows',
    );
    $newParams = array_intersect_key($oldParams, array_flip($paramKeysToCopy));
    $newParams['data_type'] = 'String';
    $newParams['weight'] = $newWeight;
    $createFieldResult = civicrm_api3('CustomField', 'create', $newParams);

    // Call Classgraduate.Updateall API to populate new field.
    civicrm_api3('Classgraduate', 'Updateall', array());

    return TRUE;
  }


  /**
   * Example: Run an external SQL script.
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4201() {
    $this->ctx->log->info('Applying update 4201');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_4201.sql');
    return TRUE;
  } // */


  /**
   * Example: Run a slow upgrade process by breaking it up into smaller chunk.
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4202() {
    $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

    $this->addTask(ts('Process first step'), 'processPart1', $arg1, $arg2);
    $this->addTask(ts('Process second step'), 'processPart2', $arg3, $arg4);
    $this->addTask(ts('Process second step'), 'processPart3', $arg5);
    return TRUE;
  }
  public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
  public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
  public function processPart3($arg5) { sleep(10); return TRUE; }
  // */


  /**
   * Example: Run an upgrade with a query that touches many (potentially
   * millions) of records by breaking it up into smaller chunks.
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4203() {
    $this->ctx->log->info('Planning update 4203'); // PEAR Log interface

    $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
    $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
    for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
      $endId = $startId + self::BATCH_SIZE - 1;
      $title = ts('Upgrade Batch (%1 => %2)', array(
        1 => $startId,
        2 => $endId,
      ));
      $sql = '
        UPDATE civicrm_contribution SET foobar = whiz(wonky()+wanker)
        WHERE id BETWEEN %1 and %2
      ';
      $params = array(
        1 => array($startId, 'Integer'),
        2 => array($endId, 'Integer'),
      );
      $this->addTask($title, 'executeSql', $sql, $params);
    }
    return TRUE;
  } // */

}
