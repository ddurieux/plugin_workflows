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
      $this->initForm(-1);
      $this->showFormHeader();
      echo Html::hidden('plugin_workflows_workflows_id',
      ['value' => $ID]);
      echo "<tr class='tasktemplate'>";
      echo "<td>".__('Tasktemplate')." :</td>";
      echo "<td align='center'>";
      Dropdown::show('Tasktemplate');
      echo "</td>";
      echo "</tr>";
      echo "<tr class='workflowtasktemplate'>";
      echo "<td>".__('Workflow Tasktemplate')." :</td>";
      echo "<td align='center'>";
      PluginWorkflowsWorkflow_tasktemplate::dropdown([
         'name'  => 'plugin_workflows_workflows_tasktemplates_id',
         'value' => ($ID != 0) ? $this->fields["plugin_workflows_workflows_tasktemplates_id"] : 0,
      ]);
      echo "</td>";
      echo "</tr>";
      echo "<tr class='nom'>";
      echo "<td>".__('Name')." :</td>";
      echo "<td align='center'>";
      Html::autocompletionTextField($this, 'name', ['value' => $this->fields['name']]);
      echo "</td>";
      echo "</tr>";
      echo "<tr class='needvalidation'>";
      echo "<td>".__('Need Validation')." :</td>";
      echo "<td align='center'>";
      Html::showCheckbox(['name'    => 'needvalidation',
      'value'   => '1',
      'checked' => $this->fields["needvalidation"]]);
      echo "</td>";
      echo "</tr>";
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

   function sortByLevel($a, $b) {
      $a = $a['level'];
      $b = $b['level'];

      if ($a == $b) {
         return 0;
      }
      return ($a < $b) ? -1 : 1;
   }

   function showTasksWorkflow($ID) {
      global $DB;

      $tasktemplates = [];
      $workflow = [];
      $workflowquery = $DB->request("SELECT * FROM glpi_plugin_workflows_workflows_tasktemplates AS a
                                     INNER JOIN glpi_plugin_workflows_workflows AS b
                                     ON a.plugin_workflows_workflows_id=b.id WHERE b.id=$ID");

      $taskquery = $DB->request("SELECT * FROM glpi_plugin_workflows_workflows AS a
                                 INNER JOIN glpi_plugin_workflows_workflows_tasktemplates AS b
                                 ON a.id=b.plugin_workflows_workflows_id WHERE a.id=$ID");
      foreach ($workflowquery as $id => $row) {
         $workflow = $row;
      }

      foreach ($taskquery as $id => $row) {
         $tasktemplates[]= $row;
      }
      if (!empty($workflow)) {
         usort($tasktemplates, 'sortByLevel');
         $list = json_encode($tasktemplates);
         echo "<div id=diagram>";
         echo "</div>";
         echo "<script>
            var diagramdoc;
            var nodes = '';
            var connections = '';
            var nocondconnections = '';
            var nocondnodes = '\\nop=>operation: No Task';
            var diagramstring = '';
            $list.forEach(function(element,i){
               if(element.level==1 && element.needvalidation==0){
               nodes+='\\nop1=>operation: '+element.name;
               connections+= '\\n\\nst->op1->';
               if(i==$list.length-1){
                  connections+='e';
               }
               }
               if(element.level==1 && element.needvalidation==1) {
                  nodes+='\\ncond=>condition: '+element.name + ' Yes or No?:>>http://www.google.com'+nocondnodes;
                  connections+= '\\n\\nst->cond1(yes)->';
                  nocondconnections+= '\\ncond1(no)->op->e';
                  if(i==$list.length-1){
                        connections+='e';
                  }
               }
               if(element.level>1 && element.needvalidation==0){
                  nodes+='\\nop'+i+'=>operation: '+element.name;
                  connections+= 'op'+i+'->';
                  if(i==$list.length-1){
                        connections+='e';
                  }
               }
               if(element.level>1 && element.needvalidation==1){
                  i=i+1;
                  nodes+='\\ncond'+i+'=>condition: '+element.name+ ' Yes or No?:>>http://www.google.com'+nocondnodes;
                  nocondconnections+= '\\ncond'+i+'(no)->op->e';
                  connections+= 'cond'+i+'(yes)->';
               }
               console.log(element.name + ' level? '+element.level+' need? '+element.needvalidation);
               });
            diagramstring = 'st=>start: $workflow[name]:>http://www.google.com[blank]'+nodes+'".
            "\\ne=>end: Fin'+connections+nocondconnections;
            console.log(diagramstring);
            var diagram = flowchart.parse(diagramstring);
            console.log(diagram);
            diagram.drawSVG('diagram');
            </script>";
      }
   }
}