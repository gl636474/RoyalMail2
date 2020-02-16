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
 * 
 * @method string getName()
 * @method void setName(string $name)
 * @method int getOrder()
 * @method void setOrder(int $value)
 * @method double getMaxLength()
 * @method void setMaxLength(double $value)
 * @method double getMaxWidth()
 * @method void setMaxWidth(double $value)
 * @method double getMaxThickness()
 * @method void setMaxThickness(double $value)
 */
class Gareth_RoyalMail2_Model_Size extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('gareth_royalmail2/size');
	}
}
