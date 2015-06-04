<?php
class Mango_Multiselect_Model_Observer {
    
    public function addLayoutHandler($observer) {
        $_is_ajax_page = Mage::app()->getRequest()->getParam("ajax") && Mage::app()->getRequest()->getParam("reloadpricefilter") ;
        if ($_is_ajax_page) {
            $action = $observer->getEvent()->getAction();
            if (($action->getRequest()->getModuleName() == 'catalog' && $action->getRequest()->getControllerName() == 'category') || $action->getRequest()->getModuleName() == 'catalogsearch') {
                    Mage::app()->getLayout()->getUpdate()->addUpdate('<remove name="content"/>');
            }
        }
    }
    
}
