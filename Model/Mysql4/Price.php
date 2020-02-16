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
class Gareth_RoyalMail2_Model_Mysql4_Price extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		// Note that id refers to the key field in the database table.
		$this->_init('gareth_royalmail2/price', 'id');
	}
}
