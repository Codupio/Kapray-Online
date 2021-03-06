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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mango_Layered_Model_System_Config_Source_Attributes {

    public function toOptionArray() {

        $_array[] = array('value' => '', 'label' => ' - - - ');
        $_array[] = array('value' => 'cat', 'label' => 'Category');
        $options = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addFieldToFilter('is_filterable', array("in" => array(1, 2)))
                ->addFieldToFilter('frontend_input', array('in' => array('select', 'multiselect','price')))
                ->addVisibleFilter();
        
        foreach ($options as $option) {
            $_array[] = array('value' => $option->getAttributeCode(), 'label' => $option->getFrontendLabel());
        }
        return $_array;
    }

}
