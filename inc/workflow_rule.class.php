<?php

class PluginWorkflowsWorkflow_rule extends Rule {

    // optional right to apply to this rule type (default: 'config'), see Rights management.
    static $rightname = 'config';

    // define a label to display in interface titles
    function getTitle() {
        return __('Workflows');
    }

    // return an array of criteria
    function getCriterias() {
        $criterias = [
            '_users_id_requester' => [
                'field'     => 'name',
                'name'      => __('Requester'),
                'table'     => 'glpi_users',
                'type'      => 'dropdown',
            ],

            'GROUPS'              => [
                'table'     => 'glpi_groups',
                'field'     => 'completename',
                'name'      => sprintf(__('%1$s: %2$s'), __('User'),
                                      __('Group')),
                'linkfield' => '',
                'type'      => 'dropdown',
                'virtual'   => true,
                'id'        => 'groups'
            ],


        ];

        $criterias['GROUPS']['table']                   = 'glpi_groups';
        $criterias['GROUPS']['field']                   = 'completename';
        $criterias['GROUPS']['name']                    = sprintf(__('%1$s: %2$s'), __('User'),
                                                                  __('Group'));
        $criterias['GROUPS']['linkfield']               = '';
        $criterias['GROUPS']['type']                    = 'dropdown';
        $criterias['GROUPS']['virtual']                 = true;
        $criterias['GROUPS']['id']                      = 'groups';

        return $criterias;
    }

    // return an array of actions
    function getActions() {
        $actions = [
            'entities_id' => [
                'name'  => __('Entity'),
                'type'  => 'dropdown',
                'table' => 'glpi_entities',
            ],

        ];

        return $actions;
    }
}
