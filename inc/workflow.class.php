<?php

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginWorkflowsWorkflow extends CommonDBTM {

   static $doHistory = true;
   static $rightname = 'ticket';

   static function getTypeName($nb = 0) {
      return _n('Tasks Workflow', 'Tasks Workflows', $nb, 'workflow');
   }

   /**
    * Define tabs to display on form page
    *
    * @param array $options
    * @return array containing the tabs name
    */
   function defineTabs($options = []) {

      $ong = [];
      $this->addDefaultFormTab($ong);
      $ong[$this->getType().'$tasktemplates'] = self::createTabEntry("task");
      $this->addStandardTab('Log', $ong, $options);

      return $ong;
   }

   static function displayTabContentForItem(\CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {

      $tasktemplates = new PluginWorkflowsWorkflow_tasktemplate;
      echo "<table style='width: 100%;vertical-align: top;'>";
      echo "<tr>";
      echo "<td style='width: 400px;vertical-align: top;'>";
      $tasktemplates->showForm('');
      echo "</td>";
      echo "<td align='center'>";
      $tasktemplates->showTasksWorkflow($item->getID());
      echo "</td>";
      echo "</tr>";
      echo "</table>";

      return true;
   }


   function showForm($ID, $options = []) {
      global $CFG_GLPI;

      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      echo "<tr class='nom'>";
      echo "<td>".__('Name')." :</td>";
      echo "<td align='center'>";
      Html::autocompletionTextField($this, 'name', ['value' => $this->fields['name']]);
      echo "</td>";
      echo "</tr>";

      $this->showFormButtons($options);
      $this->canCreateItem();
      return true;
   }

   /**
    * Get additional menu options and breadcrumb
    *
    * @global array $CFG_GLPI
    * @return array
    */
   static function getAdditionalMenuOptions() {
      global $CFG_GLPI;

      $options = [];

      $options['menu']['title'] = PluginWorkflowsWorkflow::getTypeName();
      $options['menu']['page']  = PluginWorkflowsWorkflow::getSearchURL(false);
      $options['workflow']= [
         'title'  =>       PluginWorkflowsWorkflow::getTypeName(),
         'page'   =>       PluginWorkflowsWorkflow::getSearchURL(false),
         'search' =>       PluginWorkflowsWorkflow::getSearchURL(false),
         'add'    =>       PluginWorkflowsWorkflow::getFormURL(false)
      ];

      return $options;
   }

   /**
    * Get the menu name
    *
    * @return string
    */
   static function getMenuName() {
      return PluginWorkflowsWorkflow::getTypeName();
   }
}
