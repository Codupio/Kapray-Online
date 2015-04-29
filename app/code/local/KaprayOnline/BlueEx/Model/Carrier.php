<?php
class KaprayOnline_BlueEX_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {
    
    protected $_code = 'kaprayonlineblueex';
    
    public function collectRates(       
        Mage_Shipping_Model_Rate_Request $request
    )
    {        
        $result = Mage::getModel('shipping/rate_result');
        /* @var $result Mage_Shipping_Model_Rate_Result */
        
        /* Standard Shipping */
        $result->append($this->_getStandardShippingRate());
        
        /* Express Shipping */
        $expressWeightThreshold = $this->getConfigData('express_weight_threshold');                       
        foreach ($request->getAllItems() as $item) {
            $TotalWeight += $item->getWeight();
        }
        if($TotalWeight >= $expressWeightThreshold){
            $result->append($this->_getExpressShippingRate());
        }                     
        
        return $result;
    }
    
    protected function _getStandardShippingRate()
    {
        $rate = Mage::getModel('shipping/rate_result_method');        
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */

        $rate->setCarrier($this->_code);
        /**
         * getConfigData(config_key) returns the configuration value for the
         * carriers/[carrier_code]/[config_key]
         */
        
        $rate->setCarrierTitle($this->getConfigData('title'));

        $rate->setMethod('standand');
        $rate->setMethodTitle('Standard');

        $rate->setPrice(4.99);
        $rate->setCost(0);

        return $rate;
    }
    
    protected function _getExpressShippingRate()
    {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('express');
        $rate->setMethodTitle('Express (Next day)');
        $rate->setPrice(12.99);
        $rate->setCost(0);
        
        return $rate;
    }

    public function getAllowedMethods()
    {
        return array(
            'standard' => 'Standard',
            'express' => 'Express',
        );
    }
    
    public function isTrackingAvailable()
    {
        return true;
    }

}