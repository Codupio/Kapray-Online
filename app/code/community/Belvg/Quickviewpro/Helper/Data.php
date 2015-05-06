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
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Quickviewpro_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * The main extension settings
     *
     * @var array
     */
    private $_settings = array();
    
    const DEFAULT_QUICKVIEW_MAX_HEIGHT  = 400;
    const HANDLE_POPUP                  = 'PRODUCT_quickviewpro_popup';
    const PRODUCT_LIST_CONTAINER_PREFIX = 'product-list-container';

    /**
     * Load the main extension settings
     */
    public function __construct()
    {
        $this->_settings = $this->getQuickviewSettings();
    }

    /**
     * Is the extension enabled?
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig('quickviewpro/settings/enabled', Mage::app()->getStore());
    }

    /**
     * Get the main extension settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->_settings;
    }

    /**
     * Create the main extension setings
     *
     * @return array
     */
    private function getQuickviewSettings()
    {
        $store      = Mage::app()->getStore();
        $settings   = array(
            'jsposition'        =>       Mage::getStoreConfig('quickviewpro/style/jsposition', $store),
            'add_to_cart'       => (int) Mage::getStoreConfig('quickviewpro/settings/add_to_cart', $store),
            'navigation'        => (int) Mage::getStoreConfig('quickviewpro/settings/navigation', $store),
            'navigation_preview'=> (int) Mage::getStoreConfig('quickviewpro/settings/navigation_preview', $store),
            'review'            => (int) Mage::getStoreConfig('quickviewpro/settings/review', $store),
            'share'             => (int) Mage::getStoreConfig('quickviewpro/settings/share', $store),
            'product_page_link' => (int) Mage::getStoreConfig('quickviewpro/settings/product_page_link', $store),
            'overlay_show'      => (int) Mage::getStoreConfig('quickviewpro/settings/overlay_show', $store),
            'overlay_color'     =>       Mage::getStoreConfig('quickviewpro/settings/overlay_color', $store),
            'quickview_scroll'  => (int) Mage::getStoreConfig('quickviewpro/settings/quickview_scroll', $store),
            'max_height'        => (int) Mage::getStoreConfig('quickviewpro/settings/max_height', $store),
            'overlay_opacity'   => (float)str_replace(',', '.', Mage::getStoreConfig('quickviewpro/settings/overlay_opacity', $store))
        );

        $settings   = $this->checkDefaultQuickviewSettings($settings);
        return $settings;
    }

    public function getPopupStyle()
    {
        $store = Mage::app()->getStore();
        $borderWidth = (int) Mage::getStoreConfig('quickviewpro/style/border_width', $store);
        $borderColor = Mage::getStoreConfig('quickviewpro/style/border_color', $store);

        return 'border: ' . $borderWidth . 'px solid ' . $borderColor . ';';
    }

    public function getButtonStyle($hover = FALSE)
    {
        $store = Mage::app()->getStore();
        $colorText    = Mage::getStoreConfig('quickviewpro/style/color_text', $store);
        $colorFrom    = Mage::getStoreConfig('quickviewpro/style/color_button_from', $store);
        $colorTo      = Mage::getStoreConfig('quickviewpro/style/color_button_to', $store);
        $colorDecFrom = $this->hex2RGB($colorFrom);
        $colorDecTo   = $this->hex2RGB($colorTo);

        if ($hover) {
            $colorDecFrom['red']   += 30;
            $colorDecFrom['green'] += 30;
            $colorDecFrom['blue']  += 30;

            $colorDecTo['red']     += 30;
            $colorDecTo['green']   += 30;
            $colorDecTo['blue']    += 30;
        }

        $colorDecFrom = implode(',', $colorDecFrom);
        $colorDecTo   = implode(',', $colorDecTo);

        return '
            color: ' . $colorText . ';
            background: rgb(' . $colorDecFrom . ');
            background: -moz-linear-gradient(left, rgba(' . $colorDecFrom . ',1) 0%, rgba(' . $colorDecTo . ',1) 99%);
            background: -webkit-gradient(linear, left top, right top, color-stop(0%, rgba(' . $colorDecFrom . ',1)), color-stop(99%, rgba(' . $colorDecTo . ',1)));
            background: -webkit-linear-gradient(left, rgba(' . $colorDecFrom . ',1) 0%, rgba(' . $colorDecTo . ',1) 99%);
            background: -o-linear-gradient(left, rgba(' .$colorDecFrom . ',1) 0%, rgba(' . $colorDecTo . ',1) 99%);
            background: -ms-linear-gradient(left, rgba(' . $colorDecFrom . ',1) 0%, rgba(' . $colorDecTo . ',1) 99%);
            background: linear-gradient(left, rgba(' . $colorDecFrom . ',1) 0%, rgba(' . $colorDecTo . ',1) 99%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="' . $colorFrom . '", endColorstr="' . $colorTo . '", GradientType=1 );
            border: 1px solid ' . $colorFrom . ';
        ';
    }
    
    public function getGlassButtonImg()
    {
        $img = Mage::getStoreConfig('quickviewpro/style/imgbutton', Mage::app()->getStore());

        if (empty($img)) {
            $src = Mage::getDesign()->getSkinUrl('belvg/images/quickviewpro/default_ico.png');
        } else {
            $src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'quickview/' . $img;
        }

        return $src;
    }

    public function hex2RGB($hexStr, $returnAsString = FALSE, $seperator = ',')
    {
        $hexStr   = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
        $rgbArray = array();
        if (strlen($hexStr) == 6) {
            $colorVal = hexdec($hexStr);
            $rgbArray['red']   = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue']  = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) {
            $rgbArray['red']   = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue']  = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return FALSE;
        }

        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
    }

    /**
     * Check default main settings of extension
     *
     * @param array The main extension settings 
     * @return array
     */
    private function checkDefaultQuickviewSettings($settings)
    {
        if (!$settings['max_height']) {
            $settings['max_height'] = self::DEFAULT_QUICKVIEW_MAX_HEIGHT;
        }

        if (!$settings['overlay_show']) {
            unset($settings['overlay_opacity']);
            unset($settings['overlay_color']);
        }

        return $settings;
    }

    /**
     * Get prefix for javascript storage cache
     */
    public function getJsCachePrefix()
    {
        return 'default';
        //return Mage::helper('core/url')->getCurrentBase64Url();
    }
}