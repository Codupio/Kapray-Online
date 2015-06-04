<?php
class Mango_Ajaxlist_Model_Observer {
    public function alterResponse($observer) {
        $response = $observer->getResponse();
        $html = $response->getBody();
        if(Mage::app()->getRequest()->getParam("ajax") && !Mage::app()->getStore()->isAdmin()){
            $_url_helper = Mage::helper('core/url');
            $_uec = $_url_helper->getEncodedUrl();
            $_new_uenc =  $_url_helper->urlEncode($_url_helper->removeRequestParam(  $_url_helper->getCurrentUrl(), 'ajax'));
            $html = str_replace($_uec , $_new_uenc, $html )  ;
        }
        $response->setBody($html);
    }
    
    public function addLayoutHandler($observer) {
        $_is_ajax_page = Mage::app()->getRequest()->getParam("ajax");
        //if ($_is_ajax_page) {
            $action = $observer->getEvent()->getAction();
            //print_r($action->getRequest());
            if (($action->getRequest()->getModuleName() == 'catalog' && $action->getRequest()->getControllerName() == 'category') || $action->getRequest()->getModuleName() == 'catalogsearch') {
                $layout = $observer->getEvent()->getLayout();
                if($_is_ajax_page)$layout->getUpdate()->addHandle("AJAXLIST_PAGE_HANDLER");
                $_uses_layered_navigation = false;
                if(Mage::registry('current_category') && Mage::registry('current_category')->getIsAnchor() ){
                    if($_is_ajax_page)$layout->getUpdate()->addHandle("AJAXLIST_CATEGORY_LAYERED_HANDLER");
                    $_uses_layered_navigation = true;
                }elseif($action->getRequest()->getModuleName() == 'catalogsearch' && $action->getRequest()->getControllerName() == 'result' && $action->getRequest()->getActionName() == 'index' ){
                    if($_is_ajax_page)$layout->getUpdate()->addHandle("AJAXLIST_SEARCH_RESULT_HANDLER");
                    $_uses_layered_navigation = true;
                }
                
                if (Mage::getStoreConfig("ajaxlist/frontend/horizontal_only")  && $_uses_layered_navigation ) {
                    Mage::app()->getLayout()->getUpdate()->addUpdate('<remove name="ajax_layered_navigation_left"/><remove name="catalog.leftnav"/><remove name="catalogsearch.leftnav"/>');
                    Mage::register("use_horizontal_layered_navigation_only", true);
                }
            }
       // }
    }
    
}
