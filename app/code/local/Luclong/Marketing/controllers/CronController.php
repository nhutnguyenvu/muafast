<?php

class Luclong_Marketing_CronController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
        //$this->uploadAction();
    }
    
    public function savelikeAction($link,$id) {
        
        $facebook = file_get_contents('http://api.facebook.com/restserver.php?method=links.getStats&urls='.$link.'');
        $fbbegin = '<like_count>'; $fbend = '</like_count>';
        $fbpage = $facebook;
        $fbparts = explode($fbbegin,$fbpage);
        $fbpage = $fbparts[1];
        $fbparts = explode($fbend,$fbpage);
        $fbcount = $fbparts[0];
        if($fbcount != '') {

			$sql = "UPDATE ".Mage::getSingleton('core/resource')->getTableName('marketing')." SET count_like=$fbcount WHERE marketing_id='$id'";
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');     
			try {
				$connection->query($sql);
				//die('update successfull');
				echo $id.'--'.'update successfull<br>';
			} catch (Exception $e){
				echo $e->getMessage();
			}
		}else echo"Update không thành công";
    }
    
    public function cronlikeAction(){
        $collection = Mage::getModel('marketing/marketing')->getCollection()->addFilter('status','1');
        $url = Mage::getUrl("marketing/index/detail");
        
        if(count($collection)):
            foreach($collection as $key=>$value):
                $link = $url.'?imageId='.$value['marketing_id'];
                $this->savelikeAction($link,$value['marketing_id']);
            endforeach;
        endif;
    }
}