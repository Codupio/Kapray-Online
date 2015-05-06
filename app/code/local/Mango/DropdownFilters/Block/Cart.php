<?php

class Mango_DropdownFilters_Block_Cart extends  Mage_Checkout_Block_Cart {

    public function __construct(){
        parent::__construct();

    }

    public function getContinueShoppingUrl()
    {
        $_url = parent::getContinueShoppingUrl();
        $_url = str_replace("ajax=1", "ajax=0", $_url);
        return $_url;
    }

}