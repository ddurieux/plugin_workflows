<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginWorkflowsWorkflow_ticket extends CommonDBRelation {

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
      $workflows = new PluginWorkflowsWorkflow_ticket;
      echo "<table>";
      echo "<tr>";
      echo "<td>";
      $workflows->showWorkflows($item->getID());
      echo "</tr>";
      echo "</td>";
      if (!$workflows->getFromDBByCrit(['tickets_id' => $item->getID()])) {
         echo "<tr>";
         echo "<td>";
         $workflows->showForm($item->getID());
         echo "</tr>";
         echo "</td>";
      }

      echo "</table>";

      return true;
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
      Ticket::getType();
      return __('Workflows', 'workflows');
   }

   static function getTypeName($nb = 0) {
      return _n('Tickets Workflows', 'Tickets Workflows', $nb, 'workflow');
   }

   function showForm($ID, $options = []) {
      global $CFG_GLPI;

      echo "<form name='add' method='post'
      action='" . $CFG_GLPI['root_doc'] . "/plugins/workflows/front/workflow_ticket.form.php'>";
      echo "<table>";
      echo "<tr class='workflows'>";
      echo "<td>" . __('Workflows') . " :</td>";
      echo "<td>";
      PluginWorkflowsWorkflow::dropdown([
         'name'  => 'plugin_workflows_workflows_id',
         'value' => ($ID != 0) ? "plugin_workflows_workflows_id" : 0,
      ]);
      echo "</td>";
      echo "</tr>";
      echo Html::hidden('tickets_id', ['value' => $ID]);
      echo "<td>";
      echo "<tr>";
      echo Html::submit(_x('button', 'Add'),
                           ['name'  => "add"]);
      echo "</td>";
      echo "</tr>";
      echo "</table>";
      Html::closeForm();
   }

   function showWorkflows($ID, $options = []) {
      global $CFG_GLPI;
      global $DB;
      $workflow = new PluginWorkflowsWorkflow;
      $ticketworkflows = [];
      $workflowsquery = $DB->request("SELECT * FROM glpi_plugin_workflows_workflows AS a
                                      INNER JOIN glpi_plugin_workflows_workflows_tickets AS b
                                      ON a.id = b.plugin_workflows_workflows_id where b.tickets_id=$ID");
      foreach ($workflowsquery as $key => $value) {
         $ticketworkflows[] = $value;
      }

      echo "<table>";
      echo "<tr>";
      echo "<th>";
      echo "Ticket Worlflow";
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
   }

   function post_addItem() {
      global $CFG_GLPI, $DB;
      $workflowID = $this->fields['plugin_workflows_workflows_id'];
      $tasktemplatesquery = $DB->request("SELECT * FROM glpi_plugin_workflows_workflows AS a
                                          INNER JOIN glpi_plugin_workflows_workflows_tasktemplates AS b
                                          ON a.id=b.plugin_workflows_workflows_id WHERE a.id=$workflowID");

      $tasktemplatequeryresult = [];
      $tasktemplates = [];

      foreach ($tasktemplatesquery as $key => $value) {
         $tasktemplatequeryresult[] = $value;
      }
      $task = new TicketTask;
      $ttItem = new TaskTemplate;
      foreach ($tasktemplatequeryresult as $key => $value) {
         $tasktemplates[] = $value;
      }
      foreach ($tasktemplates as $index => $element) {
         if ($ttItem->getFromDB($element['tasktemplates_id'])) {
            if ($element['level'] == 1) {
               $task->add([
                  'tickets_id'        =>   $this->fields['tickets_id'],
                  'tasktemplates_id'  =>   $element['tasktemplates_id'],
                  'content'           =>   $ttItem->fields['content'],
                  'goups_id_tech'     =>   $ttItem->fields['groups_id_tech'],
                  'action_time'       =>   $ttItem->fields['actiontime'],
                  'taskcategories_id' =>   $ttItem->fields['taskcategories_id'],
                  'is_private'        =>   $ttItem->fields['is_private'],
               ]);
            }
         }
      }
      $follow = new \ITILFollowup();
   }
}
