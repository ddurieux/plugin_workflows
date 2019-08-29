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

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_workflows_install() {
   global $DB;


if (!$DB->TableExists("glpi_plugin_workflows_workflows")) {

   $DB->query ("CREATE TABLE `glpi_plugin_workflows_workflows` (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `entities_id` int(11) NOT NULL default '0',
               `name` varchar(255) NOT NULL,
               PRIMARY KEY  (`id`),
               KEY `entities_id` (`entities_id`)
             ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}
if (!$DB->TableExists("glpi_plugin_workflows_workflows_tasktemplates")) {

   $DB->query ("CREATE TABLE `glpi_plugin_workflows_workflows_tasktemplates` (
               `id` int(11) NOT NULL  AUTO_INCREMENT,
               `name` varchar(255),
               `level` int(11) NOT NULL,
               `completename` text,
               `workflows_tasktemplates_id` int(11) NOT NULL default '0',
               `workflows_id` int(11) NOT NULL default '0',
               `tasktemplates_id` int(11) NOT NULL default '0',
               `isvalidated` bool default NOT NULL'0',
               `needvalidation` bool,
               PRIMARY KEY (`id`),
               KEY `plugin_workflows_workflows_tasktemplates_id` (`workflows_tasktemplates_id`), 
               KEY `plugin_workflows_workflows_id` (`workflows_id`),
               Key `tasktemplates_id` (`tasktemplates_id`)
             ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}
if (!$DB->TableExists("glpi_plugin_workflows_workflows_tickets")) {

   $DB->query ("CREATE TABLE `glpi_plugin_workflows_workflows_tickets` (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `tickets_id` int(11) NOT NULL default '0',
               `workflows_id` int(11) NOT NULL default '0',
               PRIMARY KEY  (`id`),
               KEY `tickets_id` (`tickets_id`),
               KEY `plugin_workflows_workflows_id` (`workflows_id`)
             ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}
if (!$DB->TableExists("glpi_plugin_workflows_workflows_tasktemplates_tickettasks")) {

   $DB->query ("CREATE TABLE `glpi_plugin_workflows_workflows_tasktemplates_tickettasks` (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `tickettasks_id` int(11) NOT NULL default '0',
               `plugin_workflows_workflows_tasktemplates_id` int(11) NOT NULL default '0',
               PRIMARY KEY  (`id`),
               KEY `tickettasks_id` (`tickettasks_id`),
               KEY `plugin_workflows_workflows_tasktemplates_id` (`tasktemplates_id`)
             ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
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
   return true;
}

function plugin_workflows_update($ID) {
  $task = new TicketTask();
  $ticket = new PluginWorkflowsWorkflow_ticket();
  $workflow = new PluginWorkflowsWorkflow();

  $task->getID();
  
   if(){

  }
}

