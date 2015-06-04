<?php

class Mango_Layered_Model_Observer {

    public function topLayeredNavigationBefore(Varien_Event_Observer $observer) {
        /* moved to Mango_Ajaxlist_Model_Observer -> addLayoutHandler to optimize.. */
        return;
    }

    public function topLayeredNavigationPrepare(Varien_Event_Observer $observer) {

        if (!Mage::registry("use_horizontal_layered_navigation_only"))
            return;

        $block = $observer->getBlock();
        if ($block instanceof Mage_Catalog_Block_Product_List) {
            $_top_layered_navigation_html = Mage::app()->getLayout()->createBlock(
                            'Mage_Catalog_Block_Layer_View', 'horizontal_layered', array('template' => 'ajaxlayerednavigation/catalog/layer/view-horizontal.phtml')
                    )->toHtml();
            Mage::register("top_layered_navigation_html", $_top_layered_navigation_html);
        }
        return;
    }

    public function topLayeredNavigationInsert(Varien_Event_Observer $observer) {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Catalog_Block_Product_List) {
            $transport = $observer->getTransport();
            $html = $transport->getHtml();
            $html = Mage::registry("top_layered_navigation_html") . $html;
            $transport->setHtml($html);
        }
        return;
    }

    /* SET TEMPLATES FOR EACH FILTER */

    public function templatesForLayeredNavigationFilter(Varien_Event_Observer $observer) {
        $front = Mage::app()->getRequest()->getRouteName();
        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();
        // Perform this operation if we're on a category view page or search results page
        if (($front == 'catalog' && $controller == 'category' && $action == 'view') || ($front == 'catalogsearch' && $controller == 'result' && $action == 'index')) {
            $block = $observer->getBlock();
            /* if ($block instanceof Mage_Catalog_Block_Layer_View) {
              $attributes = $block->getLayer()->getFilterableAttributes();
              foreach ($attributes as $_attribute) {
              $_code = $_attribute->getAttributeCode();
              if ($_code == 'price') {
              $block->getChild($_attribute->getAttributeCode() . '_filter')->setTemplate("ajaxlayerednavigation/catalog/layer/price.phtml");
              } else {
              $block->getChild($_attribute->getAttributeCode() . '_filter')->setTemplate("ajaxlayerednavigation/catalog/layer/attribute.phtml");
              }
              }
              } */
            /* if ($block instanceof Mage_Catalog_Block_Layer_View) {
              $block->setTemplate("ajaxlayerednavigation/catalog/layer/view.phtml");
              } else */
            if ($block instanceof Mage_Catalog_Block_Layer_Filter_Attribute) {
                $block->setTemplate("ajaxlayerednavigation/catalog/layer/attribute.phtml");
            } elseif ($block instanceof Mage_Catalog_Block_Layer_Filter_Price) {
                if(Mage::getStoreConfig("ajaxlist/ajaxlist/use_priceslider")){
                    $block->setTemplate("ajaxlayerednavigation/catalog/layer/price.phtml");
                }else{
                    $block->setTemplate("ajaxlayerednavigation/catalog/layer/attribute.phtml");
                }
            } elseif ($block instanceof Mage_Catalog_Block_Layer_Filter_Category) {
                if (Mage::app()->getRequest()->getModuleName() == "catalog") {
                    $block->setTemplate('ajaxlayerednavigation/catalog/layer/category.phtml');
                } else {
                    $block->setFilterId('cat')->setTemplate('ajaxlayerednavigation/catalog/layer/attribute.phtml');
                }
            } elseif ($block instanceof Mage_Catalog_Block_Layer_State) {
                $block->setTemplate("ajaxlayerednavigation/catalog/layer/state.phtml");
            }
        }
        return;
    }

}
