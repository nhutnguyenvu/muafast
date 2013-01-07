<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Rss_Order_Details extends Mage_Rss_Block_Order_Details
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('vendor/rss/order/details.phtml');
    }
}
