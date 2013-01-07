<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Common_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        if ($row->getActivated()) 
        {
            $cell = '<span class="grid-severity-notice"><span>' . $value . '</span></span>';
        } 
        else 
        {
            $cell = '<span class="grid-severity-critical"><span>' . $value . '</span></span>';
        }
        
        return $cell;
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('config_id' => $row->getConfigId()));
    }
}