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
 * @method double getPrice()
 * @method void setPrice(double $price)
 * @method date getDateFrom()
 * @method void setDateFrom(date $validFromDate)
 * @method double getMaxWeight()
 * @method void setMaxWeight(double $kgs)
 * @method double getInsurance()
 * @method void setInsurance(double $pounds)
 * @method Gareth_RoyalMail2_Model_Size getSize()
 * @method void setSize(Gareth_RoyalMail2_Model_Size $size)
 * @method Gareth_RoyalMail2_Model_Service getService()
 * @method void setService(Gareth_RoyalMail2_Model_Service $service)
 * @method Gareth_RoyalMail2_Model_Effectivefrom getEffectiveFrom()
 * @method void setEffectiveFrom(Gareth_RoyalMail2_Model_Effectivefrom $fromDate)
 */
class Gareth_RoyalMail2_Model_Price extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('gareth_royalmail2/price');
	}
	
	/**
	 * Hook to also save the Service and Size foreign keys and the child objects
	 * as well (must be written to DB before value inserted into FK column to
	 * avoid the FK constraint violation.
	 *  
	 * {@inheritDoc}
	 * @see Mage_Core_Model_Abstract::_beforeSave()
	 */
	public function _beforeSave()
	{
		parent::_beforeSave();
		
		/* @var $size Gareth_RoyalMail2_Model_Service */
		$service = $this->getService();
		if (!is_null($service))
		{
			$service->save();
			$serviceId = $service->getId();
			$this->setServiceId($serviceId);
		}
		
		/* @var $size Gareth_RoyalMail2_Model_Size */
		$size = $this->getSize();
		if (!is_null($size))
		{
			$size->save();
			$sizeId = $size->getId();
			$this->setSizeId($sizeId);
		}
		
		/* @var $effectiveFrom Gareth_RoyalMail2_Model_Effectivefrom */
		$effectiveFrom = $this->getEffectiveFrom();
		if (!is_null($effectiveFrom))
		{
			$effectiveFrom->save();
			$effectiveFromId = $effectiveFrom->getId();
			$this->setEffectiveFromId($effectiveFromId);
		}
	}
	
	/**
	 * Hook to load the Service and Size model objects from their foreign key 
	 * values.
	 * 
	 * {@inheritDoc}
	 * @see Mage_Core_Model_Abstract::_afterLoad()
	 */
	public function _afterLoad()
	{
		parent::_afterLoad();
		
		$serviceId = $this->getServiceId();
		if (!is_null($serviceId))
		{
			$service = Mage::getModel('gareth_royalmail2/service')->load($serviceId);
			$this->setService($service);
		}
		
		$sizeId = $this->getSizeId();
		if (!is_null($sizeId))
		{
			$size = Mage::getModel('gareth_royalmail2/size')->load($sizeId);
			$this->setSize($size);
		}
		
		$effectiveFromId = $this->getEffectiveFromId();
		if (!is_null($effectiveFromId))
		{
			$effectiveFrom = Mage::getModel('gareth_royalmail2/effectivefrom')->load($effectiveFromId);
			$this->setEffectiveFrom($effectiveFrom);
		}
	}
}
