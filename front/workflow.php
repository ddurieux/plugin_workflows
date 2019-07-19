<?php


include ("../../../inc/includes.php");


Html::header(
    __('Workflows', 'workflows'),
    $_SERVER["PHP_SELF"],
    'config',
    "PluginWorkflowsWorkflow"
 );
 
 Search::show('PluginWorkflowsWorkflow');
 
 Html::footer();