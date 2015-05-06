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
 
var quickview = new Array;
var Quickview = Class.create();
Quickview.prototype = {
    initialize: function(config)
    {
        this.hider  = $('quickviewpro-hider');
        this.popup  = $('quickviewpro-popup');

        this.config = Object.extend({
            itemTags       : '.category-products .item',
            settings       : new Object,
            productIds     : new Array,
            ajaxUrl        : '',
            cachePrefix    : 'qCache_',
            buttonTemplate : '<a class="quickviewpro-button" rel="{{productId}}" href="javascript:void(0);">Quickview</a>'
        }, config);

        this.showFlag;
        this.closeFlag;
    },

    init: function()
    {
        this.showFlag  = true;
        this.closeFlag = true;

        this.initButtons();
        this.initTouch();
        this.initAction();
    },

    initButtons: function()
    {
        var thisQ  = this;
        var button = new Template(this.config.buttonTemplate, new RegExp('(^|.|\\r|\\n)({{\\s*(\\w+)\\s*}})', ""));

        $$(this.config.itemTags).each(function(item, index) {
            if (item.select('.quickviewpro-button').length == 0) {
                item.insert({
                    bottom : button.evaluate({
                        productId : thisQ.config.productIds[index]
                    })
                });
            }
        });
    },

    initAction: function()
    {
        var thisQ = this;

        Event.observe(this.hider, 'click', function(event) {
            thisQ.close();
        });

        Event.observe(window, 'load', function() {
            $$(thisQ.config.itemTags).each(function(block) {
                button = block.select('.quickviewpro-button')[0];
                button.addClassName('quickviewpro-button-' + thisQ.config.settings.jsposition);
                button.up().addClassName('quickviewpro-block');
                Event.observe(button, 'click', function(event) {
                    var pro_id = this.readAttribute('rel');
                    thisQ.ajax(pro_id);
                });
            });

            $$(thisQ.config.itemTags).each(function(block) {
                button = block.select('.quickviewpro-button')[0];
                thisQ.setButtonPosition(button);
            });
        });

        Event.observe(window, 'resize', function() {
            thisQ.setOverlayHeight();
            $$(thisQ.config.itemTags).each(function(block) {
                button = block.select('.quickviewpro-button')[0];
                thisQ.setButtonPosition(button);
            });
        });
    },

    initTouch: function()
    {
        if ($$('html')[0].hasClassName('touch') && this.config.settings.jsposition != 'glass') {
            $$(this.config.itemTags).each(function(block) {
                var imgLink = block.select('.product-image')[0];
                if (typeof imgLink != 'undefined') {
                    var link = imgLink.readAttribute('href');
                    imgLink.writeAttribute('link', link);
                    imgLink.writeAttribute('href', 'javascript:;');
                    Event.observe(imgLink, 'click', function(event) {
                        button = this.up().select('.quickviewpro-button')[0];
                        //console.log(button.hasClassName('visible'));
                        if (button.hasClassName('visible')) {
                            window.location = this.readAttribute('link');
                        }
                    });
                }

                block.observe('mouseover', function() {
                    setTimeout(
                        function(){
                            button = block.select('.quickviewpro-button')[0];
                            button.addClassName('visible');
                        }, 1000
                    );

                });

                block.observe('mouseout', function() {
                    button = block.select('.quickviewpro-button')[0];
                    button.removeClassName('visible');
                });
            });
        }
    },

    setButtonPosition: function(button)
    {
        var img = button.up().select('img')[0];
        button.writeAttribute('style', 'display: block;');
        if (this.config.settings.jsposition == 'below' || this.config.settings.jsposition == 'glass') {
            // below, glass
            var top = parseInt(img.getHeight() - button.clientHeight);
        } else {
            // center
            var top = parseInt(img.getHeight() / 2);
        }

        button.writeAttribute('style', 'top: ' + top + 'px;');
    },

    slide: function(pro_id)
    {
        var popup = this.popup.select('.main-popup')[0];
        this.popup.select('.quickviewpro_popup_alpha')[0].setStyle( {height: popup.getStyle('height')} );
        this.popup.select('.quickviewpro_popup_alpha')[0].setStyle( {width: popup.getStyle('width')} );
        this.popup.select('.quickviewpro_popup_alpha')[0].show();

        if (this.popup.select('.next-product-view').size()) {
            this.popup.select('.next-product-view')[0].hide();
        }

        if (this.popup.select('.prev-product-view').size()) {
            this.popup.select('.prev-product-view')[0].hide();
        }

        this.ajax(pro_id);
    },

    ajax: function(pro_id)
    {
        if (this.showFlag) {
            this.showFlag  = false;
            this.closeFlag = false;
            loader.show();
            var html = this.getCache(pro_id);
            if (html) {
                loader.hide();
                this.show(pro_id, html);
            } else {
                var thisQ   = this;
                var ajaxUrl = this.config.ajaxUrl + 'id/' + pro_id + '/';
                new Ajax.Request( ajaxUrl, {
                    method    : 'post',
                    parameters: {
                        'pro_id'     : pro_id,
                        'isQuickview': '1'
                    },
                    onSuccess : function(transport) {
                        loader.hide();
                        if (!thisQ.closeFlag) {
                            thisQ.show(pro_id, transport.responseText);
                            thisQ.setCache(pro_id, transport.responseText);
                        }
                    }
                });
            }
        }
    },

    show: function(pro_id, html)
    {
        this.setOverlayHeight();
        this.hider.show();

        this.setPosition();
        this.popup.update(html);

        this.popup.select('.quickviewpro_popup_alpha')[0].hide();
        this.popup.show();

        this.showAfter(pro_id);
        this.showAfterYourCode(pro_id);
    },

    setOverlayHeight: function()
    {
        var heightBody = document.getElementsByTagName('body')[0].clientHeight;
        if (this.config.settings.overlay_show) {
            this.hider.setStyle({height: heightBody + 'px'});
        }
    },

    setPosition: function()
    {
        var scrollPos = _getScroll();
        var topPos    = parseInt(0.1 * document.documentElement.clientHeight + scrollPos['scrollTop']);

        this.popup.setStyle({top: topPos + 'px'});
        if (window.outerWidth < parseInt(this.popup.getStyle('width'))) {
            this.popup.setStyle({left: '0px', margin: '0 0 0 20px'});
        } else {
            this.popup.setStyle({left: '', margin: ''});
        }
    },

    close: function()
    {
        this.closeFlag = true;
        this.showFlag  = true;
        loader.hide();
        this.closeBefore();
        this.popup.update('');
        this.popup.hide();
        this.hider.hide();
    },
    
    initPopupActions: function(pro_id) 
    {
        var thisQ  = this;
        if (this.config.settings.navigation) {
            var index  = this.config.productIds.indexOf(pro_id.toString());
            var prevId = index - 1;
            var nextId = index + 1;

            if (typeof this.config.productIds[prevId] !== 'undefined' && this.popup.select('.prev-product-view').size()) {
                this.popup.select('.prev-product-view')[0]/*.writeAttribute('onclick', 'quickview.slide(' + this.config.productIds[prevId] + ')')*/.show();
                Event.observe(this.popup.select('.prev-product-view')[0], 'click', function(event) {
                    thisQ.slide(thisQ.config.productIds[prevId]);
                });
            }

            if (typeof this.config.productIds[nextId] !== 'undefined' && this.popup.select('.next-product-view').size()) {
                this.popup.select('.next-product-view')[0]/*.writeAttribute('onclick', 'quickview.slide(' + this.config.productIds[nextId] + ')')*/.show();
                Event.observe(this.popup.select('.next-product-view')[0], 'click', function(event) {
                    thisQ.slide(thisQ.config.productIds[nextId]);
                });
            }
        }

        Event.observe(this.popup.select('.close-popap')[0], 'click', function(event) {
            thisQ.close();
        });

        if (typeof ajaxcart != 'undefined' && typeof ajaxcart.initPopupActions == 'function') {
            ajaxcart.initPopupActions();
        }
    },

    /* Required javascript initialization after quickview display */
    showAfter: function(pro_id)
    {
        this.initPopupActions(pro_id);

        this.showFlag = true;    
        new Varien.BTabs('.quickviewpro_tabs');

        /*switch (this.config.settings.media) {
            case 'quickviewpro_media_cloudzoom':
                break;
            case 'quickviewpro_media_fancybox':
                break;
            default:
                Event.observe($$('.popup .product-image img')[0], 'load', function() {
                    var product_zoom = new Product.Zoom('image', 'track', 'handle', 'zoom_in', 'zoom_out', 'track_hint');
                });
        }*/

        if (this.config.settings.quickview_scroll) {
            var block = $$('.product-essential')[0];
            if ( this.config.settings.max_height >= (parseInt(block.getStyle('height')) + parseInt(block.getStyle('padding-top')) + parseInt(block.getStyle('padding-bottom'))) ) {
                $$('.product-view')[0].removeClassName('quickviewpro_scroll');
            }
        }

        if (this.config.settings.add_to_cart) {
            q_productAddToCartForm = new VarienForm('product_addtocart_form_' + pro_id);
            $$('body .popup .button').each(function(element) {
                element.setAttribute('onclick', 'q_productAddToCartForm.submit(this)');
            });
            //jQblvg('.wrap-qty').initQty();
        }

        if (typeof ajaxcart != 'undefined') {
            ajaxcart.initPopupActions();
        }
    },

    /* Your code after quickview display */
    showAfterYourCode: function(pro_id)
    {

    },

    /* Required javascript initialization before quickview close */
    closeBefore: function()
    {
        if (this.config.settings.media == 'quickviewpro_media_fancybox') {
            jQblvg('#fancybox-close').click();
        }
    },

    getCache: function(key)
    {
        return $.jStorage.get(this.config.cachePrefix + key);
    },

    setCache: function(key, value)
    {
        /*if($.jStorage.storageSize() > 128000)     // Clear cache
            $.jStorage.flush();*/
        $.jStorage.set(this.config.cachePrefix + key, value, {TTL: 600000});  // Removes a key from the storage after 10 min
    }
}
