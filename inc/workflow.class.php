<?php

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginWorkflowsWorkflow extends CommonDBTM {

    static $doHistory = true;
    static $rightname = 'computer';
    
    static function getTypeName($nb = 0) {
        return _n('Tasks Workflow', 'Tasks Workflows', $nb, 'workflow');
    }

       /**
    * Define tabs to display on form page
    *
    * @param array $options
    * @return array containing the tabs name
    */
   function defineTabs($options = []) {

    $ong = [];
    $this->addDefaultFormTab($ong);
    $this->addStandardTab('Log', $ong, $options);

    return $ong;
 }

    function showForm($ID, $options = []) {
        global $CFG_GLPI;
        $this->initForm($ID, $options);
        $this->showFormHeader($options);

        echo "<tr class='nom'>";
        echo "<td>".__('Name')." :</td>";
        echo "<td align='center'>";
        Html::autocompletionTextField($this, 'name', ['value' => $this->fields['name']]);
        echo "</td>";
        echo "</tr>";


        $this->showFormButtons($options);
        $this->canCreateItem();
        return true;
    }

    /**
    * Get additional menu options and breadcrumb
    *
    * @global array $CFG_GLPI
    * @return array
    */
   static function getAdditionalMenuOptions() {
    global $CFG_GLPI;

    $options = [];

    $options['menu']['title'] = PluginWorkflowsWorkflow::getTypeName();
    $options['menu']['page']  = PluginWorkflowsWorkflow::getSearchURL(false);
    $options['workflow']= [
        'title'         =>       PluginWorkflowsWorkflow::getTypeName(),
        'page'          =>       PluginWorkflowsWorkflow::getSearchURL(false),
        'search'        =>       PluginWorkflowsWorkflow::getSearchURL(false),
        'add'           =>       PluginWorkflowsWorkflow::getFormURL(false)
    ];
   
    return $options;
 }
  /**
  * Get the menu name
  *
  * @return string
  */
  static function getMenuName() {
    return PluginWorkflowsWorkflow::getTypeName();
 }

}
