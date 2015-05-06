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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layer category filter
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mango_Layered_Model_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category {

    protected $_categories_tree = "";

    public function getRequestVar() {
        return "cat";
    }

    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    protected $_appliedCategory = array();

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {

        if ($_module = Mage::app()->getRequest()->getModuleName() == "catalogsearch")
            return $this->applySimple($request, $filterBlock);

        $filter = $request->getParam($this->getRequestVar());
        if (preg_match('/^[0-9,]+$/', $filter)) {
            $filter = array_unique(explode(',', $filter));
        }
        if (!is_array($filter) || !count($filter)) {
            return $this;
        }
        $_product_ids = array();
        $category = $this->getCategory();
        Mage::register('current_category_filter', $category);
        foreach ($filter as $_cat_id) {
            $_cat_filter = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($_cat_id);
            if ($this->_isValidCategory($_cat_filter)) {
                $this->_appliedCategory[] = $_cat_filter;
                $_new_product_ids = Mage::getResourceModel('catalog/url')->getProductIdsByCategory($_cat_id);
                $_product_ids = array_merge($_product_ids, $_new_product_ids);
            }
        }

        $this->getLayer()->getProductCollection()->addIdFilter($_product_ids);


        foreach ($this->_appliedCategory as $_cat_filter) {
            $this->getLayer()->getState()->addFilter($this->_createItem($_cat_filter->getName(), $_cat_filter->getId()));
        }
        return $this;
    }

    public function applySimple(Zend_Controller_Request_Abstract $request, $filterBlock) {

        $filter = $request->getParam($this->getRequestVar());
        if (preg_match('/^[0-9,]+$/', $filter)) {
            $filter = array_unique(explode(',', $filter));
        }
        if (!is_array($filter) || !count($filter)) {
            return $this;
        }

        $filter = end($filter);
        if (!$filter) {
            return $this;
        }
        $this->_categoryId = $filter;

        Mage::register('current_category_filter', $this->getCategory(), true);

        $this->_appliedCategory[] = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($filter);
        foreach ($this->_appliedCategory as $_cat_filter) {

            if ($this->_isValidCategory($_cat_filter)) {
                $this->getLayer()->getProductCollection()
                        ->addCategoryFilter($_cat_filter);

                $this->getLayer()->getState()->addFilter(
                        $this->_createItem($_cat_filter->getName(), $filter)
                );
            }
        }


        return $this;
    }

    /**
     * Validate category for be using as filter
     *
     * @param   Mage_Catalog_Model_Category $category
     * @return unknown
     */
    protected function _isValidCategory($category) {
        return $category->getId();
    }

    /**
     * Get filter name
     *
     * @return string
     */
    public function getName() {
        return Mage::helper('catalog')->__('Category');
    }

    /**
     * Get selected category object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory() {


        if (Mage::registry("current_category"))
            return Mage::registry("current_category");
        else {
            if (!is_null($this->_categoryId)) {
                $category = Mage::getModel('catalog/category')
                        ->load($this->_categoryId);
                if ($category->getId()) {
                    return $category;
                }
            }
            return $this->getLayer()->getCurrentCategory();
        }
    }

    /**
     * Get filter value for reset current filter state
     * if(Mage::registry("current_category")) return Mage::registry("current_category");
      else{
      if (!is_null($this->_categoryId)) {
      $category = Mage::getModel('catalog/category')
      ->load($this->_categoryId);
      if ($category->getId()) {
      return $category;
      }
      }
      return $this->getLayer()->getCurrentCategory();
      }
     * @return mixed
     */
    public function getResetValue() {
        if ($this->_appliedCategory && is_array($this->_appliedCategory) && count($this->_appliedCategory)) {
            /**
             * Revert path ids
             */
            foreach ($this->_appliedCategory as $_app_cat) {
                $pathIds = array_reverse($_app_cat->getPathIds());
                $curCategoryId = $this->getLayer()->getCurrentCategory()->getId();
                if (isset($pathIds[1]) && $pathIds[1] != $curCategoryId) {
                    return $pathIds[1];
                }
            }
        }
        return null;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData() {
        
        if ( Mage::app()->getRequest()->getModuleName() == "catalogsearch")
            return $this->_getItemsDataSearch();

        $key = $this->getLayer()->getStateKey() . '_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
            $category = $this->getCategory();
            $categories = $this->_getChildrenCategories($category); //$category->getChildrenCategories();
            $data = array();
            foreach ($categories as $category) {
                if ($category->getIsActive()) {// && $category->getProductCount()) {
                    $_count = Mage::getModel('catalog/layer')->setCurrentCategory($category)->getProductCollection()->getSize();
                    if (!Mage::getStoreConfig("ajaxlist/ajaxlist/show_no_count") && !$_count) {
                        continue;
                    }
                    $_extra_items = $this->_renderCategoryMenuItemHtml($category);
                    if (!empty($_extra_items)) {
                        foreach ($_extra_items as $i) {
                            array_push($data, $i);
                        }
                    }
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    public function getCategoriesTree() {
        return $this->_categories_tree;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsDataSearch() {
        //return parent::_getItemsData();
        
        $key = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
            $categoty   = $this->getCategory();
            //echo $categoty->getId() . "--";
            $categories = $categoty->getChildrenCategories();
            if (!count($categories)) {
                //echo "--";
                $categories = $categoty->getParentCategory()->getChildrenCategories();
                
            }
            $this->getLayer()->getProductCollection()
                ->addCountToCategories($categories);
            
            //$_pids = $this->getLayer()->getProductCollection()->getAllIds();
            
            $data = array();
            foreach ($categories as $category) {
                //echo $category->getId() . "--";
                if ($category->getIsActive() && $category->getProductCount()) {
                    
                    /*$_count = $category->getProductCollection()
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToFilter('entity_id', array('in' => $_pids));
                    Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($_count);
                    $_count = $_count->getSize();*/
                    //echo $_count . "**";
                    /*if (!Mage::getStoreConfig("ajaxlist/ajaxlist/show_no_count") && !$_count) {
                        continue;
                    }*/

                    
                    
                    $data[] = array(
                        'label' => "0::". Mage::helper('core')->htmlEscape($category->getName()),
                        'value' => $category->getId(),
                        'count' => $category->getProductCount(),
                    );
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
        
        
        
        
        $key = $this->getLayer()->getStateKey() . '_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        $_module = Mage::app()->getRequest()->getModuleName();
        if (!$_module)
            return $data;

        if ($data === null) {
            //echo "---";
            $category = $this->getCategory();
            /** @var $categoty Mage_Catalog_Model_Categeory */
            //$categories = $category->getChildrenCategories();

            $categories = $this->_getChildrenCategories($category);

            if (!count($categories)) {

                //echo ;
                $category = Mage::getModel('catalog/category')->load($category->getParentId());
                $categories = $category->getChildrenCategories();
            }



            $this->getLayer()->getProductCollection()
                    ->addCountToCategories($categories);

            $_pids = $this->getLayer()->getProductCollection()->getAllIds();

            $data = array();
            foreach ($categories as $category) {



                if ($category->getIsActive()) {
                    $_count = $category->getProductCollection()
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            //->addIdFilter( $pids );
                            ->addAttributeToFilter('entity_id', array('in' => $_pids));



                    Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($_count);

                    $_count = $_count->getSize();

                    if (!Mage::getStoreConfig("ajaxlist/ajaxlist/show_no_count") && !$_count) {
                        continue;
                    }



                    $data[] = array(
                        'label' => "0::" . Mage::helper('core')->htmlEscape($category->getName()),
                        'value' => $category->getId(),
                        'count' => $_count,
                    );
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    protected function _getChildrenCategories($category) {
        //return $category->getResource()->getChildrenCategories($category);

        $collection = $category->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('all_children')
                ->addAttributeToSelect('is_anchor')
                ->addAttributeToFilter('is_active', 1)
                ->addAttributeToFilter('include_in_menu', 1)
                ->addIdFilter($category->getChildren())
                ->setOrder('position', Varien_Db_Select::SQL_ASC)
                ->joinUrlRewrite()
                ->load();

        return $collection;
    }

    /**
     * Render category to html
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @param boolean Whether ot not this item is first, affects list item class
     * @param boolean Whether ot not this item is outermost, affects list item class
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @param boolean Whether ot not to add on* attributes to list item
     * @return string
     */
    protected function _renderCategoryMenuItemHtml($category, $level = 0) {
        if (!$category->getIsActive()) {
            return '';
        }
        $data = array();

        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array) $category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            //$children = $category->getChildren();
            $children = $this->_getChildrenCategories($category);
            $childrenCount = $children->count();
        }

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }

        $_count = Mage::getModel('catalog/layer')->setCurrentCategory($category)->getProductCollection()->getSize();


        //echo $category->getName();

        
        $data[] = array(
            'label' =>   $level . "::" . Mage::helper('core')->htmlEscape($category->getName())   ,
            'value' =>    $category->getId(),
            'count' => $_count 
            
        );
        
        
        
        
        // render children
        $_sc = false;
        $j = 0;
        foreach ($activeChildren as $child) {
            $_sc = $this->_renderCategoryMenuItemHtml(
                    $child, ($level + 1)
            );

            if (!empty($_sc)) {
                foreach ($_sc as $i) {
                    array_push($data, $i);
                }
            }
            $j++;
        }
        return $data;
    }

    /**
     * Render categories menu in HTML
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     */
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '') {
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {
            $html .= $this->_renderCategoryMenuItemHtml(
                    $category, $level
            );
            $j++;
        }

        return $html;
    }

    /**
     * Return item position representation in menu tree
     *
     * @param int $level
     * @return string
     */
    protected function _getItemPosition($level) {
        if ($level == 0) {
            $zeroLevelPosition = isset($this->_itemLevelPositions[$level]) ? $this->_itemLevelPositions[$level] + 1 : 1;
            $this->_itemLevelPositions = array();
            $this->_itemLevelPositions[$level] = $zeroLevelPosition;
        } elseif (isset($this->_itemLevelPositions[$level])) {
            $this->_itemLevelPositions[$level]++;
        } else {
            $this->_itemLevelPositions[$level] = 1;
        }

        $position = array();
        for ($i = 0; $i <= $level; $i++) {
            if (isset($this->_itemLevelPositions[$i])) {
                $position[] = $this->_itemLevelPositions[$i];
            }
        }
        return implode('-', $position);
    }

    /**
     * Checkin activity of category
     *
     * @param   Varien_Object $category
     * @return  bool
     */
    public function isCategoryActive($category) {
        if ($this->getCurrentCategory()) {
            return in_array($category->getId(), $this->getCurrentCategory()->getPathIds());
        }
    }

}
