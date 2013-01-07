<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Statement_Pdf_Statement extends FavoredMinds_Vendor_Model_Statement_Pdf_Abstract {
  protected $_curPageNum;
  protected $_pageFooter = array();
  protected $_globalTotals = array();
  protected $_totalsPageNum = 0;

  public function before() {
    Mage::getSingleton('core/translate')->setTranslateInline(false);

    $pdf = new Zend_Pdf();
    $this->setPdf($pdf);

    return $this;
  }

  public function addStatement($statement) {
    $_helper = Mage::helper('vendor');
    $this->setStatement($statement);

    $ordersData = Zend_Json::decode($statement->getOrdersData());

    // first front page header
    $this->_curPageNum = 0;
    $this->addPage()->insertPageHeader(array('first'=>true, 'data'=>$ordersData));

    if (!empty($ordersData['orders'])) {
      // iterate through orders
      foreach ($ordersData['orders'] as $order) {
        $this->insertOrder($order);
      }
    } else {
      $this->text($_helper->__('No orders found for this period.'), 'down')
              ->moveRel(0, .5);
    }

    $this->insertTotals($ordersData['totals']);

    $this->setAlign('left')->font('normal', 10);

    $vendor = $_helper->getVendor($statement->getVendorId());

    if ($vendor->getId()) {
      $totals = $ordersData['totals'];
      $totals['vendor_name'] = $vendor->getCompanyName();
      $totals['statement_name'] = $statement->getStatementId();
      $this->_globalTotals[$vendor->getId()] = $totals;
    }

    return $this;
  }

  public function after() {
    Mage::getSingleton('core/translate')->setTranslateInline(true);
    return $this;
  }

  public function insertPageHeader($params=array()) {
    $core = Mage::helper('core');
    $_helper = Mage::helper('vendor');
    $store = null;

    // only for first page
    if (!empty($params['first'])) {
      $statement = $this->getStatement();
      $vendor = $_helper->getVendor($statement->getVendorId());
      $this->move(2.1667, 1)
            ->rectangle(6.6667, 1.1458, 1, 0)
            ->rectangle(3.33335, 1.1458, 1, 0);

      $this->move(2.2604, 1.073)
              ->font('normal', 9)
              ->text($_helper->__("Statement Information:"));
      // vendor info
      $this->move(2.2604, 1.2605)
              ->font('normal', 9)
              ->text($_helper->__("%s Vendor Statement", $vendor->getCompanyName()));
      $this->move(2.2604, 1.448)
              ->font('normal', 9)
              ->text($_helper->__("Statement Name – %s", $statement->getStatementId()));
      $this->move(2.2604, 1.6355)
              ->font('normal', 9)
              ->text($_helper->__("Statement Id – %s", $statement->getId()));
      $this->move(2.2604, 1.823)
              ->font('normal', 9)
              ->text($_helper->__("Statement Duration – %s, %s", $core->formatDate($statement->getOrderDateFrom(), 'medium'), $core->formatDate($statement->getOrderDateTo(), 'medium')));
      //address
      $this->move(5.5833, 1.073)
              ->font('normal', 9)
              ->text($_helper->__("Address:"));
      $this->move(5.5833, 1.2605)
              ->font('normal', 9)
              ->text($vendor->getCompanyName());
      $this->move(5.5833, 1.448)
              ->font('normal', 9)
              ->text($vendor->getAddress());
      $this->move(5.5833, 1.6355)
              ->font('normal', 9)
              ->text($vendor->getCity() . ', ' . $vendor->getState() . ' ' . $vendor->getZip());

      $this->font('bold', 15)
              // statement info
              ->setAlign('center')
              ->move(5.5, 2.4896)->text($_helper->__("Payment to %s: %s", $vendor->getCompanyName(), $params['data']['totals']['total_payout']))
              ->move(1.344, 3.146)
      ;

    }
    // grid titles
    $this->insertGridHeader();

    $this->_curPageNum++;
    $this->_pageFooter[] = array('page_num'=>$this->_curPageNum);

    return $this;
  }

  public function insertGridHeader() {
    $_helper = Mage::helper('vendor');
    $this->rectangle(8.28125, .375, .930, 0)
            ->rectangle(7.2917, .375, .930, 0)
            ->rectangle(6.1875, .375, .930, 0)
            ->rectangle(5.1875, .375, .930, 0)
            ->rectangle(4.2667, .375, .930, 0)
            ->rectangle(3.4375, .375, .930, 0)
            ->rectangle(2.6042, .375, .930, 0)
            ->rectangle(1.7604, .375, .930, 0)
            ->rectangle(0.8854, .375, .930, 0)
            ->movePush()
            ->moveRel(.4427, .1)
            ->font('bold', 8)
            ->setAlign('center')
            ->text($_helper->__("Order #"))
            ->moveRel(.8646, 0)->text($_helper->__("Date Order"))
            ->moveRel(.84375, 0)->text($_helper->__("# of items"))
            ->moveRel(.875, 0)->text($_helper->__("Price"))
            ->moveRel(.8346, 0)->text($_helper->__("Shipping"))
            ->moveRel(.9375, 0)->text($_helper->__("Tax"))
            ->moveRel(.8229, 0)->text($_helper->__("Handling"))
            ->moveRel(1.0625, 0)->text($_helper->__("Adjustment"))
            ->moveRel(1.0625, 0)->text($_helper->__("Revenue"))
            ->movePop(0, .375)
    ;

    return $this;
  }

  public function insertOrder($order) {
    $core = Mage::helper('core');

    $this->checkPageOverflow()
            ->setMaxHeight(0)
            ->font('normal', 8)
            ->rectangle(8.28125, .21, 1, 0)
            ->rectangle(7.2917, .21, 1, 0)
            ->rectangle(6.1875, .21, 1, 0)
            ->rectangle(5.1875, .21, 1, 0)
            ->rectangle(4.2667, .21, 1, 0)
            ->rectangle(3.4375, .21, 1, 0)
            ->rectangle(2.6042, .21, 1, 0)
            ->rectangle(1.7604, .21, 1, 0)
            ->rectangle(0.8854, .21, 1, 0)
            ->movePush()
            ->setAlign('center')
            ->moveRel(.4427, .0)
            ->text($order['id'])
            ->moveRel(.8646, 0)->text($core->formatDate($order['date'], 'short'))
            ->moveRel(.84375, 0)->text((int)$order['items'] == 0 ? 1 : $order['items'])
            ->moveRel(.875, 0)->text($order['subtotal'])
            ->moveRel(.8346, 0)->text($order['shipping'])
            ->moveRel(.9375, 0)->text($order['tax'])
            ->moveRel(.8229, 0)->text($order['handling'])
            ->moveRel(1.0625, 0)->text($order['total_adjustment'])
            ->moveRel(1.0625, 0)->text($order['total_payout'])
            ->movePop(0, $this->getMaxHeight()+5, 'point')
    ;
    return $this;
  }

  public function insertTotals($totals) {
    $core = Mage::helper('core');
    $_helper = Mage::helper('vendor');
    $store = Mage::app()->getStore();
    $statement = $this->getStatement();
    $vendor = $_helper->getVendor($statement->getVendorId());
    
    $this->checkPageOverflow(1.5)
            ->moveRel(-.1, 0.333)
            ->text($_helper->__("Payment Type:"))
            ->moveRel(8.4479, 0)
            ->movePush()
            ->setAlign('right')
            ->font('normal', 9)
            ->text($_helper->__("Total Revenue:"), 'down')
            ->text($_helper->__("%s Comission:", $store->getName()), 'down')
            ->font('bold')
            ->text($_helper->__("Payment To %s:", $vendor->getCompanyName()), 'down')
            ->movePop(0.5521, 0)
            ->font('normal', 9)
            ->text($totals['subtotal'], 'down')
            ->text($totals['comission_amount'], 'down')
            ->font('bold')
            ->text($totals['total_payout'], 'down')
    ;

    return $this;
  }

  public function insertTotalsPageHeader() {
    $core = Mage::helper('core'); 
    $_helper = Mage::helper('vendor');
    $store = null;
    $store = Mage::app()->getStore();
    $statement = $this->getStatement();
    $vendor = $_helper->getVendor($statement->getVendorId());

    $totals = array('items'=>0, 'subtotal'=>0, 'tax'=>0, 'shipping'=>0, 'handling'=>0, 'comission_amount'=>0, 'total_adjustment'=>0, 'total_payout'=>0);
    foreach ($this->_globalTotals as $line) {
      foreach ($totals as $k=>&$v) {
        $v += preg_replace('#[^0-9.-]#', '', $line[$k]);
      }
      unset($v);
    }

    $this->move(3.5417, 1.3542)
          ->rectangle(3.8854, 0.9583, 1, 0);

    $this->move(3.625, 1.5833)
            ->font('normal', 9)
            ->text($_helper->__("Statement Duration – %s, %s", $core->formatDate($statement->getOrderDateFrom(), 'medium'), $core->formatDate($statement->getOrderDateTo(), 'medium')));
            //->text($_helper->__("Statement Duration: %s", $statement->getStatementPeriod()));
    $this->move(3.625, 1.77083)
            ->font('normal', 9)
            ->text($_helper->__("Total Payments: %s", $totals['total_payout']));
    $this->move(3.625, 1.9583)
            ->font('normal', 9)
            ->text($_helper->__("Total %s Comission: %s", $store->getName(), $totals['comission_amount']));

    $this->font('bold', 15)
            // statement info
            ->setAlign('center')
            ->move(5.5, 2.4896)->text($_helper->__("Statement’s Totals"))
            ->move(0.91667, 3.208)
    ;

    $this->rectangle(9.15625, .375, .930, 0)
            ->rectangle(8.28125, .375, .930, 0)
            ->rectangle(7.2, .375, .930, 0)
            ->rectangle(6.1875, .375, .930, 0)
            ->rectangle(5.1875, .375, .930, 0)
            ->rectangle(4.2667, .375, .930, 0)
            ->rectangle(3.4375, .375, .930, 0)
            ->rectangle(2.6042, .375, .930, 0)
            ->rectangle(1.7604, .375, .930, 0)
            ->rectangle(0.83, .375, .930, 0)
            ->movePush()
            ->moveRel(.4427, .1)
            ->font('bold', 8)
            ->setAlign('center')
            ->text($_helper->__("Vendor Name"))
            ->moveRel(.8646, 0)->text($_helper->__("Statement Name"))
            ->moveRel(.84375, 0)->text($_helper->__("# of items"))
            ->moveRel(.875, 0)->text($_helper->__("Price"))
            ->moveRel(.9375, 0)->text($_helper->__("Tax"))
            ->moveRel(.8346, 0)->text($_helper->__("Shipping"))
            ->moveRel(.8229, 0)->text($_helper->__("Handling"))
            ->moveRel(1.0625, 0)->text($_helper->__("Adjustment"))
            ->moveRel(1.0625, 0)->text($_helper->__("Store Commission"))
            ->moveRel(1.0, 0)->text($_helper->__("Payment"))
            ->movePop(0, .375)
    ;

  }

  public function insertTotalsPage() {
    $_helper = Mage::helper('vendor');

    $this->_totalsPageNum = 0;

    $this->addPage()->insertTotalsPageHeader();

    $totals = array('items'=>0, 'subtotal'=>0, 'tax'=>0, 'shipping'=>0, 'handling'=>0, 'comission_amount'=>0, 'total_adjustment'=>0, 'total_payout'=>0);

    foreach ($this->_globalTotals as $line) {
      $this->checkPageoverflow(.5, 'insertTotalsPageHeader')
            ->setMaxHeight(0)
            ->font('normal', 8)
            ->rectangle(9.15625, .21, 1, 0)
            ->rectangle(8.28125, .21, 1, 0)
            ->rectangle(7.2, .21, 1, 0)
            ->rectangle(6.1875, .21, 1, 0)
            ->rectangle(5.1875, .21, 1, 0)
            ->rectangle(4.2667, .21, 1, 0)
            ->rectangle(3.4375, .21, 1, 0)
            ->rectangle(2.6042, .21, 1, 0)
            ->rectangle(1.7604, .21, 1, 0)
            ->rectangle(0.83, .21, 1, 0)
            ->movePush()
            ->setAlign('center')
            ->moveRel(.4427, .0)
            ->text($line['vendor_name'], null, 30)
            ->moveRel(.8646, 0)->text($line['statement_name'])
            ->moveRel(.84375, 0)->text((int)$line['items'] == 0 ? 1 : $line['items'])
            ->moveRel(.875, 0)->text($line['subtotal'])
            ->moveRel(.8346, 0)->text($line['tax'])
            ->moveRel(.9375, 0)->text($line['shipping'])
            ->moveRel(.8229, 0)->text($line['handling'])
            ->moveRel(1.0625, 0)->text($line['total_adjustment'])
            ->moveRel(1.0625, 0)->text($line['comission_amount'])
            ->moveRel(1, 0)->text($line['total_payout'])
            ->movePop(0, $this->getMaxHeight()+5, 'point')
      ;
      foreach ($totals as $k=>&$v) {
        $v += preg_replace('#[^0-9.-]#', '', $line[$k]);
      }
      unset($v);
    }

    $this->checkPageOverflow(.5, 'insertTotalsPageHeader')
            ->setMaxHeight(0)
            ->font('normal', 8)
            ->rectangle(9.15625, .21, 1, 0)
            ->rectangle(8.28125, .21, 1, 0)
            ->rectangle(7.2, .21, 1, 0)
            ->rectangle(6.1875, .21, 1, 0)
            ->rectangle(5.1875, .21, 1, 0)
            ->rectangle(4.2667, .21, 1, 0)
            ->rectangle(3.4375, .21, 1, 0)
            ->rectangle(2.6042, .21, 1, 0)
            ->rectangle(1.7604, .21, 1, 0)
            ->movePush()
            ->setAlign('center')
            ->moveRel(.4427, .0)
            ->text($_helper->__("Total"), null, 30)
            ->moveRel(.8646, 0)
            ->moveRel(.84375, 0)->text((int)$totals['items'] == 0 ? 1 : $totals['items'])
            ->moveRel(.875, 0)->price($totals['subtotal'])
            ->moveRel(.8346, 0)->price($totals['tax'])
            ->moveRel(.9375, 0)->price($totals['shipping'])
            ->moveRel(.8229, 0)->price($totals['handling'])
            ->moveRel(1.0625, 0)->price($totals['total_adjustment'])
            ->moveRel(1.0625, 0)->price($totals['comission_amount'])
            ->moveRel(1, 0)->price($totals['total_payout'])
            ->movePop(0, $this->getMaxHeight()+5, 'point')
    ;

    return $this;
  }
}