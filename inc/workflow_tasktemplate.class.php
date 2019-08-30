<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginWorkflowsWorkflow_tasktemplate extends CommonTreeDropdown {

   // From CommonDBTM
   public $dohistory          = true;
   public $can_be_translated  = true;
   static $rightname          = 'computer';

   static function getTypeName($nb = 0) {
      return _n('Tasks Workflow', 'Tasks Workflows', $nb, 'workflow');
   }


   function showForm($ID, $options = []) {

      $this->initForm($ID);
      $this->showFormHeader();
      echo Html::hidden('plugin_workflows_workflows_id',
                        ['value' => $_GET['id']]);

      echo "<tr class='tasktemplate'>";
      echo "<td>".__('Tasktemplate')." :</td>";
      echo "<td align='center'>";
      Dropdown::show('Tasktemplate');
      echo "</td>";
      echo "</tr>";
      echo "<tr class='workflowtasktemplate'>";
      echo "<td>".__('Parent')." :</td>";
      echo "<td align='center'>";
      PluginWorkflowsWorkflow_tasktemplate::dropdown([
         'name'  => 'plugin_workflows_workflows_tasktemplates_id',
         'value' => $this->fields["plugin_workflows_workflows_tasktemplates_id"],
      ]);
      echo "</td>";
      echo "</tr>";
      echo "<tr class='nom'>";
      echo "<td>".__('Name')." :</td>";
      echo "<td align='center'>";
      Html::autocompletionTextField($this, 'name', ['value' => $this->fields['name']]);
      echo "</td>";
      echo "</tr>";
      // echo "<tr class='needvalidation'>";
      // echo "<td>".__('Need Validation')." :</td>";
      // echo "<td align='center'>";
      // Html::showCheckbox(['name' => 'needvalidation',
      // 'value'   => '1',
      // 'checked' => $this->fields["needvalidation"]]);
      // echo "</td>";
      // echo "</tr>";
      $this->showFormButtons();
      $this->canCreateItem();
      return true;
   }

   function getAdditionalFields() {
      return [
         [
            'name'  => 'name',
            'label' => __('Name'),
            'type'  => 'textarea',
            'rows' => 10],
         ];
   }

   static function sortByLevel($a, $b) {
      if ($a['level'] == $b['level']) {
         return 0;
      }
      return ($a['level'] < $b['level']) ? -1 : +1;
   }

   function composeWorkflowTree($parent_id, $flat_tasks) {
      foreach ($flat_tasks[$parent_id] as $task) {
         $delete_icon = '';
         $url = "#";
         if (!isset($flat_tasks[$task['id']])) {
            $delete_icon = ' <i class="fas fa-times-circle"></i>';
            $url = $this->getFormURL().'?purge=1&id='.$task['id'];
         }
         echo '<li>';
         echo '<a href="'.$url.'">'.$task['name'].$delete_icon.'</a>';
         if (isset($flat_tasks[$task['id']])) {
            echo '<ul>';
            $this->composeWorkflowTree($task['id'], $flat_tasks);
            echo '</ul>';
         }
         echo '</li>';
      }
   }

   function showTasksWorkflow($ID) {
      global $DB;

   /*
   * use https://codepen.io/joellesenne/pen/KGJkz
   */
   $pwWorkflow = new PluginWorkflowsWorkflow();
   $pwWorkflow->getFromDB($ID);

   $taskquery = $DB->request("SELECT * FROM glpi_plugin_workflows_workflows_tasktemplates
       WHERE glpi_plugin_workflows_workflows_tasktemplates.plugin_workflows_workflows_id=$ID");

   $flat_tasks = [];
   foreach ($taskquery as $id => $row) {
      if (!isset($flat_tasks[$row['plugin_workflows_workflows_tasktemplates_id']])) {
         $flat_tasks[$row['plugin_workflows_workflows_tasktemplates_id']] = [];
      }
      $flat_tasks[$row['plugin_workflows_workflows_tasktemplates_id']][] = $row;
   }

   echo '<nav class="workflowdiagram">
			<ul>
            <li>
               <a href="#">'.$pwWorkflow->getName().'</a>
               <ul>
         ';
   $this->composeWorkflowTree(0, $flat_tasks);

   echo '
         </ul>
      </li>
   </ul>
</nav>';
   }
}