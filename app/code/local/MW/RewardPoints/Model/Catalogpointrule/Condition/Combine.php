<?php


class MW_Rewardpoints_Model_Catalogpointrule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
	parent::__construct();
        $this->setType('rewardpoints/catalogpointrule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        
        $productCondition = Mage::getModel('catalogrule/rule_condition_product');
        //$productCondition = Mage::getModel('rewardpoints/catalogpointrule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code=>$label) {
            $attributes[] = array('value'=>'catalogrule/rule_condition_product|'.$code, 'label'=>$label);
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'rewardpoints/catalogpointrule_condition_combine', 'label'=>Mage::helper('catalogrule')->__('Conditions Combination')),
            array('label'=>Mage::helper('rewardpoints')->__('Product Attribute'), 'value'=>$attributes),
        ));

        return $conditions;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('rewardpoints')->__("If %s of these order conditions are %s",
              $this->getAggregatorElement()->getHtml(),
			  $this->getValueElement()->getHtml()
           );
           if ($this->getId()!='1') {
               $html.= $this->getRemoveLinkHtml();
           }

        return $html;
    }


    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
