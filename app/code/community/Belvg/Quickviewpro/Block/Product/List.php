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

//class Belvg_Quickviewpro_Block_Product_List extends Mage_Catalog_Block_Product_List
class Belvg_Quickviewpro_Block_Product_List extends Mage_Core_Block_Template
{
    protected $_ids      = FALSE;
    protected $_prefix   = FALSE;
    protected $_scriptId = FALSE;

    protected function _getLoadedProductCollection()
    {
        if (Mage::app()->getFrontController()->getRequest()->getModuleName() == 'catalogsearch') {
            $layer = Mage::getSingleton('catalogsearch/layer');
        } else {
            $layer = Mage::getSingleton('catalog/layer');
        }
        

        $collection = $layer->getProductCollection();
    }

    public function setIds($ids) 
    {
        $this->_ids = $ids;

        return $this;
    }

    public function setContainer($prefix, $scriptId)
    {
        $this->_prefix   = $prefix;
        $this->_scriptId = $scriptId;

        return $this;
    }

    public function getScriptId()
    {
        if (!$this->_scriptId) {
            $this->_scriptId = rand();
        }

        return $this->_scriptId;
    }

    public function getPrefix()
    {
        if (!$this->_prefix) {
            $this->_prefix = Belvg_Quickviewpro_Helper_Data::PRODUCT_LIST_CONTAINER_PREFIX;
        }

        return $this->_prefix;
    }

    public function getContainerClass()
    {
        return $this->_prefix . $this->_scriptId;
    }

    /**
     * Getting current product ids list
     * @return array
     */
    public function getProductIds() 
    {
        // ids have to be obtained here Belvg_Quickviewpro_Model_Observer::toHtmlAfter(),
        // and transmitted through setIds($ids)
        if ($this->_ids === FALSE) {
            // if for some reason have not been set ids
            $collection = $this->_getLoadedProductCollection();
            $this->_ids = $collection->getColumnValues('entity_id');
        }

        return $this->_ids;
    }
}