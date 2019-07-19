<?php

include ("../../../inc/includes.php");

$workflow = new PluginWorkflowsWorkflow();
if (!isset($_GET["id"])) {
    $_GET["id"] = "";
 }

Html::header(__('Workflows', 'workflows'),
$_SERVER["PHP_SELF"],
'config',
"PluginWorkflowsWorkflow", "workflow");

 if (isset($_POST['add'])) {
    //Check CREATE ACL
    $workflow->check(-1, CREATE, $_POST);
    $newid = $workflow->add($_POST);
    //Redirect to newly created object form
    Html::back;
 } else if (isset($_POST['update'])) {
    //Check UPDATE ACL
    $workflow->check($_POST['id'], UPDATE);
    //Do object update
    $workflow->update($_POST);
    //Redirect to object form
    Html::back();
 } else if (isset($_POST['delete'])) {
    //Check DELETE ACL
    $workflow->check($_POST['id'], DELETE);
    //Put object in dustbin
    $workflow->delete($_POST);
    //Redirect to objects list
    $workflow->redirectToList();
 } else if (isset($_POST['purge'])) {
    //Check PURGE ACL
    $workflow->check($_POST['id'], PURGE);
    //Do object purge
    $workflow->delete($_POST, 1);
    //Redirect to objects list
    Html::redirect("{$CFG_GLPI['root_doc']}/plugins/front/workflow.php");
 } else {
    //per default, display object
    $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : 0);
    $workflow->display(
       [
          'id'           => $_GET['id'],
          'withtemplate' => $withtemplate
       ]
    );
 }
 

Html::footer();