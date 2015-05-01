<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Thinkhigh_VPCpaymentgateway_Block_Form_vpcpaymentgateway extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('vpcpaymentgateway/vpcpaymentgateway.phtml');
    }
    
    protected function _getConfig()
    {                 
        return Mage::getSingleton('payment/config');
    }
    
    public function getVpcAvailableTypes()
    {
        $obj=new Thinkhigh_VPCpaymentgateway_Model_Source_Payment_Vpc();
        $types =$obj->toOptionArray();
        if ($method = $this->getMethod()) {
            $availableTypes = $method->getConfigData('vpc_card');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $k=>$val) {
                    if (!in_array($val['value'], $availableTypes)) {
                        unset($types[$k]);
                    }
                }
            }
        }
        return $types;
    }
}