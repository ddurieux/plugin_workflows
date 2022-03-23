<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginWorkflowsWorkflow_Ticket extends CommonDBRelation {

   static $doHistory = true;
   public $can_be_translated  = true;
   // From CommonDBRelation
   static public $itemtype_2 = 'PluginWorkflowsWorkflow';
   static public $items_id_2 = 'plugin_workflows_workflows_id';
   static public $itemtype_1 = 'Ticket';
   static public $items_id_1 = 'tickets_id';
   static public $checkItem_2_Rights  = self::HAVE_VIEW_RIGHT_ON_ITEM;
   public $no_form_page                  = false;
   static public $mustBeAttached_1       = false;
   static public $mustBeAttached_2       = false;

   const VALIDATION_USER  = 1;
   const VALIDATION_GROUP = 2;

   static function displayTabContentForItem(\CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
      $pwWorkflow_Ticket = new PluginWorkflowsWorkflow_Ticket;

      if (!$pwWorkflow_Ticket->getFromDBByCrit(['tickets_id' => $_GET['id']])) {
         $pwWorkflow_Ticket->showForm('');
      } else {
         $pwWorkflow_Ticket->showWorkflows($item->getID());
      }
      return true;
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
      Ticket::getType();

      $nb = 0;
      if ($_SESSION['glpishow_count_on_tabs']) {
         $nb = countElementsInTable(self::getTable(),
                                    ['tickets_id' => $item->getID()]);
      }
      return self::createTabEntry(__('Workflow', 'worflows'), $nb);
   }

   static function getTypeName($nb = 0) {
      return _n('Tickets Workflows', 'Tickets Workflows', $nb, 'workflow');
   }

   function showForm($ID, $options = []) {
      global $CFG_GLPI;

      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";
      echo "<td>" . __('Workflow', 'workflows') . " :</td>";
      echo "<td>";
      echo Html::hidden('tickets_id', ['value' => $_GET['id']]);
      PluginWorkflowsWorkflow::dropdown([
         'name'  => 'plugin_workflows_workflows_id',
         'value' => ($ID != 0) ? "plugin_workflows_workflows_id" : 0,
      ]);
      echo "</td>";
      echo "</tr>";

      $this->showFormButtons($options);

      return true;
   }

   function showWorkflows($tickets_id, $options = []) {
      global $CFG_GLPI, $DB;

      $ticketworkflows = [];
      $workflowsquery = $DB->request("SELECT * FROM glpi_plugin_workflows_workflows AS a
                                      INNER JOIN glpi_plugin_workflows_workflows_tickets AS b
                                      ON a.id = b.plugin_workflows_workflows_id where b.tickets_id=$tickets_id");
      foreach ($workflowsquery as $key => $value) {
         $ticketworkflows[] = $value;
      }

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr>";
      echo "<th colspan='2'>";
      echo "Ticket Workflow";
      echo "</th>";
      echo "</tr>";
      foreach ($ticketworkflows as $id => $row) {
         echo "<tr>";
         echo "<td>";
         echo $row['name'];
         echo "</td>";
         echo "<td>";
         echo "<form name='delete' method='post'
         action='" . $CFG_GLPI['root_doc'] . "/plugins/workflows/front/workflow_ticket.form.php'>";
         echo html::hidden('id', ['value' => $row['id']]);
         echo Html::submit(_x('button', 'Delete permanently'), ['name'  => "delete"]);
         Html::closeForm();
         echo "</td>";
         echo "</tr>";
      }
      echo "</table>";

      $pwWorkflow_Tasktemplate = new PluginWorkflowsWorkflow_Tasktemplate();
      $this->getFromDBByCrit(['tickets_id' => $tickets_id]);

      echo "<table width='100%' class='table'>";
      echo "   <tr>";
      echo "      <td align='center'>";
      $pwWorkflow_Tasktemplate->showTasksWorkflow($this->fields['plugin_workflows_workflows_id'], true, $tickets_id);
      echo "      </td>";
      echo "   </tr>";
      echo "</table>";
   }

   function post_addItem() {
      global $DB;

      $ticketTask = new TicketTask();
      $taskTemplate = new TaskTemplate();
      $pwWorkflow_Tasktemplate_Tickettask = new PluginWorkflowsWorkflow_Tasktemplate_Tickettask();

      $workflowID = $this->fields['plugin_workflows_workflows_id'];

      $tasktemplatesquery = $DB->request("SELECT * FROM glpi_plugin_workflows_workflows_tasktemplates 
            WHERE plugin_workflows_workflows_id=$workflowID AND plugin_workflows_workflows_tasktemplates_id=0");

      foreach ($tasktemplatesquery as $data) {
         $tickettasks_id = PluginWorkflowsWorkflow_Tasktemplate::createTaskFromTemplate($data['tasktemplates_id'], $this->fields['tickets_id'], true);
         $pwWorkflow_Tasktemplate_Tickettask->add([
            'plugin_workflows_workflows_tasktemplates_id' => $data['id'],
            'tickettasks_id' => $tickettasks_id
         ]);
      }
   }
}
