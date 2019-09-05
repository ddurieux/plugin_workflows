<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginWorkflowsWorkflow_Tasktemplate_Tickettask extends CommonDBRelation {

   static $doHistory = true;
   public $can_be_translated  = true;
   // From CommonDBRelation

   static public $itemtype_2 = 'PluginWorkflowsWorkflow_Tasktemplate';
   static public $items_id_2 = 'plugin_workflows_workflows_tasktemplates_id';
   static public $itemtype_1 = 'Tickettask';
   static public $items_id_1 = 'tickettasks_id';
   static public $checkItem_2_Rights  = self::HAVE_VIEW_RIGHT_ON_ITEM;
   public $no_form_page                  = false;
   static public $mustBeAttached_1       = false;
   static public $mustBeAttached_2       = false;

   const VALIDATION_USER  = 1;
   const VALIDATION_GROUP = 2;

}
