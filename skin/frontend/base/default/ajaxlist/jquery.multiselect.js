var _search_query = "";
var _open_vertical_multiselect = false;
jQuery(document).ready(function() {

    window._search_query = window.getParameterByName("q");

    /* check uncheck buttons */
    jQuery(document).on("click", ".multiselect-dropdown-container .multiselect-buttons-container a.check-all", function(e) {
        e.preventDefault();
        e.stopPropagation();
        element = jQuery(this).data("list");
        jQuery(element).find("li").addClass("active-filter-option");
        updateListLabel(jQuery(element));
    });
    jQuery(document).on("click", ".multiselect-dropdown-container .multiselect-buttons-container a.uncheck-all", function(e) {
        e.preventDefault();
        e.stopPropagation();
        element = jQuery(this).data("list");
        jQuery(element).find("li").removeClass("active-filter-option");
        updateListLabel(jQuery(element));
    });
    /*check on the list*/
    jQuery(document).on("click", ".multiselect-dropdown-container ol.layered-links-multiselect li", function(e) {
        e.preventDefault();
        e.stopPropagation();
        jQuery(this).toggleClass("active-filter-option");
        element = jQuery(this).closest("ol.layered-links-multiselect");
        updateListLabel(element);
    });
    /* filter button */
    jQuery(document).on("click", ".multiselect-dropdown-filter-button-container button.button", function(e) {
        e.preventDefault();
        e.stopPropagation();
        applyAjaxFilters();
    });



    /* dropdown effects */
    jQuery(document).on("click", "#narrow-by-list .multiselect-dropdown .multiselect-label-container", function(e) {
        e.preventDefault();
        _is_expanded = jQuery(this).hasClass("has-expanded-multiselect");
        jQuery(".multiselect-dropdown .multiselect-label-container, .multiselect-dropdown-container.expanded-multiselect").removeClass('expanded-multiselect has-expanded-multiselect');
        if (!_is_expanded) {
            //console.log("is not expanded yet");
            jQuery(this).addClass("has-expanded-multiselect");
            jQuery(this).next(".multiselect-dropdown-container").addClass('expanded-multiselect');
        }
        window._open_vertical_multiselect = true;
    });
    jQuery(document).on("click", ".expanded-multiselect", function(e) {
        e.stopPropagation();/* do not close the dropdowns... */
    });

    /* close all dropdowns */
    jQuery(document).on("click", function(e) {
        if (!window._open_vertical_multiselect) {
            jQuery(".multiselect-dropdown .multiselect-label-container.has-expanded-multiselect, .multiselect-dropdown-container.expanded-multiselect").removeClass('expanded-multiselect has-expanded-multiselect');
        } else {
            window._open_vertical_multiselect = false;
        }
    });


    /* override price slider */
    window.StartSlider = (function() {
        min_price = 0;
        max_price = parseInt(jQuery("input#price_maximum").val());
        step_val = jQuery("input#step_value").val();
        step_val = parseInt(step_val);
        jQuery("#slider-range-price").slider({
            range: true,
            min: 0,
            max: max_price,
            step: step_val,
            values: [jQuery("#init_price_minimum").val(), jQuery("#init_price_maximum").val()],
            slide: function(a, b) {
                jQuery("input#price_maximum").val(b.values[ 1 ]);
                jQuery("input#price_minimum").val(b.values[ 0 ]);
            },
            change: function(a, b) {
                jQuery("input#price_maximum").val(b.values[ 1 ]);
                jQuery("input#price_minimum").val(b.values[ 0 ]);
                applyAjaxFilters();
            }
        });
        jQuery("input#price_maximum").val(jQuery("#slider-range-price").slider("values", 1));
        jQuery("input#price_minimum").val(jQuery("#slider-range-price").slider("values", 0));
    });

    jQuery(document).off("click", "button#price-filter-button");
    jQuery(document).on("click", "button#price-filter-button", function(e) {
        e.preventDefault();
        e.stopPropagation();
        applyAjaxFilters();
        /* call ajax filters.. */
        return false;

    });

});


function updateListLabel(element) {
    _label_id = jQuery(element).data("label-id");
    _info = [];
    _selected_items = jQuery(element).find("li.active-filter-option a").length;
    _label_text = jQuery(_label_id).find("span.multiselect-label-text");
    if (_selected_items === 0) {
        _label_text.html(window._multiselect_label);
    } else if (_selected_items > _show_count_after) {
        _text = window._selected_count_label.replace('#', _selected_items);
        _label_text.html(_text);
    } else {
        jQuery(element).find("li.active-filter-option a").each(function() {
            _info.push(jQuery(this).html());
        });
        _text = _info.join();
        _label_text.html(_text);
    }
}


function getParameterByName(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

function applyAjaxFilters() {
    _v = new Array();
    /* all layered links, including categories */
    jQuery("ol.layered-links-multiselect").each(function() {
        _values = new Array();
        jQuery(this).find("li.active-filter-option a").each(function() {
            _value = jQuery(this).data("attribute-value");
            _values.push(_value);
        });
        _param_value = _values.join();
        alert(_param_value);
        if (_param_value !== "") {
            _param = jQuery(this).data("attribute") + "=" + _param_value;
            _v.push(_param);
        }
    });
    /* price slider values */
    /* only if price slider is present... */
    if (jQuery("#slider-range-price").length) {

        _slider_min = jQuery("#slider-range-price").slider("option", "min");
        _slider_max = jQuery("#slider-range-price").slider("option", "max");

        /* check if values have changed */
        if (jQuery("#price_minimum").val() !== _slider_min || jQuery("#price_maximum").val() !== _slider_max) {
            step_val = parseInt(jQuery("input#step_value").val());
            request_price_min = Math.floor(jQuery("#price_minimum").val() / step_val) * step_val;
            request_price_max = Math.ceil(jQuery("#price_maximum").val() / step_val) * step_val;
            _param = "price=" + request_price_min + "-" + request_price_max;
            _v.push(_param);
        }
    }
    _p = _v.join("&");
    if (window._search_query)
        _p = _p + "&" + window._search_query;

    window.location="?"+ _p;
    //window.location.hash = _p;
}

function extraAjaxLayeredNavigationScripts() {/* this function will reload the price filters when priceslider is disabled, (beta) */
    //return;
    if (window._use_priceslider)
        return;
    _v = new Array();
    /* all layered links, including categories */
    jQuery("ol.layered-links-multiselect").not("#multiselect-price").each(function() {
        _values = new Array();
        jQuery(this).find("li.active-filter-option a").each(function() {
            _value = jQuery(this).data("attribute-value");
            _values.push(_value);
        });
        _param_value = _values.join();
        if (_param_value !== "") {
            _param = jQuery(this).data("attribute") + "=" + _param_value;
            _v.push(_param);
        }
    });
    _v.push('reloadpricefilter=true&ajax=1');
    _p = _v.join("&");
    if (window._search_query)
        _p = _p + "&" + window._search_query;

    path = window.location.href;
    path = path.split("#")[0];
    path = path.split("?")[0];
    path = path + "?" + _p;
    jQuery.get(path, {}, function(a, b, c) {
        _i = jQuery(a).find("#filter-price-content");
        jQuery("#filter-price-content").replaceWith(_i);
        _priceVal = querySt("price");
        _values = _priceVal.split(",");
        jQuery.each(_values, function(i, val) {
            jQuery("#multiselect-price li a[data-attribute-value='" + val + "']").click();
        });
    });
}

function querySt(Key) {
    var url = window.location.hash.slice(1);
    //var url = window.location.slice(1);
    KeysValues = url.split(/[\?&]+/);
    for (i = 0; i < KeysValues.length; i++) {
        KeyValue = KeysValues[i].split("=");
        if (KeyValue[0] == Key) {
            return KeyValue[1];
        }
    }
}


