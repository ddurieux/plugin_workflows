<?php

include ("../../../inc/includes.php");

$dropdown = new PluginWorkflowsWorkflow_Ticket();

if (isset($_POST["delete"])) {

   Session::checkRight("entity", UPDATE);
   $dropdown->delete($_POST);
   Html::back();
} else if (isset($_POST["add"])) {
   Session::checkRight("entity", CREATE);
   $dropdown->add($_POST);
   Html::back();
}

include (GLPI_ROOT . "/front/dropdown.common.form.php");