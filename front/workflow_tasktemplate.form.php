<?php

include ("../../../inc/includes.php");

$dropdown = new PluginWorkflowsWorkflow_Tasktemplate();

use Glpi\Event;
if (isset($_POST['tasktemplates_id'])) {
   $err = false;
   $tab_msg_err = [];
   if (trim($_POST['name']) == "") {
      $tab_msg_err[] = 'Nom vide';
      $err = true;
   }
   if ($_POST['tasktemplates_id'] === "0") {
      $tab_msg_err[] = 'Gabarit de tâche vide';
      $err = true;
   }
   if ($err === true) {
      $reset_popup = true; // permet de d'effacer tout message précédent
      foreach (array_values($tab_msg_err) as $msg_err) {
         Session::addMessageAfterRedirect($msg_err, false, ERROR, $reset_popup); // ajout d'un message dans popup erreur apres redirection
         $reset_popup = false; // permet de ne plus effacer tout message précédent
      }
      Html::back();
   }
}

if (isset($_GET['purge']) && isset($_GET['id'])) {

   $dropdown->check($_GET["id"], PURGE);
   $dropdown->delete($_GET, 1);

   Event::log($_GET["id"], get_class($dropdown), 4, "setup",
               //TRANS: %s is the user login
               sprintf(__('%s purges an item'), $_SESSION["glpiname"]));
   Html::back();
}

include (GLPI_ROOT . "/front/dropdown.common.form.php");