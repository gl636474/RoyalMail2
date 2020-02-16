<?php
/**
 * Royal Mail 2 Magento extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is copyright Gareth Ladd 2020. Not for public dissemination
 * nor use.
 *
 * DISCLAIMER
 *
 * This program is private software. It comes without any warranty, to
 * the extent permitted by applicable law. You may not copy, modify nor
 * distribute it. The author takes no responsibility for any consequences of
 * unauthorised usage of this file or any part thereof.
 */

/* @var $this Gareth_RoyalMail2_Model_Resource_Setup */
/* @var $installer Gareth_RoyalMail2_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// $db_price = Mage::getModel('gareth_royalmail2/price')->load(1);
// var_dump($db_price);
// var_dump($db_price->getService()->getName());
// var_dump($db_price->getSize()->getMaxLength());
// die("GOT PRICE");

$mar2019 = $installer->createEffectiveFrom('2019-03-25');

$size_letter = $installer->createSize('Letter', 'letter', 24, 16.5, 0.5, 100);
$size_large_letter = $installer->createSize('Large Letter', 'lgeletter', 35.3, 25, 2.5, 200);
$size_small_parcel = $installer->createSize('Small Parcel', 'smparcel', 45, 35, 16, 300);
$size_medium_parcel = $installer->createSize('Medium Parcel', 'medparcel', 61, 46, 46, 400);
$size_large_parcel = $installer->createSize('Large Parcel', 'lgeparcel', 250, 250, 250, 500);

$service_first = $installer->createService('First Class', 'rm_1stclass');
$service_second = $installer->createService('Second Class', 'rm_2ndclass');
$service_first_signed = $installer->createService('First Class Signed For', 'rm_1stclass_signed');
$service_second_signed = $installer->createService('Second Class Signed For', 'rm_2ndclass_signed');

// TODO: add effective_from_date arg to createPrice() and to all calls below.

// createProce args: service, size, max_weight, insurance, price, effective_from_date
$price1 = $installer->createPrice($service_first, $size_letter, 0.100, 20.00, 0.70, $mar2019);
$price2 = $installer->createPrice($service_second, $size_letter, 0.100, 20.00, 0.61, $mar2019);

$price3 = $installer->createPrice($service_first, $size_large_letter, 0.100, 20.00, 1.06, $mar2019);
$price4 = $installer->createPrice($service_first, $size_large_letter, 0.250, 20.00, 1.50, $mar2019);
$price5 = $installer->createPrice($service_first, $size_large_letter, 0.500, 20.00, 1.97, $mar2019);
$price6 = $installer->createPrice($service_first, $size_large_letter, 0.750, 20.00, 2.72, $mar2019);

$price7 = $installer->createPrice($service_second, $size_large_letter, 0.100, 20.00, 0.83, $mar2019);
$price8 = $installer->createPrice($service_second, $size_large_letter, 0.250, 20.00, 1.32, $mar2019);
$price9 = $installer->createPrice($service_second, $size_large_letter, 0.500, 20.00, 1.72, $mar2019);
$price10 = $installer->createPrice($service_second, $size_large_letter, 0.750, 20.00, 2.33, $mar2019);

$price11 = $installer->createPrice($service_first, $size_small_parcel, 1.00, 20.00, 3.55, $mar2019);
$price12 = $installer->createPrice($service_first, $size_small_parcel, 2.00, 20.00, 5.50, $mar2019);

$price13 = $installer->createPrice($service_second, $size_small_parcel, 1.00, 20.00, 3.00, $mar2019);
$price14 = $installer->createPrice($service_second, $size_small_parcel, 2.00, 20.00, 3.00, $mar2019);

$price15 = $installer->createPrice($service_first, $size_medium_parcel, 1.00, 20.00, 5.80, $mar2019);
$price16 = $installer->createPrice($service_first, $size_medium_parcel, 2.00, 20.00, 8.95, $mar2019);
$price17 = $installer->createPrice($service_first, $size_medium_parcel, 5.00, 20.00, 15.85, $mar2019);
$price18 = $installer->createPrice($service_first, $size_medium_parcel, 10.00, 20.00, 21.90, $mar2019);
$price19 = $installer->createPrice($service_first, $size_medium_parcel, 20.00, 20.00, 33.40, $mar2019);

$price20 = $installer->createPrice($service_second, $size_medium_parcel, 1.00, 20.00, 5.10, $mar2019);
$price21 = $installer->createPrice($service_second, $size_medium_parcel, 2.00, 20.00, 5.10, $mar2019);
$price22 = $installer->createPrice($service_second, $size_medium_parcel, 5.00, 20.00, 13.75, $mar2019);
$price23 = $installer->createPrice($service_second, $size_medium_parcel, 10.00, 20.00, 20.25, $mar2019);
$price24 = $installer->createPrice($service_second, $size_medium_parcel, 20.00, 20.00, 28.55, $mar2019);

$installer->endSetup(); 

