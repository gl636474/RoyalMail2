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
    
    public function getAllowedMethods()
    {
        return array(
            'standard'    =>  'Standard delivery'
        );
    }
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod('large');
        $method->setMethodTitle('Standard delivery');
        $method->setPrice(1.23);
        $method->setCost(0);
        
        /** @var Mage_Shipping_Model_Rate_Result $result */
        $rate_result = Mage::getModel('shipping/rate_result');
        $rate_result->append($method);
        
        return $rate_result;
    }
}
