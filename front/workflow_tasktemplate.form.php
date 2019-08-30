<?php

include ("../../../inc/includes.php");

$dropdown = new PluginWorkflowsWorkflow_tasktemplate();

use Glpi\Event;

if (isset($_GET['purge']) && isset($_GET['id'])) {

   $dropdown->check($_GET["id"], PURGE);
   $dropdown->delete($_GET, 1);

   Event::log($_GET["id"], get_class($dropdown), 4, "setup",
               //TRANS: %s is the user login
               sprintf(__('%s purges an item'), $_SESSION["glpiname"]));
   Html::back();
}

include (GLPI_ROOT . "/front/dropdown.common.form.php");