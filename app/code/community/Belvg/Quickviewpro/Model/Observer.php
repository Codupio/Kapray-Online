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
 * @package    Belvg_Quickviewpro
 * @copyright  Copyright (c) 2010 - 2014 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Quickviewpro_Model_Observer
{
    protected $_action;
    protected $_isQuickview = NULL;

    public function layoutLoadBefore(Varien_Event_Observer $observer)
    {
        if ($this->_ifIsQuickview()) {
            $layoutUpdate = $observer->getEvent()->getLayout()->getUpdate();
            $layoutUpdate->addHandle(Belvg_Quickviewpro_Helper_Data::HANDLE_POPUP);
        }
    }

    protected function _ifIsQuickview()
    {
        if ($this->_isQuickview === NULL) {
            $this->_isQuickview = FALSE;
            $isQuickview = Mage::app()->getRequest()->getParam('isQuickview');
            if ($isQuickview) {
                $product = Mage::registry('current_product');
                if ($product instanceof Mage_Catalog_Model_Product && $product->getId()) {
                    $this->_isQuickview = TRUE;
                }
            }
        }

        return $this->_isQuickview;
    }

    public function toHtmlBefore(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block->getNameInLayout() != 'root') {
            return;
        }

        if ($this->_ifIsQuickview()) {
            $block->setTemplate('belvg/quickviewpro/page/root.phtml');
        }
    }

    public function toHtmlAfter(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('quickviewpro');
        if (!$helper->isEnabled()) {
            return;
        }

        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Catalog_Block_Product_List || $block instanceof Mage_Catalog_Block_Product_New || $block instanceof Belvg_Productslider_Block_Product) {
            $transport  = $observer->getEvent()->getTransport();

            $collection = $block->getProductCollection();
            if (!($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract)) {
                $collection = $block->getLoadedProductCollection();
            }

            if ($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract && $collection->count()) {
                $listBlock = Mage::app()->getLayout()->createBlock('quickviewpro/product_list');
                $ids       = $collection->getColumnValues('entity_id');

                $scriptId  = rand();
                $prefix    = Belvg_Quickviewpro_Helper_Data::PRODUCT_LIST_CONTAINER_PREFIX;
                $listBlock
                    ->setIds($ids)
                    ->setContainer($prefix, $scriptId)
                    ->setTemplate('belvg/quickviewpro/script_list.phtml');

                $html = '<div class="' . $prefix . ' ' . $listBlock->getContainerClass() . '">' . $transport->getHtml() . '</div>' . $listBlock->toHtml();

                $transport->setHtml($html);
            }

        }
    }
}