jQuery(document).ready(function(){
    var i=true;
    var j=true;
    var k="";
    var l="";
    function setReload_nav(a){
        window.reload_nav=a
    }
    function getReload_nav(){
        return window.reload_nav
    }
    function setReload_compare(a){
        window.reload_compare=a
    }
    function getReload_compare(){
        return window.reload_compare
    }
    function setCartDeleteText(a){
        window.cartDeleteText=a
    }
    setReload_nav(true);
    setReload_compare(true);
    var m=location.hash.slice(1);
    if(m!=""){
        handleHashChange()
    }
    jQuery(window).hashchange(function(e){
        handleHashChange()
    });

    /* remove pager selects click event */
    jQuery(".pager select, .toolbar select").prop("onchange", null).attr("onchange", null);
    
    jQuery(document).on("change",".pager select, .toolbar select",function(){
        setReload_nav(true);
        setReload_compare(true);
        window.location.hash=hashUrl(jQuery(this).val());
        return false
    });
    jQuery(document).on("click", ".pager a, .toolbar a",function(){
        setReload_nav(false);
        setReload_compare(false);
        window.location.hash=hashUrl(jQuery(this).attr("href"));
        return false
    });
    /*jQuery("#cart-sidebar a.btn-remove").live('click',function(){
        if(confirm(window.cartDeleteText)){
            setReload_nav(false);
            setReload_compare(false);
            loadAjaxProductsList(jQuery(this).attr("href"),false,false,true)
        }
        return false
    });*/
    jQuery(document).on("click", ".block-layered-nav #narrow-by-list a, .block-layered-nav .currently a, .block-layered-nav .actions a" ,function(){
        setReload_nav(true);
        setReload_compare(true);
        /*if(jQuery(this).attr("id")=="price-filter-button"){
            step_val=parseInt(jQuery("input#step_value").val());
            request_price_min=Math.floor(jQuery("#price_minimum").val()/step_val)*step_val;
            request_price_max=Math.ceil(jQuery("#price_maximum").val()/step_val)*step_val;
            new_url=ajaxListURL(jQuery("#price_slider_url").val())+"&price="+request_price_min+"-"+request_price_max;
            window.location.hash=hashUrl(new_url)
        }else{*/
            window.location.hash=hashUrl(jQuery(this).attr("href"))
        //}
        return false
    });
    jQuery(document).on("click", ".col-main .add-to-links a.link-compare" ,function(){
        setReload_nav(false);
        setReload_compare(true);
        loadAjaxProductsList(jQuery(this).attr("href"),false,true);
        return false
    });
    jQuery(document).on("click",".block.block-compare a.btn-remove, .block.block-compare .actions a",function(){
        setReload_nav(false);
        setReload_compare(true);
        loadAjaxProductsList(jQuery(this).attr("href"),false,true);
        return false
    });
    /*jQuery("#price_minimum, #price_maximum").live("keyup",function(a){
        if(a.keyCode==13){
            jQuery("#price-filter-button").click()
        }
    });*/
    function handleHashChange(){
        var a=location.hash.slice(1);
        path=window.location.href;
        path=path.split("#")[0];
        path=path.split("?")[0];
        path=ajaxListURL(path+"?"+a);
        nv=getReload_nav();
        cm=getReload_compare();
        loadAjaxProductsList(path,nv,cm,false);
        setReload_nav(true);
        setReload_compare(true)
    }
    function loadAjaxProductsList(e,f,g,h){
        e=ajaxListURL(e);
        jQuery(".category-products").append("<div class=\"products-list-loader\"><div></div></div>");
        if(f){
            jQuery(".block-layered-nav").append("<div class=\"products-list-loader\"><div></div></div>")
            
        }
        jQuery.get(e,{},function(a,b,c){
            if(b=="error"){
                jQuery(".category-products").html("<p>There was an error making the AJAX request</p>")
            }else{
                var d=jQuery("<div />").html(a);
                jQuery(".category-products").html(d.find('.category-products').html());
                jQuery.scrollTo(".col-main",700);
                if(f){
                    jQuery(".block-layered-nav").html(d.find('.block-layered-nav').html());
                    
                    
                    prepareMultiSelectLists();
                //StartSlider()
                }
                if(g){
                    jQuery(".block-compare").html(d.find('.block-compare').html())
                }
                if(h){
                    jQuery(".block-cart").html(d.find('.block-cart').html())
                }
                if(typeof(window.ajaxproload)=="function"){
                    ajaxproload()
                }
                /* remove pager selects click event */
                jQuery(".pager select, .toolbar select").prop("onchange", null).attr("onchange", null);
            }
        })
    }
    function ajaxListURL(a){
        if(a.indexOf("ajax=1")<0){
            if(a.indexOf("?")<0){
                a=a+"?ajax=1"
            }else{
                a=a+"&ajax=1"
            }
        }
        return a
    }
    function hashUrl(a){
        a.match(/\?(.+)$/);
        var b=RegExp.$1;
        if(b.indexOf("ajax=1")>=0){
            b=b.replace("ajax=1&","");
            b=b.replace("&ajax=1","");
            b=b.replace("ajax=1","")
        }
        return b
    }
    /*jQuery("#products-list button.btn-cart").live("click",function(){
        loadAjaxProductsList(jQuery(this).attr("rel"),false,false,true);
        return false
    })*/
    
    /* drop down functions*/
    
    function prepareMultiSelectLists() {
    
        jQuery("select.layered-navigation-multiselect.attribute-filter-select").multiselect({
            selectedList : 5,
            minWidth: 165,
            create: function(event, ui){
                footer = jQuery('<div />')
                .addClass('ui-widget-footer ui-corner-all ui-multiselect-header ui-helper-clearfix');
                button = jQuery('<button>').append('<span>' + "Apply Filter" + '</span>').button({
                    icons: {
                        primary: "ui-icon-search"
                    }
                }).addClass("attribute-filter-button").appendTo(footer);
                jQuery(this).multiselect("widget").append(footer);
            }
        });
    
    
        _sel = 4;
        _multiple = true;
        _header = true;
        if(window._search_query!=""){
            _sel = 1;
            //_multiple = false
            _header = false;
        }
        
    
        jQuery("select.layered-navigation-multiselect.cat-filter-select").multiselect({
            selectedList : _sel,
            multiple: _multiple,
            minWidth: 165,
            header: _header,
            create: function(event, ui){
                footer = jQuery('<div />')
                .addClass('ui-widget-footer ui-corner-all ui-multiselect-header ui-helper-clearfix');
                button = jQuery('<button>').append('<span>' + "Apply Filter" + '</span>').button({
                    icons: {
                        primary: "ui-icon-search"
                    }
                }).addClass("attribute-filter-button").appendTo(footer);
                jQuery(this).multiselect("widget").append(footer);
            
            
                jQuery(this).multiselect("widget").find("input:checkbox").each(function(i){
                    _level = jQuery(this).attr("class");
                    jQuery(this).closest("li").addClass(_level );
                });
            
            },
            click: function(e,ui){
               
                if(window._search_query!=""){
                    jQuery(this).multiselect("widget").find(":checkbox").each(function(i,e){
                        if(  jQuery(e).val()!=ui.value) jQuery(e).attr("checked",false);
                    });
                    jQuery("select.layered-navigation-multiselect.cat-filter-select").val(ui.value);
                }
               
            }
        });
        
        
        
        
        
        
        jQuery("select.layered-navigation-multiselect.price-filter-select").multiselect({
            selectedList : 1,
            //multiple: false,
            minWidth: 165,
            //header: false,
            create: function(event, ui){
                footer = jQuery('<div />')
                .addClass('ui-widget-footer ui-corner-all ui-multiselect-header ui-helper-clearfix');
                button = jQuery('<button>').append('<span>' + "Apply Filter" + '</span>').button({
                    icons: {
                        primary: "ui-icon-search"
                    }
                }).addClass("attribute-filter-button").appendTo(footer);
                jQuery(this).multiselect("widget").append(footer);
            
            
                jQuery(this).multiselect("widget").find("input:checkbox").each(function(i){
                    _level = jQuery(this).attr("class");
                    jQuery(this).closest("li").addClass(_level );
                });
            
            },
            click: function(e,ui){
                //jQuery(this).multiselect("uncheckAll");
                //jQuery(this).multiselect("widget").find("select").val(ui.value);
                //alert(ui.value);
                jQuery(this).multiselect("widget").find(":checkbox").each(function(i,e){
                    if(  jQuery(e).val()!=ui.value) jQuery(e).attr("checked",false);
                });
                jQuery("select.layered-navigation-multiselect.price-filter-select").val(ui.value);
            //jQuery(this).multiselect('refresh');
            }
        });
        
    
        jQuery("#apply-filters").button({
            icons: {
                primary: "ui-icon-search"
            }
        });
    
    }
    
    jQuery(document).on("click", "button.attribute-filter-button,#apply-filters",function(){
        applyAjaxFilters();
        return false;
    });
    
    
    
    
    function applyAjaxFilters(){
        _v = new Array();
        jQuery("select.layered-navigation-multiselect").multiselect("close");
        //_x = 
        jQuery( "#narrow-by-list select.layered-navigation-multiselect.attribute-filter-select, #narrow-by-list select.layered-navigation-multiselect.price-filter-select").each(function(i, elem){
            //_t = jQuery(elem).find("span.filter_values").text(); 
            _t = jQuery(elem).val();
            // alert(_t);
            if(_t){
                _v.push(  jQuery(elem).attr("name") + "=" + _t );
            }
        });
        
        jQuery( "#narrow-by-list select.layered-navigation-multiselect.cat-filter-select").each(function(i, elem){
            _t = jQuery(elem).val();
            if(_t){
                _v.push(  jQuery(elem).attr("name") + "=" + _t );
            }else{
                if(window._selected_category!="")_v.push(  jQuery(elem).attr("name") + "=" + _selected_category );
            }
        });
        
        /*jQuery( "#narrow-by-list select.layered-navigation-multiselect.price-filter-select").each(function(i, elem){
            _t = jQuery(elem).val();
            if(_t){
                _v.push(  jQuery(elem).attr("name") + "=" + _t );
            }
        });   */           
        
        
        
        //_c = jQuery("#clear-layered-nav-url").text(); 
        _p = _v.join("&"); 
        //_l = "";
        /*search results*/
        if( window._search_query!="") _p = _p + "&" + window._search_query;
        
        window.location.hash = _p
        
        /*if(_p){
            if(_c.indexOf("?")<0){
                _c=_c+"?" ;
            }else{
                _c=_c+"&";
            }
            _l = _c + _p ; 
        }
        
        if( _l && _l != document.URL ){
            window.location.hash=hashUrl(new_url);
            //window.location = _l; 
        }else{
            alert("Please choose different filters");
        }
        */
        
        
        return false;
        
        
    }
    
    prepareMultiSelectLists();
    
    
});




