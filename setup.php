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

define('PLUGIN_WORKFLOWS_VERSION', '9.4+1.0');

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_workflows() {
   global $PLUGIN_HOOKS;

   $plugin = new Plugin();
   $PLUGIN_HOOKS['csrf_compliant']['workflows'] = true;

   $PLUGIN_HOOKS['add_css']['workflows'] = [
      "css/diagram.css"
   ];

   $PLUGIN_HOOKS['item_update']['workflows'] = [
      'TicketTask' => 'plugin_workflows_update',
   ];
   $PLUGIN_HOOKS['item_add']['workflows'] = [
      'Ticket' => 'plugin_workflows_add',
   ];

   Plugin::registerClass('PluginWorkflowsWorkflow_Ticket', [
      'addtabon' => [
         'Ticket'
         ]
      ]
   );
   Plugin::registerClass('PluginWorkflowsWorkflow_Tasktemplate_Tickettask');

   // Rules
   $PLUGIN_HOOKS['use_rules']['workflows'] = ['RuleTicket'];
   $PLUGIN_HOOKS['rule_matched']['workflows'] = 'plugin_workflows_rulematched';



   if ($plugin->isInstalled('workflows') && $plugin->isActivated('workflows')) {
      $PLUGIN_HOOKS["menu_toadd"]['workflows']['config'] ='PluginWorkflowsWorkflow';
   }
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_workflows() {
   return [
      'name'           => 'workflows',
      'version'        => PLUGIN_WORKFLOWS_VERSION,
      'author'         => 'Jessica DUDON && David DURIEUX',
      'license'        => '',
      'homepage'       => '',
      'requirements'   => [
         'glpi' => [
            'min' => '9.4',
            'max' => '9.5'
         ]
      ]
   ];
}

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_workflows_check_prerequisites() {

   $version = preg_replace('/^((\d+\.?)+).*$/', '$1', GLPI_VERSION);
   if (version_compare($version, '9.4', '<')) {
      echo "This plugin requires GLPI >= 9.4";
      return false;
   }
   return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_workflows_check_config($verbose = false) {
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      echo __('Installed / not configured', 'workflows');
   }
   return false;
}
