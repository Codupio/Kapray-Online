<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/**********************************************
 *        MAGENTO EDITION USAGE NOTICE        *
 **********************************************/
/* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/**********************************************
 *        DISCLAIMER                          *
 **********************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 **********************************************
 * @category   Belvg
 * @package    Belvg_ProductZoomLight
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_ProductZoomLight_Model_Source_Media_Fancybox_Title
{
    /**
     * The position of title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'float',   'label' => Mage::helper('adminhtml')->__('Float')),
            array('value' => 'outside', 'label' => Mage::helper('adminhtml')->__('Outside')),
            array('value' => 'inside',  'label' => Mage::helper('adminhtml')->__('Inside')),
            array('value' => 'over',    'label' => Mage::helper('adminhtml')->__('Over')),
        );
    }

}
