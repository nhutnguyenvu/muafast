<?php

class MW_Rewardpoints_Block_Adminhtml_Catalogpointrules_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
	$this->_objectId = 'id';
        $this->_blockGroup = 'rewardpoints';
        $this->_controller = 'adminhtml_catalogpointrules';


        //Rewardpoints_Model_Pointrules::RULE_ACTION_TYPE_ADD
        $this->_formScripts[] = "

            checkTypes = function(){
                if ($('rule_action_type').getValue() == '".MW_Rewardpoints_Model_Catalogpointrules::RULE_ACTION_TYPE_DONTPROCESS."'){
                    $('rule_points').value = '1';
                    $('rule_points').up(1).hide();
                } else {
                    $('rule_points').up(1).show();
                }
            };

            Event.observe($('rule_action_type'), 'change', function(event) {
                checkTypes();
            });


            document.observe('dom:loaded', function() {
                checkTypes();
                $('rule_rule_type').up(1).hide();
            });
        ";


        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('rewardpoints')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('rewardpoints')->__('Delete Rule'));

        $filter = Mage::registry('catalogpointrules_data');
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('catalogpointrules_data');
        if ($rule->getRuleId()) {
            return Mage::helper('rewardpoints')->__("Edit Rule '%s'", $this->htmlEscape($rule->getTitle()));
        }
        else {
            return Mage::helper('rewardpoints')->__('New Rule');
        }
    }
}
