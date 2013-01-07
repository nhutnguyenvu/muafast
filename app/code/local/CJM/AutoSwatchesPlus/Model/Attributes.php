<?php
class CJM_AutoSwatchesPlus_Model_Attributes
{
    public static $_entityTypeId;
    public static $_productAttributes;
    public static $_productAttributeOptions;

    public static function getAvailableSwatchAttributes()
    {
        if(is_array(self::$_productAttributes))
            return self::$_productAttributes;

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');
        $select = $db->select()->from($resource->getTableName('eav/entity_type'), 'entity_type_id')->where('entity_type_code=?', 'catalog_product')->limit(1);

        self::$_entityTypeId = $db->fetchOne($select);

        $select = $db->select()->from($resource->getTableName('eav/attribute'), array(
                    'title' => 'frontend_label',    
                    'id'    => 'attribute_id',      
                    'code'  => 'attribute_code',    
                ))
            ->where('entity_type_id=?', self::$_entityTypeId)->where('frontend_label<>""')->where('find_in_set(frontend_input, "select") AND find_in_set(is_user_defined, "1")')->order('frontend_label');

        foreach($db->fetchAll($select) as $s)
            self::$_productAttributes[$s['id']] = array(
                    'title' => $s['title'],
                    'code'  => $s['code'],
            );

        return self::$_productAttributes;
    }
	
	public static function toOptionArray()
    {
        if(is_array(self::$_productAttributeOptions)) return self::$_productAttributeOptions;

        self::$_productAttributeOptions = array();

        foreach(self::getAvailableSwatchAttributes() as $id => $data)
            self::$_productAttributeOptions[] = array(
                'value' => $id,
                'label' => $data['title'].' ('.$data['code'].')'
            );

        return self::$_productAttributeOptions;
    }
}