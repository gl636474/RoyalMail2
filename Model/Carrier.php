<?php
/**
 * The Royal Mail Shipping Method. This class requires the following config
 * settings either in <defaults> in config.xml or set via the admin interface
 * before this class is first used:
 * 
 * <pre>
    &lt;default&gt;
        &lt;carriers&gt;
            &lt;gareth_royalmail2&gt;
                &lt;active&gt;1&lt;/active&gt;
                &lt;sort_order&gt;10&lt;/sort_order&gt;
                &lt;model&gt;gareth_royalmail2/carrier&lt;/model&gt;
                &lt;title&gt;RoyalMail&lt;/title&gt;
                &lt;sallowspecific&gt;1&lt;/sallowspecific&gt;
                &lt;specificcountry&gt;UK&lt;/specificcountry&gt;
            &lt;/gareth_royalmail2&gt;
        &lt;/carriers&gt;
    &lt;/default&gt;
   </pre>
 *
 * Note:
 * Magento will check the <sallowspecific> and <specificcountry> config settings
 * but not whether we are enabled. We must check:
 * <code>$this->getConfigFlag('active')</code> ourselves.
 * 
 * Note:
 * An exception in a carrier during checkout will cause  Magento to default to
 * the unchanged cart page (instead of showing blank or stacktrace).
 * 
 * TODO deal with parent/child products - currently they are totally ignored
 * @author gareth
 */
class Gareth_RoyalMail2_Model_Carrier
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Carriers code, as defined in parent class
     *
     * @var string
     */
    protected $_code = 'gareth_royalmail2';
    
    /**
     * A cache of all the Gareth_RoyalMail2_Model_Service objects in the DB.
     */
    protected $_services = null;
    
    /**
     * Returns all the Gareth_RoyalMail2_Model_Service objects in the DB.
     * @return array service_id=>Gareth_RoyalMail2_Model_Service
     */
    protected function getAllServices()
    {
    	if (is_null($this->_services))
    	{
    		$this->_services = array();
    		/* @var $serviceCollection Gareth_RoyalMail2_Model_Mysql4_Service_Collection */
	    	$serviceCollection = Mage::getModel('gareth_royalmail2/service')->getCollection();
	    	/* @var $service Gareth_RoyalMail2_Model_Service */
	    	foreach ($serviceCollection as $service)
	    	{
	    		$this->_services[$service->getId()] = $service;
	    	}
    	}
    	return $this->_services;
    }
    
    /**
     * Given all items in an order, returns the array:
     * <code>
     * array($total_volume, $total_weight, $max_length, $max_width, $max_depth)
     * </code>
     *
     * which is the total volume and weight of all the items in the request,
     * counting for the quantity of each item.
     *
     * @param array $all_items array of Mage_Sales_Model_Quote_Item
     * @return array array($total_volume, $total_weight, $max_length, $max_width, $max_depth)
     */
    protected function calculateTotalVolumeAndWeight(array $all_items)
    {
    	$default_length = $this->getConfigData('default_length');
    	$default_width = $this->getConfigData('default_width');
    	$default_depth = $this->getConfigData('default_depth');
    	$default_weight = $this->getConfigData('default_weight');
    	
    	$total_weight = 0;
    	$total_volume = 0;
    	$max_length = 0;
    	$max_width = 0;
    	$max_depth = 0;
    	
    	/* @var Mage_Sales_Model_Quote_Item $item */
    	foreach ($all_items as $item)
    	{
    		// Pretend this item does not exist for weight/dimensions/price
    		// calcs if:
    		//  * it is virtual (not physically shipped)
    		//  * if it has free shipping

    		if ($item->getProduct()->isVirtual() || $item->getFreeShipping())
    		{
    			$item_name = $item->getName();
    			Mage::log("Skipping virtual/free item: $item_name", Zend_Log::INFO, 'gareth.log');
    			continue;
    		}
    		
    		// ONE of the next two if blocks will apply for each parent/child
    		// product hierarchy - we either process the parent and ignore the
    		// the children (if shipped together) OR ignore the parent and
    		// process the children (if shipped separately)
    		
    		if ($item->getHasChildren() && $item->isShipSeparately())
    		{
    			// ignore this parent - will process each child individually
    			$item_name = $item->getName();
    			Mage::log("Skipping parent item where children shipped separately: $item_name", Zend_Log::INFO, 'gareth.log');
    			continue;
    		}
    		
    		/* @var $parentItem Mage_Sales_Model_Quote_Item */
    		$parentItem = $item->getParentItem();
    		if (!is_null($parentItem) && !$parentItem->isShipSeparately())
    		{
    			// ignore this child - it will be shipped with parent
    			$item_name = $item->getName();
    			Mage::log("Skipping child item where children shipped with parent: $item_name", Zend_Log::INFO, 'gareth.log');
    			continue;
    		}
    		
    		// must load full product to get EAVs (e.g. package_depth) loaded
    		// otherwise they will be null
    		$productId = $item->getProduct()->getId();
    		$full_product = Mage::getModel('catalog/product')->load($productId);
    		
    		$product_length = $full_product->getPackageHeight();
    		if (empty($product_length))
    		{
    			Mage::log("Product length not set, using default: $default_length", Zend_log::DEBUG, 'gareth.log');
    			$product_length = $default_length;
    		}
    		else 
    		{
    			Mage::log("Product length: $product_length", Zend_log::DEBUG, 'gareth.log');
    		}
    		
    		$product_width = $full_product->getPackageWidth();
    		if (empty($product_width))
    		{
    			Mage::log("Product width not set, using default: $default_width", Zend_log::DEBUG, 'gareth.log');
    			$product_width = $default_width;
     		}
    		else
    		{
    			Mage::log("Product width: $product_width", Zend_log::DEBUG, 'gareth.log');
    		}
    		
    		$product_depth = $full_product->getPackageDepth();	
    		if (empty($product_depth))
    		{
    			Mage::log("Product depth not set, using default: $default_depth", Zend_log::DEBUG, 'gareth.log');
    			$product_depth = $default_depth;
    		}
    		else
    		{
    			Mage::log("Product depth: $product_depth", Zend_log::DEBUG, 'gareth.log');
    		}
    		
    		$item_weight = $item->getWeight();
    		if (is_numeric($item_weight) && $item_weight > 0)
    		{
    			Mage::log("Product weight: $item_weight", Zend_log::DEBUG, 'gareth.log');
    		}
    		else 
    		{
    			Mage::log("Product weight not set, using default: $default_weight", Zend_log::DEBUG, 'gareth.log');
    			$item_weight = $default_weight;
    		}
    		
    		$product_quantity = $item->getQty();
    		
    		// NB getWeight() returns the weight of one product
    		$item_weight *= $product_quantity;
    		$total_weight += $item_weight;
    		
    		// NB getPackageDepth() etc. returns the depth of one product
    		$product_volume = $product_depth * $product_length * $product_width;
    		$item_volume = $product_volume * $product_quantity;
    		$total_volume += $item_volume;
    		
    		$max_length = max($max_length, $product_length);
    		$max_width = max($max_width, $product_width);
    		$max_depth = max($max_depth, $product_depth);
    	}
    	
    	Mage::log('Total volume: '.$total_volume, Zend_Log::INFO, 'gareth.log');
    	Mage::log('Total weight: '.$total_weight, Zend_Log::INFO, 'gareth.log');
    	Mage::log("Max dimentions: $max_length x $max_width x $max_depth", Zend_Log::DEBUG, 'gareth.log');
    	
    	return array($total_volume, $total_weight, $max_length, $max_width, $max_depth);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see Mage_Shipping_Model_Carrier_Interface::getAllowedMethods()
     */
    public function getAllowedMethods()
    {
    	$methods = array();
    	/* @var $service Gareth_RoyalMail2_Model_Service */
    	foreach ($this->getAllServices() as $service)
    	{
    		$code = $service->getCode();
    		$name = $service->getName();
    		$methods[$code] = $name;
    	}
    	return $methods;
    }
    
    /**
     * {@inheritDoc}
     * @see Mage_Shipping_Model_Carrier_Abstract::collectRates()
     * @see Varien_Db_Adapter_Pdo_Mysql::prepareSqlCondition()
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
    	// Check system config to see if we are enabled
    	if (!$this->isActive()) {
    		return false;
    	}
    	
    	Mage::Log("RoyalMail2_Carrier::collectRates(): delivery for country ID: ".$request->getDestCountryId(), Zend_Log::DEBUG, 'gareth.log');
    	
    	// inspect all items for total weight/volume and max width/height/depth
    	list($total_volume, $total_weight, $max_length, $max_width, $max_depth) = $this->calculateTotalVolumeAndWeight($request->getAllItems());
    	
    	// First see what size letter/parcel we are. Search for sizes where
    	// length/width/thickness >= max_height/width/depth order so smallest
    	// size returned first.
    	
    	/* @var $sizeCollection Gareth_RoyalMail2_Model_Mysql4_Size_Collection */
    	$sizeCollection = Mage::getModel('gareth_royalmail2/size')->getCollection();
    	$sizeCollection->addFieldToFilter('max_length', array('gteq' => $max_length));
    	$sizeCollection->addFieldToFilter('max_width', array('gteq' => $max_width));
    	$sizeCollection->addFieldToFilter('max_thickness', array('gteq' => $max_depth));
    	$sizeCollection->getSelect()->where('(max_length * max_width * max_thickness >= ?)', $total_volume);
    	$sizeCollection->setOrder('`order`', 'ASC'); //  quote SQL reserved word
    	$sizeCollection->setPageSize(1);
    	
    	/* @var $size Gareth_RoyalMail2_Model_Size */
    	$size = $sizeCollection->getFirstItem();
    	
    	// Next, determine which set of prices to use. Search effective from
    	// dates for the latest (maximum) before now.
    	/* @var $dateCollection Gareth_RoyalMail2_Model_Mysql4_Effectivefrom_Collection */
    	$dateCollection = Mage::getModel('gareth_royalmail2/effectivefrom')->getCollection();
    	$now = date_create("now", timezone_open("Europe/London"))->format('Y-m-d');
    	$dateCollection->addFieldToFilter('date_from',array('to'=>$now));  
    	$dateCollection->setOrder('date_from', 'DESC'); // largest first
    	$dateCollection->setPageSize(1);
    	
    	/* @var $effectiveDate Gareth_RoyalMail2_Model_Effectivefrom */
    	$effectiveDate = $dateCollection->getFirstItem();
    	
    	// Now get the minimum price for each service:
    	// * for the above $size
    	// * for the effective from date above
    	// * where the maximum weight for that price is greater than our weight
    	
    	/* @var $priceCollection Gareth_RoyalMail2_Model_Mysql4_Price_Collection */
    	$priceCollection = Mage::getModel('gareth_royalmail2/price')->getCollection();
    	$priceCollection->removeAllFieldsFromSelect();
    	$priceCollection->getSelect()->columns(
    			array(	'MIN(price) as price',
    					'max_weight',
    					'insurance',
    					'service_id',
    					'size_id',
    			));

    	$priceCollection->addFieldToFilter('size_id', $size->getId());
    	
    	$priceCollection->addFieldToFilter('effective_from_id', $effectiveDate->getId());
    	
    	$priceCollection->addFieldToFilter('max_weight', array('gteq'=>$total_weight));
    	
    	$priceCollection->getSelect()->group('service_id');
    	
    	/* @var $rate_result Mage_Shipping_Model_Rate_Result  */
    	/* @var $price Gareth_RoyalMail2_Model_Price */
    	$rate_result = Mage::getModel('shipping/rate_result');  	
		foreach ($priceCollection as $price)  
		{
			// TODO: override price collection to also load size, service and
			// effectivedate i.e. join to size and service so all 3 objects can
			// be loaded from a single SQL statement.
			
			$serviceId = $price->getServiceId();
			/* @var $service Gareth_RoyalMail2_Model_Service */
			$service = $this->getAllServices()[$serviceId];
			
			$shippingPrice = $this->getFinalPriceWithHandlingFee($price->getPrice());
			
			/** @var Mage_Shipping_Model_Rate_Result_Method $rate */
			$method = Mage::getModel('shipping/rate_result_method');
			$method->setCarrier($this->_code);
			$method->setCarrierTitle($this->getConfigData('title'));
			$method->setMethod($service->getCode());
			$method->setMethodTitle($service->getName());
			$method->setPrice($shippingPrice);
			$method->setCost($shippingPrice);
			
			$rate_result->append($method);
		}
		return $rate_result;
    }
}
