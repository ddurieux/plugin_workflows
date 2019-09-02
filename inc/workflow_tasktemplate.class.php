<?php

use tests\units\TicketTask;

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

      echo "<tr>";
      echo "<td>".__('Name')." :</td>";
      echo "<td align='center'>";
      Html::autocompletionTextField($this, 'name', ['value' => $this->fields['name']]);
      echo "</td>";
      echo "<td>"._n('Task template', 'Task templates', 1)." :</td>";
      echo "<td align='center'>";
      Dropdown::show('Tasktemplate');
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td colspan='2'></td>";
      echo "<td>".__('Parent of')." :</td>";
      echo "<td align='center'>";
      PluginWorkflowsWorkflow_tasktemplate::dropdown([
         'name'  => 'plugin_workflows_workflows_tasktemplates_id',
         'value' => $this->fields["plugin_workflows_workflows_tasktemplates_id"],
      ]);
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

   function composeWorkflowTree($parent_id, $flat_tasks, $display_only, $tickets_id, $tickettasks) {
      foreach ($flat_tasks[$parent_id] as $task) {
         $delete_icon = '';
         $url = "#";
         if (!$display_only && !isset($flat_tasks[$task['id']])) {
            $delete_icon = ' <i class="fas fa-times-circle"></i>';
            $url = $this->getFormURL().'?purge=1&id='.$task['id'];
         }
         $css_green = '';
         if ($tickets_id > 0
               && countElementsInTable(PluginWorkflowsWorkflow_Tasktemplate_Tickettask::getTable(),
                                       ['plugin_workflows_workflows_tasktemplates_id' => $task['id'], 'tickettasks_id' => $tickettasks])) {
            // TODO faire requete avec jointure
            $css_green = 'class="workflowdiagramgreen"';
         }

         echo '<li>';
         echo '<a href="'.$url.'" '.$css_green.'>'.$task['name'].$delete_icon.'</a>';
         if (isset($flat_tasks[$task['id']])) {
            echo '<ul>';
            $this->composeWorkflowTree($task['id'], $flat_tasks, $display_only, $tickets_id, $tickettasks);
            echo '</ul>';
         }
         echo '</li>';
      }
   }

   function showTasksWorkflow($ID, $display_only = false, $tickets_id = 0) {
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

      $tickettasks = [];
      if ($tickets_id > 0) {
         $iterator = $DB->request([
            'FROM'   => \TicketTask::getTable(),
            'WHERE'  => [
               'tickets_id' => $tickets_id
            ]
         ]);
         while ($data = $iterator->next()) {
            $tickettasks[] = $data['id'];
         }
      }

      $css_green = '';
      if ($tickets_id > 0) {
         $css_green = 'class="workflowdiagramgreen"';
      }

      echo '<nav class="workflowdiagram">
         <table>
            <tr>
               <td>
                  <ul>
                     <li>
                        <a href="#" '.$css_green.'>'.$pwWorkflow->getName().'</a>
                        <ul>
            ';
      $this->composeWorkflowTree(0, $flat_tasks, $display_only, $tickets_id, $tickettasks);

      echo '
                  </ul>
               </li>
            </ul>
         </td>
      </tr>
   </table>
</nav>';
   }
}
