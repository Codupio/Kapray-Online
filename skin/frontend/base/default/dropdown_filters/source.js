jQuery(document).ready(function(){

    jQuery(".pager select, .toolbar select").live('change',function(){
        loadAjaxProductsList(jQuery(this).val(), false ,false);
        return false
     });

    jQuery(".pager a, .toolbar a").live('click',function(){
        loadAjaxProductsList(jQuery(this).attr("href"), false, false);
        return false
     });

    jQuery(".block-layered-nav a").live("click",function(){
        loadAjaxProductsList(jQuery(this).attr("href"), true, false);
        return false
    });

    jQuery(".col-main .add-to-links a.link-compare").live('click',function(){
        loadAjaxProductsList(jQuery(this).attr("href"), false, true);
        return false
     });


    jQuery(".layered-navigation-select").live('change',function(){
        loadAjaxProductsList(jQuery(this).val(), true, false);
        return false
    });


    function loadAjaxProductsList(e, reload_navigation, reload_compare ){
        e=ajaxListURL(e);
        jQuery(".col-main").append("<div class=\"products-list-loader\"><div>Loading</div></div>");
        if(reload_navigation){
            jQuery(".block-layered-nav").append("<div class=\"products-list-loader\"><div>Loading</div></div>");
        }
        jQuery.get(e,{},function(a,b,c){
            if(b=="error"){
                jQuery(".col-main").html("<p>There was an error making the AJAX request</p>")
            }else{
                var d=jQuery("<div />").html(a);
                jQuery(".col-main").html(d.find('.col-main').html());
                if(reload_navigation){
                    jQuery(".block-layered-nav").html(d.find('.block-layered-nav').html());
                }
       if(reload_compare){
                            jQuery(".block-compare").html(d.find('.block-compare').html());
       }
                if(typeof(window.ajaxproload)=="function"){
                    ajaxproload()
                }
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
});