<?php

include ("../../../inc/includes.php");

// Session::checkRight("entity", UPDATE);

// Plugin::load('workflow', true);

$dropdown = new PluginWorkflowsWorkflow_tasktemplate();

include (GLPI_ROOT . "/front/dropdown.common.form.php");