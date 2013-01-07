<?php

class NBSSystem_Nitrogento_Model_Optimization_Cdn
{
    /**
    * Cdn HTML
    *
    * @param   string $html (normal)
    * @return   string $html (cdn)
    */
    public function cdnHtml($html) 
    {
        Mage::register('number',1);
        
        $baseUrl = Mage::getStoreConfig('nitrogento/optimization_cdn/base');
        
        $patternSearchImg="/(<img[\d\D]+?src\=(\'|\")http\:\/\/)($baseUrl\/)([\d\D]+?)((\'|\")([\d\D]+?)\/>)/i";
        $patternSearchScript="/(<script type=\"text\/javascript\" src\=(\'|\")http\:\/\/)($baseUrl\/)([\d\D]+?)((\'|\")([\d\D]+?)<\/script>)/i";
        $patternSearchLink="/(<link[\d\D]+?href\=(\'|\")http\:\/\/)($baseUrl\/)([\d\D]+?)((\'|\")([\d\D]+?)\/>)/i";
        
        $html = preg_replace_callback($patternSearchImg,"NBSSystem_Nitrogento_Model_Optimization_Cdn::reTravaille",$html);
        $html = preg_replace_callback($patternSearchScript,"NBSSystem_Nitrogento_Model_Optimization_Cdn::reTravaille",$html);
        $html = preg_replace_callback($patternSearchLink,"NBSSystem_Nitrogento_Model_Optimization_Cdn::reTravaille",$html);
        
        return $html;
    }
    
    //function qui genere le pattern de remplacement 
    private static function reTravaille($matches)
    {
        $nombreCdn = Mage::getStoreConfig('nitrogento/optimization_cdn/number'); //BO
        $futureBaseUrl= Mage::getStoreConfig('nitrogento/optimization_cdn/cdn'); //BO
        
        //init separation
        $newUrl = explode('[X]',$futureBaseUrl);
        $newUrlArray=array();
        
        $number = Mage::registry('number');
        if($number>$nombreCdn) {$number=1;}
        
        if(count($newUrl)>1)
        {
            $newRandomUrl= $newUrl[0].$number.$newUrl[1].'/';
        }
        else
        {
            $newRandomUrl = $newUrl[0].'/';
        }
        $number++; Mage::unregister('number');
        Mage::register('number',$number);
        
        return $matches[1].$newRandomUrl.$matches[4].$matches[5];
    }
}