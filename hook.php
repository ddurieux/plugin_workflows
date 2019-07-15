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

$migration = new Migration(PLUGIN_WORKFLOWS_VERSION);

// Création de la table uniquement lors de la première installation
if (!TableExists("glpi_plugin_workflow_workflows")) {

   // requete de création de la table    
   $DB->query ("CREATE TABLE `glpi_plugin_workflow_workflows` (
               `id` int(11) NOT NULL,
               `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
               `name` varchar(255) NOT NULL,
               PRIMARY KEY  (`id`),
             ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}
if (!TableExists("glpi_plugin_workflow_tasks")) {

   $DB->query ("CREATE TABLE `glpi_plugin_workflow_tasks` (
               `id` int(11) NOT NULL,
               `workflows_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugin_workflow_worlflows (id)',
               `tasks_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_tasktemplates (id)',
               PRIMARY KEY  (`id`),
             ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}
if (!TableExists("glpi_plugin_workflow_tickets")) {

   $DB->query ("CREATE TABLE `glpi_plugin_workflow_tickets` (
               `id` int(11) NOT NULL,
               `tickets_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_tickets (id)',
               `workflows_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugin_workflow_worlflows (id)',
               PRIMARY KEY  (`id`),
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
   return true;
}

