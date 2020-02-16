<?php
/**
 * Royal Maio 2 Magento extension
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

/* @var $db Varien_Db_Adapter_Interface */
$db = $installer->getConnection();

$priceTableName = $installer->getTable('gareth_royalmail2/price');
$serviceTableName = $installer->getTable('gareth_royalmail2/service');
$sizeTableName = $installer->getTable('gareth_royalmail2/size');
$effectivefromTableName = $installer->getTable('gareth_royalmail2/effectivefrom');

$db->dropTable($priceTableName);
$db->dropTable($serviceTableName);
$db->dropTable($sizeTableName);
$db->dropTable($effectivefromTableName);



// *****************************************************************************
// ** Size Table
// *****************************************************************************

/* @var $size_table Varien_Db_Ddl_Table */
$size_table = $db->newTable($sizeTableName);

$size_id_col_name = 'id';
$size_table->addColumn($size_id_col_name, Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
		'primary' => true,
		'identity' => true, // implies auto_increment
), 'DB ID - not used in any queries');

$size_name_col_name = 'name';
$size_table->addColumn($size_name_col_name, Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable' => false,
), 'Royal Mail name for this size');

$size_code_col_name = 'code';
$size_table->addColumn($size_code_col_name, Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable' => false,
), 'internal name for this size');

$order_col_name = 'order';
$size_table->addColumn($order_col_name, Varien_Db_Ddl_Table::TYPE_INTEGER, 255, array(
		'unsigned' => true,
		'nullable' => false,
), 'The relative ordering of this size from smallest (zero) to biggest (positive)');

$max_length_col_name = 'max_length';
$size_table->addColumn($max_length_col_name, Varien_Db_Ddl_Table::TYPE_DOUBLE, null, array(
		'nullable' => false,
), 'The max package length for this size');

$max_width_col_name = 'max_width';
$size_table->addColumn($max_width_col_name, Varien_Db_Ddl_Table::TYPE_DOUBLE, null, array(
		'nullable' => false,
), 'The max package width for this size');

$max_thickness_col_name = 'max_thickness';
$size_table->addColumn($max_thickness_col_name, Varien_Db_Ddl_Table::TYPE_DOUBLE, null, array(
		'nullable' => false,
), 'The max package thickness for this size');

// create indexes on fields in where and order by clauses
$size_table_index_fields = array(
		$max_length_col_name,
		$max_width_col_name,
		$max_thickness_col_name,
		$order_col_name);
$size_table_index_name = $installer->getIdxName($sizeTableName, $size_table_index_fields);
$size_table->addIndex($size_table_index_name,
		$size_table_index_fields,
		array('type'=>Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));

$db->createTable($size_table);



// *****************************************************************************
// ** Service Table
// *****************************************************************************

/* @var $service_table Varien_Db_Ddl_Table */
$service_table = $db->newTable($serviceTableName);

$service_id_col_name = 'id';
$service_table->addColumn($service_id_col_name, Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
		'primary' => true,
		'identity' => true, // implies auto_increment
), 'DB ID - not used in any queries');

$service_name_col_name = 'name';
$service_table->addColumn($service_name_col_name, Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable' => false,
), 'Royal Mail name for this service eg first class');

$service_code_col_name = 'code';
$service_table->addColumn($service_code_col_name, Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable' => false,
), 'short code for this service');

$db->createTable($service_table);



// *****************************************************************************
// ** Effective From Table
// *****************************************************************************

/* @var $service_table Varien_Db_Ddl_Table */
$effectivefrom_table = $db->newTable($effectivefromTableName);

$effectivefrom_id_col_name = 'id';
$effectivefrom_table->addColumn($effectivefrom_id_col_name, Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
		'primary' => true,
		'identity' => true, // implies auto_increment
), 'DB ID - not used in any queries');

$date_from_col_name = 'date_from';
$effectivefrom_table->addColumn($date_from_col_name, Varien_Db_Ddl_Table::TYPE_DATE, null, array(
		'nullable' => false,
), 'The date prices come into effect');

$db->createTable($effectivefrom_table);



// *****************************************************************************
// ** Price Table
// *****************************************************************************

/* @var $price_table Varien_Db_Ddl_Table */
$price_table = $db->newTable($priceTableName);

$price_id_col_name = 'id';
$price_table->addColumn($price_id_col_name, Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
		'primary' => true,
		'identity' => true, // implies auto_increment
), 'DB ID - not used in any queries');

$price_col_name = 'price';
$price_table->addColumn($price_col_name, Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable' => false,
), 'The price for this max weight and size combination for this service');

$max_weight_col_name = 'max_weight';
$price_table->addColumn($max_weight_col_name, Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable' => false,
), 'Max weight in KG for this price/service');

$insurance_col_name = 'insurance';
$price_table->addColumn($insurance_col_name, Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable' => false,
), 'The maximum compensation for this price/service');

// Create foreign key from price table to size table
$price_table_size_fk_col_name = "size_id";
$price_table->addColumn($price_table_size_fk_col_name, Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
), "Foreign Key to $sizeTableName.id");

$price_table_size_fk_name = $installer->getFkName(
		$priceTableName, $price_table_size_fk_col_name,
		$sizeTableName, $size_id_col_name);
$price_table->addForeignKey($price_table_size_fk_name,
		$price_table_size_fk_col_name, $sizeTableName, $size_id_col_name);

// Create foreign key from price table to service table
$price_table_service_fk_col_name = "service_id";
$price_table->addColumn($price_table_service_fk_col_name, Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
), "Foreign Key to $serviceTableName.id");

$price_table_service_fk_name = $installer->getFkName(
		$priceTableName, $price_table_service_fk_col_name,
		$serviceTableName, $service_id_col_name);
$price_table->addForeignKey($price_table_service_fk_name,
		$price_table_service_fk_col_name, $serviceTableName, $service_id_col_name);

// Create foreign key from price table to effective from table
$price_table_effective_from_fk_col_name = "effective_from_id";
$price_table->addColumn($price_table_effective_from_fk_col_name, Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
), "Foreign Key to $effectivefromTableName.id");

$price_table_effective_from_fk_name = $installer->getFkName(
		$priceTableName, $price_table_effective_from_fk_col_name,
		$effectivefromTableName, $effectivefrom_id_col_name);
$price_table->addForeignKey($price_table_effective_from_fk_name,
		$price_table_effective_from_fk_col_name, $effectivefromTableName, $effectivefrom_id_col_name);
$db->createTable($price_table);



$installer->endSetup(); 
