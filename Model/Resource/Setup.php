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

require_once 'app/Mage.php';


/**
 * Helper methods for install and upgrade scripts in
 * <code>data/royalmail2_setup</code> and 
 * <code>sql/royalmail2_setup</code>. The class running the sql/data setup
 * <code>$this</code> or <code>$installer</code> will be an instance of this
 * class, assuming the following in the <code>config.xml</code> file:
 * 
 * <pre>
 * &lt;resources&gt;
 *  &lt;!-- ... --&gt;
 *  &lt;royalmail2_setup&gt;
 *      &lt;setup&gt;
 *          &lt;module&gt;Gareth_RoyalMail2&lt;/module&gt;
 *          &lt;class&gt;Gareth_RoyalMail2_Model_Resource_Mysql4_Setup&lt;/class&gt;
 *      &lt;/setup&gt;
 *      &lt;!-- ... --&gt;
 * </pre>
 */
class Gareth_RoyalMail2_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
	/**
	 * @var $timeZone DateTimeZone Cached DateTimeZone instance
	 */
	private $timeZone = null;
	
	protected function getTimeZone()
	{
		if (is_null($this->timeZone))
		{
			$this->timeZone = timezone_open('Europe/London');
		}
		return $this->timeZone;
	}
	
	/**
	 * Creates a new size from the arguments.
	 * 
	 * @param string $name Royal Mail name for the package size classification, e.g. "Large Letter"
	 * @param string $code internal name for the package size classification, e.g. "lgeletter"
	 * @param int $max_length maximum length (largest dimention)
	 * @param int $max_width maximum width (middle dimention)
	 * @param int $max_thickness maximum length (smallest dimention)
	 * @param int $sort_order
	 * @return Gareth_RoyalMail2_Model_Size new Size instance (already persisted to DB)
	 */
	public function createSize($name, $code, $max_length, $max_width, $max_thickness, $sort_order)
	{
		/* @var $size Gareth_RoyalMail2_Model_Size */
		$size = Mage::getModel('gareth_royalmail2/size');
		$size->setName($name);
		$size->setCode($code);
		$size->setMaxLength($max_length);
		$size->setMaxWidth($max_width);
		$size->setMaxThickness($max_thickness);
		$size->setOrder(floor($sort_order));
		
		$size->save();
		return $size;
	}
	
	/**
	 * Creates a new service from the arguments.
	 *
	 * @param string $name Royal Mail name for the service, e.g. "First Class Signed For"
	 * @param string $code short internal code for the service, e.g. "rm_1stclass_signed"
	 * @return Gareth_RoyalMail2_Model_Service new Service instance (already persisted to DB)
	 */
	public function createService($name, $code)
	{
		/* @var $service Gareth_RoyalMail2_Model_Service */
		$service = Mage::getModel('gareth_royalmail2/service');
		$service->setName($name);
		$service->setCode($code);
		
		$service->save();
		return $service;
	}
	
	/**
	 * Creates a new effective from date object from the arguments.
	 *
	 * @param $date string The date/time a set of prices comes into force, e.g. "2019-04-01"
	 * @return Gareth_RoyalMail2_Model_Effectivefrom new EffectiveFrom instance (already persisted to DB)
	 */
	public function createEffectiveFrom($date)
	{
		if (date_create($date, $this->getTimeZone()) == false)
		{
			var_dump($date);
			die("Invalid date string passed to createEffectiveFrom() - see above.");
		}
		
		/* @var $service Gareth_RoyalMail2_Model_Effectivefrom */
		$effectiveFrom = Mage::getModel('gareth_royalmail2/effectivefrom');
		$effectiveFrom->setDateFrom($date);
		
		$effectiveFrom->save();
		return $effectiveFrom;
	}
	
	public function createPrice($service, $size, $max_wieght, $insurance, $price, $effective_from)
	{
		/* @var $price_object Gareth_RoyalMail2_Model_Price */
		$price_object = Mage::getModel('gareth_royalmail2/price');
		$price_object->setService($service);
		$price_object->setSize($size);
		$price_object->setEffectiveFrom($effective_from);
		$price_object->setMaxWeight($max_wieght);
		$price_object->setInsurance($insurance);
		$price_object->setPrice($price);
		
		$price_object->save();
		return $price_object;
	}
}
