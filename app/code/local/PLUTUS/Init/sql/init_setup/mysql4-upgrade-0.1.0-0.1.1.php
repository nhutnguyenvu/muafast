<?php

$new_regions = array(
    'DNa' => "Đà Nẵng",

);

// specify country code for new regions
$country_code = 'VN';

// specify locale
$locale = 'vi_VN';

// create our core_write conection object
$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

// iterate our new regions
foreach ($new_regions as $region_code => $region_name) {

    // insert region
    $sql = "INSERT INTO `directory_country_region` (`region_id`,`country_id`,`code`,`default_name`) VALUES (NULL,?,?,?)";
    $connection->query($sql, array($country_code, $region_code, $region_name));

    // get new region id for next query
    $region_id = $connection->lastInsertId();

    // insert region name
    $sql = "INSERT INTO `directory_country_region_name` (`locale`,`region_id`,`name`) VALUES (?,?,?)";
    $connection->query($sql, array($locale, $region_id, $region_name));
}
?>