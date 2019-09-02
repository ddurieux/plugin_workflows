<?php
/*
 -------------------------------------------------------------------------
 workflows plugin for GLPI
 Copyright (C) 2019 by the workflows Development Team.

 https://github.com/pluginsGLPI/workflows
 -------------------------------------------------------------------------

 LICENSE

 This file is part of workflows.

 workflows is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 workflows is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with workflows. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

use tests\units\TicketTask;

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_workflows_install() {
   global $DB;

   if (!$DB->TableExists("glpi_plugin_workflows_workflows")) {
      $DB->query("CREATE TABLE `glpi_plugin_workflows_workflows` (
                 `id` int(11) NOT NULL AUTO_INCREMENT,
                 `entities_id` int(11) NOT NULL default '0',
                 `is_recursive` tinyint(1) NOT NULL DEFAULT '0',
                 `name` varchar(255) NOT NULL,
                 PRIMARY KEY  (`id`),
                 KEY `entities_id` (`entities_id`)
               ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
   }
   if (!$DB->TableExists("glpi_plugin_workflows_workflows_tasktemplates")) {
      $DB->query("CREATE TABLE `glpi_plugin_workflows_workflows_tasktemplates` (
                 `id` int(11) NOT NULL  AUTO_INCREMENT,
                 `name` varchar(255),
                 `level` int(11) NOT NULL,
                 `completename` text,
                 `plugin_workflows_workflows_tasktemplates_id` int(11) NOT NULL default '0',
                 `plugin_workflows_workflows_id` int(11) NOT NULL default '0',
                 `tasktemplates_id` int(11) NOT NULL default '0',
                 `is_validated` int(11) NOT NULL default '0',
                 `needvalidation` bool,
                 PRIMARY KEY (`id`),
                 KEY `plugin_workflows_workflows_tasktemplates_id` (`plugin_workflows_workflows_tasktemplates_id`),
                 KEY `plugin_workflows_workflows_id` (`plugin_workflows_workflows_id`),
                 Key `tasktemplates_id` (`tasktemplates_id`)
               ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
   }
   if (!$DB->TableExists("glpi_plugin_workflows_workflows_tickets")) {
      $DB->query("CREATE TABLE `glpi_plugin_workflows_workflows_tickets` (
                 `id` int(11) NOT NULL AUTO_INCREMENT,
                 `tickets_id` int(11) NOT NULL default '0',
                 `plugin_workflows_workflows_id` int(11) NOT NULL default '0',
                 PRIMARY KEY  (`id`),
                 KEY `tickets_id` (`tickets_id`),
                 KEY `plugin_workflows_workflows_id` (`plugin_workflows_workflows_id`)
               ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
   }
   if (!$DB->TableExists("glpi_plugin_workflows_workflows_tasktemplates_tickettasks")) {
      $DB->query("CREATE TABLE `glpi_plugin_workflows_workflows_tasktemplates_tickettasks` (
                 `id` int(11) NOT NULL AUTO_INCREMENT,
                 `tickettasks_id` int(11) NOT NULL default '0',
                 `plugin_workflows_workflows_tasktemplates_id` int(11) NOT NULL default '0',
                 PRIMARY KEY  (`id`),
                 KEY `tickettasks_id` (`tickettasks_id`),
                 KEY `plugin_workflows_workflows_tasktemplates_id` (`plugin_workflows_workflows_tasktemplates_id`)
               ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
   }
   return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_workflows_uninstall() {
   global $DB;

   $DB->query("DROP TABLE IF EXISTS `glpi_plugin_workflows_workflows`;");
   $DB->query("DROP TABLE IF EXISTS `glpi_plugin_workflows_workflows_tasktemplates`;");
   $DB->query("DROP TABLE IF EXISTS `glpi_plugin_workflows_workflows_tickets`;");
   $DB->query("DROP TABLE IF EXISTS `glpi_plugin_workflows_workflows_tasktemplates_tickettasks`;");
   return true;
}

/**
 * When update a tickettask, create the next one
 */
function plugin_workflows_update(\TicketTask $item) {
   global $DB;

   if ($item->fields['state'] != Planning::DONE) {
      return true;
   }

   $pwWorkflow_Ticket = new PluginWorkflowsWorkflow_ticket();
   $pwWorkflow_Tasktemplate_Tickettask = new PluginWorkflowsWorkflow_Tasktemplate_Tickettask();
   $taskTemplate = new TaskTemplate();
   $ticketTask = new \TicketTask();

   if ($pwWorkflow_Tasktemplate_Tickettask->getFromDBByCrit(['tickettasks_id' => $item->getID()])) {
      // get the new tasks to create
      $iterator = $DB->request([
         'FROM'   => \PluginWorkflowsWorkflow_tasktemplate::getTable(),
         'WHERE'  => [
            'plugin_workflows_workflows_tasktemplates_id' => $pwWorkflow_Tasktemplate_Tickettask->fields['plugin_workflows_workflows_tasktemplates_id']
         ]
      ]);
      while ($data = $iterator->next()) {
         // check if task not yet added (can be the case if on older task updated)
         if (!countElementsInTable(PluginWorkflowsWorkflow_Tasktemplate_Tickettask::getTable(), ['plugin_workflows_workflows_tasktemplates_id' => $data['id']])) {
            $taskTemplate->getFromDB($data['tasktemplates_id']);
            $tickettasks_id = $ticketTask->add([
               'tickets_id'        =>  $item->fields['tickets_id'],
               'tasktemplates_id'  =>  $taskTemplate->fields['id'],
               'content'           =>  addslashes($taskTemplate->fields['content']),
               'goups_id_tech'     =>  $taskTemplate->fields['groups_id_tech'],
               'action_time'       =>  $taskTemplate->fields['actiontime'],
               'taskcategories_id' =>  $taskTemplate->fields['taskcategories_id'],
               'is_private'        =>  $taskTemplate->fields['is_private'],
            ]);
            $pwWorkflow_Tasktemplate_Tickettask->add([
               'plugin_workflows_workflows_tasktemplates_id' => $data['id'],
               'tickettasks_id' => $tickettasks_id
            ]);
         }
      }
   }
}
